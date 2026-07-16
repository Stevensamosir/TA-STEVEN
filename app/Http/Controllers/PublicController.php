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
            ->whereHas('user', fn($u) => $u->where('is_active', true))
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->whereHas('user', fn($u) => $u->where('name', 'like', '%'.$request->search.'%'))
                        ->orWhere('expertise', 'like', '%'.$request->search.'%')
                        ->orWhere('jabatan_fungsional', 'like', '%'.$request->search.'%')
                        ->orWhere('alias', 'like', '%'.$request->search.'%')
                        ->orWhereHas('studyProgram', fn($sp) => $sp->where('name', 'like', '%'.$request->search.'%'));
                });
            })
            ->when($request->prodi, fn($q) => $q->where('study_program_id', $request->prodi))
            ->get();

        return view('public.home', compact('lecturers', 'studyPrograms'));
    }

    public function show(Request $request, $id)
    {
        // FIX BUG PRIVASI: semua eager load SEKARANG difilter visibility=public,
        // supaya data yang ditandai Privat oleh dosen TIDAK bocor ke pengunjung
        // tanpa login. Sebelumnya tidak difilter sama sekali.
        $lecturer = Lecturer::with([
            'user', 'studyProgram',
            'educations'        => fn($q) => $q->where('visibility', 'public')->orderByDesc('year'),
            'researches'        => fn($q) => $q->where('visibility', 'public')->orderByDesc('year'),
            'communityServices' => fn($q) => $q->where('community_services.visibility', 'public')->orderByDesc('year'),
            'publications'      => fn($q) => $q->where('visibility', 'public')->orderByDesc('year'),
            'books'             => fn($q) => $q->where('visibility', 'public')->orderByDesc('year'),
            'hkis'              => fn($q) => $q->where('visibility', 'public')->orderByDesc('year'),
            'awards'            => fn($q) => $q->where('visibility', 'public')->orderByDesc('date'),
        ])->where('is_public', true)->findOrFail($id);

        $filterYear  = $request->year;
        $pubYears    = $lecturer->publications->pluck('year')->unique()->sortDesc()->values();
        $publications = $filterYear
            ? $lecturer->publications->where('year', $filterYear)
            : $lecturer->publications;

        $currentYear = (int) date('Y');
        $years5      = range($currentYear - 4, $currentYear);

        $allActivityYears = collect()
            ->merge($lecturer->researches->pluck('year'))
            ->merge($lecturer->communityServices->pluck('year'))
            ->merge($lecturer->publications->pluck('year'))
            ->filter()->unique()->sort()->values();

        $yearsAll = $allActivityYears->isNotEmpty()
            ? range($allActivityYears->first(), max($allActivityYears->last(), $currentYear))
            : $years5;

        $buildSeries = function ($years) use ($lecturer) {
            return [
                'years'    => collect($years)->map(fn($y) => (string) $y)->toArray(),
                'research' => collect($years)->map(fn($y) => $lecturer->researches->where('year', $y)->count())->toArray(),
                'service'  => collect($years)->map(fn($y) => $lecturer->communityServices->where('year', $y)->count())->toArray(),
                'pub'      => collect($years)->map(fn($y) => $lecturer->publications->where('year', $y)->count())->toArray(),
            ];
        };

        $chart5    = $buildSeries($years5);
        $chartAll  = $buildSeries($yearsAll);

        $years        = $years5;
        $researchData = $chart5['research'];
        $serviceData  = $chart5['service'];
        $pubData      = $chart5['pub'];

        return view('public.show', compact(
            'lecturer',
            'filterYear', 'pubYears', 'publications',
            'years', 'researchData', 'serviceData', 'pubData',
            'chart5', 'chartAll'
        ));
    }
}
