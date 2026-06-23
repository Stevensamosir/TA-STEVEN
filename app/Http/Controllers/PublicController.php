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
                // PENTING: seluruh kondisi OR di-bungkus dalam satu closure
                // (where(function...)) supaya tidak "bocor" keluar dari syarat
                // is_public=true & filter prodi. Tanpa pembungkus ini, Laravel
                // akan menempatkan orWhere() sejajar dengan where('is_public',
                // true) di level query utama — yang secara presedensi SQL
                // berarti dosen NON-PUBLIK (internal-only) bisa ikut nongol di
                // hasil pencarian publik kalau expertise/jabatannya kebetulan
                // cocok dengan kata kunci. Filter prodi juga jadi tidak
                // berfungsi saat dipakai bersamaan dengan pencarian.
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
        $lecturer = Lecturer::with([
            'user', 'studyProgram',
            'educations'        => fn($q) => $q->orderByDesc('year'),
            'researches'        => fn($q) => $q->orderByDesc('year'),
            'communityServices' => fn($q) => $q->orderByDesc('year'),
            'publications'      => fn($q) => $q->orderByDesc('year'),
        ])->where('is_public', true)->findOrFail($id);

        // ── Tahun filter publikasi ────────────────────────────────
        $filterYear  = $request->year;
        $pubYears    = $lecturer->publications->pluck('year')->unique()->sortDesc()->values();
        $publications = $filterYear
            ? $lecturer->publications->where('year', $filterYear)
            : $lecturer->publications;

        // ── Data chart performa: dua mode, mengikuti pola SINTA ──
        // (skor "overall" sepanjang karier berdampingan dengan skor "3 tahun
        // terakhir") — di sini disederhanakan jadi 2 pilihan: 5 Tahun Terakhir
        // (default, fokus aktivitas terkini) dan Semua Tahun (akumulasi penuh,
        // supaya rekam jejak dosen senior tidak terpotong oleh window tetap).
        $currentYear = (int) date('Y');
        $years5      = range($currentYear - 4, $currentYear);

        // Tahun-tahun yang benar-benar punya data, dari ketiga kategori sekaligus
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

        // Variabel lama tetap disediakan (dipakai bagian lain / kompatibilitas)
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
