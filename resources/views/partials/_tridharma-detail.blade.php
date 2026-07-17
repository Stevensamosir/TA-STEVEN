{{--
    Partial detail Tridharma dosen (dipakai bersama).
    Menampilkan SEMUA data internal dosen (termasuk yang ditandai Privat),
    tidak difilter visibility -- karena ini untuk konsumsi internal fakultas.

    Dipakai oleh:
      - admin/internal-show.blade.php  (Dekan & Kaprodi, layout dashboard)
      - lppm/dosen-show.blade.php      (LPPM, layout lppm)

    Parameter:
      $lecturer   (wajib)  Model Lecturer yang sudah eager-load relasi tridharma.
      $backUrl    (opsional) URL tombol "kembali". Kalau null, tombol tidak muncul.
      $backLabel  (opsional) Teks tombol kembali. Default: "Kembali".

    Catatan: <script> sengaja INLINE (bukan @push) supaya partial ini
    layout-agnostic -- berjalan sama baik di layout dashboard maupun lppm.
--}}
@php
    $backUrl   = $backUrl   ?? null;
    $backLabel = $backLabel ?? 'Kembali';

    // Kontrol tombol Edit per-tab. Default KOSONG -> tidak ada tombol edit
    // (perilaku untuk admin/internal-show Kaprodi & Dekan tetap seperti semula).
    // lppm/dosen-show mengisi $editableTypes = ['penelitian','pengabdian'] dan
    // $editRoutePrefix = 'lppm' supaya tombol edit hanya muncul di sisi LPPM.
    $editableTypes    = $editableTypes    ?? [];
    $editRoutePrefix  = $editRoutePrefix  ?? null;
    $canEditPenelitian = $editRoutePrefix && in_array('penelitian', $editableTypes);
    $canEditPengabdian = $editRoutePrefix && in_array('pengabdian', $editableTypes);

    // Daftar bulan untuk dropdown di form edit inline.
    $bulanList = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

@endphp

<style>
    .tab-btn.active { background:#003087; color:#fff; }
    .tab-btn { transition: all .2s; }
    .tab-panel { display:none; }
    .tab-panel.active { display:block; }
    .timeline-dot::before {
        content:''; position:absolute; left:-1.1rem; top:.3rem;
        width:.6rem; height:.6rem; border-radius:50%; background:#003087;
    }
</style>

<div class="pt-4 space-y-4">
    @if($backUrl)
    <a href="{{ $backUrl }}" class="text-sm text-del hover:underline inline-flex items-center gap-1">
        &larr; {{ $backLabel }}
    </a>
    @endif

    {{-- Header info dosen --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-6 flex items-center gap-4">
        @if($lecturer->photo)
            <img src="{{ Storage::url($lecturer->photo) }}" alt="{{ $lecturer->user->name }}"
                 class="w-20 h-20 rounded-full object-cover ring-2 ring-del/20 flex-shrink-0">
        @else
            <div class="w-20 h-20 rounded-full bg-gradient-to-br from-del to-blue-600 flex items-center justify-center flex-shrink-0">
                <span class="text-white font-bold text-2xl select-none">{{ strtoupper(substr($lecturer->user->name, 0, 1)) }}</span>
            </div>
        @endif
        <div class="min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <h2 class="text-lg font-bold text-gray-800">{{ $lecturer->user->name }}</h2>
                <span class="text-xs font-medium px-2 py-0.5 rounded-full
                    {{ $lecturer->is_public ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ $lecturer->is_public ? 'Profil Publik' : 'Profil Internal' }}
                </span>
            </div>
            <p class="text-sm text-gray-500 mt-1">
                {{ $lecturer->studyProgram->name ?? '-' }} &middot;
                NIDN: {{ $lecturer->nidn ?? '-' }} &middot;
                {{ $lecturer->jabatan_fungsional ?? '-' }}
            </p>
            @if($lecturer->user->email)
                <p class="text-xs text-gray-400 mt-0.5">{{ $lecturer->user->email }}</p>
            @endif
        </div>
    </div>

    {{-- Tab navigasi + isi --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="flex border-b border-gray-100 px-4 pt-4 gap-1 overflow-x-auto">
            @foreach([
                ['pendidikan',   'Pendidikan',   $lecturer->educations->count()],
                ['penelitian',   'Penelitian',   $lecturer->researches->count()],
                ['pengabdian',   'Pengabdian',   $lecturer->communityServices->count()],
                ['publikasi',    'Publikasi',    $lecturer->publications->count()],
                ['buku',         'Buku',         $lecturer->books->count()],
                ['hki',          'HKI',          $lecturer->hkis->count()],
                ['penghargaan',  'Penghargaan',  $lecturer->awards->count()],
            ] as [$key, $label, $count])
            <button onclick="switchTab('{{ $key }}')" id="tab-btn-{{ $key }}"
                    class="tab-btn flex items-center gap-1.5 text-sm font-medium px-4 py-2 rounded-t-lg text-gray-500 hover:text-del whitespace-nowrap">
                {{ $label }}
                <span class="text-xs bg-gray-100 px-1.5 py-0.5 rounded-full">{{ $count }}</span>
            </button>
            @endforeach
        </div>

        <div class="p-5">

            {{-- TAB: PENDIDIKAN --}}
            <div id="tab-pendidikan" class="tab-panel">
                @if($lecturer->educations->isEmpty())
                    <p class="text-sm text-gray-500 text-center py-8">Belum ada data pendidikan.</p>
                @else
                <div class="relative pl-6 border-l-2 border-gray-100 space-y-5">
                    @foreach($lecturer->educations as $edu)
                    <div class="relative timeline-dot">
                        <div class="flex items-start justify-between">
                            <div>
                                <span class="inline-block text-xs font-bold bg-del text-white px-2 py-0.5 rounded mb-1">{{ $edu->degree }}</span>
                                <p class="font-semibold text-gray-800 text-sm">{{ $edu->institution }}</p>
                                @if($edu->major)
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $edu->major }}</p>
                                @endif
                            </div>
                            <div class="flex items-center gap-2">
                                                                <span class="text-xs text-gray-400 font-medium bg-gray-50 px-2 py-1 rounded-lg">{{ $edu->year }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- TAB: PENELITIAN --}}
            <div id="tab-penelitian" class="tab-panel">
                @if($lecturer->researches->isEmpty())
                    <p class="text-sm text-gray-500 text-center py-8">Belum ada data penelitian.</p>
                @else
                <div class="space-y-3">
                    @foreach($lecturer->researches as $item)
                    <div class="rounded-xl border border-transparent {{ $canEditPenelitian ? 'hover:border-blue-100' : '' }}">
                    <div class="flex items-start gap-3 p-3 rounded-xl hover:bg-blue-50/50 transition-colors border border-transparent hover:border-blue-100">
                        <div class="w-9 h-9 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-del" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <p class="text-sm font-medium text-gray-800 leading-snug">{{ $item->title }}</p>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                                                        @if($canEditPenelitian)
                                        <button type="button"
                                                onclick="document.getElementById('edit-penelitian-{{ $item->id }}').classList.toggle('hidden')"
                                                class="text-xs bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 px-2.5 py-0.5 rounded-lg font-medium transition-colors">
                                            Edit
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-2 mt-1.5">
                                <span class="text-xs text-gray-400 bg-gray-50 px-2 py-0.5 rounded">{{ $item->year }}</span>
                                @if($item->funding_source)
                                    <span class="text-xs text-gray-400 bg-gray-50 px-2 py-0.5 rounded">{{ $item->funding_source }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($canEditPenelitian)
                    {{-- Form edit inline (mengikuti pola toggle di dosen/penelitian.blade.php) --}}
                    <div id="edit-penelitian-{{ $item->id }}" class="hidden px-3 pb-3">
                        <form action="{{ route($editRoutePrefix . '.penelitian.update', $item->id) }}" method="POST"
                              class="bg-blue-50/40 border border-blue-100 rounded-xl p-3 space-y-3">
                            @csrf @method('PUT')
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div class="md:col-span-3">
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Judul Penelitian</label>
                                    <input type="text" name="title" value="{{ $item->title }}" required
                                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Tahun</label>
                                    <input type="number" name="year" value="{{ $item->year }}" min="1970" max="{{ date('Y') }}" required
                                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Bulan</label>
                                    <select name="month" required
                                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                                        <option value=""> - Pilih - </option>
                                        @foreach($bulanList as $i => $bulan)
                                            <option value="{{ $i+1 }}" {{ (int) $item->month === $i+1 ? 'selected' : '' }}>{{ $bulan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Sumber Dana</label>
                                <input type="text" name="funding_source" value="{{ $item->funding_source }}"
                                       class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" class="bg-del text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-del-light transition">Simpan</button>
                                <button type="button"
                                        onclick="document.getElementById('edit-penelitian-{{ $item->id }}').classList.add('hidden')"
                                        class="px-4 py-2 rounded-lg text-sm text-gray-500 border border-gray-200 hover:bg-gray-50 transition">Batal</button>
                            </div>
                        </form>
                    </div>
                    @endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- TAB: PENGABDIAN (PKM, many-to-many) --}}
            <div id="tab-pengabdian" class="tab-panel">
                @if($lecturer->communityServices->isEmpty())
                    <p class="text-sm text-gray-500 text-center py-8">Belum ada data pengabdian.</p>
                @else
                <div class="space-y-3">
                    @foreach($lecturer->communityServices as $item)
                    <div class="rounded-xl border border-transparent {{ $canEditPengabdian ? 'hover:border-emerald-100' : '' }}">
                    <div class="flex items-start gap-3 p-3 rounded-xl hover:bg-emerald-50/50 transition-colors border border-transparent hover:border-emerald-100">
                        <div class="w-9 h-9 rounded-lg bg-emerald-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <p class="text-sm font-medium text-gray-800 leading-snug">{{ $item->title }}</p>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                                                        @if($canEditPengabdian)
                                        <button type="button"
                                                onclick="document.getElementById('edit-pengabdian-{{ $item->id }}').classList.toggle('hidden')"
                                                class="text-xs bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 px-2.5 py-0.5 rounded-lg font-medium transition-colors">
                                            Edit
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-2 mt-1.5">
                                <span class="text-xs text-gray-400 bg-gray-50 px-2 py-0.5 rounded">{{ $item->year }}</span>
                                @if($item->location)
                                    <span class="text-xs text-gray-400 bg-gray-50 px-2 py-0.5 rounded">📍 {{ $item->location }}</span>
                                @endif
                                @if($item->pkm_type)
                                    <span class="text-xs text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded font-medium">{{ $item->pkm_type }}</span>
                                @endif
                                @if($item->pkm_scheme)
                                    <span class="text-xs text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded font-medium">{{ $item->pkm_scheme }}</span>
                                @endif
                            </div>
                            @if($item->lecturers->isNotEmpty())
                                <p class="text-xs text-gray-500 mt-1.5">
                                    <span class="font-medium">Tim Dosen:</span>
                                    {{ $item->lecturers->map(fn($l) => ($l->user->name ?? '-') . ' (' . ($l->pivot->role ?? '-') . ')')->join(', ') }}
                                </p>
                            @endif
                            @if($item->student_members)
                                <p class="text-xs text-gray-500 mt-1">
                                    <span class="font-medium">Anggota Mahasiswa:</span> {{ $item->student_members }}
                                </p>
                            @endif
                        </div>
                    </div>

                    @if($canEditPengabdian)
                    {{-- Form edit inline PKM. lecturer_id = dosen halaman ini; role diambil
                         dari pivot dosen ini (peran spesifik per dosen pada kegiatan). --}}
                    <div id="edit-pengabdian-{{ $item->id }}" class="hidden px-3 pb-3">
                        <form action="{{ route($editRoutePrefix . '.pkm.update', $item->id) }}" method="POST"
                              class="bg-emerald-50/40 border border-emerald-100 rounded-xl p-3 space-y-3">
                            @csrf @method('PUT')
                            <input type="hidden" name="lecturer_id" value="{{ $lecturer->id }}">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Judul Kegiatan PKM</label>
                                <input type="text" name="title" value="{{ $item->title }}" required
                                       class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Tahun</label>
                                    <input type="number" name="year" value="{{ $item->year }}" min="1970" max="{{ date('Y') }}" required
                                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Bulan</label>
                                    <select name="month" required
                                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                                        <option value=""> - Pilih - </option>
                                        @foreach($bulanList as $i => $bulan)
                                            <option value="{{ $i+1 }}" {{ (int) $item->month === $i+1 ? 'selected' : '' }}>{{ $bulan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Lokasi</label>
                                    <input type="text" name="location" value="{{ $item->location }}"
                                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Jenis PKM</label>
                                    <select name="pkm_type"
                                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                                        <option value=""> - Pilih - </option>
                                        @foreach(['Internal','Nasional','Internasional'] as $tipe)
                                            <option value="{{ $tipe }}" {{ $item->pkm_type === $tipe ? 'selected' : '' }}>{{ $tipe }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Skema PKM</label>
                                    <select name="pkm_scheme"
                                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                                        <option value=""> - Tidak masuk skema - </option>
                                        @foreach(['PKM-RE','PKM-RSH','PKM-K','PKM-PM','PKM-PI','PKM-KC','PKM-KI','PKM-VGK','PKM-AI','PKM-GFT'] as $skema)
                                            <option value="{{ $skema }}" {{ $item->pkm_scheme === $skema ? 'selected' : '' }}>{{ $skema }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Peran Dosen Ini</label>
                                    <select name="role" required
                                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                                        @foreach(['Ketua','Anggota'] as $peran)
                                            <option value="{{ $peran }}" {{ ($item->pivot->role ?? '') === $peran ? 'selected' : '' }}>{{ $peran }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Anggota Mahasiswa</label>
                                <input type="text" name="student_members" value="{{ $item->student_members }}" placeholder="Nama, dipisah koma"
                                       class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" class="bg-del text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-del-light transition">Simpan</button>
                                <button type="button"
                                        onclick="document.getElementById('edit-pengabdian-{{ $item->id }}').classList.add('hidden')"
                                        class="px-4 py-2 rounded-lg text-sm text-gray-500 border border-gray-200 hover:bg-gray-50 transition">Batal</button>
                            </div>
                        </form>
                    </div>
                    @endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- TAB: PUBLIKASI --}}
            <div id="tab-publikasi" class="tab-panel">
                @if($lecturer->publications->isEmpty())
                    <p class="text-sm text-gray-500 text-center py-8">Belum ada publikasi.</p>
                @else
                <div class="space-y-3">
                    @foreach($lecturer->publications as $pub)
                    <div class="flex items-start gap-3 p-3 rounded-xl hover:bg-violet-50/50 transition-colors border border-transparent hover:border-violet-100">
                        <div class="w-9 h-9 rounded-lg bg-violet-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 12h6m4 0h.01"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                @if($pub->publisher_url)
                                    <a href="{{ $pub->publisher_url }}" target="_blank" class="text-sm font-medium text-del hover:underline leading-snug block">{{ $pub->title }}</a>
                                @else
                                    <p class="text-sm font-medium text-gray-800 leading-snug">{{ $pub->title }}</p>
                                @endif
                                                            </div>
                            <div class="flex flex-wrap gap-2 mt-1.5">
                                <span class="text-xs text-gray-400 bg-gray-50 px-2 py-0.5 rounded">{{ $pub->year }}</span>
                                @if($pub->publisher)
                                    <span class="text-xs text-gray-400 bg-gray-50 px-2 py-0.5 rounded">{{ $pub->publisher }}</span>
                                @endif
                                @if($pub->authors)
                                    <span class="text-xs text-gray-400 italic">{{ $pub->authors }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- TAB: BUKU --}}
            <div id="tab-buku" class="tab-panel">
                @if($lecturer->books->isEmpty())
                    <p class="text-sm text-gray-500 text-center py-8">Belum ada data buku.</p>
                @else
                <div class="space-y-3">
                    @foreach($lecturer->books as $item)
                    <div class="flex items-start gap-3 p-3 rounded-xl hover:bg-amber-50/50 transition-colors border border-transparent hover:border-amber-100">
                        <div class="w-9 h-9 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <p class="text-sm font-medium text-gray-800 leading-snug">{{ $item->title }}</p>
                                                            </div>
                            <div class="flex flex-wrap gap-2 mt-1.5">
                                <span class="text-xs text-gray-400 bg-gray-50 px-2 py-0.5 rounded">{{ $item->year }}</span>
                                @if($item->publisher)
                                    <span class="text-xs text-gray-400 bg-gray-50 px-2 py-0.5 rounded">{{ $item->publisher }}</span>
                                @endif
                                @if($item->isbn)
                                    <span class="text-xs text-gray-400 bg-gray-50 px-2 py-0.5 rounded">ISBN: {{ $item->isbn }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- TAB: HKI --}}
            <div id="tab-hki" class="tab-panel">
                @if($lecturer->hkis->isEmpty())
                    <p class="text-sm text-gray-500 text-center py-8">Belum ada data HKI.</p>
                @else
                <div class="space-y-3">
                    @foreach($lecturer->hkis as $item)
                    <div class="flex items-start gap-3 p-3 rounded-xl hover:bg-rose-50/50 transition-colors border border-transparent hover:border-rose-100">
                        <div class="w-9 h-9 rounded-lg bg-rose-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <p class="text-sm font-medium text-gray-800 leading-snug">{{ $item->title }}</p>
                                                            </div>
                            <div class="flex flex-wrap gap-2 mt-1.5">
                                <span class="text-xs text-gray-400 bg-gray-50 px-2 py-0.5 rounded">{{ $item->year }}</span>
                                @if($item->type)
                                    <span class="text-xs text-gray-400 bg-gray-50 px-2 py-0.5 rounded">{{ $item->type }}</span>
                                @endif
                                @if($item->certificate_number)
                                    <span class="text-xs text-gray-400 bg-gray-50 px-2 py-0.5 rounded">No. {{ $item->certificate_number }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- TAB: PENGHARGAAN --}}
            <div id="tab-penghargaan" class="tab-panel">
                @if($lecturer->awards->isEmpty())
                    <p class="text-sm text-gray-500 text-center py-8">Belum ada data penghargaan.</p>
                @else
                <div class="space-y-3">
                    @foreach($lecturer->awards as $item)
                    <div class="flex items-start gap-3 p-3 rounded-xl hover:bg-yellow-50/50 transition-colors border border-transparent hover:border-yellow-100">
                        <div class="w-9 h-9 rounded-lg bg-yellow-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                @if($item->evidence_url)
                                    <a href="{{ $item->evidence_url }}" target="_blank" class="text-sm font-medium text-del hover:underline leading-snug block">{{ $item->name }}</a>
                                @else
                                    <p class="text-sm font-medium text-gray-800 leading-snug">{{ $item->name }}</p>
                                @endif
                                                            </div>
                            <div class="flex flex-wrap gap-2 mt-1.5">
                                @if($item->level)
                                    <span class="text-xs text-yellow-700 bg-yellow-50 px-2 py-0.5 rounded font-medium">{{ $item->level }}</span>
                                @endif
                                @if($item->date)
                                    <span class="text-xs text-gray-400 bg-gray-50 px-2 py-0.5 rounded">{{ \Carbon\Carbon::parse($item->date)->format('d M Y') }}</span>
                                @endif
                                @if($item->organizer)
                                    <span class="text-xs text-gray-400 bg-gray-50 px-2 py-0.5 rounded">{{ $item->organizer }}</span>
                                @endif
                                @if($item->rank)
                                    <span class="text-xs text-gray-400 bg-gray-50 px-2 py-0.5 rounded">{{ $item->rank }}</span>
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

<script>
function switchTab(key) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    const panel = document.getElementById('tab-' + key);
    const btn = document.getElementById('tab-btn-' + key);
    if (panel) panel.classList.add('active');
    if (btn) btn.classList.add('active');
}
// Aktifkan tab pertama yang ada datanya
const counts = {
    pendidikan:  {{ $lecturer->educations->count() }},
    penelitian:  {{ $lecturer->researches->count() }},
    pengabdian:  {{ $lecturer->communityServices->count() }},
    publikasi:   {{ $lecturer->publications->count() }},
    buku:        {{ $lecturer->books->count() }},
    hki:         {{ $lecturer->hkis->count() }},
    penghargaan: {{ $lecturer->awards->count() }},
};
const tabs = ['pendidikan','penelitian','pengabdian','publikasi','buku','hki','penghargaan'];
switchTab(tabs.find(t => counts[t] > 0) || 'pendidikan');
</script>
