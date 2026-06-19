@extends('layouts.dashboard')
@section('title', 'Data Internal')
@section('page-title', 'Data Internal Semua Dosen')
@section('content')

@php
    $authUser  = auth()->user();
    $isDekan   = $authUser->isDekan();
    $isKaprodi = $authUser->isKaprodi();
@endphp

<div class="pt-4 space-y-4">
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-sm text-del flex items-start gap-2">
        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p>
            Halaman ini menampilkan <strong>semua data</strong> termasuk data internal (tidak publik).
            @if($isDekan)
                Sebagai <strong>Dekan</strong>, Anda dapat mengedit profil semua dosen.
            @elseif($isKaprodi)
                Sebagai <strong>Kaprodi</strong>, Anda hanya dapat mengedit profil dosen di program studi Anda.
            @endif
        </p>
    </div>

    @foreach($lecturers as $lecturer)
    @php
        // Tentukan apakah user yang login boleh mengedit dosen ini
        $canEdit = false;
        if ($isDekan) {
            $canEdit = true;
        } elseif ($isKaprodi) {
            $isDosen      = $lecturer->user->role === 'dosen';
            $sameProdi    = $lecturer->study_program_id === $myProdiId;
            $isOwnAccount = $lecturer->user_id === $authUser->id;
            // Kaprodi boleh edit: dosen di prodinya + profilnya sendiri
            // Tidak boleh edit: dekan, kaprodi lain, dosen prodi lain
            $canEdit = ($isDosen && $sameProdi) || $isOwnAccount;
        }
    @endphp

    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 bg-gray-50 border-b border-gray-100">
            <div>
                <h3 class="font-semibold text-gray-800">{{ $lecturer->user->name }}</h3>
                <p class="text-xs text-gray-500 mt-0.5">
                    {{ $lecturer->studyProgram->name ?? '-' }} ·
                    NIDN: {{ $lecturer->nidn ?? '-' }} ·
                    <span class="font-medium
                        {{ $lecturer->user->role === 'dekan'   ? 'text-red-600'    :
                          ($lecturer->user->role === 'kaprodi' ? 'text-yellow-600' : 'text-del') }}">
                        {{ ucfirst($lecturer->user->role) }}
                    </span>
                </p>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs font-medium px-2 py-1 rounded-full
                    {{ $lecturer->is_public ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ $lecturer->is_public ? 'Profil Publik' : 'Profil Internal' }}
                </span>

                {{-- Tombol Edit hanya muncul jika user berhak --}}
                {{-- Route ini sekarang satu nama saja (admin.profil.edit), dibuka untuk
                     Dekan & Kaprodi; pembatasan rinci dijaga assertKaprodiCanEdit() --}}
                @if($canEdit)
                    <a href="{{ route('admin.profil.edit', $lecturer->id) }}"
                       class="text-xs text-del hover:underline font-medium">
                        Edit
                    </a>
                @endif
            </div>
        </div>

        <div class="p-4 grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="bg-blue-50 rounded-xl p-3 text-center">
                <div class="font-bold text-del">{{ $lecturer->educations->count() }}</div>
                <div class="text-xs text-gray-500">Pendidikan</div>
                <div class="text-xs text-gray-400">{{ $lecturer->educations->where('visibility','private')->count() }} internal</div>
            </div>
            <div class="bg-indigo-50 rounded-xl p-3 text-center">
                <div class="font-bold text-indigo-700">{{ $lecturer->researches->count() }}</div>
                <div class="text-xs text-gray-500">Penelitian</div>
                <div class="text-xs text-gray-400">{{ $lecturer->researches->where('visibility','private')->count() }} internal</div>
            </div>
            <div class="bg-green-50 rounded-xl p-3 text-center">
                <div class="font-bold text-green-700">{{ $lecturer->communityServices->count() }}</div>
                <div class="text-xs text-gray-500">Pengabdian</div>
                <div class="text-xs text-gray-400">{{ $lecturer->communityServices->where('visibility','private')->count() }} internal</div>
            </div>
            <div class="bg-purple-50 rounded-xl p-3 text-center">
                <div class="font-bold text-purple-700">{{ $lecturer->publications->count() }}</div>
                <div class="text-xs text-gray-500">Publikasi</div>
                <div class="text-xs text-gray-400">{{ $lecturer->publications->where('visibility','private')->count() }} internal</div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection

