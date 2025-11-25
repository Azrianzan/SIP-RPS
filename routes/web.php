<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProyekController;
use App\Http\Controllers\SekolahController;
use App\Http\Controllers\LaporanController;

// 1. Halaman Login (Guest Only)
Route::middleware(['guest'])->group(function () {
    Route::get('/', [AuthController::class, 'index'])->name('login'); 
    Route::get('/login', [AuthController::class, 'index'])->name('login.view');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Logout (Bisa diakses siapa saja yang login)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// 2. Group Route yang butuh Login (Auth Middleware)
Route::middleware(['auth'])->group(function () {
    
    // Halaman Dashboard (Untuk Pimpinan)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    

    // Halaman Kelola Pengguna (Untuk Admin)
    Route::middleware(['isAdmin'])->group(function () {
    Route::get('/kelola-pengguna', [PenggunaController::class, 'index'])->name('pengguna.index');
    Route::post('/kelola-pengguna', [PenggunaController::class, 'store'])->name('pengguna.store');
    Route::get('/kelola-pengguna/{id}', [PenggunaController::class, 'show'])->name('pengguna.show');
    Route::put('/kelola-pengguna/{id}', [PenggunaController::class, 'update'])->name('pengguna.update');
    Route::delete('/kelola-pengguna/{id}', [PenggunaController::class, 'destroy'])->name('pengguna.destroy');
    });

    // Halaman Kelola Proyek (Untuk PJL)
    //Route::get('/kelola-proyek', [ProyekController::class, 'index'])->name('proyek.index');
    Route::get('/kelola-proyek/{id}/data', [ProyekController::class, 'getData'])->name('proyek.data');
    Route::resource('kelola-proyek', ProyekController::class)
        ->parameters(['kelola-proyek' => 'proyek'])
        ->names('proyek');

    // Halaman Kelola Sekolah (Resource Controller)
    Route::resource('sekolah', SekolahController::class);

    // Route untuk menampilkan Form Laporan (membawa ID proyek)
    Route::get('/kelola-proyek/{proyek}/laporan/create', [LaporanController::class, 'create'])->name('laporan.create');
    
    // Route untuk menyimpan Laporan
    Route::post('/kelola-proyek/{proyek}/laporan', [LaporanController::class, 'store'])->name('laporan.store');

    // Route untuk Validasi Laporan (Terima/Tolak)
    Route::post('/laporan/{id}/validasi', [App\Http\Controllers\LaporanController::class, 'validasi'])->name('laporan.validasi');

    // Halaman Filter Ekspor
    Route::get('/ekspor-laporan', [LaporanController::class, 'indexEkspor'])->name('ekspor.index');
    // Action Print (PDF via Browser)
    Route::get('/ekspor-laporan/print', [LaporanController::class, 'printLaporan'])->name('ekspor.print');
    // Action Download CSV (Excel)
    Route::get('/ekspor-laporan/csv', [LaporanController::class, 'exportCsv'])->name('ekspor.csv');
});