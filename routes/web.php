<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

// Publik (aturan bisnis no. 1)
Route::view('/', 'pages.home')->name('home');

// Tamu belum login
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');

    Route::get('/register', [RegisterController::class, 'create'])->name('register');          
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');   
});

// Sudah login (semua role)
Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

// Wisatawan
Route::middleware(['auth', 'role:wisatawan'])->group(function () {
    // laporan.create, laporan.store, pengajuan.*, komentar.* ...
});

// Pengelola
Route::middleware(['auth', 'role:pengelola'])->prefix('pengelola')->name('pengelola.')->group(function () {
    Route::view('/dashboard', 'pengelola.dashboard')->name('dashboard');
});

// Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::view('/dashboard', 'admin.dashboard')->name('dashboard');
});