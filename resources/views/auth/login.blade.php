<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Sistem Profil Dosen Vokasi IT Del</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='6' fill='%23003087'/%3E%3Ctext x='50%25' y='55%25' dominant-baseline='middle' text-anchor='middle' font-family='Arial' font-weight='bold' font-size='13' fill='white'%3EDel%3C/text%3E%3C/svg%3E">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{colors:{del:{DEFAULT:'#003087',light:'#0051c3',dark:'#001f5c',50:'#eff6ff'}}}}}</script>
</head>
<body class="min-h-screen bg-gradient-to-br from-del to-del-light flex items-center justify-center p-4" style="font-family:'Plus Jakarta Sans',sans-serif;">
<div class="w-full max-w-md">
    <!-- Card -->
    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
        <!-- Header -->
        <div class="bg-del px-8 pt-8 pb-6 text-center">
            <img src="/images/logo-del.png" alt="IT Del" class="h-16 mx-auto mb-3" onerror="this.style.display='none'">
            <h1 class="text-white font-bold text-xl">Sistem Informasi Profil Dosen</h1>
            <p class="text-blue-200 text-sm mt-1">Fakultas Vokasi — Institut Teknologi Del</p>
        </div>
        <!-- Form -->
        <div class="px-8 py-6">
            <h2 class="text-gray-800 font-semibold text-lg mb-6">Masuk ke akun Anda</h2>

            @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm mb-5">
                @foreach($errors->all() as $error)<p>• {{ $error }}</p>@endforeach
            </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="text-sm font-medium text-gray-700 block mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-del/30 focus:border-del transition"
                           placeholder="nama@itdel.ac.id">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 block mb-1.5">Password</label>
                    <input type="password" name="password" required
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-del/30 focus:border-del transition"
                           placeholder="••••••••">
                </div>
                <button type="submit"
                        class="w-full bg-del text-white font-semibold py-2.5 rounded-xl hover:bg-del-light transition-colors shadow-sm mt-2">
                    Masuk
                </button>
            </form>
            <p class="text-center text-xs text-gray-400 mt-6">
                © {{ date('Y') }} Institut Teknologi Del · Fakultas Vokasi
            </p>
        </div>
    </div>
</div>
</body>
</html>
