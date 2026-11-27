<?php

namespace App\Http\Controllers\Pengelola;

use App\Http\Controllers\Controller;
use App\Models\RiwayatPenanganan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class InfoController extends Controller
{
    /**
     * Informasi panduan untuk pengelola (Figma: Halaman Informasi, node 30:1019).
     * Isi panduan statis, sedangkan nama destinasi dan kartu "simulasi tampilan publik"
     * diambil dari data destinasi pengelola yang sedang login.
     */
    public function __invoke(Request $request): View
    {
        $destinasi = $request->user()->destinasi;

        // Contoh kartu publik: pembaruan penyelesaian terbaru di destinasi ini (bila sudah ada)
        $contohPembaruan = null;
        if ($destinasi) {
            $contohPembaruan = RiwayatPenanganan::query()
                ->with(['foto', 'laporan'])
                ->whereHas('laporan', fn ($q) => $q->where('id_destinasi', $destinasi->id_destinasi))
                ->where('jenis_tindakan', RiwayatPenanganan::PENYELESAIAN)
                ->latest()
                ->first();
        }

        // Foto kartu: foto bukti pembaruan, atau foto destinasi bila belum ada pembaruan
        $fotoContoh = null;
        if ($foto = $contohPembaruan?->foto->first()) {
            $fotoContoh = $foto->url ?? asset('storage/'.$foto->path);
        } elseif ($destinasi?->foto) {
            $fotoContoh = Str::startsWith($destinasi->foto, ['http://', 'https://', '/'])
                ? $destinasi->foto
                : asset('storage/'.$destinasi->foto);
        }

        return view('pengelola.info', compact('destinasi', 'contohPembaruan', 'fotoContoh'));
    }
}