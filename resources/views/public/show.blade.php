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

    <!-- Breadcrumb + Back Button -->
    <div class="flex items-center gap-3 mb-6">
        <button onclick="history.back()"
            class="flex items-center gap-1.5 text-xs text-gray-500 hover:text-del bg-white border border-gray-200 hover:border-del rounded-lg px-3 py-1.5 transition-colors flex-shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali
        </button>
        <nav class="flex items-center gap-2 text-xs text-gray-400 overflow-hidden">
            <a href="{{ route('home') }}" class="hover:text-del transition-colors whitespace-nowrap">Beranda</a>
            <span>/</span>
            <a href="{{ route('public.dosen') }}" class="hover:text-del transition-colors whitespace-nowrap">Dosen</a>
            <span>/</span>
            <span class="text-gray-600 font-medium truncate">{{ $lecturer->user->name }}</span>
        </nav>
    </div>

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

                    @php
                        // Email hasil fallback sync (dosen<angka>@del.ac.id) bukan email asli
                        // dosen -- jangan tampilkan ke publik, cukup pesan netral.
                        $isFallbackEmail = $lecturer->user->email
                            && preg_match('/^dosen\d+@del\.ac\.id$/', $lecturer->user->email);
                    @endphp
                    @if($lecturer->user->email && !$isFallbackEmail)
                        <div class="mt-4 pt-4 border-t border-gray-100 text-left">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Kontak</p>
                            <a href="mailto:{{ $lecturer->user->email }}"
                               class="inline-flex items-center gap-2 text-sm text-del hover:underline break-all">
                                <svg class="w-4 h-4 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                {{ $lecturer->user->email }}
                            </a>
                        </div>
                    @else
                        <div class="mt-4 pt-4 border-t border-gray-100 text-left">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Kontak</p>
                            <p class="text-sm text-gray-400 italic">Email belum tersedia</p>
                        </div>
                    @endif
                </div>
            </div>


        </div>

        <!-- ════════════════ MAIN CONTENT ════════════════ -->
        <div class="lg:col-span-8 space-y-5 fade-up fade-up-d1">

            <!-- Grafik Performa Akademik -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-start justify-between mb-5 gap-3">
                    <div class="select-none">
                        <h2 class="text-base font-bold text-gray-800">Grafik Performa Akademik</h2>
                        <p class="text-xs text-gray-400 mt-0.5" id="chartRangeLabel">5 tahun terakhir</p>
                    </div>
                    <div class="flex items-center gap-4 flex-wrap justify-end">
                        {{-- Legend (klikable — klik untuk sembunyikan/tampilkan) --}}
                        <div class="flex gap-1 text-xs text-gray-500 select-none" id="pub-legend">
                            @foreach([[0,'bg-blue-500','Penelitian'],[1,'bg-emerald-500','Pengabdian'],[2,'bg-violet-500','Publikasi']] as [$idx,$bg,$lbl])
                            <button type="button" onclick="togglePubDataset({{ $idx }})" id="pub-legend-{{ $idx }}"
                                    class="flex items-center gap-1.5 px-2 py-1 rounded hover:bg-gray-100 transition-all cursor-pointer">
                                <span class="w-2.5 h-2.5 rounded-sm {{ $bg }} flex-shrink-0" id="pub-legend-dot-{{ $idx }}"></span>
                                {{ $lbl }}
                            </button>
                            @endforeach
                        </div>
                        {{-- Toggle rentang waktu: 5 Tahun Terakhir / Semua Tahun (pola sama dengan
                             SINTA yang menampilkan skor "3 tahun terakhir" berdampingan dengan skor
                             "overall" sepanjang karier) --}}
                        <div class="flex items-center bg-gray-100 rounded-lg p-0.5 gap-0.5 text-xs" id="rangeToggle">
                            <button type="button" onclick="setChartRange('5')" id="btn-range-5"
                                    class="chart-toggle-btn active px-2.5 py-1 rounded-md transition-all font-medium">
                                5 Tahun
                            </button>
                            <button type="button" onclick="setChartRange('10')" id="btn-range-10"
                                    class="chart-toggle-btn px-2.5 py-1 rounded-md transition-all font-medium">
                                10 Tahun
                            </button>
                            <button type="button" onclick="setChartRange('all')" id="btn-range-all"
                                    class="chart-toggle-btn px-2.5 py-1 rounded-md transition-all font-medium">
                                Semua
                            </button>
                        </div>
                        {{-- Toggle Bar / Line --}}
                        <div class="flex items-center bg-gray-100 rounded-lg p-0.5 gap-0.5" id="chartToggle">
                            <button type="button" onclick="setChartType('bar')" id="btn-bar"
                                    title="Grafik Batang"
                                    class="chart-toggle-btn active p-1.5 rounded-md transition-all">
                                {{-- ikon bar chart --}}
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                    <rect x="3"  y="12" width="4" height="9" rx="1"/>
                                    <rect x="10" y="7"  width="4" height="14" rx="1"/>
                                    <rect x="17" y="4"  width="4" height="17" rx="1"/>
                                </svg>
                            </button>
                            <button type="button" onclick="setChartType('line')" id="btn-line"
                                    title="Grafik Garis"
                                    class="chart-toggle-btn p-1.5 rounded-md transition-all">
                                {{-- ikon line chart --}}
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="3 17 8 11 13 14 19 6"/>
                                    <circle cx="3"  cy="17" r="1.5" fill="currentColor" stroke="none"/>
                                    <circle cx="8"  cy="11" r="1.5" fill="currentColor" stroke="none"/>
                                    <circle cx="13" cy="14" r="1.5" fill="currentColor" stroke="none"/>
                                    <circle cx="19" cy="6"  r="1.5" fill="currentColor" stroke="none"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                @php $hasActivity = max(array_merge($chartAll['research'], $chartAll['service'], $chartAll['pub'])) > 0; @endphp
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
                        ['pendidikan',   'Pendidikan',   $lecturer->educations->count()],
                        ['penelitian',   'Penelitian',   $lecturer->researches->count()],
                        ['pengabdian',   'Pengabdian',   $lecturer->communityServices->count()],
                        ['publikasi',    'Publikasi',    $lecturer->publications->count()],
                        ['buku',         'Buku',         $lecturer->books->count()],
                        ['hki',          'HKI',          $lecturer->hkis->count()],
                        ['penghargaan',  'Penghargaan',  $lecturer->awards->count()],
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
                            <p class="text-sm text-gray-500 text-center py-8">Belum ada data pendidikan.</p>
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
                            <p class="text-sm text-gray-500 text-center py-8">Belum ada data penelitian.</p>
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
                            <p class="text-sm text-gray-500 text-center py-8">Belum ada data pengabdian.</p>
                        @else
                        <div class="space-y-3">
                            @foreach($lecturer->communityServices as $item)
                            <div class="flex items-start gap-3 p-3 rounded-xl hover:bg-emerald-50/50 transition-colors border border-transparent hover:border-emerald-100">
                                <div class="w-9 h-9 rounded-lg bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800 leading-snug">{{ $item->title }}</p>
                                    <div class="flex flex-wrap gap-2 mt-1.5">
                                        <span class="text-xs text-gray-400 bg-gray-50 px-2 py-0.5 rounded">{{ $item->year }}</span>
                                        @if($item->location)
                                            <span class="text-xs text-gray-400 bg-gray-50 px-2 py-0.5 rounded">📍 {{ $item->location }}</span>
                                        @endif
                                        @if($item->pkm_type)
                                            <span class="text-xs text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded font-medium">{{ $item->pkm_type }}</span>
                                        @endif
                                    </div>
                                    @if($item->lecturers->isNotEmpty())
                                        <p class="text-xs text-gray-500 mt-1.5">
                                            <span class="font-medium">Tim Dosen:</span>
                                            {{ $item->lecturers->map(fn($l) => $l->user->name . ' (' . $l->pivot->role . ')')->join(', ') }}
                                        </p>
                                    @endif
                                    @if($item->student_members)
                                        <p class="text-xs text-gray-500 mt-1">
                                            <span class="font-medium">Anggota Mahasiswa:</span> {{ $item->student_members }}
                                        </p>
                                    @endif
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
    <div class="flex items-center bg-gray-100 rounded-lg p-0.5 gap-0.5 text-xs" id="pubYearToggle">
                                <button type="button" onclick="filterPubYear('all')" id="pub-yr-all"
                                        class="pub-yr-btn px-2.5 py-1 rounded-md font-medium transition-all bg-white text-del shadow-sm">
                                    Semua
                                </button>
                                <button type="button" onclick="filterPubYear('5')" id="pub-yr-5"
                                        class="pub-yr-btn px-2.5 py-1 rounded-md font-medium transition-all text-gray-400">
                                    5 Tahun
                                </button>
                                <button type="button" onclick="filterPubYear('10')" id="pub-yr-10"
                                        class="pub-yr-btn px-2.5 py-1 rounded-md font-medium transition-all text-gray-400">
                                    10 Tahun
                                </button>
                            </div>

                        </div>

                        @if($publications->isEmpty())
                            <p class="text-sm text-gray-500 text-center py-8">Belum ada publikasi.</p>
                        @else
                        <div class="space-y-3">
                            @foreach($publications as $pub)
                            <div class="flex items-start gap-3 p-3 rounded-xl hover:bg-violet-50/50 transition-colors border border-transparent hover:border-violet-100" data-pub-year="{{ $pub->year }}">
                                <div class="w-9 h-9 rounded-lg bg-violet-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 12h6m4 0h.01"/>
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
                                        @if($pub->authors)
                                            @php
                                                $authorList = collect(explode(',', $pub->authors))->map(fn($a) => trim($a))->filter();
                                                $displayAuthors = $authorList->take(3)->join(', ');
                                                $moreCount = max(0, $authorList->count() - 3);
                                            @endphp
                                            <span class="text-xs text-gray-400 italic">
                                                {{ $displayAuthors }}{{ $moreCount > 0 ? ', +' . $moreCount . ' lainnya' : '' }}
                                            </span>
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

                    <!-- TAB: BUKU -->
                    <div id="tab-buku" class="tab-panel">
                        @if($lecturer->books->isEmpty())
                            <p class="text-sm text-gray-500 text-center py-8">Belum ada data buku.</p>
                        @else
                        <div class="space-y-3">
                            @foreach($lecturer->books as $item)
                            <div class="flex items-start gap-3 p-3 rounded-xl hover:bg-amber-50/50 transition-colors border border-transparent hover:border-amber-100">
                                <div class="w-9 h-9 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800 leading-snug">{{ $item->title }}</p>
                                    <div class="flex flex-wrap gap-2 mt-1.5">
                                        <span class="text-xs text-gray-400 bg-gray-50 px-2 py-0.5 rounded">{{ $item->year }}</span>
                                        @if($item->publisher)
                                            <span class="text-xs text-gray-400 bg-gray-50 px-2 py-0.5 rounded">{{ $item->publisher }}</span>
                                        @endif
                                        @if($item->isbn)
                                            <span class="text-xs text-gray-400 bg-gray-50 px-2 py-0.5 rounded">ISBN: {{ $item->isbn }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>

                    <!-- TAB: HKI -->
                    <div id="tab-hki" class="tab-panel">
                        @if($lecturer->hkis->isEmpty())
                            <p class="text-sm text-gray-500 text-center py-8">Belum ada data HKI.</p>
                        @else
                        <div class="space-y-3">
                            @foreach($lecturer->hkis as $item)
                            <div class="flex items-start gap-3 p-3 rounded-xl hover:bg-rose-50/50 transition-colors border border-transparent hover:border-rose-100">
                                <div class="w-9 h-9 rounded-lg bg-rose-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800 leading-snug">{{ $item->title }}</p>
                                    <div class="flex flex-wrap gap-2 mt-1.5">
                                        <span class="text-xs text-gray-400 bg-gray-50 px-2 py-0.5 rounded">{{ $item->year }}</span>
                                        @if($item->type)
                                            <span class="text-xs text-gray-400 bg-gray-50 px-2 py-0.5 rounded">{{ $item->type }}</span>
                                        @endif
                                        @if($item->certificate_number)
                                            <span class="text-xs text-gray-400 bg-gray-50 px-2 py-0.5 rounded">No. {{ $item->certificate_number }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>

                    <!-- TAB: PENGHARGAAN -->
                    <div id="tab-penghargaan" class="tab-panel">
                        @if($lecturer->awards->isEmpty())
                            <p class="text-sm text-gray-500 text-center py-8">Belum ada data penghargaan.</p>
                        @else
                        <div class="space-y-3">
                            @foreach($lecturer->awards as $item)
                            <div class="flex items-start gap-3 p-3 rounded-xl hover:bg-yellow-50/50 transition-colors border border-transparent hover:border-yellow-100">
                                <div class="w-9 h-9 rounded-lg bg-yellow-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    @if($item->evidence_url)
                                        <a href="{{ $item->evidence_url }}" target="_blank" class="text-sm font-medium text-del hover:underline leading-snug block">{{ $item->name }}</a>
                                    @else
                                        <p class="text-sm font-medium text-gray-800 leading-snug">{{ $item->name }}</p>
                                    @endif
                                    <div class="flex flex-wrap gap-2 mt-1.5">
                                        <span class="text-xs text-yellow-700 bg-yellow-50 px-2 py-0.5 rounded font-medium">{{ $item->level }}</span>
                                        <span class="text-xs text-gray-400 bg-gray-50 px-2 py-0.5 rounded">{{ \Carbon\Carbon::parse($item->date)->format('d M Y') }}</span>
                                        @if($item->organizer)
                                            <span class="text-xs text-gray-400 bg-gray-50 px-2 py-0.5 rounded">{{ $item->organizer }}</span>
                                        @endif
                                        @if($item->rank)
                                            <span class="text-xs text-gray-400 bg-gray-50 px-2 py-0.5 rounded">{{ $item->rank }}</span>
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
const tabs = ['pendidikan','penelitian','pengabdian','publikasi','buku','hki','penghargaan'];
const counts = {
    pendidikan:  {{ $lecturer->educations->count() }},
    penelitian:  {{ $lecturer->researches->count() }},
    pengabdian:  {{ $lecturer->communityServices->count() }},
    publikasi:   {{ $lecturer->publications->count() }},
    buku:        {{ $lecturer->books->count() }},
    hki:         {{ $lecturer->hkis->count() }},
    penghargaan: {{ $lecturer->awards->count() }},
};
const urlTab = new URLSearchParams(window.location.search).get('tab');
const firstWithData = urlTab || tabs.find(t => counts[t] > 0) || 'pendidikan';
switchTab(firstWithData);

// ── Chart.js dengan toggle Bar/Line dan toggle Rentang Waktu ──
@if($hasActivity ?? false)
const ctx = document.getElementById('performaChart');
let performaChart = null;

// Dua set data: 5 tahun terakhir (default) dan semua tahun beraktivitas
const RANGE_DATA = {
    '5':   {
        labels:   {!! json_encode($chart5['years']) !!},
        research: {!! json_encode($chart5['research']) !!},
        service:  {!! json_encode($chart5['service']) !!},
        pub:      {!! json_encode($chart5['pub']) !!},
    },
    'all': {
        labels:   {!! json_encode($chartAll['years']) !!},
        research: {!! json_encode($chartAll['research']) !!},
        service:  {!! json_encode($chartAll['service']) !!},
        pub:      {!! json_encode($chartAll['pub']) !!},
    },
};
const RANGE_LABEL_TEXT = { '5': '5 tahun terakhir', '10': '10 tahun terakhir', 'all': 'Semua tahun' };

let currentType  = 'bar';
let currentRange = '5';

const COLORS = {
    research : { solid: 'rgba(59,130,246,0.85)',  border: 'rgba(59,130,246,1)',   fill: 'rgba(59,130,246,0.12)'  },
    service  : { solid: 'rgba(16,185,129,0.85)',  border: 'rgba(16,185,129,1)',   fill: 'rgba(16,185,129,0.12)'  },
    pub      : { solid: 'rgba(139,92,246,0.85)',  border: 'rgba(139,92,246,1)',   fill: 'rgba(139,92,246,0.12)'  },
};

const commonOptions = {
    responsive: true,
    plugins: {
        legend: { display: false },
        tooltip: { backgroundColor: '#1e293b', padding: 10, cornerRadius: 8 }
    },
    scales: {
        x: { grid: { display: true, color:'#f3f4f6', lineWidth:0.5 }, border: { display: false } },
        y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 }, grid: { color: '#e5e7eb', lineWidth: 0.5 }, border: { display: false } }
    }
};

function buildDatasets(type, data) {
    const isBar = type === 'bar';
    const mk = (label, key) => ({
        label, data: data[key],
        backgroundColor: isBar ? COLORS[key].solid : COLORS[key].fill,
        borderColor:     COLORS[key].border,
        borderWidth:     isBar ? 0 : 2,
        borderRadius:    isBar ? 6 : 0,
        borderSkipped:   false,
        fill:            !isBar,
        tension:         0.4,
        pointBackgroundColor: COLORS[key].border,
        pointRadius:     isBar ? 0 : 4,
        pointHoverRadius: isBar ? 0 : 6,
    });
    return [mk('Penelitian', 'research'), mk('Pengabdian', 'service'), mk('Publikasi', 'pub')];
}

function rebuildChart() {
    const data = RANGE_DATA[currentRange];
    if (performaChart) performaChart.destroy();
    performaChart = new Chart(ctx, {
        type: currentType,
        data: { labels: data.labels, datasets: buildDatasets(currentType, data) },
        options: commonOptions,
    });
    // Re-apply hidden state pakai getDatasetMeta (reliable di Chart.js 4)
    _pubHidden.forEach((hidden, i) => {
        const meta = performaChart.getDatasetMeta(i);
        if (meta) meta.hidden = hidden;
    });
    if (_pubHidden.some(h => h)) performaChart.update();
}

function setChartType(type) {
    currentType = type;
    document.querySelectorAll('#chartToggle .chart-toggle-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('btn-' + type).classList.add('active');
    rebuildChart();
}

function setChartRange(range) {
    currentRange = range;
    // Generate 10yr slice dinamis dari all-data saat pertama dipilih
    if (range === '10' && !RANGE_DATA['10']) {
        const all = RANGE_DATA['all'];
        const n = all.labels.length;
        const s = Math.max(0, n - 10);
        RANGE_DATA['10'] = {
            labels:   all.labels.slice(s),
            research: all.research.slice(s),
            service:  all.service.slice(s),
            pub:      all.pub.slice(s),
        };
    }
    document.querySelectorAll('#rangeToggle .chart-toggle-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('btn-range-' + range).classList.add('active');
    document.getElementById('chartRangeLabel').textContent = RANGE_LABEL_TEXT[range];
    rebuildChart();
}

// Toggle dataset saat legend diklik (saran Bu Ana)
const _pubHidden = [false, false, false];
function togglePubDataset(index) {
    _pubHidden[index] = !_pubHidden[index];
    const btn = document.getElementById('pub-legend-' + index);
    const dot = document.getElementById('pub-legend-dot-' + index);
    if (_pubHidden[index]) {
        btn.classList.add('opacity-40');
        dot.classList.add('opacity-30');
    } else {
        btn.classList.remove('opacity-40');
        dot.classList.remove('opacity-30');
    }
    if (performaChart) {
        const meta = performaChart.getDatasetMeta(index);
        if (meta) meta.hidden = _pubHidden[index];
        performaChart.update();
    }
}

if (ctx) {
    rebuildChart();
}
@endif

function filterPubYear(range) {
    const now = new Date().getFullYear();
    document.querySelectorAll('.pub-yr-btn').forEach(b => {
        b.classList.remove('bg-white','text-del','shadow-sm');
        b.classList.add('text-gray-400');
    });
    const activeBtn = document.getElementById('pub-yr-' + range);
    if(activeBtn){ activeBtn.classList.remove('text-gray-400'); activeBtn.classList.add('bg-white','text-del','shadow-sm'); }
    document.querySelectorAll('[data-pub-year]').forEach(el => {
        const yr = parseInt(el.dataset.pubYear);
        let show = true;
        if(range === '5') show = yr >= now - 4;
        else if(range === '10') show = yr >= now - 9;
        el.style.display = show ? '' : 'none';
    });
}

// Style tombol toggle aktif
const toggleStyle = document.createElement('style');
toggleStyle.textContent = `
    .chart-toggle-btn        { color: #9ca3af; }
    .chart-toggle-btn.active { background: #fff; color: #003087; box-shadow: 0 1px 3px rgba(0,0,0,0.12); }
    .chart-toggle-btn:hover:not(.active) { color: #374151; }
`;
document.head.appendChild(toggleStyle);
</script>
@endpush
