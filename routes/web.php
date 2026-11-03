<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\SuratMasukController;
use App\Http\Controllers\Admin\SuratKeluarController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\PK\PKDashboardController;
use App\Http\Controllers\PK\KlienController;
use App\Http\Controllers\PK\AturJadwalController;
use App\Http\Controllers\PK\JadwalBimbinganController;
use App\Http\Controllers\PK\KehadiranKlienController;
use App\Http\Controllers\PK\PKProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes - SIMBA (Sistem Informasi Balai Pemasyarakatan)
| Bapas Kelas I Banjarmasin
|--------------------------------------------------------------------------
*/

// Root redirection
Route::get('/', function () {
    if (Auth::check()) {
        return Auth::user()->isAdmin() ? redirect()->route('admin.dashboard') : redirect()->route('pk.dashboard');
    }
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// -------------------------------------------------------------------------
// Panel Admin / Bagian Umum
// -------------------------------------------------------------------------
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Modul Surat Masuk
    Route::get('/surat-masuk', [SuratMasukController::class, 'index'])->name('surat-masuk.index');
    Route::post('/surat-masuk', [SuratMasukController::class, 'store'])->name('surat-masuk.store');
    Route::get('/surat-masuk/{id}', [SuratMasukController::class, 'show'])->name('surat-masuk.show');
    Route::put('/surat-masuk/{id}', [SuratMasukController::class, 'update'])->name('surat-masuk.update');
    Route::delete('/surat-masuk/{id}', [SuratMasukController::class, 'destroy'])->name('surat-masuk.destroy');
    Route::get('/surat-masuk/{id}/download', [SuratMasukController::class, 'download'])->name('surat-masuk.download');

    // Modul Surat Keluar
    Route::get('/surat-keluar', [SuratKeluarController::class, 'index'])->name('surat-keluar.index');
    Route::post('/surat-keluar', [SuratKeluarController::class, 'store'])->name('surat-keluar.store');
    Route::get('/surat-keluar/{id}', [SuratKeluarController::class, 'show'])->name('surat-keluar.show');
    Route::put('/surat-keluar/{id}', [SuratKeluarController::class, 'update'])->name('surat-keluar.update');
    Route::delete('/surat-keluar/{id}', [SuratKeluarController::class, 'destroy'])->name('surat-keluar.destroy');
    Route::get('/surat-keluar/{id}/download', [SuratKeluarController::class, 'download'])->name('surat-keluar.download');

    // Profil Admin (Tanpa fitur ganti password)
    Route::get('/profile', [AdminProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [AdminProfileController::class, 'update'])->name('profile.update');
});

// -------------------------------------------------------------------------
// Panel Pembimbing Kemasyarakatan (PK)
// -------------------------------------------------------------------------
Route::middleware(['auth', 'role:pk'])->prefix('pk')->name('pk.')->group(function () {
    Route::get('/dashboard', [PKDashboardController::class, 'index'])->name('dashboard');

    // Modul Data Klien (Tanpa jenis bimbingan)
    Route::get('/klien', [KlienController::class, 'index'])->name('klien.index');
    Route::post('/klien', [KlienController::class, 'store'])->name('klien.store');
    Route::get('/klien/{id}', [KlienController::class, 'show'])->name('klien.show');
    Route::put('/klien/{id}', [KlienController::class, 'update'])->name('klien.update');
    Route::delete('/klien/{id}', [KlienController::class, 'destroy'])->name('klien.destroy');

    // Modul Atur Jadwal (Hari dan Jam Mulai bimbingan, tanpa batasan jam selesai & kuota)
    Route::get('/atur-jadwal', [AturJadwalController::class, 'index'])->name('atur-jadwal.index');
    Route::post('/atur-jadwal', [AturJadwalController::class, 'store'])->name('atur-jadwal.store');
    Route::put('/atur-jadwal/{id}', [AturJadwalController::class, 'update'])->name('atur-jadwal.update');
    Route::delete('/atur-jadwal/{id}', [AturJadwalController::class, 'destroy'])->name('atur-jadwal.destroy');

    // Modul Jadwal Bimbingan
    Route::get('/jadwal', [JadwalBimbinganController::class, 'index'])->name('jadwal.index');
    Route::post('/jadwal', [JadwalBimbinganController::class, 'store'])->name('jadwal.store');
    Route::get('/jadwal/{id}', [JadwalBimbinganController::class, 'show'])->name('jadwal.show');
    Route::put('/jadwal/{id}', [JadwalBimbinganController::class, 'update'])->name('jadwal.update');
    Route::delete('/jadwal/{id}', [JadwalBimbinganController::class, 'destroy'])->name('jadwal.destroy');

    // Modul Pencatatan Bukti Kehadiran Klien
    Route::get('/kehadiran', [KehadiranKlienController::class, 'index'])->name('kehadiran.index');
    Route::post('/kehadiran', [KehadiranKlienController::class, 'store'])->name('kehadiran.store');
    Route::get('/kehadiran/{id}', [KehadiranKlienController::class, 'show'])->name('kehadiran.show');
    Route::put('/kehadiran/{id}', [KehadiranKlienController::class, 'update'])->name('kehadiran.update');
    Route::delete('/kehadiran/{id}', [KehadiranKlienController::class, 'destroy'])->name('kehadiran.destroy');

    // Profil PK (Tanpa fitur ganti password dan tanpa kolom kontak)
    Route::get('/profile', [PKProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [PKProfileController::class, 'update'])->name('profile.update');
});
