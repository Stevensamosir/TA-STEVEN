@extends('layouts.dashboard')
@section('title', 'Kelola Program Studi')
@section('page-title', 'Kelola Program Studi')

@section('content')

{{-- Error --}}
@if($errors->any())
<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm mb-5">
    @foreach($errors->all() as $error)<p>• {{ $error }}</p>@endforeach
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ── FORM TAMBAH ── --}}
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-800 mb-4">Tambah Program Studi</h2>
            <form action="{{ route('admin.prodi.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="text-xs font-medium text-gray-600 block mb-1.5">Nama Program Studi</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           placeholder="cth: D4 Teknologi Rekayasa Perangkat Lunak"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-del/30 focus:border-del transition">
                </div>
                <button type="submit"
                        class="w-full bg-del text-white font-semibold py-2.5 rounded-xl hover:bg-del-light transition-colors text-sm">
                    + Tambah Prodi
                </button>
            </form>
        </div>

        {{-- Info Box --}}
        <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4 mt-4 text-xs text-blue-700 leading-relaxed">
            <p class="font-semibold mb-1">⚠️ Catatan</p>
            <p>Prodi yang masih memiliki dosen <strong>tidak dapat dihapus</strong>. Pindahkan atau hapus dosen terlebih dahulu sebelum menghapus prodi.</p>
        </div>
    </div>

    {{-- ── DAFTAR PRODI ── --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-800">Daftar Program Studi</h2>
                <span class="text-xs bg-del/10 text-del font-semibold px-3 py-1 rounded-full">
                    {{ $studyPrograms->count() }} Prodi
                </span>
            </div>

            @if($studyPrograms->isEmpty())
                <div class="text-center py-12 text-gray-400 text-sm">
                    Belum ada program studi. Tambahkan di form sebelah kiri.
                </div>
            @else
            <div class="divide-y divide-gray-50">
                @foreach($studyPrograms as $prodi)
                <div class="px-6 py-4" x-data="{ editing: false }">

                    {{-- View mode --}}
                    <div x-show="!editing" class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-del/10 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-del" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $prodi->name }}</p>
                                <p class="text-xs text-gray-400">
                                    {{ $prodi->lecturers_count }} dosen
                                    @if($prodi->lecturers_count === 0)
                                        <span class="text-orange-400">· bisa dihapus</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <button @click="editing = true"
                                    class="text-xs text-del border border-del/30 hover:bg-del/5 px-3 py-1.5 rounded-lg transition-colors font-medium">
                                Edit
                            </button>
                            @if($prodi->lecturers_count === 0)
                            <form action="{{ route('admin.prodi.destroy', $prodi->id) }}" method="POST"
                                  onsubmit="return confirm('Hapus program studi {{ $prodi->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="text-xs text-red-500 border border-red-200 hover:bg-red-50 px-3 py-1.5 rounded-lg transition-colors font-medium">
                                    Hapus
                                </button>
                            </form>
                            @else
                            <span class="text-xs text-gray-300 border border-gray-100 px-3 py-1.5 rounded-lg cursor-not-allowed">
                                Hapus
                            </span>
                            @endif
                        </div>
                    </div>

                    {{-- Edit mode --}}
                    <div x-show="editing" x-cloak>
                        <form action="{{ route('admin.prodi.update', $prodi->id) }}" method="POST"
                              class="flex items-center gap-2">
                            @csrf @method('PUT')
                            <input type="text" name="name" value="{{ $prodi->name }}" required
                                   class="flex-1 border border-del/40 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                            <button type="submit"
                                    class="text-xs bg-del text-white px-4 py-2 rounded-xl hover:bg-del-light transition-colors font-semibold flex-shrink-0">
                                Simpan
                            </button>
                            <button type="button" @click="editing = false"
                                    class="text-xs text-gray-500 border border-gray-200 px-4 py-2 rounded-xl hover:bg-gray-50 transition-colors flex-shrink-0">
                                Batal
                            </button>
                        </form>
                    </div>

                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="//unpkg.com/alpinejs" defer></script>
@endpush
