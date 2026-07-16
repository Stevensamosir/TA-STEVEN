@extends('layouts.dashboard')
@section('title', 'Penghargaan')
@section('page-title', 'Data Penghargaan')

@section('content')
<div class="pt-4 space-y-6">

    <!-- Form Tambah -->
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Tambah Penghargaan</h3>
        <form action="{{ route('dosen.penghargaan.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Nama Penghargaan *</label>
                    <input type="text" name="name" required placeholder="Nama penghargaan"
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tingkat *</label>
                    <select name="level" required class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                        <option value="Internasional">Internasional</option>
                        <option value="Nasional">Nasional</option>
                        <option value="Lokal">Lokal</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Penyelenggara</label>
                    <input type="text" name="organizer" placeholder="Penyelenggara"
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Peringkat</label>
                    <input type="text" name="rank" placeholder="mis. Juara 1"
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal *</label>
                    <input type="date" name="date" required max="{{ date('Y-m-d') }}"
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tautan Bukti/Sertifikat</label>
                    <input type="url" name="evidence_url" placeholder="https://..."
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
            <h3 class="font-semibold text-gray-800">Daftar Penghargaan ({{ $penghargaans->count() }})</h3>
        </div>
        @if($penghargaans->isEmpty())
            <div class="text-center py-12 text-gray-400 text-sm">Belum ada data penghargaan.</div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[760px]">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-center w-10">No.</th>
                        <th class="px-5 py-3 text-left">Nama</th>
                        <th class="px-5 py-3 text-left">Tingkat</th>
                        <th class="px-5 py-3 text-left">Penyelenggara</th>
                        <th class="px-5 py-3 text-left">Peringkat</th>
                        <th class="px-5 py-3 text-left">Tanggal</th>
                        <th class="px-5 py-3 text-left">Bukti</th>
                        <th class="px-5 py-3 text-left">Visibilitas</th>
                        <th class="px-5 py-3 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($penghargaans as $item)
                    <tr class="hover:bg-gray-50/50 transition-colors" id="row-award-{{ $item->id }}">
                        <td class="px-4 py-3 text-center text-xs text-gray-400 font-medium">{{ $loop->iteration }}</td>
                        <td class="px-5 py-3 font-medium text-gray-800">{{ $item->name }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ $item->level }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ $item->organizer ?? '-' }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ $item->rank ?? '-' }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ \Illuminate\Support\Carbon::parse($item->date)->format('d M Y') }}</td>
                        <td class="px-5 py-3 text-gray-500">
                            @if($item->evidence_url)
                                <a href="{{ $item->evidence_url }}" target="_blank" rel="noopener noreferrer" class="text-del hover:underline">Lihat</a>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <form action="{{ route('dosen.penghargaan.visibility', $item->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-xs font-medium px-2.5 py-1 rounded-full {{ $item->visibility === 'public' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $item->visibility === 'public' ? 'Publik' : 'Privat' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <button onclick="toggleEditRow('award', {{ $item->id }})"
                                    class="text-xs bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 px-3 py-1 rounded-lg font-medium transition-colors">
                                    Edit
                                </button>
                                <form action="{{ route('dosen.penghargaan.destroy', $item->id) }}" method="POST"
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
                    <tr id="edit-award-{{ $item->id }}" class="hidden bg-blue-50/30">
                        <td colspan="9" class="px-5 py-4">
                            <form action="{{ route('dosen.penghargaan.update', $item->id) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                    <input type="text" name="name" value="{{ $item->name }}" required placeholder="Nama"
                                           class="px-3 py-2 border border-gray-200 rounded-lg text-sm md:col-span-2">
                                    <select name="level" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                        <option value="Internasional" @selected($item->level === 'Internasional')>Internasional</option>
                                        <option value="Nasional" @selected($item->level === 'Nasional')>Nasional</option>
                                        <option value="Lokal" @selected($item->level === 'Lokal')>Lokal</option>
                                    </select>
                                    <input type="date" name="date" value="{{ \Illuminate\Support\Carbon::parse($item->date)->format('Y-m-d') }}" required
                                           class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    <input type="text" name="organizer" value="{{ $item->organizer }}" placeholder="Penyelenggara"
                                           class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    <input type="text" name="rank" value="{{ $item->rank }}" placeholder="Peringkat"
                                           class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    <input type="url" name="evidence_url" value="{{ $item->evidence_url }}" placeholder="https://..."
                                           class="px-3 py-2 border border-gray-200 rounded-lg text-sm md:col-span-2">
                                    <select name="visibility" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                        <option value="public" @selected($item->visibility === 'public')>Publik</option>
                                        <option value="private" @selected($item->visibility === 'private')>Privat</option>
                                    </select>
                                </div>
                                <div class="flex gap-3 mt-3">
                                    <button type="submit" class="bg-del text-white px-4 py-2 rounded-lg text-sm font-medium">Simpan</button>
                                    <button type="button" onclick="toggleEditRow('award', {{ $item->id }})"
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
