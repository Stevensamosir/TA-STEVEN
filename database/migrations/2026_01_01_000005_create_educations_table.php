<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('educations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lecturer_id')
                  ->constrained('lecturers')
                  ->onDelete('cascade');
            $table->string('degree', 10)
                  ->comment('S1, S2, S3, D3, D4');
            $table->string('institution');
            $table->string('major')->nullable()
                  ->comment('Program studi/jurusan');
            $table->year('year')
                  ->comment('Tahun lulus');
            $table->enum('visibility', ['public', 'private'])
                  ->default('public')
                  ->comment('Visibilitas per item lapis 2');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('educations');
    }
};
