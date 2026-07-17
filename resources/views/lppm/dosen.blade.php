@extends('layouts.lppm')
@section('title', 'Daftar Dosen')
@section('content')

<div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-sm text-del flex items-start gap-2">
    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <p>
        Daftar seluruh dosen aktif <strong>Fakultas Vokasi</strong>. Klik nama dosen untuk melihat
        detail Tridharma-nya (termasuk data internal yang tidak dipublikasikan).
    </p>
</div>

{{-- Pencarian (nama/NIDN) + filter program studi.
     Mengikuti field pencarian yang sama dengan pencarian dosen LPPM (nama & NIDN),
     memakai form GET seperti pola daftar/filter di halaman Data Internal. --}}
<form action="{{ route('lppm.daftar-dosen') }}" method="GET" class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">
    <div class="relative flex-1 max-w-md">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/></svg>
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cari nama atau NIDN dosen..."
               class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
    </div>
    <select name="prodi" class="px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30 bg-white">
        <option value="">Semua Program Studi</option>
        @foreach($studyPrograms as $sp)
            <option value="{{ $sp->id }}" {{ request('prodi') == $sp->id ? 'selected' : '' }}>
                {{ $sp->name }}
            </option>
        @endforeach
    </select>
    <button type="submit" class="bg-del text-white px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-del-light transition-colors whitespace-nowrap">
        Filter
    </button>
    @if(request()->hasAny(['search','prodi']))
        <a href="{{ route('lppm.daftar-dosen') }}" class="px-4 py-2.5 rounded-xl text-sm font-medium border border-gray-200 text-gray-500 hover:bg-gray-50 transition-colors whitespace-nowrap">
            Reset
        </a>
    @endif
</form>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 bg-gray-50">
        <h2 class="text-sm font-semibold text-gray-700">Dosen Fakultas Vokasi</h2>
        <span class="text-xs text-gray-500">{{ $lecturers->count() }} dosen</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100">
                    <th class="px-5 py-3">Nama</th>
                    <th class="px-5 py-3">NIDN</th>
                    <th class="px-5 py-3">Program Studi</th>
                    <th class="px-5 py-3">Jabatan Fungsional</th>
                    <th class="px-5 py-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($lecturers as $lecturer)
                <tr class="hover:bg-gray-50/60 transition-colors">
                    <td class="px-5 py-3">
                        <a href="{{ route('lppm.daftar-dosen.show', $lecturer->id) }}"
                           class="font-medium text-gray-800 hover:text-del hover:underline">
                            {{ $lecturer->user->name }}
                        </a>
                    </td>
                    <td class="px-5 py-3 text-gray-600">{{ $lecturer->nidn ?? '-' }}</td>
                    <td class="px-5 py-3 text-gray-600">{{ $lecturer->studyProgram->name ?? '-' }}</td>
                    <td class="px-5 py-3 text-gray-600">{{ $lecturer->jabatan_fungsional ?? '-' }}</td>
                    <td class="px-5 py-3">
                        @if($lecturer->user->is_active)
                            <span class="text-xs font-medium px-2 py-1 rounded-full bg-green-50 text-green-700">Aktif</span>
                        @else
                            <span class="text-xs font-medium px-2 py-1 rounded-full bg-gray-100 text-gray-500">Nonaktif</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-10 text-center text-sm text-gray-400">
                        Tidak ada dosen yang cocok dengan pencarian.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
