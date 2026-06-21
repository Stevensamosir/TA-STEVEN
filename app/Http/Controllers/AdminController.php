<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Lecturer;
use App\Models\StudyProgram;
use App\Models\Publication;

class AdminController extends Controller
{
    // ─────────────────────────────────────────────────────────────────
    // DEFENSE-IN-DEPTH: Route middleware sudah memblokir akses salah role.
    // Ini lapisan kedua jika ada bypass.
    // ─────────────────────────────────────────────────────────────────
    private function assertDekanOnly(): void
    {
        if (!auth()->user()->isDekan()) {
            abort(403, 'Akses ditolak. Hanya Dekan yang dapat melakukan aksi ini.');
        }
    }

    // Kaprodi hanya boleh edit dosen yang ada di prodinya sendiri
    // dan target bukan Dekan atau Kaprodi lain
    private function assertKaprodiCanEdit(Lecturer $target): void
    {
        $auth = auth()->user();

        // Dekan boleh semua
        if ($auth->isDekan()) return;

        // Kaprodi: cek scope
        if ($auth->isKaprodi()) {
            $myLecturer = $auth->lecturer;

            // Tidak boleh edit Dekan
            if ($target->user->role === 'dekan') {
                abort(403, 'Kaprodi tidak dapat mengedit profil Dekan.');
            }

            // Tidak boleh edit Kaprodi lain
            if ($target->user->role === 'kaprodi' && $target->user_id !== $auth->id) {
                abort(403, 'Kaprodi hanya dapat mengedit dosen di prodinya sendiri.');
            }

            // Tidak boleh edit dosen prodi lain
            if ($myLecturer && $target->study_program_id !== $myLecturer->study_program_id) {
                abort(403, 'Kaprodi hanya dapat mengedit dosen di prodinya sendiri.');
            }

            return;
        }

        abort(403);
    }

    // ─── DASHBOARD (Dekan only) ──────────────────────────────────────
    public function index()
    {
        $this->assertDekanOnly();
        $stats = [
            'total_dosen'     => Lecturer::count(),
            'total_publik'    => Lecturer::where('is_public', true)->count(),
            'total_prodi'     => StudyProgram::count(),
            'total_publikasi' => Publication::count(),
        ];
        $recentDosen = Lecturer::with('user', 'studyProgram')->latest()->take(5)->get();
        return view('admin.index', compact('stats', 'recentDosen'));
    }

    // ─── KELOLA DOSEN (Dekan only) ───────────────────────────────────
    public function dosenList(Request $request)
    {
        $this->assertDekanOnly();
        $dosens = Lecturer::with(['user', 'studyProgram'])
            ->withCount(['educations', 'researches', 'communityServices', 'publications'])
            ->when($request->search, function ($q) use ($request) {
                // Kondisi OR dibungkus dalam satu closure (sama seperti perbaikan
                // di PublicController) supaya tidak bocor keluar dan tetap aman
                // ditambah filter lain di masa depan.
                $q->where(function ($sub) use ($request) {
                    $sub->whereHas('user', fn($u) => $u->where('name', 'like', '%'.$request->search.'%')
                                                         ->orWhere('email', 'like', '%'.$request->search.'%'))
                        ->orWhereHas('studyProgram', fn($sp) => $sp->where('name', 'like', '%'.$request->search.'%'));
                });
            })
            ->get();
        $activeDekanCount = User::where('role', 'dekan')->where('is_active', true)->count();
        return view('admin.dosen', compact('dosens', 'activeDekanCount'));
    }

    public function createDosen()
    {
        $this->assertDekanOnly();
        $studyPrograms = StudyProgram::all();
        $dekanExists   = User::where('role', 'dekan')->where('is_active', true)->exists();
        return view('admin.dosen-create', compact('studyPrograms', 'dekanExists'));
    }

    public function storeDosen(Request $request)
    {
        $this->assertDekanOnly();
        $request->validate([
            'name'               => 'required|string|max:255',
            'email'              => 'required|email|unique:users',
            'password'           => 'required|min:8',
            'role'               => 'required|in:dosen,kaprodi,dekan',
            'study_program_id'   => 'required|exists:study_programs,id',
            'nidn'               => 'nullable|string|max:20',
            'jabatan_fungsional' => 'nullable|string|in:Asisten Ahli,Lektor,Lektor Kepala,Guru Besar / Profesor',
            'expertise'          => 'nullable|string',
        ]);

        // Constraint: Dekan maksimal 1 aktif
        if ($request->role === 'dekan') {
            $exists = User::where('role', 'dekan')->where('is_active', true)->exists();
            if ($exists) {
                return back()
                    ->withErrors(['role' => 'Hanya dapat ada 1 Dekan aktif. Nonaktifkan Dekan yang ada terlebih dahulu.'])
                    ->withInput();
            }
        }

        // Constraint: Kaprodi maksimal 1 aktif per program studi
        if ($request->role === 'kaprodi') {
            $prodiName = StudyProgram::find($request->study_program_id)?->name ?? 'prodi ini';
            $existsKaprodi = User::where('role', 'kaprodi')
                ->where('is_active', true)
                ->whereHas('lecturer', fn($q) => $q->where('study_program_id', $request->study_program_id))
                ->exists();
            if ($existsKaprodi) {
                return back()
                    ->withErrors(['role' => "Sudah ada Kaprodi aktif untuk {$prodiName}. Nonaktifkan Kaprodi yang ada terlebih dahulu."])
                    ->withInput();
            }
        }

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => $request->role,
            'is_active' => true,
        ]);
        Lecturer::create([
            'user_id'            => $user->id,
            'study_program_id'   => $request->study_program_id,
            'nidn'               => $request->nidn,
            'jabatan_fungsional' => $request->jabatan_fungsional,
            'expertise'          => $request->expertise,
            'is_public'          => true,
        ]);

        // Notifikasi dinamis per role
        $roleLabel = match($request->role) {
            'kaprodi' => 'kaprodi',
            'dekan'   => 'dekan',
            default   => 'dosen',
        };
        return redirect()->route('admin.dosen')
            ->with('success', "Akun {$roleLabel} {$request->name} berhasil dibuat.");
    }

    public function editDosen($id)
    {
        $this->assertDekanOnly();
        $lecturer      = Lecturer::with('user', 'studyProgram')->findOrFail($id);
        $studyPrograms = StudyProgram::all();
        $dekanExists   = User::where('role', 'dekan')->where('is_active', true)
                             ->where('id', '!=', $lecturer->user->id)->exists();
        return view('admin.dosen-edit', compact('lecturer', 'studyPrograms', 'dekanExists'));
    }

    public function updateDosen(Request $request, $id)
    {
        $this->assertDekanOnly();
        $lecturer = Lecturer::with('user')->findOrFail($id);
        $request->validate([
            'name'               => 'required|string|max:255',
            'email'              => 'required|email|unique:users,email,'.$lecturer->user->id,
            'role'               => 'required|in:dosen,kaprodi,dekan',
            'study_program_id'   => 'required|exists:study_programs,id',
            'nidn'               => 'nullable|string|max:20',
            'jabatan_fungsional' => 'nullable|string',
            'expertise'          => 'nullable|string',
        ]);

        // Constraint: Dekan maksimal 1 aktif (saat ubah role ke dekan)
        if ($request->role === 'dekan' && $lecturer->user->role !== 'dekan') {
            $exists = User::where('role', 'dekan')->where('is_active', true)
                         ->where('id', '!=', $lecturer->user->id)->exists();
            if ($exists) {
                return back()
                    ->withErrors(['role' => 'Hanya dapat ada 1 Dekan aktif. Nonaktifkan Dekan yang ada terlebih dahulu.'])
                    ->withInput();
            }
        }

        // PROTEKSI: jangan sampai satu-satunya Dekan aktif diturunkan rolenya
        // lewat form Edit biasa — ini bisa membuat sistem kehilangan akses
        // admin sama sekali tanpa pengganti. Kalau memang mau lengser, harus
        // lewat alur "Transfer Jabatan Dekan" yang menjamin ada penerus.
        if ($lecturer->user->role === 'dekan' && $request->role !== 'dekan') {
            $activeDekanCount = User::where('role', 'dekan')->where('is_active', true)->count();
            if ($activeDekanCount <= 1) {
                return back()
                    ->withErrors(['role' => 'Tidak bisa mengubah role satu-satunya Dekan aktif lewat form ini. Gunakan fitur "Transfer Jabatan Dekan" di halaman Profil Saya agar selalu ada penerus.'])
                    ->withInput();
            }
        }

        // Constraint: Kaprodi maksimal 1 aktif per prodi (saat ubah role/prodi ke kaprodi)
        if ($request->role === 'kaprodi') {
            $prodiName = StudyProgram::find($request->study_program_id)?->name ?? 'prodi ini';
            $existsKaprodi = User::where('role', 'kaprodi')
                ->where('is_active', true)
                ->where('id', '!=', $lecturer->user_id)
                ->whereHas('lecturer', fn($q) => $q->where('study_program_id', $request->study_program_id))
                ->exists();
            if ($existsKaprodi) {
                return back()
                    ->withErrors(['role' => "Sudah ada Kaprodi aktif untuk {$prodiName}. Nonaktifkan Kaprodi yang ada terlebih dahulu."])
                    ->withInput();
            }
        }

        $lecturer->user->update([
            'name'  => $request->name,
            'email' => $request->email,
            'role'  => $request->role,
        ]);
        $lecturer->update([
            'study_program_id'   => $request->study_program_id,
            'nidn'               => $request->nidn,
            'jabatan_fungsional' => $request->jabatan_fungsional,
            'expertise'          => $request->expertise,
        ]);
        return redirect()->route('admin.dosen')->with('success', 'Data dosen berhasil diperbarui.');
    }

    public function resetPassword($id)
    {
        $this->assertDekanOnly();
        $lecturer = Lecturer::with('user')->findOrFail($id);
        $newPass  = 'password123';
        $lecturer->user->update(['password' => Hash::make($newPass)]);
        return back()->with('success', "Password direset ke: $newPass");
    }

    public function toggleActive($id)
    {
        $this->assertDekanOnly();
        $lecturer = Lecturer::with('user')->findOrFail($id);

        // PROTEKSI: jangan sampai Dekan menonaktifkan akun sendiri (bisa benar-
        // benar terkunci tidak bisa login lagi), atau menonaktifkan satu-
        // satunya Dekan aktif lewat baris dosen lain yang kebetulan dia sendiri.
        if ($lecturer->user_id === auth()->id() && $lecturer->user->is_active) {
            return back()->withErrors([
                'dosen' => 'Anda tidak bisa menonaktifkan akun Anda sendiri.'
            ]);
        }
        if ($lecturer->user->role === 'dekan' && $lecturer->user->is_active) {
            $activeDekanCount = User::where('role', 'dekan')->where('is_active', true)->count();
            if ($activeDekanCount <= 1) {
                return back()->withErrors([
                    'dosen' => 'Tidak bisa menonaktifkan satu-satunya akun Dekan aktif di sistem.'
                ]);
            }
        }

        $lecturer->user->update(['is_active' => !$lecturer->user->is_active]);
        $status = $lecturer->user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Akun dosen berhasil $status.");
    }

    // ─── HAPUS PERMANEN DOSEN ─────────────────────────────────────
    // Hanya untuk kasus "salah buat akun" — dosen yang BELUM punya data
    // Tridharma sama sekali (pendidikan, penelitian, pengabdian, publikasi
    // semuanya 0). Begitu salah satu data sudah diisi, hapus permanen
    // diblokir di sini DAN di tombolnya (view) — harus pakai nonaktifkan
    // supaya riwayat akademik dosen tidak hilang.
    public function destroyDosen($id)
    {
        $this->assertDekanOnly();
        $lecturer = Lecturer::with('user')
            ->withCount(['educations', 'researches', 'communityServices', 'publications'])
            ->findOrFail($id);

        // PROTEKSI 1: tidak boleh hapus akun sendiri yang sedang login,
        // berapa pun jumlah data Tridharma-nya. Menghapus akun sendiri saat
        // masih login bisa membuat sesi jadi rusak / tidak ada Dekan aktif.
        if ($lecturer->user_id === auth()->id()) {
            return back()->withErrors([
                'dosen' => 'Anda tidak bisa menghapus akun Anda sendiri.'
            ]);
        }

        // PROTEKSI 2: jangan sampai sistem kehabisan akun Dekan aktif.
        if ($lecturer->user->role === 'dekan') {
            $activeDekanCount = User::where('role', 'dekan')->where('is_active', true)->count();
            if ($activeDekanCount <= 1) {
                return back()->withErrors([
                    'dosen' => 'Tidak bisa menghapus satu-satunya akun Dekan aktif di sistem.'
                ]);
            }
        }

        $totalData = $lecturer->educations_count + $lecturer->researches_count
                   + $lecturer->community_services_count + $lecturer->publications_count;

        if ($totalData > 0) {
            return back()->withErrors([
                'dosen' => 'Dosen ini sudah memiliki data Tridharma, tidak bisa dihapus permanen. Gunakan tombol Nonaktifkan.'
            ]);
        }

        $user = $lecturer->user;
        $lecturer->delete();
        $user?->delete();

        return back()->with('success', 'Akun dosen berhasil dihapus permanen.');
    }

    public function toggleVisibility($id)
    {
        $this->assertDekanOnly();
        $lecturer = Lecturer::with('user')->findOrFail($id);
        $lecturer->update(['is_public' => !$lecturer->is_public]);
        $status = $lecturer->is_public ? 'publik' : 'internal';
        return back()->with('success', "Profil dosen sekarang $status.");
    }

    // ─── EDIT PROFIL DOSEN (Dekan semua, Kaprodi hanya prodinya) ────
    public function editProfilDosen($id)
    {
        $lecturer = Lecturer::with('user', 'studyProgram')->findOrFail($id);
        $this->assertKaprodiCanEdit($lecturer);
        return view('admin.profil-edit', compact('lecturer'));
    }

    public function updateProfilDosen(Request $request, $id)
    {
        $lecturer = Lecturer::with('user')->findOrFail($id);
        $this->assertKaprodiCanEdit($lecturer);
        $lecturer->update($request->only(['nidn', 'jabatan_fungsional', 'expertise', 'is_public']));
        return back()->with('success', 'Profil dosen berhasil diperbarui.');
    }

    // ─── HIERARKI (Dekan only) ───────────────────────────────────────
    public function hierarki()
    {
        $this->assertDekanOnly();
        $studyPrograms = StudyProgram::with(['headLecturer.user', 'lecturers.user'])->get();
        $lecturers     = Lecturer::with('user')->get();
        return view('admin.hierarki', compact('studyPrograms', 'lecturers'));
    }

    public function updateHierarki(Request $request, $id)
    {
        $this->assertDekanOnly();
        $prodi = StudyProgram::findOrFail($id);

        $request->validate([
            'head_lecturer_id' => 'nullable|exists:lecturers,id',
        ]);

        $newHeadId = $request->head_lecturer_id ?: null;

        $newHead = null;
        if ($newHeadId) {
            $newHead = Lecturer::with('user')->findOrFail($newHeadId);

            // Kaprodi WAJIB dosen dari prodi ini sendiri — mencegah salah pilih
            // dosen prodi lain lewat dropdown (dropdown sudah difilter di view,
            // ini lapisan kedua di backend).
            if ($newHead->study_program_id !== $prodi->id) {
                return back()->withErrors([
                    'head_lecturer_id' => 'Kaprodi harus dipilih dari dosen yang berada di program studi ini.'
                ]);
            }
            if ($newHead->user->role === 'dekan') {
                return back()->withErrors([
                    'head_lecturer_id' => 'Dekan tidak dapat merangkap sebagai Kaprodi.'
                ]);
            }
            if (!$newHead->user->is_active) {
                return back()->withErrors([
                    'head_lecturer_id' => 'Tidak bisa menjadikan dosen nonaktif sebagai Kaprodi.'
                ]);
            }
        }

        // ── PENTING: sinkronkan dengan kolom users.role ──
        // Sebelumnya head_lecturer_id hanya label tampilan dan TIDAK memberi
        // akses Kaprodi sungguhan (semua pengecekan RBAC di sistem membaca
        // users.role, bukan kolom ini). Sekarang keduanya disatukan supaya
        // memilih Kaprodi di halaman ini benar-benar memberi akses.
        //
        // Turunkan SEMUA dosen ber-role kaprodi lain di prodi yang sama
        // (bukan cuma yang sebelumnya jadi head_lecturer_id) — soalnya role
        // kaprodi bisa juga diset terpisah lewat halaman Kelola Dosen, jadi
        // perlu dijamin tidak ada dua Kaprodi nyangkut bersamaan di satu prodi.
        Lecturer::where('study_program_id', $prodi->id)
            ->whereHas('user', fn($q) => $q->where('role', 'kaprodi'))
            ->when($newHeadId, fn($q) => $q->where('id', '!=', $newHeadId))
            ->get()
            ->each(fn($lec) => $lec->user->update(['role' => 'dosen']));

        if ($newHead && $newHead->user->role !== 'kaprodi') {
            $newHead->user->update(['role' => 'kaprodi']);
        }

        $prodi->update(['head_lecturer_id' => $newHeadId]);

        return back()->with('success', 'Kaprodi berhasil diperbarui.');
    }

    // ─── DATA INTERNAL (Dekan + Kaprodi) ────────────────────────────
    // Tampilkan semua dosen, tapi tombol Edit hanya untuk yang di scope masing-masing
    public function internal(Request $request)
    {
        $myProdiId = null;

        $query = Lecturer::with(['user', 'studyProgram',
            'educations', 'researches', 'communityServices', 'publications'
        ]);

        // FIX: Kaprodi hanya boleh melihat data dosen di prodinya sendiri.
        // Sebelumnya scoping hanya diterapkan di Blade (tombol Edit), sehingga
        // Kaprodi tetap bisa melihat data internal dosen prodi lain. Sekarang
        // scoping diterapkan di level query agar konsisten dengan RBAC.
        if (auth()->user()->isKaprodi()) {
            $myProdiId = auth()->user()->lecturer?->study_program_id;
            $query->where('study_program_id', $myProdiId);
        }

        // Pencarian nama/kepakaran — diterapkan SETELAH scoping prodi di atas,
        // dan dibungkus dalam satu closure supaya kondisi OR tidak bocor keluar
        // dari batas prodi Kaprodi.
        $query->when($request->search, function ($q) use ($request) {
            $q->where(function ($sub) use ($request) {
                $sub->whereHas('user', fn($u) => $u->where('name', 'like', '%'.$request->search.'%'))
                    ->orWhere('expertise', 'like', '%'.$request->search.'%');
            });
        });

        $lecturers = $query->get();

        return view('admin.internal', compact('lecturers', 'myProdiId'));
    }

    // ─── KELOLA PRODI (Dekan only) ───────────────────────────────────
    public function prodiList()
    {
        $this->assertDekanOnly();
        $studyPrograms = StudyProgram::withCount('lecturers')->get();
        return view('admin.prodi', compact('studyPrograms'));
    }

    public function storeProdi(Request $request)
    {
        $this->assertDekanOnly();
        $request->validate(['name' => 'required|string|max:255|unique:study_programs']);
        StudyProgram::create(['name' => $request->name]);
        return back()->with('success', 'Program studi berhasil ditambahkan.');
    }

    public function updateProdi(Request $request, $id)
    {
        $this->assertDekanOnly();
        $request->validate(['name' => 'required|string|max:255|unique:study_programs,name,'.$id]);
        StudyProgram::findOrFail($id)->update(['name' => $request->name]);
        return back()->with('success', 'Program studi berhasil diperbarui.');
    }

    public function destroyProdi($id)
    {
        $this->assertDekanOnly();
        $prodi = StudyProgram::withCount('lecturers')->findOrFail($id);
        if ($prodi->lecturers_count > 0) {
            return back()->withErrors(['prodi' => 'Tidak bisa hapus prodi yang masih memiliki dosen.']);
        }
        $prodi->delete();
        return back()->with('success', 'Program studi berhasil dihapus.');
    }
}
