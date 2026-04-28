<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lecturer;
use App\Models\StudyProgram;

class PublicController extends Controller
{
    public function index(Request $request)
    {
        $query = Lecturer::with(['user', 'studyProgram'])
            ->where('is_public', true);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%$search%"))
                  ->orWhere('expertise', 'like', "%$search%")
                  ->orWhere('nidn', 'like', "%$search%");
        }

        if ($request->filled('prodi')) {
            $query->where('study_program_id', $request->prodi);
        }

        $lecturers = $query->get();
        $studyPrograms = StudyProgram::all();

        return view('public.index', compact('lecturers', 'studyPrograms'));
    }

    public function show($id)
    {
        $lecturer = Lecturer::with([
            'user',
            'studyProgram',
            'educations' => fn($q) => $q->where('visibility', 'public')->orderBy('year', 'desc'),
            'researches' => fn($q) => $q->where('visibility', 'public')->orderBy('year', 'desc'),
            'communityServices' => fn($q) => $q->where('visibility', 'public')->orderBy('year', 'desc'),
            'publications' => fn($q) => $q->where('visibility', 'public')->orderBy('year', 'desc'),
        ])->where('is_public', true)->findOrFail($id);

        // Data grafik performa (5 tahun terakhir)
        $years = range(date('Y') - 4, date('Y'));
        $researchData = [];
        $serviceData  = [];
        $pubData      = [];

        foreach ($years as $year) {
            $researchData[] = $lecturer->researches->where('year', $year)->count();
            $serviceData[]  = $lecturer->communityServices->where('year', $year)->count();
            $pubData[]      = $lecturer->publications->where('year', $year)->count();
        }

        // Filter publikasi berdasarkan tahun
        $filterYear   = request('year');
        $publications = $lecturer->publications;
        if ($filterYear) {
            $publications = $publications->where('year', $filterYear);
        }
        $pubYears = $lecturer->publications->pluck('year')->unique()->sortDesc();

        return view('public.show', compact(
            'lecturer', 'years', 'researchData', 'serviceData', 'pubData',
            'publications', 'pubYears', 'filterYear'
        ));
    }

    public function search(Request $request)
    {
        return $this->index($request);
    }
}
