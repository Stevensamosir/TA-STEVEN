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
                <input type="password" name="current_password" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                @error('current_password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Password Baru</label>
                <input type="password" name="password" required minlength="8" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
            </div>
            <button type="submit" class="w-full bg-del text-white py-2.5 rounded-xl text-sm font-semibold hover:bg-del-light transition-colors">
                Perbarui Password
            </button>
        </form>
    </div>
</div>
@endsection
