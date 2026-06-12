<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DoiController;

// PUBLIC
Route::get('/',           [PublicController::class, 'index'])->name('home');
Route::get('/dosen',      [PublicController::class, 'index'])->name('public.dosen');
Route::get('/dosen/{id}', [PublicController::class, 'show'])->name('public.dosen.show');

// AUTH
Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ─────────────────────────────────────────────────────────────────────────────
// DOSEN DASHBOARD — semua role (dosen, kaprodi, dekan) edit profil sendiri
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:dosen,kaprodi,dekan'])->prefix('dashboard')->name('dosen.')->group(function () {
    Route::get('/',         [DosenController::class, 'index'])->name('index');
    Route::get('/profil',   [DosenController::class, 'editProfil'])->name('profil.edit');
    Route::put('/profil',   [DosenController::class, 'updateProfil'])->name('profil.update');

    Route::get('/pendidikan',                   [DosenController::class, 'pendidikan'])->name('pendidikan');
    Route::post('/pendidikan',                  [DosenController::class, 'storePendidikan'])->name('pendidikan.store');
    Route::put('/pendidikan/{id}',              [DosenController::class, 'updatePendidikan'])->name('pendidikan.update');
    Route::delete('/pendidikan/{id}',           [DosenController::class, 'destroyPendidikan'])->name('pendidikan.destroy');
    Route::patch('/pendidikan/{id}/visibility', [DosenController::class, 'togglePendidikanVisibility'])->name('pendidikan.visibility');

    Route::get('/penelitian',                   [DosenController::class, 'penelitian'])->name('penelitian');
    Route::post('/penelitian',                  [DosenController::class, 'storePenelitian'])->name('penelitian.store');
    Route::put('/penelitian/{id}',              [DosenController::class, 'updatePenelitian'])->name('penelitian.update');
    Route::delete('/penelitian/{id}',           [DosenController::class, 'destroyPenelitian'])->name('penelitian.destroy');
    Route::patch('/penelitian/{id}/visibility', [DosenController::class, 'togglePenelitianVisibility'])->name('penelitian.visibility');

    Route::get('/pengabdian',                   [DosenController::class, 'pengabdian'])->name('pengabdian');
    Route::post('/pengabdian',                  [DosenController::class, 'storePengabdian'])->name('pengabdian.store');
    Route::put('/pengabdian/{id}',              [DosenController::class, 'updatePengabdian'])->name('pengabdian.update');
    Route::delete('/pengabdian/{id}',           [DosenController::class, 'destroyPengabdian'])->name('pengabdian.destroy');
    Route::patch('/pengabdian/{id}/visibility', [DosenController::class, 'togglePengabdianVisibility'])->name('pengabdian.visibility');

    Route::get('/publikasi',                    [DosenController::class, 'publikasi'])->name('publikasi');
    Route::post('/publikasi',                   [DosenController::class, 'storePublikasi'])->name('publikasi.store');
    Route::put('/publikasi/{id}',               [DosenController::class, 'updatePublikasi'])->name('publikasi.update');
    Route::delete('/publikasi/{id}',            [DosenController::class, 'destroyPublikasi'])->name('publikasi.destroy');
    Route::patch('/publikasi/{id}/visibility',  [DosenController::class, 'togglePublikasiVisibility'])->name('publikasi.visibility');

    Route::get('/password', [DosenController::class, 'editPassword'])->name('password');
    Route::put('/password', [DosenController::class, 'updatePassword'])->name('password.update');

    // Transfer jabatan Dekan (hanya Dekan yang bisa, dari halaman profil sendiri)
    Route::post('/transfer-dekan', [DosenController::class, 'transferDekan'])->name('transfer-dekan');
});

// ─────────────────────────────────────────────────────────────────────────────
// ADMIN — DEKAN ONLY (akses penuh ke semua fitur manajemen)
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:dekan'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/',                              [AdminController::class, 'index'])->name('index');

    // Kelola Akun Dosen
    Route::get('/dosen',                         [AdminController::class, 'dosenList'])->name('dosen');
    Route::get('/dosen/create',                  [AdminController::class, 'createDosen'])->name('dosen.create');
    Route::post('/dosen',                        [AdminController::class, 'storeDosen'])->name('dosen.store');
    Route::get('/dosen/{id}/edit',               [AdminController::class, 'editDosen'])->name('dosen.edit');
    Route::put('/dosen/{id}',                    [AdminController::class, 'updateDosen'])->name('dosen.update');
    Route::patch('/dosen/{id}/reset-password',   [AdminController::class, 'resetPassword'])->name('dosen.reset-password');
    Route::patch('/dosen/{id}/toggle-active',    [AdminController::class, 'toggleActive'])->name('dosen.toggle-active');
    Route::patch('/dosen/{id}/visibility',       [AdminController::class, 'toggleVisibility'])->name('dosen.visibility');

    // Edit Profil Dosen (oleh Dekan)
    Route::get('/profil/{id}',                   [AdminController::class, 'editProfilDosen'])->name('profil.edit');
    Route::put('/profil/{id}',                   [AdminController::class, 'updateProfilDosen'])->name('profil.update');

    // Hierarki & Prodi
    Route::get('/hierarki',                      [AdminController::class, 'hierarki'])->name('hierarki');
    Route::put('/hierarki/{id}',                 [AdminController::class, 'updateHierarki'])->name('hierarki.update');
    Route::get('/prodi',                         [AdminController::class, 'prodiList'])->name('prodi');
    Route::post('/prodi',                        [AdminController::class, 'storeProdi'])->name('prodi.store');
    Route::put('/prodi/{id}',                    [AdminController::class, 'updateProdi'])->name('prodi.update');
    Route::delete('/prodi/{id}',                 [AdminController::class, 'destroyProdi'])->name('prodi.destroy');

});

// ─────────────────────────────────────────────────────────────────────────────
// MONITORING — KAPRODI ONLY
// Kaprodi: edit profil dosen di prodinya sendiri
// (route /internal sudah ada di group Dekan di atas — tidak perlu duplikat)
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:kaprodi'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/profil/{id}',   [AdminController::class, 'editProfilDosen'])->name('profil.edit');
    Route::put('/profil/{id}',   [AdminController::class, 'updateProfilDosen'])->name('profil.update');
});

// Data Internal: Dekan + Kaprodi sama-sama boleh akses
// Route terpisah agar tidak ada naming conflict
Route::middleware(['auth', 'role:dekan,kaprodi'])->prefix('admin')->group(function () {
    Route::get('/internal', [AdminController::class, 'internal'])->name('admin.internal');
});


// ─────────────────────────────────────────────────────────────────────────────
// DOI LOOKUP — Public API (tidak perlu login, Crossref adalah public API)
// ─────────────────────────────────────────────────────────────────────────────
Route::get('/api/doi', [DoiController::class, 'fetch'])->name('api.doi');
