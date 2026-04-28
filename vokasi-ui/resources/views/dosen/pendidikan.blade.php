@extends('layouts.dashboard')
@section('title', 'Pendidikan')
@section('page-title', 'Riwayat Pendidikan')

@section('content')
<div class="pt-4 space-y-6">

    <!-- Form Tambah -->
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Tambah Pendidikan</h3>
        <form action="{{ route('dosen.pendidikan.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Jenjang</label>
                    <select name="degree" class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                        <option value="S1">S1</option>
                        <option value="S2">S2</option>
                        <option value="S3">S3</option>
                        <option value="D3">D3</option>
                        <option value="D4">D4</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Institusi *</label>
                    <input type="text" name="institution" required placeholder="Nama universitas"
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Program Studi</label>
                    <input type="text" name="major" placeholder="Jurusan / prodi"
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tahun Lulus *</label>
                    <input type="number" name="year" required min="1950" max="{{ date('Y') }}" placeholder="{{ date('Y') }}"
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                </div>
            </div>
            <div class="flex items-center gap-4 mt-4">
                <div class="flex items-center gap-2">
                    <label class="text-xs font-medium text-gray-600">Visibilitas:</label>
                    <select name="visibility" class="px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                        <option value="public">Publik</option>
                        <option value="private">Internal</option>
                    </select>
                </div>
                <button type="submit" class="bg-del text-white px-5 py-2 rounded-xl text-sm font-semibold hover:bg-del-light transition-colors">
                    + Tambah
                </button>
            </div>
        </form>
    </div>

    <!-- Tabel Data -->
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">Daftar Pendidikan ({{ $educations->count() }})</h3>
        </div>

        @if($educations->isEmpty())
            <div class="text-center py-12 text-gray-400 text-sm">Belum ada data pendidikan.</div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3 text-left">Jenjang</th>
                        <th class="px-5 py-3 text-left">Institusi</th>
                        <th class="px-5 py-3 text-left">Prodi</th>
                        <th class="px-5 py-3 text-left">Tahun</th>
                        <th class="px-5 py-3 text-left">Visibilitas</th>
                        <th class="px-5 py-3 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($educations as $edu)
                    <tr class="hover:bg-gray-50/50 transition-colors" id="row-edu-{{ $edu->id }}">
                        <td class="px-5 py-3">
                            <span class="bg-del text-white text-xs font-bold px-2 py-1 rounded-full">{{ $edu->degree }}</span>
                        </td>
                        <td class="px-5 py-3 font-medium text-gray-800">{{ $edu->institution }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ $edu->major ?? '-' }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $edu->year }}</td>
                        <td class="px-5 py-3">
                            <form action="{{ route('dosen.pendidikan.visibility', $edu->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-full transition-colors
                                    {{ $edu->visibility === 'public' ? 'bg-green-50 text-green-700 hover:bg-green-100' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $edu->visibility === 'public' ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                    {{ $edu->visibility === 'public' ? 'Publik' : 'Internal' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <button onclick="toggleEditRow('edu', {{ $edu->id }})" class="text-xs text-blue-600 hover:underline">Edit</button>
                                <form action="{{ route('dosen.pendidikan.destroy', $edu->id) }}" method="POST" onsubmit="return confirm('Hapus data ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-red-500 hover:underline">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <!-- Edit Row -->
                    <tr id="edit-edu-{{ $edu->id }}" class="hidden bg-blue-50/30">
                        <td colspan="6" class="px-5 py-4">
                            <form action="{{ route('dosen.pendidikan.update', $edu->id) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                    <select name="degree" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                        @foreach(['S1','S2','S3','D3','D4'] as $d)
                                            <option value="{{ $d }}" {{ $edu->degree == $d ? 'selected' : '' }}>{{ $d }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" name="institution" value="{{ $edu->institution }}" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    <input type="text" name="major" value="{{ $edu->major }}" placeholder="Prodi" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    <input type="number" name="year" value="{{ $edu->year }}" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                </div>
                                <div class="flex gap-3 mt-3">
                                    <select name="visibility" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                        <option value="public" {{ $edu->visibility == 'public' ? 'selected' : '' }}>Publik</option>
                                        <option value="private" {{ $edu->visibility == 'private' ? 'selected' : '' }}>Internal</option>
                                    </select>
                                    <button type="submit" class="bg-del text-white px-4 py-2 rounded-lg text-sm font-medium">Simpan</button>
                                    <button type="button" onclick="toggleEditRow('edu', {{ $edu->id }})" class="px-4 py-2 rounded-lg text-sm text-gray-500 border border-gray-200">Batal</button>
                                </div>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleEditRow(type, id) {
    const row = document.getElementById(`edit-${type}-${id}`);
    row.classList.toggle('hidden');
}
</script>
@endpush
