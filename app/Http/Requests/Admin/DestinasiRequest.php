<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DestinasiRequest extends FormRequest
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
            'id_kecamatan' => ['required', 'integer', 'exists:kecamatan,id_kecamatan'],
            'deskripsi' => ['nullable', 'string'],
            'link_maps' => ['nullable', 'url', 'max:500'],
            'foto' => ['nullable', 'file', 'mimetypes:image/jpeg,image/png,image/webp', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nama' => 'nama destinasi',
            'id_kategori_wisata' => 'kategori wisata',
            'id_kecamatan' => 'kecamatan',
            'link_maps' => 'link maps',
        ];
    }
}
