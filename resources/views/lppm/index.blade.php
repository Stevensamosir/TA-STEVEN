@extends('layouts.lppm')
@section('title', 'Input Tridharma')
@section('content')

    <!-- Pencarian dosen -->
    <section class="bg-white rounded-2xl shadow p-6">
        <h2 class="font-semibold text-gray-800 mb-1">Cari Dosen</h2>
        <p class="text-sm text-gray-500 mb-4">Ketik nama atau NIDN dosen Fakultas Vokasi, lalu pilih dari hasil pencarian.</p>

        <input type="text" id="dosenSearchInput" autocomplete="off" placeholder="Contoh: Ardiles atau 0420098501"
               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-del/30 focus:border-del">

        <div id="dosenSearchResults" class="mt-2 space-y-1"></div>

        <!-- Kartu konfirmasi dosen terpilih -->
        <div id="selectedDosenCard" class="hidden mt-4 flex items-center gap-3 bg-del-50 border border-del/20 rounded-xl p-4">
            <img id="selectedDosenPhoto" src="" alt="" class="w-12 h-12 rounded-full object-cover bg-gray-200"
                 onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2248%22 height=%2248%22><rect width=%2248%22 height=%2248%22 fill=%22%23cbd5e1%22/></svg>'">
            <div>
                <p class="font-semibold text-gray-800" id="selectedDosenName"></p>
                <p class="text-xs text-gray-500"><span id="selectedDosenProdi"></span> · NIDN <span id="selectedDosenNidn"></span></p>
            </div>
            <button type="button" onclick="clearSelectedDosen()" class="ml-auto text-xs text-gray-400 hover:text-red-500">Ganti</button>
        </div>
    </section>

    {{-- 3 kolom sejajar di layar besar: Penelitian | PKM | Riwayat (lebih sempit).
         Di layar kecil tetap menumpuk vertikal (grid-cols-1). --}}
    <div class="grid grid-cols-1 lg:grid-cols-[2fr_2fr_1.2fr] gap-4 items-start">

    <!-- Form Penelitian (nonaktif sampai dosen dipilih) -->
    <section class="bg-white rounded-2xl shadow p-6" id="formPenelitianSection">
        <h2 class="font-semibold text-gray-800 mb-4">Input Data Penelitian Dosen</h2>
        <form action="{{ route('lppm.penelitian.store') }}" method="POST" class="space-y-4" id="formPenelitian">
            @csrf
            <input type="hidden" name="lecturer_id" id="penelitianLecturerId">
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1.5">Judul Penelitian</label>
                <input type="text" name="title" required
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-del/30 focus:border-del">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-700 block mb-1.5">Tahun</label>
                    <input type="number" name="year" value="{{ old('year', now()->year) }}" min="1970" max="{{ date('Y') }}" required
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-del/30 focus:border-del">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 block mb-1.5">Bulan</label>
                    <select name="month" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-del/30 focus:border-del">
                        <option value=""> - Pilih - </option>
                        @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $bulan)
                            <option value="{{ $i+1 }}" {{ old('month') == $i+1 ? 'selected' : '' }}>{{ $bulan }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 block mb-1.5">Sumber Dana</label>
                    <input type="text" name="funding_source"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-del/30 focus:border-del">
                </div>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1.5">Visibilitas</label>
                <select name="visibility" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-del/30 focus:border-del">
                    <option value="public">Publik</option>
                    <option value="private">Privat</option>
                </select>
            </div>
            <button type="submit" disabled id="btnSimpanPenelitian"
                    class="bg-del text-white font-semibold px-5 py-2.5 rounded-xl hover:bg-del-light transition disabled:opacity-40 disabled:cursor-not-allowed">
                Simpan Penelitian
            </button>
            <p class="text-xs text-gray-400" id="hintPenelitian">Pilih dosen dulu di atas sebelum mengisi form ini.</p>
        </form>
    </section>

    <!-- Form PKM (nonaktif sampai dosen dipilih) -->
    <section class="bg-white rounded-2xl shadow p-6" id="formPkmSection">
        <h2 class="font-semibold text-gray-800 mb-4">Input Data PKM / Pengabdian Dosen</h2>
        <form action="{{ route('lppm.pkm.store') }}" method="POST" class="space-y-4" id="formPkm">
            @csrf
            <input type="hidden" name="lecturer_id" id="pkmLecturerId">
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1.5">Judul Kegiatan PKM</label>
                <input type="text" name="title" required
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-del/30 focus:border-del">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-700 block mb-1.5">Tahun</label>
                    <input type="number" name="year" value="{{ old('year', now()->year) }}" min="1970" max="{{ date('Y') }}" required
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-del/30 focus:border-del">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 block mb-1.5">Bulan</label>
                    <select name="month" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-del/30 focus:border-del">
                        <option value=""> - Pilih - </option>
                        @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $bulan)
                            <option value="{{ $i+1 }}" {{ old('month') == $i+1 ? 'selected' : '' }}>{{ $bulan }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 block mb-1.5">Lokasi</label>
                    <input type="text" name="location"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-del/30 focus:border-del">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-700 block mb-1.5">Jenis PKM</label>
                    <select name="pkm_type"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-del/30 focus:border-del">
                        <option value=""> - Pilih - </option>
                        <option value="Internal">Internal</option>
                        <option value="Nasional">Nasional</option>
                        <option value="Internasional">Internasional</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 block mb-1.5">Skema PKM (opsional)</label>
                    <select name="pkm_scheme"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-del/30 focus:border-del">
                        <option value=""> - Tidak masuk skema kompetisi - </option>
                        <optgroup label="Skema Pendanaan">
                            <option value="PKM-RE">PKM-RE (Riset Eksakta)</option>
                            <option value="PKM-RSH">PKM-RSH (Riset Sosial Humaniora)</option>
                            <option value="PKM-K">PKM-K (Kewirausahaan)</option>
                            <option value="PKM-PM">PKM-PM (Pengabdian kepada Masyarakat)</option>
                            <option value="PKM-PI">PKM-PI (Penerapan IPTEK)</option>
                            <option value="PKM-KC">PKM-KC (Karsa Cipta)</option>
                            <option value="PKM-KI">PKM-KI (Karya Inovatif)</option>
                            <option value="PKM-VGK">PKM-VGK (Video Gagasan Konstruktif)</option>
                        </optgroup>
                        <optgroup label="Skema Insentif">
                            <option value="PKM-AI">PKM-AI (Artikel Ilmiah)</option>
                            <option value="PKM-GFT">PKM-GFT (Gagasan Futuristik Tertulis)</option>
                        </optgroup>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 block mb-1.5">Peran Dosen Ini</label>
                    <select name="role" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-del/30 focus:border-del">
                        <option value="Ketua">Ketua</option>
                        <option value="Anggota">Anggota</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1.5">Anggota Mahasiswa</label>
                <input type="text" name="student_members" placeholder="Nama, dipisah koma"
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-del/30 focus:border-del">
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700 block mb-1.5">Visibilitas</label>
                <select name="visibility" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-del/30 focus:border-del">
                    <option value="public">Publik</option>
                    <option value="private">Privat</option>
                </select>
            </div>
            <button type="submit" disabled id="btnSimpanPkm"
                    class="bg-del text-white font-semibold px-5 py-2.5 rounded-xl hover:bg-del-light transition disabled:opacity-40 disabled:cursor-not-allowed">
                Simpan PKM
            </button>
            <p class="text-xs text-gray-400" id="hintPkm">Pilih dosen dulu di atas sebelum mengisi form ini.</p>
        </form>
    </section>

    <!-- Riwayat Pengisian Terakhir (kolom ke-3, lebih sempit) -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-del px-5 py-4">
            <h2 class="text-white font-semibold">Riwayat Pengisian Terakhir</h2>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($riwayat as $r)
            <div class="px-5 py-3 flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-800 truncate">{{ $r->judul }}</p>
                    <p class="text-xs text-gray-500">{{ $r->nama_dosen }} &middot; {{ $r->jenis }}</p>
                </div>
                <span class="text-xs text-gray-400 whitespace-nowrap">{{ $r->tanggal->diffForHumans() }}</span>
            </div>
            @empty
            <div class="px-5 py-10 text-center text-sm text-gray-400">
                Belum ada data yang Anda input.
            </div>
            @endforelse
        </div>
    </div>

    </div>

@endsection

@push('scripts')
<script>
const searchInput   = document.getElementById('dosenSearchInput');
const resultsBox    = document.getElementById('dosenSearchResults');
const selectedCard  = document.getElementById('selectedDosenCard');
let debounceTimer;

searchInput.addEventListener('input', () => {
    clearTimeout(debounceTimer);
    const q = searchInput.value.trim();
    if (q.length < 2) { resultsBox.innerHTML = ''; return; }

    debounceTimer = setTimeout(async () => {
        try {
            const res = await fetch(`{{ route('lppm.search-dosen') }}?q=${encodeURIComponent(q)}`);
            const data = await res.json();
            renderResults(data);
        } catch (e) {
            resultsBox.innerHTML = '<p class="text-xs text-red-500 px-2">Gagal mencari dosen.</p>';
        }
    }, 300);
});

function renderResults(list) {
    if (!list.length) {
        resultsBox.innerHTML = '<p class="text-xs text-gray-400 px-2 py-2">Tidak ditemukan.</p>';
        return;
    }
    resultsBox.innerHTML = list.map(d => `
        <button type="button" onclick='selectDosen(${JSON.stringify(d)})'
                class="w-full text-left flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 transition">
            <img src="${d.photo ?? ''}" onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2232%22 height=%2232%22><rect width=%2232%22 height=%2232%22 fill=%22%23cbd5e1%22/></svg>'"
                 class="w-8 h-8 rounded-full object-cover bg-gray-200">
            <span>
                <span class="block text-sm font-medium text-gray-800">${d.name}</span>
                <span class="block text-xs text-gray-500">${d.prodi} · NIDN ${d.nidn}</span>
            </span>
        </button>
    `).join('');
}

function selectDosen(d) {
    document.getElementById('selectedDosenPhoto').src = d.photo ?? '';
    document.getElementById('selectedDosenName').textContent = d.name;
    document.getElementById('selectedDosenProdi').textContent = d.prodi;
    document.getElementById('selectedDosenNidn').textContent = d.nidn;
    selectedCard.classList.remove('hidden');

    document.getElementById('penelitianLecturerId').value = d.id;
    document.getElementById('pkmLecturerId').value = d.id;
    document.getElementById('btnSimpanPenelitian').disabled = false;
    document.getElementById('btnSimpanPkm').disabled = false;
    document.getElementById('hintPenelitian').textContent = `Mengisi data untuk ${d.name}.`;
    document.getElementById('hintPkm').textContent = `Mengisi data untuk ${d.name}.`;

    searchInput.value = '';
    resultsBox.innerHTML = '';
}

function clearSelectedDosen() {
    selectedCard.classList.add('hidden');
    document.getElementById('penelitianLecturerId').value = '';
    document.getElementById('pkmLecturerId').value = '';
    document.getElementById('btnSimpanPenelitian').disabled = true;
    document.getElementById('btnSimpanPkm').disabled = true;
    document.getElementById('hintPenelitian').textContent = 'Pilih dosen dulu di atas sebelum mengisi form ini.';
    document.getElementById('hintPkm').textContent = 'Pilih dosen dulu di atas sebelum mengisi form ini.';
}
</script>
@endpush
