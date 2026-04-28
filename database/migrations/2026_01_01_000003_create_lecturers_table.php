<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lecturers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->unique() // 1-to-1 dengan users
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->foreignId('study_program_id')
                  ->constrained('study_programs')
                  ->onDelete('restrict');
            $table->string('nidn', 20)->nullable();
            $table->text('expertise')->nullable();
            $table->string('photo')->nullable();
            $table->boolean('is_public')->default(true)
                  ->comment('Visibilitas profil lapis 1 - tampil tanpa login');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lecturers');
    }
};
