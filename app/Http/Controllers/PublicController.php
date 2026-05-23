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
                  ->orWhere('expertise', 'like', '%'.$request->search.'%')
                  ->orWhere('jabatan_fungsional', 'like', '%'.$request->search.'%');
            })
            ->when($request->prodi, fn($q) => $q->where('study_program_id', $request->prodi))
            ->get();

        return view('public.home', compact('lecturers', 'studyPrograms'));
    }

    public function show(Request $request, $id)
    {
        $lecturer = Lecturer::with([
            'user', 'studyProgram',
            'educations'        => fn($q) => $q->where('visibility', 'public')->orderByDesc('year'),
            'researches'        => fn($q) => $q->where('visibility', 'public')->orderByDesc('year'),
            'communityServices' => fn($q) => $q->where('visibility', 'public')->orderByDesc('year'),
            'publications'      => fn($q) => $q->where('visibility', 'public')->orderByDesc('year'),
        ])->where('is_public', true)->findOrFail($id);

        // ── Tahun filter publikasi ────────────────────────────────
        $filterYear  = $request->year;
        $pubYears    = $lecturer->publications->pluck('year')->unique()->sortDesc()->values();
        $publications = $filterYear
            ? $lecturer->publications->where('year', $filterYear)
            : $lecturer->publications;

        // ── Data chart performa (5 tahun terakhir) ────────────────
        $currentYear = (int) date('Y');
        $years       = range($currentYear - 4, $currentYear);

        $researchData = collect($years)->map(
            fn($y) => $lecturer->researches->where('year', $y)->count()
        )->values()->toArray();

        $serviceData = collect($years)->map(
            fn($y) => $lecturer->communityServices->where('year', $y)->count()
        )->values()->toArray();

        $pubData = collect($years)->map(
            fn($y) => $lecturer->publications->where('year', $y)->count()
        )->values()->toArray();

        return view('public.show', compact(
            'lecturer',
            'filterYear', 'pubYears', 'publications',
            'years', 'researchData', 'serviceData', 'pubData'
        ));
    }
}
