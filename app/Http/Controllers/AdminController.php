<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Lecturer;
use App\Models\StudyProgram;
use App\Models\Publication;
use App\Models\Research;
use App\Models\CommunityService;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{
    private function assertDekanOnly(): void
    {
        if (!auth()->user()->isDekan()) {
            abort(403, 'Akses ditolak. Hanya Dekan yang dapat melakukan aksi ini.');
        }
    }

    private function assertKaprodiCanEdit(Lecturer $target): void
    {
        $auth = auth()->user();

        if ($auth->isDekan()) return;

        if ($auth->isKaprodi()) {
            $myLecturer = $auth->lecturer;

            if ($target->user->role === 'dekan') {
                abort(403, 'Kaprodi tidak dapat mengedit profil Dekan.');
            }
            if ($target->user->role === 'kaprodi' && $target->user_id !== $auth->id) {
                abort(403, 'Kaprodi hanya dapat mengedit dosen di prodinya sendiri.');
            }
            if ($myLecturer && $target->study_program_id !== $myLecturer->study_program_id) {
                abort(403, 'Kaprodi hanya dapat mengedit dosen di prodinya sendiri.');
            }
            return;
        }

        abort(403);
    }

    // ─── DASHBOARD (Dekan only) ──────────────────────────────────────
    public function index()
    {
        $this->assertDekanOnly();
        $stats = [
            'total_dosen'     => Lecturer::count(),
            'total_publik'    => Lecturer::where('is_public', true)->count(),
            'total_prodi'     => StudyProgram::count(),
            'total_publikasi' => Publication::count(),
        ];
        $recentDosen = Lecturer::with('user', 'studyProgram')->latest()->take(5)->get();

        $currentYear = (int) date('Y');
        $activityYears = collect()
            ->merge(Research::distinct()->pluck('year'))
            ->merge(CommunityService::distinct()->pluck('year'))
            ->merge(Publication::distinct()->pluck('year'))
            ->filter()->unique()->sort()->values();

        $yearsAll = $activityYears->isNotEmpty()
            ? range($activityYears->first(), max($activityYears->last(), $currentYear))
            : range($currentYear - 4, $currentYear);

        $buildAdminChart = function ($years) {
            return [
                'years'      => array_map('strval', $years),
                'penelitian' => array_map(fn($y) => Research::where('year', $y)->count(), $years),
                'pengabdian' => array_map(fn($y) => CommunityService::where('year', $y)->count(), $years),
                'publikasi'  => array_map(fn($y) => Publication::where('year', $y)->count(), $years),
            ];
        };

        $adminChart5   = $buildAdminChart(range($currentYear - 4, $currentYear));
        $adminChart10  = $buildAdminChart(range($currentYear - 9, $currentYear));
        $adminChartAll = $buildAdminChart($yearsAll);

        return view('admin.index', compact('stats', 'recentDosen', 'adminChart5', 'adminChart10', 'adminChartAll'));
    }

    // ─── EDIT PROFIL DOSEN (Dekan semua, Kaprodi hanya prodinya) ────
    // DIPERSEMPIT: hanya expertise yang boleh diubah manual. Toggle is_public
    // sudah dicabut (tanpa dasar requirement) -- kolomnya tetap ada tapi tidak
    // ada UI untuk mengubahnya di mana pun.
    // Field lain (nidn, jabatan_fungsional, nip, alias, jenjang_pendidikan,
    // nama, email, role, prodi) sekarang sumber kebenarannya CIS, disinkron
    // otomatis tiap login (lihat AuthController::login).
    public function editProfilDosen($id)
    {
        $lecturer = Lecturer::with('user', 'studyProgram')->findOrFail($id);
        $this->assertKaprodiCanEdit($lecturer);
        return view('admin.profil-edit', compact('lecturer'));
    }

    public function updateProfilDosen(Request $request, $id)
    {
        $lecturer = Lecturer::with('user')->findOrFail($id);
        $this->assertKaprodiCanEdit($lecturer);
        $request->validate([
            'expertise' => 'nullable|string|max:255',
        ]);
        $lecturer->update([
            'expertise' => $request->expertise,
        ]);
        return back()->with('success', 'Profil dosen berhasil diperbarui.');
    }

    // ─── DATA INTERNAL (Dekan + Kaprodi) ────────────────────────────
    public function internal(Request $request)
    {
        $myProdiId = null;

        $query = Lecturer::with(['user', 'studyProgram',
            'educations', 'researches', 'communityServices', 'publications'
        ]);

        if (auth()->user()->isKaprodi()) {
            $myProdiId = auth()->user()->lecturer?->study_program_id;
            $query->where('study_program_id', $myProdiId);
        }

        $query->when($request->search, function ($q) use ($request) {
            $q->where(function ($sub) use ($request) {
                $sub->whereHas('user', fn($u) => $u->where('name', 'like', '%'.$request->search.'%'))
                    ->orWhere('expertise', 'like', '%'.$request->search.'%');
            });
        });

        if (auth()->user()->isDekan() && $request->filled('prodi')) {
            $query->where('study_program_id', $request->prodi);
        }

        $query->when($request->filled('role'), function ($q) use ($request) {
            $q->whereHas('user', fn($u) => $u->where('role', $request->role));
        });

        $lecturers    = $query->whereHas('user', fn($u) => $u->where('is_active', true))
                               ->where('user_id', '!=', auth()->id())
                               ->orderBy('study_program_id')->get();
        $studyPrograms = StudyProgram::orderBy('name')->get();

        return view('admin.internal', compact('lecturers', 'myProdiId', 'studyPrograms'));
    }

    // ─── DETAIL TRIDHARMA (Dekan semua, Kaprodi hanya scope prodinya) ───
    // Beda dari halaman publik: di sini SEMUA data ditampilkan (termasuk yang
    // ditandai Privat), tidak difilter visibility, karena ini memang untuk
    // internal fakultas, bukan pengunjung umum.
    public function internalShow($id)
    {
        $lecturer = \App\Models\Lecturer::with([
            'user', 'studyProgram',
            'educations', 'researches', 'publications', 'books', 'hkis', 'awards',
            'communityServices.lecturers.user',
        ])->findOrFail($id);

        if (auth()->user()->isKaprodi()) {
            $myProdiId = auth()->user()->lecturer?->study_program_id;
            if ($lecturer->study_program_id !== $myProdiId) {
                abort(403, 'Kaprodi hanya dapat melihat detail dosen di program studi Anda.');
            }
        }

        return view('admin.internal-show', compact('lecturer'));
    }

    // ─── LAPORAN TRIDHARMA (Dekan semua Vokasi, Kaprodi hanya prodinya) ──
    // Filter periode berdasarkan TAHUN + BULAN ASLI penelitian/PKM (kolom
    // year & month di tabel researches/community_services), BUKAN tanggal
    // data itu diinput ke sistem.
    // Semua logika pengambilan data laporan dipusatkan di sini supaya index &
    // export (PDF/Excel) memakai hasil yang identik dengan filter yang aktif.
    private function getLaporanTridharmaData(Request $request): array
    {
        $periode = $request->get('periode', 'tahun_ini');
        $now     = now();

        // Periode "semua" = tanpa batas tanggal sama sekali (pemantauan menyeluruh)
        $isAllTime = $periode === 'semua';

        // Tentukan rentang tahun+bulan berdasarkan periode yang dipilih
        [$startYear, $startMonth, $endYear, $endMonth] = match ($periode) {
            'bulan_ini'  => [$now->year, $now->month, $now->year, $now->month],
            '3_bulan'    => [
                $now->copy()->subMonths(2)->year, $now->copy()->subMonths(2)->month,
                $now->year, $now->month,
            ],
            'tahun_ini'  => [$now->year, 1, $now->year, 12],
            'custom'     => [
                (int) $request->get('tahun', $now->year),
                (int) $request->get('bulan_dari', 1),
                (int) $request->get('tahun', $now->year),
                (int) $request->get('bulan_sampai', 12),
            ],
            'semua'      => [null, null, null, null],
            default      => [$now->year, 1, $now->year, 12],
        };

        $applyPeriodFilter = function ($query) use ($startYear, $startMonth, $endYear, $endMonth, $isAllTime) {
            if ($isAllTime) {
                return; // tidak difilter sama sekali -- tampilkan semua data
            }
            $query->where(function ($q) use ($startYear, $startMonth, $endYear, $endMonth) {
                // Rentang sederhana: asumsikan startYear == endYear untuk periode standar
                // (bulan_ini, 3_bulan biasa tidak lewat pergantian tahun; kalau lewat,
                // logika ini masih benar karena dibungkus OR per kombinasi tahun-bulan)
                if ($startYear === $endYear) {
                    $q->where('year', $startYear)
                      ->where(function ($m) use ($startMonth, $endMonth) {
                          // Data lama yang belum ada bulannya (NULL) tetap ditampilkan
                          // selama tahunnya cocok -- supaya data sebelum fitur Bulan
                          // ada tidak "hilang" dari laporan.
                          $m->whereBetween('month', [$startMonth, $endMonth])
                            ->orWhereNull('month');
                      });
                } else {
                    $q->where(function ($sub) use ($startYear, $startMonth) {
                        $sub->where('year', $startYear)
                            ->where(function ($m) use ($startMonth) {
                                $m->where('month', '>=', $startMonth)->orWhereNull('month');
                            });
                    })->orWhere(function ($sub) use ($endYear, $endMonth) {
                        $sub->where('year', $endYear)
                            ->where(function ($m) use ($endMonth) {
                                $m->where('month', '<=', $endMonth)->orWhereNull('month');
                            });
                    });
                }
            });
        };

        $lecturerScope = function ($query) {
            if (auth()->user()->isKaprodi()) {
                $myProdiId = auth()->user()->lecturer?->study_program_id;
                $query->where('study_program_id', $myProdiId);
            }
        };

        $penelitian = Research::with(['lecturer.user', 'lecturer.studyProgram'])
            ->whereHas('lecturer', $lecturerScope)
            ->tap($applyPeriodFilter)
            ->orderByDesc('year')->orderByDesc('month')
            ->get();

        $pkm = CommunityService::with(['lecturers.user', 'lecturers.studyProgram'])
            ->whereHas('lecturers', $lecturerScope)
            ->tap($applyPeriodFilter)
            ->orderByDesc('year')->orderByDesc('month')
            ->get();

        return compact('penelitian', 'pkm', 'periode', 'startYear', 'startMonth', 'endYear', 'endMonth', 'isAllTime');
    }

    public function laporanTridharma(Request $request)
    {
        $data = $this->getLaporanTridharmaData($request);
        $data['studyPrograms'] = StudyProgram::orderBy('name')->get();
        return view('admin.laporan-tridharma', $data);
    }

    public function exportLaporanTridharmaPdf(Request $request)
    {
        $data = $this->getLaporanTridharmaData($request);
        $pdf = Pdf::loadView('admin.laporan-tridharma-pdf', $data);
        return $pdf->download('laporan-tridharma-' . now()->format('Ymd-His') . '.pdf');
    }

}
