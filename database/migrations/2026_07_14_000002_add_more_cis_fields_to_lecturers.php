<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Melengkapi field dari respons CIS yang sebelumnya belum disimpan
 * (nip, jenjang_pendidikan).
 *
 * CATATAN: kolom `alias` TIDAK ditambahkan di sini karena sudah ada lebih
 * dulu lewat migration 2026_05_30_000001_add_alias_to_lecturers_table.php.
 * Menambahkannya lagi akan menyebabkan error "duplicate column".
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lecturers', function (Blueprint $table) {
            $table->string('nip', 30)->nullable()->after('cis_pegawai_id');
            $table->string('jenjang_pendidikan')->nullable()->after('jabatan_fungsional');
        });
    }

    public function down(): void
    {
        Schema::table('lecturers', function (Blueprint $table) {
            $table->dropColumn(['nip', 'jenjang_pendidikan']);
        });
    }
};
