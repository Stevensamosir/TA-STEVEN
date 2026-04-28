@extends('layouts.dashboard')
@section('title', 'Penelitian')
@section('page-title', 'Data Penelitian')

@section('content')
<div class="pt-4 space-y-6">
    <!-- Form Tambah -->
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Tambah Penelitian</h3>
        <form action="{{ route('dosen.penelitian.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Judul Penelitian *</label>
                    <input type="text" name="title" required placeholder="Judul penelitian"
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tahun *</label>
                    <input type="number" name="year" required min="1990" max="{{ date('Y')+1 }}" placeholder="{{ date('Y') }}"
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                </div>
            </div>
            <div class="flex gap-4 items-end">
                <div class="flex-1">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Sumber Dana</label>
                    <input type="text" name="funding_source" placeholder="Contoh: DIPA IT Del, Mandiri, Kemendikbud"
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Visibilitas</label>
                    <select name="visibility" class="px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none">
                        <option value="public">Publik</option>
                        <option value="private">Internal</option>
                    </select>
                </div>
                <button type="submit" class="bg-del text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-del-light">+ Tambah</button>
            </div>
        </form>
    </div>

    <!-- Tabel -->
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="p-5 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Daftar Penelitian ({{ $penelitians->count() }})</h3>
        </div>
        @if($penelitians->isEmpty())
            <div class="text-center py-12 text-gray-400 text-sm">Belum ada data penelitian.</div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3 text-left">Judul</th>
                        <th class="px-5 py-3 text-left">Tahun</th>
                        <th class="px-5 py-3 text-left">Sumber Dana</th>
                        <th class="px-5 py-3 text-left">Visibilitas</th>
                        <th class="px-5 py-3 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($penelitians as $item)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-3 font-medium text-gray-800 max-w-xs">{{ $item->title }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $item->year }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ $item->funding_source ?? '-' }}</td>
                        <td class="px-5 py-3">
                            <form action="{{ route('dosen.penelitian.visibility', $item->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-full transition-colors {{ $item->visibility === 'public' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $item->visibility === 'public' ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                    {{ $item->visibility === 'public' ? 'Publik' : 'Internal' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <button onclick="this.closest('tr').nextElementSibling.classList.toggle('hidden')" class="text-xs text-blue-600 hover:underline">Edit</button>
                                <form action="{{ route('dosen.penelitian.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-red-500 hover:underline">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <tr class="hidden bg-blue-50/30">
                        <td colspan="5" class="px-5 py-4">
                            <form action="{{ route('dosen.penelitian.update', $item->id) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-3">
                                    <div class="md:col-span-2">
                                        <input type="text" name="title" value="{{ $item->title }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    </div>
                                    <input type="number" name="year" value="{{ $item->year }}" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                </div>
                                <div class="flex gap-3">
                                    <input type="text" name="funding_source" value="{{ $item->funding_source }}" placeholder="Sumber dana" class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    <select name="visibility" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                        <option value="public" {{ $item->visibility == 'public' ? 'selected' : '' }}>Publik</option>
                                        <option value="private" {{ $item->visibility == 'private' ? 'selected' : '' }}>Internal</option>
                                    </select>
                                    <button type="submit" class="bg-del text-white px-4 py-2 rounded-lg text-sm">Simpan</button>
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
