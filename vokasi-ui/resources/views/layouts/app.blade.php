<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Profil Dosen') — Fakultas Vokasi IT Del</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 50:'#eff6ff', 100:'#dbeafe', 500:'#3b82f6', 600:'#2563eb', 700:'#1d4ed8', 800:'#1e3a8a', 900:'#1e3a8a' },
                        del: { DEFAULT:'#003087', light:'#0051c3', dark:'#001f5c' }
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        display: ['Playfair Display', 'serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .nav-link { @apply text-sm font-medium text-gray-600 hover:text-del transition-colors; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-800">

<!-- NAVBAR -->
<nav class="bg-white border-b border-gray-100 sticky top-0 z-50 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <div class="w-9 h-9 bg-del rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-sm">Del</span>
                </div>
                <div class="leading-tight">
                    <div class="text-xs text-gray-500 font-medium">Institut Teknologi Del</div>
                    <div class="text-sm font-bold text-del">Fakultas Vokasi</div>
                </div>
            </a>

            <!-- Nav Links -->
            <div class="hidden md:flex items-center gap-6">
                <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'text-del font-semibold' : '' }}">Beranda</a>
                <a href="{{ route('public.dosen') }}" class="nav-link {{ request()->routeIs('public.*') ? 'text-del font-semibold' : '' }}">Dosen</a>
            </div>

            <!-- Auth -->
            <div class="flex items-center gap-3">
                @auth
                    @if(in_array(auth()->user()->role, ['dekan','kaprodi']))
                        <a href="{{ route('admin.index') }}" class="text-sm font-medium text-del hover:underline">Dashboard Admin</a>
                    @else
                        <a href="{{ route('dosen.index') }}" class="text-sm font-medium text-del hover:underline">Dashboard</a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="text-sm text-gray-500 hover:text-red-500">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="bg-del text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-del-light transition-colors">
                        Masuk
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>

<!-- CONTENT -->
<main>
    @yield('content')
</main>

<!-- FOOTER -->
<footer class="bg-del text-white mt-16 py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-start gap-6">
            <div>
                <div class="font-bold text-lg mb-1">Fakultas Vokasi</div>
                <div class="text-blue-200 text-sm">Institut Teknologi Del</div>
                <div class="text-blue-300 text-xs mt-2">Jl. Sisingamangaraja, Laguboti, Toba, Sumatera Utara</div>
            </div>
            <div class="text-blue-300 text-sm">
                © {{ date('Y') }} Institut Teknologi Del. Sistem Informasi Profil Dosen Vokasi.
            </div>
        </div>
    </div>
</footer>

@stack('scripts')
</body>
</html>
