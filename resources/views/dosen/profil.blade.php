@extends('layouts.dashboard')
@section('title', 'Edit Profil')
@section('page-title', 'Edit Profil')

@section('content')
<div class="pt-4">

    <div class="max-w-2xl">

        {{-- ── Form Edit Profil ── --}}
        <div>
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <form action="{{ route('dosen.profil.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf @method('PUT')

                    {{-- Foto --}}
                    <div class="flex items-center gap-6 pb-5 border-b border-gray-100">
                        <div class="w-20 h-20 rounded-2xl bg-blue-50 border-2 border-del/20 flex items-center justify-center overflow-hidden flex-shrink-0">
                            @if($lecturer->photo)
                                <img src="{{ Storage::url($lecturer->photo) }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-del text-2xl font-bold">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Foto Profil</label>
                            <input type="file" name="photo" accept="image/jpeg,image/png"
                                class="text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-blue-50 file:text-del file:text-sm file:font-medium hover:file:bg-blue-100 cursor-pointer">
                            <p class="text-xs text-gray-400 mt-1">JPG, PNG, maks. 4MB</p>
                        </div>
                    </div>

                    {{-- Nama --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30 focus:border-del">
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}"
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30 focus:border-del">
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Email ini akan ditampilkan di profil publik Anda</p>
                        @error('email')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- NIDN --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">NIDN</label>
                        <input type="text" name="nidn" value="{{ old('nidn', $lecturer->nidn) }}"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30 focus:border-del"
                            placeholder="10 digit NIDN">
                    </div>

                    {{-- Jabatan Fungsional --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Jabatan Fungsional
                            <span class="text-xs text-gray-400 font-normal ml-1">(Sesuai SK Dikti)</span>
                        </label>
                        <select name="jabatan_fungsional"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30 focus:border-del bg-white">
                            <option value=""> - Pilih Jabatan Fungsional - </option>
                            @foreach(App\Models\Lecturer::jabatanFungsionalOptions() as $jf)
                                <option value="{{ $jf }}" {{ old('jabatan_fungsional', $lecturer->jabatan_fungsional) === $jf ? 'selected' : '' }}>
                                    {{ $jf }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Berdasarkan Permendikbud No. 92 Tahun 2014</p>
                    </div>

                    {{-- Kepakaran --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Kepakaran / Bidang Keahlian</label>
                        <textarea name="expertise" rows="3"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30 focus:border-del resize-none"
                            placeholder="Contoh: Rekayasa Perangkat Lunak, Machine Learning, Web Development">{{ old('expertise', $lecturer->expertise) }}</textarea>
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

    </div>
</div>
@endsection
