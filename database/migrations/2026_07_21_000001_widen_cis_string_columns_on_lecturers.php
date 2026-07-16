<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Data test di CIS dev environment kadang berisi nilai sampah/acak yang jauh
 * lebih panjang dari NIP/NIDN asli (contoh nyata: nip 33 karakter, padahal
 * NIP asli biasanya 18 digit). Lebarkan kolom-kolom yang datanya berasal dari
 * CIS supaya sinkronisasi tidak gampang gagal gara-gara data uji yang kotor.
 * Butuh doctrine/dbal untuk ->change().
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lecturers', function (Blueprint $table) {
            $table->string('nidn', 100)->nullable()->change();
            $table->string('nip', 100)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('lecturers', function (Blueprint $table) {
            $table->string('nidn', 20)->nullable()->change();
            $table->string('nip', 30)->nullable()->change();
        });
    }
};
