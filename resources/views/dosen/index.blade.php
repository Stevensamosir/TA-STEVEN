@extends('layouts.dashboard')
@section('title', 'Dashboard Dosen')
@section('page-title', 'Dashboard')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')
<div class="pt-4 space-y-6">

    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-del to-del-light rounded-2xl p-6 text-white relative overflow-hidden">
        <div class="absolute right-0 top-0 bottom-0 w-32 opacity-10"
             style="background-image:radial-gradient(circle, white 1px, transparent 1px); background-size:16px 16px;">
        </div>
        <p class="text-blue-200 text-sm mb-1">Selamat datang,</p>
        <h2 class="text-xl font-bold mb-1">{{ auth()->user()->name }}</h2>
        <p class="text-blue-200 text-sm">
            {{ $lecturer->studyProgram->name ?? 'Fakultas Vokasi' }}
            @if($lecturer->jabatan_fungsional) · {{ $lecturer->jabatan_fungsional }} @endif
            · NIDN: {{ $lecturer->nidn ?? '-' }}
        </p>

        <!-- Status badge -->
        <div class="mt-4 flex items-center gap-2">
            @if($lecturer->is_public)
                <span class="inline-flex items-center gap-1.5 bg-white/20 text-white text-xs font-medium px-3 py-1 rounded-full">
                    <span class="w-1.5 h-1.5 bg-green-400 rounded-full"></span>Profil Publik Aktif
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 bg-white/20 text-white text-xs font-medium px-3 py-1 rounded-full">
                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span>Profil Tidak Publik
                </span>
            @endif
            <a href="{{ route('public.dosen.show', $lecturer->id) }}" target="_blank"
               class="inline-flex items-center gap-1 text-blue-200 hover:text-white text-xs font-medium transition-colors">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>Lihat Profil →
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['label'=>'Pendidikan', 'value'=>$stats['pendidikan'], 'color'=>'text-del',        'bg'=>'bg-blue-50',   'href'=>route('dosen.pendidikan'),  'icon'=>'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
            ['label'=>'Penelitian',  'value'=>$stats['penelitian'],  'color'=>'text-indigo-600', 'bg'=>'bg-indigo-50', 'href'=>route('dosen.penelitian'),   'icon'=>'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z'],
            ['label'=>'Pengabdian',  'value'=>$stats['pengabdian'],  'color'=>'text-emerald-600','bg'=>'bg-emerald-50','href'=>route('dosen.pengabdian'),  'icon'=>'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z'],
            ['label'=>'Publikasi',   'value'=>$stats['publikasi'],   'color'=>'text-violet-600', 'bg'=>'bg-violet-50', 'href'=>route('dosen.publikasi'),   'icon'=>'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        ] as $stat)
        <a href="{{ $stat['href'] }}"
           class="bg-white rounded-2xl border border-gray-100 p-5 hover:shadow-md transition-all group">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 h-9 {{ $stat['bg'] }} rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 {{ $stat['color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}"/>
                    </svg>
                </div>
                <svg class="w-4 h-4 text-gray-300 group-hover:text-gray-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
            <div class="text-3xl font-bold {{ $stat['color'] }}">{{ $stat['value'] }}</div>
            <div class="text-sm text-gray-500 mt-1">{{ $stat['label'] }}</div>
        </a>
        @endforeach
    </div>

    <!-- Chart + Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <!-- Chart Aktivitas -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 p-5">
            <div class="flex items-start justify-between mb-5 gap-3 flex-wrap">
                <div>
                    <h3 class="font-semibold text-gray-800 text-sm">Grafik Aktivitas Akademik</h3>
                    <p class="text-xs text-gray-400 mt-0.5" id="dashChartRangeLabel">5 tahun terakhir</p>
                </div>
                <div class="flex items-center gap-3 flex-wrap">
                    <!-- Legend (klikable — klik untuk sembunyikan/tampilkan data) -->
                    <div class="flex gap-1" id="dash-legend">
                        @foreach([[0,'bg-indigo-500','Penelitian'],[1,'bg-emerald-500','Pengabdian'],[2,'bg-violet-500','Publikasi']] as [$idx,$bg,$lbl])
                        <button type="button" onclick="toggleDashDataset({{ $idx }})" id="dash-legend-{{ $idx }}"
                                class="flex items-center gap-1.5 text-xs text-gray-500 hover:text-gray-800 cursor-pointer select-none px-2 py-1 rounded hover:bg-gray-100 transition-all">
                            <span class="w-2.5 h-2.5 rounded-sm {{ $bg }} flex-shrink-0" id="dash-legend-dot-{{ $idx }}"></span>
                            {{ $lbl }}
                        </button>
                        @endforeach
                    </div>
                    <!-- Toggle rentang waktu -->
                    <div class="flex items-center gap-2 flex-wrap">
                        <div class="flex items-center bg-gray-100 rounded-lg p-0.5 gap-0.5 text-xs">
                            <button type="button" onclick="setDashChartRange('5')" id="dash-btn-range-5"
                                    class="dash-range-btn px-2.5 py-1 rounded-md transition-all font-medium bg-white text-del shadow-sm">
                                5 Tahun
                            </button>
                            <button type="button" onclick="setDashChartRange('10')" id="dash-btn-range-10"
                                    class="dash-range-btn px-2.5 py-1 rounded-md transition-all font-medium text-gray-400">
                                10 Tahun
                            </button>
                            <button type="button" onclick="setDashChartRange('all')" id="dash-btn-range-all"
                                    class="dash-range-btn px-2.5 py-1 rounded-md transition-all font-medium text-gray-400">
                                Semua
                            </button>
                        </div>
                        <div class="flex items-center bg-gray-100 rounded-lg p-0.5 gap-0.5">
                            <button type="button" onclick="setDashChartType('bar')" id="dash-type-btn-bar"
                                    title="Grafik Batang" class="dash-type-btn p-1.5 rounded-md transition-all text-gray-400">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><rect x="3" y="12" width="4" height="9" rx="1"/><rect x="10" y="7" width="4" height="14" rx="1"/><rect x="17" y="4" width="4" height="17" rx="1"/></svg>
                            </button>
                            <button type="button" onclick="setDashChartType('line')" id="dash-type-btn-line"
                                    title="Grafik Garis" class="dash-type-btn p-1.5 rounded-md transition-all bg-white text-del shadow-sm">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 17 8 11 13 14 19 6"/><circle cx="3" cy="17" r="1.5" fill="currentColor" stroke="none"/><circle cx="8" cy="11" r="1.5" fill="currentColor" stroke="none"/><circle cx="13" cy="14" r="1.5" fill="currentColor" stroke="none"/><circle cx="19" cy="6" r="1.5" fill="currentColor" stroke="none"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            @php
                $allZero = max(array_merge(
                    $chartDataAll['penelitian'],
                    $chartDataAll['pengabdian'],
                    $chartDataAll['publikasi']
                )) === 0;
            @endphp

            @if($allZero)
                <div class="flex flex-col items-center justify-center py-12 text-gray-300">
                    <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <p class="text-sm">Tambahkan data penelitian, pengabdian, atau publikasi untuk melihat grafik.</p>
                </div>
            @else
                <canvas id="dashboardChart" height="90"></canvas>
            @endif
        </div>

        <!-- Aktivitas Terbaru -->
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <h3 class="font-semibold text-gray-800 text-sm mb-4">Aktivitas Terbaru</h3>
            @if($recentActivities->isEmpty())
                <div class="flex flex-col items-center justify-center py-8 text-gray-300">
                    <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="text-xs text-center">Belum ada aktivitas. Mulai tambahkan data Anda.</p>
                </div>
            @else
            <div class="space-y-3">
                @foreach($recentActivities as $act)
                @php
                    $colorMap = ['blue'=>'bg-blue-100 text-blue-700','emerald'=>'bg-emerald-100 text-emerald-700','violet'=>'bg-violet-100 text-violet-700'];
                    $c = $colorMap[$act['color']] ?? 'bg-gray-100 text-gray-600';
                @endphp
                <div class="flex items-start gap-3">
                    <span class="inline-block text-xs font-semibold px-2 py-0.5 rounded mt-0.5 flex-shrink-0 {{ $c }}">
                        {{ $act['type'] }}
                    </span>
                    <div class="min-w-0">
                        <p class="text-xs text-gray-700 leading-snug line-clamp-2">{{ $act['title'] }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $act['year'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    <!-- Quick Links -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <a href="{{ route('dosen.profil.edit') }}"
           class="bg-white border border-gray-100 rounded-2xl p-5 hover:shadow-md transition-all flex items-center gap-4 group">
            <div class="w-10 h-10 bg-blue-50 group-hover:bg-del rounded-xl flex items-center justify-center transition-colors">
                <svg class="w-5 h-5 text-del group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <p class="font-semibold text-gray-800 text-sm">Edit Profil</p>
                <p class="text-xs text-gray-400">Perbarui foto, kepakaran, jabatan fungsional</p>
            </div>
        </a>
        <a href="{{ route('public.dosen.show', $lecturer->id) }}" target="_blank"
           class="bg-white border border-gray-100 rounded-2xl p-5 hover:shadow-md transition-all flex items-center gap-4 group">
            <div class="w-10 h-10 bg-blue-50 group-hover:bg-del rounded-xl flex items-center justify-center transition-colors">
                <svg class="w-5 h-5 text-del group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
            </div>
            <div>
                <p class="font-semibold text-gray-800 text-sm">Lihat Profil Publik</p>
                <p class="text-xs text-gray-400">Tampilan yang dilihat pengunjung</p>
            </div>
        </a>
    </div>

</div>
@endsection

@push('scripts')
<script>
@if(!$allZero)
const ctx = document.getElementById('dashboardChart');
let dashboardChart = null;
let dashCurrentRange = '5';

const DASH_RANGE_DATA = {
    '5':   {
        labels: {!! json_encode($chartData5['years']) !!},
        penelitian: {!! json_encode($chartData5['penelitian']) !!},
        pengabdian: {!! json_encode($chartData5['pengabdian']) !!},
        publikasi:  {!! json_encode($chartData5['publikasi']) !!},
    },
    '10': {
        labels: {!! json_encode($chartData10['years']) !!},
        penelitian: {!! json_encode($chartData10['penelitian']) !!},
        pengabdian: {!! json_encode($chartData10['pengabdian']) !!},
        publikasi:  {!! json_encode($chartData10['publikasi']) !!},
    },
    'all': {
        labels: {!! json_encode($chartDataAll['years']) !!},
        penelitian: {!! json_encode($chartDataAll['penelitian']) !!},
        pengabdian: {!! json_encode($chartDataAll['pengabdian']) !!},
        publikasi:  {!! json_encode($chartDataAll['publikasi']) !!},
    },
};
const DASH_RANGE_LABEL = { '5': '5 tahun terakhir', '10': '10 tahun terakhir', 'all': 'Semua tahun' };
let dashChartType = 'line';

function buildDashDatasets(data, isBar) {
    return [
        { label:'Penelitian', data:data.penelitian, borderColor:'rgb(99,102,241)', backgroundColor: isBar?'rgba(99,102,241,0.75)':'rgba(99,102,241,0.08)', borderWidth: isBar?0:2.5, borderRadius: isBar?4:0, pointRadius: isBar?0:4, pointHoverRadius:6, tension:0.4, fill:!isBar },
        { label:'Pengabdian', data:data.pengabdian, borderColor:'rgb(16,185,129)',  backgroundColor: isBar?'rgba(16,185,129,0.75)':'rgba(16,185,129,0.08)',  borderWidth: isBar?0:2.5, borderRadius: isBar?4:0, pointRadius: isBar?0:4, pointHoverRadius:6, tension:0.4, fill:!isBar },
        { label:'Publikasi',  data:data.publikasi,  borderColor:'rgb(139,92,246)',  backgroundColor: isBar?'rgba(139,92,246,0.75)':'rgba(139,92,246,0.08)',  borderWidth: isBar?0:2.5, borderRadius: isBar?4:0, pointRadius: isBar?0:4, pointHoverRadius:6, tension:0.4, fill:!isBar },
    ];
}

function rebuildDashChart() {
    const data = DASH_RANGE_DATA[dashCurrentRange];
    const isBar = dashChartType === 'bar';
    if (dashboardChart) dashboardChart.destroy();
    dashboardChart = new Chart(ctx, {
        type: dashChartType,
        data: { labels: data.labels, datasets: buildDashDatasets(data, isBar) },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: { backgroundColor: '#1e293b', padding: 10, cornerRadius: 8, mode: 'index', intersect: false }
            },
            scales: {
                x: { grid: { display: true, color:'#e5e7eb', lineWidth:0.8 }, border: { display: false } },
                y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 }, grid: { color: '#e5e7eb', lineWidth: 0.8 }, border: { display: false } }
            },
            interaction: { mode: 'index', intersect: false },
        }
    });
    // Re-apply hidden state pakai getDatasetMeta (reliable di Chart.js 4)
    _dashHidden.forEach((hidden, i) => {
        const meta = dashboardChart.getDatasetMeta(i);
        if (meta) meta.hidden = hidden;
    });
    if (_dashHidden.some(h => h)) dashboardChart.update();
}
function setDashChartType(type) {
    dashChartType = type;
    document.querySelectorAll('.dash-type-btn').forEach(b => { b.classList.remove('bg-white','text-del','shadow-sm'); b.classList.add('text-gray-400'); });
    const btn = document.getElementById('dash-type-btn-' + type);
    if(btn){ btn.classList.remove('text-gray-400'); btn.classList.add('bg-white','text-del','shadow-sm'); }
    rebuildDashChart();
}

function setDashChartRange(range) {
    dashCurrentRange = range;
    document.querySelectorAll('.dash-range-btn').forEach(b => {
        b.classList.remove('bg-white', 'text-del', 'shadow-sm');
        b.classList.add('text-gray-400');
    });
    const activeBtn = document.getElementById('dash-btn-range-' + range);
    activeBtn.classList.remove('text-gray-400');
    activeBtn.classList.add('bg-white', 'text-del', 'shadow-sm');
    document.getElementById('dashChartRangeLabel').textContent = DASH_RANGE_LABEL[range];
    rebuildDashChart();
}

// Toggle dataset saat legend diklik (saran Bu Ana)
const _dashHidden = [false, false, false];
function toggleDashDataset(index) {
    _dashHidden[index] = !_dashHidden[index];
    const btn = document.getElementById('dash-legend-' + index);
    const dot = document.getElementById('dash-legend-dot-' + index);
    if (_dashHidden[index]) {
        btn.classList.add('opacity-40');
        dot.classList.add('opacity-30');
    } else {
        btn.classList.remove('opacity-40');
        dot.classList.remove('opacity-30');
    }
    if (dashboardChart) {
        const meta = dashboardChart.getDatasetMeta(index);
        if (meta) meta.hidden = _dashHidden[index];
        dashboardChart.update();
    }
}

if (ctx) {
    rebuildDashChart();
}
@endif
</script>
@endpush
