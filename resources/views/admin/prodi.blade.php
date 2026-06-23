@extends('layouts.dashboard')
@section('title', 'Kelola Program Studi')
@section('page-title', 'Kelola Program Studi')

@section('content')
<div class="pt-4 space-y-6 max-w-3xl">

    @if($errors->has('prodi'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
            {{ $errors->first('prodi') }}
        </div>
    @endif

    {{-- Form Tambah Prodi --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <h2 class="text-sm font-semibold text-gray-800 mb-4">Tambah Program Studi</h2>
        <form action="{{ route('admin.prodi.store') }}" method="POST" class="flex gap-3">
            @csrf
            <input type="text" name="name" value="{{ old('name') }}" required
                   placeholder="Nama program studi..."
                   class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30 focus:border-del @error('name') border-red-300 @enderror">
            <button type="submit" class="bg-del text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-del-light transition-colors whitespace-nowrap">
                + Tambah
            </button>
        </form>
        @error('name')
            <p class="text-xs text-red-500 mt-2">{{ $message }}</p>
        @enderror
    </div>

    {{-- Daftar Prodi --}}
    <div class="space-y-4">
        <h2 class="text-sm font-semibold text-gray-700 px-1">
            Daftar Program Studi
            <span class="ml-2 text-xs font-normal text-gray-400">({{ $studyPrograms->count() }} prodi)</span>
        </h2>

        @if($studyPrograms->isEmpty())
            <div class="bg-white rounded-2xl border border-gray-100 px-5 py-10 text-center text-sm text-gray-400">
                Belum ada program studi. Tambahkan di atas.
            </div>
        @else
            @foreach($studyPrograms as $prodi)
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden" id="prodi-row-{{ $prodi->id }}">

                {{-- Header prodi --}}
                <div class="px-5 py-4 border-b border-gray-50">
                    <div class="flex items-center justify-between gap-4" id="prodi-view-{{ $prodi->id }}">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ $prodi->name }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $prodi->lecturers_count }} dosen terdaftar</p>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <button type="button" onclick="toggleEdit({{ $prodi->id }})"
                                class="text-xs bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 px-3 py-1 rounded-lg font-medium transition-colors">
                                Edit Nama
                            </button>
                            @if($prodi->lecturers_count > 0)
                                <span class="text-xs bg-gray-50 text-gray-300 border border-gray-100 px-3 py-1 rounded-lg font-medium cursor-not-allowed"
                                      title="Tidak bisa dihapus — masih ada dosen">Hapus</span>
                            @else
                                <form action="{{ route('admin.prodi.destroy', $prodi->id) }}" method="POST"
                                      onsubmit="return confirm('Hapus program studi ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="text-xs bg-red-50 text-red-600 hover:bg-red-100 border border-red-200 px-3 py-1 rounded-lg font-medium transition-colors">
                                        Hapus
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    {{-- Form edit nama inline --}}
                    <form action="{{ route('admin.prodi.update', $prodi->id) }}" method="POST"
                          id="prodi-edit-{{ $prodi->id }}" class="hidden mt-3 flex gap-3 items-center">
                        @csrf @method('PUT')
                        <input type="text" name="name" value="{{ $prodi->name }}" required
                               class="flex-1 px-4 py-2 border border-del/40 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                        <button type="submit" class="bg-del text-white px-4 py-2 rounded-xl text-xs font-semibold hover:bg-del-light transition-colors">
                            Simpan
                        </button>
                        <button type="button" onclick="toggleEdit({{ $prodi->id }})"
                                class="px-4 py-2 rounded-xl text-xs font-medium text-gray-500 border border-gray-200 hover:bg-gray-50">
                            Batal
                        </button>
                    </form>
                </div>

                {{-- Kaprodi assignment (gabungan Hierarki) --}}
                <div class="px-5 py-4 bg-gray-50/50">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-gray-500 mb-0.5">Kaprodi saat ini</p>
                            <p class="text-sm font-semibold text-gray-800">
                                {{ $prodi->headLecturer?->user->name ?? '—' }}
                            </p>
                        </div>
                        <form action="{{ route('admin.hierarki.update', $prodi->id) }}" method="POST"
                              class="flex gap-2 items-center sm:flex-shrink-0">
                            @csrf @method('PUT')
                            <select name="head_lecturer_id"
                                class="w-full sm:w-auto px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30 bg-white">
                                <option value="">— Tidak ada Kaprodi —</option>
                                @foreach($lecturers->where('study_program_id', $prodi->id) as $lec)
                                    <option value="{{ $lec->id }}" {{ $prodi->head_lecturer_id == $lec->id ? 'selected' : '' }}>
                                        {{ $lec->user->name }}{{ !$lec->user->is_active ? ' (nonaktif)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit"
                                class="whitespace-nowrap bg-del text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-del-light transition-colors">
                                Simpan
                            </button>
                        </form>
                    </div>
                </div>

            </div>
            @endforeach
        @endif
    </div>
</div>

@push('scripts')
<script>
function toggleEdit(id) {
    const view = document.getElementById('prodi-view-' + id);
    const form = document.getElementById('prodi-edit-' + id);
    const isHidden = form.classList.contains('hidden');
    view.classList.toggle('hidden', isHidden);
    form.classList.toggle('hidden', !isHidden);
    if (isHidden) form.querySelector('input[name="name"]').focus();
}
</script>
@endpush
@endsection
