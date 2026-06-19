{{-- Partial menu aksi titik-tiga (Reset Password & Hapus Permanen).
     Dipakai bersama oleh versi tabel (suffix 'd') & versi kartu mobile (suffix 'm')
     supaya tidak ada duplikasi kode dan tidak ada bentrok ID di halaman yang sama. --}}
@php
    $d = $row['d'];
    $hapusLocked     = $row['hapusLocked'];
    $hapusLockReason = $row['hapusLockReason'];
@endphp
<div id="menu-{{ $d->id }}-{{ $suffix }}"
     class="hidden absolute right-0 top-full mt-1 w-56 bg-white border border-gray-100 rounded-xl shadow-lg z-20 py-1.5 text-left">

    <form action="{{ route('admin.dosen.reset-password', $d->id) }}" method="POST"
          onsubmit="return confirm('Reset password {{ $d->user->name }} ke default?')">
        @csrf @method('PATCH')
        <button type="submit"
                class="w-full text-left px-4 py-2 text-xs text-gray-700 hover:bg-gray-50 flex items-center gap-2">
            <svg class="w-3.5 h-3.5 text-orange-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            Reset Password
        </button>
    </form>

    <div class="my-1 border-t border-gray-50"></div>

    @if(!$hapusLocked)
        <form action="{{ route('admin.dosen.destroy', $d->id) }}" method="POST"
              onsubmit="return confirm('Hapus permanen akun {{ $d->user->name }}? Tindakan ini tidak bisa dibatalkan.')">
            @csrf @method('DELETE')
            <button type="submit"
                    class="w-full text-left px-4 py-2 text-xs text-red-500 hover:bg-red-50 flex items-center gap-2">
                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Hapus Permanen
            </button>
        </form>
    @else
        <div class="px-4 py-2 text-xs text-gray-300 flex items-center gap-2 cursor-not-allowed" title="{{ $hapusLockReason }}">
            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            Hapus Permanen
        </div>
        <p class="px-4 pb-1 text-[10px] text-gray-400 leading-snug">{{ $hapusLockReason }}</p>
    @endif
</div>
