<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Sistem Profil Dosen Vokasi</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{colors:{del:{DEFAULT:'#003087',light:'#0051c3',dark:'#001f5c'}},fontFamily:{sans:['Plus Jakarta Sans','sans-serif'],display:['Playfair Display','serif']}}}}</script>
</head>
<body class="min-h-screen bg-gradient-to-br from-del-dark via-del to-del-light flex items-center justify-center p-4" style="font-family:'Plus Jakarta Sans',sans-serif;">

<div class="w-full max-w-md">
    <!-- Card -->
    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
        <!-- Header -->
        <div class="bg-del px-8 pt-8 pb-6 text-center">
            <div class="w-14 h-14 bg-white rounded-xl flex items-center justify-center mx-auto mb-4 shadow">
                <span class="text-del font-bold text-xl">Del</span>
            </div>
            <h1 class="text-white font-bold text-xl">Sistem Informasi</h1>
            <p class="text-blue-200 text-sm mt-1">Profil Dosen Fakultas Vokasi</p>
        </div>

        <!-- Form -->
        <div class="px-8 py-8">
            <h2 class="text-xl font-bold text-gray-800 mb-6">Masuk ke Akun</h2>

            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del focus:border-transparent transition-all @error('email') border-red-300 @enderror"
                        placeholder="nama@del.ac.id">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-del focus:border-transparent transition-all"
                        placeholder="••••••••">
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-del rounded border-gray-300">
                        <span class="text-sm text-gray-600">Ingat saya</span>
                    </label>
                </div>

                <button type="submit" class="w-full bg-del text-white font-semibold py-3 rounded-xl hover:bg-del-light transition-colors text-sm shadow-lg shadow-blue-900/20">
                    Masuk
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-gray-100">
                <a href="{{ route('home') }}" class="flex items-center justify-center gap-2 text-sm text-gray-500 hover:text-del transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali ke halaman publik
                </a>
            </div>
        </div>
    </div>

    <p class="text-center text-blue-200 text-xs mt-6">© {{ date('Y') }} Institut Teknologi Del</p>
</div>

</body>
</html>
