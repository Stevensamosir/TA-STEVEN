@extends('layouts.dashboard')
@section('title', 'Edit Dosen')
@section('page-title', 'Edit Data Dosen')
@section('content')
<div class="pt-4 max-w-2xl">
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <form action="{{ route('admin.dosen.update', $lecturer->id) }}" method="POST" class="space-y-5">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $lecturer->user->name) }}" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email', $lecturer->user->email) }}" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Role</label>
                    <select name="role" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none">
                        @foreach(['dosen','kaprodi','dekan'] as $r)
                            <option value="{{ $r }}" {{ $lecturer->user->role === $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Program Studi</label>
                    <select name="study_program_id" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none">
                        @foreach($studyPrograms as $prodi)
                            <option value="{{ $prodi->id }}" {{ $lecturer->study_program_id === $prodi->id ? 'selected' : '' }}>{{ $prodi->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">NIDN</label>
                    <input type="text" name="nidn" value="{{ old('nidn', $lecturer->nidn) }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Kepakaran</label>
                <textarea name="expertise" rows="2" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none resize-none">{{ old('expertise', $lecturer->expertise) }}</textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="bg-del text-white px-6 py-2.5 rounded-xl text-sm font-semibold hover:bg-del-light">Simpan</button>
                <a href="{{ route('admin.dosen') }}" class="px-6 py-2.5 rounded-xl text-sm text-gray-600 border border-gray-200 hover:bg-gray-50">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
