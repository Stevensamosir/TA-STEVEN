<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kolom head_lecturer_id ditambahkan TERPISAH setelah tabel lecturers
     * dibuat untuk menghindari circular foreign key constraint.
     * 
     * Urutan:
     * 1. study_programs dibuat tanpa head_lecturer_id
     * 2. lecturers dibuat dengan FK ke study_programs
     * 3. Migration ini menambahkan head_lecturer_id ke study_programs
     */
    public function up(): void
    {
        Schema::table('study_programs', function (Blueprint $table) {
            $table->foreignId('head_lecturer_id')
                  ->nullable()
                  ->after('name')
                  ->constrained('lecturers')
                  ->onDelete('set null')
                  ->comment('Kaprodi prodi ini');
        });
    }

    public function down(): void
    {
        Schema::table('study_programs', function (Blueprint $table) {
            $table->dropForeign(['head_lecturer_id']);
            $table->dropColumn('head_lecturer_id');
        });
    }
};
