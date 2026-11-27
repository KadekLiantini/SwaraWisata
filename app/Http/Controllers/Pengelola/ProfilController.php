<?php

namespace App\Http\Controllers\Pengelola;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\RiwayatPenanganan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProfilController extends Controller
{
    /**
     * Profil pengelola (Figma: Halaman Profil Pengelola).
     * Tab "akun": rincian akun & destinasi terikat. Tab "riwayat": pembaruan penanganan yang dikirim pengelola ini.
     */
    public function show(Request $request): View
    {
        $akun = $request->user();
        $destinasi = $akun->destinasi?->load(['kecamatan.kabupaten', 'kategoriWisata']);
        $tab = $request->query('tab') === 'riwayat' ? 'riwayat' : 'akun';

        // Ringkasan angka laporan di destinasi yang dikelola
        $hitung = fn (?string $status = null) => Laporan::query()
            ->where('id_destinasi', $destinasi?->id_destinasi)
            ->when($status, fn ($q) => $q->where('status', $status))
            ->count();

        $statistik = [
            'total'           => $hitung(),
            'belum_dilihat'   => $hitung(Laporan::STATUS_BELUM_DILIHAT),
            'ditindaklanjuti' => $hitung(Laporan::STATUS_DITINDAKLANJUTI),
            'selesai'         => $hitung(Laporan::STATUS_SELESAI),
        ];

        // Riwayat pembaruan milik pengelola ini (laporan yang sudah dihapus wisatawan tidak ikut)
        $riwayatSaya = RiwayatPenanganan::query()
            ->where('id_akun', $akun->id_akun)
            ->whereHas('laporan');

        $jumlahRiwayat = (clone $riwayatSaya)->count();

        $daftarRiwayat = null;
        if ($tab === 'riwayat') {
            $q = trim((string) $request->query('q'));
            $jenis = $request->query('jenis');

            $daftarRiwayat = $riwayatSaya
                ->with(['laporan', 'foto'])
                ->withCount('komentar')
                ->when(in_array($jenis, [RiwayatPenanganan::TINDAK_LANJUT, RiwayatPenanganan::PENYELESAIAN], true),
                    fn ($query) => $query->where('jenis_tindakan', $jenis))
                ->when($q !== '', function ($query) use ($q) {
                    // "#16" atau "16" mencari ID laporan, selain itu mencari judul laporan / isi pembaruan
                    $query->where(function ($sub) use ($q) {
                        if (preg_match('/^#?(\d+)$/', $q, $cocok)) {
                            $sub->where('id_laporan', $cocok[1]);
                        }
                        $sub->orWhereHas('laporan', fn ($l) => $l->where('judul', 'like', "%{$q}%"))
                            ->orWhere('deskripsi', 'like', "%{$q}%");
                    });
                })
                ->orderBy('created_at', $request->query('urut') === 'terlama' ? 'asc' : 'desc')
                ->paginate(5)
                ->withQueryString()
                ->fragment('tab-profil');
        }

        // Inisial avatar dari username, misalnya pengelola_kawahputih -> PK
        $inisial = collect(preg_split('/[_\s.]+/', $akun->username))->filter()->take(2)
            ->map(fn ($kata) => mb_strtoupper(mb_substr($kata, 0, 1)))->implode('');
        $inisial = mb_strlen($inisial) < 2 ? mb_strtoupper(mb_substr($akun->username, 0, 2)) : $inisial;

        // ID registrasi tampilan, misalnya PGL-BUL-004 (3 huruf kabupaten + id akun)
        $kabupaten = (string) $destinasi?->kecamatan?->kabupaten?->nama;
        $kodeKabupaten = Str::upper(Str::substr(Str::of($kabupaten)->replace(['Kota ', 'Kabupaten ', 'Kab. '], '')->trim(), 0, 3)) ?: 'XXX';
        $idRegistrasi = sprintf('PGL-%s-%03d', $kodeKabupaten, $akun->id_akun);

        // Foto destinasi: URL penuh dipakai apa adanya, path biasa diambil dari storage publik
        $fotoDestinasi = null;
        if ($destinasi?->foto) {
            $fotoDestinasi = Str::startsWith($destinasi->foto, ['http://', 'https://', '/'])
                ? $destinasi->foto
                : asset('storage/'.$destinasi->foto);
        }

        return view('pengelola.profil', compact(
            'akun', 'destinasi', 'tab', 'statistik', 'jumlahRiwayat', 'daftarRiwayat',
            'inisial', 'idRegistrasi', 'fotoDestinasi',
        ));
    }
}