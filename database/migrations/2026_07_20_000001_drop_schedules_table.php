<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fitur "Jadwal Dosen" / "Penjadwalan Dosen" dihapus total dari aplikasi,
     * jadi tabel schedules tidak dipakai lagi.
     */
    public function up(): void
    {
        Schema::dropIfExists('schedules');
    }

    /**
     * Sengaja dikosongkan. Struktur asli tabel schedules ada di migration
     * 2026_07_19_000001_create_books_hkis_awards_schedules_tables.php, namun
     * blok pembuatannya juga sudah dihapus bersamaan dengan penghapusan fitur.
     * Kalau suatu saat perlu dipulihkan, buat kembali skema tabelnya secara
     * eksplisit di sini atau lewat migration baru.
     */
    public function down(): void
    {
        // no-op
    }
};
