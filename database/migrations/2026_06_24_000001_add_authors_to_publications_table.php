<?php
// Jalankan: php artisan make:migration add_authors_to_publications_table
// Lalu isi dengan konten ini, atau langsung copy file ini ke folder database/migrations/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('publications', function (Blueprint $table) {
            $table->string('authors', 1000)->nullable()
                  ->after('publisher_url')
                  ->comment('Nama penulis, dipisah koma. Contoh: Budi, Sari, Ahmad, ...');
        });
    }

    public function down(): void
    {
        Schema::table('publications', function (Blueprint $table) {
            $table->dropColumn('authors');
        });
    }
};
