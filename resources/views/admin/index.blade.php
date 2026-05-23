@extends('layouts.dashboard')
@section('title','Dashboard Admin')
@section('page-title','Dashboard Admin')
@section('content')

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

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    <a href="{{ route('admin.dosen') }}" class="flex items-center gap-3 bg-del text-white px-5 py-4 rounded-xl hover:bg-del-light transition-colors shadow-sm">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        <div><p class="text-sm font-semibold">Kelola Dosen</p><p class="text-xs text-blue-200">Tambah / edit akun dosen</p></div>
    </a>
    <a href="{{ route('admin.hierarki') }}" class="flex items-center gap-3 bg-white border border-gray-200 text-gray-700 px-5 py-4 rounded-xl hover:border-del hover:text-del transition-colors shadow-sm">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/></svg>
        <div><p class="text-sm font-semibold">Hierarki Kaprodi</p><p class="text-xs text-gray-400">Atur Kaprodi per prodi</p></div>
    </a>
    <a href="{{ route('admin.internal') }}" class="flex items-center gap-3 bg-white border border-gray-200 text-gray-700 px-5 py-4 rounded-xl hover:border-del hover:text-del transition-colors shadow-sm">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586l5.414 5.414V19a2 2 0 01-2 2z"/></svg>
        <div><p class="text-sm font-semibold">Data Internal</p><p class="text-xs text-gray-400">Lihat data non-publik</p></div>
    </a>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="text-sm font-semibold text-gray-800">Dosen Terbaru Ditambahkan</h2>
        <a href="{{ route('admin.dosen') }}" class="text-xs text-del hover:underline font-medium">Lihat semua →</a>
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
                <a href="{{ route('admin.dosen.edit', $dosen->id) }}" class="text-xs text-del hover:underline font-medium">Edit</a>
            </div>
        </div>
        @empty
        <div class="px-6 py-10 text-center text-gray-400 text-sm">Belum ada data dosen.</div>
        @endforelse
    </div>
</div>
@endsection
