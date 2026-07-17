<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Profil Dosen') Fakultas Vokasi IT Del</title>

    {{-- ✅ FAVICON: Pakai logo asli IT Del --}}
    <link rel="icon" type="image/png" href="/images/logo-del.png">
    <link rel="shortcut icon" href="/images/logo-del.png">
    <meta name="theme-color" content="#003087">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        del: { DEFAULT:'#003087', light:'#0051c3', dark:'#001f5c', 50:'#eff6ff' }
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        display: ['Playfair Display', 'serif'],
                    }
                }
            }
        }
    </script>
    @stack('styles')
    <style>
        /* ── Cegah kursor "ngetik" (I-beam) muncul di teks biasa ──
           Default browser akan menampilkan kursor I-beam di semua teks yang
           bisa di-select (h1, p, span, dll), padahal cuma kotak input yang
           seharusnya bisa diketik. Aturan ini membatasi kursor I-beam HANYA
           untuk elemen form sungguhan. */
        body, p, h1, h2, h3, h4, h5, h6, span, div, li, label {
            cursor: default;
        }
        input, textarea, select, [contenteditable="true"] {
            cursor: text;
        }
        button, a, [role="button"], summary {
            cursor: pointer;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800" style="font-family:'Plus Jakarta Sans',sans-serif;">

<!-- NAVBAR -->
<nav class="bg-white border-b border-gray-100 sticky top-0 z-50 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <img src="/images/logo-del.png" alt="IT Del" class="h-10 w-auto"
                     onerror="this.style.display='none'; document.getElementById('logo-svg').style.display='flex'">
                {{-- ✅ SVG Fallback otomatis jika file logo belum ada --}}
                <div id="logo-svg" style="display:none"
                     class="w-10 h-10 bg-del rounded-lg items-center justify-center">
                    <span class="text-white font-bold text-sm">Del</span>
                </div>
                <div class="leading-tight">
                    <div class="text-xs text-gray-400 font-medium">Institut Teknologi Del</div>
                    <div class="text-sm font-bold text-del">Fakultas Vokasi</div>
                </div>
            </a>

            <!-- Auth -->
            <div class="flex items-center gap-3">
                @auth
                    @if(auth()->user()->isDekan())
                        {{-- Dekan → dashboard dekan utama --}}
                        <a href="{{ route('admin.index') }}" class="text-sm font-medium text-del hover:underline hidden sm:block">
                            Dashboard Dekan
                        </a>
                    @elseif(auth()->user()->isKaprodi())
                        {{-- Kaprodi → halaman Data Internal (route yg memang boleh diakses Kaprodi) --}}
                        <a href="{{ route('admin.internal') }}" class="text-sm font-medium text-del hover:underline hidden sm:block">
                            Data Internal
                        </a>
                    @else
                        {{-- Dosen biasa → dashboard dosen --}}
                        <a href="{{ route('dosen.index') }}" class="text-sm font-medium text-del hover:underline hidden sm:block">
                            Dashboard
                        </a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="text-sm text-gray-500 hover:text-red-500 transition-colors">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                       class="bg-del text-white text-sm font-semibold px-5 py-2 rounded-lg hover:bg-del-light transition-colors shadow-sm">
                        Masuk
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>

@if(session('success'))
<div class="bg-green-50 border-b border-green-200 text-green-800 px-4 py-3 text-sm text-center">
    {{ session('success') }}
</div>
@endif

<main>
    @yield('content')
</main>

<footer class="bg-del text-white mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex flex-col md:flex-row justify-between items-start gap-8">
            <div class="flex items-center gap-4">
                <img src="/images/logo-del.png" alt="IT Del"
                     class="h-14 w-auto opacity-90" style="mix-blend-mode: screen;"
                     onerror="this.style.display='none'">
                <div>
                    <div class="font-bold text-lg">Fakultas Vokasi</div>
                    <div class="text-blue-200 text-sm">Institut Teknologi Del</div>
                    <div class="text-blue-300 text-xs mt-2 leading-relaxed">
                        Jl. Sisingamangaraja, Sitoluama<br>
                        Laguboti, Toba, Sumatera Utara 22381
                    </div>
                </div>
            </div>
            <div class="flex flex-col gap-2">
                <p class="text-xs font-semibold text-blue-200 uppercase tracking-wider mb-1">Tautan</p>
                <a href="https://del.ac.id" target="_blank" class="text-sm text-blue-200 hover:text-white transition-colors">del.ac.id</a>
                <a href="{{ route('home') }}" class="text-sm text-blue-200 hover:text-white transition-colors">Daftar Dosen</a>
                <a href="{{ route('login') }}" class="text-sm text-blue-200 hover:text-white transition-colors">Masuk</a>
            </div>
            <div class="text-blue-300 text-xs text-right">
                <p>© {{ date('Y') }} Institut Teknologi Del</p>
                <p class="mt-1">Sistem Informasi Profil Dosen Vokasi</p>
            </div>
        </div>
    </div>
</footer>

@stack('scripts')
</body>
</html>