<?php

namespace App\Http\Controllers\Pengelola;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\RiwayatPenanganan;
use App\Models\FotoRiwayat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\KategoriKerusakan;
use App\Models\Komentar;

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
    $pengelola = auth()->user();
    $idDestinasi = $pengelola->id_destinasi;

    // Filter status
    $filter = $request->query('filter', 'semua');

    if (!in_array($filter, [
        'semua',
        'dilihat',
        'ditindaklanjuti',
        'selesai',
    ], true)) {
        $filter = 'semua';
    }

    // Pencarian
    $search = trim($request->query('search', ''));

    // Filter kategori
    $kategori = $request->query('kategori', '');

    // Pengurutan
    $sort = $request->query('sort', 'terbaru');

    if (!in_array($sort, ['terbaru', 'terlama'], true)) {
        $sort = 'terbaru';
    }

    // Query dasar laporan milik destinasi pengelola
    $laporanDasar = Laporan::query()
        ->where('id_destinasi', $idDestinasi ?: 0);

    // Hitung ringkasan status
    $perluDitinjau = (clone $laporanDasar)
        ->where('status', Laporan::STATUS_BELUM_DILIHAT)
        ->count();

    $dalamPeninjauan = (clone $laporanDasar)
        ->where('status', Laporan::STATUS_DILIHAT)
        ->count();

    $sedangDiperbaiki = (clone $laporanDasar)
        ->where('status', Laporan::STATUS_DITINDAKLANJUTI)
        ->count();

    $selesaiDiverifikasi = (clone $laporanDasar)
        ->where('status', Laporan::STATUS_SELESAI)
        ->count();

    // Ambil laporan beserta relasinya
    $query = Laporan::with([
        'pelapor',
        'destinasi',
        'kategoriKerusakan',
        'foto',
    ])->where('id_destinasi', $idDestinasi ?: 0);

    // Filter status
    if ($filter === 'dilihat') {
        $query->where('status', Laporan::STATUS_DILIHAT);
    } elseif ($filter === 'ditindaklanjuti') {
        $query->where('status', Laporan::STATUS_DITINDAKLANJUTI);
    } elseif ($filter === 'selesai') {
        $query->where('status', Laporan::STATUS_SELESAI);
    }

    // Pencarian judul, ID, atau username pelapor
    if ($search !== '') {
        $idSearch = ltrim($search, '#');

        $query->where(function ($q) use ($search, $idSearch) {
            $q->where('judul', 'like', "%{$search}%")
                ->orWhereHas('pelapor', function ($pelaporQuery) use ($search) {
                    $pelaporQuery->where(
                        'username',
                        'like',
                        "%{$search}%"
                    );
                });

            if ($idSearch !== '' && ctype_digit($idSearch)) {
                $q->orWhere('id_laporan', (int) $idSearch);
            }
        });
    }

    // Filter kategori
    if ($kategori !== '') {
        $query->where('id_kategori_kerusakan', $kategori);
    }

    // Pengurutan laporan
    $query->orderBy(
        'created_at',
        $sort === 'terbaru' ? 'desc' : 'asc'
    );

    // Pagination: 6 laporan per halaman
    $laporan = $query->paginate(6)->withQueryString();

    // Daftar kategori untuk dropdown
    $daftarKategori = KategoriKerusakan::query()
    ->orderByRaw("CASE WHEN LOWER(nama) = 'lainnya' THEN 1 ELSE 0 END")
    ->orderBy('nama')
    ->get();

    return view('pengelola.laporan.daftar', compact(
        'laporan',
        'filter',
        'search',
        'kategori',
        'sort',
        'daftarKategori',
        'perluDitinjau',
        'dalamPeninjauan',
        'sedangDiperbaiki',
        'selesaiDiverifikasi'
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
            'riwayat.komentar.penulis',
            'riwayat.komentar.balasan.penulis',
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
                'max:500',
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
                'max:2048',
            ],
        ], [
            'jenis_tindakan.required' => 'Jenis tindakan wajib dipilih.',
            'deskripsi.required' => 'Deskripsi penanganan wajib diisi.',
            'foto.required' => 'Foto bukti wajib diunggah.',
            'foto.min' => 'Minimal unggah satu foto bukti.',
            'foto.*.image' => 'File bukti harus berupa gambar.',
            'foto.*.max' => 'Ukuran setiap foto maksimal 2 MB.',
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
        RiwayatPenanganan $riwayat) {
        $this->pastikanMilikPengelola($laporan, $riwayat);

        $validated = $request->validate([
            'deskripsi' => [
                'required',
                'string',
                'max:500',
            ],
            'foto' => [
                'sometimes',
                'array',
                'min:1',
            ],
            'foto.*' => [
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ], [
            'deskripsi.required' => 'Deskripsi penanganan wajib diisi.',
            'foto.min' => 'Minimal unggah satu foto jika ingin mengganti foto bukti.',
            'foto.*.image' => 'File bukti harus berupa gambar.',
            'foto.*.max' => 'Ukuran setiap foto maksimal 2 MB.',
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

    public function storeBalasanKomentar(Request $request, Laporan $laporan, RiwayatPenanganan $riwayat, Komentar $komentar
    ) {
        $this->pastikanMilikDestinasi($laporan);

        abort_unless(
            (int) $riwayat->id_laporan === (int) $laporan->id_laporan,
            404
        );

        abort_unless(
            (int) $komentar->id_riwayat === (int) $riwayat->id_riwayat
            && $komentar->id_parent === null,
            404
        );

        $validated = $request->validate([
            'isi' => ['required', 'string', 'max:300'],
        ]);
        
        // Cegah pengelola membalas komentar yang sudah ditanggapi
        if ($komentar->balasan()->exists()) {
            return redirect()
                ->route('pengelola.laporan.show', $laporan->id_laporan)
                ->with('error', 'Komentar ini sudah memiliki tanggapan resmi.');
        }

        Komentar::create([
            'id_riwayat' => $riwayat->id_riwayat,
            'id_akun' => auth()->user()->id_akun,
            'id_parent' => $komentar->id_komentar,
            'isi' => $validated['isi'],
        ]);

        return redirect()
            ->route('pengelola.laporan.show', $laporan->id_laporan)
            ->with('success', 'Tanggapan berhasil dikirim.');
    }
}
