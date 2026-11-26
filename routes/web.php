<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Wisatawan\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Wisatawan\LaporanController;
use App\Http\Controllers\Wisatawan\ProfilController;
use App\Http\Controllers\Admin\DestinasiController;

// Publik (aturan bisnis no. 1)
Route::get('/', HomeController::class)->middleware('guest')->name('home');

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
    Route::get('/beranda', [DashboardController::class, 'index'])
    ->name('wisatawan.beranda');

    Route::get('/profil', [ProfilController::class, 'show'])->name('wisatawan.profil');                              
    Route::put('/profil/password', [ProfilController::class, 'updatePassword'])->name('wisatawan.password.update');   

    // laporan.create, laporan.store, pengajuan.*, komentar.* ...
    // Halaman pelaporan kerusakan destinasi
    Route::get('/destinasi/{destinasi}/laporan/create', [LaporanController::class, 'create'])
        ->name('laporan.create');
    Route::post('/destinasi/{destinasi}/laporan', [LaporanController::class, 'store'])
        ->name('laporan.store');
    // Detail laporan milik wisatawan
    Route::get('/laporan/{laporan}/edit', [LaporanController::class, 'edit'])->name('laporan.edit');   // BARU
    Route::get('/laporan/{laporan:id_laporan}', [LaporanController::class, 'show'])
        ->name('laporan.show');
    Route::put('/laporan/{laporan:id_laporan}', [LaporanController::class, 'update'])
    ->name('laporan.update');
    // Hapus laporan milik wisatawan
    Route::delete('/laporan/{laporan:id_laporan}', [LaporanController::class, 'destroy'])
        ->name('laporan.destroy');
});

// Pengelola
Route::middleware(['auth', 'role:pengelola'])->prefix('pengelola')->name('pengelola.')->group(function () {
    Route::view('/dashboard', 'pengelola.dashboard')->name('dashboard');
});

// Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::view('/dashboard', 'admin.dashboard')->name('dashboard');

    Route::get('/destinasi', [DestinasiController::class, 'index'])->name('destinasi.index');
    Route::get('/destinasi/create', [DestinasiController::class, 'create'])->name('destinasi.create');
    Route::post('/destinasi', [DestinasiController::class, 'store'])->name('destinasi.store');
    Route::get('/destinasi/{id_destinasi}/edit', [DestinasiController::class, 'edit'])->name('destinasi.edit');
    Route::put('/destinasi/{id_destinasi}', [DestinasiController::class, 'update'])->name('destinasi.update');
    Route::get('/destinasi/{id_destinasi}/delete', [DestinasiController::class, 'delete'])->name('destinasi.delete');
    Route::delete('/destinasi/{id_destinasi}', [DestinasiController::class, 'destroy'])->name('destinasi.destroy');
});