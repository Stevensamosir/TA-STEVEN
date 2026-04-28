<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Lecturer;
use App\Models\StudyProgram;

class AdminController extends Controller
{
    public function index()
    {
        $stats = [
            'total_dosen'   => Lecturer::count(),
            'total_publik'  => Lecturer::where('is_public', true)->count(),
            'total_prodi'   => StudyProgram::count(),
            'total_publikasi' => \App\Models\Publication::count(),
        ];
        $recentDosen = Lecturer::with('user', 'studyProgram')->latest()->take(5)->get();
        return view('admin.index', compact('stats', 'recentDosen'));
    }

    public function dosenList()
    {
        $dosens = Lecturer::with(['user', 'studyProgram'])->get();
        return view('admin.dosen', compact('dosens'));
    }

    public function createDosen()
    {
        $studyPrograms = StudyProgram::all();
        return view('admin.dosen-create', compact('studyPrograms'));
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
        $lecturer = Lecturer::with('user', 'studyProgram')->findOrFail($id);
        $studyPrograms = StudyProgram::all();
        return view('admin.dosen-edit', compact('lecturer', 'studyPrograms'));
    }

    public function updateDosen(Request $request, $id)
    {
        $lecturer = Lecturer::with('user')->findOrFail($id);
        $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|unique:users,email,' . $lecturer->user->id,
            'role'             => 'required|in:dosen,kaprodi,dekan',
            'study_program_id' => 'required|exists:study_programs,id',
            'nidn'             => 'nullable|string|max:20',
            'expertise'        => 'nullable|string',
        ]);

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
        $newPassword = 'password123';
        $lecturer->user->update(['password' => Hash::make($newPassword)]);
        return back()->with('success', "Password berhasil direset ke: $newPassword");
    }

    public function toggleActive($id)
    {
        $lecturer = Lecturer::with('user')->findOrFail($id);
        $lecturer->user->update(['is_active' => !$lecturer->user->is_active]);
        $status = $lecturer->user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Akun dosen berhasil $status.");
    }

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
        return back()->with('success', "Profil dosen sekarang bersifat $status.");
    }

    public function hierarki()
    {
        $studyPrograms = StudyProgram::with(['headLecturer.user', 'lecturers.user'])->get();
        $lecturers = Lecturer::with('user')->get();
        return view('admin.hierarki', compact('studyPrograms', 'lecturers'));
    }

    public function updateHierarki(Request $request, $id)
    {
        $prodi = StudyProgram::findOrFail($id);
        $prodi->update(['head_lecturer_id' => $request->head_lecturer_id]);
        return back()->with('success', 'Kaprodi berhasil diperbarui.');
    }

    public function internal()
    {
        $lecturers = Lecturer::with(['user', 'studyProgram',
            'educations', 'researches', 'communityServices', 'publications'
        ])->get();
        return view('admin.internal', compact('lecturers'));
    }
}
