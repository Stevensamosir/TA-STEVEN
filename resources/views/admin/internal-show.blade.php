@extends('layouts.dashboard')
@section('title', $lecturer->user->name)
@section('page-title', 'Detail Dosen')
@section('content')

{{-- Detail Tridharma dipindah ke partial bersama supaya tidak duplikasi dengan
     halaman "Daftar Dosen" milik LPPM (lppm/dosen-show.blade.php). --}}
@include('partials._tridharma-detail', [
    'lecturer'  => $lecturer,
    'backUrl'   => route('admin.internal'),
    'backLabel' => 'Kembali ke Data Internal',
])

@endsection
