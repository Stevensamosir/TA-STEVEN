<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CisAuthService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('cis.base_url'), '/');
    }

    /**
     * Login ke CIS. PENTING: respons Login CIS TIDAK berisi pegawai_id,
     * prodi_id, nidn, nip, alias, jenjang_pendidikan -- field itu cuma
     * ada di endpoint Get Dosen. Jadi setelah login berhasil, kita panggil
     * Get Dosen dan cocokkan lewat user_id untuk melengkapi datanya.
     *
     * @return array{user: array, token: ?string}|null
     */
    public function login(string $username, string $password): ?array
    {
        try {
            $response = $this->httpClient()
                ->asForm()
                ->timeout(6)
                ->post("{$this->baseUrl}/jwt-api/do-auth", [
                    'username' => $username,
                    'password' => $password,
                ]);
        } catch (\Throwable $e) {
            report($e);
            return null;
        }

        if (!$response->successful()) {
            return null;
        }

        $body = $response->json();

        $ok = ($body['result'] ?? null) === 'Ok' || ($body['result'] ?? null) === true;
        if (!$ok || empty($body['user'])) {
            return null;
        }

        $cisUser = $body['user'];
        $token   = $body['token'] ?? null;

        // Lengkapi data dari Get Dosen (pegawai_id, prodi_id, nidn, nip, alias, dst)
        $profile = $this->findDosenProfileByUserId($token, $cisUser['user_id'] ?? null);
        if ($profile) {
            $cisUser = array_merge($cisUser, $profile);
        }

        return [
            'user'  => $cisUser,
            'token' => $token,
        ];
    }

    /**
     * Ambil SELURUH daftar dosen dari CIS (di-cache 30 menit). Dipakai baik
     * untuk melengkapi data satu dosen saat login, maupun untuk sinkronisasi
     * massal seluruh dosen Fakultas Vokasi.
     */
    public function fetchDosenList(string $token): array
    {
        return Cache::remember('cis:dosen-list', now()->addMinutes(30), function () use ($token) {
            try {
                $response = $this->httpClient()
                    ->withToken($token)
                    ->timeout(15)
                    ->get("{$this->baseUrl}/library-api/dosen");
            } catch (\Throwable $e) {
                report($e);
                return [];
            }

            if (!$response->successful()) {
                return [];
            }

            return $response->json('data.dosen', []);
        });
    }

    /**
     * Ambil daftar lengkap dosen dari CIS (di-cache 30 menit supaya tidak
     * memanggil CIS berulang-ulang di setiap login), lalu cari satu baris
     * yang user_id-nya cocok dengan hasil login.
     */
    private function findDosenProfileByUserId(?string $token, $userId): ?array
    {
        if (!$token || !$userId) {
            return null;
        }

        $list = $this->fetchDosenList($token);

        foreach ($list as $item) {
            if ((string) ($item['user_id'] ?? '') === (string) $userId) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Cek status aktif seorang dosen di CIS. Endpoint ini terpisah dari daftar
     * massal (library-api/dosen) yang TIDAK menyediakan info ini. Dipanggil
     * satu per satu -- kalau CIS ke depannya punya versi massal, ganti method
     * ini supaya lebih efisien.
     *
     * Return true kalau aktif ATAU kalau pengecekan gagal (fail-safe: JANGAN
     * sampai gangguan jaringan bikin dosen aktif keanggap tidak aktif).
     */
    public function isDosenActive(string $token, $pegawaiId): bool
    {
        try {
            $response = $this->httpClient()
                ->withToken($token)
                ->timeout(10)
                ->get("{$this->baseUrl}/library-api/get-dosen-active-status", [
                    'pegawai_id' => $pegawaiId,
                ]);
        } catch (\Throwable $e) {
            report($e);
            return true; // fail-safe
        }

        if (!$response->successful()) {
            return true; // fail-safe
        }

        return trim((string) $response->json('data', 'Aktif')) === 'Aktif';
    }

    /**
     * Sinkronisasi seluruh dosen Fakultas Vokasi dari CIS. Dipanggil otomatis
     * di background setiap kali Dekan login (lihat AuthController), BUKAN lewat
     * tombol manual. Return array statistik ['created'=>, 'updated'=>, 'skipped'=>,
     * 'failed'=>, 'inactive'=>].
     */
    public function syncVokasiLecturers(string $token): array
    {
        $list     = $this->fetchDosenList($token);
        $prodiMap = config('cis.vokasi_prodi_map');

        $created = 0; $updated = 0; $skipped = 0; $failed = 0; $inactive = 0;

        foreach ($list as $item) {
          try {
            $prodiId = isset($item['prodi_id']) ? (string) $item['prodi_id'] : null;
            if (!$prodiId || !array_key_exists($prodiId, $prodiMap)) {
                $skipped++;
                continue;
            }

            $pegawaiId = $item['pegawai_id'] ?? null;
            if (!$pegawaiId) {
                $skipped++;
                continue;
            }

            $namaValid = trim((string) ($item['nama'] ?? '')) !== ''
                      && trim((string) ($item['nama'] ?? '')) !== '-';
            if (!$namaValid) {
                $skipped++;
                continue; // data CIS rusak/kosong
            }

            if (config('cis.auto_deactivate_enabled') && !$this->isDosenActive($token, $pegawaiId)) {
                $inactive++;
                $existingLecturer = \App\Models\Lecturer::where('cis_pegawai_id', $pegawaiId)->first();
                if ($existingLecturer) {
                    $existingLecturer->user->update(['is_active' => false]);
                }
                continue;
            }

            $cisFields = [
                'nidn'               => $item['nidn'] ?? null,
                'nip'                => $item['nip'] ?? null,
                'alias'              => $item['alias'] ?? null,
                'jabatan_fungsional' => $item['jabatan_akademik_desc'] ?? null,
                'jenjang_pendidikan' => $item['jenjang_pendidikan'] ?? null,
            ];

            $lecturer = \App\Models\Lecturer::where('cis_pegawai_id', $pegawaiId)->first();

            if ($lecturer) {
                $lecturer->update($cisFields);
                $updated++;
                continue;
            }

            $rawEmail = $item['email'] ?? null;
            $firstEmail = $rawEmail ? trim(explode(',', $rawEmail)[0]) : null;
            $email = ($firstEmail && filter_var($firstEmail, FILTER_VALIDATE_EMAIL))
                ? $firstEmail
                : "dosen{$pegawaiId}@del.ac.id"; // fallback UNIK per pegawai_id

            $existingUser = \App\Models\User::where('email', $email)->first();
            if ($existingUser && $existingUser->lecturer) {
                $existingUser->lecturer->update(array_merge($cisFields, ['cis_pegawai_id' => $pegawaiId]));
                $updated++;
                continue;
            }

            $studyProgram = \App\Models\StudyProgram::firstOrCreate(['name' => $prodiMap[$prodiId]]);

            $user = $existingUser ?: \App\Models\User::create([
                'name'      => $item['nama'] ?? 'Dosen',
                'email'     => $email,
                'username'  => null,
                'password'  => null,
                'role'      => 'dosen',
                'is_active' => true,
            ]);

            \App\Models\Lecturer::create(array_merge($cisFields, [
                'user_id'          => $user->id,
                'study_program_id' => $studyProgram->id,
                'cis_pegawai_id'   => $pegawaiId,
                'is_public'        => true,
            ]));
            $created++;
          } catch (\Throwable $e) {
              report($e);
              $failed++;
          }
        }

        return compact('created', 'updated', 'skipped', 'failed', 'inactive');
    }

    /**
     * HTTP client dengan SSL verification dimatikan HANYA di environment
     * local (server dev CIS pakai self-signed certificate). Tidak aktif
     * di production.
     */
    private function httpClient()
    {
        return Http::when(
            app()->environment('local'),
            fn ($http) => $http->withoutVerifying()
        );
    }

    /**
     * Deteksi Kaprodi dari array "jabatan": cari teks yang mengandung
     * pola di config('cis.kaprodi_jabatan_pattern').
     */
    public function isKaprodiFromJabatan(array $cisUser): bool
    {
        $pattern = strtolower(config('cis.kaprodi_jabatan_pattern', ''));
        return $this->jabatanContainsAny($cisUser, [$pattern]);
    }

    /**
     * Deteksi LPPM dari array "jabatan": cari teks yang mengandung salah
     * satu pola di config('cis.lppm_jabatan_pattern').
     */
    public function isLppmFromJabatan(array $cisUser): bool
    {
        $patterns = config('cis.lppm_jabatan_pattern', []);
        if (is_string($patterns)) {
            $patterns = [$patterns];
        }
        return $this->jabatanContainsAny($cisUser, $patterns);
    }

    /**
     * Cek Dekan dari whitelist pegawai_id (tidak bisa dideteksi otomatis
     * dari teks jabatan, lihat catatan di config/cis.php).
     */
    public function isDekanFromWhitelist(int|string $pegawaiId): bool
    {
        return in_array((string) $pegawaiId, config('cis.dekan_pegawai_ids', []), true);
    }

    private function jabatanContainsAny(array $cisUser, array $patterns): bool
    {
        $jabatanList = $cisUser['jabatan'] ?? [];
        foreach ($jabatanList as $item) {
            $teks = strtolower($item['jabatan'] ?? '');
            foreach ($patterns as $pattern) {
                if ($pattern !== '' && str_contains($teks, strtolower($pattern))) {
                    return true;
                }
            }
        }
        return false;
    }
}
