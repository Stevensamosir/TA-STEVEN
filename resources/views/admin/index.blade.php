@extends('layouts.dashboard')
@section('title','Dashboard Admin')
@section('page-title','Dashboard Admin')
@section('content')

{{-- Stat Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8 mt-2">
    <div class="bg-white rounded-2xl border border-gray-100 p-5 flex items-center gap-4 shadow-sm">
        <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-del" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
        </div>
        <div><p class="text-2xl font-bold text-gray-800">{{ $stats['total_dosen'] }}</p><p class="text-xs text-gray-400">Total Dosen</p></div>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 p-5 flex items-center gap-4 shadow-sm">
        <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
        </div>
        <div><p class="text-2xl font-bold text-gray-800">{{ $stats['total_publik'] }}</p><p class="text-xs text-gray-400">Profil Publik</p></div>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 p-5 flex items-center gap-4 shadow-sm">
        <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/></svg>
        </div>
        <div><p class="text-2xl font-bold text-gray-800">{{ $stats['total_prodi'] }}</p><p class="text-xs text-gray-400">Program Studi</p></div>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 p-5 flex items-center gap-4 shadow-sm">
        <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13"/></svg>
        </div>
        <div><p class="text-2xl font-bold text-gray-800">{{ $stats['total_publikasi'] }}</p><p class="text-xs text-gray-400">Total Publikasi</p></div>
    </div>
</div>

{{-- Quick links --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    <a href="{{ route('admin.laporan-tridharma') }}" class="flex items-center gap-3 bg-white border border-gray-200 text-gray-700 px-5 py-4 rounded-xl hover:border-del hover:text-del transition-colors shadow-sm">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        <div><p class="text-sm font-semibold">Laporan Tridharma</p><p class="text-xs text-gray-400">Rekap penelitian &amp; PKM per periode</p></div>
    </a>
    <a href="{{ route('admin.internal') }}" class="flex items-center gap-3 bg-white border border-gray-200 text-gray-700 px-5 py-4 rounded-xl hover:border-del hover:text-del transition-colors shadow-sm">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586l5.414 5.414V19a2 2 0 01-2 2z"/></svg>
        <div><p class="text-sm font-semibold">Data Internal</p><p class="text-xs text-gray-400">Lihat data non-publik</p></div>
    </a>
</div>

{{-- Chart aktivitas Tridharma seluruh Fakultas (SAMA dengan chart di halaman publik) --}}
@php
$adminAllZero = max(array_merge($adminChart5['penelitian'], $adminChart5['pengabdian'], $adminChart5['publikasi'])) === 0
              && max(array_merge($adminChartAll['penelitian'], $adminChartAll['pengabdian'], $adminChartAll['publikasi'])) === 0;
@endphp
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-8">
    <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-sm font-semibold text-gray-800">Aktivitas Tridharma — Seluruh Fakultas Vokasi</h2>
            <p class="text-xs text-gray-400 mt-0.5" id="adminChartRangeLabel">5 tahun terakhir</p>
        </div>
        @if(!$adminAllZero)
        <div class="flex items-center gap-2 flex-wrap">
            {{-- Legend (klikable — toggle dataset) --}}
            <div class="flex gap-1 text-xs text-gray-500 mr-2" id="admin-legend">
                @foreach([[0,'bg-blue-500','Penelitian'],[1,'bg-emerald-500','Pengabdian'],[2,'bg-violet-500','Publikasi']] as [$idx,$bg,$lbl])
                <button type="button" onclick="toggleAdminDataset({{ $idx }})" id="admin-legend-{{ $idx }}"
                        class="flex items-center gap-1.5 px-2 py-1 rounded hover:bg-gray-100 transition-all cursor-pointer select-none">
                    <span class="w-2.5 h-2.5 rounded-sm {{ $bg }} flex-shrink-0" id="admin-legend-dot-{{ $idx }}"></span>
                    {{ $lbl }}
                </button>
                @endforeach
            </div>
            {{-- Range toggle (3 pilihan, sama seperti halaman publik) --}}
            <div class="flex items-center bg-gray-100 rounded-lg p-0.5 gap-0.5 text-xs">
                <button type="button" onclick="setAdminRange('5')" id="admin-btn-range-5"
                        class="admin-range-btn active px-2.5 py-1 rounded-md font-medium transition-all">5 Tahun</button>
                <button type="button" onclick="setAdminRange('10')" id="admin-btn-range-10"
                        class="admin-range-btn px-2.5 py-1 rounded-md font-medium transition-all">10 Tahun</button>
                <button type="button" onclick="setAdminRange('all')" id="admin-btn-range-all"
                        class="admin-range-btn px-2.5 py-1 rounded-md font-medium transition-all">Semua</button>
            </div>
            {{-- Chart type toggle (Bar / Line) --}}
            <div class="flex items-center bg-gray-100 rounded-lg p-0.5 gap-0.5">
                <button type="button" onclick="setAdminType('bar')" id="admin-btn-bar"
                        title="Grafik Batang" class="admin-type-btn active p-1.5 rounded-md transition-all">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><rect x="3" y="12" width="4" height="9" rx="1"/><rect x="10" y="7" width="4" height="14" rx="1"/><rect x="17" y="4" width="4" height="17" rx="1"/></svg>
                </button>
                <button type="button" onclick="setAdminType('line')" id="admin-btn-line"
                        title="Grafik Garis" class="admin-type-btn p-1.5 rounded-md transition-all">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="3 17 8 11 13 14 19 6"/>
                        <circle cx="3" cy="17" r="1.5" fill="currentColor" stroke="none"/>
                        <circle cx="8" cy="11" r="1.5" fill="currentColor" stroke="none"/>
                        <circle cx="13" cy="14" r="1.5" fill="currentColor" stroke="none"/>
                        <circle cx="19" cy="6" r="1.5" fill="currentColor" stroke="none"/>
                    </svg>
                </button>
            </div>
        </div>
        @endif
    </div>
    <div class="px-6 py-5">
        @if($adminAllZero)
            <div class="flex flex-col items-center justify-center py-10 text-gray-300">
                <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <p class="text-sm">Belum ada data aktivitas</p>
            </div>
        @else
            <canvas id="adminChart" height="80"></canvas>
        @endif
    </div>
</div>

{{-- Recent dosen --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="text-sm font-semibold text-gray-800">Dosen Terbaru Ditambahkan</h2>
        <a href="{{ route('admin.internal') }}" class="text-xs text-del hover:underline font-medium">Lihat semua →</a>
    </div>
    <div class="divide-y divide-gray-50">
        @forelse($recentDosen as $dosen)
        <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-del/10 border-2 border-del/20 flex items-center justify-center overflow-hidden flex-shrink-0">
                    @if($dosen->photo)
                        <img src="{{ Storage::url($dosen->photo) }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-del font-bold text-sm">{{ strtoupper(substr($dosen->user->name ?? '?', 0, 1)) }}</span>
                    @endif
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-800">{{ $dosen->user->name ?? '-' }}</p>
                    <p class="text-xs text-gray-400">{{ $dosen->studyProgram->name ?? '-' }}{{ $dosen->nidn ? ' · NIDN '.$dosen->nidn : '' }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs px-2.5 py-1 rounded-full font-medium {{ $dosen->is_public ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ $dosen->is_public ? 'Publik' : 'Internal' }}
                </span>
                <a href="{{ route('admin.profil.edit', $dosen->id) }}" class="text-xs text-del hover:underline font-medium">Edit</a>
            </div>
        </div>
        @empty
        <div class="px-6 py-10 text-center text-gray-400 text-sm">Belum ada data dosen.</div>
        @endforelse
    </div>
</div>

@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
@if(!$adminAllZero)
const adminCtx = document.getElementById('adminChart');
let adminChart = null;
let adminRange = '5';
let adminType  = 'bar';

const ADMIN_DATA = {
    '5':   {!! json_encode($adminChart5) !!},
    '10':  {!! json_encode($adminChart10) !!},
    'all': {!! json_encode($adminChartAll) !!},
};
const ADMIN_RANGE_LABEL = { '5': '5 tahun terakhir', '10': '10 tahun terakhir', 'all': 'Semua tahun' };

function buildAdminChart() {
    const d = ADMIN_DATA[adminRange];
    const isBar = adminType === 'bar';
    if (adminChart) adminChart.destroy();
    adminChart = new Chart(adminCtx, {
        type: adminType,
        data: {
            labels: d.years,
            datasets: [
                { label:'Penelitian', data: d.penelitian, backgroundColor: isBar ? 'rgba(99,102,241,0.75)' : 'rgba(99,102,241,0.08)',  borderColor:'rgb(99,102,241)',  borderWidth: isBar ? 0 : 2.5, borderRadius: isBar ? 4 : 0, pointRadius: isBar ? 0 : 4, pointHoverRadius: 6, tension: 0.4, fill: !isBar },
                { label:'Pengabdian', data: d.pengabdian, backgroundColor: isBar ? 'rgba(16,185,129,0.75)' : 'rgba(16,185,129,0.08)', borderColor:'rgb(16,185,129)', borderWidth: isBar ? 0 : 2.5, borderRadius: isBar ? 4 : 0, pointRadius: isBar ? 0 : 4, pointHoverRadius: 6, tension: 0.4, fill: !isBar },
                { label:'Publikasi',  data: d.publikasi,  backgroundColor: isBar ? 'rgba(139,92,246,0.75)'  : 'rgba(139,92,246,0.08)',  borderColor:'rgb(139,92,246)',  borderWidth: isBar ? 0 : 2.5, borderRadius: isBar ? 4 : 0, pointRadius: isBar ? 0 : 4, pointHoverRadius: 6, tension: 0.4, fill: !isBar },
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: { backgroundColor:'#1e293b', padding:10, cornerRadius:8, mode:'index', intersect:false }
            },
            scales: {
                x: { grid: { display: true, color:'#f3f4f6', lineWidth:0.5 }, border:{ display:false } },
                y: { beginAtZero:true, ticks:{ stepSize:1, precision:0 }, grid:{ color:'#e5e7eb', lineWidth:0.5 }, border:{ display:false } }
            },
            interaction: { mode:'index', intersect:false },
        }
    });
    // Re-apply hidden state pakai getDatasetMeta (reliable)
    if (typeof _adminHidden !== 'undefined') {
        _adminHidden.forEach((hidden, i) => {
            const meta = adminChart.getDatasetMeta(i);
            if (meta) meta.hidden = hidden;
        });
        if (_adminHidden.some(h => h)) adminChart.update();
    }
}

function setAdminRange(range) {
    adminRange = range;
    document.querySelectorAll('.admin-range-btn').forEach(b => {
        b.classList.remove('bg-white','text-del','shadow-sm');
        b.classList.add('text-gray-400');
    });
    const btn = document.getElementById('admin-btn-range-' + range);
    btn.classList.remove('text-gray-400');
    btn.classList.add('bg-white','text-del','shadow-sm');
    document.getElementById('adminChartRangeLabel').textContent = ADMIN_RANGE_LABEL[range];
    buildAdminChart();
}

function setAdminType(type) {
    adminType = type;
    document.querySelectorAll('.admin-type-btn').forEach(b => {
        b.classList.remove('bg-white','text-del','shadow-sm');
        b.classList.add('text-gray-400');
    });
    const btn = document.getElementById('admin-btn-' + type);
    btn.classList.remove('text-gray-400');
    btn.classList.add('bg-white','text-del','shadow-sm');
    buildAdminChart();
}


// ─── Legend toggle ───────────────────────────────────────────────
const _adminHidden = [false, false, false];
function toggleAdminDataset(index) {
    _adminHidden[index] = !_adminHidden[index];
    const btn = document.getElementById('admin-legend-' + index);
    const dot = document.getElementById('admin-legend-dot-' + index);
    btn.classList.toggle('opacity-40', _adminHidden[index]);
    dot.classList.toggle('opacity-30', _adminHidden[index]);
    if (adminChart) {
        const meta = adminChart.getDatasetMeta(index);
        if (meta) meta.hidden = _adminHidden[index];
        adminChart.update();
    }
}

buildAdminChart();
@endif
</script>
@endpush
