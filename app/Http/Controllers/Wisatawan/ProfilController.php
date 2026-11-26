<?php

namespace App\Http\Controllers\Wisatawan;

use App\Http\Controllers\Controller;
use App\Models\Destinasi;
use App\Models\Laporan;
use App\Models\PengajuanDestinasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use App\Models\KategoriKerusakan;

class ProfilController extends Controller
{
    /** Halaman profil wisatawan: identitas, ringkasan aktivitas, riwayat laporan & pengajuan */
    public function show(Request $request): View
    {
        $akun = $request->user();
        $tab = $request->query('tab') === 'pengajuan' ? 'pengajuan' : 'laporan';
        $cari = trim((string) $request->query('q'));
        $status = $request->query('status');
        $urutan = $request->query('urut') === 'terlama' ? 'asc' : 'desc';

        // Ringkasan aktivitas (4 kartu statistik)
        $jumlahStatus = $akun->laporan()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $statistik = [
            'total'           => (int) $jumlahStatus->sum(),
            'dilihat'         => (int) ($jumlahStatus[Laporan::STATUS_DILIHAT] ?? 0),
            'ditindaklanjuti' => (int) ($jumlahStatus[Laporan::STATUS_DITINDAKLANJUTI] ?? 0),
            'selesai'         => (int) ($jumlahStatus[Laporan::STATUS_SELESAI] ?? 0),
            'pengajuan'       => $akun->pengajuan()->count(),
            'disetujui'       => $akun->pengajuan()->where('status', PengajuanDestinasi::DISETUJUI)->count(),
        ];

        if ($tab === 'laporan') {
            $daftar = $akun->laporan()
                ->with(['destinasi.kecamatan.kabupaten', 'kategoriKerusakan', 'foto'])
                ->when($cari !== '', function ($query) use ($cari) {
                    // Cari lewat ID laporan (#16 atau 16) atau judul laporan
                    if (preg_match('/^#?(\d+)$/', $cari, $cocok)) {
                        $query->where(fn ($q) => $q->where('id_laporan', $cocok[1])->orWhere('judul', 'like', "%{$cari}%"));
                    } else {
                        $query->where('judul', 'like', "%{$cari}%");
                    }
                })
                ->when($status, fn ($query) => $query->where('status', $status))
                ->when($request->query('destinasi'), fn ($query, $id) => $query->where('id_destinasi', $id))
                ->orderBy('created_at', $urutan)
                ->paginate(5)
                ->withQueryString()
                ->fragment('riwayat');

            $opsiDestinasi = Destinasi::whereIn('id_destinasi', $akun->laporan()->distinct()->pluck('id_destinasi'))
                ->orderBy('nama')
                ->pluck('nama', 'id_destinasi');
        } else {
            $daftar = $akun->pengajuan()
                ->with(['kecamatan.kabupaten', 'kategoriWisata'])
                ->when($cari !== '', fn ($query) => $query->where('nama_lokasi', 'like', "%{$cari}%"))
                ->when($status, fn ($query) => $query->where('status', $status))
                ->orderBy('created_at', $urutan)
                ->paginate(5)
                ->withQueryString()
                ->fragment('riwayat');

            $opsiDestinasi = collect();
        }

        // Bila simpan edit laporan gagal validasi, modal edit ditampilkan lagi beserta pesan error
        $laporanEdit = old('_edit_laporan')
            ? $akun->laporan()->with(['foto', 'destinasi'])->find(old('_edit_laporan'))
            : null;
        $kategoriKerusakan = $laporanEdit ? KategoriKerusakan::orderBy('nama')->get() : collect();

        return view('wisatawan.profil', compact('akun', 'tab', 'statistik', 'daftar', 'opsiDestinasi', 'laporanEdit', 'kategoriKerusakan'));
    }

    /** Ganti password dari halaman profil */
    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'password_lama' => ['required', 'current_password'],
            'password_baru' => ['required', 'confirmed', 'different:password_lama', Password::min(8)],
        ], [
            'password_lama.required'         => 'Password lama wajib diisi.',
            'password_lama.current_password' => 'Password lama tidak sesuai.',
            'password_baru.required'         => 'Password baru wajib diisi.',
            'password_baru.confirmed'        => 'Konfirmasi password baru tidak sama.',
            'password_baru.different'        => 'Password baru harus berbeda dari password lama.',
            'password_baru.min'              => 'Password baru minimal 8 karakter.',
        ]);

        $request->user()->update(['password' => $data['password_baru']]); // di-hash otomatis oleh cast model Akun

        return back()->with('success', 'Password berhasil diganti.');
    }
}