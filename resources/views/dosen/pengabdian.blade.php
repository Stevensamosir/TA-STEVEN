@extends('layouts.dashboard')
@section('title', 'Pengabdian')
@section('page-title', 'Data Pengabdian Masyarakat')
@section('content')
<div class="pt-4 space-y-6">
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Tambah Pengabdian</h3>
        <form action="{{ route('dosen.pengabdian.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Judul Kegiatan *</label>
                    <input type="text" name="title" required
                           class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tahun *</label>
                    <input type="number" name="year" required min="1990" max="{{ date('Y')+1 }}" placeholder="{{ date('Y') }}"
                           class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 sm:items-end">
                <div class="flex-1">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Lokasi</label>
                    <input type="text" name="location" placeholder="Lokasi kegiatan"
                           class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                </div>
                <button type="submit" class="w-full sm:w-auto bg-del text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-del-light">
                    + Tambah
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="p-5 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Daftar Pengabdian ({{ $pengabdians->count() }})</h3>
        </div>
        @if($pengabdians->isEmpty())
            <div class="text-center py-12 text-gray-400 text-sm">Belum ada data pengabdian.</div>
        @else
        <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[480px]">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                <tr>
                    <th class="px-4 py-3 text-center w-10">No.</th>
                    <th class="px-5 py-3 text-left">Judul</th>
                    <th class="px-5 py-3 text-left">Tahun</th>
                    <th class="px-5 py-3 text-left">Lokasi</th>
                    <th class="px-5 py-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($pengabdians as $item)
                <tr class="hover:bg-gray-50/50" id="row-pgb-{{ $item->id }}">
                    <td class="px-4 py-3 text-center text-xs text-gray-400 font-medium">{{ $loop->iteration }}</td>
                    <td class="px-5 py-3 font-medium text-gray-800">{{ $item->title }}</td>
                    <td class="px-5 py-3 text-gray-600">{{ $item->year }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $item->location ?? '-' }}</td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-2">
                            <button onclick="toggleEditRow('pgb', {{ $item->id }})"
                                class="text-xs bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 px-3 py-1 rounded-lg font-medium transition-colors">
                                Edit
                            </button>
                            <form action="{{ route('dosen.pengabdian.destroy', $item->id) }}" method="POST"
                                  onsubmit="return confirm('Hapus?')" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="text-xs bg-red-50 text-red-600 hover:bg-red-100 border border-red-200 px-3 py-1 rounded-lg font-medium transition-colors">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <tr id="edit-pgb-{{ $item->id }}" class="hidden bg-blue-50/30">
                    <td colspan="5" class="px-5 py-4">
                        <form action="{{ route('dosen.pengabdian.update', $item->id) }}" method="POST">
                            @csrf @method('PUT')
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-3">
                                <div class="md:col-span-2">
                                    <input type="text" name="title" value="{{ $item->title }}" required
                                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                </div>
                                <input type="number" name="year" value="{{ $item->year }}"
                                       class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div class="flex gap-3">
                                <input type="text" name="location" value="{{ $item->location }}" placeholder="Lokasi"
                                       class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                <button type="submit" class="bg-del text-white px-4 py-2 rounded-lg text-sm font-medium">Simpan</button>
                                <button type="button" onclick="toggleEditRow('pgb', {{ $item->id }})"
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
    document.getElementById(`edit-${type}-${id}`).classList.toggle('hidden');
}
</script>
@endpush
