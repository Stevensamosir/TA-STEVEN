@extends('layouts.dashboard')
@section('title', 'Buku')
@section('page-title', 'Data Buku')

@section('content')
<div class="pt-4 space-y-6">

    <!-- Form Tambah -->
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Tambah Buku</h3>
        <form action="{{ route('dosen.buku.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Judul Buku *</label>
                    <input type="text" name="title" required placeholder="Judul buku"
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tahun *</label>
                    <input type="number" name="year" value="{{ old('year', now()->year) }}" required min="1970" max="{{ date('Y') }}" placeholder="{{ date('Y') }}"
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Penerbit</label>
                    <input type="text" name="publisher" placeholder="Nama penerbit"
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">ISBN</label>
                    <input type="text" name="isbn" placeholder="ISBN"
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Visibilitas</label>
                    <select name="visibility" required class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                        <option value="public">Publik</option>
                        <option value="private">Privat</option>
                    </select>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="bg-del text-white px-5 py-2 rounded-xl text-sm font-semibold hover:bg-del-light transition-colors">
                    + Tambah
                </button>
            </div>
        </form>
    </div>

    <!-- Tabel Data -->
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="p-5 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Daftar Buku ({{ $bukus->count() }})</h3>
        </div>
        @if($bukus->isEmpty())
            <div class="text-center py-12 text-gray-400 text-sm">Belum ada data buku.</div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[640px]">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-center w-10">No.</th>
                        <th class="px-5 py-3 text-left">Judul</th>
                        <th class="px-5 py-3 text-left">Tahun</th>
                        <th class="px-5 py-3 text-left">Penerbit</th>
                        <th class="px-5 py-3 text-left">ISBN</th>
                        <th class="px-5 py-3 text-left">Visibilitas</th>
                        <th class="px-5 py-3 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($bukus as $item)
                    <tr class="hover:bg-gray-50/50 transition-colors" id="row-buku-{{ $item->id }}">
                        <td class="px-4 py-3 text-center text-xs text-gray-400 font-medium">{{ $loop->iteration }}</td>
                        <td class="px-5 py-3 font-medium text-gray-800">{{ $item->title }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $item->year }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ $item->publisher ?? '-' }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ $item->isbn ?? '-' }}</td>
                        <td class="px-5 py-3">
                            <form action="{{ route('dosen.buku.visibility', $item->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-xs font-medium px-2.5 py-1 rounded-full {{ $item->visibility === 'public' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $item->visibility === 'public' ? 'Publik' : 'Privat' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <button onclick="toggleEditRow('buku', {{ $item->id }})"
                                    class="text-xs bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 px-3 py-1 rounded-lg font-medium transition-colors">
                                    Edit
                                </button>
                                <form action="{{ route('dosen.buku.destroy', $item->id) }}" method="POST"
                                      onsubmit="return confirm('Hapus data ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="text-xs bg-red-50 text-red-600 hover:bg-red-100 border border-red-200 px-3 py-1 rounded-lg font-medium transition-colors">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <!-- Edit Row -->
                    <tr id="edit-buku-{{ $item->id }}" class="hidden bg-blue-50/30">
                        <td colspan="7" class="px-5 py-4">
                            <form action="{{ route('dosen.buku.update', $item->id) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                    <input type="text" name="title" value="{{ $item->title }}" required placeholder="Judul"
                                           class="px-3 py-2 border border-gray-200 rounded-lg text-sm md:col-span-2">
                                    <input type="number" name="year" value="{{ $item->year }}"
                                           class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    <input type="text" name="publisher" value="{{ $item->publisher }}" placeholder="Penerbit"
                                           class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    <input type="text" name="isbn" value="{{ $item->isbn }}" placeholder="ISBN"
                                           class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    <select name="visibility" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                        <option value="public" @selected($item->visibility === 'public')>Publik</option>
                                        <option value="private" @selected($item->visibility === 'private')>Privat</option>
                                    </select>
                                </div>
                                <div class="flex gap-3 mt-3">
                                    <button type="submit" class="bg-del text-white px-4 py-2 rounded-lg text-sm font-medium">Simpan</button>
                                    <button type="button" onclick="toggleEditRow('buku', {{ $item->id }})"
                                            class="px-4 py-2 rounded-lg text-sm text-gray-500 border border-gray-200">Batal</button>
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
