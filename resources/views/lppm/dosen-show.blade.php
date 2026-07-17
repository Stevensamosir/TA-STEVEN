@extends('layouts.lppm')
@section('title', $lecturer->user->name)
@section('content')

{{-- Reuse partial detail Tridharma yang sama dengan Data Internal (Kaprodi/Dekan),
     hanya tombol "kembali" yang diarahkan ke daftar dosen LPPM. --}}
@include('partials._tridharma-detail', [
    'lecturer'        => $lecturer,
    'backUrl'         => route('lppm.daftar-dosen'),
    'backLabel'       => 'Kembali ke Daftar Dosen',
    // Aktifkan tombol Edit hanya untuk tab Penelitian & Pengabdian (wewenang LPPM).
    'editableTypes'   => ['penelitian', 'pengabdian'],
    'editRoutePrefix' => 'lppm',
])

@endsection
