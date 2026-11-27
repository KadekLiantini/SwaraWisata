<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

/**
 * Ganti password untuk semua role (dipakai dialog <x-dialog.ganti-password>).
 */
class PasswordController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'password_lama' => ['required', 'current_password'],
            'password_baru' => ['required', 'confirmed', 'different:password_lama', Password::min(8)],
        ], [
            'password_lama.required'         => 'Password lama wajib diisi.',
            'password_lama.current_password' => 'Password lama tidak sesuai.',
            'password_baru.required'         => 'Password baru wajib diisi.',
            'password_baru.confirmed'        => 'Konfirmasi password baru tidak sama.',
            'password_baru.different'        => 'Password baru harus berbeda dari password lama.',
            'password_baru.min'              => 'Password baru minimal 8 karakter.',
        ]);

        $request->user()->update(['password' => $data['password_baru']]); // di-hash otomatis oleh cast model Akun

        return back()->with('success', 'Password berhasil diganti.');
    }
}