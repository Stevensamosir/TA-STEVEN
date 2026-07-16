<?php

namespace App\Http\Controllers;

use App\Models\Lecturer;
use Illuminate\Http\Request;

class LPPMController extends Controller
{
    public function index()
    {
        $riwayat = collect()
            ->merge(
                \App\Models\Research::with(['lecturer.user'])
                    ->whereNotNull('input_by_lppm_id')
                    ->where('input_by_lppm_id', auth()->id())
                    ->latest()
                    ->take(15)
                    ->get()
                    ->map(fn($r) => (object) [
                        'jenis' => 'Penelitian',
                        'judul' => $r->title,
                        'nama_dosen' => $r->lecturer->user->name ?? '-',
                        'tanggal' => $r->created_at,
                    ])
            )
            ->merge(
                \App\Models\CommunityService::with(['lecturers.user'])
                    ->whereNotNull('input_by_lppm_id')
                    ->where('input_by_lppm_id', auth()->id())
                    ->latest()
                    ->take(15)
                    ->get()
                    ->map(fn($p) => (object) [
                        'jenis' => 'PKM',
                        'judul' => $p->title,
                        'nama_dosen' => $p->lecturers->pluck('user.name')->join(', ') ?: '-',
                        'tanggal' => $p->created_at,
                    ])
            )
            ->sortByDesc('tanggal')
            ->take(15)
            ->values();

        return view('lppm.index', compact('riwayat'));
    }

    /**
     * Pencarian dosen real-time (dipanggil lewat fetch/AJAX dari view).
     * Cari berdasarkan nama ATAU NIDN, kembalikan max 8 hasil dengan
     * data identitas (foto, nama, NIDN, prodi) untuk ditampilkan sebagai
     * preview sebelum staf LPPM memilih dosen yang benar.
     */
    public function searchDosen(Request $request)
    {
        $request->validate(['q' => 'required|string|min:2']);

        $query = trim($request->q);

        $lecturers = Lecturer::with(['user', 'studyProgram'])
            ->where(function ($qb) use ($query) {
                $qb->where('nidn', 'like', "%{$query}%")
                   ->orWhereHas('user', function ($uq) use ($query) {
                       $uq->where('name', 'like', "%{$query}%");
                   });
            })
            ->limit(8)
            ->get()
            ->map(function ($lecturer) {
                return [
                    'id'          => $lecturer->id,
                    'name'        => $lecturer->user->name ?? '(tanpa nama)',
                    'nidn'        => $lecturer->nidn ?? '-',
                    'prodi'       => $lecturer->studyProgram->name ?? '-',
                    'photo'       => $lecturer->photo ?? null,
                    'jabatan'     => $lecturer->jabatan_fungsional ?? '-',
                ];
            });

        return response()->json($lecturers);
    }

    private function findLecturerById(int $id): ?Lecturer
    {
        return Lecturer::with('user')->find($id);
    }

    public function storePenelitian(Request $request)
    {
        $request->validate([
            'lecturer_id'    => 'required|integer|exists:lecturers,id',
            'title'          => 'required|string|max:500',
            'year'           => 'required|integer|min:1970|max:' . date('Y'),
            'month'          => 'required|integer|min:1|max:12',
            'funding_source' => 'nullable|string|max:255',
            'visibility'     => 'required|in:public,private',
        ]);

        $lecturer = $this->findLecturerById((int) $request->lecturer_id);

        if (!$lecturer) {
            return back()
                ->withErrors(['lecturer_id' => 'Dosen tidak ditemukan. Silakan cari ulang.'])
                ->withInput();
        }

        $lecturer->researches()->create(array_merge(
            $request->only(['title', 'year', 'month', 'funding_source', 'visibility']),
            ['input_by_lppm_id' => auth()->id()]
        ));

        return back()->with('success', 'Data penelitian untuk ' . $lecturer->user->name . ' berhasil ditambahkan.');
    }

    public function storePkm(Request $request)
    {
        $request->validate([
            'lecturer_id'     => 'required|integer|exists:lecturers,id',
            'title'           => 'required|string|max:500',
            'year'            => 'required|integer|min:1970|max:' . date('Y'),
            'month'           => 'required|integer|min:1|max:12',
            'location'        => 'nullable|string|max:255',
            'pkm_type'        => 'nullable|in:Internal,Nasional,Internasional',
            'pkm_scheme'      => 'nullable|in:PKM-RE,PKM-RSH,PKM-K,PKM-PM,PKM-PI,PKM-KC,PKM-KI,PKM-VGK,PKM-AI,PKM-GFT',
            'student_members' => 'nullable|string|max:1000',
            'role'            => 'required|in:Ketua,Anggota',
            'visibility'      => 'required|in:public,private',
        ]);

        $lecturer = $this->findLecturerById((int) $request->lecturer_id);

        if (!$lecturer) {
            return back()
                ->withErrors(['lecturer_id' => 'Dosen tidak ditemukan. Silakan cari ulang.'])
                ->withInput();
        }

        $lecturer->communityServices()->create(
            array_merge(
                $request->only(['title', 'year', 'month', 'location', 'pkm_type', 'pkm_scheme', 'student_members', 'visibility']),
                ['input_by_lppm_id' => auth()->id()]
            ),
            ['role' => $request->role]
        );

        return back()->with('success', 'Data PKM untuk ' . $lecturer->user->name . ' berhasil ditambahkan.');
    }
}
