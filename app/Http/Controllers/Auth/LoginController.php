<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Akun;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LoginController extends Controller
{
    /** Maksimal percobaan gagal sebelum dikunci sementara */
    private const MAKS_PERCOBAAN = 5;

    /** Tampilkan halaman login (satu halaman untuk wisatawan, pengelola, admin) */
    public function create(): View
    {
        return view('auth.login');
    }

    /** Proses login (aturan bisnis no. 3, IPO Login Wisatawan/Pengelola/Admin) */
    public function store(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('username', 'password');
        $kunci = Str::lower($request->input('username')).'|'.$request->ip();

        // Cegah tebak password berulang: 5 kali gagal → tunggu 60 detik
        if (RateLimiter::tooManyAttempts($kunci, self::MAKS_PERCOBAAN)) {
            $detik = RateLimiter::availableIn($kunci);

            return back()
                ->withInput($request->only('username'))
                ->withErrors(['login' => "Terlalu banyak percobaan masuk. Coba lagi dalam {$detik} detik."]);
        }

        if (! Auth::attempt($credentials)) {
            RateLimiter::hit($kunci, 60);

            return back()
                ->withInput($request->only('username'))
                ->withErrors(['login' => 'Username atau password salah.']);
        }

        RateLimiter::clear($kunci);

        /** @var Akun $akun */
        $akun = Auth::user();

        // Batasan sistem no. 8: pengelola wajib terhubung ke satu destinasi
        if ($akun->isPengelola() && is_null($akun->id_destinasi)) {
            $this->keluarkan($request);

            return back()
                ->withInput($request->only('username'))
                ->withErrors(['login' => 'Akun pengelola belum terhubung ke destinasi. Hubungi admin.']);
        }

        $request->session()->regenerate();

        return $this->arahkanSetelahLogin($request, $akun)
            ->with('success', "Selamat datang, {$akun->username}!");
    }

    /** Logout untuk semua role */
    public function destroy(Request $request): RedirectResponse
    {
        $this->keluarkan($request);

        return redirect()->route('home')->with('success', 'Anda telah keluar dari SwaraWisata.');
    }

    /**
     * Wisatawan dikembalikan ke halaman yang tadi ia buka (misal detail laporan).
     * Pengelola dan admin selalu ke dashboard masing-masing, kecuali halaman tujuannya
     * memang bagian dari area mereka sendiri.
     */
    private function arahkanSetelahLogin(Request $request, Akun $akun): RedirectResponse
    {
        if ($akun->isWisatawan()) {
            return redirect()->intended(route('home'));
        }

        $tujuan = $request->session()->pull('url.intended');
        $area = url($akun->isAdmin() ? '/admin' : '/pengelola');

        if ($tujuan && str_starts_with($tujuan, $area)) {
            return redirect()->to($tujuan);
        }

        return redirect()->to($akun->dashboardRoute());
    }

    private function keluarkan(Request $request): void
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}