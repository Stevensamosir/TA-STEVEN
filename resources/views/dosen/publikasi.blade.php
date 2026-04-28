@extends('layouts.dashboard')
@section('title', 'Publikasi')
@section('page-title', 'Data Publikasi')

@section('content')
<div class="pt-4 space-y-6">
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Tambah Publikasi</h3>
        <form action="{{ route('dosen.publikasi.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Judul Publikasi *</label>
                    <input type="text" name="title" required class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tahun *</label>
                    <input type="number" name="year" required min="1990" max="{{ date('Y')+1 }}" placeholder="{{ date('Y') }}" class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Penerbit / Jurnal</label>
                    <input type="text" name="publisher" placeholder="Nama jurnal atau prosiding" class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Link Sumber (URL)</label>
                    <input type="url" name="publisher_url" placeholder="https://doi.org/..." class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                </div>
            </div>
            <div class="flex gap-3 items-center">
                <select name="visibility" class="px-3 py-2.5 border border-gray-200 rounded-xl text-sm">
                    <option value="public">Publik</option>
                    <option value="private">Internal</option>
                </select>
                <button type="submit" class="bg-del text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-del-light">+ Tambah</button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="p-5 border-b border-gray-100"><h3 class="font-semibold text-gray-800">Daftar Publikasi ({{ $publikasis->count() }})</h3></div>
        @if($publikasis->isEmpty())
            <div class="text-center py-12 text-gray-400 text-sm">Belum ada data publikasi.</div>
        @else
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                <tr>
                    <th class="px-5 py-3 text-left">Judul</th>
                    <th class="px-5 py-3 text-left">Tahun</th>
                    <th class="px-5 py-3 text-left">Penerbit</th>
                    <th class="px-5 py-3 text-left">Link</th>
                    <th class="px-5 py-3 text-left">Visibilitas</th>
                    <th class="px-5 py-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($publikasis as $item)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-5 py-3 font-medium text-gray-800 max-w-xs">{{ $item->title }}</td>
                    <td class="px-5 py-3 text-gray-600">{{ $item->year }}</td>
                    <td class="px-5 py-3 text-gray-500 text-xs">{{ $item->publisher ?? '-' }}</td>
                    <td class="px-5 py-3">
                        @if($item->publisher_url)
                            <a href="{{ $item->publisher_url }}" target="_blank" class="text-xs text-del hover:underline flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                Lihat
                            </a>
                        @else
                            <span class="text-xs text-gray-300">-</span>
                        @endif
                    </td>
                    <td class="px-5 py-3">
                        <form action="{{ route('dosen.publikasi.visibility', $item->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-full {{ $item->visibility === 'public' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $item->visibility === 'public' ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                {{ $item->visibility === 'public' ? 'Publik' : 'Internal' }}
                            </button>
                        </form>
                    </td>
                    <td class="px-5 py-3">
                        <form action="{{ route('dosen.publikasi.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus?')" class="inline">
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
