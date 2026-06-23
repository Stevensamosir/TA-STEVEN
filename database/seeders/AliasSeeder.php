<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder untuk mengisi kolom alias pada tabel lecturers.
 * Alias = inisial dosen dari Daftar Nama Dosen Aktif Fakultas Vokasi.
 * Jalankan: php artisan db:seed --class=AliasSeeder
 */
class AliasSeeder extends Seeder
{
    public function run(): void
    {
        // Format: 'nama lengkap (sebagian, case-insensitive)' => 'ALIAS'
        $aliases = [
            'Goklas Henry'          => 'GHP',
            'Hernawati Susanti'     => 'HER',
            'Monalisa Pasaribu'     => 'MPR',
            'Rudy Chandra'          => 'RCH',
            'Tegar Arifin'          => 'TAP',
            'Bella Wahmilyana'      => 'BWA',
            'Tiurma Lumban'         => 'TLG',
            'Eka Stephani'          => 'ESS',
            'Frengki Simatupang'    => 'FST',
            'Gerry Italiano'        => 'GIW',
            'Istas Pratomo'         => 'IPM',
            'Marojahan'             => 'MMS',
            'Pandapotan Siagian'    => 'PDS',
            'Sari Muthia'           => 'SML',
            'Febrian Winston'       => 'FWH',
            'Ana Muliyana'          => 'AMP',
            'Ardiles Sinaga'        => 'ADS',
            'Arnaldo Marulitua'     => 'AMS',
            'Oppir Hutapea'         => 'OPH',
            'Riyanthi Angrainy'     => 'RIS',
            'Rumondang Miranda'     => 'RMM',
            'Cynthia Deborah'       => 'CDN',
        ];

        foreach ($aliases as $namaPart => $alias) {
            // Cari user dengan nama mengandung bagian tersebut
            $userId = DB::table('users')
                ->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($namaPart) . '%'])
                ->value('id');

            if ($userId) {
                DB::table('lecturers')
                    ->where('user_id', $userId)
                    ->update(['alias' => $alias]);
                $this->command->info("✅ {$namaPart} → {$alias}");
            } else {
                $this->command->warn("⚠️  Tidak ditemukan: {$namaPart}");
            }
        }
    }
}
