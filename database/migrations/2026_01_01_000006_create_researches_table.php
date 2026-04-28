<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('researches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lecturer_id')
                  ->constrained('lecturers')
                  ->onDelete('cascade');
            $table->string('title');
            $table->year('year');
            $table->string('funding_source')->nullable()
                  ->comment('Sumber dana penelitian');
            $table->enum('visibility', ['public', 'private'])
                  ->default('public');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('researches');
    }
};
