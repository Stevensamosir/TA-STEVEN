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
                    <input type="number" name="year" value="{{ old('year', now()->year) }}" required min="1990" max="{{ date('Y')+1 }}" placeholder="{{ date('Y') }}"
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Bulan *</label>
                    <select name="month" required
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                        <option value=""> - Pilih - </option>
                        @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $bulan)
                            <option value="{{ $i+1 }}" {{ old('month') == $i+1 ? 'selected' : '' }}>{{ $bulan }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex gap-4 items-end">
                <div class="flex-1">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Sumber Dana</label>
                    <input type="text" name="funding_source" placeholder="Contoh: DIPA IT Del, Mandiri, Kemendikbud"
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
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
            <table class="w-full text-sm min-w-[480px]">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-center w-10">No.</th>
                        <th class="px-5 py-3 text-left">Judul</th>
                        <th class="px-5 py-3 text-left">Tahun</th>
                        <th class="px-5 py-3 text-left">Sumber Dana</th>
                        <th class="px-5 py-3 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($penelitians as $item)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-center text-xs text-gray-400 font-medium">{{ $loop->iteration }}</td>
                        <td class="px-5 py-3 font-medium text-gray-800 max-w-xs">{{ $item->title }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $item->year }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ $item->funding_source ?? '-' }}</td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <button onclick="this.closest('tr').nextElementSibling.classList.toggle('hidden')"
                                    class="text-xs bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 px-3 py-1 rounded-lg font-medium transition-colors">
                                    Edit
                                </button>
                                <form action="{{ route('dosen.penelitian.destroy', $item->id) }}" method="POST"
                                      onsubmit="return confirm('Hapus?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="text-xs bg-red-50 text-red-600 hover:bg-red-100 border border-red-200 px-3 py-1 rounded-lg font-medium transition-colors">
                                        Hapus
                                    </button>
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
                                        <input type="text" name="title" value="{{ $item->title }}"
                                               class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    </div>
                                    <input type="number" name="year" value="{{ $item->year }}"
                                           class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    <select name="month" required
                                            class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                        <option value=""> - Bulan - </option>
                                        @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $bulan)
                                            <option value="{{ $i+1 }}" {{ $item->month == $i+1 ? 'selected' : '' }}>{{ $bulan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex gap-3">
                                    <input type="text" name="funding_source" value="{{ $item->funding_source }}"
                                           placeholder="Sumber dana" class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    <button type="submit" class="bg-del text-white px-4 py-2 rounded-lg text-sm">Simpan</button>
                                    <button type="button"
                                            onclick="this.closest('tr').classList.add('hidden')"
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
