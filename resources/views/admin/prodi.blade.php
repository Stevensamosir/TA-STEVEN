@extends('layouts.dashboard')
@section('title', 'Kelola Program Studi')
@section('page-title', 'Kelola Program Studi')

@section('content')
<div class="pt-4 space-y-6 max-w-2xl">

    {{-- Error global (misal: tidak bisa hapus prodi yang ada dosennya) --}}
    @if($errors->has('prodi'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
            {{ $errors->first('prodi') }}
        </div>
    @endif

    {{-- ── FORM TAMBAH PRODI ── --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <h2 class="text-sm font-semibold text-gray-800 mb-4">Tambah Program Studi</h2>
        <form action="{{ route('admin.prodi.store') }}" method="POST" class="flex gap-3">
            @csrf
            <input type="text" name="name" value="{{ old('name') }}" required
                   placeholder="Nama program studi..."
                   class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30 focus:border-del @error('name') border-red-300 @enderror">
            <button type="submit"
                    class="bg-del text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-del-light transition-colors whitespace-nowrap">
                + Tambah
            </button>
        </form>
        @error('name')
            <p class="text-xs text-red-500 mt-2">{{ $message }}</p>
        @enderror
    </div>

    {{-- ── DAFTAR PRODI ── --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-800">
                Daftar Program Studi
                <span class="ml-2 text-xs font-normal text-gray-400">({{ $studyPrograms->count() }} prodi)</span>
            </h2>
        </div>

        @if($studyPrograms->isEmpty())
            <div class="px-5 py-10 text-center text-sm text-gray-400">
                Belum ada program studi. Tambahkan di atas.
            </div>
        @else
            <ul class="divide-y divide-gray-50">
                @foreach($studyPrograms as $prodi)
                <li class="px-5 py-4" id="prodi-row-{{ $prodi->id }}">

                    {{-- Mode tampil normal --}}
                    <div class="flex items-center justify-between gap-4" id="prodi-view-{{ $prodi->id }}">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $prodi->name }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $prodi->lecturers_count }} dosen terdaftar</p>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            {{-- Tombol Edit --}}
                            <button type="button"
                                    onclick="toggleEdit({{ $prodi->id }})"
                                    class="text-xs text-del hover:underline font-medium">
                                Edit
                            </button>
                            {{-- Tombol Hapus (nonaktif jika masih ada dosen) --}}
                            @if($prodi->lecturers_count > 0)
                                <span class="text-xs text-gray-300 cursor-not-allowed" title="Tidak bisa dihapus — masih ada dosen">
                                    Hapus
                                </span>
                            @else
                                <form action="{{ route('admin.prodi.destroy', $prodi->id) }}" method="POST"
                                      onsubmit="return confirm('Hapus program studi ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-red-400 hover:text-red-600 font-medium">
                                        Hapus
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    {{-- Mode edit inline (tersembunyi dulu) --}}
                    <form action="{{ route('admin.prodi.update', $prodi->id) }}" method="POST"
                          id="prodi-edit-{{ $prodi->id }}"
                          class="hidden mt-3 flex gap-3 items-center">
                        @csrf @method('PUT')
                        <input type="text" name="name" value="{{ $prodi->name }}" required
                               class="flex-1 px-4 py-2 border border-del/40 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                        <button type="submit"
                                class="bg-del text-white px-4 py-2 rounded-xl text-xs font-semibold hover:bg-del-light transition-colors">
                            Simpan
                        </button>
                        <button type="button" onclick="toggleEdit({{ $prodi->id }})"
                                class="px-4 py-2 rounded-xl text-xs font-medium text-gray-500 border border-gray-200 hover:bg-gray-50">
                            Batal
                        </button>
                    </form>

                </li>
                @endforeach
            </ul>
        @endif
    </div>

</div>

@push('scripts')
<script>
function toggleEdit(id) {
    const view = document.getElementById('prodi-view-' + id);
    const form = document.getElementById('prodi-edit-' + id);
    const hidden = form.classList.contains('hidden');
    view.classList.toggle('hidden', hidden);
    form.classList.toggle('hidden', !hidden);
    if (hidden) form.querySelector('input[name="name"]').focus();
}
</script>
@endpush
@endsection
