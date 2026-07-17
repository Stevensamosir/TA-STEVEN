<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lecturer_id')->constrained('lecturers')->onDelete('cascade');
            $table->string('title');
            $table->year('year');
            $table->string('publisher')->nullable();
            $table->string('isbn', 30)->nullable();
            $table->enum('visibility', ['public', 'private'])->default('public');
            $table->timestamps();
        });

        Schema::create('hkis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lecturer_id')->constrained('lecturers')->onDelete('cascade');
            $table->string('title');
            $table->year('year');
            $table->string('type')->nullable()->comment('Jenis HKI: Paten, Hak Cipta, Merek, dll (bebas)');
            $table->string('certificate_number', 100)->nullable();
            $table->enum('visibility', ['public', 'private'])->default('public');
            $table->timestamps();
        });

        Schema::create('awards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lecturer_id')->constrained('lecturers')->onDelete('cascade');
            $table->string('name')->comment('nama_penghargaan');
            $table->enum('level', ['Internasional', 'Nasional', 'Lokal'])->comment('tingkat');
            $table->string('organizer')->nullable()->comment('penyelenggara');
            $table->string('rank')->nullable()->comment('peringkat, mis. Juara 1');
            $table->date('date')->comment('tanggal');
            $table->string('evidence_url', 500)->nullable()->comment('bukti - tautan sertifikat/dokumentasi');
            $table->enum('visibility', ['public', 'private'])->default('public');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('awards');
        Schema::dropIfExists('hkis');
        Schema::dropIfExists('books');
    }
};
