<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /** Pemakaian: ->middleware('role:admin') atau 'role:wisatawan,pengelola' */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $akun = $request->user();

        if (! $akun) {
            return redirect()->route('login');
        }

        if (! in_array($akun->role, $roles, true)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        // Batasan sistem no. 8: pengelola wajib terhubung ke satu destinasi
        if ($akun->isPengelola() && is_null($akun->id_destinasi)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['username' => 'Akun pengelola belum terhubung ke destinasi. Hubungi admin.']);
        }

        return $next($request);
    }
}