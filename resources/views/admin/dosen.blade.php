@extends('layouts.dashboard')
@section('title', 'Kelola Dosen')
@section('page-title', 'Kelola Dosen')

@section('content')
<div class="pt-4 space-y-4">
    <div class="flex justify-between items-center">
        <p class="text-sm text-gray-500">Total: <strong>{{ $dosens->count() }}</strong> dosen</p>
        <a href="{{ route('admin.dosen.create') }}" class="bg-del text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-del-light transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Dosen
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3 text-left">Nama</th>
                        <th class="px-5 py-3 text-left">Email</th>
                        <th class="px-5 py-3 text-left">Prodi</th>
                        <th class="px-5 py-3 text-left">Role</th>
                        <th class="px-5 py-3 text-left">Status Akun</th>
                        <th class="px-5 py-3 text-left">Profil</th>
                        <th class="px-5 py-3 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($dosens as $d)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-3 font-medium text-gray-800">{{ $d->user->name }}</td>
                        <td class="px-5 py-3 text-gray-500 text-xs">{{ $d->user->email }}</td>
                        <td class="px-5 py-3 text-gray-500 text-xs">{{ $d->studyProgram->name ?? '-' }}</td>
                        <td class="px-5 py-3">
                            <span class="text-xs font-medium px-2 py-1 rounded-full
                                {{ $d->user->role === 'dekan' ? 'bg-red-50 text-red-700' : ($d->user->role === 'kaprodi' ? 'bg-yellow-50 text-yellow-700' : 'bg-blue-50 text-del') }}">
                                {{ ucfirst($d->user->role) }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <form action="{{ route('admin.dosen.toggle-active', $d->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-xs font-medium px-2.5 py-1 rounded-full {{ $d->user->is_active ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600' }}">
                                    {{ $d->user->is_active ? '✓ Aktif' : '✗ Nonaktif' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-5 py-3">
                            <form action="{{ route('admin.dosen.visibility', $d->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-xs font-medium px-2.5 py-1 rounded-full {{ $d->is_public ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $d->is_public ? 'Publik' : 'Internal' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.dosen.edit', $d->id) }}" class="text-xs text-del hover:underline">Edit</a>
                                <form action="{{ route('admin.dosen.reset-password', $d->id) }}" method="POST" onsubmit="return confirm('Reset password ke default?')">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-xs text-orange-500 hover:underline">Reset PW</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
