<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tambah kolom bulan (1-12) untuk keperluan Laporan Tridharma dengan filter
 * periode (bulanan/3 bulan/tahunan). Sebelumnya cuma ada kolom year.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('researches', function (Blueprint $table) {
            $table->unsignedTinyInteger('month')->nullable()->after('year');
        });

        Schema::table('community_services', function (Blueprint $table) {
            $table->unsignedTinyInteger('month')->nullable()->after('year');
        });
    }

    public function down(): void
    {
        Schema::table('researches', function (Blueprint $table) {
            $table->dropColumn('month');
        });

        Schema::table('community_services', function (Blueprint $table) {
            $table->dropColumn('month');
        });
    }
};
