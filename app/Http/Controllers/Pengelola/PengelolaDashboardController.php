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

        // Tentukan filter yang sedang dipilih.
        $filter = $request->query('filter', 'perlu_penanganan');

        // Batasi pilihan filter yang diperbolehkan.
        if (!in_array($filter, [
            'semua',
            'perlu_penanganan',
            'belum_dilihat',
            'ditindaklanjuti',
        ])) {
            $filter = 'perlu_penanganan';
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

            // Hitung jumlah laporan berdasarkan status.
            $totalLaporan = (clone $laporan)->count();

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
            $queryLaporanTerbaru = (clone $laporan)->with([
                'kategoriKerusakan',
                'pelapor',
                'foto',
            ]);

            // Terapkan filter pada tabel laporan terbaru.
            if ($filter === 'perlu_penanganan') {
                $queryLaporanTerbaru->whereIn('status', [
                    Laporan::STATUS_BELUM_DILIHAT,
                    Laporan::STATUS_DILIHAT,
                    Laporan::STATUS_DITINDAKLANJUTI,
                ]);
            } elseif ($filter === 'belum_dilihat') {
                $queryLaporanTerbaru->where(
                    'status',
                    Laporan::STATUS_BELUM_DILIHAT
                );
            } elseif ($filter === 'ditindaklanjuti') {
                $queryLaporanTerbaru->where(
                    'status',
                    Laporan::STATUS_DITINDAKLANJUTI
                );
            }

            // Ambil maksimal lima laporan sesuai filter.
            $laporanTerbaru = $queryLaporanTerbaru
                ->orderByDesc('created_at')
                ->take(5)
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
            'jumlahKategoriTerbesar',
            'filter'
        ));
    }
}
