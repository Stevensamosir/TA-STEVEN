@extends('layouts.dashboard')
@section('title', 'Publikasi')
@section('page-title', 'Data Publikasi')

@push('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="pt-4 space-y-6">

    <!-- Form Tambah dengan DOI Auto-fill -->
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-1">Tambah Publikasi</h3>
        <p class="text-xs text-gray-400 mb-5">Masukkan DOI untuk otomatis mengisi data dari Crossref, atau isi manual.</p>

        <!-- DOI Lookup Box -->
        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-5">
            <label class="block text-sm font-semibold text-del mb-2">
                🔍 Auto-fill via DOI (Crossref)
            </label>
            <div class="flex gap-2">
                <input type="text" id="doi-input"
                    class="flex-1 px-3 py-2.5 border border-blue-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30 bg-white"
                    placeholder="Contoh: 10.1145/3644815.3644951 atau https://doi.org/10.xxxx/...">
                <button type="button" onclick="fetchDoi()"
                    id="doi-btn"
                    class="bg-del text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-del-light transition-colors whitespace-nowrap flex items-center gap-2">
                    <svg id="doi-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                    <svg id="doi-spinner" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                    Cari
                </button>
            </div>
            <!-- Status pesan -->
            <div id="doi-status" class="mt-2 text-xs hidden"></div>
            <!-- Preview hasil DOI -->
            <div id="doi-preview" class="mt-3 hidden">
                <div class="bg-white border border-blue-200 rounded-xl p-3">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span class="text-xs font-semibold text-green-700">Data ditemukan dari Crossref</span>
                    </div>
                    <p class="text-sm font-medium text-gray-800" id="preview-title"></p>
                    <div class="flex flex-wrap gap-3 mt-1">
                        <span class="text-xs text-gray-500" id="preview-year"></span>
                        <span class="text-xs text-gray-500" id="preview-publisher"></span>
                    </div>
                    <button type="button" onclick="applyDoi()"
                        class="mt-3 bg-green-50 text-green-700 border border-green-200 px-4 py-2 rounded-lg text-xs font-semibold hover:bg-green-100 transition-colors">
                        ✓ Terapkan ke Form
                    </button>
                </div>
            </div>
        </div>

        <!-- Form Manual -->
        <form action="{{ route('dosen.publikasi.store') }}" method="POST" id="pub-form">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Judul Publikasi *</label>
                    <input type="text" name="title" id="field-title" required
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30"
                        placeholder="Judul publikasi">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tahun *</label>
                    <input type="number" name="year" id="field-year" required
                        min="1990" max="{{ date('Y')+1 }}" placeholder="{{ date('Y') }}"
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Penerbit / Jurnal</label>
                    <input type="text" name="publisher" id="field-publisher"
                        placeholder="Nama jurnal atau prosiding"
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Link Sumber (URL / DOI)
                        <span class="text-gray-400 font-normal">— otomatis dari DOI</span>
                    </label>
                    <input type="url" name="publisher_url" id="field-url"
                        placeholder="https://doi.org/..."
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del/30">
                </div>
            </div>
            <div class="flex gap-3 items-center">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Visibilitas</label>
                    <select name="visibility" class="px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none">
                        <option value="public">Publik</option>
                        <option value="private">Internal</option>
                    </select>
                </div>
                <button type="submit" class="mt-5 bg-del text-white px-6 py-2.5 rounded-xl text-sm font-semibold hover:bg-del-light transition-colors">
                    + Simpan Publikasi
                </button>
            </div>
        </form>
    </div>

    <!-- Tabel Publikasi -->
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">Daftar Publikasi ({{ $publikasis->count() }})</h3>
        </div>
        @if($publikasis->isEmpty())
            <div class="text-center py-12 text-gray-400 text-sm">Belum ada data publikasi. Tambahkan via DOI atau manual.</div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3 text-left">Judul</th>
                        <th class="px-5 py-3 text-left">Tahun</th>
                        <th class="px-5 py-3 text-left">Penerbit</th>
                        <th class="px-5 py-3 text-left">Link</th>
                        <th class="px-5 py-3 text-left">Visibilitas</th>
                        <th class="px-5 py-3 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($publikasis as $item)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-3 font-medium text-gray-800 max-w-xs">
                            <p class="line-clamp-2">{{ $item->title }}</p>
                        </td>
                        <td class="px-5 py-3 text-gray-600">{{ $item->year }}</td>
                        <td class="px-5 py-3 text-gray-500 text-xs max-w-[140px]">
                            <p class="line-clamp-2">{{ $item->publisher ?? '-' }}</p>
                        </td>
                        <td class="px-5 py-3">
                            @if($item->publisher_url)
                                <a href="{{ $item->publisher_url }}" target="_blank"
                                   class="text-xs text-del hover:underline flex items-center gap-1 whitespace-nowrap">
                                    <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    Lihat
                                </a>
                            @else
                                <span class="text-xs text-gray-300">-</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <form action="{{ route('dosen.publikasi.visibility', $item->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-full transition-colors
                                           {{ $item->visibility === 'public' ? 'bg-green-50 text-green-700 hover:bg-green-100' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $item->visibility === 'public' ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                    {{ $item->visibility === 'public' ? 'Publik' : 'Internal' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <button onclick="this.closest('tr').nextElementSibling.classList.toggle('hidden')"
                                    class="text-xs text-blue-600 hover:underline">Edit</button>
                                <form action="{{ route('dosen.publikasi.destroy', $item->id) }}" method="POST"
                                    onsubmit="return confirm('Hapus publikasi ini?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-red-500 hover:underline">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <!-- Edit Inline Row -->
                    <tr class="hidden bg-blue-50/20">
                        <td colspan="6" class="px-5 py-4">
                            <form action="{{ route('dosen.publikasi.update', $item->id) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-3">
                                    <div class="md:col-span-2">
                                        <input type="text" name="title" value="{{ $item->title }}"
                                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    </div>
                                    <input type="number" name="year" value="{{ $item->year }}"
                                        class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                                    <input type="text" name="publisher" value="{{ $item->publisher }}"
                                        placeholder="Penerbit/Jurnal"
                                        class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    <input type="url" name="publisher_url" value="{{ $item->publisher_url }}"
                                        placeholder="https://doi.org/..."
                                        class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                </div>
                                <div class="flex gap-3">
                                    <select name="visibility" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                        <option value="public" {{ $item->visibility == 'public' ? 'selected' : '' }}>Publik</option>
                                        <option value="private" {{ $item->visibility == 'private' ? 'selected' : '' }}>Internal</option>
                                    </select>
                                    <button type="submit" class="bg-del text-white px-4 py-2 rounded-lg text-sm font-medium">Simpan</button>
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
// Simpan data DOI hasil fetch
let doiData = null;

// Bersihkan input DOI: terima URL (https://doi.org/10.xxxx/xxx) atau DOI langsung
function cleanDoi(raw) {
    return raw.replace(/^https?:\/\/(dx\.)?doi\.org\//i, '').replace(/^doi:/i, '').trim();
}

// Ubah struktur JSON Crossref (message.*) jadi bentuk data yang dipakai form ini
function parseCrossrefMessage(msg, doi) {
    const title = msg.title && msg.title[0] ? msg.title[0] : null;
    if (!title) return null;

    let year = null;
    if (msg.published && msg.published['date-parts'] && msg.published['date-parts'][0]) {
        year = msg.published['date-parts'][0][0];
    } else if (msg.created && msg.created['date-parts'] && msg.created['date-parts'][0]) {
        year = msg.created['date-parts'][0][0];
    }

    let publisher = null;
    if (msg['container-title'] && msg['container-title'][0]) {
        publisher = msg['container-title'][0];
    } else if (msg.publisher) {
        publisher = msg.publisher;
    } else if (msg.institution && msg.institution[0] && msg.institution[0].name) {
        publisher = msg.institution[0].name;
    }

    const authors = (msg.author || [])
        .map(a => `${a.given || ''} ${a.family || ''}`.trim())
        .filter(Boolean);

    return { title, year, publisher, publisher_url: `https://doi.org/${doi}`, type: msg.type || 'journal-article', authors };
}

// Percobaan 1: langsung dari browser ke Crossref.
// Ini menghindari masalah koneksi keluar dari server lokal (firewall/SSL/cURL)
// yang sering jadi penyebab "Gagal menghubungi Crossref API" padahal internet di
// komputer sendiri baik-baik saja.
async function fetchFromCrossrefDirect(doi) {
    try {
        const res = await fetch(`https://api.crossref.org/works/${encodeURIComponent(doi)}`, {
            headers: { 'Accept': 'application/json' }
        });
        if (!res.ok) return null;
        const json = await res.json();
        return parseCrossrefMessage(json.message || {}, doi);
    } catch (e) {
        return null; // kemungkinan diblokir CORS/jaringan browser — lanjut ke fallback
    }
}

// Percobaan 2 (fallback): lewat backend Laravel /api/doi, kalau percobaan 1 gagal.
async function fetchFromBackend(doi) {
    try {
        const res = await fetch(`/api/doi?doi=${encodeURIComponent(doi)}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        });
        const result = await res.json();
        if (!res.ok || result.error) return null;
        return result.data;
    } catch (e) {
        return null;
    }
}

async function fetchDoi() {
    const doiRaw = document.getElementById('doi-input').value.trim();
    if (!doiRaw) {
        showStatus('Masukkan DOI terlebih dahulu.', 'error');
        return;
    }
    const doi = cleanDoi(doiRaw);

    // Loading state
    document.getElementById('doi-icon').classList.add('hidden');
    document.getElementById('doi-spinner').classList.remove('hidden');
    document.getElementById('doi-btn').disabled = true;
    document.getElementById('doi-preview').classList.add('hidden');
    showStatus('Mencari di Crossref...', 'info');

    try {
        let data = await fetchFromCrossrefDirect(doi);
        if (!data) {
            data = await fetchFromBackend(doi);
        }

        if (!data) {
            showStatus('DOI tidak ditemukan / tidak bisa diakses. Isi manual saja di bawah, tidak masalah.', 'error');
            return;
        }

        doiData = data;

        // Tampilkan preview
        document.getElementById('preview-title').textContent = doiData.title;
        document.getElementById('preview-year').textContent = doiData.year ? `📅 ${doiData.year}` : '';
        document.getElementById('preview-publisher').textContent = doiData.publisher ? `📖 ${doiData.publisher}` : '';
        document.getElementById('doi-preview').classList.remove('hidden');
        showStatus('', 'clear');

    } catch (e) {
        showStatus('Gagal mengambil data DOI. Isi manual saja di bawah, tidak masalah.', 'error');
    } finally {
        document.getElementById('doi-icon').classList.remove('hidden');
        document.getElementById('doi-spinner').classList.add('hidden');
        document.getElementById('doi-btn').disabled = false;
    }
}

function applyDoi() {
    if (!doiData) return;
    document.getElementById('field-title').value     = doiData.title || '';
    document.getElementById('field-year').value      = doiData.year || '';
    document.getElementById('field-publisher').value = doiData.publisher || '';
    document.getElementById('field-url').value       = doiData.publisher_url || '';

    // Highlight fields yang diisi
    ['field-title', 'field-year', 'field-publisher', 'field-url'].forEach(id => {
        const el = document.getElementById(id);
        el.classList.add('border-green-400', 'bg-green-50');
        setTimeout(() => el.classList.remove('border-green-400', 'bg-green-50'), 2000);
    });

    document.getElementById('doi-preview').classList.add('hidden');
    showStatus('✓ Data berhasil diterapkan ke form!', 'success');
}

function showStatus(msg, type) {
    const el = document.getElementById('doi-status');
    if (!msg) { el.classList.add('hidden'); return; }
    el.classList.remove('hidden', 'text-red-600', 'text-blue-600', 'text-green-600');
    if (type === 'error') el.classList.add('text-red-600');
    else if (type === 'success') el.classList.add('text-green-600');
    else el.classList.add('text-blue-600');
    el.textContent = msg;
}

// Enter key pada input DOI
document.getElementById('doi-input').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') { e.preventDefault(); fetchDoi(); }
});
</script>
@endpush
