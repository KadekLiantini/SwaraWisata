<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\VerifikasiPengajuanRequest;
use App\Models\Destinasi;
use App\Models\Kabupaten;
use App\Models\KategoriWisata;
use App\Models\Kecamatan;
use App\Models\PengajuanDestinasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PengajuanController extends Controller
{
    /** Jumlah baris per halaman pada daftar pengajuan. */
    private const PER_PAGE = 6;

    /** Batas panjang kata kunci pencarian agar query tetap wajar. */
    private const SEARCH_MAX_LENGTH = 100;

    /** Direktori foto destinasi pada disk public. */
    private const FOTO_DIRECTORY = 'destinasi';

    /** Nilai status yang boleh dipakai sebagai filter. */
    private const STATUS_VALID = [
        PengajuanDestinasi::MENUNGGU,
        PengajuanDestinasi::DISETUJUI,
        PengajuanDestinasi::DITOLAK,
    ];

    /** Daftar pengajuan lokasi wisata beserta data pendukung modal verifikasi. */
    public function index(Request $request): View
    {
        $searchInput = $request->input('search', '');
        $search = is_string($searchInput) ? trim($searchInput) : '';
        $search = mb_substr($search, 0, self::SEARCH_MAX_LENGTH);

        $statusInput = $request->input('status', 'semua');
        $status = is_string($statusInput) ? $statusInput : 'semua';

        if ($status !== 'semua' && ! in_array($status, self::STATUS_VALID, true)) {
            $status = 'semua';
        }

        $kabupatenInput = $request->input('id_kabupaten', 0);
        $idKabupaten = is_scalar($kabupatenInput) ? max(0, (int) $kabupatenInput) : 0;

        $sortInput = $request->input('sort', 'terbaru');
        $sort = is_string($sortInput) ? $sortInput : 'terbaru';

        if (! in_array($sort, ['terbaru', 'terlama'], true)) {
            $sort = 'terbaru';
        }

        $ringkasan = $this->ringkasanStatus();

        $query = PengajuanDestinasi::query()
            ->with([
                'pengaju',
                'verifikator',
                'kecamatan.kabupaten',
                'kategoriWisata',
                'destinasi',
            ]);

        if ($search !== '') {
            // OR dikelompokkan agar tidak merusak filter status dan kabupaten.
            $query->where(function ($q) use ($search) {
                $q->where('pengajuan_destinasi.nama_lokasi', 'like', "%{$search}%")
                    ->orWhere('pengajuan_destinasi.link_maps', 'like', "%{$search}%")
                    ->orWhereHas('pengaju', function ($q) use ($search) {
                        $q->where('akun.username', 'like', "%{$search}%");
                    });
            });
        }

        if ($status !== 'semua') {
            $query->where('pengajuan_destinasi.status', $status);
        }

        if ($idKabupaten > 0) {
            $query->whereHas('kecamatan', function ($q) use ($idKabupaten) {
                $q->where('kecamatan.id_kabupaten', $idKabupaten);
            });
        }

        if ($sort === 'terlama') {
            $query->orderBy('pengajuan_destinasi.created_at')
                ->orderBy('pengajuan_destinasi.id_pengajuan');
        } else {
            $query->orderByDesc('pengajuan_destinasi.created_at')
                ->orderByDesc('pengajuan_destinasi.id_pengajuan');
        }

        $pengajuan = $query->paginate(self::PER_PAGE)->withQueryString();

        $kabupaten = Kabupaten::query()->orderBy('nama')->get();
        $kecamatan = Kecamatan::query()->with('kabupaten')->orderBy('nama')->get();
        $kategoriWisata = KategoriWisata::query()->orderBy('nama')->get();

        $filter = [
            'search' => $search,
            'status' => $status,
            'id_kabupaten' => $idKabupaten,
            'sort' => $sort,
        ];

        $verifikasiId = max(0, (int) $request->query('verifikasi', 0));

        return view('admin.pengajuan.index', compact(
            'pengajuan',
            'ringkasan',
            'kabupaten',
            'kecamatan',
            'kategoriWisata',
            'filter',
            'verifikasiId'
        ));
    }

    /** Pertahankan kompatibilitas tautan lama dengan mengarahkan langsung ke modal daftar pengajuan. */
    public function show(PengajuanDestinasi $pengajuan): RedirectResponse
    {
        $params = $this->masihMenunggu($pengajuan)
            ? ['verifikasi' => $pengajuan->id_pengajuan]
            : [];

        return redirect()->route('admin.pengajuan.index', $params);
    }

    /** Setujui pengajuan sekaligus membuat satu destinasi baru dalam satu transaksi. */
    public function setujui(VerifikasiPengajuanRequest $request, PengajuanDestinasi $pengajuan): RedirectResponse
    {
        if (! $this->masihMenunggu($pengajuan)) {
            return redirect()
                ->route('admin.pengajuan.index')
                ->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $validated = $request->validated();
        $fotoPath = null;

        try {
            $fotoPath = $this->storeFoto($request);

            DB::transaction(function () use ($pengajuan, $validated, $fotoPath) {
                $terkunci = PengajuanDestinasi::query()
                    ->lockForUpdate()
                    ->find($pengajuan->id_pengajuan);

                if (! $terkunci || ! $this->masihMenunggu($terkunci)) {
                    throw new \RuntimeException('Pengajuan sudah diproses oleh admin lain.');
                }

                $destinasi = new Destinasi([
                    'nama' => $validated['nama'],
                    'id_kecamatan' => (int) $validated['id_kecamatan'],
                    'id_kategori_wisata' => (int) $validated['id_kategori_wisata'],
                    'deskripsi' => $validated['deskripsi'],
                    'link_maps' => $validated['link_maps'],
                    'foto' => $fotoPath,
                ]);
                $destinasi->is_active = true;
                $destinasi->save();

                $terkunci->update([
                    'id_destinasi' => $destinasi->id_destinasi,
                    'status' => PengajuanDestinasi::DISETUJUI,
                    'id_verifikator' => Auth::id(),
                    'diverifikasi_pada' => now(),
                ]);
            });
        } catch (\Throwable $e) {
            $this->removeFotoIfUploaded($fotoPath);

            report($e);

            return redirect()
                ->route('admin.pengajuan.index')
                ->withInput()
                ->with('error', 'Pengajuan gagal disetujui. Silakan coba lagi.')
                ->with('buka_verifikasi', true);
        }

        return redirect()
            ->route('admin.pengajuan.index')
            ->with('success', 'Pengajuan berhasil disetujui dan destinasi baru telah ditambahkan.');
    }

    /** Tolak pengajuan yang masih menunggu verifikasi. */
    public function tolak(PengajuanDestinasi $pengajuan): RedirectResponse
    {
        if (! $this->masihMenunggu($pengajuan)) {
            return redirect()
                ->route('admin.pengajuan.index')
                ->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        try {
            DB::transaction(function () use ($pengajuan) {
                $terkunci = PengajuanDestinasi::query()
                    ->lockForUpdate()
                    ->find($pengajuan->id_pengajuan);

                if (! $terkunci || ! $this->masihMenunggu($terkunci)) {
                    throw new \RuntimeException('Pengajuan sudah diproses oleh admin lain.');
                }

                $terkunci->update([
                    'status' => PengajuanDestinasi::DITOLAK,
                    'id_verifikator' => Auth::id(),
                    'diverifikasi_pada' => now(),
                ]);
            });
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('admin.pengajuan.index')
                ->with('error', 'Pengajuan gagal ditolak. Silakan coba lagi.');
        }

        return redirect()
            ->route('admin.pengajuan.index')
            ->with('success', 'Pengajuan lokasi berhasil ditolak.');
    }

    /** Pengajuan hanya boleh diproses bila masih menunggu dan belum punya destinasi. */
    private function masihMenunggu(PengajuanDestinasi $pengajuan): bool
    {
        return $pengajuan->status === PengajuanDestinasi::MENUNGGU
            && $pengajuan->id_destinasi === null;
    }

    /** Simpan satu foto ke disk public. Tidak menimpa file yang sudah ada. */
    private function storeFoto(Request $request): ?string
    {
        if (! $request->hasFile('foto')) {
            return null;
        }

        $file = $request->file('foto');

        // Ekstensi ditentukan dari hasil deteksi MIME server, bukan dari nama/ekstensi asli.
        $mime = $file->getMimeType();
        $petaEkstensi = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];

        if (! is_string($mime) || ! isset($petaEkstensi[$mime])) {
            throw new \RuntimeException('Format foto tidak didukung.');
        }

        $ekstensi = $petaEkstensi[$mime];
        $nama = 'destinasi-'.date('YmdHis').'-'.Str::random(12).'.'.$ekstensi;
        $path = self::FOTO_DIRECTORY.'/'.$nama;

        try {
            $tersimpan = $file->storeAs(self::FOTO_DIRECTORY, $nama, [
                'disk' => 'public',
                'visibility' => 'public',
            ]);
        } catch (\Throwable $e) {
            $this->removeFotoIfUploaded($path);

            throw $e;
        }

        if (! is_string($tersimpan) || $tersimpan === '') {
            $this->removeFotoIfUploaded($path);

            throw new \RuntimeException('Gagal menyimpan foto destinasi.');
        }

        return $tersimpan;
    }

    private function removeFotoIfUploaded(?string $path): void
    {
        $path = $this->fotoPathForDeletion($path);

        if ($path === null) {
            return;
        }

        try {
            if (Destinasi::where('foto', $path)->exists()) {
                return;
            }
        } catch (\Throwable $e) {
            report($e);

            return;
        }

        try {
            $disk = Storage::disk('public');

            if ($disk->exists($path) && ! $disk->delete($path)) {
                report(new \RuntimeException('Gagal menghapus file foto destinasi: '.$path));
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /** Terima hanya satu file langsung di dalam direktori foto destinasi. */
    private function fotoPathForDeletion(?string $path): ?string
    {
        if ($path === null) {
            return null;
        }

        $path = str_replace('\\', '/', trim($path));
        $prefix = self::FOTO_DIRECTORY.'/';

        if (! str_starts_with($path, $prefix)) {
            return null;
        }

        $namaFile = substr($path, strlen($prefix));

        if (
            $namaFile === ''
            || $namaFile === '.'
            || $namaFile === '..'
            || str_contains($namaFile, '/')
            || str_contains($namaFile, "\0")
            || ! preg_match('/\.(jpg|jpeg|png|webp)$/i', $namaFile)
        ) {
            return null;
        }

        return $prefix.$namaFile;
    }

    /** Jumlah pengajuan per status, dihitung langsung dari database. */
    private function ringkasanStatus(): array
    {
        $jumlahStatus = PengajuanDestinasi::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $ringkasan = [
            'total' => 0,
            PengajuanDestinasi::MENUNGGU => 0,
            PengajuanDestinasi::DISETUJUI => 0,
            PengajuanDestinasi::DITOLAK => 0,
        ];

        foreach (self::STATUS_VALID as $status) {
            $ringkasan[$status] = (int) ($jumlahStatus[$status] ?? 0);
        }

        $ringkasan['total'] = (int) $jumlahStatus->sum();

        return $ringkasan;
    }
}