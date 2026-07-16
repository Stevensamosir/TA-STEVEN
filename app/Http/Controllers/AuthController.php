<?php

namespace App\Http\Controllers;

use App\Models\Lecturer;
use App\Models\StudyProgram;
use App\Models\User;
use App\Services\CisAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Login lewat CIS. Urutan pengecekan role:
     * 1. Dekan (whitelist pegawai_id) -- tidak lewat filter prodi Vokasi
     * 2. LPPM (pattern di array jabatan) -- tidak lewat filter prodi Vokasi
     * 3. Kaprodi (pattern di array jabatan) -- HARUS Fakultas Vokasi
     * 4. Dosen (default) -- HARUS Fakultas Vokasi
     *
     * Tidak ada fallback jika CIS tidak dapat diakses (keputusan terkonfirmasi).
     */
    public function login(Request $request, CisAuthService $cis)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $result = $cis->login($request->username, $request->password);

        if (!$result) {
            return back()
                ->withErrors(['username' => 'Username atau password CIS salah, atau CIS sedang tidak dapat diakses.'])
                ->withInput($request->only('username'));
        }

        // Simpan token CIS di session (bukan DB) -- dipakai ulang untuk fitur
        // sinkronisasi massal dosen (khusus Dekan). Otomatis hilang saat logout.
        session(['cis_token' => $result['token'] ?? null]);

        $cisUser   = $result['user'];
        $pegawaiId = $cisUser['pegawai_id'] ?? null;

        if (!$pegawaiId) {
            return back()
                ->withErrors(['username' => 'Data pegawai_id tidak ditemukan pada respons CIS.'])
                ->withInput($request->only('username'));
        }

        // === JALUR 1: Dekan (whitelist) ===
        // Dekan TETAP dapat profil Lecturer (dibutuhkan seluruh fitur dashboard
        // yang memanggil auth()->user()->lecturer), tapi TIDAK kena filter
        // "harus Fakultas Vokasi" seperti Kaprodi/Dosen -- study_program_id
        // boleh null kalau prodi_id dari CIS tidak ketemu/di luar Vokasi.
        if ($cis->isDekanFromWhitelist($pegawaiId)) {
            $user = $this->provisionDekan($cisUser, $pegawaiId);
            return $this->finishLogin($request, $user, $cis);
        }

        // === JALUR 2: LPPM (deteksi dari jabatan, tidak perlu filter prodi) ===
        if ($cis->isLppmFromJabatan($cisUser)) {
            $user = $this->provisionNonLecturerUser($cisUser, 'lppm');
            return $this->finishLogin($request, $user, $cis);
        }

        // === JALUR 3 & 4: Kaprodi / Dosen ===
        // Lecturer lookup DULU (sebelum filter prodi Vokasi), supaya returning
        // user tidak ke-lock out kalau CIS mengubah/menomori-ulang prodi_id-nya.
        $lecturer = Lecturer::where('cis_pegawai_id', $pegawaiId)->first();

        $cisFields = [
            'nidn'               => $this->cleanValue($cisUser['nidn'] ?? null),
            'nip'                => $this->cleanValue($cisUser['nip'] ?? null),
            'alias'              => $this->cleanValue($cisUser['alias'] ?? null),
            'jabatan_fungsional' => $this->cleanValue($cisUser['jabatan_akademik_desc'] ?? null),
            'jenjang_pendidikan' => $this->cleanValue($cisUser['jenjang_pendidikan'] ?? null),
        ];

        if ($lecturer) {
            // Skenario A: dosen/kaprodi yang SUDAH terdaftar -- TIDAK digerbang ulang
            // oleh filter prodi Vokasi. Kalau CIS mengubah/menomori-ulang prodi_id
            // dosen ini di kemudian hari, dia TETAP BOLEH login (bukan pendaftar baru).
            // Role tetap di-cek ULANG tiap login supaya: (1) dosen hasil sinkronisasi
            // massal yang ternyata Kaprodi otomatis terkoreksi begitu login sendiri,
            // (2) perubahan jabatan di CIS ikut kesinkron otomatis. study_program_id
            // lokal sengaja TIDAK ikut diubah otomatis di sini -- kalau dosennya
            // memang pindah prodi beneran, perlu penyesuaian manual / sync massal.
            // Dekan TIDAK disentuh di sini (whitelist, bukan Jalur ini).
            $user = $lecturer->user;
            $isKaprodiNow = $cis->isKaprodiFromJabatan($cisUser);
            $user->update([
                'name' => $cisUser['nama'] ?? $user->name,
                'role' => $isKaprodiNow ? 'kaprodi' : 'dosen',
            ]);
            $lecturer->update($cisFields);

            return $this->finishLogin($request, $user, $cis);
        }

        // Belum pernah terdaftar sama sekali -- BARU DI SINI filter Fakultas Vokasi
        // berlaku, karena ini soal "apakah boleh mendaftar", bukan "apakah boleh
        // login lagi".
        $prodiMap   = config('cis.vokasi_prodi_map');
        $cisProdiId = isset($cisUser['prodi_id']) ? (string) $cisUser['prodi_id'] : null;

        if (!$cisProdiId || !array_key_exists($cisProdiId, $prodiMap)) {
            return back()
                ->withErrors(['username' => 'Akun ini bukan dosen Fakultas Vokasi. Akses SIPD tidak diizinkan.'])
                ->withInput($request->only('username'));
        }

        [$user, $lecturer] = DB::transaction(function () use ($cisUser, $cisFields, $prodiMap, $cisProdiId, $pegawaiId, $cis) {
            $studyProgram = StudyProgram::firstOrCreate(['name' => $prodiMap[$cisProdiId]]);
            $isKaprodi    = $cis->isKaprodiFromJabatan($cisUser);

            $email = $this->extractEmail($cisUser['email'] ?? null)
                     ?? (($cisUser['username'] ?? 'dosen') . '@del.ac.id');

            $existingUser = User::where('email', $email)->first();

            if ($existingUser && $existingUser->lecturer) {
                // Skenario B: user & lecturer LAMA sudah ada (misal dari seeder data contoh
                // sebelum integrasi CIS), tapi belum ter-link ke cis_pegawai_id. JANGAN
                // dihapus (mungkin sudah ada Penelitian/PKM nempel), cukup LINK akun ini
                // ke data CIS supaya login berikutnya langsung kena skenario A.
                $user     = $existingUser;
                $lecturer = $existingUser->lecturer;

                $user->update([
                    'name'     => $cisUser['nama'] ?? $user->name,
                    'username' => $cisUser['username'] ?? $user->username,
                    'role'     => $isKaprodi ? 'kaprodi' : 'dosen',
                ]);

                $lecturer->update(array_merge($cisFields, [
                    'study_program_id' => $studyProgram->id,
                    'cis_pegawai_id'   => $pegawaiId,
                ]));
            } else {
                if ($existingUser && !$existingUser->lecturer) {
                    // Skenario C: user nyangkut tanpa Lecturer pasangan (dari percobaan
                    // login sebelumnya yang gagal separuh jalan). Aman dihapus, tidak ada
                    // data Tridharma yang nempel ke Lecturer karena Lecturer-nya tidak ada.
                    $existingUser->delete();
                }

                // Skenario D: benar-benar dosen baru, belum pernah ada di SIPD sama sekali.
                $user = User::create([
                    'name'      => $cisUser['nama'] ?? $cisUser['username'],
                    'email'     => $email,
                    'username'  => $cisUser['username'] ?? null,
                    'password'  => null,
                    'role'      => $isKaprodi ? 'kaprodi' : 'dosen',
                    'is_active' => true,
                ]);

                $lecturer = Lecturer::create(array_merge($cisFields, [
                    'user_id'          => $user->id,
                    'study_program_id' => $studyProgram->id,
                    'cis_pegawai_id'   => $pegawaiId,
                    'is_public'        => true,
                ]));
            }

            return [$user, $lecturer];
        });

        return $this->finishLogin($request, $user, $cis);
    }

    /**
     * Provisioning khusus Dekan: SELALU dapat profil Lecturer (beda dari LPPM
     * yang tetap non-lecturer), tapi tidak difilter Fakultas Vokasi seperti
     * Kaprodi/Dosen. study_program_id null kalau prodi CIS-nya tidak cocok
     * dengan config('cis.vokasi_prodi_map').
     */
    private function provisionDekan(array $cisUser, $pegawaiId): User
    {
        $cisFields = [
            'nidn'               => $this->cleanValue($cisUser['nidn'] ?? null),
            'nip'                => $this->cleanValue($cisUser['nip'] ?? null),
            'alias'              => $this->cleanValue($cisUser['alias'] ?? null),
            'jabatan_fungsional' => $this->cleanValue($cisUser['jabatan_akademik_desc'] ?? null),
            'jenjang_pendidikan' => $this->cleanValue($cisUser['jenjang_pendidikan'] ?? null),
        ];

        // Sudah pernah login & Lecturer sudah ter-link ke CIS -> tinggal refresh.
        $lecturer = Lecturer::where('cis_pegawai_id', $pegawaiId)->first();
        if ($lecturer) {
            $user = $lecturer->user;
            $user->update([
                'name' => $cisUser['nama'] ?? $user->name,
                'role' => 'dekan',
            ]);
            $lecturer->update($cisFields);
            return $user;
        }

        return DB::transaction(function () use ($cisUser, $pegawaiId, $cisFields) {
            $email = $this->extractEmail($cisUser['email'] ?? null)
                     ?? (($cisUser['username'] ?? 'dekan') . '@del.ac.id');

            // Reuse akun User Dekan lama kalau sudah ada dari sebelum fix ini
            // (dibuat via provisionNonLecturerUser, belum punya Lecturer).
            // JANGAN dihapus -- cukup dilengkapi Lecturer-nya.
            $user = User::where('username', $cisUser['username'] ?? null)->first()
                  ?? User::where('email', $email)->first();

            if ($user) {
                $user->update([
                    'name'      => $cisUser['nama'] ?? $user->name,
                    'username'  => $cisUser['username'] ?? $user->username,
                    'role'      => 'dekan',
                    'is_active' => true,
                ]);
            } else {
                $user = User::create([
                    'name'      => $cisUser['nama'] ?? $cisUser['username'],
                    'email'     => $email,
                    'username'  => $cisUser['username'] ?? null,
                    'password'  => null,
                    'role'      => 'dekan',
                    'is_active' => true,
                ]);
            }

            $prodiMap       = config('cis.vokasi_prodi_map');
            $cisProdiId     = isset($cisUser['prodi_id']) ? (string) $cisUser['prodi_id'] : null;
            $studyProgramId = ($cisProdiId && array_key_exists($cisProdiId, $prodiMap))
                ? StudyProgram::firstOrCreate(['name' => $prodiMap[$cisProdiId]])->id
                : null;

            if ($user->lecturer) {
                $user->lecturer->update(array_merge($cisFields, [
                    'study_program_id' => $studyProgramId,
                    'cis_pegawai_id'   => $pegawaiId,
                ]));
            } else {
                Lecturer::create(array_merge($cisFields, [
                    'user_id'          => $user->id,
                    'study_program_id' => $studyProgramId,
                    'cis_pegawai_id'   => $pegawaiId,
                    'is_public'        => true,
                ]));
            }

            return $user;
        });
    }

    /**
     * Provisioning untuk role yang bukan dosen (Dekan, LPPM): tidak punya
     * profil Lecturer, hanya akun User.
     */
    private function provisionNonLecturerUser(array $cisUser, string $role): User
    {
        $email = $this->extractEmail($cisUser['email'] ?? null)
                 ?? (($cisUser['username'] ?? $role) . '@del.ac.id');

        $existing = User::where('email', $email)
            ->where('username', '!=', $cisUser['username'] ?? null)
            ->first();
        if ($existing) {
            $existing->delete();
        }

        return User::firstOrCreate(
            ['username' => $cisUser['username'] ?? null],
            [
                'name'      => $cisUser['nama'] ?? $cisUser['username'],
                'email'     => $email,
                'password'  => null,
                'role'      => $role,
                'is_active' => true,
            ]
        );
    }

    private function finishLogin(Request $request, User $user, ?CisAuthService $cis = null)
    {
        if (!$user->is_active) {
            return back()
                ->withErrors(['username' => 'Akun Anda telah dinonaktifkan. Hubungi admin.'])
                ->withInput($request->only('username'));
        }

        $user->update(['last_login_at' => now()]);

        // Sinkronisasi otomatis di background, dipicu SIAPAPUN yang login berhasil
        // (Dosen/Kaprodi/Dekan/LPPM) -- bukan cuma Dekan seperti sebelumnya.
        $token = session('cis_token');
        if ($token && $cis) {
            app()->terminating(function () use ($cis, $token) {
                $cis->syncVokasiLecturers($token);
            });
        }

        Auth::login($user);
        $request->session()->regenerate();

        if ($user->isDekan()) {
            return redirect()->intended(route('admin.index'));
        }

        if ($user->isLppm()) {
            return redirect()->intended(route('lppm.index'));
        }

        return redirect()->intended(route('dosen.index'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }

    private function extractEmail(?string $raw): ?string
    {
        if (!$raw || trim($raw) === '-') {
            return null;
        }
        $first = trim(explode(',', $raw)[0]);
        return filter_var($first, FILTER_VALIDATE_EMAIL) ? $first : null;
    }

    private function cleanValue(?string $value): ?string
    {
        $value = trim((string) $value);
        return ($value === '' || $value === '-') ? null : $value;
    }
}
