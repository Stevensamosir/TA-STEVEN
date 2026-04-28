@extends('layouts.dashboard')
@section('title', 'Data Internal')
@section('page-title', 'Data Internal Semua Dosen')
@section('content')
<div class="pt-4 space-y-4">
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-sm text-del flex items-start gap-2">
        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p>Halaman ini menampilkan <strong>semua data</strong> termasuk data yang bersifat internal (tidak publik). Hanya dapat diakses oleh Dekan dan Kaprodi.</p>
    </div>

    @foreach($lecturers as $lecturer)
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 bg-gray-50 border-b border-gray-100">
            <div>
                <h3 class="font-semibold text-gray-800">{{ $lecturer->user->name }}</h3>
                <p class="text-xs text-gray-500">{{ $lecturer->studyProgram->name ?? '-' }} · NIDN: {{ $lecturer->nidn ?? '-' }}</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs font-medium px-2 py-1 rounded-full {{ $lecturer->is_public ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ $lecturer->is_public ? 'Profil Publik' : 'Profil Internal' }}
                </span>
                <a href="{{ route('admin.dosen.edit', $lecturer->id) }}" class="text-xs text-del hover:underline">Edit</a>
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
