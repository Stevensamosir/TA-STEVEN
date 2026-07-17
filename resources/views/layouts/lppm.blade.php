<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') | SIPD - LPPM</title>
    <link rel="icon" type="image/png" href="/images/logo-del.png">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{colors:{del:{DEFAULT:'#003087',light:'#0051c3',dark:'#001f5c',50:'#eff6ff'}}}}}</script>
    @stack('styles')
</head>
<body class="min-h-screen bg-gray-50" style="font-family:'Plus Jakarta Sans',sans-serif;">

{{-- Navigasi utama role LPPM. LPPM memakai top-nav (bukan sidebar seperti
     dashboard dosen/admin), jadi menu "Daftar Dosen" ditambahkan di sini. --}}
<nav class="bg-del text-white px-6 py-3 flex items-center justify-between">
    <div class="flex items-center gap-6">
        <div class="flex items-center gap-3">
            <img src="/images/logo-del.png" alt="IT Del" class="h-9" onerror="this.style.display='none'">
            <span class="font-semibold">SIPD - Dashboard LPPM</span>
        </div>
        <div class="hidden sm:flex items-center gap-1">
            <a href="{{ route('lppm.index') }}"
               class="text-sm px-3 py-1.5 rounded-lg transition {{ request()->routeIs('lppm.index') ? 'bg-white/15 text-white font-semibold' : 'text-blue-100 hover:text-white hover:bg-white/10' }}">
                Input Tridharma
            </a>
            <a href="{{ route('lppm.daftar-dosen') }}"
               class="text-sm px-3 py-1.5 rounded-lg transition {{ request()->routeIs('lppm.daftar-dosen') ? 'bg-white/15 text-white font-semibold' : 'text-blue-100 hover:text-white hover:bg-white/10' }}">
                Daftar Dosen
            </a>
        </div>
    </div>
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button class="text-sm text-blue-100 hover:text-white">Keluar</button>
    </form>
</nav>

{{-- Menu versi mobile (di bawah navbar, karena menu utama disembunyikan di layar kecil) --}}
<div class="sm:hidden bg-del-dark text-white px-6 py-2 flex items-center gap-1">
    <a href="{{ route('lppm.index') }}"
       class="text-sm px-3 py-1.5 rounded-lg transition {{ request()->routeIs('lppm.index') ? 'bg-white/15 font-semibold' : 'text-blue-100 hover:bg-white/10' }}">
        Input Tridharma
    </a>
    <a href="{{ route('lppm.daftar-dosen') }}"
       class="text-sm px-3 py-1.5 rounded-lg transition {{ request()->routeIs('lppm.daftar-dosen') ? 'bg-white/15 font-semibold' : 'text-blue-100 hover:bg-white/10' }}">
        Daftar Dosen
    </a>
</div>

<main class="max-w-6xl mx-auto px-4 py-8 space-y-6">

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
            @foreach($errors->all() as $error)<p>• {{ $error }}</p>@endforeach
        </div>
    @endif

    @yield('content')

</main>

@stack('scripts')
</body>
</html>
