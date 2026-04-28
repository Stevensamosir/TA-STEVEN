@extends('layouts.dashboard')
@section('title', 'Tambah Dosen')
@section('page-title', 'Tambah Akun Dosen')
@section('content')
<div class="pt-4 max-w-2xl">
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <form action="{{ route('admin.dosen.store') }}" method="POST" class="space-y-5">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap *</label>
                    <input type="text" name="name" required value="{{ old('name') }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email *</label>
                    <input type="email" name="email" required value="{{ old('email') }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Password *</label>
                    <input type="password" name="password" required minlength="8" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Role *</label>
                    <select name="role" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                        <option value="dosen">Dosen</option>
                        <option value="kaprodi">Kaprodi</option>
                        <option value="dekan">Dekan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Program Studi *</label>
                    <select name="study_program_id" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                        @foreach($studyPrograms as $prodi)
                            <option value="{{ $prodi->id }}">{{ $prodi->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">NIDN</label>
                    <input type="text" name="nidn" value="{{ old('nidn') }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Kepakaran</label>
                <textarea name="expertise" rows="2" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30 resize-none">{{ old('expertise') }}</textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="bg-del text-white px-6 py-2.5 rounded-xl text-sm font-semibold hover:bg-del-light">Buat Akun</button>
                <a href="{{ route('admin.dosen') }}" class="px-6 py-2.5 rounded-xl text-sm text-gray-600 border border-gray-200 hover:bg-gray-50">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
