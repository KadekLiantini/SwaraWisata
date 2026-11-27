<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriKerusakan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KategoriPelaporanController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $status = $request->input('status', 'aktif');

        if (! in_array($status, ['aktif', 'nonaktif', 'semua'], true)) {
            $status = 'aktif';
        }

        $ringkasan = [
            'total' => KategoriKerusakan::count(),
            'aktif' => KategoriKerusakan::active()->count(),
            'nonaktif' => KategoriKerusakan::inactive()->count(),
        ];

        $query = KategoriKerusakan::withCount('laporan');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        if ($status === 'aktif') {
            $query->active();
        } elseif ($status === 'nonaktif') {
            $query->inactive();
        }

        $kategori = $query
            ->orderBy('nama')
            ->paginate(7)
            ->withQueryString();

        return view('admin.kategori-pelaporan.index', compact(
            'kategori',
            'ringkasan',
            'search',
            'status',
        ));
    }

    public function create()
    {
        return view('admin.kategori-pelaporan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100', 'unique:kategori_kerusakan,nama'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        KategoriKerusakan::create($validated);

        return redirect()
            ->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit($id_kategori_kerusakan)
    {
        $kategori = KategoriKerusakan::findOrFail($id_kategori_kerusakan);

        return view('admin.kategori-pelaporan.edit', compact('kategori'));
    }

    public function update(Request $request, $id_kategori_kerusakan)
    {
        $kategori = KategoriKerusakan::findOrFail($id_kategori_kerusakan);

        $validated = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:100',
                Rule::unique('kategori_kerusakan', 'nama')
                    ->ignore($kategori->id_kategori_kerusakan, 'id_kategori_kerusakan'),
            ],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $kategori->update($validated);

        return redirect()
            ->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function updateStatus($id_kategori_kerusakan)
    {
        $kategori = KategoriKerusakan::findOrFail($id_kategori_kerusakan);

        $kategori->is_active = ! $kategori->is_active;
        $kategori->save();

        $pesan = $kategori->is_active
            ? 'Kategori berhasil diaktifkan.'
            : 'Kategori berhasil dinonaktifkan.';

        return redirect()
            ->route('admin.kategori.index')
            ->with('success', $pesan);
    }
}
