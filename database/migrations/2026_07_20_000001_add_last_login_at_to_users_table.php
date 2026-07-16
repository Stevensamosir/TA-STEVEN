<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pembeda antara akun yang dibuat lewat sinkronisasi massal (belum pernah
 * login beneran) vs akun yang penggunanya sudah pernah login sendiri lewat
 * CIS. NULL berarti belum pernah login.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_login_at')->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('last_login_at');
        });
    }
};
