<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Education;
use App\Models\Research;
use App\Models\CommunityService;
use App\Models\Publication;

class DosenController extends Controller
{
    private function getLecturer()
    {
        return Auth::user()->lecturer;
    }

    // ======================== DASHBOARD ========================
    public function index()
    {
        $lecturer = $this->getLecturer();
        $stats = [
            'pendidikan'  => $lecturer->educations()->count(),
            'penelitian'  => $lecturer->researches()->count(),
            'pengabdian'  => $lecturer->communityServices()->count(),
            'publikasi'   => $lecturer->publications()->count(),
        ];
        return view('dosen.index', compact('lecturer', 'stats'));
    }

    // ======================== PROFIL ========================
    public function editProfil()
    {
        $lecturer = $this->getLecturer();
        return view('dosen.profil', compact('lecturer'));
    }

    public function updateProfil(Request $request)
    {
        $request->validate([
            'nidn'      => 'nullable|string|max:20',
            'expertise' => 'nullable|string',
            'is_public' => 'boolean',
            'photo'     => 'nullable|image|max:2048',
        ]);

        $lecturer = $this->getLecturer();
        $data = $request->only(['nidn', 'expertise']);
        $data['is_public'] = $request->boolean('is_public');

        if ($request->hasFile('photo')) {
            if ($lecturer->photo) Storage::disk('public')->delete($lecturer->photo);
            $data['photo'] = $request->file('photo')->store('photos', 'public');
        }

        $lecturer->update($data);

        // Update nama user
        if ($request->filled('name')) {
            Auth::user()->update(['name' => $request->name]);
        }

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    // ======================== PENDIDIKAN ========================
    public function pendidikan()
    {
        $lecturer = $this->getLecturer();
        $educations = $lecturer->educations()->orderBy('year', 'desc')->get();
        return view('dosen.pendidikan', compact('lecturer', 'educations'));
    }

    public function storePendidikan(Request $request)
    {
        $request->validate([
            'degree'      => 'required|string|max:10',
            'institution' => 'required|string|max:255',
            'major'       => 'nullable|string|max:255',
            'year'        => 'required|integer|min:1950|max:' . date('Y'),
            'visibility'  => 'required|in:public,private',
        ]);

        $this->getLecturer()->educations()->create($request->all());
        return back()->with('success', 'Data pendidikan berhasil ditambahkan.');
    }

    public function updatePendidikan(Request $request, $id)
    {
        $education = Education::where('lecturer_id', $this->getLecturer()->id)->findOrFail($id);
        $request->validate([
            'degree'      => 'required|string|max:10',
            'institution' => 'required|string|max:255',
            'major'       => 'nullable|string|max:255',
            'year'        => 'required|integer|min:1950|max:' . date('Y'),
            'visibility'  => 'required|in:public,private',
        ]);
        $education->update($request->all());
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
        return back()->with('success', 'Visibilitas diperbarui.');
    }

    // ======================== PENELITIAN ========================
    public function penelitian()
    {
        $lecturer = $this->getLecturer();
        $penelitians = $lecturer->researches()->orderBy('year', 'desc')->get();
        return view('dosen.penelitian', compact('lecturer', 'penelitians'));
    }

    public function storePenelitian(Request $request)
    {
        $request->validate([
            'title'          => 'required|string',
            'year'           => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'funding_source' => 'nullable|string|max:255',
            'visibility'     => 'required|in:public,private',
        ]);
        $this->getLecturer()->researches()->create($request->all());
        return back()->with('success', 'Data penelitian berhasil ditambahkan.');
    }

    public function updatePenelitian(Request $request, $id)
    {
        $item = Research::where('lecturer_id', $this->getLecturer()->id)->findOrFail($id);
        $request->validate([
            'title'          => 'required|string',
            'year'           => 'required|integer',
            'funding_source' => 'nullable|string|max:255',
            'visibility'     => 'required|in:public,private',
        ]);
        $item->update($request->all());
        return back()->with('success', 'Data penelitian berhasil diperbarui.');
    }

    public function destroyPenelitian($id)
    {
        Research::where('lecturer_id', $this->getLecturer()->id)->findOrFail($id)->delete();
        return back()->with('success', 'Data penelitian berhasil dihapus.');
    }

    public function togglePenelitianVisibility($id)
    {
        $item = Research::where('lecturer_id', $this->getLecturer()->id)->findOrFail($id);
        $item->update(['visibility' => $item->visibility === 'public' ? 'private' : 'public']);
        return back()->with('success', 'Visibilitas diperbarui.');
    }

    // ======================== PENGABDIAN ========================
    public function pengabdian()
    {
        $lecturer = $this->getLecturer();
        $pengabdians = $lecturer->communityServices()->orderBy('year', 'desc')->get();
        return view('dosen.pengabdian', compact('lecturer', 'pengabdians'));
    }

    public function storePengabdian(Request $request)
    {
        $request->validate([
            'title'      => 'required|string',
            'year'       => 'required|integer',
            'location'   => 'nullable|string|max:255',
            'visibility' => 'required|in:public,private',
        ]);
        $this->getLecturer()->communityServices()->create($request->all());
        return back()->with('success', 'Data pengabdian berhasil ditambahkan.');
    }

    public function updatePengabdian(Request $request, $id)
    {
        $item = CommunityService::where('lecturer_id', $this->getLecturer()->id)->findOrFail($id);
        $item->update($request->all());
        return back()->with('success', 'Data pengabdian berhasil diperbarui.');
    }

    public function destroyPengabdian($id)
    {
        CommunityService::where('lecturer_id', $this->getLecturer()->id)->findOrFail($id)->delete();
        return back()->with('success', 'Data pengabdian berhasil dihapus.');
    }

    public function togglePengabdianVisibility($id)
    {
        $item = CommunityService::where('lecturer_id', $this->getLecturer()->id)->findOrFail($id);
        $item->update(['visibility' => $item->visibility === 'public' ? 'private' : 'public']);
        return back()->with('success', 'Visibilitas diperbarui.');
    }

    // ======================== PUBLIKASI ========================
    public function publikasi()
    {
        $lecturer = $this->getLecturer();
        $publikasis = $lecturer->publications()->orderBy('year', 'desc')->get();
        return view('dosen.publikasi', compact('lecturer', 'publikasis'));
    }

    public function storePublikasi(Request $request)
    {
        $request->validate([
            'title'         => 'required|string',
            'year'          => 'required|integer',
            'publisher'     => 'nullable|string|max:255',
            'publisher_url' => 'nullable|url|max:500',
            'visibility'    => 'required|in:public,private',
        ]);
        $this->getLecturer()->publications()->create($request->all());
        return back()->with('success', 'Data publikasi berhasil ditambahkan.');
    }

    public function updatePublikasi(Request $request, $id)
    {
        $item = Publication::where('lecturer_id', $this->getLecturer()->id)->findOrFail($id);
        $item->update($request->all());
        return back()->with('success', 'Data publikasi berhasil diperbarui.');
    }

    public function destroyPublikasi($id)
    {
        Publication::where('lecturer_id', $this->getLecturer()->id)->findOrFail($id)->delete();
        return back()->with('success', 'Data publikasi berhasil dihapus.');
    }

    public function togglePublikasiVisibility($id)
    {
        $item = Publication::where('lecturer_id', $this->getLecturer()->id)->findOrFail($id);
        $item->update(['visibility' => $item->visibility === 'public' ? 'private' : 'public']);
        return back()->with('success', 'Visibilitas diperbarui.');
    }

    // ======================== PASSWORD ========================
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

        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        Auth::user()->update(['password' => Hash::make($request->password)]);
        return back()->with('success', 'Password berhasil diperbarui.');
    }
}
