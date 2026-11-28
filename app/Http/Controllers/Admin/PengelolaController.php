<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AkunPengelolaRequest;
use App\Models\Akun;
use App\Models\Destinasi;
use App\Models\Kabupaten;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PengelolaController extends Controller
{
    public function index(Request $request): View
    {
        $search = mb_substr(trim((string) $request->input('search', '')), 0, 100);
        $idKabupaten = (int) $request->input('id_kabupaten', 0);
        $status = (string) $request->input('status', 'semua');
        $sort = (string) $request->input('sort', 'terbaru');

        if (! in_array($status, ['semua', 'aktif', 'nonaktif'], true)) {
            $status = 'semua';
        }

        if (! in_array($sort, ['terbaru', 'terlama', 'username_asc'], true)) {
            $sort = 'terbaru';
        }

        $query = Akun::query()
            ->where('role', Akun::ROLE_PENGELOLA)
            ->with(['destinasi.kecamatan.kabupaten', 'destinasi.kategoriWisata']);

        if ($search !== '') {
            $like = '%' . addcslashes($search, '%_\\') . '%';
            $query->where(function ($sub) use ($like) {
                $sub->where('username', 'like', $like)
                    ->orWhereHas('destinasi', fn ($q) => $q->where('nama', 'like', $like));
            });
        }

        if ($idKabupaten > 0) {
            $query->whereHas('destinasi.kecamatan', fn ($q) => $q->where('id_kabupaten', $idKabupaten));
        }

        if ($status === 'aktif') {
            $query->whereHas('destinasi', fn ($q) => $q->where('is_active', true));
        } elseif ($status === 'nonaktif') {
            $query->whereHas('destinasi', fn ($q) => $q->where('is_active', false));
        }

        match ($sort) {
            'terlama' => $query->orderBy('created_at', 'asc')->orderBy('id_akun', 'asc'),
            'username_asc' => $query->orderBy('username', 'asc'),
            default => $query->orderBy('created_at', 'desc')->orderBy('id_akun', 'desc'),
        };

        $pengelola = $query->paginate(10)->withQueryString();

        $kabupaten = Kabupaten::orderBy('nama')->get();

        $destinasiTersedia = Destinasi::with(['kecamatan.kabupaten', 'kategoriWisata'])
            ->doesntHave('pengelola')
            ->orderBy('nama')
            ->get();

        $semuaDestinasi = Destinasi::with(['kecamatan.kabupaten', 'kategoriWisata', 'pengelola'])
            ->orderBy('nama')
            ->get();

        $totalPengelola = Akun::where('role', Akun::ROLE_PENGELOLA)->count();
        $pengelolaAktif = Akun::where('role', Akun::ROLE_PENGELOLA)
            ->whereHas('destinasi', fn ($q) => $q->where('is_active', true))
            ->count();

        $ringkasan = [
            'total' => $totalPengelola,
            'aktif' => $pengelolaAktif,
            'nonaktif' => max(0, $totalPengelola - $pengelolaAktif),
            'belum_terhubung' => $destinasiTersedia->count(),
        ];

        $filter = [
            'search' => $search,
            'id_kabupaten' => $idKabupaten,
            'status' => $status,
            'sort' => $sort,
        ];

        $bukaTambah = $request->boolean('terbitkan');

        return view('admin.pengelola.index', compact(
            'pengelola',
            'kabupaten',
            'destinasiTersedia',
            'semuaDestinasi',
            'ringkasan',
            'filter',
            'bukaTambah'
        ));
    }

    public function store(AkunPengelolaRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $akun = DB::transaction(fn () => Akun::create([
                'username' => $validated['username'],
                'password' => $validated['password'],
                'role' => Akun::ROLE_PENGELOLA,
                'id_destinasi' => (int) $validated['id_destinasi'],
            ]));

            return redirect()
                ->route('admin.pengelola.index')
                ->with('success', 'Akun pengelola @' . $akun->username . ' berhasil diterbitkan.');
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('buka_tambah_pengelola', true)
                ->with('error', 'Terjadi kesalahan saat menerbitkan akun pengelola. Silakan coba lagi.');
        }
    }

public function update(AkunPengelolaRequest $request, int $id_akun): RedirectResponse
    {
        $pengelola = Akun::where('role', Akun::ROLE_PENGELOLA)->findOrFail($id_akun);
        $validated = $request->validated();

        try {
            DB::transaction(function () use ($pengelola, $validated) {
                $data = [
                    'username' => $validated['username'],
                    'id_destinasi' => (int) $validated['id_destinasi'],
                ];

                if (! empty($validated['password'])) {
                    $data['password'] = $validated['password'];
                }

                $pengelola->update($data);
            });

            return redirect()
                ->route('admin.pengelola.index')
                ->with('success', 'Data akun pengelola @' . $pengelola->username . ' berhasil diperbarui.');
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('buka_edit_pengelola_id', $pengelola->id_akun)
                ->with('error', 'Terjadi kesalahan saat memperbarui akun pengelola.');
        }
    }

    public function destroy(int $id_akun): RedirectResponse
    {
        $pengelola = Akun::where('role', Akun::ROLE_PENGELOLA)->findOrFail($id_akun);

        if ($pengelola->riwayatPenanganan()->exists() || $pengelola->komentar()->exists()) {
            return redirect()
                ->route('admin.pengelola.index')
                ->with('error', 'Akun @' . $pengelola->username . ' memiliki rekam jejak penanganan laporan sehingga tidak dapat dihapus. Gunakan fitur Edit untuk mereset kata sandinya.');
        }

        $username = $pengelola->username;

        try {
            DB::transaction(fn () => $pengelola->delete());

            return redirect()
                ->route('admin.pengelola.index')
                ->with('success', 'Akun pengelola @' . $username . ' berhasil dihapus.');
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('admin.pengelola.index')
                ->with('error', 'Akun pengelola tidak dapat dihapus karena masih terhubung dengan data lain.');
        }
    }
}