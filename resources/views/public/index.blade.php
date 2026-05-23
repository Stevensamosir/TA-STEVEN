@extends('layouts.app')
@section('title', 'Dosen Fakultas Vokasi')

@section('content')
<!-- Hero -->
<section class="bg-gradient-to-br from-del to-del-light text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-4 mb-6">
            <img src="/images/logo-del.png" alt="Logo IT Del"
                 class="h-16 w-auto brightness-0 invert drop-shadow-lg"
                 onerror="this.style.display='none'">
            <div>
                <p class="text-blue-200 text-xs font-medium uppercase tracking-widest">Institut Teknologi Del</p>
                <p class="text-white text-sm font-semibold">Fakultas Vokasi</p>
            </div>
        </div>
        <div class="max-w-2xl">
            <h1 class="text-4xl font-bold mb-4 leading-tight" style="font-family:'Playfair Display',serif;">
                Profil Dosen<br>Fakultas Vokasi
            </h1>
            <p class="text-blue-100 text-lg mb-8">Temukan dosen-dosen terbaik kami beserta rekam jejak akademik dan kontribusi riset mereka.</p>

            <!-- Search Bar -->
            <form action="{{ route('home') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="w-full pl-10 pr-4 py-3 rounded-xl text-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-white/50"
                        placeholder="Cari nama dosen atau kepakaran...">
                </div>
                <select name="prodi" class="px-4 py-3 rounded-xl text-gray-800 text-sm focus:outline-none bg-white min-w-[160px]">
                    <option value="">Semua Prodi</option>
                    @foreach($studyPrograms as $prodi)
                        <option value="{{ $prodi->id }}" {{ request('prodi') == $prodi->id ? 'selected' : '' }}>
                            {{ $prodi->name }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="bg-white text-del font-semibold px-6 py-3 rounded-xl hover:bg-blue-50 transition-colors text-sm shadow">
                    Cari
                </button>
            </form>
        </div>
    </div>
</section>

<!-- Dosen Grid -->
<section class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        @if(request('search') || request('prodi'))
        <div class="mb-6 flex items-center gap-2">
            <p class="text-gray-600 text-sm">Menampilkan <strong>{{ $lecturers->count() }}</strong> hasil</p>
            <a href="{{ route('home') }}" class="text-xs text-red-500 hover:underline ml-2">× Reset filter</a>
        </div>
        @endif

        @if($lecturers->isEmpty())
            <div class="text-center py-16">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                <p class="text-gray-500">Tidak ada dosen ditemukan.</p>
            </div>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($lecturers as $lecturer)
            <a href="{{ route('public.dosen.show', $lecturer->id) }}"
               class="bg-white rounded-2xl border border-gray-100 hover:shadow-lg hover:border-del/30 transition-all duration-200 overflow-hidden group">

                <!-- Photo — FIX: object-position agar wajah tidak terpotong -->
                <div class="h-48 bg-gradient-to-br from-blue-50 to-blue-100 relative overflow-hidden">
                    @if($lecturer->photo)
                        <img src="{{ Storage::url($lecturer->photo) }}"
                             alt="{{ $lecturer->user->name }}"
                             class="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <div class="w-20 h-20 rounded-full bg-del/10 border-4 border-del/20 flex items-center justify-center">
                                <span class="text-del font-bold text-3xl">{{ strtoupper(substr($lecturer->user->name, 0, 1)) }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Logo watermark kecil -->
                    <div class="absolute top-2 right-2 opacity-70">
                        <img src="/images/logo-del.png" alt="IT Del" class="h-6 w-auto brightness-0 invert drop-shadow"
                             onerror="this.style.display='none'">
                    </div>

                    <!-- Prodi Badge -->
                    <div class="absolute bottom-2 left-2">
                        <span class="bg-del/90 backdrop-blur-sm text-white text-xs font-medium px-2.5 py-1 rounded-full">
                            {{ $lecturer->studyProgram->name ?? '-' }}
                        </span>
                    </div>
                </div>

                <!-- Info -->
                <div class="p-4">
                    <h3 class="font-semibold text-gray-800 text-sm mb-1 group-hover:text-del transition-colors line-clamp-2 break-words">
                        {{ $lecturer->user->name }}
                    </h3>
                    @if($lecturer->nidn)
                        <p class="text-xs text-gray-400 mb-2">NIDN: {{ $lecturer->nidn }}</p>
                    @endif
                    @if($lecturer->expertise)
                        <p class="text-xs text-gray-500 line-clamp-2 mb-3">{{ $lecturer->expertise }}</p>
                    @endif

                    <!-- Stats -->
                    <div class="flex gap-4 pt-3 border-t border-gray-50">
                        <div class="text-center">
                            <div class="text-sm font-bold text-del">{{ $lecturer->publications()->where('visibility','public')->count() }}</div>
                            <div class="text-xs text-gray-400">Publikasi</div>
                        </div>
                        <div class="text-center">
                            <div class="text-sm font-bold text-del">{{ $lecturer->researches()->where('visibility','public')->count() }}</div>
                            <div class="text-xs text-gray-400">Penelitian</div>
                        </div>
                        <div class="text-center">
                            <div class="text-sm font-bold text-del">{{ $lecturer->communityServices()->where('visibility','public')->count() }}</div>
                            <div class="text-xs text-gray-400">Pengabdian</div>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        @endif
    </div>
</section>
@endsection
