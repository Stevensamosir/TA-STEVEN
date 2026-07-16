<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * PENTING: migration ini memakai Schema::table(...)->change(), yang butuh
 * paket doctrine/dbal. Jalankan dulu:
 *   composer require doctrine/dbal
 * sebelum php artisan migrate.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Username CIS, dipakai untuk login (bukan email lagi)
            $table->string('username')->nullable()->unique()->after('email');
            // Password lokal jadi opsional: dosen/kaprodi/dekan login lewat CIS,
            // tidak ada password tersimpan untuk mereka.
            $table->string('password')->nullable()->change();
        });

        // Tambah role 'lppm'. Raw statement karena mengubah ENUM tidak
        // didukung penuh oleh Schema::change() untuk tipe enum di semua driver.
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('dekan','kaprodi','dosen','lppm') NOT NULL DEFAULT 'dosen'");
        }
        // Catatan: jika database dev memakai SQLite, ENUM disimpan sebagai TEXT
        // tanpa constraint, jadi tidak perlu ALTER terpisah untuk SQLite.

        Schema::table('lecturers', function (Blueprint $table) {
            // ID pegawai di CIS, dipakai untuk mencocokkan akun CIS <-> lecturer lokal
            $table->unsignedBigInteger('cis_pegawai_id')->nullable()->unique()->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('lecturers', function (Blueprint $table) {
            $table->dropColumn('cis_pegawai_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
            $table->string('password')->nullable(false)->change();
        });

        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('dekan','kaprodi','dosen') NOT NULL DEFAULT 'dosen'");
        }
    }
};
