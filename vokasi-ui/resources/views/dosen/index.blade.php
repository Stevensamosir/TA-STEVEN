@extends('layouts.dashboard')
@section('title', 'Dashboard Dosen')
@section('page-title', 'Dashboard')

@section('content')
<div class="pt-4 space-y-6">

    <!-- Welcome -->
    <div class="bg-gradient-to-r from-del to-del-light rounded-2xl p-6 text-white">
        <p class="text-blue-200 text-sm mb-1">Selamat datang,</p>
        <h2 class="text-xl font-bold mb-1">{{ auth()->user()->name }}</h2>
        <p class="text-blue-200 text-sm">{{ $lecturer->studyProgram->name ?? 'Fakultas Vokasi' }} · NIDN: {{ $lecturer->nidn ?? '-' }}</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['label' => 'Pendidikan', 'value' => $stats['pendidikan'], 'color' => 'bg-blue-50 text-del', 'href' => route('dosen.pendidikan')],
            ['label' => 'Penelitian', 'value' => $stats['penelitian'], 'color' => 'bg-indigo-50 text-indigo-700', 'href' => route('dosen.penelitian')],
            ['label' => 'Pengabdian', 'value' => $stats['pengabdian'], 'color' => 'bg-green-50 text-green-700', 'href' => route('dosen.pengabdian')],
            ['label' => 'Publikasi', 'value' => $stats['publikasi'], 'color' => 'bg-purple-50 text-purple-700', 'href' => route('dosen.publikasi')],
        ] as $stat)
        <a href="{{ $stat['href'] }}" class="bg-white rounded-2xl border border-gray-100 p-5 hover:shadow-md transition-all">
            <div class="text-3xl font-bold {{ explode(' ', $stat['color'])[1] }}">{{ $stat['value'] }}</div>
            <div class="text-sm text-gray-500 mt-1">{{ $stat['label'] }}</div>
        </a>
        @endforeach
    </div>

    <!-- Status Profil -->
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-semibold text-gray-800 mb-1">Status Profil</h3>
                <p class="text-sm text-gray-500">Kontrol visibilitas profil publik Anda</p>
            </div>
            <div class="flex items-center gap-3">
                @if($lecturer->is_public)
                    <span class="inline-flex items-center gap-1.5 bg-green-50 text-green-700 text-sm font-medium px-3 py-1.5 rounded-full">
                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                        Publik
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-600 text-sm font-medium px-3 py-1.5 rounded-full">
                        <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                        Internal
                    </span>
                @endif
                <a href="{{ route('dosen.profil.edit') }}" class="text-sm text-del font-medium hover:underline">Ubah</a>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <a href="{{ route('dosen.profil.edit') }}" class="bg-white border border-gray-100 rounded-2xl p-5 hover:shadow-md transition-all flex items-center gap-4">
            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-del" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <div>
                <p class="font-semibold text-gray-800 text-sm">Edit Profil</p>
                <p class="text-xs text-gray-400">Perbarui foto dan kepakaran</p>
            </div>
        </a>
        <a href="{{ route('public.dosen.show', $lecturer->id) }}" target="_blank" class="bg-white border border-gray-100 rounded-2xl p-5 hover:shadow-md transition-all flex items-center gap-4">
            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-del" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </div>
            <div>
                <p class="font-semibold text-gray-800 text-sm">Lihat Profil Publik</p>
                <p class="text-xs text-gray-400">Tampilan yang dilihat pengunjung</p>
            </div>
        </a>
    </div>
</div>
@endsection
