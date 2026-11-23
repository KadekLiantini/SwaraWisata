<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** Aturan bisnis no. 2: registrasi hanya username + password, username unik */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'min:4', 'max:20', 'regex:/^[A-Za-z0-9_]+$/', 'unique:akun,username'],
            'password' => ['required', 'string', 'confirmed', Password::min(8)],
        ];
    }

    public function messages(): array
    {
        return [
            'username.required'  => 'Username wajib diisi.',
            'username.min'       => 'Username minimal 4 karakter.',
            'username.max'       => 'Username maksimal 20 karakter.',
            'username.regex'     => 'Username hanya boleh berisi huruf, angka, dan underscore (tanpa spasi).',
            'username.unique'    => 'Username sudah digunakan. Silakan pilih username lain.',
            'password.required'  => 'Password wajib diisi.',
            'password.min'       => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sama dengan password.',
        ];
    }

    /** Hapus spasi di awal/akhir username sebelum divalidasi */
    protected function prepareForValidation(): void
    {
        $this->merge(['username' => trim((string) $this->input('username'))]);
    }
}