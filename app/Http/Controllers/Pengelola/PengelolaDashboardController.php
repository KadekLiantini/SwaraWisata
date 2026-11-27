<?php

namespace App\Http\Controllers\Pengelola;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Laporan;
use App\Models\KategoriKerusakan;

class PengelolaDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Ambil data pengelola yang sedang login.
        $pengelola = auth()->user();
        $idDestinasi = $pengelola->id_destinasi;

        // Ambil nilai filter dari URL.
        $search = $request->query('search', '');
        $search = is_string($search) ? trim($search) : '';

        $status = $request->query('status', '');
        $status = is_string($status) ? $status : '';

        $sort = $request->query('sort', 'terbaru');
        $sort = is_string($sort) ? $sort : 'terbaru';

        // Batasi pilihan status yang diperbolehkan.
        $statusValid = [
            '',
            Laporan::STATUS_DILIHAT,
            Laporan::STATUS_DITINDAKLANJUTI,
            Laporan::STATUS_SELESAI,
        ];

        if (!in_array($status, $statusValid, true)) {
            $status = '';
        }

        // Batasi pilihan pengurutan.
        if (!in_array($sort, ['terbaru', 'terlama'], true)) {
            $sort = 'terbaru';
        }

        // Jika pengelola belum memiliki destinasi, tampilkan data kosong.
        if (!$idDestinasi) {
            $totalLaporan = 0;
            $belumDilihat = 0;
            $dilihat = 0;
            $ditindaklanjuti = 0;
            $selesai = 0;
            $jumlahPerluDitangani = 0;

            $kategoriTeratas = collect();
            $laporanTerbaru = collect();
            $jumlahKategoriTerbesar = 0;
        } else {
            // Batasi seluruh perhitungan laporan pada destinasi pengelola.
            $laporan = Laporan::query()
                ->where('id_destinasi', $idDestinasi);

            // Hitung jumlah seluruh laporan.
            $totalLaporan = (clone $laporan)->count();

            // Hitung laporan berdasarkan status.
            $belumDilihat = (clone $laporan)
                ->where('status', Laporan::STATUS_BELUM_DILIHAT)
                ->count();

            $dilihat = (clone $laporan)
                ->where('status', Laporan::STATUS_DILIHAT)
                ->count();

            $ditindaklanjuti = (clone $laporan)
                ->where('status', Laporan::STATUS_DITINDAKLANJUTI)
                ->count();

            $selesai = (clone $laporan)
                ->where('status', Laporan::STATUS_SELESAI)
                ->count();

            // Hitung jumlah laporan yang perlu penanganan.
            $jumlahPerluDitangani = (clone $laporan)
                ->whereIn('status', [
                    Laporan::STATUS_BELUM_DILIHAT,
                    Laporan::STATUS_DILIHAT,
                    Laporan::STATUS_DITINDAKLANJUTI,
                ])
                ->count();

            // Ambil lima kategori kerusakan yang paling banyak dilaporkan.
            $kategoriTeratas = KategoriKerusakan::query()
                ->whereHas('laporan', function ($query) use ($idDestinasi) {
                    $query->where('id_destinasi', $idDestinasi);
                })
                ->withCount([
                    'laporan' => function ($query) use ($idDestinasi) {
                        $query->where('id_destinasi', $idDestinasi);
                    }
                ])
                ->orderByDesc('laporan_count')
                ->take(5)
                ->get();

            $jumlahKategoriTerbesar = $kategoriTeratas->max('laporan_count') ?? 0;

            // Siapkan query laporan terbaru.
            $queryLaporanTerbaru = (clone $laporan)
                ->with([
                    'kategoriKerusakan',
                    'pelapor',
                    'foto',
                ]);

            // Filter pencarian berdasarkan judul atau ID laporan.
            if ($search !== '') {
                $queryLaporanTerbaru->where(function ($query) use ($search) {
                    $query->where('judul', 'like', '%' . $search . '%');

                    // Mendukung pencarian ID dengan format #16 atau 16.
                    $idSearch = ltrim($search, '#');

                    if (ctype_digit($idSearch)) {
                        $query->orWhere('id_laporan', (int) $idSearch);
                    }
                });
            }

            // Filter berdasarkan status.
            if ($status !== '') {
                $queryLaporanTerbaru->where('status', $status);
            }

            // Urutkan laporan berdasarkan waktu dibuat.
            $queryLaporanTerbaru->orderBy(
                'created_at',
                $sort === 'terlama' ? 'asc' : 'desc'
            );

            // Ambil maksimal lima laporan.
            $laporanTerbaru = $queryLaporanTerbaru
                ->limit(5)
                ->get();
        }

        // Gabungkan status dilihat dan ditindaklanjuti sebagai status proses.
        $jumlahProses = $dilihat + $ditindaklanjuti;

        // Hitung persentase untuk visualisasi dashboard.
        $persentaseSelesai = $totalLaporan > 0
            ? round(($selesai / $totalLaporan) * 100, 1)
            : 0;

        $persentaseProses = $totalLaporan > 0
            ? round(($jumlahProses / $totalLaporan) * 100, 1)
            : 0;

        $persentaseBelumDilihat = $totalLaporan > 0
            ? round(($belumDilihat / $totalLaporan) * 100, 1)
            : 0;

        // Kirim data ke halaman dashboard.
        return view('pengelola.dashboard', compact(
            'pengelola',
            'totalLaporan',
            'belumDilihat',
            'dilihat',
            'ditindaklanjuti',
            'selesai',
            'jumlahPerluDitangani',
            'jumlahProses',
            'persentaseSelesai',
            'persentaseProses',
            'persentaseBelumDilihat',
            'kategoriTeratas',
            'laporanTerbaru',
            'jumlahKategoriTerbesar'
        ));
    }
}
