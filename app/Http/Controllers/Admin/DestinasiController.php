<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DestinasiRequest;
use App\Models\Akun;
use App\Models\Destinasi;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\KategoriWisata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DestinasiController extends Controller
{
    /** Direktori foto destinasi pada disk public. */
    private const FOTO_DIRECTORY = 'destinasi';

    /**
     * Daftar destinasi dari database dengan relasi yang dipakai tabel Admin.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $idKabupaten = (int) $request->input('id_kabupaten', 0);
        $status = strtolower(trim((string) $request->input('status', '')));
        $sort = strtolower(trim((string) $request->input('sort', '')));

        if (! in_array($status, ['terhubung', 'belum_terhubung'], true)) {
            $status = '';
        }

        if ($sort !== 'nama_az') {
            $sort = '';
        }

        $ringkasan = [
            'total' => Destinasi::count(),
            'terhubung' => Destinasi::whereHas('pengelola', function ($q) {
                $q->where('akun.role', Akun::ROLE_PENGELOLA);
            })->count(),
            'belum_terhubung' => Destinasi::whereDoesntHave('pengelola', function ($q) {
                $q->where('akun.role', Akun::ROLE_PENGELOLA);
            })->count(),
        ];

        $query = Destinasi::with([
            'kecamatan.kabupaten',
            'kategoriWisata',
            'pengelola' => function ($q) {
                $q->where('akun.role', Akun::ROLE_PENGELOLA);
            },
        ])->withCount('laporan');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('destinasi.nama', 'like', "%{$search}%")
                    ->orWhereHas('kecamatan', function ($q) use ($search) {
                        $q->where('kecamatan.nama', 'like', "%{$search}%");
                    });
            });
        }

        if ($idKabupaten > 0) {
            $query->whereHas('kecamatan', function ($q) use ($idKabupaten) {
                $q->where('kecamatan.id_kabupaten', $idKabupaten);
            });
        }

        if ($status === 'terhubung') {
            $query->whereHas('pengelola', function ($q) {
                $q->where('akun.role', Akun::ROLE_PENGELOLA);
            });
        } elseif ($status === 'belum_terhubung') {
            $query->whereDoesntHave('pengelola', function ($q) {
                $q->where('akun.role', Akun::ROLE_PENGELOLA);
            });
        }

        $params = array_filter([
            'search' => $search,
            'id_kabupaten' => $idKabupaten > 0 ? $idKabupaten : '',
            'status' => $status,
            'sort' => $sort,
        ], fn ($value) => $value !== null && $value !== '');

        if ($sort === 'nama_az') {
            $query->orderBy('destinasi.nama')->orderBy('destinasi.id_destinasi');
        } else {
            $query->orderByDesc('destinasi.created_at')->orderBy('destinasi.id_destinasi');
        }

        $destinasi = $query->paginate(5)
            ->appends($params);

        $kabupaten = Kabupaten::orderBy('nama')->pluck('nama', 'id_kabupaten');

        return view('admin.destinasi.index', compact('destinasi', 'ringkasan', 'kabupaten'));
    }

    /**
     * Form tambah: kategori dan kecamatan diambil dari database.
     */
    public function create()
    {
        $kategoriWisata = KategoriWisata::orderBy('nama')->pluck('nama', 'id_kategori_wisata');
        $kecamatan = Kecamatan::with('kabupaten')
            ->get()
            ->sortBy([
                ['kabupaten.nama', 'asc'],
                ['nama', 'asc'],
            ])
            ->mapWithKeys(function (Kecamatan $item) {
                $wilayah = $item->kabupaten ? $item->kabupaten->nama : '-';
                $label = trim($item->nama.' - '.$wilayah);

                return [$item->id_kecamatan => $label];
            });

        return view('admin.destinasi.create', compact('kategoriWisata', 'kecamatan'));
    }

    /**
     * Simpan destinasi baru.
     */
    public function store(DestinasiRequest $request)
    {
        $validated = $request->validated();
        $data = [
            'nama' => $validated['nama'],
            'id_kategori_wisata' => (int) $validated['id_kategori_wisata'],
            'id_kecamatan' => (int) $validated['id_kecamatan'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'link_maps' => $validated['link_maps'] ?? null,
        ];

        $fotoBaru = null;

        try {
            $fotoBaru = $this->storeFoto($request);
            $data['foto'] = $fotoBaru;

            Destinasi::create($data);
        } catch (\Throwable $e) {
            $this->removeFotoIfUploaded($fotoBaru);

            report($e);

            return redirect()
                ->route('admin.destinasi.create')
                ->withInput()
                ->with('error', 'Destinasi gagal disimpan. Silakan coba lagi.');
        }

        return redirect()
            ->route('admin.destinasi.index')
            ->with('success', 'Destinasi berhasil ditambahkan.');
    }

    /**
     * Form ubah dengan data destinasi yang akan diedit.
     */
    public function edit($id_destinasi)
    {
        $destinasi = Destinasi::findOrFail($id_destinasi);

        $kategoriWisata = KategoriWisata::orderBy('nama')->pluck('nama', 'id_kategori_wisata');
        $kecamatan = Kecamatan::with('kabupaten')
            ->get()
            ->sortBy([
                ['kabupaten.nama', 'asc'],
                ['nama', 'asc'],
            ])
            ->mapWithKeys(function (Kecamatan $item) {
                $wilayah = $item->kabupaten ? $item->kabupaten->nama : '-';
                $label = trim($item->nama.' - '.$wilayah);

                return [$item->id_kecamatan => $label];
            });

        return view('admin.destinasi.edit', compact('destinasi', 'kategoriWisata', 'kecamatan'));
    }

    /**
     * Perbarui destinasi.
     */
    public function update(DestinasiRequest $request, $id_destinasi)
    {
        $destinasi = Destinasi::findOrFail($id_destinasi);
        $fotoLama = $destinasi->foto;

        $validated = $request->validated();
        $data = [
            'nama' => $validated['nama'],
            'id_kategori_wisata' => (int) $validated['id_kategori_wisata'],
            'id_kecamatan' => (int) $validated['id_kecamatan'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'link_maps' => $validated['link_maps'] ?? null,
        ];

        try {
            $fotoBaru = $this->storeFoto($request);
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('admin.destinasi.edit', ['id_destinasi' => $id_destinasi])
                ->withInput()
                ->with('error', 'Foto gagal diunggah. Silakan coba lagi.');
        }

        if ($fotoBaru !== null) {
            $data['foto'] = $fotoBaru;
        }

        try {
            DB::transaction(function () use ($destinasi, $data) {
                $destinasi->update($data);
            });
        } catch (\Throwable $e) {
            // DB gagal: foto lama tetap utuh, foto baru yang sudah diunggah dibersihkan.
            $this->removeFotoIfUploaded($fotoBaru);

            report($e);

            return redirect()
                ->route('admin.destinasi.edit', ['id_destinasi' => $id_destinasi])
                ->withInput()
                ->with('error', 'Destinasi gagal diperbarui. Silakan coba lagi.');
        }

        // DB berhasil: hapus foto lama hanya jika foto benar-benar diganti.
        if ($fotoBaru !== null && $fotoBaru !== $fotoLama) {
            $this->removeFotoIfUploaded($fotoLama);
        }

        return redirect()
            ->route('admin.destinasi.index')
            ->with('success', 'Destinasi berhasil diperbarui.');
    }

    /**
     * Konfirmasi penghapusan.
     */
    public function delete($id_destinasi)
    {
        $destinasi = Destinasi::with(['kecamatan.kabupaten', 'kategoriWisata'])
            ->findOrFail($id_destinasi);

        return view('admin.destinasi.delete', compact('destinasi'));
    }

    /**
     * Hapus destinasi. File foto dihapus hanya setelah data berhasil dihapus.
     */
    public function destroy($id_destinasi)
    {
        $destinasi = Destinasi::findOrFail($id_destinasi);
        $foto = $destinasi->foto;

        try {
            DB::transaction(function () use ($destinasi) {
                $destinasi->delete();
            });
        } catch (\Throwable $e) {
            // DB gagal: jangan sentuh file foto.
            report($e);

            return redirect()
                ->route('admin.destinasi.index')
                ->with('error', 'Destinasi gagal dihapus. Pastikan tidak ada data lain yang masih terhubung.');
        }

        $this->removeFotoIfUploaded($foto);

        return redirect()
            ->route('admin.destinasi.index')
            ->with('success', 'Destinasi berhasil dihapus.');
    }

    /**
     * Simpan satu foto ke disk public. Tidak menimpa file yang sudah ada.
     */
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
            // Bersihkan file parsial jika proses tulis gagal, lalu teruskan errornya.
            $this->removeFotoIfUploaded($path);

            throw $e;
        }

        if (! is_string($tersimpan) || $tersimpan === '') {
            $this->removeFotoIfUploaded($path);

            throw new \RuntimeException('Gagal menyimpan foto destinasi.');
        }

        return $tersimpan;
    }

    /**
     * Hapus file foto secara aman.
     *
     * File tidak dihapus jika path masih direferensikan oleh record Destinasi lain.
     * Kegagalan penghapusan file sengaja tidak dilempar agar operasi database
     * yang sudah berhasil tidak berubah menjadi gagal.
     */
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
            // Referensi tidak dapat dipastikan: jangan hapus file.
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

    /**
     * Terima hanya satu file langsung di dalam direktori foto destinasi.
     */
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
}
