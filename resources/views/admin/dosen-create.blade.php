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
                    <div class="relative">
                        <input type="password" id="pwd_create" name="password" required minlength="8" class="w-full px-4 py-2.5 pr-11 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                        <button type="button" onclick="togglePasswordVisibility('pwd_create', this)" tabindex="-1" aria-label="Tampilkan/sembunyikan password" class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600">
                            <svg class="eye-icon-open w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M10 3.5c-4.5 0-8.05 3-9.5 6.5 1.45 3.5 5 6.5 9.5 6.5s8.05-3 9.5-6.5C18.05 6.5 14.5 3.5 10 3.5zM10 14a4 4 0 110-8 4 4 0 010 8z"/></svg>
                            <svg class="eye-icon-closed w-5 h-5 hidden" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3.28 2.22a.75.75 0 00-1.06 1.06l14.5 14.5a.75.75 0 101.06-1.06l-1.745-1.745a10.029 10.029 0 003.3-4.38 1.651 1.651 0 000-1.185A10.004 10.004 0 009.999 3a9.956 9.956 0 00-4.744 1.194L3.28 2.22zM7.752 6.69l1.092 1.092a2.5 2.5 0 013.374 3.373l1.091 1.092a4 4 0 00-5.557-5.557z" clip-rule="evenodd"/><path d="M10.748 13.93l2.523 2.523a9.987 9.987 0 01-3.27.547c-4.258 0-7.894-2.66-9.337-6.41a1.651 1.651 0 010-1.186A10.007 10.007 0 012.839 6.02L6.07 9.252a4 4 0 004.678 4.678z"/></svg>
                        </button>
                    </div>
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
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Jabatan Fungsional
                </label>
                <select name="jabatan_fungsional"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30 bg-white">
                    <option value="">— Pilih (opsional) —</option>
                    @foreach(App\Models\Lecturer::jabatanFungsionalOptions() as $jf)
                        <option value="{{ $jf }}" {{ old('jabatan_fungsional') === $jf ? 'selected' : '' }}>{{ $jf }}</option>
                    @endforeach
                </select>
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

