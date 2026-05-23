<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lecturers', function (Blueprint $table) {
            $table->string('jabatan_fungsional', 100)
                  ->nullable()
                  ->after('nidn')
                  ->comment('Jabatan fungsional akademik dosen: Asisten Ahli, Lektor, Lektor Kepala, Guru Besar');
        });
    }

    public function down(): void
    {
        Schema::table('lecturers', function (Blueprint $table) {
            $table->dropColumn('jabatan_fungsional');
        });
    }
};
