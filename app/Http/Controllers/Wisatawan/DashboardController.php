<?php

namespace App\Http\Controllers\Wisatawan;

use App\Http\Controllers\Controller;
use App\Models\Akun;
use App\Models\Destinasi;
use App\Models\Kabupaten;
use App\Models\KategoriWisata;
use App\Models\Laporan;
use App\Models\RiwayatPenanganan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        /** @var Akun $akun */
        $akun = auth()->user();

        // Ringkasan laporan wisatawan
        $laporanSaya = $akun->laporan();

        $totalLaporan = (clone $laporanSaya)->count();

        $menungguLaporan = (clone $laporanSaya)
            ->whereIn('status', [
                Laporan::STATUS_BELUM_DILIHAT,
                Laporan::STATUS_DILIHAT,
            ])->count();

        $ditindaklanjuti = (clone $laporanSaya)
            ->where('status', Laporan::STATUS_DITINDAKLANJUTI)
            ->count();

        $selesai = (clone $laporanSaya)
            ->where('status', Laporan::STATUS_SELESAI)
            ->count();

        // Pencarian dan filter destinasi
        $queryDestinasi = Destinasi::with([
                'kecamatan.kabupaten',
                'kategoriWisata',
            ])
            ->withCount('laporan')
            ->when($request->filled('cari'), function ($query) use ($request) {
                $query->where('nama', 'like', '%' . $request->cari . '%');
            })
            ->when($request->filled('kabupaten'), function ($query) use ($request) {
                $query->whereHas('kecamatan', function ($q) use ($request) {
                    $q->where('id_kabupaten', $request->kabupaten);
                });
            })
            ->when($request->filled('kategori'), function ($query) use ($request) {
                $query->where('id_kategori_wisata', $request->kategori);
            });

        // Sorting destinasi
        $urutkan = $request->input('urutkan', 'terbanyak');

        switch ($urutkan) {
            case 'nama_asc':
                $queryDestinasi->orderBy('nama', 'asc');
                break;

            case 'nama_desc':
                $queryDestinasi->orderBy('nama', 'desc');
                break;

            case 'terbaru':
                $queryDestinasi->orderByDesc('created_at');
                break;

            default:
                $urutkan = 'terbanyak';
                $queryDestinasi->orderByDesc('laporan_count');
                break;
        }

        $destinasi = $queryDestinasi
            ->paginate(6)
            ->withQueryString();

        // Data pilihan filter
        $kabupaten = Kabupaten::orderBy('nama')->get();
        $kategoriWisata = KategoriWisata::orderBy('nama')->get();

        // Pembaruan laporan milik wisatawan
        $pembaruan = RiwayatPenanganan::with([
                'laporan.destinasi',
            ])
            ->whereHas('laporan', function ($query) use ($akun) {
                $query->where('id_akun', $akun->id_akun);
            })
            ->latest('created_at')
            ->take(3)
            ->get();

        return view('wisatawan.beranda', compact(
            'akun',
            'totalLaporan',
            'menungguLaporan',
            'ditindaklanjuti',
            'selesai',
            'destinasi',
            'kabupaten',
            'kategoriWisata',
            'pembaruan',
            'urutkan',
        ));
    }
}
