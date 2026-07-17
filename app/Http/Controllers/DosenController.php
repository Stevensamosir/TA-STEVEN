<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Lecturer;
use App\Models\Education;
use App\Models\Research;
use App\Models\CommunityService;
use App\Models\Publication;
use App\Models\Book;
use App\Models\Hki;
use App\Models\Award;

class DosenController extends Controller
{
    private function getLecturer()
    {
        return auth()->user()->lecturer;
    }

    // ─── DASHBOARD ──────────────────────────────────────────
    public function index()
    {
        $lecturer = $this->getLecturer()->load([
            'studyProgram','educations','researches','communityServices','publications'
        ]);

        $stats = [
            'pendidikan' => $lecturer->educations->count(),
            'penelitian' => $lecturer->researches->count(),
            'pengabdian' => $lecturer->communityServices->count(),
            'publikasi'  => $lecturer->publications->count(),
        ];

        // ── Data chart dashboard: dua mode, sama seperti grafik publik ──
        // (5 Tahun Terakhir / Semua Tahun) supaya riwayat dosen senior tidak
        // ikut terpotong window tetap.
        $currentYear = (int) date('Y');
        $chartYears5 = range($currentYear - 4, $currentYear);

        $allActivityYears = collect()
            ->merge($lecturer->researches->pluck('year'))
            ->merge($lecturer->communityServices->pluck('year'))
            ->merge($lecturer->publications->pluck('year'))
            ->filter()->unique()->sort()->values();

        $chartYearsAll = $allActivityYears->isNotEmpty()
            ? range($allActivityYears->first(), max($allActivityYears->last(), $currentYear))
            : $chartYears5;

        $buildChart = function ($years) use ($lecturer) {
            return [
                'years'      => array_map('strval', $years),
                'penelitian' => collect($years)->map(fn($y) => $lecturer->researches->where('year', $y)->count())->values()->toArray(),
                'pengabdian' => collect($years)->map(fn($y) => $lecturer->communityServices->where('year', $y)->count())->values()->toArray(),
                'publikasi'  => collect($years)->map(fn($y) => $lecturer->publications->where('year', $y)->count())->values()->toArray(),
            ];
        };

        $chartYears10 = range($currentYear - 9, $currentYear);
        $chartData5   = $buildChart($chartYears5);
        $chartData10  = $buildChart($chartYears10);
        $chartDataAll = $buildChart($chartYearsAll);
        $chartData    = $chartData5; // tetap disediakan untuk kompatibilitas

        // ── Aktivitas terbaru (5 item) ──
        $recentActivities = collect()
            ->merge($lecturer->researches->map(fn($r) => [
                'type' => 'Penelitian', 'title' => $r->title, 'year' => $r->year, 'color' => 'blue'
            ]))
            ->merge($lecturer->communityServices->map(fn($s) => [
                'type' => 'Pengabdian', 'title' => $s->title, 'year' => $s->year, 'color' => 'emerald'
            ]))
            ->merge($lecturer->publications->map(fn($p) => [
                'type' => 'Publikasi', 'title' => $p->title, 'year' => $p->year, 'color' => 'violet'
            ]))
            ->sortByDesc('year')
            ->take(5)
            ->values();

        return view('dosen.index', compact('lecturer', 'stats', 'chartData', 'chartData5', 'chartData10', 'chartDataAll', 'recentActivities'));
    }

    // ─── PROFIL ──────────────────────────────────────────────
    public function editProfil()
    {
        $lecturer = $this->getLecturer()->load('studyProgram');

        return view('dosen.profil', compact('lecturer'));
    }

    public function updateProfil(Request $request)
    {
        $lecturer = $this->getLecturer();
        $request->validate([
            'nidn'               => 'nullable|string|max:20',
            'jabatan_fungsional' => 'nullable|string|in:Asisten Ahli,Lektor,Lektor Kepala,Guru Besar / Profesor',
            'expertise'          => 'nullable|string|max:500',
            'photo'              => 'nullable|image|max:2048',
            'email'              => 'nullable|email|max:255|unique:users,email,' . auth()->id(),
        ]);

        $data = $request->only(['nidn', 'jabatan_fungsional', 'expertise']);

        if ($request->hasFile('photo')) {
            if ($lecturer->photo) Storage::disk('public')->delete($lecturer->photo);
            $data['photo'] = $request->file('photo')->store('photos', 'public');
        }

        $lecturer->update($data);

        // Update nama user jika berubah
        if ($request->filled('name') && $request->name !== auth()->user()->name) {
            auth()->user()->update(['name' => $request->name]);
        }

        // Update email user jika berubah
        if ($request->filled('email') && $request->email !== auth()->user()->email) {
            auth()->user()->update(['email' => $request->email]);
        }

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    // ─── PENDIDIKAN ──────────────────────────────────────────
    public function pendidikan()
    {
        $lecturer   = $this->getLecturer();
        $educations = $lecturer->educations()->orderByDesc('year')->get();
        return view('dosen.pendidikan', compact('lecturer', 'educations'));
    }

    public function storePendidikan(Request $request)
    {
        $request->validate([
            'degree'      => 'required|string|max:100',
            'institution' => 'required|string|max:255',
            'year'        => 'required|integer|min:1970|max:'.date('Y'),
            'visibility'  => 'required|in:public,private',
        ]);
        $this->getLecturer()->educations()->create($request->only(['degree','institution','year','visibility']));
        return back()->with('success', 'Data pendidikan berhasil ditambahkan.');
    }

    public function updatePendidikan(Request $request, $id)
    {
        $request->validate([
            'degree'      => 'required|string|max:100',
            'institution' => 'required|string|max:255',
            'year'        => 'required|integer|min:1970|max:'.date('Y'),
            'visibility'  => 'required|in:public,private',
        ]);
        Education::where('lecturer_id', $this->getLecturer()->id)->findOrFail($id)
            ->update($request->only(['degree','institution','year','visibility']));
        return back()->with('success', 'Data pendidikan berhasil diperbarui.');
    }

    public function destroyPendidikan($id)
    {
        Education::where('lecturer_id', $this->getLecturer()->id)->findOrFail($id)->delete();
        return back()->with('success', 'Data pendidikan berhasil dihapus.');
    }

    public function togglePendidikanVisibility($id)
    {
        $edu = Education::where('lecturer_id', $this->getLecturer()->id)->findOrFail($id);
        $edu->update(['visibility' => $edu->visibility === 'public' ? 'private' : 'public']);
        return back()->with('success', 'Visibilitas berhasil diperbarui.');
    }

    // ─── PENELITIAN ──────────────────────────────────────────
    public function penelitian()
    {
        $lecturer    = $this->getLecturer();
        // ✅ FIX: $penelitians (bukan $researches)
        $penelitians = $lecturer->researches()->orderByDesc('year')->get();
        return view('dosen.penelitian', compact('lecturer', 'penelitians'));
    }

    public function storePenelitian(Request $request)
    {
        $request->validate([
            'title'          => 'required|string|max:500',
            'year'           => 'required|integer|min:1970|max:'.date('Y'),
            'month'          => 'required|integer|min:1|max:12',
            'funding_source' => 'nullable|string|max:255',
            'visibility'     => 'required|in:public,private',
        ]);
        $this->getLecturer()->researches()->create($request->only(['title','year','month','funding_source','visibility']));
        return back()->with('success', 'Data penelitian berhasil ditambahkan.');
    }

    public function updatePenelitian(Request $request, $id)
    {
        $request->validate([
            'title'          => 'required|string|max:500',
            'year'           => 'required|integer|min:1970|max:'.date('Y'),
            'month'          => 'required|integer|min:1|max:12',
            'funding_source' => 'nullable|string|max:255',
            'visibility'     => 'required|in:public,private',
        ]);
        Research::where('lecturer_id', $this->getLecturer()->id)->findOrFail($id)
            ->update($request->only(['title','year','month','funding_source','visibility']));
        return back()->with('success', 'Data penelitian berhasil diperbarui.');
    }

    public function destroyPenelitian($id)
    {
        Research::where('lecturer_id', $this->getLecturer()->id)->findOrFail($id)->delete();
        return back()->with('success', 'Data penelitian berhasil dihapus.');
    }

    public function togglePenelitianVisibility($id)
    {
        $r = Research::where('lecturer_id', $this->getLecturer()->id)->findOrFail($id);
        $r->update(['visibility' => $r->visibility === 'public' ? 'private' : 'public']);
        return back()->with('success', 'Visibilitas berhasil diperbarui.');
    }

    // ─── PENGABDIAN ──────────────────────────────────────────
    public function pengabdian()
    {
        $lecturer    = $this->getLecturer();
        $pengabdians = $lecturer->communityServices()->orderByDesc('year')->get();
        return view('dosen.pengabdian', compact('lecturer', 'pengabdians'));
    }

    public function storePengabdian(Request $request)
    {
        $request->validate([
            'title'           => 'required|string|max:500',
            'year'            => 'required|integer|min:1970|max:'.date('Y'),
            'month'           => 'required|integer|min:1|max:12',
            'location'        => 'nullable|string|max:255',
            'pkm_type'        => 'nullable|in:Internal,Nasional,Internasional',
            'pkm_scheme'      => 'nullable|in:PKM-RE,PKM-RSH,PKM-K,PKM-PM,PKM-PI,PKM-KC,PKM-KI,PKM-VGK,PKM-AI,PKM-GFT',
            'student_members' => 'nullable|string|max:1000',
            'visibility'      => 'required|in:public,private',
        ]);
        $this->getLecturer()->communityServices()->create(
            $request->only(['title','year','month','location','pkm_type','pkm_scheme','student_members','visibility']),
            ['role' => 'Ketua']
        );
        return back()->with('success', 'Data pengabdian berhasil ditambahkan.');
    }

    public function updatePengabdian(Request $request, $id)
    {
        $request->validate([
            'title'           => 'required|string|max:500',
            'year'            => 'required|integer|min:1970|max:'.date('Y'),
            'month'           => 'required|integer|min:1|max:12',
            'location'        => 'nullable|string|max:255',
            'pkm_type'        => 'nullable|in:Internal,Nasional,Internasional',
            'pkm_scheme'      => 'nullable|in:PKM-RE,PKM-RSH,PKM-K,PKM-PM,PKM-PI,PKM-KC,PKM-KI,PKM-VGK,PKM-AI,PKM-GFT',
            'student_members' => 'nullable|string|max:1000',
            'visibility'      => 'required|in:public,private',
        ]);
        $item = $this->getLecturer()->communityServices()->where('community_services.id', $id)->firstOrFail();
        $item->update($request->only(['title','year','month','location','pkm_type','pkm_scheme','student_members','visibility']));
        return back()->with('success', 'Data pengabdian berhasil diperbarui.');
    }

    public function destroyPengabdian($id)
    {
        $lecturer = $this->getLecturer();
        $item = $lecturer->communityServices()->where('community_services.id', $id)->firstOrFail();
        $lecturer->communityServices()->detach($item->id);

        // Kalau sudah tidak ada dosen lain yang terhubung ke PKM ini, hapus permanen
        if ($item->lecturers()->count() === 0) {
            $item->delete();
        }

        return back()->with('success', 'Data pengabdian berhasil dihapus.');
    }

    public function togglePengabdianVisibility($id)
    {
        $item = $this->getLecturer()->communityServices()->where('community_services.id', $id)->firstOrFail();
        $item->update(['visibility' => $item->visibility === 'public' ? 'private' : 'public']);
        return back()->with('success', 'Visibilitas berhasil diperbarui.');
    }

    // ─── PUBLIKASI ───────────────────────────────────────────
    public function publikasi()
    {
        $lecturer   = $this->getLecturer();
        // ✅ FIX: $publikasis (bukan $publikasi)
        $publikasis = $lecturer->publications()->orderByDesc('year')->get();
        return view('dosen.publikasi', compact('lecturer', 'publikasis'));
    }

    public function storePublikasi(Request $request)
    {
        $request->validate([
            'title'         => 'required|string|max:500',
            'publisher'     => 'nullable|string|max:255',
            'publisher_url' => 'nullable|string|max:500',
            'year'          => 'required|integer|min:1970|max:'.date('Y'),
            'visibility'    => 'required|in:public,private',
        ]);
        $this->getLecturer()->publications()->create($request->only(['title','publisher','publisher_url','year','visibility','authors']));
        return back()->with('success', 'Data publikasi berhasil ditambahkan.');
    }

    public function updatePublikasi(Request $request, $id)
    {
        $request->validate([
            'title'         => 'required|string|max:500',
            'publisher'     => 'nullable|string|max:255',
            'publisher_url' => 'nullable|string|max:500',
            'year'          => 'required|integer|min:1970|max:'.date('Y'),
            'visibility'    => 'required|in:public,private',
        ]);
        Publication::where('lecturer_id', $this->getLecturer()->id)->findOrFail($id)
            ->update($request->only(['title','publisher','publisher_url','year','visibility','authors']));
        return back()->with('success', 'Data publikasi berhasil diperbarui.');
    }

    public function destroyPublikasi($id)
    {
        Publication::where('lecturer_id', $this->getLecturer()->id)->findOrFail($id)->delete();
        return back()->with('success', 'Data publikasi berhasil dihapus.');
    }

    public function togglePublikasiVisibility($id)
    {
        $p = Publication::where('lecturer_id', $this->getLecturer()->id)->findOrFail($id);
        $p->update(['visibility' => $p->visibility === 'public' ? 'private' : 'public']);
        return back()->with('success', 'Visibilitas berhasil diperbarui.');
    }

    // ─── BUKU ──────────────────────────────────────────
    public function buku()
    {
        $lecturer = $this->getLecturer();
        $bukus    = $lecturer->books()->orderByDesc('year')->get();
        return view('dosen.buku', compact('lecturer', 'bukus'));
    }

    public function storeBuku(Request $request)
    {
        $request->validate([
            'title'      => 'required|string|max:500',
            'year'       => 'required|integer|min:1970|max:'.date('Y'),
            'publisher'  => 'nullable|string|max:255',
            'isbn'       => 'nullable|string|max:30',
            'visibility' => 'required|in:public,private',
        ]);
        $this->getLecturer()->books()->create($request->only(['title','year','publisher','isbn','visibility']));
        return back()->with('success', 'Data buku berhasil ditambahkan.');
    }

    public function updateBuku(Request $request, $id)
    {
        $request->validate([
            'title'      => 'required|string|max:500',
            'year'       => 'required|integer|min:1970|max:'.date('Y'),
            'publisher'  => 'nullable|string|max:255',
            'isbn'       => 'nullable|string|max:30',
            'visibility' => 'required|in:public,private',
        ]);
        Book::where('lecturer_id', $this->getLecturer()->id)->findOrFail($id)
            ->update($request->only(['title','year','publisher','isbn','visibility']));
        return back()->with('success', 'Data buku berhasil diperbarui.');
    }

    public function destroyBuku($id)
    {
        Book::where('lecturer_id', $this->getLecturer()->id)->findOrFail($id)->delete();
        return back()->with('success', 'Data buku berhasil dihapus.');
    }

    public function toggleBukuVisibility($id)
    {
        $b = Book::where('lecturer_id', $this->getLecturer()->id)->findOrFail($id);
        $b->update(['visibility' => $b->visibility === 'public' ? 'private' : 'public']);
        return back()->with('success', 'Visibilitas berhasil diperbarui.');
    }

    // ─── HKI ───────────────────────────────────────────
    public function hki()
    {
        $lecturer = $this->getLecturer();
        $hkis     = $lecturer->hkis()->orderByDesc('year')->get();
        return view('dosen.hki', compact('lecturer', 'hkis'));
    }

    public function storeHki(Request $request)
    {
        $request->validate([
            'title'              => 'required|string|max:500',
            'year'               => 'required|integer|min:1970|max:'.date('Y'),
            'type'               => 'nullable|string|max:100',
            'certificate_number' => 'nullable|string|max:100',
            'visibility'         => 'required|in:public,private',
        ]);
        $this->getLecturer()->hkis()->create($request->only(['title','year','type','certificate_number','visibility']));
        return back()->with('success', 'Data HKI berhasil ditambahkan.');
    }

    public function updateHki(Request $request, $id)
    {
        $request->validate([
            'title'              => 'required|string|max:500',
            'year'               => 'required|integer|min:1970|max:'.date('Y'),
            'type'               => 'nullable|string|max:100',
            'certificate_number' => 'nullable|string|max:100',
            'visibility'         => 'required|in:public,private',
        ]);
        Hki::where('lecturer_id', $this->getLecturer()->id)->findOrFail($id)
            ->update($request->only(['title','year','type','certificate_number','visibility']));
        return back()->with('success', 'Data HKI berhasil diperbarui.');
    }

    public function destroyHki($id)
    {
        Hki::where('lecturer_id', $this->getLecturer()->id)->findOrFail($id)->delete();
        return back()->with('success', 'Data HKI berhasil dihapus.');
    }

    public function toggleHkiVisibility($id)
    {
        $h = Hki::where('lecturer_id', $this->getLecturer()->id)->findOrFail($id);
        $h->update(['visibility' => $h->visibility === 'public' ? 'private' : 'public']);
        return back()->with('success', 'Visibilitas berhasil diperbarui.');
    }

    // ─── PENGHARGAAN ───────────────────────────────────
    public function penghargaan()
    {
        $lecturer  = $this->getLecturer();
        $penghargaans = $lecturer->awards()->orderByDesc('date')->get();
        return view('dosen.penghargaan', compact('lecturer', 'penghargaans'));
    }

    public function storePenghargaan(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:500',
            'level'        => 'required|in:Internasional,Nasional,Lokal',
            'organizer'    => 'nullable|string|max:255',
            'rank'         => 'nullable|string|max:100',
            'date'         => 'required|date',
            'evidence_url' => 'nullable|url|max:500',
            'visibility'   => 'required|in:public,private',
        ]);
        $this->getLecturer()->awards()->create($request->only(['name','level','organizer','rank','date','evidence_url','visibility']));
        return back()->with('success', 'Data penghargaan berhasil ditambahkan.');
    }

    public function updatePenghargaan(Request $request, $id)
    {
        $request->validate([
            'name'         => 'required|string|max:500',
            'level'        => 'required|in:Internasional,Nasional,Lokal',
            'organizer'    => 'nullable|string|max:255',
            'rank'         => 'nullable|string|max:100',
            'date'         => 'required|date',
            'evidence_url' => 'nullable|url|max:500',
            'visibility'   => 'required|in:public,private',
        ]);
        Award::where('lecturer_id', $this->getLecturer()->id)->findOrFail($id)
            ->update($request->only(['name','level','organizer','rank','date','evidence_url','visibility']));
        return back()->with('success', 'Data penghargaan berhasil diperbarui.');
    }

    public function destroyPenghargaan($id)
    {
        Award::where('lecturer_id', $this->getLecturer()->id)->findOrFail($id)->delete();
        return back()->with('success', 'Data penghargaan berhasil dihapus.');
    }

    public function togglePenghargaanVisibility($id)
    {
        $a = Award::where('lecturer_id', $this->getLecturer()->id)->findOrFail($id);
        $a->update(['visibility' => $a->visibility === 'public' ? 'private' : 'public']);
        return back()->with('success', 'Visibilitas berhasil diperbarui.');
    }

    // ─── PASSWORD ────────────────────────────────────────────
    // Dihapus: tidak ada password lokal untuk role dosen/kaprodi/dekan/lppm,
    // semua autentikasi lewat CIS. Ganti password lewat SSO Institut
    // Teknologi Del (https://sso.del.ac.id), bukan dari SIPD.
}
