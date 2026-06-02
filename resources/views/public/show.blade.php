@extends('layouts.app')
@section('title', $lecturer->user->name)

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
    .tab-btn.active { background:#003087; color:#fff; }
    .tab-btn { transition: all .2s; }
    .tab-panel { display:none; }
    .tab-panel.active { display:block; }
    .timeline-dot::before {
        content:''; position:absolute; left:-1.1rem; top:.3rem;
        width:.6rem; height:.6rem; border-radius:50%; background:#003087;
    }
    .stat-ring {
        background: conic-gradient(#003087 var(--pct), #e5e7eb 0%);
    }
    /* Hide scrollbar but allow scroll — untuk sticky sidebar */
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

    @keyframes fadeUp { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }
    .fade-up { animation: fadeUp .4s ease both; }
    .fade-up-d1 { animation-delay: .1s; }
    .fade-up-d2 { animation-delay: .2s; }
    .fade-up-d3 { animation-delay: .3s; }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-gray-400 mb-6">
        <a href="{{ route('home') }}" class="hover:text-del transition-colors">Beranda</a>
        <span>/</span>
        <a href="{{ route('public.dosen') }}" class="hover:text-del transition-colors">Dosen</a>
        <span>/</span>
        <span class="text-gray-600 font-medium">{{ $lecturer->user->name }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- ════════════════ LEFT SIDEBAR ════════════════ -->
        <div class="lg:col-span-4 space-y-5 lg:sticky lg:top-4 lg:self-start lg:max-h-[calc(100vh-2rem)] lg:overflow-y-auto scrollbar-hide">

            <!-- Hero Card -->
            {{-- ✅ FIX: hapus overflow-hidden dari wrapper agar avatar tidak terpotong --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm fade-up">

                <!-- Cover gradient — overflow-hidden hanya di sini agar rounded-t tetap bersih -->
                <div class="h-24 bg-gradient-to-br from-del to-blue-600 relative rounded-t-2xl overflow-hidden">
                    <!-- Pola dot dekoratif -->
                    <div class="absolute inset-0 opacity-10"
                         style="background-image:url(\"data:image/svg+xml,%3Csvg width='40' height='40' xmlns='http://www.w3.org/2000/svg'%3E%3Ccircle cx='20' cy='20' r='1.5' fill='white'/%3E%3C/svg%3E\")">
                    </div>
                    <!-- Lingkaran dekoratif kanan -->
                    <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/10 rounded-full"></div>
                    <div class="absolute -right-2 -bottom-8 w-20 h-20 bg-white/5 rounded-full"></div>
                </div>

                <!-- Avatar — z-10 agar selalu di atas cover, relative agar keluar dari flow -->
                <div class="flex justify-center -mt-12 mb-4 relative z-10">
                    @if($lecturer->photo)
                        <img src="{{ Storage::url($lecturer->photo) }}"
                             alt="{{ $lecturer->user->name }}"
                             class="w-24 h-24 rounded-full border-4 border-white shadow-lg object-cover ring-2 ring-del/30">
                    @else
                        <div class="w-24 h-24 rounded-full border-4 border-white shadow-lg bg-gradient-to-br from-del to-blue-600
                                    flex items-center justify-center ring-2 ring-del/30">
                            <span class="text-white font-bold text-3xl select-none">
                                {{ strtoupper(substr($lecturer->user->name, 0, 1)) }}
                            </span>
                        </div>
                    @endif
                </div>

                <!-- Info teks -->
                <div class="px-5 pb-6 text-center">
                    <h1 class="text-lg font-bold text-gray-800 leading-tight">{{ $lecturer->user->name }}</h1>

                    @if($lecturer->jabatan_fungsional)
                        <p class="text-sm font-semibold text-del mt-0.5">{{ $lecturer->jabatan_fungsional }}</p>
                    @endif

                    @if($lecturer->nidn)
                        <p class="text-xs text-gray-400 mt-1">NIDN {{ $lecturer->nidn }}</p>
                    @endif

                    @if($lecturer->studyProgram)
                        <span class="inline-block mt-3 bg-blue-50 text-del text-xs font-semibold px-3 py-1.5 rounded-full border border-blue-100">
                            {{ $lecturer->studyProgram->name }}
                        </span>
                    @endif

                    @if($lecturer->expertise)
                        <div class="mt-4 pt-4 border-t border-gray-100 text-left">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Kepakaran</p>
                            <p class="text-sm text-gray-700 leading-relaxed">{{ $lecturer->expertise }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Stat Cards -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 fade-up fade-up-d1">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Statistik</p>
                <div class="grid grid-cols-2 gap-3">
                    @foreach([
                        ['Pendidikan',  $lecturer->educations->count(),        'bg-blue-600',   '📚'],
                        ['Penelitian',  $lecturer->researches->count(),         'bg-indigo-600', '🔬'],
                        ['Pengabdian',  $lecturer->communityServices->count(),  'bg-emerald-600','🌱'],
                        ['Publikasi',   $lecturer->publications->count(),        'bg-violet-600', '📄'],
                    ] as [$label, $count, $color, $icon])
                    <div class="rounded-xl bg-gray-50 p-3 text-center hover:shadow-sm transition-all">
                        <div class="text-xl mb-0.5">{{ $icon }}</div>
                        <div class="text-2xl font-bold text-gray-800">{{ $count }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">{{ $label }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Contact / Share -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 fade-up fade-up-d2">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Bagikan Profil</p>
                <button onclick="navigator.clipboard.writeText(window.location.href).then(()=>alert('URL disalin!'))"
                        class="w-full flex items-center justify-center gap-2 bg-gray-50 hover:bg-del hover:text-white text-gray-600 text-sm font-medium rounded-xl px-4 py-2.5 transition-all border border-gray-200 hover:border-del">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                    </svg>
                    Salin Tautan
                </button>
            </div>
        </div>

        <!-- ════════════════ MAIN CONTENT ════════════════ -->
        <div class="lg:col-span-8 space-y-5 fade-up fade-up-d1">

            <!-- Grafik Performa Akademik -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h2 class="text-base font-bold text-gray-800">Grafik Performa Akademik</h2>
                        <p class="text-xs text-gray-400 mt-0.5">5 tahun terakhir</p>
                    </div>
                    <div class="flex gap-3 text-xs text-gray-500">
                        @foreach([['bg-blue-500','Penelitian'],['bg-emerald-500','Pengabdian'],['bg-violet-500','Publikasi']] as [$c,$l])
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-sm {{ $c }}"></span>{{ $l }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @php $hasActivity = max(array_merge($researchData, $serviceData, $pubData)) > 0; @endphp
                @if($hasActivity)
                    <canvas id="performaChart" height="100"></canvas>
                @else
                    <div class="flex flex-col items-center justify-center py-12 text-gray-300">
                        <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <p class="text-sm">Belum ada data aktivitas</p>
                    </div>
                @endif
            </div>

            <!-- Tab Navigation -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden fade-up fade-up-d2">
                <div class="flex border-b border-gray-100 px-4 pt-4 gap-1 overflow-x-auto">
                    @foreach([
                        ['pendidikan',  'Pendidikan',  $lecturer->educations->count()],
                        ['penelitian',  'Penelitian',  $lecturer->researches->count()],
                        ['pengabdian',  'Pengabdian',  $lecturer->communityServices->count()],
                        ['publikasi',   'Publikasi',   $lecturer->publications->count()],
                    ] as [$key, $label, $count])
                    <button onclick="switchTab('{{ $key }}')" id="tab-btn-{{ $key }}"
                            class="tab-btn flex items-center gap-1.5 text-sm font-medium px-4 py-2 rounded-t-lg text-gray-500 hover:text-del whitespace-nowrap">
                        {{ $label }}
                        <span class="text-xs bg-gray-100 px-1.5 py-0.5 rounded-full">{{ $count }}</span>
                    </button>
                    @endforeach
                </div>

                <div class="p-5">

                    <!-- TAB: PENDIDIKAN -->
                    <div id="tab-pendidikan" class="tab-panel">
                        @if($lecturer->educations->isEmpty())
                            <p class="text-sm text-gray-400 text-center py-8">Belum ada data pendidikan.</p>
                        @else
                        <div class="relative pl-6 border-l-2 border-gray-100 space-y-5">
                            @foreach($lecturer->educations as $edu)
                            <div class="relative timeline-dot">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <span class="inline-block text-xs font-bold bg-del text-white px-2 py-0.5 rounded mb-1">{{ $edu->degree }}</span>
                                        <p class="font-semibold text-gray-800 text-sm">{{ $edu->institution }}</p>
                                        @if($edu->major)
                                            <p class="text-xs text-gray-500 mt-0.5">{{ $edu->major }}</p>
                                        @endif
                                    </div>
                                    <span class="text-xs text-gray-400 font-medium bg-gray-50 px-2 py-1 rounded-lg">{{ $edu->year }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>

                    <!-- TAB: PENELITIAN -->
                    <div id="tab-penelitian" class="tab-panel">
                        @if($lecturer->researches->isEmpty())
                            <p class="text-sm text-gray-400 text-center py-8">Belum ada data penelitian.</p>
                        @else
                        <div class="space-y-3">
                            @foreach($lecturer->researches as $item)
                            <div class="flex items-start gap-3 p-3 rounded-xl hover:bg-blue-50/50 transition-colors border border-transparent hover:border-blue-100">
                                <div class="w-9 h-9 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-del" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800 leading-snug">{{ $item->title }}</p>
                                    <div class="flex flex-wrap gap-2 mt-1.5">
                                        <span class="text-xs text-gray-400 bg-gray-50 px-2 py-0.5 rounded">{{ $item->year }}</span>
                                        @if($item->funding_source)
                                            <span class="text-xs text-gray-400 bg-gray-50 px-2 py-0.5 rounded">{{ $item->funding_source }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>

                    <!-- TAB: PENGABDIAN -->
                    <div id="tab-pengabdian" class="tab-panel">
                        @if($lecturer->communityServices->isEmpty())
                            <p class="text-sm text-gray-400 text-center py-8">Belum ada data pengabdian.</p>
                        @else
                        <div class="space-y-3">
                            @foreach($lecturer->communityServices as $item)
                            <div class="flex items-start gap-3 p-3 rounded-xl hover:bg-emerald-50/50 transition-colors border border-transparent hover:border-emerald-100">
                                <div class="w-9 h-9 rounded-lg bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800 leading-snug">{{ $item->title }}</p>
                                    <div class="flex flex-wrap gap-2 mt-1.5">
                                        <span class="text-xs text-gray-400 bg-gray-50 px-2 py-0.5 rounded">{{ $item->year }}</span>
                                        @if($item->location)
                                            <span class="text-xs text-gray-400 bg-gray-50 px-2 py-0.5 rounded">📍 {{ $item->location }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>

                    <!-- TAB: PUBLIKASI -->
                    <div id="tab-publikasi" class="tab-panel">
                        <!-- Filter Year -->
                        <div class="flex items-center gap-2 mb-4">
                            <form action="{{ route('public.dosen.show', $lecturer->id) }}" method="GET" class="flex items-center gap-2">
                                <select name="year" onchange="this.form.submit()"
                                        class="text-xs border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-1 focus:ring-del bg-white">
                                    <option value="">Semua Tahun</option>
                                    @foreach($pubYears as $y)
                                        <option value="{{ $y }}" {{ $filterYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endforeach
                                </select>
                                @if($filterYear)
                                    <a href="{{ route('public.dosen.show', $lecturer->id) }}"
                                       class="text-xs text-red-400 hover:underline flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>Reset
                                    </a>
                                @endif
                            </form>
                            @if($filterYear)
                                <span class="text-xs text-gray-400">— {{ $publications->count() }} hasil tahun {{ $filterYear }}</span>
                            @endif
                        </div>

                        @if($publications->isEmpty())
                            <p class="text-sm text-gray-400 text-center py-8">Belum ada publikasi.</p>
                        @else
                        <div class="space-y-3">
                            @foreach($publications as $pub)
                            <div class="flex items-start gap-3 p-3 rounded-xl hover:bg-violet-50/50 transition-colors border border-transparent hover:border-violet-100">
                                <div class="w-9 h-9 rounded-lg bg-violet-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    @if($pub->publisher_url)
                                        <a href="{{ $pub->publisher_url }}" target="_blank"
                                           class="text-sm font-medium text-del hover:underline leading-snug block">
                                            {{ $pub->title }}
                                        </a>
                                    @else
                                        <p class="text-sm font-medium text-gray-800 leading-snug">{{ $pub->title }}</p>
                                    @endif
                                    <div class="flex flex-wrap gap-2 mt-1.5">
                                        <span class="text-xs text-gray-400 bg-gray-50 px-2 py-0.5 rounded">{{ $pub->year }}</span>
                                        @if($pub->publisher)
                                            <span class="text-xs text-gray-400 bg-gray-50 px-2 py-0.5 rounded">{{ $pub->publisher }}</span>
                                        @endif
                                        @if($pub->publisher_url)
                                            <a href="{{ $pub->publisher_url }}" target="_blank"
                                               class="text-xs text-del flex items-center gap-1 hover:underline">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                </svg>Lihat Sumber
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
// ── Tab System ────────────────────────────────────────────
function switchTab(key) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + key).classList.add('active');
    document.getElementById('tab-btn-' + key).classList.add('active');
}
// Aktifkan tab pertama yang ada data
const tabs = ['pendidikan','penelitian','pengabdian','publikasi'];
const counts = {
    pendidikan: {{ $lecturer->educations->count() }},
    penelitian: {{ $lecturer->researches->count() }},
    pengabdian: {{ $lecturer->communityServices->count() }},
    publikasi:  {{ $lecturer->publications->count() }},
};
const urlTab = new URLSearchParams(window.location.search).get('tab');
const firstWithData = urlTab || tabs.find(t => counts[t] > 0) || 'pendidikan';
switchTab(firstWithData);

// ── Chart.js ──────────────────────────────────────────────
@if($hasActivity ?? false)
const ctx = document.getElementById('performaChart');
if (ctx) {
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_map('strval', $years)) !!},
            datasets: [
                {
                    label: 'Penelitian',
                    data: {!! json_encode($researchData) !!},
                    backgroundColor: 'rgba(59,130,246,0.85)',
                    borderRadius: 6,
                    borderSkipped: false,
                },
                {
                    label: 'Pengabdian',
                    data: {!! json_encode($serviceData) !!},
                    backgroundColor: 'rgba(16,185,129,0.85)',
                    borderRadius: 6,
                    borderSkipped: false,
                },
                {
                    label: 'Publikasi',
                    data: {!! json_encode($pubData) !!},
                    backgroundColor: 'rgba(139,92,246,0.85)',
                    borderRadius: 6,
                    borderSkipped: false,
                },
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding: 10,
                    cornerRadius: 8,
                }
            },
            scales: {
                x: { grid: { display: false }, border: { display: false } },
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, precision: 0 },
                    grid: { color: '#f1f5f9' },
                    border: { display: false }
                }
            }
        }
    });
}
@endif
</script>
@endpush
