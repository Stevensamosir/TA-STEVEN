<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DoiController;
use App\Http\Controllers\LPPMController;

// ─────────────────────────────────────────────────────────────────────────────
// PUBLIC — Tidak perlu login
// ─────────────────────────────────────────────────────────────────────────────
Route::get('/',           [PublicController::class, 'index'])->name('home');
Route::get('/dosen',      [PublicController::class, 'index'])->name('public.dosen');
Route::get('/dosen/{id}', [PublicController::class, 'show'])->name('public.dosen.show');

// ─────────────────────────────────────────────────────────────────────────────
// AUTH
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ─────────────────────────────────────────────────────────────────────────────
// DOSEN DASHBOARD — semua role yang login (dosen, kaprodi, dekan)
// Setiap user mengelola profil dan tridharma MILIKNYA SENDIRI
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:dosen,kaprodi,dekan'])->prefix('dashboard')->name('dosen.')->group(function () {
    Route::get('/',         [DosenController::class, 'index'])->name('index');
    Route::get('/profil',   [DosenController::class, 'editProfil'])->name('profil.edit');
    Route::put('/profil',   [DosenController::class, 'updateProfil'])->name('profil.update');

    // Pendidikan
    Route::get('/pendidikan',                   [DosenController::class, 'pendidikan'])->name('pendidikan');
    Route::post('/pendidikan',                  [DosenController::class, 'storePendidikan'])->name('pendidikan.store');
    Route::put('/pendidikan/{id}',              [DosenController::class, 'updatePendidikan'])->name('pendidikan.update');
    Route::delete('/pendidikan/{id}',           [DosenController::class, 'destroyPendidikan'])->name('pendidikan.destroy');
    Route::patch('/pendidikan/{id}/visibility', [DosenController::class, 'togglePendidikanVisibility'])->name('pendidikan.visibility');

    // Penelitian
    Route::get('/penelitian',                   [DosenController::class, 'penelitian'])->name('penelitian');
    Route::post('/penelitian',                  [DosenController::class, 'storePenelitian'])->name('penelitian.store');
    Route::put('/penelitian/{id}',              [DosenController::class, 'updatePenelitian'])->name('penelitian.update');
    Route::delete('/penelitian/{id}',           [DosenController::class, 'destroyPenelitian'])->name('penelitian.destroy');
    Route::patch('/penelitian/{id}/visibility', [DosenController::class, 'togglePenelitianVisibility'])->name('penelitian.visibility');

    // Pengabdian
    Route::get('/pengabdian',                   [DosenController::class, 'pengabdian'])->name('pengabdian');
    Route::post('/pengabdian',                  [DosenController::class, 'storePengabdian'])->name('pengabdian.store');
    Route::put('/pengabdian/{id}',              [DosenController::class, 'updatePengabdian'])->name('pengabdian.update');
    Route::delete('/pengabdian/{id}',           [DosenController::class, 'destroyPengabdian'])->name('pengabdian.destroy');
    Route::patch('/pengabdian/{id}/visibility', [DosenController::class, 'togglePengabdianVisibility'])->name('pengabdian.visibility');

    // Publikasi
    Route::get('/publikasi',                    [DosenController::class, 'publikasi'])->name('publikasi');
    Route::post('/publikasi',                   [DosenController::class, 'storePublikasi'])->name('publikasi.store');
    Route::put('/publikasi/{id}',               [DosenController::class, 'updatePublikasi'])->name('publikasi.update');
    Route::delete('/publikasi/{id}',            [DosenController::class, 'destroyPublikasi'])->name('publikasi.destroy');
    Route::patch('/publikasi/{id}/visibility',  [DosenController::class, 'togglePublikasiVisibility'])->name('publikasi.visibility');

    // Buku
    Route::get('/buku',                   [DosenController::class, 'buku'])->name('buku');
    Route::post('/buku',                  [DosenController::class, 'storeBuku'])->name('buku.store');
    Route::put('/buku/{id}',              [DosenController::class, 'updateBuku'])->name('buku.update');
    Route::delete('/buku/{id}',           [DosenController::class, 'destroyBuku'])->name('buku.destroy');
    Route::patch('/buku/{id}/visibility', [DosenController::class, 'toggleBukuVisibility'])->name('buku.visibility');

    // HKI
    Route::get('/hki',                   [DosenController::class, 'hki'])->name('hki');
    Route::post('/hki',                  [DosenController::class, 'storeHki'])->name('hki.store');
    Route::put('/hki/{id}',              [DosenController::class, 'updateHki'])->name('hki.update');
    Route::delete('/hki/{id}',           [DosenController::class, 'destroyHki'])->name('hki.destroy');
    Route::patch('/hki/{id}/visibility', [DosenController::class, 'toggleHkiVisibility'])->name('hki.visibility');

    // Penghargaan
    Route::get('/penghargaan',                   [DosenController::class, 'penghargaan'])->name('penghargaan');
    Route::post('/penghargaan',                  [DosenController::class, 'storePenghargaan'])->name('penghargaan.store');
    Route::put('/penghargaan/{id}',              [DosenController::class, 'updatePenghargaan'])->name('penghargaan.update');
    Route::delete('/penghargaan/{id}',           [DosenController::class, 'destroyPenghargaan'])->name('penghargaan.destroy');
    Route::patch('/penghargaan/{id}/visibility', [DosenController::class, 'togglePenghargaanVisibility'])->name('penghargaan.visibility');

    // Jadwal Dosen
    Route::get('/jadwal',        [DosenController::class, 'jadwal'])->name('jadwal');
    Route::post('/jadwal',       [DosenController::class, 'storeJadwal'])->name('jadwal.store');
    Route::put('/jadwal/{id}',   [DosenController::class, 'updateJadwal'])->name('jadwal.update');
    Route::delete('/jadwal/{id}', [DosenController::class, 'destroyJadwal'])->name('jadwal.destroy');
});

// ─────────────────────────────────────────────────────────────────────────────
// ADMIN — DEKAN ONLY
// Semua route di sini dilindungi route middleware role:dekan
// + assertDekanOnly() di dalam controller sebagai defense-in-depth
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:dekan'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
});

// ─────────────────────────────────────────────────────────────────────────────
// DATA INTERNAL & EDIT PROFIL DOSEN — Dekan + Kaprodi
// FIX (perbaikan menyeluruh): route /profil/{id} SEBELUMNYA ada di dalam grup
// role:dekan saja, sehingga Kaprodi diblokir middleware sebelum sempat dicek
// assertKaprodiCanEdit() di controller. Sempat dibuat route duplikat khusus
// Kaprodi (admin.kaprodi.profil.*), tapi karena pola URL-nya identik dengan
// punya Dekan, route itu tidak pernah benar-benar tercapai (Laravel selalu
// mencocokkan ke route pertama yang match, yaitu milik Dekan) — duplikat itu
// dihapus. Sekarang HANYA SATU route bernama admin.profil.edit/update yang
// terbuka untuk Dekan & Kaprodi; pembatasan rinci (Kaprodi hanya boleh edit
// dosen di prodinya sendiri) tetap dijaga oleh assertKaprodiCanEdit() di
// AdminController.
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:dekan,kaprodi'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/internal',           [AdminController::class, 'internal'])->name('internal');
    Route::get('/internal/{id}',      [AdminController::class, 'internalShow'])->name('internal.show');
    Route::get('/profil/{id}',        [AdminController::class, 'editProfilDosen'])->name('profil.edit');
    Route::put('/profil/{id}',        [AdminController::class, 'updateProfilDosen'])->name('profil.update');
    Route::get('/laporan-tridharma',             [AdminController::class, 'laporanTridharma'])->name('laporan-tridharma');
    Route::get('/laporan-tridharma/export/pdf',  [AdminController::class, 'exportLaporanTridharmaPdf'])->name('laporan-tridharma.export.pdf');
    Route::get('/penjadwalan',        [AdminController::class, 'penjadwalan'])->name('penjadwalan');
});

// ─────────────────────────────────────────────────────────────────────────────
// DOI LOOKUP — Public API (Crossref adalah public API, tidak perlu login)
// ─────────────────────────────────────────────────────────────────────────────
Route::get('/api/doi', [DoiController::class, 'fetch'])->name('api.doi');

// ─────────────────────────────────────────────────────────────────────────────
// LPPM — input Penelitian & PKM dosen
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:lppm'])->prefix('lppm')->name('lppm.')->group(function () {
    Route::get('/',              [LPPMController::class, 'index'])->name('index');
    Route::get('/search-dosen',  [LPPMController::class, 'searchDosen'])->name('search-dosen');
    Route::post('/penelitian',   [LPPMController::class, 'storePenelitian'])->name('penelitian.store');
    Route::post('/pkm',          [LPPMController::class, 'storePkm'])->name('pkm.store');
});