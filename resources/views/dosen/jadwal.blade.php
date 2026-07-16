@extends('layouts.dashboard')
@section('title', 'Jadwal Dosen')
@section('page-title', 'Jadwal Ketersediaan Pribadi')

@section('content')
<div class="pt-4 space-y-6">
    <div class="bg-blue-50 border border-blue-100 text-del px-4 py-3 rounded-xl text-sm">
        Ini kalender ketersediaan pribadi Anda (mis. untuk bimbingan/konsultasi), <strong>bukan</strong> jadwal mengajar resmi dari sistem akademik.
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
            @foreach($errors->all() as $error)<p>&bull; {{ $error }}</p>@endforeach
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <h2 class="font-semibold text-gray-800 mb-4">Tambah Jadwal</h2>
        <form action="{{ route('dosen.jadwal.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
            @csrf
            <div>
                <label class="text-xs font-medium text-gray-600 block mb-1">Hari</label>
                <select name="day" required class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm">
                    @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $d)
                        <option value="{{ $d }}">{{ $d }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-medium text-gray-600 block mb-1">Jam Mulai</label>
                <input type="time" name="start_time" required class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm">
            </div>
            <div>
                <label class="text-xs font-medium text-gray-600 block mb-1">Jam Selesai</label>
                <input type="time" name="end_time" required class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm">
            </div>
            <div>
                <label class="text-xs font-medium text-gray-600 block mb-1">Status</label>
                <select name="status" required class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm">
                    <option value="Tersedia">Tersedia</option>
                    <option value="Tidak Tersedia">Tidak Tersedia</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-medium text-gray-600 block mb-1">Keterangan</label>
                <input type="text" name="description" placeholder="Opsional" class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm">
            </div>
            <div class="md:col-span-5">
                <button type="submit" class="bg-del text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-del-light">+ Tambah Jadwal</button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                <tr>
                    <th class="px-5 py-3 text-left">Hari</th>
                    <th class="px-5 py-3 text-left">Jam</th>
                    <th class="px-5 py-3 text-left">Status</th>
                    <th class="px-5 py-3 text-left">Keterangan</th>
                    <th class="px-5 py-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($jadwals as $j)
                <tr>
                    <td class="px-5 py-3 font-medium text-gray-800">{{ $j->day }}</td>
                    <td class="px-5 py-3 text-gray-600">{{ substr($j->start_time,0,5) }} - {{ substr($j->end_time,0,5) }}</td>
                    <td class="px-5 py-3">
                        <span class="text-xs px-2 py-1 rounded-full {{ $j->status === 'Tersedia' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">{{ $j->status }}</span>
                    </td>
                    <td class="px-5 py-3 text-gray-500">{{ $j->description ?? '-' }}</td>
                    <td class="px-5 py-3">
                        <form action="{{ route('dosen.jadwal.destroy', $j->id) }}" method="POST" onsubmit="return confirm('Hapus jadwal ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-500 hover:underline">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-5 py-10 text-center text-gray-400 text-sm">Belum ada jadwal.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
