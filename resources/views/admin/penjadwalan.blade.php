@extends('layouts.dashboard')
@section('title', 'Penjadwalan Dosen')
@section('page-title', 'Monitoring Jadwal Dosen')

@section('content')
<div class="pt-4 space-y-4">
    <div class="bg-blue-50 border border-blue-100 text-del px-4 py-3 rounded-xl text-sm">
        Halaman ini menampilkan jadwal ketersediaan pribadi seluruh dosen
        @if(auth()->user()->isKaprodi()) di program studi Anda @else Fakultas Vokasi @endif
        (view-only).
    </div>

    @if(auth()->user()->isDekan())
    <form action="{{ route('admin.penjadwalan') }}" method="GET" class="flex gap-3">
        <select name="prodi" onchange="this.form.submit()" class="px-3 py-2.5 border border-gray-200 rounded-xl text-sm bg-white">
            <option value="">Semua Program Studi</option>
            @foreach($studyPrograms as $sp)
                <option value="{{ $sp->id }}" {{ request('prodi') == $sp->id ? 'selected' : '' }}>{{ $sp->name }}</option>
            @endforeach
        </select>
    </form>
    @endif

    @forelse($lecturers as $lecturer)
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="px-5 py-3 bg-gray-50 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800 text-sm">{{ $lecturer->user->name }}</h3>
            <p class="text-xs text-gray-500">{{ $lecturer->studyProgram->name ?? '-' }}</p>
        </div>
        <div class="p-4">
            @if($lecturer->schedules->isEmpty())
                <p class="text-xs text-gray-400">Belum ada jadwal yang diisi.</p>
            @else
                <div class="flex flex-wrap gap-2">
                    @foreach($lecturer->schedules as $j)
                        <span class="text-xs px-2.5 py-1 rounded-lg border {{ $j->status === 'Tersedia' ? 'border-green-200 bg-green-50 text-green-700' : 'border-gray-200 bg-gray-50 text-gray-500' }}">
                            {{ $j->day }} {{ substr($j->start_time,0,5) }}-{{ substr($j->end_time,0,5) }}
                        </span>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    @empty
        <div class="bg-white rounded-2xl border border-gray-100 px-5 py-10 text-center text-sm text-gray-400">
            Tidak ada dosen di scope ini.
        </div>
    @endforelse
</div>
@endsection
