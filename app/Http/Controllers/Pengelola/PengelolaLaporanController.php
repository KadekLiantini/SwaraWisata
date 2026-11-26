<?php

namespace App\Http\Controllers\Pengelola;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\RiwayatPenanganan;
use App\Models\FotoRiwayat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PengelolaLaporanController extends Controller
{
    private function pastikanMilikDestinasi(Laporan $laporan): void
    {
        abort_unless(
            (int) $laporan->id_destinasi === (int) auth()->user()->id_destinasi,
            404
        );
    }
    public function index(Request $request)
{
    $filter = $request->query('filter', 'semua');

    $query = Laporan::with([
        'pelapor',
        'destinasi',
        'kategoriKerusakan',
        'foto',
    ]);

    // Batasi laporan hanya untuk destinasi pengelola yang login
    $idDestinasi = auth()->user()->id_destinasi;

    if ($idDestinasi) {
        $query->where('id_destinasi', $idDestinasi);
    } else {
        $query->whereRaw('1 = 0');
    }

    // Filter berdasarkan status laporan
    if ($filter === 'perlu_penanganan') {
        $query->whereIn('status', [
            Laporan::STATUS_BELUM_DILIHAT,
            Laporan::STATUS_DILIHAT,
            Laporan::STATUS_DITINDAKLANJUTI,
        ]);
    } elseif ($filter === 'belum_dilihat') {
    $query->where(
        'status',
        Laporan::STATUS_BELUM_DILIHAT
    );
    } elseif ($filter === 'ditindaklanjuti') {
        $query->where(
            'status',
            Laporan::STATUS_DITINDAKLANJUTI
        );
    } elseif ($filter === 'belum_ditindaklanjuti') {
        $query->whereIn('status', [
            Laporan::STATUS_BELUM_DILIHAT,
            Laporan::STATUS_DILIHAT,
        ]);
    } else {
        $filter = 'semua';
    }

    $laporan = $query
        ->latest()
        ->paginate(10)
        ->withQueryString();

    return view('pengelola.laporan.index', compact(
        'laporan',
        'filter'
    ));
}
    private function pastikanMilikPengelola(
        Laporan $laporan,
        RiwayatPenanganan $riwayat
    ): void {
        $this->pastikanMilikDestinasi($laporan);

        abort_unless(
            (int) $riwayat->id_laporan === (int) $laporan->id_laporan,
            404
        );

        abort_unless(
            (int) $riwayat->id_akun === (int) auth()->user()->id_akun,
            403
        );
    }

    public function show(Laporan $laporan)
    {
        $this->pastikanMilikDestinasi($laporan);
        $laporan->tandaiDilihat();

        $laporan->load([
            'pelapor',
            'destinasi',
            'kategoriKerusakan',
            'foto',
            'riwayat.pengelola',
            'riwayat.foto',
        ]);

        return view('pengelola.laporan.show', compact('laporan'));
    }

    public function storeRiwayat(Request $request, Laporan $laporan)
    {
        $this->pastikanMilikDestinasi($laporan);

        $validated = $request->validate([
            'jenis_tindakan' => [
                'required',
                'in:tindak_lanjut,penyelesaian',
            ],
            'deskripsi' => [
                'required',
                'string',
                'max:2000',
            ],
            'foto' => [
                'required',
                'array',
                'min:1',
            ],
            'foto.*' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ], [
            'jenis_tindakan.required' => 'Jenis tindakan wajib dipilih.',
            'deskripsi.required' => 'Deskripsi penanganan wajib diisi.',
            'foto.required' => 'Foto bukti wajib diunggah.',
            'foto.min' => 'Minimal unggah satu foto bukti.',
            'foto.*.image' => 'File bukti harus berupa gambar.',
            'foto.*.max' => 'Ukuran setiap foto maksimal 5 MB.',
        ]);

        $fotoTersimpan = [];
        $fotoLama = [];

        try {
            DB::transaction(function () use (
                $validated,
                $request,
                $laporan,
                &$fotoTersimpan,
                &$fotoLama
            ) {
                $riwayat = null;

                if ($validated['jenis_tindakan'] === 'penyelesaian') {
                    $riwayat = RiwayatPenanganan::where(
                        'id_laporan',
                        $laporan->id_laporan
                    )
                        ->where('jenis_tindakan', 'penyelesaian')
                        ->lockForUpdate()
                        ->first();

                    if ($riwayat) {
                        abort_unless(
                            (int) $riwayat->id_akun ===
                                (int) auth()->user()->id_akun,
                            403,
                            'Bukti penyelesaian ini dibuat oleh pengelola lain.'
                        );

                        $fotoLama = $riwayat->foto()
                            ->pluck('path')
                            ->all();

                        $riwayat->update([
                            'deskripsi' => $validated['deskripsi'],
                        ]);

                        $riwayat->foto()->delete();
                    }
                }

                if (!$riwayat) {
                    $riwayat = RiwayatPenanganan::create([
                        'id_laporan' => $laporan->id_laporan,
                        'id_akun' => auth()->user()->id_akun,
                        'jenis_tindakan' => $validated['jenis_tindakan'],
                        'deskripsi' => $validated['deskripsi'],
                    ]);
                }

                foreach ($request->file('foto', []) as $file) {
                    $path = $file->store('foto-riwayat', 'public');
                    $fotoTersimpan[] = $path;

                    FotoRiwayat::create([
                        'id_riwayat' => $riwayat->id_riwayat,
                        'path' => $path,
                    ]);
                }

                $laporan->hitungUlangStatus();
            });
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($fotoTersimpan);
            throw $e;
        }

        Storage::disk('public')->delete($fotoLama);

        return redirect()
            ->route('pengelola.laporan.show', $laporan->id_laporan)
            ->with('success', 'Riwayat penanganan berhasil disimpan.');
    }

    public function updateRiwayat(
        Request $request,
        Laporan $laporan,
        RiwayatPenanganan $riwayat
    ) {
        $this->pastikanMilikPengelola($laporan, $riwayat);

        $validated = $request->validate([
            'deskripsi' => [
                'required',
                'string',
                'max:2000',
            ],
            'foto' => [
                'sometimes',
                'array',
                'min:1',
            ],
            'foto.*' => [
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ], [
            'deskripsi.required' => 'Deskripsi penanganan wajib diisi.',
            'foto.min' => 'Minimal unggah satu foto jika ingin mengganti foto bukti.',
            'foto.*.image' => 'File bukti harus berupa gambar.',
            'foto.*.max' => 'Ukuran setiap foto maksimal 5 MB.',
        ]);

        $fotoTersimpan = [];
        $fotoLama = [];

        try {
            DB::transaction(function () use (
                $request,
                $validated,
                $laporan,
                $riwayat,
                &$fotoTersimpan,
                &$fotoLama
            ) {
                $riwayat->update([
                    'deskripsi' => $validated['deskripsi'],
                ]);

                if ($request->hasFile('foto')) {
                    $fotoLama = $riwayat->foto()
                        ->pluck('path')
                        ->all();

                    $riwayat->foto()->delete();

                    foreach ($request->file('foto', []) as $file) {
                        $path = $file->store('foto-riwayat', 'public');
                        $fotoTersimpan[] = $path;

                        FotoRiwayat::create([
                            'id_riwayat' => $riwayat->id_riwayat,
                            'path' => $path,
                        ]);
                    }
                }

                $laporan->hitungUlangStatus();
            });
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($fotoTersimpan);
            throw $e;
        }

        Storage::disk('public')->delete($fotoLama);

        return redirect()
            ->route('pengelola.laporan.show', $laporan->id_laporan)
            ->with('success', 'Riwayat penanganan berhasil diperbarui.');
    }

    public function destroyRiwayat(
        Laporan $laporan,
        RiwayatPenanganan $riwayat
    ) {
        $this->pastikanMilikPengelola($laporan, $riwayat);

        $fotoLama = $riwayat->foto()
            ->pluck('path')
            ->all();

        DB::transaction(function () use ($laporan, $riwayat) {
            $riwayat->delete();
            $laporan->hitungUlangStatus();
        });

        Storage::disk('public')->delete($fotoLama);

        return redirect()
            ->route('pengelola.laporan.show', $laporan->id_laporan)
            ->with('success', 'Riwayat penanganan berhasil dihapus.');
    }
}
