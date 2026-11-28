<?php

namespace App\Http\Requests\Admin;

use App\Models\Akun;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AkunPengelolaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $username = ltrim(trim((string) $this->input('username', '')), '@');

        $this->merge([
            'username' => strtolower($username),
        ]);
    }

    public function rules(): array
    {
        $pengelola = $this->route('pengelola');
        $idAkun = (int) $this->route('pengelola');
        $isUpdate = $idAkun > 0;

        return [
            'id_destinasi' => [
                'required',
                'integer',
                'exists:destinasi,id_destinasi',
                Rule::unique('akun', 'id_destinasi')->ignore($idAkun, 'id_akun'),
            ],
            'username' => [
                'required',
                'string',
                'min:4',
                'max:50',
                'regex:/^[a-z0-9_]+$/',
                Rule::unique('akun', 'username')->ignore($idAkun, 'id_akun'),
            ],
            'password' => $isUpdate
                ? ['nullable', 'string', 'min:8', 'max:100']
                : ['required', 'string', 'min:8', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_destinasi.required' => 'Destinasi wisata wajib dipilih.',
            'id_destinasi.exists' => 'Destinasi wisata tidak ditemukan.',
            'id_destinasi.unique' => 'Destinasi wisata ini sudah memiliki akun pengelola.',
            'username.required' => 'Username pengelola wajib diisi.',
            'username.min' => 'Username minimal 4 karakter.',
            'username.max' => 'Username maksimal 50 karakter.',
            'username.regex' => 'Username hanya boleh berisi huruf kecil, angka, dan underscore (_).',
            'username.unique' => 'Username sudah digunakan oleh akun lain.',
            'password.required' => 'Kata sandi kredensial wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.max' => 'Kata sandi maksimal 100 karakter.',
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator): void
    {
        $idAkun = (int) $this->route('pengelola');
        $key = $idAkun > 0 ? 'buka_edit_pengelola_id' : 'buka_tambah_pengelola';
        $val = $idAkun > 0 ? $idAkun : true;

        session()->flash($key, $val);

        parent::failedValidation($validator);
    }
}