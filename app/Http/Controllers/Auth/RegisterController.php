<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Akun;
use Illuminate\Support\Facades\Auth;  
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RegisterController extends Controller
{
    /** Tampilkan halaman registrasi (khusus wisatawan) */
    public function create(): View
    {
        return view('auth.register');
    }

    /** Simpan akun wisatawan baru (IPO: Registrasi Wisatawan) */
    public function store(RegisterRequest $request): RedirectResponse
    {
        $akun = Akun::create([
            'username' => $request->validated('username'),
            'password' => $request->validated('password'), // otomatis di-hash oleh cast 'hashed' di model Akun
            'role'     => Akun::ROLE_WISATAWAN,
        ]);

        Auth::login($akun);
        $request->session()->regenerate();

        return redirect()
            ->intended(route('home'))
            ->with('success', "Registrasi berhasil. Selamat datang, {$akun->username}!");
    }
}