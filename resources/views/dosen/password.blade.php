@extends('layouts.dashboard')
@section('title', 'Ubah Password')
@section('page-title', 'Ubah Password')
@section('content')
<div class="pt-4 max-w-md">
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <form action="{{ route('dosen.password.update') }}" method="POST" class="space-y-5">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Password Saat Ini</label>
                <div class="relative">
                    <input type="password" id="pwd_current" name="current_password" required class="w-full px-4 py-2.5 pr-11 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                    <button type="button" onclick="togglePasswordVisibility('pwd_current', this)" tabindex="-1" aria-label="Tampilkan/sembunyikan password" class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600">
                        <svg class="eye-icon-open w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M10 3.5c-4.5 0-8.05 3-9.5 6.5 1.45 3.5 5 6.5 9.5 6.5s8.05-3 9.5-6.5C18.05 6.5 14.5 3.5 10 3.5zM10 14a4 4 0 110-8 4 4 0 010 8z"/></svg>
                        <svg class="eye-icon-closed w-5 h-5 hidden" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3.28 2.22a.75.75 0 00-1.06 1.06l14.5 14.5a.75.75 0 101.06-1.06l-1.745-1.745a10.029 10.029 0 003.3-4.38 1.651 1.651 0 000-1.185A10.004 10.004 0 009.999 3a9.956 9.956 0 00-4.744 1.194L3.28 2.22zM7.752 6.69l1.092 1.092a2.5 2.5 0 013.374 3.373l1.091 1.092a4 4 0 00-5.557-5.557z" clip-rule="evenodd"/><path d="M10.748 13.93l2.523 2.523a9.987 9.987 0 01-3.27.547c-4.258 0-7.894-2.66-9.337-6.41a1.651 1.651 0 010-1.186A10.007 10.007 0 012.839 6.02L6.07 9.252a4 4 0 004.678 4.678z"/></svg>
                    </button>
                </div>
                @error('current_password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Password Baru</label>
                <div class="relative">
                    <input type="password" id="pwd_new" name="password" required minlength="8" class="w-full px-4 py-2.5 pr-11 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                    <button type="button" onclick="togglePasswordVisibility('pwd_new', this)" tabindex="-1" aria-label="Tampilkan/sembunyikan password" class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600">
                        <svg class="eye-icon-open w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M10 3.5c-4.5 0-8.05 3-9.5 6.5 1.45 3.5 5 6.5 9.5 6.5s8.05-3 9.5-6.5C18.05 6.5 14.5 3.5 10 3.5zM10 14a4 4 0 110-8 4 4 0 010 8z"/></svg>
                        <svg class="eye-icon-closed w-5 h-5 hidden" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3.28 2.22a.75.75 0 00-1.06 1.06l14.5 14.5a.75.75 0 101.06-1.06l-1.745-1.745a10.029 10.029 0 003.3-4.38 1.651 1.651 0 000-1.185A10.004 10.004 0 009.999 3a9.956 9.956 0 00-4.744 1.194L3.28 2.22zM7.752 6.69l1.092 1.092a2.5 2.5 0 013.374 3.373l1.091 1.092a4 4 0 00-5.557-5.557z" clip-rule="evenodd"/><path d="M10.748 13.93l2.523 2.523a9.987 9.987 0 01-3.27.547c-4.258 0-7.894-2.66-9.337-6.41a1.651 1.651 0 010-1.186A10.007 10.007 0 012.839 6.02L6.07 9.252a4 4 0 004.678 4.678z"/></svg>
                    </button>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Konfirmasi Password Baru</label>
                <div class="relative">
                    <input type="password" id="pwd_confirm" name="password_confirmation" required class="w-full px-4 py-2.5 pr-11 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                    <button type="button" onclick="togglePasswordVisibility('pwd_confirm', this)" tabindex="-1" aria-label="Tampilkan/sembunyikan password" class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600">
                        <svg class="eye-icon-open w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M10 3.5c-4.5 0-8.05 3-9.5 6.5 1.45 3.5 5 6.5 9.5 6.5s8.05-3 9.5-6.5C18.05 6.5 14.5 3.5 10 3.5zM10 14a4 4 0 110-8 4 4 0 010 8z"/></svg>
                        <svg class="eye-icon-closed w-5 h-5 hidden" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3.28 2.22a.75.75 0 00-1.06 1.06l14.5 14.5a.75.75 0 101.06-1.06l-1.745-1.745a10.029 10.029 0 003.3-4.38 1.651 1.651 0 000-1.185A10.004 10.004 0 009.999 3a9.956 9.956 0 00-4.744 1.194L3.28 2.22zM7.752 6.69l1.092 1.092a2.5 2.5 0 013.374 3.373l1.091 1.092a4 4 0 00-5.557-5.557z" clip-rule="evenodd"/><path d="M10.748 13.93l2.523 2.523a9.987 9.987 0 01-3.27.547c-4.258 0-7.894-2.66-9.337-6.41a1.651 1.651 0 010-1.186A10.007 10.007 0 012.839 6.02L6.07 9.252a4 4 0 004.678 4.678z"/></svg>
                    </button>
                </div>
            </div>
            <button type="submit" class="w-full bg-del text-white py-2.5 rounded-xl text-sm font-semibold hover:bg-del-light transition-colors">
                Perbarui Password
            </button>
        </form>
    </div>
</div>
@endsection
