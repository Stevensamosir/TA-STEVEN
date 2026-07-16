<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\StudyProgram;
use App\Models\Lecturer;
use App\Models\Education;
use App\Models\Research;
use App\Models\CommunityService;
use App\Models\Publication;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ============================================================
        // 1. PROGRAM STUDI — 3 prodi aktual Fakultas Vokasi IT Del
        // ============================================================
        $trpl = StudyProgram::create(['name' => 'D4 Teknologi Rekayasa Perangkat Lunak']);
        $ti   = StudyProgram::create(['name' => 'D3 Teknologi Informasi']);
        $tk   = StudyProgram::create(['name' => 'D3 Teknik Komputer']);

        // ============================================================
        // 2. USERS — Dekan, 3 Kaprodi, dan beberapa dosen representatif
        // Menggunakan nama dan email aktual Fakultas Vokasi IT Del
        // ============================================================

        // --- Dekan ---
        $userDekan = User::create([
            'name'      => 'Riyanthi Angrainy Sianturi, S.Sos., M.Ds.',
            'email'     => 'riyanthi.sianturi@del.ac.id',
            'password'  => Hash::make('password123'),
            'role'      => 'dekan',
            'is_active' => true,
        ]);

        // --- Kaprodi D4 TRPL ---
        $userKaprodiTRPL = User::create([
            'name'      => 'Ardiles Sinaga, S.T., M.T.',
            'email'     => 'ardiles.sinaga@del.ac.id',
            'password'  => Hash::make('password123'),
            'role'      => 'kaprodi',
            'is_active' => true,
        ]);

        // --- Kaprodi D3 TI ---
        $userKaprodiTI = User::create([
            'name'      => 'Goklas Henry Agus Panjaitan, S.Tr.Kom., M.T.',
            'email'     => 'goklas.panjaitan@del.ac.id',
            'password'  => Hash::make('password123'),
            'role'      => 'kaprodi',
            'is_active' => true,
        ]);

        // --- Dosen D3 TI ---
        $userTegar = User::create([
            'name'      => 'Tegar Arifin Prasetyo, S.Si., M.Si.',
            'email'     => 'tegar.prasetyo@del.ac.id',
            'password'  => Hash::make('password123'),
            'role'      => 'dosen',
            'is_active' => true,
        ]);

        $userRudy = User::create([
            'name'      => 'Rudy Chandra, S.Kom., M.Kom.',
            'email'     => 'rudy.chandra@del.ac.id',
            'password'  => Hash::make('password123'),
            'role'      => 'dosen',
            'is_active' => true,
        ]);

        // --- Dosen D4 TRPL ---
        $userAna = User::create([
            'name'      => 'Ana Muliyana, M.Pd.',
            'email'     => 'ana.muliyana@del.ac.id',
            'password'  => Hash::make('password123'),
            'role'      => 'dosen',
            'is_active' => true,
        ]);

        $userOppir = User::create([
            'name'      => 'Oppir Hutapea, S.Tr.Kom., M.Kom.',
            'email'     => 'oppir.hutapea@del.ac.id',
            'password'  => Hash::make('password123'),
            'role'      => 'dosen',
            'is_active' => true,
        ]);

        $userArnaldo = User::create([
            'name'      => 'Prof. Dr. Arnaldo Marulitua Sinaga, S.T., M.InfoTech.',
            'email'     => 'arnaldo.sinaga@del.ac.id',
            'password'  => Hash::make('password123'),
            'role'      => 'dosen',
            'is_active' => true,
        ]);

        // --- Dosen D3 TK ---
        $userFebrian = User::create([
            'name'      => 'Febrian Winston Hutagalung, S.T., M.T.',
            'email'     => 'febrian.hutagalung@del.ac.id',
            'password'  => Hash::make('password123'),
            'role'      => 'dosen',
            'is_active' => true,
        ]);

        $userEka = User::create([
            'name'      => 'Eka Stephani Sinambela, S.ST., M.Sc.',
            'email'     => 'eka.sinambela@del.ac.id',
            'password'  => Hash::make('password123'),
            'role'      => 'dosen',
            'is_active' => true,
        ]);

        $userCynthia = User::create([
            'name'      => 'Cynthia Deborah Nababan, S.Tr.Kom., M.Kom.',
            'email'     => 'cynthia.nababan@del.ac.id',
            'password'  => Hash::make('password123'),
            'role'      => 'dosen',
            'is_active' => true,
        ]);

        // ============================================================
        // 3. LECTURERS — profil dosen
        // Dekan (Bu Riyanthi) → TRPL (karena beliau dari DIVTRPL)
        // ============================================================
        $lecDekan = Lecturer::create([
            'user_id'            => $userDekan->id,
            'study_program_id'   => $trpl->id,
            'nidn'               => '0101019001',
            'jabatan_fungsional' => 'Lektor',
            'expertise'          => 'Desain Komunikasi Visual, Human-Computer Interaction',
            'is_public'          => true,
        ]);

        $lecKaprodiTRPL = Lecturer::create([
            'user_id'            => $userKaprodiTRPL->id,
            'study_program_id'   => $trpl->id,
            'nidn'               => '0201019002',
            'jabatan_fungsional' => 'Lektor',
            'expertise'          => 'Rekayasa Perangkat Lunak, Sistem Informasi',
            'is_public'          => true,
        ]);

        $lecKaprodiTI = Lecturer::create([
            'user_id'            => $userKaprodiTI->id,
            'study_program_id'   => $ti->id,
            'nidn'               => '0301019003',
            'jabatan_fungsional' => 'Lektor',
            'expertise'          => 'Jaringan Komputer, Keamanan Sistem',
            'is_public'          => true,
        ]);

        $lecTegar = Lecturer::create([
            'user_id'            => $userTegar->id,
            'study_program_id'   => $ti->id,
            'nidn'               => '0401019004',
            'jabatan_fungsional' => 'Asisten Ahli',
            'expertise'          => 'Basis Data, Statistika Komputasi',
            'is_public'          => true,
        ]);

        $lecRudy = Lecturer::create([
            'user_id'            => $userRudy->id,
            'study_program_id'   => $ti->id,
            'nidn'               => '0501019005',
            'jabatan_fungsional' => 'Asisten Ahli',
            'expertise'          => 'Pemrograman Web, Mobile Development',
            'is_public'          => true,
        ]);

        $lecAna = Lecturer::create([
            'user_id'            => $userAna->id,
            'study_program_id'   => $trpl->id,
            'nidn'               => '0601019006',
            'jabatan_fungsional' => 'Asisten Ahli',
            'expertise'          => 'Pendidikan Teknologi, Pengembangan Kurikulum',
            'is_public'          => true,
        ]);

        $lecOppir = Lecturer::create([
            'user_id'            => $userOppir->id,
            'study_program_id'   => $trpl->id,
            'nidn'               => '0701019007',
            'jabatan_fungsional' => 'Asisten Ahli',
            'expertise'          => 'Rekayasa Perangkat Lunak, Web Development',
            'is_public'          => true,
        ]);

        $lecArnaldo = Lecturer::create([
            'user_id'            => $userArnaldo->id,
            'study_program_id'   => $trpl->id,
            'nidn'               => '0801019008',
            'jabatan_fungsional' => 'Guru Besar / Profesor',
            'expertise'          => 'Jaringan Komputer, Teknologi Informasi',
            'is_public'          => true,
        ]);

        $lecFebrian = Lecturer::create([
            'user_id'            => $userFebrian->id,
            'study_program_id'   => $tk->id,
            'nidn'               => '0901019009',
            'jabatan_fungsional' => 'Asisten Ahli',
            'expertise'          => 'Embedded Systems, IoT',
            'is_public'          => true,
        ]);

        $lecEka = Lecturer::create([
            'user_id'            => $userEka->id,
            'study_program_id'   => $tk->id,
            'nidn'               => '1001019010',
            'jabatan_fungsional' => 'Asisten Ahli',
            'expertise'          => 'Elektronika, Instrumentasi',
            'is_public'          => true,
        ]);

        $lecCynthia = Lecturer::create([
            'user_id'            => $userCynthia->id,
            'study_program_id'   => $trpl->id,
            'nidn'               => '1101019011',
            'jabatan_fungsional' => 'Asisten Ahli',
            'expertise'          => 'Rekayasa Perangkat Lunak, Pengujian Sistem',
            'is_public'          => true,
        ]);

        // ============================================================
        // 4. SET HEAD_LECTURER per prodi
        // ============================================================
        $trpl->update(['head_lecturer_id' => $lecKaprodiTRPL->id]);
        $ti->update(['head_lecturer_id'   => $lecKaprodiTI->id]);
        // Kaprodi D3 TK belum ditentukan, biarkan null dulu

        // ============================================================
        // 5. DATA TRIDHARMA — contoh data untuk demo/UAT
        // Oppir (dosen biasa) dipakai sebagai contoh terlengkap
        // ============================================================
        Education::insert([
            ['lecturer_id' => $lecOppir->id,  'degree' => 'S1', 'institution' => 'Universitas Sumatera Utara', 'major' => 'Teknik Informatika',          'year' => 2015, 'visibility' => 'public',  'created_at' => now(), 'updated_at' => now()],
            ['lecturer_id' => $lecOppir->id,  'degree' => 'S2', 'institution' => 'Institut Teknologi Bandung', 'major' => 'Informatika',                  'year' => 2019, 'visibility' => 'public',  'created_at' => now(), 'updated_at' => now()],
            ['lecturer_id' => $lecTegar->id,  'degree' => 'S1', 'institution' => 'Institut Pertanian Bogor',   'major' => 'Statistika',                   'year' => 2016, 'visibility' => 'public',  'created_at' => now(), 'updated_at' => now()],
            ['lecturer_id' => $lecTegar->id,  'degree' => 'S2', 'institution' => 'Institut Teknologi Bandung', 'major' => 'Sains Komputasi',              'year' => 2021, 'visibility' => 'private', 'created_at' => now(), 'updated_at' => now()],
            ['lecturer_id' => $lecAna->id,    'degree' => 'S1', 'institution' => 'Universitas Pendidikan Indonesia', 'major' => 'Pendidikan Matematika', 'year' => 2013, 'visibility' => 'public',  'created_at' => now(), 'updated_at' => now()],
            ['lecturer_id' => $lecAna->id,    'degree' => 'S2', 'institution' => 'Universitas Pendidikan Indonesia', 'major' => 'Pendidikan Teknologi',  'year' => 2018, 'visibility' => 'public',  'created_at' => now(), 'updated_at' => now()],
            ['lecturer_id' => $lecFebrian->id,'degree' => 'S1', 'institution' => 'Universitas Sumatera Utara', 'major' => 'Teknik Elektro',               'year' => 2014, 'visibility' => 'public',  'created_at' => now(), 'updated_at' => now()],
            ['lecturer_id' => $lecFebrian->id,'degree' => 'S2', 'institution' => 'Institut Teknologi Bandung', 'major' => 'Teknik Elektro',               'year' => 2020, 'visibility' => 'public',  'created_at' => now(), 'updated_at' => now()],
        ]);

        Research::insert([
            ['lecturer_id' => $lecOppir->id,  'title' => 'Pengembangan Framework Microservice untuk Sistem Informasi Perguruan Tinggi Vokasi', 'year' => 2023, 'funding_source' => 'DIPA IT Del', 'visibility' => 'public',  'created_at' => now(), 'updated_at' => now()],
            ['lecturer_id' => $lecOppir->id,  'title' => 'Analisis Keamanan Aplikasi Web Berbasis Laravel pada Institusi Pendidikan',          'year' => 2024, 'funding_source' => 'Mandiri',    'visibility' => 'public',  'created_at' => now(), 'updated_at' => now()],
            ['lecturer_id' => $lecTegar->id,  'title' => 'Implementasi Machine Learning untuk Prediksi Performa Mahasiswa',                   'year' => 2023, 'funding_source' => 'DIPA IT Del', 'visibility' => 'public',  'created_at' => now(), 'updated_at' => now()],
            ['lecturer_id' => $lecFebrian->id,'title' => 'Sistem Monitoring Energi Berbasis IoT untuk Bangunan Kampus',                       'year' => 2024, 'funding_source' => 'Kemendikbud', 'visibility' => 'public',  'created_at' => now(), 'updated_at' => now()],
            ['lecturer_id' => $lecFebrian->id,'title' => 'Desain Sistem Kendali Otomatis pada Perangkat Embedded',                            'year' => 2023, 'funding_source' => 'DIPA IT Del', 'visibility' => 'private', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $pkmSeed = [
            ['lecturer' => $lecOppir,   'title' => 'Pelatihan Pemrograman Web untuk Siswa SMK se-Kabupaten Toba',     'year' => 2023, 'location' => 'Toba, Sumatera Utara',    'visibility' => 'public'],
            ['lecturer' => $lecOppir,   'title' => 'Workshop Digitalisasi UMKM Berbasis Aplikasi Web',                 'year' => 2024, 'location' => 'Laguboti, Toba',          'visibility' => 'public'],
            ['lecturer' => $lecTegar,   'title' => 'Pendampingan Analisis Data untuk Pemda Kabupaten Toba',            'year' => 2024, 'location' => 'Balige, Toba',            'visibility' => 'public'],
            ['lecturer' => $lecFebrian, 'title' => 'Instalasi dan Pelatihan Sistem Panel Surya untuk Desa Terpencil',  'year' => 2023, 'location' => 'Samosir, Sumatera Utara', 'visibility' => 'public'],
        ];
        foreach ($pkmSeed as $row) {
            $cs = CommunityService::create([
                'title'      => $row['title'],
                'year'       => $row['year'],
                'location'   => $row['location'],
                'visibility' => $row['visibility'],
            ]);
            $cs->lecturers()->attach($row['lecturer']->id, ['role' => 'Ketua']);
        }

        Publication::insert([
            ['lecturer_id' => $lecOppir->id,  'title' => 'Microservice Architecture for Higher Education Information Systems: A Case Study at Vocational Faculty',     'year' => 2023, 'publisher' => 'Journal of Information Systems Engineering and Business Intelligence', 'publisher_url' => 'https://e-journal.unair.ac.id/JISEBI',             'visibility' => 'public',  'created_at' => now(), 'updated_at' => now()],
            ['lecturer_id' => $lecOppir->id,  'title' => 'V-Model Implementation in Web-Based Academic Information System Development: A Systematic Review',           'year' => 2024, 'publisher' => 'JURNAL PETISI',                                                      'publisher_url' => 'https://e-journal.unmul.ac.id/index.php/petisi',  'visibility' => 'public',  'created_at' => now(), 'updated_at' => now()],
            ['lecturer_id' => $lecTegar->id,  'title' => 'Predictive Analytics for Student Academic Performance Using Ensemble Machine Learning Methods',              'year' => 2023, 'publisher' => 'IJCCS (Indonesian Journal of Computing and Cybernetics Systems)',   'publisher_url' => 'https://jurnal.ugm.ac.id/ijccs',                  'visibility' => 'public',  'created_at' => now(), 'updated_at' => now()],
            ['lecturer_id' => $lecFebrian->id,'title' => 'IoT-Based Energy Monitoring System for Campus Buildings: Design and Implementation',                         'year' => 2024, 'publisher' => 'IEEE Access',                                                        'publisher_url' => 'https://ieeexplore.ieee.org',                     'visibility' => 'public',  'created_at' => now(), 'updated_at' => now()],
            ['lecturer_id' => $lecAna->id,    'title' => 'Analisis Efektivitas Pembelajaran Berbasis Proyek pada Program Studi Vokasi Teknologi Informasi',            'year' => 2023, 'publisher' => 'Jurnal Pendidikan Vokasi',                                           'publisher_url' => 'https://journal.uny.ac.id/index.php/jpv',         'visibility' => 'public',  'created_at' => now(), 'updated_at' => now()],
        ]);

        $this->command->info('✅ Seeder selesai:');
        $this->command->info('   Prodi : D4 TRPL, D3 TI, D3 TK');
        $this->command->info('   Users : 1 Dekan, 2 Kaprodi, 8 Dosen (11 total)');
        $this->command->info('   Data  : tridharma lengkap untuk demo/UAT');
        $this->command->line('');
        $this->command->info('   Login credentials (semua password: password123):');
        $this->command->info('   Dekan   : riyanthi.sianturi@del.ac.id');
        $this->command->info('   Kaprodi : ardiles.sinaga@del.ac.id / goklas.panjaitan@del.ac.id');
        $this->command->info('   Dosen   : oppir.hutapea@del.ac.id / tegar.prasetyo@del.ac.id / dll');
    }
}
