@extends('layouts.dashboard')
@section('title', 'Hierarki Kaprodi')
@section('page-title', 'Hierarki Program Studi')
@section('content')
<div class="pt-4 space-y-4">
    @foreach($studyPrograms as $prodi)
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <div class="flex items-start justify-between gap-6">
            <div>
                <h3 class="font-bold text-gray-800 text-base">{{ $prodi->name }}</h3>
                <p class="text-sm text-gray-500 mt-1">
                    Kaprodi saat ini: <strong>{{ $prodi->headLecturer?->user->name ?? 'Belum ditentukan' }}</strong>
                </p>
                <p class="text-xs text-gray-400 mt-1">{{ $prodi->lecturers->count() }} dosen terdaftar</p>
            </div>
            <form action="{{ route('admin.hierarki.update', $prodi->id) }}" method="POST" class="flex items-center gap-3 flex-shrink-0">
                @csrf @method('PUT')
                <select name="head_lecturer_id" class="px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                    <option value="">-- Pilih Kaprodi --</option>
                    @foreach($lecturers as $lec)
                        <option value="{{ $lec->id }}" {{ $prodi->head_lecturer_id == $lec->id ? 'selected' : '' }}>
                            {{ $lec->user->name }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="bg-del text-white px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-del-light">Simpan</button>
            </form>
        </div>
    </div>
    @endforeach
</div>
@endsection
