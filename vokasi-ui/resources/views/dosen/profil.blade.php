@extends('layouts.dashboard')
@section('title', 'Edit Profil')
@section('page-title', 'Edit Profil')

@section('content')
<div class="pt-4 max-w-2xl">
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <form action="{{ route('dosen.profil.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf @method('PUT')

            <!-- Foto -->
            <div class="flex items-center gap-6 pb-5 border-b border-gray-100">
                <div class="w-20 h-20 rounded-2xl bg-blue-50 border-2 border-del/20 flex items-center justify-center overflow-hidden">
                    @if($lecturer->photo)
                        <img src="{{ Storage::url($lecturer->photo) }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-del text-2xl font-bold">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    @endif
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Foto Profil</label>
                    <input type="file" name="photo" accept="image/*" class="text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-blue-50 file:text-del file:text-sm file:font-medium hover:file:bg-blue-100 cursor-pointer">
                    <p class="text-xs text-gray-400 mt-1">JPG, PNG, maks. 2MB</p>
                </div>
            </div>

            <!-- Nama -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30 focus:border-del">
            </div>

            <!-- NIDN -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">NIDN</label>
                <input type="text" name="nidn" value="{{ old('nidn', $lecturer->nidn) }}"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30 focus:border-del"
                    placeholder="10 digit NIDN">
            </div>

            <!-- Kepakaran -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Kepakaran / Bidang Keahlian</label>
                <textarea name="expertise" rows="3"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30 focus:border-del resize-none"
                    placeholder="Contoh: Rekayasa Perangkat Lunak, Machine Learning, Web Development">{{ old('expertise', $lecturer->expertise) }}</textarea>
            </div>

            <!-- Visibilitas Profil -->
            <div class="bg-gray-50 rounded-xl p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-800">Profil Publik</p>
                        <p class="text-xs text-gray-500 mt-0.5">Aktifkan agar profil terlihat oleh pengunjung tanpa login</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_public" value="1" class="sr-only peer" {{ $lecturer->is_public ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-del"></div>
                    </label>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-del text-white px-6 py-2.5 rounded-xl text-sm font-semibold hover:bg-del-light transition-colors">
                    Simpan Perubahan
                </button>
                <a href="{{ route('dosen.index') }}" class="px-6 py-2.5 rounded-xl text-sm font-medium text-gray-600 border border-gray-200 hover:bg-gray-50 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
