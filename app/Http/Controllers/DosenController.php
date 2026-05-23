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

        // ✅ FIX: tambah $stats sesuai blade
        $stats = [
            'pendidikan' => $lecturer->educations->count(),
            'penelitian' => $lecturer->researches->count(),
            'pengabdian' => $lecturer->communityServices->count(),
            'publikasi'  => $lecturer->publications->count(),
        ];

        return view('dosen.index', compact('lecturer', 'stats'));
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
            'nidn'      => 'nullable|string|max:20',
            'expertise' => 'nullable|string|max:500',
            'photo'     => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['nidn', 'expertise']);

        if ($request->hasFile('photo')) {
            if ($lecturer->photo) Storage::disk('public')->delete($lecturer->photo);
            $data['photo'] = $request->file('photo')->store('photos', 'public');
        }

        $lecturer->update($data);
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
            'visibility'  => 'required|in:public,internal',
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
            'visibility'  => 'required|in:public,internal',
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
        $edu->update(['visibility' => $edu->visibility === 'public' ? 'internal' : 'public']);
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
            'title'      => 'required|string|max:500',
            'year'       => 'required|integer|min:1970|max:'.date('Y'),
            'funding'    => 'nullable|string|max:255',
            'visibility' => 'required|in:public,internal',
        ]);
        $this->getLecturer()->researches()->create($request->only(['title','year','funding','visibility']));
        return back()->with('success', 'Data penelitian ditambahkan.');
    }

    public function updatePenelitian(Request $request, $id)
    {
        $request->validate([
            'title'      => 'required|string|max:500',
            'year'       => 'required|integer|min:1970|max:'.date('Y'),
            'funding'    => 'nullable|string|max:255',
            'visibility' => 'required|in:public,internal',
        ]);
        Research::where('lecturer_id', $this->getLecturer()->id)->findOrFail($id)
            ->update($request->only(['title','year','funding','visibility']));
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
        $r->update(['visibility' => $r->visibility === 'public' ? 'internal' : 'public']);
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
            'visibility' => 'required|in:public,internal',
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
            'visibility' => 'required|in:public,internal',
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
        $cs->update(['visibility' => $cs->visibility === 'public' ? 'internal' : 'public']);
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
            'title'      => 'required|string|max:500',
            'journal'    => 'nullable|string|max:255',
            'year'       => 'required|integer|min:1970|max:'.date('Y'),
            'doi'        => 'nullable|string|max:255',
            'visibility' => 'required|in:public,internal',
        ]);
        $this->getLecturer()->publications()->create($request->only(['title','journal','year','doi','visibility']));
        return back()->with('success', 'Data publikasi ditambahkan.');
    }

    public function updatePublikasi(Request $request, $id)
    {
        $request->validate([
            'title'      => 'required|string|max:500',
            'journal'    => 'nullable|string|max:255',
            'year'       => 'required|integer|min:1970|max:'.date('Y'),
            'doi'        => 'nullable|string|max:255',
            'visibility' => 'required|in:public,internal',
        ]);
        Publication::where('lecturer_id', $this->getLecturer()->id)->findOrFail($id)
            ->update($request->only(['title','journal','year','doi','visibility']));
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
        $p->update(['visibility' => $p->visibility === 'public' ? 'internal' : 'public']);
        return back()->with('success', 'Visibilitas diperbarui.');
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
