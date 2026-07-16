<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk ke Sistem Profil Dosen Vokasi IT Del</title>
    <link rel="icon" type="image/png" href="/images/logo-del.png">
    <link rel="shortcut icon" href="/images/logo-del.png">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{colors:{del:{DEFAULT:'#003087',light:'#0051c3',dark:'#001f5c',50:'#eff6ff'}}}}}</script>
    <style>
        /* Cegah kursor "ngetik" (I-beam) muncul di teks biasa - hanya kotak input asli yang boleh */
        body, p, h1, h2, h3, h4, h5, h6, span, div, li, label {
            cursor: default;
        }
        input, textarea, select, [contenteditable="true"] {
            cursor: text;
        }
        button, a, [role="button"] {
            cursor: pointer;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-del to-del-light flex items-center justify-center p-4" style="font-family:'Plus Jakarta Sans',sans-serif;">
<div class="w-full max-w-md">
    <!-- Card -->
    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
        <!-- Header -->
        <div class="bg-del px-8 pt-8 pb-6 text-center">
            <img src="/images/logo-del.png" alt="IT Del" class="h-16 mx-auto mb-3" onerror="this.style.display='none'">
            <h1 class="text-white font-bold text-xl">Sistem Informasi Profil Dosen</h1>
            <p class="text-blue-200 text-sm mt-1">Fakultas Vokasi - Institut Teknologi Del</p>
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
                    <label class="text-sm font-medium text-gray-700 block mb-1.5">Username CIS</label>
                    <input type="text" name="username" value="{{ old('username') }}" required
                    autocomplete="off"
                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-del/30 focus:border-del transition"
                    placeholder="nama.belakang">
                </div>
                <p class="text-xs text-gray-400 -mt-2">Gunakan username & password akun CIS Institut Teknologi Del Anda.</p>
                <div>
                    <label class="text-sm font-medium text-gray-700 block mb-1.5">Password</label>
                    <div class="relative">
                        <input type="password" id="pwd_login" name="password" required
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 pr-11 text-sm focus:outline-none focus:ring-2 focus:ring-del/30 focus:border-del transition"
                               placeholder="••••••••">
                        <button type="button" onclick="togglePasswordVisibility('pwd_login', this)" tabindex="-1" aria-label="Tampilkan/sembunyikan password" class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600">
                            <svg class="eye-icon-open w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M10 3.5c-4.5 0-8.05 3-9.5 6.5 1.45 3.5 5 6.5 9.5 6.5s8.05-3 9.5-6.5C18.05 6.5 14.5 3.5 10 3.5zM10 14a4 4 0 110-8 4 4 0 010 8z"/></svg>
                            <svg class="eye-icon-closed w-5 h-5 hidden" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3.28 2.22a.75.75 0 00-1.06 1.06l14.5 14.5a.75.75 0 101.06-1.06l-1.745-1.745a10.029 10.029 0 003.3-4.38 1.651 1.651 0 000-1.185A10.004 10.004 0 009.999 3a9.956 9.956 0 00-4.744 1.194L3.28 2.22zM7.752 6.69l1.092 1.092a2.5 2.5 0 013.374 3.373l1.091 1.092a4 4 0 00-5.557-5.557z" clip-rule="evenodd"/><path d="M10.748 13.93l2.523 2.523a9.987 9.987 0 01-3.27.547c-4.258 0-7.894-2.66-9.337-6.41a1.651 1.651 0 010-1.186A10.007 10.007 0 012.839 6.02L6.07 9.252a4 4 0 004.678 4.678z"/></svg>
                        </button>
                    </div>
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
<script>
    function togglePasswordVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        if (!input) return;
        const willShow = input.type === 'password';
        input.type = willShow ? 'text' : 'password';
        btn.querySelector('.eye-icon-open').classList.toggle('hidden', willShow);
        btn.querySelector('.eye-icon-closed').classList.toggle('hidden', !willShow);
    }
</script>
</body>
</html>

