<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('community_services', function (Blueprint $table) {
            $table->enum('pkm_scheme', [
                'PKM-RE', 'PKM-RSH', 'PKM-K', 'PKM-PM', 'PKM-PI', 'PKM-KC', 'PKM-KI', 'PKM-VGK',
                'PKM-AI', 'PKM-GFT',
            ])->nullable()->after('pkm_type')
              ->comment('Skema resmi Belmawa/Kemdiktisaintek, opsional -- tidak semua PKM ikut kompetisi ini');
        });
    }

    public function down(): void
    {
        Schema::table('community_services', function (Blueprint $table) {
            $table->dropColumn('pkm_scheme');
        });
    }
};
