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

        // ── Data chart dashboard: 5 tahun terakhir ──
        $currentYear = (int) date('Y');
        $chartYears  = range($currentYear - 4, $currentYear);

        $chartData = [
            'years'    => array_map('strval', $chartYears),
            'penelitian' => collect($chartYears)->map(
                fn($y) => $lecturer->researches->where('year', $y)->count()
            )->values()->toArray(),
            'pengabdian' => collect($chartYears)->map(
                fn($y) => $lecturer->communityServices->where('year', $y)->count()
            )->values()->toArray(),
            'publikasi' => collect($chartYears)->map(
                fn($y) => $lecturer->publications->where('year', $y)->count()
            )->values()->toArray(),
        ];

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

        return view('dosen.index', compact('lecturer', 'stats', 'chartData', 'recentActivities'));
    }

    // ─── PROFIL ──────────────────────────────────────────────
    public function editProfil()
    {
        $lecturer = $this->getLecturer()->load('studyProgram');

        // Untuk Dekan: kirim daftar semua dosen aktif (untuk dropdown transfer jabatan)
        $allLecturers = collect();
        if (auth()->user()->isDekan()) {
            $allLecturers = \App\Models\Lecturer::with(['user', 'studyProgram'])
                ->whereHas('user', fn($q) => $q->where('is_active', true)->where('role', '!=', 'dekan'))
                ->get();
        }

        return view('dosen.profil', compact('lecturer', 'allLecturers'));
    }

    public function updateProfil(Request $request)
    {
        $lecturer = $this->getLecturer();
        $request->validate([
            'nidn'               => 'nullable|string|max:20',
            'jabatan_fungsional' => 'nullable|string|in:Asisten Ahli,Lektor,Lektor Kepala,Guru Besar / Profesor',
            'expertise'          => 'nullable|string|max:500',
            'photo'              => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['nidn', 'jabatan_fungsional', 'expertise']);
        $data['is_public'] = $request->boolean('is_public');

        if ($request->hasFile('photo')) {
            if ($lecturer->photo) Storage::disk('public')->delete($lecturer->photo);
            $data['photo'] = $request->file('photo')->store('photos', 'public');
        }

        $lecturer->update($data);

        // Update nama user jika berubah
        if ($request->filled('name') && $request->name !== auth()->user()->name) {
            auth()->user()->update(['name' => $request->name]);
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
        return back()->with('success', 'Data pendidikan diperbarui.');
    }

    public function destroyPendidikan($id)
    {
        Education::where('lecturer_id', $this->getLecturer()->id)->findOrFail($id)->delete();
        return back()->with('success', 'Data pendidikan dihapus.');
    }

    public function togglePendidikanVisibility($id)
    {
        $edu = Education::where('lecturer_id', $this->getLecturer()->id)->findOrFail($id);
        $edu->update(['visibility' => $edu->visibility === 'public' ? 'private' : 'public']);
        return back()->with('success', 'Visibilitas diperbarui.');
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
            'funding_source' => 'nullable|string|max:255',
            'visibility'     => 'required|in:public,private',
        ]);
        $this->getLecturer()->researches()->create($request->only(['title','year','funding_source','visibility']));
        return back()->with('success', 'Data penelitian ditambahkan.');
    }

    public function updatePenelitian(Request $request, $id)
    {
        $request->validate([
            'title'          => 'required|string|max:500',
            'year'           => 'required|integer|min:1970|max:'.date('Y'),
            'funding_source' => 'nullable|string|max:255',
            'visibility'     => 'required|in:public,private',
        ]);
        Research::where('lecturer_id', $this->getLecturer()->id)->findOrFail($id)
            ->update($request->only(['title','year','funding_source','visibility']));
        return back()->with('success', 'Data penelitian diperbarui.');
    }

    public function destroyPenelitian($id)
    {
        Research::where('lecturer_id', $this->getLecturer()->id)->findOrFail($id)->delete();
        return back()->with('success', 'Data penelitian dihapus.');
    }

    public function togglePenelitianVisibility($id)
    {
        $r = Research::where('lecturer_id', $this->getLecturer()->id)->findOrFail($id);
        $r->update(['visibility' => $r->visibility === 'public' ? 'private' : 'public']);
        return back()->with('success', 'Visibilitas diperbarui.');
    }

    // ─── PENGABDIAN ──────────────────────────────────────────
    public function pengabdian()
    {
        $lecturer    = $this->getLecturer();
        // ✅ FIX: $pengabdians (bukan $pengabdian)
        $pengabdians = $lecturer->communityServices()->orderByDesc('year')->get();
        return view('dosen.pengabdian', compact('lecturer', 'pengabdians'));
    }

    public function storePengabdian(Request $request)
    {
        $request->validate([
            'title'      => 'required|string|max:500',
            'year'       => 'required|integer|min:1970|max:'.date('Y'),
            'location'   => 'nullable|string|max:255',
            'visibility' => 'required|in:public,private',
        ]);
        $this->getLecturer()->communityServices()->create($request->only(['title','year','location','visibility']));
        return back()->with('success', 'Data pengabdian ditambahkan.');
    }

    public function updatePengabdian(Request $request, $id)
    {
        $request->validate([
            'title'      => 'required|string|max:500',
            'year'       => 'required|integer|min:1970|max:'.date('Y'),
            'location'   => 'nullable|string|max:255',
            'visibility' => 'required|in:public,private',
        ]);
        CommunityService::where('lecturer_id', $this->getLecturer()->id)->findOrFail($id)
            ->update($request->only(['title','year','location','visibility']));
        return back()->with('success', 'Data pengabdian diperbarui.');
    }

    public function destroyPengabdian($id)
    {
        CommunityService::where('lecturer_id', $this->getLecturer()->id)->findOrFail($id)->delete();
        return back()->with('success', 'Data pengabdian dihapus.');
    }

    public function togglePengabdianVisibility($id)
    {
        $cs = CommunityService::where('lecturer_id', $this->getLecturer()->id)->findOrFail($id);
        $cs->update(['visibility' => $cs->visibility === 'public' ? 'private' : 'public']);
        return back()->with('success', 'Visibilitas diperbarui.');
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
        $this->getLecturer()->publications()->create($request->only(['title','publisher','publisher_url','year','visibility']));
        return back()->with('success', 'Data publikasi ditambahkan.');
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
            ->update($request->only(['title','publisher','publisher_url','year','visibility']));
        return back()->with('success', 'Data publikasi diperbarui.');
    }

    public function destroyPublikasi($id)
    {
        Publication::where('lecturer_id', $this->getLecturer()->id)->findOrFail($id)->delete();
        return back()->with('success', 'Data publikasi dihapus.');
    }

    public function togglePublikasiVisibility($id)
    {
        $p = Publication::where('lecturer_id', $this->getLecturer()->id)->findOrFail($id);
        $p->update(['visibility' => $p->visibility === 'public' ? 'private' : 'public']);
        return back()->with('success', 'Visibilitas diperbarui.');
    }

    // ─── TRANSFER JABATAN DEKAN (Dekan only, dari profil sendiri) ───
    public function transferDekan(Request $request)
    {
        $auth = auth()->user();

        // Hanya Dekan yang bisa
        if (!$auth->isDekan()) {
            abort(403, 'Hanya Dekan yang dapat mengalihkan jabatan.');
        }

        $request->validate([
            'transfer_to_lecturer_id' => 'required|exists:lecturers,id',
        ]);

        $targetLecturer = \App\Models\Lecturer::with('user')->findOrFail($request->transfer_to_lecturer_id);

        // Pastikan target bukan Dekan itu sendiri
        if ($targetLecturer->user_id === $auth->id) {
            return back()->withErrors(['transfer_to_lecturer_id' => 'Tidak dapat mengalihkan jabatan ke diri sendiri.']);
        }

        // Pastikan target adalah dosen aktif (bukan Dekan lain)
        if (!$targetLecturer->user->is_active) {
            return back()->withErrors(['transfer_to_lecturer_id' => 'Akun tujuan tidak aktif.']);
        }
        if ($targetLecturer->user->role === 'dekan') {
            return back()->withErrors(['transfer_to_lecturer_id' => 'Akun tujuan sudah menjabat sebagai Dekan.']);
        }

        // Simpan role lama target untuk notifikasi
        $targetOldRole = $targetLecturer->user->role;
        $targetName    = $targetLecturer->user->name;

        // 1. Turunkan Dekan saat ini menjadi Dosen
        $auth->update(['role' => 'dosen']);

        // 2. Angkat target menjadi Dekan baru
        $targetLecturer->user->update(['role' => 'dekan']);

        // 3. Logout sesi Dekan lama dan redirect ke login
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', "Jabatan Dekan berhasil dialihkan ke {$targetName}. Silakan login ulang.");
    }

    // ─── PASSWORD ────────────────────────────────────────────
    public function editPassword()
    {
        return view('dosen.password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak sesuai.']);
        }

        auth()->user()->update(['password' => Hash::make($request->password)]);
        return back()->with('success', 'Password berhasil diubah.');
    }
}
