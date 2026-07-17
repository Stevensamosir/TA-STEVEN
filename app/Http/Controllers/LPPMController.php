<?php

namespace App\Http\Controllers;

use App\Models\Lecturer;
use App\Models\StudyProgram;
use App\Models\Research;
use App\Models\CommunityService;
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

    /**
     * Daftar Dosen (khusus LPPM) -- menampilkan SELURUH dosen aktif Fakultas
     * Vokasi (scope fakultas, tidak per prodi). Tidak difilter is_public karena
     * LPPM adalah role internal yang berhak melihat seluruh data internal dosen.
     * Pencarian pakai field yang sama dengan searchDosen(): nama ATAU NIDN.
     */
    public function daftarDosen(Request $request)
    {
        $query = Lecturer::with(['user', 'studyProgram'])
            ->whereHas('user', fn($u) => $u->where('is_active', true));

        $query->when($request->filled('search'), function ($q) use ($request) {
            $term = trim($request->search);
            $q->where(function ($sub) use ($term) {
                $sub->where('nidn', 'like', "%{$term}%")
                    ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$term}%"));
            });
        });

        $query->when($request->filled('prodi'), fn($q) => $q->where('study_program_id', $request->prodi));

        $lecturers     = $query->orderBy('study_program_id')->get();
        $studyPrograms = StudyProgram::orderBy('name')->get();

        return view('lppm.dosen', compact('lecturers', 'studyPrograms'));
    }

    /**
     * Detail Tridharma seorang dosen dari sisi LPPM. Reuse view/partial yang
     * sama dengan Data Internal (Kaprodi/Dekan). Semua data ditampilkan
     * (termasuk yang Privat), karena ini konsumsi internal.
     */
    public function dosenShow($id)
    {
        $lecturer = Lecturer::with([
            'user', 'studyProgram',
            'educations', 'researches', 'publications', 'books', 'hkis', 'awards',
            'communityServices.lecturers.user',
        ])->whereHas('user', fn($u) => $u->where('is_active', true))
          ->findOrFail($id);

        return view('lppm.dosen-show', compact('lecturer'));
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
        ]);

        $lecturer = $this->findLecturerById((int) $request->lecturer_id);

        if (!$lecturer) {
            return back()
                ->withErrors(['lecturer_id' => 'Dosen tidak ditemukan. Silakan cari ulang.'])
                ->withInput();
        }

        $lecturer->researches()->create(array_merge(
            $request->only(['title', 'year', 'month', 'funding_source']),
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
        ]);

        $lecturer = $this->findLecturerById((int) $request->lecturer_id);

        if (!$lecturer) {
            return back()
                ->withErrors(['lecturer_id' => 'Dosen tidak ditemukan. Silakan cari ulang.'])
                ->withInput();
        }

        $lecturer->communityServices()->create(
            array_merge(
                $request->only(['title', 'year', 'month', 'location', 'pkm_type', 'pkm_scheme', 'student_members']),
                ['input_by_lppm_id' => auth()->id()]
            ),
            ['role' => $request->role]
        );

        return back()->with('success', 'Data PKM untuk ' . $lecturer->user->name . ' berhasil ditambahkan.');
    }

    /**
     * Pastikan Lecturer masih anggota aktif Fakultas Vokasi. Semua dosen di
     * sistem ini adalah Fakultas Vokasi, jadi "kepemilikan" = data terhubung
     * ke dosen yang benar-benar ada & aktif. Kalau tidak, tolak (403) supaya
     * LPPM tidak bisa mengedit data yang menggantung / di luar cakupan.
     */
    private function assertLecturerInFaculty(?Lecturer $lecturer): void
    {
        if (!$lecturer || !$lecturer->user || !$lecturer->user->is_active) {
            abort(403, 'Data ini tidak dapat diedit: dosen di luar cakupan Fakultas Vokasi atau nonaktif.');
        }
    }

    /**
     * Update data Penelitian (dari halaman detail dosen LPPM).
     * Penelitian hasMany milik satu Lecturer -- validasi kepemilikan lewat
     * relasi lecturer-nya.
     */
    public function updatePenelitian(Request $request, $id)
    {
        $research = Research::with('lecturer.user')->findOrFail($id);
        $this->assertLecturerInFaculty($research->lecturer);

        $request->validate([
            'title'          => 'required|string|max:500',
            'year'           => 'required|integer|min:1970|max:' . date('Y'),
            'month'          => 'required|integer|min:1|max:12',
            'funding_source' => 'nullable|string|max:255',
        ]);

        $research->update($request->only(['title', 'year', 'month', 'funding_source']));

        return back()->with('success', 'Data penelitian berhasil diperbarui.');
    }

    /**
     * Update data PKM/Pengabdian (dari halaman detail dosen LPPM).
     * PKM many-to-many: field utama dibagikan ke semua anggota tim, sedangkan
     * "role" (Ketua/Anggota) spesifik per dosen (kolom pivot). lecturer_id
     * menentukan pivot mana yang diperbarui, sekaligus dipakai untuk validasi
     * kepemilikan (dosen harus benar-benar anggota kegiatan ini & aktif).
     */
    public function updatePkm(Request $request, $id)
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
        ]);

        $pkm = CommunityService::with('lecturers.user')->findOrFail($id);

        // Dosen yang diedit harus benar-benar anggota kegiatan PKM ini.
        $lecturer = $pkm->lecturers->firstWhere('id', (int) $request->lecturer_id);
        if (!$lecturer) {
            abort(403, 'Dosen tidak terhubung dengan data PKM ini.');
        }
        $this->assertLecturerInFaculty($lecturer);

        $pkm->update($request->only([
            'title', 'year', 'month', 'location', 'pkm_type', 'pkm_scheme', 'student_members',
        ]));

        // Perbarui peran dosen ini pada kegiatan (kolom pivot).
        $pkm->lecturers()->updateExistingPivot((int) $request->lecturer_id, ['role' => $request->role]);

        return back()->with('success', 'Data PKM berhasil diperbarui.');
    }
}
