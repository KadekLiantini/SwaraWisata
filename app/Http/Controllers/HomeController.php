<?php

namespace App\Http\Controllers;

use App\Models\Destinasi;
use App\Models\KategoriKerusakan;
use App\Models\Laporan;
use App\Models\RiwayatPenanganan;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Landing page publik (aturan bisnis no. 1, IPO "Menjelajahi Homepage"):
     * informasi website, statistik, Top 3 destinasi, dan pratinjau laporan.
     */
    public function __invoke(): View
    {
        $statistik = [
            'total'     => Laporan::count(),
            'selesai'   => Laporan::where('status', Laporan::STATUS_SELESAI)->count(),
            'destinasi' => Destinasi::active()->count(),
        ];

        // Top 3 destinasi aktif dengan jumlah laporan terbanyak
        $top3 = Destinasi::active()
            ->with('kecamatan.kabupaten')
            ->whereHas('laporan')
            ->withCount([
                'laporan',
                'laporan as selesai_count' => fn ($q) => $q->where('status', Laporan::STATUS_SELESAI),
                'laporan as proses_count'  => fn ($q) => $q->where('status', Laporan::STATUS_DITINDAKLANJUTI),
            ])
            ->orderByDesc('laporan_count')
            ->take(3)
            ->get();

        // Kategori kerusakan yang paling sering dilaporkan di tiap destinasi
        foreach ($top3 as $destinasi) {
            $destinasi->fasilitas_terbanyak = KategoriKerusakan::query()
                ->withCount(['laporan' => fn ($q) => $q->where('id_destinasi', $destinasi->id_destinasi)])
                ->orderByDesc('laporan_count')
                ->value('nama');
        }

        $maksLaporan = max(1, (int) $top3->max('laporan_count'));

        // 4 laporan terbaru untuk pratinjau (tanpa deskripsi lengkap)
        $laporanTerbaru = Laporan::with(['destinasi', 'kategoriKerusakan', 'foto'])
            ->latest()
            ->take(4)
            ->get();

        // Pembaruan penanganan terbaru dari pengelola, untuk kartu di hero
        $pembaruanTerakhir = RiwayatPenanganan::with('laporan')
            ->whereHas('laporan')
            ->latest()
            ->first();

        return view('pages.home', compact('statistik', 'top3', 'maksLaporan', 'laporanTerbaru', 'pembaruanTerakhir'));
    }
}