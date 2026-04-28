@extends('layouts.dashboard')
@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard Admin')

@section('content')
<div class="pt-4 space-y-6">

    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-del-dark to-del rounded-2xl p-6 text-white">
        <p class="text-blue-200 text-sm mb-1">Panel Administrasi</p>
        <h2 class="text-xl font-bold">Sistem Informasi Profil Dosen Vokasi</h2>
        <p class="text-blue-200 text-sm mt-1">{{ auth()->user()->name }} · {{ ucfirst(auth()->user()->role) }}</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['label' => 'Total Dosen', 'value' => $stats['total_dosen'], 'color' => 'text-del', 'bg' => 'bg-blue-50'],
            ['label' => 'Profil Publik', 'value' => $stats['total_publik'], 'color' => 'text-green-700', 'bg' => 'bg-green-50'],
            ['label' => 'Program Studi', 'value' => $stats['total_prodi'], 'color' => 'text-purple-700', 'bg' => 'bg-purple-50'],
            ['label' => 'Total Publikasi', 'value' => $stats['total_publikasi'], 'color' => 'text-orange-700', 'bg' => 'bg-orange-50'],
        ] as $stat)
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <div class="text-3xl font-bold {{ $stat['color'] }}">{{ $stat['value'] }}</div>
            <div class="text-sm text-gray-500 mt-1">{{ $stat['label'] }}</div>
        </div>
        @endforeach
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('admin.dosen.create') }}" class="bg-del text-white rounded-2xl p-5 hover:bg-del-light transition-colors text-center">
            <svg class="w-6 h-6 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            <p class="text-sm font-semibold">Tambah Dosen</p>
        </a>
        <a href="{{ route('admin.dosen') }}" class="bg-white border border-gray-100 rounded-2xl p-5 hover:shadow-md transition-all text-center">
            <svg class="w-6 h-6 mx-auto mb-2 text-del" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
            <p class="text-sm font-semibold text-gray-700">Kelola Dosen</p>
        </a>
        <a href="{{ route('admin.hierarki') }}" class="bg-white border border-gray-100 rounded-2xl p-5 hover:shadow-md transition-all text-center">
            <svg class="w-6 h-6 mx-auto mb-2 text-del" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
            <p class="text-sm font-semibold text-gray-700">Hierarki Prodi</p>
        </a>
        <a href="{{ route('admin.internal') }}" class="bg-white border border-gray-100 rounded-2xl p-5 hover:shadow-md transition-all text-center">
            <svg class="w-6 h-6 mx-auto mb-2 text-del" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <p class="text-sm font-semibold text-gray-700">Data Internal</p>
        </a>
    </div>

    <!-- Recent Dosen -->
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">Dosen Terbaru</h3>
            <a href="{{ route('admin.dosen') }}" class="text-sm text-del hover:underline">Lihat semua</a>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                <tr>
                    <th class="px-5 py-3 text-left">Nama</th>
                    <th class="px-5 py-3 text-left">Prodi</th>
                    <th class="px-5 py-3 text-left">Status</th>
                    <th class="px-5 py-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($recentDosen as $d)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-5 py-3 font-medium text-gray-800">{{ $d->user->name }}</td>
                    <td class="px-5 py-3 text-gray-500 text-xs">{{ $d->studyProgram->name ?? '-' }}</td>
                    <td class="px-5 py-3">
                        <span class="text-xs font-medium px-2 py-1 rounded-full {{ $d->is_public ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $d->is_public ? 'Publik' : 'Internal' }}
                        </span>
                    </td>
                    <td class="px-5 py-3">
                        <a href="{{ route('admin.dosen.edit', $d->id) }}" class="text-xs text-del hover:underline">Edit</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
