<?php

namespace App\Http\Requests\Admin;

use App\Models\Kecamatan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class VerifikasiPengajuanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:150'],
            'id_kategori_wisata' => ['required', 'integer', 'exists:kategori_wisata,id_kategori_wisata'],
            'id_kabupaten' => ['nullable', 'integer', 'exists:kabupaten,id_kabupaten'],
            'id_kecamatan' => ['required', 'integer', 'exists:kecamatan,id_kecamatan'],
            'deskripsi' => ['required', 'string', 'max:500'],
            // Hanya http/https agar tautan tidak bisa memakai skema berbahaya seperti javascript:.
            'link_maps' => ['required', 'string', 'max:500', 'url:http,https'],
            'foto' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $idKabupaten = $this->input('id_kabupaten');

            if ($idKabupaten === null || $idKabupaten === '') {
                return;
            }

            $cocok = Kecamatan::query()
                ->where('id_kecamatan', $this->input('id_kecamatan'))
                ->where('id_kabupaten', $idKabupaten)
                ->exists();

            if (! $cocok) {
                $validator->errors()->add(
                    'id_kecamatan',
                    'Kecamatan yang dipilih tidak termasuk kabupaten/kota tersebut.'
                );
            }
        });
    }

    /**
     * Validasi gagal langsung kembali ke daftar pengajuan agar pop-up verifikasi terbuka ulang.
     */
    protected function getRedirectUrl(): string
    {
        return route('admin.pengajuan.index');
    }

    public function attributes(): array
    {
        return [
            'nama' => 'nama tempat wisata',
            'id_kategori_wisata' => 'kategori wisata',
            'id_kabupaten' => 'kabupaten/kota',
            'id_kecamatan' => 'kecamatan',
            'deskripsi' => 'deskripsi destinasi',
            'link_maps' => 'tautan titik lokasi Google Maps',
            'foto' => 'foto potensi resmi',
        ];
    }
}
