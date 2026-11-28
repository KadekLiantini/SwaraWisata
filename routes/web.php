<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Wisatawan\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Wisatawan\LaporanController;
use App\Http\Controllers\Wisatawan\ProfilController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DestinasiController;
use App\Http\Controllers\Admin\KategoriPelaporanController;
use App\Http\Controllers\Admin\PengajuanController;
use App\Http\Controllers\Pengelola\PengelolaLaporanController;
use App\Http\Controllers\Wisatawan\PengajuanDestinasiController;
use App\Http\Controllers\PasswordController;                                      
use App\Http\Controllers\Pengelola\ProfilController as PengelolaProfilController;  
use App\Http\Controllers\Pengelola\InfoController;

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
    // Pengajuan destinasi wisata baru
    Route::get('/pengajuan-destinasi', [PengajuanDestinasiController::class, 'create'])
        ->name('pengajuan.create');

    Route::get('/pengajuan-destinasi/kecamatan/{idKabupaten}', [PengajuanDestinasiController::class, 'getKecamatan'])
        ->name('pengajuan.kecamatan');

    Route::post('/pengajuan-destinasi', [PengajuanDestinasiController::class, 'store'])
        ->name('pengajuan.store');    
    // Komentar
    Route::post('/laporan/{laporan:id_laporan}/riwayat/{riwayat:id_riwayat}/komentar', [LaporanController::class, 'storeKomentar',])->name('laporan.komentar.store')
        ->withoutScopedBindings();
});

// Pengelola
Route::middleware(['auth', 'role:pengelola'])
->prefix('pengelola')
->name('pengelola.')
->group(function () {
    Route::get('/dashboard', [
        \App\Http\Controllers\Pengelola\PengelolaDashboardController::class,
        'index'
    ])->name('dashboard');

    //Profil dan update password
    Route::get('/profil', [PengelolaProfilController::class, 'show'])->name('profil');   
    Route::put('/profil/password', PasswordController::class)->name('password.update'); 

    //halaman info
    Route::get('/informasi', InfoController::class)->name('info');

    // Daftar semua laporan
    Route::get('/laporan', [
        PengelolaLaporanController::class,
        'index'
    ])->name('laporan.index');

    // Detail laporan
    Route::get('/laporan/{laporan}', [
        PengelolaLaporanController::class,
        'show'
    ])->name('laporan.show');

    Route::post('/laporan/{laporan}/riwayat', [
        PengelolaLaporanController::class,
        'storeRiwayat'
    ])->name('laporan.riwayat.store');

    Route::put('/laporan/{laporan}/riwayat/{riwayat}', [
        PengelolaLaporanController::class,
        'updateRiwayat'
    ])->name('laporan.riwayat.update');

    Route::delete('/laporan/{laporan}/riwayat/{riwayat}', [
        PengelolaLaporanController::class,
        'destroyRiwayat'
    ])->name('laporan.riwayat.destroy');
});

// Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Navigasi admin. Halaman pengelola.terbitkan masih placeholder sampai fiturnya tersedia.
    Route::get('/pengajuan', [PengajuanController::class, 'index'])->name('pengajuan.index');
    Route::get('/pengajuan/{pengajuan}', [PengajuanController::class, 'show'])->name('pengajuan.show');
    Route::post('/pengajuan/{pengajuan}/setujui', [PengajuanController::class, 'setujui'])->name('pengajuan.setujui');
    Route::post('/pengajuan/{pengajuan}/tolak', [PengajuanController::class, 'tolak'])->name('pengajuan.tolak');
    Route::get('/pengelola/terbitkan', fn () => redirect()->route('admin.dashboard'))->name('pengelola.terbitkan');

    Route::get('/destinasi', [DestinasiController::class, 'index'])->name('destinasi.index');
    Route::get('/destinasi/create', [DestinasiController::class, 'create'])->name('destinasi.create');
    Route::post('/destinasi', [DestinasiController::class, 'store'])->name('destinasi.store');
    Route::get('/destinasi/{id_destinasi}/edit', [DestinasiController::class, 'edit'])->name('destinasi.edit');
    Route::put('/destinasi/{id_destinasi}', [DestinasiController::class, 'update'])->name('destinasi.update');
    Route::patch('/destinasi/{id_destinasi}/status', [DestinasiController::class, 'updateStatus'])->name('destinasi.status');

    // Kategori Pelaporan
    Route::get('/kategori-pelaporan', [KategoriPelaporanController::class, 'index'])
        ->name('kategori.index');

    Route::get('/kategori-pelaporan/create', [KategoriPelaporanController::class, 'create'])
        ->name('kategori.create');

    Route::post('/kategori-pelaporan', [KategoriPelaporanController::class, 'store'])
        ->name('kategori.store');

    Route::get('/kategori-pelaporan/{id_kategori_kerusakan}/edit', [KategoriPelaporanController::class, 'edit'])
        ->name('kategori.edit');

    Route::put('/kategori-pelaporan/{id_kategori_kerusakan}', [KategoriPelaporanController::class, 'update'])
        ->name('kategori.update');

    Route::patch('/kategori-pelaporan/{id_kategori_kerusakan}/status', [KategoriPelaporanController::class, 'updateStatus'])
        ->name('kategori.status');
});
