<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lecturer;
use App\Models\StudyProgram;

class PublicController extends Controller
{
    public function index(Request $request)
    {
        $studyPrograms = StudyProgram::all();

        $lecturers = Lecturer::with(['user', 'studyProgram'])
            ->where('is_public', true)
            ->when($request->search, function ($q) use ($request) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', '%'.$request->search.'%'))
                  ->orWhere('expertise', 'like', '%'.$request->search.'%');
            })
            ->when($request->prodi, fn($q) => $q->where('study_program_id', $request->prodi))
            ->get();

        return view('public.home', compact('lecturers', 'studyPrograms'));
    }

    public function show($id)
    {
        $lecturer = Lecturer::with([
            'user', 'studyProgram',
            'educations' => fn($q) => $q->where('visibility', 'public'),
            'researches' => fn($q) => $q->where('visibility', 'public'),
            'communityServices' => fn($q) => $q->where('visibility', 'public'),
            'publications' => fn($q) => $q->where('visibility', 'public'),
        ])->where('is_public', true)->findOrFail($id);

        return view('public.dosen-detail', compact('lecturer'));
    }
}
