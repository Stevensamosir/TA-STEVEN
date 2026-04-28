@extends('layouts.app')
@section('title', $lecturer->user->name)
@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Back -->
    <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-del mb-6 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali ke Daftar Dosen
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- LEFT: Profil Card -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
                <!-- Photo -->
                <div class="h-52 bg-gradient-to-br from-del to-del-light flex items-center justify-center">
                    @if($lecturer->photo)
                        <img src="{{ Storage::url($lecturer->photo) }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-24 h-24 rounded-full bg-white/20 border-4 border-white/40 flex items-center justify-center">
                            <span class="text-white font-bold text-4xl">{{ strtoupper(substr($lecturer->user->name, 0, 1)) }}</span>
                        </div>
                    @endif
                </div>

                <div class="p-5">
                    <h1 class="text-xl font-bold text-gray-800 mb-1">{{ $lecturer->user->name }}</h1>
                    @if($lecturer->nidn)
                        <p class="text-sm text-gray-400 mb-3">NIDN: {{ $lecturer->nidn }}</p>
                    @endif
                    @if($lecturer->studyProgram)
                        <span class="inline-block bg-blue-50 text-del text-xs font-semibold px-3 py-1 rounded-full mb-4">
                            {{ $lecturer->studyProgram->name }}
                        </span>
                    @endif
                    @if($lecturer->expertise)
                        <div class="pt-4 border-t border-gray-100">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Kepakaran</p>
                            <p class="text-sm text-gray-700 leading-relaxed">{{ $lecturer->expertise }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Stats -->
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">Ringkasan</p>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-blue-50 rounded-xl p-3 text-center">
                        <div class="text-2xl font-bold text-del">{{ $lecturer->educations->count() }}</div>
                        <div class="text-xs text-gray-500 mt-1">Pendidikan</div>
                    </div>
                    <div class="bg-blue-50 rounded-xl p-3 text-center">
                        <div class="text-2xl font-bold text-del">{{ $lecturer->researches->count() }}</div>
                        <div class="text-xs text-gray-500 mt-1">Penelitian</div>
                    </div>
                    <div class="bg-blue-50 rounded-xl p-3 text-center">
                        <div class="text-2xl font-bold text-del">{{ $lecturer->communityServices->count() }}</div>
                        <div class="text-xs text-gray-500 mt-1">Pengabdian</div>
                    </div>
                    <div class="bg-blue-50 rounded-xl p-3 text-center">
                        <div class="text-2xl font-bold text-del">{{ $lecturer->publications->count() }}</div>
                        <div class="text-xs text-gray-500 mt-1">Publikasi</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT: Detail -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Grafik Performa -->
            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
                <h2 class="text-base font-bold text-gray-800 mb-4">Grafik Performa Akademik</h2>
                <canvas id="performaChart" height="120"></canvas>
            </div>

            <!-- Pendidikan -->
            @if($lecturer->educations->isNotEmpty())
            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
                <h2 class="text-base font-bold text-gray-800 mb-4">Riwayat Pendidikan</h2>
                <div class="space-y-3">
                    @foreach($lecturer->educations as $edu)
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-full bg-del flex items-center justify-center flex-shrink-0 mt-0.5">
                            <span class="text-white text-xs font-bold">{{ $edu->degree }}</span>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ $edu->institution }}</p>
                            @if($edu->major)<p class="text-xs text-gray-500">{{ $edu->major }}</p>@endif
                            <p class="text-xs text-gray-400">{{ $edu->year }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Penelitian -->
            @if($lecturer->researches->isNotEmpty())
            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
                <h2 class="text-base font-bold text-gray-800 mb-4">Penelitian</h2>
                <div class="space-y-3">
                    @foreach($lecturer->researches as $item)
                    <div class="flex items-start gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-del" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-800">{{ $item->title }}</p>
                            <div class="flex gap-3 mt-1">
                                <span class="text-xs text-gray-400">{{ $item->year }}</span>
                                @if($item->funding_source)
                                <span class="text-xs text-gray-400">· {{ $item->funding_source }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Pengabdian -->
            @if($lecturer->communityServices->isNotEmpty())
            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
                <h2 class="text-base font-bold text-gray-800 mb-4">Pengabdian Masyarakat</h2>
                <div class="space-y-3">
                    @foreach($lecturer->communityServices as $item)
                    <div class="flex items-start gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors">
                        <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $item->title }}</p>
                            <div class="flex gap-3 mt-1">
                                <span class="text-xs text-gray-400">{{ $item->year }}</span>
                                @if($item->location)<span class="text-xs text-gray-400">· {{ $item->location }}</span>@endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Publikasi -->
            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-bold text-gray-800">Publikasi</h2>
                    <!-- Filter Tahun -->
                    <form action="{{ route('public.dosen.show', $lecturer->id) }}" method="GET" class="flex items-center gap-2">
                        <select name="year" onchange="this.form.submit()"
                            class="text-xs border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-1 focus:ring-del">
                            <option value="">Semua Tahun</option>
                            @foreach($pubYears as $y)
                                <option value="{{ $y }}" {{ $filterYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                        @if($filterYear)
                        <a href="{{ route('public.dosen.show', $lecturer->id) }}" class="text-xs text-red-400 hover:underline">Reset</a>
                        @endif
                    </form>
                </div>

                @if($publications->isEmpty())
                    <p class="text-sm text-gray-400 text-center py-4">Belum ada publikasi.</p>
                @else
                <div class="space-y-3">
                    @foreach($publications as $pub)
                    <div class="flex items-start gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors">
                        <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <div class="flex-1">
                            @if($pub->publisher_url)
                                <a href="{{ $pub->publisher_url }}" target="_blank" class="text-sm font-medium text-del hover:underline">{{ $pub->title }}</a>
                            @else
                                <p class="text-sm font-medium text-gray-800">{{ $pub->title }}</p>
                            @endif
                            <div class="flex flex-wrap gap-3 mt-1">
                                <span class="text-xs text-gray-400">{{ $pub->year }}</span>
                                @if($pub->publisher)
                                    <span class="text-xs text-gray-400">· {{ $pub->publisher }}</span>
                                @endif
                                @if($pub->publisher_url)
                                    <a href="{{ $pub->publisher_url }}" target="_blank" class="text-xs text-del flex items-center gap-1 hover:underline">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        Lihat Sumber
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const ctx = document.getElementById('performaChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($years) !!},
        datasets: [
            {
                label: 'Penelitian',
                data: {!! json_encode($researchData) !!},
                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                borderRadius: 6,
            },
            {
                label: 'Pengabdian',
                data: {!! json_encode($serviceData) !!},
                backgroundColor: 'rgba(16, 185, 129, 0.8)',
                borderRadius: 6,
            },
            {
                label: 'Publikasi',
                data: {!! json_encode($pubData) !!},
                backgroundColor: 'rgba(139, 92, 246, 0.8)',
                borderRadius: 6,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 } }
        }
    }
});
</script>
@endpush
