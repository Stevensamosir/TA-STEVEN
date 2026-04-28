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
                    <input type="text" name="title" required class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tahun *</label>
                    <input type="number" name="year" required min="1990" max="{{ date('Y')+1 }}" placeholder="{{ date('Y') }}" class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                </div>
            </div>
            <div class="flex gap-4 items-end">
                <div class="flex-1">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Lokasi</label>
                    <input type="text" name="location" placeholder="Lokasi kegiatan" class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                </div>
                <select name="visibility" class="px-3 py-2.5 border border-gray-200 rounded-xl text-sm">
                    <option value="public">Publik</option>
                    <option value="private">Internal</option>
                </select>
                <button type="submit" class="bg-del text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-del-light">+ Tambah</button>
            </div>
        </form>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="p-5 border-b border-gray-100"><h3 class="font-semibold text-gray-800">Daftar Pengabdian ({{ $pengabdians->count() }})</h3></div>
        @if($pengabdians->isEmpty())
            <div class="text-center py-12 text-gray-400 text-sm">Belum ada data pengabdian.</div>
        @else
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                <tr>
                    <th class="px-5 py-3 text-left">Judul</th>
                    <th class="px-5 py-3 text-left">Tahun</th>
                    <th class="px-5 py-3 text-left">Lokasi</th>
                    <th class="px-5 py-3 text-left">Visibilitas</th>
                    <th class="px-5 py-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($pengabdians as $item)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-5 py-3 font-medium text-gray-800">{{ $item->title }}</td>
                    <td class="px-5 py-3 text-gray-600">{{ $item->year }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $item->location ?? '-' }}</td>
                    <td class="px-5 py-3">
                        <form action="{{ route('dosen.pengabdian.visibility', $item->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-full {{ $item->visibility === 'public' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $item->visibility === 'public' ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                {{ $item->visibility === 'public' ? 'Publik' : 'Internal' }}
                            </button>
                        </form>
                    </td>
                    <td class="px-5 py-3">
                        <form action="{{ route('dosen.pengabdian.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus?')" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-500 hover:underline">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>
@endsection
