@extends('layouts.dashboard')
@section('title', 'Laporan Tridharma')
@section('page-title', 'Laporan Tridharma')
@section('content')

@php
    $bulanNama = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];
    $periodeLabel = [
        'semua'     => 'Semua',
        'bulan_ini' => 'Bulan Ini',
        '3_bulan'   => '3 Bulan Terakhir',
        'tahun_ini' => 'Tahun Ini',
        'custom'    => 'Kustom',
    ][$periode] ?? 'Tahun Ini';
    $isAllTime = $isAllTime ?? ($periode === 'semua');
@endphp

<div class="pt-4 space-y-6">

    {{-- Info periode aktif --}}
    <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 text-sm text-del flex items-start gap-2">
        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p>
            Menampilkan data penelitian &amp; PKM dengan periode <strong>{{ $periodeLabel }}</strong>
            @if($isAllTime)
                &mdash; <strong>semua data tanpa batas tanggal</strong>.
            @else
                &mdash; {{ $bulanNama[$startMonth] ?? $startMonth }} {{ $startYear }}
                s/d {{ $bulanNama[$endMonth] ?? $endMonth }} {{ $endYear }}.
            @endif
            Filter berdasarkan <strong>tahun &amp; bulan asli</strong> kegiatan, bukan tanggal input.
            @if(auth()->user()->isKaprodi())
                Sebagai <strong>Kaprodi</strong>, data dibatasi hanya program studi Anda.
            @endif
        </p>
    </div>

    {{-- Filter periode --}}
    <form action="{{ route('admin.laporan-tridharma') }}" method="GET"
          class="bg-white rounded-2xl border border-gray-100 p-5 space-y-4">
        <div class="flex flex-wrap items-end gap-3">
            <div>
                <label class="text-xs font-medium text-gray-500 block mb-1.5">Periode</label>
                <select name="periode" onchange="toggleCustom(this.value)"
                        class="px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30 bg-white">
                    <option value="semua"     {{ $periode === 'semua'     ? 'selected' : '' }}>Semua</option>
                    <option value="bulan_ini" {{ $periode === 'bulan_ini' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="3_bulan"   {{ $periode === '3_bulan'   ? 'selected' : '' }}>3 Bulan Terakhir</option>
                    <option value="tahun_ini" {{ $periode === 'tahun_ini' ? 'selected' : '' }}>Tahun Ini</option>
                    <option value="custom"    {{ $periode === 'custom'    ? 'selected' : '' }}>Kustom</option>
                </select>
            </div>

            {{-- Opsi custom: tahun + bulan dari/sampai --}}
            <div id="customFields" class="{{ $periode === 'custom' ? 'flex' : 'hidden' }} flex-wrap items-end gap-3">
                <div>
                    <label class="text-xs font-medium text-gray-500 block mb-1.5">Tahun</label>
                    <input type="number" name="tahun" min="1970" max="{{ date('Y') }}"
                           value="{{ request('tahun', $startYear) }}"
                           class="w-28 px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-500 block mb-1.5">Bulan Dari</label>
                    <select name="bulan_dari" class="px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30 bg-white">
                        @foreach($bulanNama as $num => $nama)
                            <option value="{{ $num }}" {{ (int) request('bulan_dari', $startMonth) === $num ? 'selected' : '' }}>{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-500 block mb-1.5">Bulan Sampai</label>
                    <select name="bulan_sampai" class="px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30 bg-white">
                        @foreach($bulanNama as $num => $nama)
                            <option value="{{ $num }}" {{ (int) request('bulan_sampai', $endMonth) === $num ? 'selected' : '' }}>{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <button type="submit" class="bg-del text-white px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-del-light transition-colors whitespace-nowrap">
                Terapkan
            </button>

            {{-- Export mengikuti filter yang sedang aktif (query string yang sama) --}}
            <a href="{{ route('admin.laporan-tridharma.export.pdf', request()->query()) }}"
               class="px-4 py-2.5 rounded-xl text-sm font-medium border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors inline-flex items-center gap-2">
                Export PDF
            </a>
        </div>
    </form>

    {{-- Tabel Penelitian --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-50 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-800">Penelitian</h2>
            <span class="text-xs text-gray-400">{{ $penelitian->count() }} data</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3 text-left">Nama Dosen</th>
                        <th class="px-5 py-3 text-left">Prodi</th>
                        <th class="px-5 py-3 text-left">Judul</th>
                        <th class="px-5 py-3 text-left">Tahun</th>
                        <th class="px-5 py-3 text-left">Bulan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($penelitian as $item)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-3 font-medium text-gray-800">{{ $item->lecturer->user->name ?? '-' }}</td>
                        <td class="px-5 py-3 text-gray-500 text-xs">{{ $item->lecturer->studyProgram->name ?? '-' }}</td>
                        <td class="px-5 py-3 text-gray-700">{{ $item->title }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ $item->year }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ $item->month ? ($bulanNama[$item->month] ?? $item->month) : '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-10 text-center text-gray-400 text-sm">Tidak ada data pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Tabel PKM / Pengabdian --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-50 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-800">PKM / Pengabdian</h2>
            <span class="text-xs text-gray-400">{{ $pkm->count() }} data</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3 text-left">Nama Dosen</th>
                        <th class="px-5 py-3 text-left">Prodi</th>
                        <th class="px-5 py-3 text-left">Judul</th>
                        <th class="px-5 py-3 text-left">Tahun</th>
                        <th class="px-5 py-3 text-left">Bulan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($pkm as $item)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-3 font-medium text-gray-800">
                            {{ $item->lecturers->map(fn($l) => $l->user->name . ' (' . $l->pivot->role . ')')->join(', ') ?: '-' }}
                        </td>
                        <td class="px-5 py-3 text-gray-500 text-xs">
                            {{ $item->lecturers->map(fn($l) => $l->studyProgram->name ?? '-')->unique()->join(', ') ?: '-' }}
                        </td>
                        <td class="px-5 py-3 text-gray-700">{{ $item->title }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ $item->year }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ $item->month ? ($bulanNama[$item->month] ?? $item->month) : '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-10 text-center text-gray-400 text-sm">Tidak ada data pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function toggleCustom(value) {
        const box = document.getElementById('customFields');
        if (!box) return;
        box.classList.toggle('hidden', value !== 'custom');
        box.classList.toggle('flex', value === 'custom');
    }
</script>
@endpush
@endsection
