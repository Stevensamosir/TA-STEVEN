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
        // 1. STUDY PROGRAMS (tanpa kaprodi dulu — circular reference)
        // ============================================================
        $trpl = StudyProgram::create(['name' => 'Teknologi Rekayasa Perangkat Lunak']);
        $trkj = StudyProgram::create(['name' => 'Teknologi Rekayasa Komputer Jaringan']);

        // ============================================================
        // 2. USERS
        // ============================================================
        $userDekan = User::create([
            'name'      => 'Dr. Budi Santoso, M.Kom.',
            'email'     => 'dekan@vokasi.del.ac.id',
            'password'  => Hash::make('password123'),
            'role'      => 'dekan',
            'is_active' => true,
        ]);

        $userKaprodi = User::create([
            'name'      => 'Oppir Hutapea, S.Tr.Kom., M.Kom.',
            'email'     => 'oppir.hutapea@del.ac.id',
            'password'  => Hash::make('password123'),
            'role'      => 'kaprodi',
            'is_active' => true,
        ]);

        $userDosen1 = User::create([
            'name'      => 'Pak Tegar',
            'email'     => 'tegar@del.ac.id',
            'password'  => Hash::make('password123'),
            'role'      => 'dosen',
            'is_active' => true,
        ]);

        $userDosen2 = User::create([
            'name'      => 'Pak Rudy',
            'email'     => 'rudy@del.ac.id',
            'password'  => Hash::make('password123'),
            'role'      => 'dosen',
            'is_active' => true,
        ]);

        // ============================================================
        // 3. LECTURERS
        // ============================================================
        $lecDekan = Lecturer::create([
            'user_id'          => $userDekan->id,
            'study_program_id' => $trpl->id,
            'nidn'             => '0101019001',
            'expertise'        => 'Sistem Informasi, Tata Kelola TI',
            'is_public'        => true,
        ]);

        $lecKaprodi = Lecturer::create([
            'user_id'          => $userKaprodi->id,
            'study_program_id' => $trpl->id,
            'nidn'             => '0201029001',
            'expertise'        => 'Rekayasa Perangkat Lunak, Web Development',
            'is_public'        => true,
        ]);

        $lecDosen1 = Lecturer::create([
            'user_id'          => $userDosen1->id,
            'study_program_id' => $trpl->id,
            'nidn'             => '0301039001',
            'expertise'        => 'Mobile Development, UI/UX',
            'is_public'        => true,
        ]);

        $lecDosen2 = Lecturer::create([
            'user_id'          => $userDosen2->id,
            'study_program_id' => $trkj->id,
            'nidn'             => '0401049001',
            'expertise'        => 'Jaringan Komputer, Keamanan Sistem',
            'is_public'        => true,
        ]);

        // ============================================================
        // 4. UPDATE KAPRODI (setelah lecturers ada)
        // ============================================================
        $trpl->update(['head_lecturer_id' => $lecKaprodi->id]);
        $trkj->update(['head_lecturer_id' => $lecDosen2->id]);

        // ============================================================
        // 5. DATA TRIDHARMA — Oppir (Kaprodi) sebagai contoh terlengkap
        // ============================================================
        Education::insert([
            ['lecturer_id' => $lecKaprodi->id, 'degree' => 'S1', 'institution' => 'Universitas Sumatera Utara',     'major' => 'Teknik Informatika',            'year' => 2015, 'visibility' => 'public',  'created_at' => now(), 'updated_at' => now()],
            ['lecturer_id' => $lecKaprodi->id, 'degree' => 'S2', 'institution' => 'Institut Teknologi Bandung',     'major' => 'Informatika',                   'year' => 2019, 'visibility' => 'public',  'created_at' => now(), 'updated_at' => now()],
            ['lecturer_id' => $lecDosen1->id,  'degree' => 'S1', 'institution' => 'Universitas Gadjah Mada',        'major' => 'Ilmu Komputer',                 'year' => 2016, 'visibility' => 'public',  'created_at' => now(), 'updated_at' => now()],
            ['lecturer_id' => $lecDosen1->id,  'degree' => 'S2', 'institution' => 'Institut Teknologi Del',         'major' => 'Teknologi Informasi',           'year' => 2021, 'visibility' => 'private', 'created_at' => now(), 'updated_at' => now()],
        ]);

        Research::insert([
            ['lecturer_id' => $lecKaprodi->id, 'title' => 'Pengembangan Framework Microservice untuk Sistem Informasi Perguruan Tinggi', 'year' => 2023, 'funding_source' => 'DIPA IT Del', 'visibility' => 'public',  'created_at' => now(), 'updated_at' => now()],
            ['lecturer_id' => $lecKaprodi->id, 'title' => 'Analisis Keamanan Aplikasi Web Berbasis Laravel pada Institusi Pendidikan',   'year' => 2024, 'funding_source' => 'Mandiri',    'visibility' => 'public',  'created_at' => now(), 'updated_at' => now()],
            ['lecturer_id' => $lecDosen1->id,  'title' => 'Implementasi CI/CD Pipeline untuk Pengembangan Aplikasi Mobile',             'year' => 2023, 'funding_source' => 'DIPA IT Del', 'visibility' => 'public',  'created_at' => now(), 'updated_at' => now()],
            ['lecturer_id' => $lecDosen2->id,  'title' => 'Optimasi Jaringan SDN untuk Kampus Pintar',                                  'year' => 2024, 'funding_source' => 'Kemendikbud', 'visibility' => 'private', 'created_at' => now(), 'updated_at' => now()],
        ]);

        CommunityService::insert([
            ['lecturer_id' => $lecKaprodi->id, 'title' => 'Pelatihan Pemrograman Web untuk Siswa SMK se-Kabupaten Toba', 'year' => 2023, 'location' => 'Toba, Sumatera Utara',  'visibility' => 'public',  'created_at' => now(), 'updated_at' => now()],
            ['lecturer_id' => $lecKaprodi->id, 'title' => 'Workshop Digitalisasi UMKM Berbasis Aplikasi Android',        'year' => 2024, 'location' => 'Laguboti, Toba',        'visibility' => 'public',  'created_at' => now(), 'updated_at' => now()],
            ['lecturer_id' => $lecDosen1->id,  'title' => 'Pendampingan Pembuatan Website Desa Wisata',                  'year' => 2024, 'location' => 'Samosir, Sumatera Utara','visibility' => 'public',  'created_at' => now(), 'updated_at' => now()],
        ]);

        Publication::insert([
            ['lecturer_id' => $lecKaprodi->id, 'title' => 'Microservice Architecture for Higher Education Information Systems: A Case Study',          'year' => 2023, 'publisher' => 'Journal of Information Systems Engineering and Business Intelligence', 'publisher_url' => 'https://e-journal.unair.ac.id/JISEBI', 'visibility' => 'public',  'created_at' => now(), 'updated_at' => now()],
            ['lecturer_id' => $lecKaprodi->id, 'title' => 'V-Model Implementation in Web-Based Academic Information System Development',               'year' => 2024, 'publisher' => 'JURNAL PETISI', 'publisher_url' => 'https://e-journal.unmul.ac.id/index.php/petisi', 'visibility' => 'public',  'created_at' => now(), 'updated_at' => now()],
            ['lecturer_id' => $lecDosen1->id,  'title' => 'Mobile-First Approach in Designing Responsive Academic Portals for Vocational Education',  'year' => 2023, 'publisher' => 'IJCCS (Indonesian Journal of Computing and Cybernetics Systems)', 'publisher_url' => 'https://jurnal.ugm.ac.id/ijccs', 'visibility' => 'public',  'created_at' => now(), 'updated_at' => now()],
            ['lecturer_id' => $lecDosen2->id,  'title' => 'Software Defined Networking for Smart Campus Infrastructure Optimization',                  'year' => 2024, 'publisher' => 'IEEE Access', 'publisher_url' => 'https://ieeexplore.ieee.org', 'visibility' => 'private', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $this->command->info('✅ Seeder selesai:');
        $this->command->info('   - 2 prodi, 4 users, 4 lecturers');
        $this->command->info('   - Data tridharma lengkap (pendidikan, penelitian, pengabdian, publikasi)');
        $this->command->info('   Login: dekan@vokasi.del.ac.id / oppir.hutapea@del.ac.id / tegar@del.ac.id / rudy@del.ac.id');
        $this->command->info('   Password semua: password123');
    }
}
