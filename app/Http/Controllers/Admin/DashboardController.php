<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Akun;
use App\Models\Destinasi;
use App\Models\PengajuanDestinasi;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /** Ringkasan data sistem untuk halaman Dashboard Admin. */
    public function index(): View
    {
        $jumlahStatus = PengajuanDestinasi::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $statistik = [
            'total_destinasi' => Destinasi::count(),
            'pengajuan_menunggu' => (int) ($jumlahStatus[PengajuanDestinasi::MENUNGGU] ?? 0),
            'pengajuan_disetujui' => (int) ($jumlahStatus[PengajuanDestinasi::DISETUJUI] ?? 0),
            'pengajuan_ditolak' => (int) ($jumlahStatus[PengajuanDestinasi::DITOLAK] ?? 0),
        ];

        $pengelolaAktif = Akun::query()
            ->where('role', Akun::ROLE_PENGELOLA)
            ->count();

        $destinasiTanpaPengelola = Destinasi::query()
            ->whereDoesntHave('pengelola', function ($query) {
                $query->where('akun.role', Akun::ROLE_PENGELOLA);
            })
            ->count();

        $pengajuanMenunggu = PengajuanDestinasi::query()
            ->with(['pengaju', 'kecamatan.kabupaten', 'kategoriWisata'])
            ->where('status', PengajuanDestinasi::MENUNGGU)
            ->latest('created_at')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'statistik',
            'pengelolaAktif',
            'destinasiTanpaPengelola',
            'pengajuanMenunggu'
        ));
    }
}
