@extends('layouts.dashboard')
@section('title', 'Kelola Dosen')
@section('page-title', 'Kelola Dosen')

@section('content')
<div class="pt-4 space-y-4">

    @if($errors->has('dosen'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
            {{ $errors->first('dosen') }}
        </div>
    @endif

    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
        <p class="text-sm text-gray-500">Total: <strong>{{ $dosens->count() }}</strong> dosen</p>
        <a href="{{ route('admin.dosen.create') }}" class="bg-del text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-del-light transition-colors flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Dosen
        </a>
    </div>

    @php
        // Hitung sekali di sini supaya tidak duplikat logika antara versi tabel & versi kartu
        $rows = $dosens->map(function ($d) use ($activeDekanCount) {
            $totalData = $d->educations_count + $d->researches_count
                       + $d->community_services_count + $d->publications_count;
            $isSelf      = $d->user_id === auth()->id();
            $isLastDekan = $d->user->role === 'dekan' && $activeDekanCount <= 1;
            $hapusLocked = $totalData > 0 || $isSelf || $isLastDekan;
            $hapusLockReason = $isSelf
                ? 'Anda tidak bisa menghapus akun Anda sendiri.'
                : ($isLastDekan
                    ? 'Tidak bisa menghapus satu-satunya akun Dekan aktif.'
                    : 'Dosen ini sudah punya data Tridharma — gunakan Nonaktifkan.');
            return [
                'd' => $d,
                'hapusLocked' => $hapusLocked,
                'hapusLockReason' => $hapusLockReason,
            ];
        });
    @endphp

    {{-- ════════════════ VERSI TABEL (desktop/tablet, md ke atas) ════════════════ --}}
    <div class="hidden md:block bg-white rounded-2xl border border-gray-100 overflow-visible">
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
                    @foreach($rows as $row)
                    @php $d = $row['d']; @endphp
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-3 font-medium text-gray-800">{{ $d->user->name }}</td>
                        <td class="px-5 py-3 text-gray-500 text-xs">{{ $d->user->email }}</td>
                        <td class="px-5 py-3 text-gray-500 text-xs">{{ $d->studyProgram->name ?? '-' }}</td>
                        <td class="px-5 py-3">
                            <span class="text-xs font-medium px-2 py-1 rounded-full
                                {{ $d->user->role === 'dekan'   ? 'bg-red-50 text-red-700'    :
                                  ($d->user->role === 'kaprodi' ? 'bg-yellow-50 text-yellow-700' :
                                                                  'bg-blue-50 text-del') }}">
                                {{ ucfirst($d->user->role) }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <form action="{{ route('admin.dosen.toggle-active', $d->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    class="text-xs font-medium px-2.5 py-1 rounded-full
                                        {{ $d->user->is_active ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600' }}">
                                    {{ $d->user->is_active ? '✓ Aktif' : '✗ Nonaktif' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-5 py-3">
                            <form action="{{ route('admin.dosen.visibility', $d->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    class="text-xs font-medium px-2.5 py-1 rounded-full
                                        {{ $d->is_public ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $d->is_public ? 'Publik' : 'Internal' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3 relative">
                                <a href="{{ route('admin.dosen.edit', $d->id) }}"
                                   class="text-xs text-del hover:underline font-medium">Edit</a>

                                <button type="button" onclick="toggleMenu('{{ $d->id }}-d')"
                                        class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-md p-1 transition-colors"
                                        aria-label="Menu aksi lainnya">
                                    <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                                        <circle cx="10" cy="4"  r="1.6"/><circle cx="10" cy="10" r="1.6"/><circle cx="10" cy="16" r="1.6"/>
                                    </svg>
                                </button>

                                @include('admin.partials.dosen-action-menu', ['row' => $row, 'suffix' => 'd'])
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ════════════════ VERSI KARTU (mobile, di bawah md) ════════════════ --}}
    <div class="md:hidden bg-white rounded-2xl border border-gray-100 divide-y divide-gray-50 overflow-visible">
        @foreach($rows as $row)
        @php $d = $row['d']; @endphp
        <div class="p-4 space-y-3">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="font-medium text-gray-800 text-sm truncate">{{ $d->user->name }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ $d->user->email }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $d->studyProgram->name ?? '-' }}</p>
                </div>
                <span class="text-xs font-medium px-2 py-1 rounded-full flex-shrink-0
                    {{ $d->user->role === 'dekan'   ? 'bg-red-50 text-red-700'    :
                      ($d->user->role === 'kaprodi' ? 'bg-yellow-50 text-yellow-700' :
                                                      'bg-blue-50 text-del') }}">
                    {{ ucfirst($d->user->role) }}
                </span>
            </div>

            <div class="flex flex-wrap gap-2">
                <form action="{{ route('admin.dosen.toggle-active', $d->id) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit"
                        class="text-xs font-medium px-2.5 py-1 rounded-full
                            {{ $d->user->is_active ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600' }}">
                        {{ $d->user->is_active ? '✓ Aktif' : '✗ Nonaktif' }}
                    </button>
                </form>
                <form action="{{ route('admin.dosen.visibility', $d->id) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit"
                        class="text-xs font-medium px-2.5 py-1 rounded-full
                            {{ $d->is_public ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $d->is_public ? 'Publik' : 'Internal' }}
                    </button>
                </form>
            </div>

            <div class="flex items-center justify-between pt-1">
                <a href="{{ route('admin.dosen.edit', $d->id) }}"
                   class="text-xs text-del hover:underline font-medium">Edit Dosen</a>

                <div class="relative">
                    <button type="button" onclick="toggleMenu('{{ $d->id }}-m')"
                            class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-md p-1.5 transition-colors"
                            aria-label="Menu aksi lainnya">
                        <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                            <circle cx="10" cy="4"  r="1.6"/><circle cx="10" cy="10" r="1.6"/><circle cx="10" cy="16" r="1.6"/>
                        </svg>
                    </button>
                    @include('admin.partials.dosen-action-menu', ['row' => $row, 'suffix' => 'm'])
                </div>
            </div>
        </div>
        @endforeach
    </div>

</div>

@push('scripts')
<script>
    // Buka/tutup menu titik-tiga, dan otomatis pilih arah (ke bawah / ke atas)
    // supaya tidak pernah ketutupan tepi layar — dicek ulang tiap kali dibuka.
    function toggleMenu(id) {
        const target = document.getElementById('menu-' + id);
        if (!target) return;
        const isHidden = target.classList.contains('hidden');

        document.querySelectorAll('[id^="menu-"]').forEach(m => m.classList.add('hidden'));

        if (isHidden) {
            target.classList.remove('hidden');
            positionMenu(target);
        }
    }

    function positionMenu(menu) {
        // Reset ke posisi default (buka ke bawah) dulu sebelum diukur ulang
        menu.classList.remove('bottom-full', 'mb-1');
        menu.classList.add('top-full', 'mt-1');

        const rect = menu.getBoundingClientRect();
        const spaceBelow = window.innerHeight - rect.bottom;

        // Kalau bagian bawah menu keluar dari layar (atau sangat mepet), buka ke atas
        if (spaceBelow < 8) {
            menu.classList.remove('top-full', 'mt-1');
            menu.classList.add('bottom-full', 'mb-1');
        }
    }

    document.addEventListener('click', function (e) {
        if (!e.target.closest('[id^="menu-"]') && !e.target.closest('button[onclick^="toggleMenu"]')) {
            document.querySelectorAll('[id^="menu-"]').forEach(m => m.classList.add('hidden'));
        }
    });

    // Tutup ulang & posisikan ulang menu saat layar di-resize (rotasi HP, dst)
    window.addEventListener('resize', function () {
        document.querySelectorAll('[id^="menu-"]').forEach(m => m.classList.add('hidden'));
    });
</script>
@endpush
@endsection
