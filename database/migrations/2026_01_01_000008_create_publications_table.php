<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lecturer_id')
                  ->constrained('lecturers')
                  ->onDelete('cascade');
            $table->string('title');
            $table->year('year');
            $table->string('publisher')->nullable()
                  ->comment('Nama jurnal/prosiding/penerbit');
            $table->string('publisher_url', 500)->nullable()
                  ->comment('Tautan asli ke sumber publikasi - kebutuhan Pak Febrian');
            $table->enum('visibility', ['public', 'private'])
                  ->default('public');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publications');
    }
};
