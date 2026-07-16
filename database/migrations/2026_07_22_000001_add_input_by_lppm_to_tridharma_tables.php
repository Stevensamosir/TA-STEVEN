<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menandai data Penelitian/PKM yang diinput LEWAT LPPM (atas nama dosen lain),
 * beda dari data yang diinput dosen sendiri. Dipakai untuk fitur "Riwayat
 * Pengisian Terakhir" di halaman LPPM.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('researches', function (Blueprint $table) {
            $table->foreignId('input_by_lppm_id')->nullable()->constrained('users')->nullOnDelete();
        });
        Schema::table('community_services', function (Blueprint $table) {
            $table->foreignId('input_by_lppm_id')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('researches', function (Blueprint $table) {
            $table->dropConstrainedForeignId('input_by_lppm_id');
        });
        Schema::table('community_services', function (Blueprint $table) {
            $table->dropConstrainedForeignId('input_by_lppm_id');
        });
    }
};
