<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fitur "Visibilitas" per-item tridharma dihapus total dari aplikasi
     * (semua data tridharma sekarang bersifat publik). Kolom visibility di
     * seluruh tabel tridharma tidak dipakai lagi, jadi di-drop permanen.
     *
     * Catatan: is_public di tabel lecturers (visibilitas profil level dosen)
     * SENGAJA dipertahankan -- itu yang menjaga dosen nonaktif dari CIS tetap
     * tidak tampil publik.
     */
    private array $tables = [
        'educations',
        'researches',
        'community_services',
        'publications',
        'books',
        'hkis',
        'awards',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasColumn($table, 'visibility')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropColumn('visibility');
                });
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (!Schema::hasColumn($table, 'visibility')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->enum('visibility', ['public', 'private'])->default('public');
                });
            }
        }
    }
};
