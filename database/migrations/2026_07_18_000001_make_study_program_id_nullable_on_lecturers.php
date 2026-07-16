<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Dekan tidak selalu punya prodi Fakultas Vokasi yang jelas di CIS (jabatannya
 * administratif, bukan dosen prodi tertentu). Supaya Dekan tetap bisa punya
 * profil Lecturer (dibutuhkan seluruh fitur dashboard), study_program_id harus
 * boleh null untuk kasus ini. Butuh doctrine/dbal untuk ->change().
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lecturers', function (Blueprint $table) {
            $table->foreignId('study_program_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('lecturers', function (Blueprint $table) {
            $table->foreignId('study_program_id')->nullable(false)->change();
        });
    }
};
