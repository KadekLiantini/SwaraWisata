<?php

namespace App\Http\Controllers\Wisatawan;

use App\Http\Controllers\Controller;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\KategoriWisata;
use App\Models\PengajuanDestinasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PengajuanDestinasiController extends Controller
{
    public function create(): View
    {
        $kabupaten = Kabupaten::orderBy('nama')->get();
        $kategoriWisata = KategoriWisata::orderBy('nama')->get();

        $riwayatPengajuan = PengajuanDestinasi::with([
            'kecamatan.kabupaten',
            'kategoriWisata',
            'destinasi',
        ])
            ->where('id_akun', Auth::id())
            ->latest('created_at')
            ->get();

        $jumlahPengajuan = $riwayatPengajuan->count();

        return view('wisatawan.pengajuan-destinasi.create', compact(
            'kabupaten',
            'kategoriWisata',
            'riwayatPengajuan',
            'jumlahPengajuan'
        ));
    }

    public function getKecamatan($idKabupaten)
    {
        return Kecamatan::where('id_kabupaten', $idKabupaten)
            ->orderBy('nama')
            ->get(['id_kecamatan', 'nama']);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_lokasi' => ['required', 'string', 'max:150'],
            'id_kecamatan' => ['required', 'exists:kecamatan,id_kecamatan'],
            'id_kategori_wisata' => ['required', 'exists:kategori_wisata,id_kategori_wisata'],
            'link_maps' => ['required', 'url', 'max:500'],
        ]);

        PengajuanDestinasi::create([
            ...$validated,
            'id_akun' => Auth::id(),
            'status' => PengajuanDestinasi::MENUNGGU,
        ]);

        return redirect()
            ->route('wisatawan.beranda')
            ->with('success', 'Pengajuan destinasi berhasil dikirim.');
    }
}