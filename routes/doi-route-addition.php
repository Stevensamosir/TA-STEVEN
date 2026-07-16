<?php
// Tambahkan baris ini ke routes/web.php
// Di bagian PALING BAWAH, sebelum closing

use App\Http\Controllers\DoiController;

// DOI Lookup API (tidak perlu login - Crossref adalah public API)
Route::get('/api/doi', [DoiController::class, 'fetch'])->name('api.doi');
