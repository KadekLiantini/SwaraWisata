<?php

namespace App\Http\Controllers\Wisatawan;

use App\Http\Controllers\Controller;
use App\Models\Destinasi;
use App\Models\KategoriKerusakan;
use Illuminate\View\View;
use App\Models\Laporan;
use App\Models\FotoLaporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Akun;
use App\Models\RiwayatPenanganan;
use Illuminate\Support\Str;

class LaporanController extends Controller
{
    public function create(Request $request, Destinasi $destinasi): View
    {
        $destinasi->load([
            'kecamatan.kabupaten',
            'kategoriWisata',
        ]);

        $kategoriKerusakan = KategoriKerusakan::orderByRaw("
            CASE WHEN nama = 'Lainnya' THEN 1 ELSE 0 END")->orderBy('nama')->get();

        // Mengambil seluruh laporan pada destinasi ini
        $laporanDasar = Laporan::where('id_destinasi', $destinasi->id_destinasi);

        // Statistik laporan
        $totalAduan = (clone $laporanDasar)->count();

        $belumDilihat = (clone $laporanDasar)
            ->where('status', Laporan::STATUS_BELUM_DILIHAT)
            ->count();

        $menungguTindakan = (clone $laporanDasar)
            ->where('status', Laporan::STATUS_DILIHAT)
            ->count();

        $sedangDiproses = (clone $laporanDasar)
            ->where('status', Laporan::STATUS_DITINDAKLANJUTI)
            ->count();

        $tuntasDiperbaiki = (clone $laporanDasar)
            ->where('status', Laporan::STATUS_SELESAI)
            ->count();

        // Filter status laporan
        $status = $request->query('status');

        $laporanQuery = Laporan::with([
            'pelapor',
            'kategoriKerusakan',
            'foto',
            'riwayat.pengelola',
            'riwayat.foto',
        ])->where('id_destinasi', $destinasi->id_destinasi);

        $daftarStatus = [
            Laporan::STATUS_BELUM_DILIHAT,
            Laporan::STATUS_DILIHAT,
            Laporan::STATUS_DITINDAKLANJUTI,
            Laporan::STATUS_SELESAI,
        ];

        if (in_array($status, $daftarStatus, true)) {
            $laporanQuery->where('status', $status);
        }

        // Pengurutan laporan
        $urutan = $request->query('urutan', 'terbaru');

        if ($urutan === 'terlama') {
            $laporanQuery->orderBy('created_at');
        } else {
            $laporanQuery->orderByDesc('created_at');
        }

        $laporan = $laporanQuery->paginate(3)->withQueryString();

        $laporan->getCollection()->transform(function ($item) {
            $item->laporan_saya = $item->id_akun == auth()->user()->id_akun;

            $item->status_label = match ($item->status) {
                Laporan::STATUS_BELUM_DILIHAT => 'Belum Dilihat',
                Laporan::STATUS_DILIHAT => 'Dilihat',
                Laporan::STATUS_DITINDAKLANJUTI => 'Ditindaklanjuti',
                Laporan::STATUS_SELESAI => 'Selesai',
                default => $item->status,
            };

            $item->status_class = match ($item->status) {
                Laporan::STATUS_BELUM_DILIHAT => 'bg-gray-100 text-gray-600',
                Laporan::STATUS_DILIHAT => 'bg-blue-50 text-blue-700',
                Laporan::STATUS_DITINDAKLANJUTI => 'bg-amber-50 text-amber-700',
                Laporan::STATUS_SELESAI => 'bg-green-50 text-green-700',
                default => 'bg-gray-100 text-gray-600',
            };

            $item->riwayat_terakhir = $item->riwayat->last();

            $item->pesan_penanganan = match ($item->status) {
            'belum_dilihat' => 'Laporan menunggu untuk ditinjau oleh pengelola.',

            'dilihat' => 'Telah ditinjau pengawas lapangan. Menunggu jadwal penanganan.',

            'ditindaklanjuti' => $item->riwayat_terakhir?->deskripsi
                ?? 'Pengelola sedang menindaklanjuti laporan.',

            'selesai' => 'Telah diperbaiki oleh Tim Sarana Pengelola '
                . ($item->riwayat_terakhir?->pengelola?->username ?? 'pengelola'),

            default => '',
        };

            return $item;
        });

        return view('wisatawan.laporan.create', compact(
            'destinasi',
            'kategoriKerusakan',
            'totalAduan',
            'belumDilihat',
            'menungguTindakan',
            'sedangDiproses',
            'tuntasDiperbaiki',
            'laporan',
            'status',
            'urutan',
        ));
    }

    public function show(Laporan $laporan): View
    {
        // Ambil seluruh data laporan dan relasinya
        $laporan->load([
            'pelapor',
            'destinasi.kecamatan.kabupaten',
            'destinasi.kategoriWisata',
            'kategoriKerusakan',
            'foto',
            'riwayat.pengelola',
            'riwayat.foto',
            'riwayat.komentar.penulis',
            'riwayat.komentar.balasan.penulis',
        ]);

        $kategoriKerusakan = KategoriKerusakan::orderByRaw("
            CASE WHEN nama = 'Lainnya' THEN 1 ELSE 0 END")->orderBy('nama')->get();

        $riwayatTerakhir = $laporan->riwayat->last();

        $riwayatTindakLanjut = $laporan->riwayat
            ->where('jenis_tindakan', RiwayatPenanganan::TINDAK_LANJUT)
            ->values();

        $riwayatPenyelesaian = $laporan->riwayat
            ->firstWhere('jenis_tindakan', RiwayatPenanganan::PENYELESAIAN);

        $jumlahKomentar = $laporan->riwayat->sum(
            fn ($riwayat) => $riwayat->komentar->sum(
                fn ($komentar) => 1 + $komentar->balasan->count()
            )
        );

        $pengelolaDestinasi = Akun::query()
            ->where('id_destinasi', $laporan->id_destinasi)
            ->where('role', Akun::ROLE_PENGELOLA)
            ->first();

        $laporanSaya = $laporan->milik(auth()->user());

        // Edit dan hapus hanya diperbolehkan sebelum ditindaklanjuti
        $bolehDiubah = in_array($laporan->status, [
            Laporan::STATUS_BELUM_DILIHAT,
            Laporan::STATUS_DILIHAT,
        ], true);

        // Ambil 2 laporan lain dari destinasi yang sama
        $laporanLain = Laporan::with([
            'pelapor',
            'kategoriKerusakan',
            'foto',
            'riwayat.pengelola',
            'riwayat.foto',
        ])
            ->where('id_destinasi', $laporan->id_destinasi)
            ->where('id_laporan', '!=', $laporan->id_laporan)
            ->latest()
            ->take(2)
            ->get()
            ->map(function ($item) {
                $item->status_tampilan = match ($item->status) {
                    Laporan::STATUS_SELESAI => [
                        'label' => 'Selesai Ditangani',
                        'class' => 'bg-[#e5f5ef] text-[#008568]',
                        'dot' => 'bg-[#008568]',
                    ],
                    Laporan::STATUS_DITINDAKLANJUTI => [
                        'label' => 'Ditindaklanjuti',
                        'class' => 'bg-[#fff3df] text-[#b7791f]',
                        'dot' => 'bg-[#d99a32]',
                    ],
                    Laporan::STATUS_DILIHAT => [
                        'label' => 'Telah Dilihat',
                        'class' => 'bg-[#dee8ff] text-[#496da8]',
                        'dot' => 'bg-[#6689c5]',
                    ],
                    default => [
                        'label' => 'Belum Dilihat',
                        'class' => 'bg-[#f1f3f7] text-[#6e7a74]',
                        'dot' => 'bg-[#9ca3af]',
                    ],
                };

                return $item;
            });

        return view('wisatawan.laporan.show', compact(
            'pengelolaDestinasi',
            'laporan',
            'laporanLain',
            'bolehDiubah',
            'laporanSaya',
            'riwayatTindakLanjut',
            'riwayatPenyelesaian',
            'riwayatTerakhir',
            'jumlahKomentar',
            'kategoriKerusakan'
        ));
    }

    public function store(Request $request, Destinasi $destinasi)
    {
        $validated = $request->validate([
            'id_kategori_kerusakan' => ['required', 'exists:kategori_kerusakan,id_kategori_kerusakan'],
            'judul' => ['required', 'string', 'max:150'],
            'lokasi_spesifik' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string', 'max:1000'],
            'foto' => ['required', 'array', 'min:1', 'max:3'],
            'foto.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        DB::transaction(function () use ($request, $validated, $destinasi) {
            $laporan = Laporan::create([
                'id_akun' => auth()->user()->id_akun,
                'id_destinasi' => $destinasi->id_destinasi,
                'id_kategori_kerusakan' => $validated['id_kategori_kerusakan'],
                'judul' => $validated['judul'],
                'lokasi_spesifik' => $validated['lokasi_spesifik'],
                'deskripsi' => $validated['deskripsi'],
                'status' => Laporan::STATUS_BELUM_DILIHAT,
            ]);

            foreach ($request->file('foto') as $foto) {
                FotoLaporan::create([
                    'id_laporan' => $laporan->id_laporan,
                    'path' => $foto->store('laporan', 'public'),
                ]);
            }
        });

        return redirect()
            ->route('laporan.create', $destinasi->id_destinasi)
            ->with('success', 'Laporan berhasil dikirim.');
    }

        /** Mengirim modal edit laporan (dipakai halaman profil lewat JavaScript) */
    public function edit(Request $request, Laporan $laporan)
    {
        if (! $laporan->milik($request->user())) {
            abort(403, 'Kamu tidak memiliki akses untuk mengubah laporan ini.');
        }

        if (! $laporan->bisaDiubah()) {
            abort_if($request->ajax(), 409);

            return redirect()
                ->route('laporan.show', $laporan->id_laporan)
                ->with('error', 'Laporan tidak dapat diubah karena sudah ditindaklanjuti.');
        }

        // Dibuka langsung tanpa JavaScript: arahkan ke halaman detail
        if (! $request->ajax()) {
            return redirect()->route('laporan.show', $laporan->id_laporan);
        }

        $laporan->load(['foto', 'destinasi']);

        return view('wisatawan.laporan.partials.modal-edit', [
            'laporan'           => $laporan,
            'kategoriKerusakan' => KategoriKerusakan::orderBy('nama')->get(),
            'kembaliKe'         => url()->previous(),
        ]);
    }

    public function update(Request $request, Laporan $laporan)
    {
        // Pastikan laporan merupakan milik wisatawan yang sedang login
        if (!$laporan->milik(auth()->user())) {
            abort(403, 'Kamu tidak memiliki akses untuk mengubah laporan ini.');
        }

        // BARU: halaman tujuan setelah simpan (profil atau detail), hanya jika masih di website ini
        $kembali = (string) $request->input('kembali');
        $tujuan = Str::startsWith($kembali, url('/'))
            ? $kembali
            : route('laporan.show', $laporan->id_laporan);

        // Pastikan status laporan masih memungkinkan untuk diedit
        if (!in_array($laporan->status, [
            Laporan::STATUS_BELUM_DILIHAT,
            Laporan::STATUS_DILIHAT,
        ], true)) {
            return redirect()
                ->to($tujuan) // DIUBAH
                ->with('error', 'Laporan tidak dapat diubah karena sudah ditindaklanjuti.');
        }

        $validated = $request->validate([
            'id_kategori_kerusakan' => [
                'required',
                'exists:kategori_kerusakan,id_kategori_kerusakan',
            ],
            'judul' => ['required', 'string', 'max:150'],
            'lokasi_spesifik' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string', 'max:1000'],
            'foto' => ['nullable', 'array'],
            'foto.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'foto_dihapus' => ['nullable', 'array'],
            'foto_dihapus.*' => ['integer', 'distinct'],
        ]);

        $fotoBaru = $request->file('foto', []);
        $idFotoDihapus = $validated['foto_dihapus'] ?? [];

        // Ambil foto lama yang benar-benar terhubung dengan laporan ini
        $fotoLamaDihapus = $laporan->foto()
            ->whereIn('id_foto_laporan', $idFotoDihapus)
            ->get();

        // Pastikan ID foto yang dikirim memang milik laporan ini
        if ($fotoLamaDihapus->count() !== count($idFotoDihapus)) {
            return back()
                ->withInput()
                ->withErrors([
                    'foto' => 'Terdapat foto yang tidak valid untuk dihapus.',
                ]);
        }

        // Hitung jumlah foto setelah penghapusan dan penambahan
        $jumlahFotoTersisa = $laporan->foto()->count()
            - $fotoLamaDihapus->count()
            + count($fotoBaru);

        if ($jumlahFotoTersisa > 3) {
            return back()
                ->withInput()
                ->withErrors([
                    'foto' => 'Total foto laporan maksimal 3 foto.',
                ]);
        }

        if ($jumlahFotoTersisa < 1) {
            return back()
                ->withInput()
                ->withErrors([
                    'foto' => 'Laporan harus memiliki minimal 1 foto.',
                ]);
        }

        // Simpan path foto lama untuk dihapus dari storage setelah transaksi
        $pathFotoDihapus = [];

        DB::transaction(function () use (
            $validated,
            $laporan,
            $fotoBaru,
            $fotoLamaDihapus,
            &$pathFotoDihapus
        ) {
            $laporan->update([
                'id_kategori_kerusakan' => $validated['id_kategori_kerusakan'],
                'judul' => $validated['judul'],
                'lokasi_spesifik' => $validated['lokasi_spesifik'],
                'deskripsi' => $validated['deskripsi'],
            ]);

            // Hapus data foto lama dari database
            foreach ($fotoLamaDihapus as $foto) {
                $pathFotoDihapus[] = $foto->path;
                $foto->delete();
            }

            // Simpan foto baru
            foreach ($fotoBaru as $foto) {
                FotoLaporan::create([
                    'id_laporan' => $laporan->id_laporan,
                    'path' => $foto->store('laporan', 'public'),
                ]);
            }
        });

        // Hapus file foto lama dari storage
        if (!empty($pathFotoDihapus)) {
            Storage::disk('public')->delete($pathFotoDihapus);
        }

        return redirect()
            ->to($tujuan)
            ->with('success', 'Laporan berhasil diperbarui.');
    }

    public function destroy(Request $request, Laporan $laporan) // DIUBAH: tambah Request $request
    {
        // Pastikan laporan merupakan milik wisatawan yang sedang login
        if (!$laporan->milik(auth()->user())) {
            abort(403, 'Kamu tidak memiliki akses untuk menghapus laporan ini.');
        }

        // BARU: alamat asal (misalnya profil), hanya jika masih di website ini
        $kembali = (string) $request->input('kembali');
        $dariHalamanLain = Str::startsWith($kembali, url('/'));

        // Pastikan status laporan masih memungkinkan untuk dihapus
        if (!in_array($laporan->status, [
            Laporan::STATUS_BELUM_DILIHAT,
            Laporan::STATUS_DILIHAT,
        ], true)) {
            return redirect()
                ->to($dariHalamanLain ? $kembali : route('laporan.show', $laporan->id_laporan)) // DIUBAH
                ->with('error', 'Laporan tidak dapat dihapus karena sudah ditindaklanjuti.');
        }

        $idDestinasi = $laporan->id_destinasi;

        // Hapus laporan menggunakan Soft Delete
        $laporan->delete();

        return redirect()
            ->to($dariHalamanLain ? $kembali : route('laporan.create', $idDestinasi)) // DIUBAH
            ->with('success', 'Laporan berhasil dihapus.');
    }
}