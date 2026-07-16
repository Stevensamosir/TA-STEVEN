<?php

return [

    // Base URL API CIS. Isi CIS_BASE_URL di .env, JANGAN hardcode di sini.
    'base_url' => env('CIS_BASE_URL', 'https://cis.del.ac.id/api'),

    /*
    |--------------------------------------------------------------------------
    | Pemetaan prodi_id CIS -> nama program studi lokal SIPD
    |--------------------------------------------------------------------------
    | PERLU DIKONFIRMASI KE PAK OPPIR:
    | Daftar ini dipakai untuk memfilter "hanya dosen Fakultas Vokasi yang
    | boleh login ke SIPD" (khusus role Dosen & Kaprodi). prodi_id 2
    | "DIII Manajemen Informatika" SENGAJA TIDAK dimasukkan karena tidak ada
    | di DatabaseSeeder.php dan tidak ada Kaprodi aktif untuk prodi ini.
    */
    'vokasi_prodi_map' => [
        '1' => 'D3 Teknologi Informasi',
        '3' => 'D3 Teknik Komputer',
        '4' => 'D4 Teknologi Rekayasa Perangkat Lunak',
    ],

    /*
    |--------------------------------------------------------------------------
    | Pola teks untuk deteksi role Kaprodi dari array "jabatan" pada respons
    | login CIS. Dikonfirmasi dari data nyata (akun ardiles.sinaga): teks
    | Kaprodi selalu mengandung "Ketua Program Studi".
    */
    'kaprodi_jabatan_pattern' => 'ketua program studi',

    /*
    |--------------------------------------------------------------------------
    | Pola teks untuk deteksi role LPPM dari array "jabatan". Dikonfirmasi
    | dari data nyata (akun rosni): teks persis "Ketua Lembaga Penelitian
    | dan Pengabdian kepada Masyarakat (LPPM)".
    */
    'lppm_jabatan_pattern' => ['ketua lembaga penelitian dan pengabdian', 'lppm'],

    /*
    |--------------------------------------------------------------------------
    | Whitelist pegawai_id Dekan
    |--------------------------------------------------------------------------
    | Dekan TIDAK BISA dideteksi otomatis dari teks jabatan CIS (dikonfirmasi
    | dari testing: akun test riyanthi jabatannya "Kepala Divisi Pusat
    | Inovasi dan Usaha", bukan "Dekan"). Hanya ada 1 Dekan Fakultas Vokasi,
    | didaftarkan manual di sini pakai pegawai_id CIS-nya.
    */
    'dekan_pegawai_ids' => array_filter(explode(',', env('CIS_DEKAN_PEGAWAI_IDS', ''))),

    // Sementara dimatikan (lihat FIX_MATIKAN_AUTO_DEACTIVATE.md) -- data status aktif
    // di CIS dev tidak reliable, sempat salah nonaktifkan dosen yang beneran aktif
    // (Oppir, Dekan). Set CIS_AUTO_DEACTIVATE=true di .env kalau sudah dikonfirmasi
    // data produksi CIS reliable.
    'auto_deactivate_enabled' => env('CIS_AUTO_DEACTIVATE', false),

];
