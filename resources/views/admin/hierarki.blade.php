@extends('layouts.dashboard')
@section('title', 'Hierarki Kaprodi')
@section('page-title', 'Hierarki Program Studi')
@section('content')
<div class="pt-4 space-y-4">

    @if($errors->has('head_lecturer_id'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
            {{ $errors->first('head_lecturer_id') }}
        </div>
    @endif

    @foreach($studyPrograms as $prodi)
    <div class="bg-white rounded-2xl border border-gray-100 p-5 sm:p-6">
        <div class="mb-4">
            <h3 class="font-bold text-gray-800 text-base">{{ $prodi->name }}</h3>
            <p class="text-sm text-gray-500 mt-1">
                Kaprodi saat ini: <strong>{{ $prodi->headLecturer?->user->name ?? 'Belum ditentukan' }}</strong>
            </p>
            <p class="text-xs text-gray-400 mt-0.5">{{ $prodi->lecturers->count() }} dosen terdaftar</p>
        </div>
        <form action="{{ route('admin.hierarki.update', $prodi->id) }}" method="POST"
              class="flex flex-col sm:flex-row gap-2 sm:items-center">
            @csrf @method('PUT')
            <select name="head_lecturer_id"
                class="flex-1 w-full sm:max-w-xs px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30 bg-white">
                <option value="">-- Tidak ada Kaprodi --</option>
                @foreach($lecturers->where('study_program_id', $prodi->id) as $lec)
                    <option value="{{ $lec->id }}" {{ $prodi->head_lecturer_id == $lec->id ? 'selected' : '' }}>
                        {{ $lec->user->name }} {{ !$lec->user->is_active ? '(nonaktif)' : '' }}
                    </option>
                @endforeach
            </select>
            <button type="submit"
                class="w-full sm:w-auto bg-del text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-del-light text-center transition-colors">
                Simpan
            </button>
        </form>
    </div>
    @endforeach
</div>
@endsection
