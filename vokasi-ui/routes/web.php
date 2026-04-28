<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\AdminController;

// ============================================================
// PUBLIC ROUTES (tanpa login)
// ============================================================
Route::get('/', [PublicController::class, 'index'])->name('home');
Route::get('/dosen', [PublicController::class, 'index'])->name('public.dosen');
Route::get('/dosen/{id}', [PublicController::class, 'show'])->name('public.dosen.show');
Route::get('/search', [PublicController::class, 'search'])->name('public.search');

// ============================================================
// AUTH ROUTES
// ============================================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ============================================================
// DOSEN ROUTES (role: dosen, kaprodi, dekan)
// ============================================================
Route::middleware(['auth', 'role:dosen,kaprodi,dekan'])->prefix('dashboard')->name('dosen.')->group(function () {
    Route::get('/', [DosenController::class, 'index'])->name('index');

    // Profil
    Route::get('/profil', [DosenController::class, 'editProfil'])->name('profil.edit');
    Route::put('/profil', [DosenController::class, 'updateProfil'])->name('profil.update');

    // Pendidikan
    Route::get('/pendidikan', [DosenController::class, 'pendidikan'])->name('pendidikan');
    Route::post('/pendidikan', [DosenController::class, 'storePendidikan'])->name('pendidikan.store');
    Route::put('/pendidikan/{id}', [DosenController::class, 'updatePendidikan'])->name('pendidikan.update');
    Route::delete('/pendidikan/{id}', [DosenController::class, 'destroyPendidikan'])->name('pendidikan.destroy');
    Route::patch('/pendidikan/{id}/visibility', [DosenController::class, 'togglePendidikanVisibility'])->name('pendidikan.visibility');

    // Penelitian
    Route::get('/penelitian', [DosenController::class, 'penelitian'])->name('penelitian');
    Route::post('/penelitian', [DosenController::class, 'storePenelitian'])->name('penelitian.store');
    Route::put('/penelitian/{id}', [DosenController::class, 'updatePenelitian'])->name('penelitian.update');
    Route::delete('/penelitian/{id}', [DosenController::class, 'destroyPenelitian'])->name('penelitian.destroy');
    Route::patch('/penelitian/{id}/visibility', [DosenController::class, 'togglePenelitianVisibility'])->name('penelitian.visibility');

    // Pengabdian
    Route::get('/pengabdian', [DosenController::class, 'pengabdian'])->name('pengabdian');
    Route::post('/pengabdian', [DosenController::class, 'storePengabdian'])->name('pengabdian.store');
    Route::put('/pengabdian/{id}', [DosenController::class, 'updatePengabdian'])->name('pengabdian.update');
    Route::delete('/pengabdian/{id}', [DosenController::class, 'destroyPengabdian'])->name('pengabdian.destroy');
    Route::patch('/pengabdian/{id}/visibility', [DosenController::class, 'togglePengabdianVisibility'])->name('pengabdian.visibility');

    // Publikasi
    Route::get('/publikasi', [DosenController::class, 'publikasi'])->name('publikasi');
    Route::post('/publikasi', [DosenController::class, 'storePublikasi'])->name('publikasi.store');
    Route::put('/publikasi/{id}', [DosenController::class, 'updatePublikasi'])->name('publikasi.update');
    Route::delete('/publikasi/{id}', [DosenController::class, 'destroyPublikasi'])->name('publikasi.destroy');
    Route::patch('/publikasi/{id}/visibility', [DosenController::class, 'togglePublikasiVisibility'])->name('publikasi.visibility');

    // Password
    Route::get('/password', [DosenController::class, 'editPassword'])->name('password');
    Route::put('/password', [DosenController::class, 'updatePassword'])->name('password.update');
});

// ============================================================
// ADMIN ROUTES (role: dekan, kaprodi)
// ============================================================
Route::middleware(['auth', 'role:dekan,kaprodi'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');

    // Kelola Akun Dosen
    Route::get('/dosen', [AdminController::class, 'dosenList'])->name('dosen');
    Route::get('/dosen/create', [AdminController::class, 'createDosen'])->name('dosen.create');
    Route::post('/dosen', [AdminController::class, 'storeDosen'])->name('dosen.store');
    Route::get('/dosen/{id}/edit', [AdminController::class, 'editDosen'])->name('dosen.edit');
    Route::put('/dosen/{id}', [AdminController::class, 'updateDosen'])->name('dosen.update');
    Route::patch('/dosen/{id}/reset-password', [AdminController::class, 'resetPassword'])->name('dosen.reset-password');
    Route::patch('/dosen/{id}/toggle-active', [AdminController::class, 'toggleActive'])->name('dosen.toggle-active');

    // Kelola Profil Dosen (admin edit profil dosen manapun)
    Route::get('/profil/{id}', [AdminController::class, 'editProfilDosen'])->name('profil.edit');
    Route::put('/profil/{id}', [AdminController::class, 'updateProfilDosen'])->name('profil.update');

    // Visibilitas Global
    Route::patch('/dosen/{id}/visibility', [AdminController::class, 'toggleVisibility'])->name('dosen.visibility');

    // Hierarki Kaprodi
    Route::get('/hierarki', [AdminController::class, 'hierarki'])->name('hierarki');
    Route::put('/hierarki/{id}', [AdminController::class, 'updateHierarki'])->name('hierarki.update');

    // Data Internal
    Route::get('/internal', [AdminController::class, 'internal'])->name('internal');
});
