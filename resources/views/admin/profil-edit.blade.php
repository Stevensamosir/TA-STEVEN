@extends('layouts.dashboard')
@section('title', 'Edit Profil Dosen')
@section('page-title', 'Edit Profil Dosen')

@section('content')
<div class="pt-4 max-w-2xl">

    {{-- Info konteks: siapa yang diedit --}}
    <div class="bg-blue-50 border border-blue-100 rounded-xl px-5 py-3 mb-5 flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-del/10 flex items-center justify-center flex-shrink-0">
            <span class="text-del font-bold text-sm">{{ strtoupper(substr($lecturer->user->name, 0, 1)) }}</span>
        </div>
        <div>
            <p class="text-sm font-semibold text-gray-800">{{ $lecturer->user->name }}</p>
            <p class="text-xs text-gray-500">
                {{ $lecturer->studyProgram->name ?? '-' }} ·
                <span class="
                    {{ $lecturer->user->role === 'dekan'   ? 'text-red-600'    :
                      ($lecturer->user->role === 'kaprodi' ? 'text-yellow-600' : 'text-del') }} font-medium">
                    {{ ucfirst($lecturer->user->role) }}
                </span>
            </p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        @if($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 rounded-xl p-4 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.profil.update', $lecturer->id) }}" method="POST" class="space-y-5">
            @csrf @method('PUT')

            {{-- NIDN --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">NIDN</label>
                <input type="text" name="nidn"
                    value="{{ old('nidn', $lecturer->nidn) }}"
                    placeholder="10 digit NIDN"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30 focus:border-del">
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
                        <option value="{{ $jf }}"
                            {{ old('jabatan_fungsional', $lecturer->jabatan_fungsional) === $jf ? 'selected' : '' }}>
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
                    placeholder="Contoh: Rekayasa Perangkat Lunak, Machine Learning, Web Development"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30 focus:border-del resize-none">{{ old('expertise', $lecturer->expertise) }}</textarea>
            </div>

            {{-- Tombol --}}
            <div class="flex gap-3 pt-2">
                <button type="submit"
                    class="bg-del text-white px-6 py-2.5 rounded-xl text-sm font-semibold hover:bg-del-light transition-colors">
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.internal') }}"
                    class="px-6 py-2.5 rounded-xl text-sm font-medium text-gray-600 border border-gray-200 hover:bg-gray-50 transition-colors">
                    Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
