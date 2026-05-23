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
    // ─── DASHBOARD ──────────────────────────────────────────
    public function index()
    {
        $stats = [
            'total_dosen'     => Lecturer::count(),
            'total_publik'    => Lecturer::where('is_public', true)->count(),
            'total_prodi'     => StudyProgram::count(),
            'total_publikasi' => Publication::count(),
        ];
        $recentDosen = Lecturer::with('user', 'studyProgram')->latest()->take(5)->get();
        return view('admin.index', compact('stats', 'recentDosen'));
    }

    // ─── KELOLA DOSEN ────────────────────────────────────────
    public function dosenList()
    {
        $dosens = Lecturer::with(['user', 'studyProgram'])->get();
        return view('admin.dosen', compact('dosens'));
    }

    public function createDosen()
    {
        $studyPrograms = StudyProgram::all();
        $dekanExists   = User::where('role', 'dekan')->where('is_active', true)->exists();
        return view('admin.dosen-create', compact('studyPrograms', 'dekanExists'));
    }

    public function storeDosen(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|unique:users',
            'password'         => 'required|min:8',
            'role'             => 'required|in:dosen,kaprodi,dekan',
            'study_program_id' => 'required|exists:study_programs,id',
            'nidn'             => 'nullable|string|max:20',
            'expertise'        => 'nullable|string',
        ]);

        // ✅ CONSTRAINT: Dekan maksimal 1 aktif
        if ($request->role === 'dekan') {
            $exists = User::where('role', 'dekan')->where('is_active', true)->exists();
            if ($exists) {
                return back()
                    ->withErrors(['role' => 'Hanya dapat ada 1 Dekan aktif. Nonaktifkan Dekan yang ada terlebih dahulu.'])
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
            'user_id'          => $user->id,
            'study_program_id' => $request->study_program_id,
            'nidn'             => $request->nidn,
            'expertise'        => $request->expertise,
            'is_public'        => true,
        ]);

        return redirect()->route('admin.dosen')->with('success', 'Akun dosen berhasil dibuat.');
    }

    public function editDosen($id)
    {
        $lecturer      = Lecturer::with('user', 'studyProgram')->findOrFail($id);
        $studyPrograms = StudyProgram::all();
        $dekanExists   = User::where('role', 'dekan')->where('is_active', true)
                             ->where('id', '!=', $lecturer->user->id)->exists();
        return view('admin.dosen-edit', compact('lecturer', 'studyPrograms', 'dekanExists'));
    }

    public function updateDosen(Request $request, $id)
    {
        $lecturer = Lecturer::with('user')->findOrFail($id);

        $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|unique:users,email,'.$lecturer->user->id,
            'role'             => 'required|in:dosen,kaprodi,dekan',
            'study_program_id' => 'required|exists:study_programs,id',
            'nidn'             => 'nullable|string|max:20',
            'expertise'        => 'nullable|string',
        ]);

        // ✅ CONSTRAINT: Dekan maksimal 1 aktif (saat ubah role ke dekan)
        if ($request->role === 'dekan' && $lecturer->user->role !== 'dekan') {
            $exists = User::where('role', 'dekan')->where('is_active', true)
                         ->where('id', '!=', $lecturer->user->id)->exists();
            if ($exists) {
                return back()
                    ->withErrors(['role' => 'Hanya dapat ada 1 Dekan aktif. Nonaktifkan Dekan yang ada terlebih dahulu.'])
                    ->withInput();
            }
        }

        $lecturer->user->update([
            'name'  => $request->name,
            'email' => $request->email,
            'role'  => $request->role,
        ]);
        $lecturer->update([
            'study_program_id' => $request->study_program_id,
            'nidn'             => $request->nidn,
            'expertise'        => $request->expertise,
        ]);

        return redirect()->route('admin.dosen')->with('success', 'Data dosen berhasil diperbarui.');
    }

    public function resetPassword($id)
    {
        $lecturer = Lecturer::with('user')->findOrFail($id);
        $newPass  = 'password123';
        $lecturer->user->update(['password' => Hash::make($newPass)]);
        return back()->with('success', "Password direset ke: $newPass");
    }

    public function toggleActive($id)
    {
        $lecturer = Lecturer::with('user')->findOrFail($id);
        $lecturer->user->update(['is_active' => !$lecturer->user->is_active]);
        $status = $lecturer->user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Akun dosen berhasil $status.");
    }

    // ─── KELOLA PROFIL DOSEN ────────────────────────────────
    public function editProfilDosen($id)
    {
        $lecturer = Lecturer::with('user', 'studyProgram')->findOrFail($id);
        return view('admin.profil-edit', compact('lecturer'));
    }

    public function updateProfilDosen(Request $request, $id)
    {
        $lecturer = Lecturer::findOrFail($id);
        $lecturer->update($request->only(['nidn', 'expertise', 'is_public']));
        return back()->with('success', 'Profil dosen berhasil diperbarui.');
    }

    public function toggleVisibility($id)
    {
        $lecturer = Lecturer::findOrFail($id);
        $lecturer->update(['is_public' => !$lecturer->is_public]);
        $status = $lecturer->is_public ? 'publik' : 'internal';
        return back()->with('success', "Profil dosen sekarang $status.");
    }

    // ─── HIERARKI ────────────────────────────────────────────
    public function hierarki()
    {
        $studyPrograms = StudyProgram::with(['headLecturer.user', 'lecturers.user'])->get();
        $lecturers     = Lecturer::with('user')->get();
        return view('admin.hierarki', compact('studyPrograms', 'lecturers'));
    }

    public function updateHierarki(Request $request, $id)
    {
        StudyProgram::findOrFail($id)->update(['head_lecturer_id' => $request->head_lecturer_id]);
        return back()->with('success', 'Kaprodi berhasil diperbarui.');
    }

    // ─── DATA INTERNAL ────────────────────────────────────────
    public function internal()
    {
        $lecturers = Lecturer::with(['user','studyProgram',
            'educations','researches','communityServices','publications'
        ])->get();
        return view('admin.internal', compact('lecturers'));
    }
    // ─── KELOLA PRODI ────────────────────────────────────────
public function prodiList()
{
    $studyPrograms = StudyProgram::withCount('lecturers')->get();
    return view('admin.prodi', compact('studyPrograms'));
}

public function storeProdi(Request $request)
{
    $request->validate(['name' => 'required|string|max:255|unique:study_programs']);
    StudyProgram::create(['name' => $request->name]);
    return back()->with('success', 'Program studi berhasil ditambahkan.');
}

public function updateProdi(Request $request, $id)
{
    $request->validate(['name' => 'required|string|max:255|unique:study_programs,name,'.$id]);
    StudyProgram::findOrFail($id)->update(['name' => $request->name]);
    return back()->with('success', 'Program studi berhasil diperbarui.');
}

public function destroyProdi($id)
{
    $prodi = StudyProgram::withCount('lecturers')->findOrFail($id);
    if ($prodi->lecturers_count > 0) {
        return back()->withErrors(['prodi' => 'Tidak bisa hapus prodi yang masih memiliki dosen.']);
    }
    $prodi->delete();
    return back()->with('success', 'Program studi berhasil dihapus.');
}
}
