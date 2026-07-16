<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Field baru di community_services
        Schema::table('community_services', function (Blueprint $table) {
            $table->enum('pkm_type', ['Internal', 'Nasional', 'Internasional'])
                  ->nullable()->after('location');
            $table->string('student_members', 1000)->nullable()->after('pkm_type')
                  ->comment('Nama mahasiswa anggota, dipisah koma');
        });

        // Tabel pivot many-to-many
        Schema::create('lecturer_community_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lecturer_id')->constrained('lecturers')->onDelete('cascade');
            $table->foreignId('community_service_id')->constrained('community_services')->onDelete('cascade');
            $table->string('role')->default('Anggota');
            $table->timestamps();
            // Nama index eksplisit & pendek: nama auto-generate default
            // (lecturer_community_service_lecturer_id_community_service_id_unique)
            // melebihi batas 64 karakter identifier MySQL.
            $table->unique(['lecturer_id', 'community_service_id'], 'lcs_unique');
        });

        // Pindahkan data lama (lecturer_id langsung) ke pivot, sebagai peran "Ketua"
        $rows = DB::table('community_services')->whereNotNull('lecturer_id')->get(['id', 'lecturer_id']);
        foreach ($rows as $row) {
            DB::table('lecturer_community_service')->insertOrIgnore([
                'lecturer_id'          => $row->lecturer_id,
                'community_service_id' => $row->id,
                'role'                 => 'Ketua',
                'created_at'           => now(),
                'updated_at'           => now(),
            ]);
        }

        // Hapus kolom lecturer_id lama (sudah digantikan pivot)
        Schema::table('community_services', function (Blueprint $table) {
            $table->dropForeign(['lecturer_id']);
            $table->dropColumn('lecturer_id');
        });
    }

    public function down(): void
    {
        Schema::table('community_services', function (Blueprint $table) {
            $table->foreignId('lecturer_id')->nullable()->after('id')
                  ->constrained('lecturers')->cascadeOnDelete();
        });
        Schema::dropIfExists('lecturer_community_service');
        Schema::table('community_services', function (Blueprint $table) {
            $table->dropColumn(['pkm_type', 'student_members']);
        });
    }
};
