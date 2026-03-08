<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>Daftar — FinTrack</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] }, borderRadius: { '4xl': '2rem', '3xl': '1.5rem' } } } }</script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; } * { -webkit-tap-highlight-color: transparent; }</style>
</head>
<body class="min-h-screen" style="background: linear-gradient(135deg, #0F172A 0%, #1E3A8A 50%, #0F172A 100%);">
<div class="max-w-md mx-auto min-h-screen flex flex-col">

    <div class="flex items-center px-6 pt-12 pb-6">
        <a href="{{ route('login') }}" class="w-10 h-10 rounded-2xl bg-white/10 flex items-center justify-center mr-4">
            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-800 text-white">Buat Akun</h1>
            <p class="text-blue-300 text-xs">Gratis, selamanya</p>
        </div>
    </div>

    <div class="bg-white rounded-t-4xl px-6 pt-8 pb-10 flex-1 shadow-2xl" x-data="{ showPass: false }">
        @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-2xl px-4 py-3 mb-4">
            @foreach($errors->all() as $err)
            <p class="text-red-600 text-sm">{{ $err }}</p>
            @endforeach
        </div>
        @endif

        <form action="{{ route('register') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="text-xs font-600 text-gray-500 uppercase tracking-wide mb-1.5 block">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama kamu"
                       class="w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all"
                       required>
            </div>

            <div>
                <label class="text-xs font-600 text-gray-500 uppercase tracking-wide mb-1.5 block">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="email@kamu.com"
                       class="w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all"
                       required>
            </div>

            <div>
                <label class="text-xs font-600 text-gray-500 uppercase tracking-wide mb-1.5 block">Password</label>
                <div class="relative">
                    <input :type="showPass ? 'text' : 'password'" name="password" placeholder="Min. 8 karakter"
                           class="w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all pr-12"
                           required>
                    <button type="button" @click="showPass = !showPass" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                </div>
            </div>

            <div>
                <label class="text-xs font-600 text-gray-500 uppercase tracking-wide mb-1.5 block">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" placeholder="Ulangi password"
                       class="w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all"
                       required>
            </div>

            <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-700 py-4 rounded-2xl transition-all text-sm shadow-lg shadow-blue-200 mt-2">
                Buat Akun Gratis
            </button>
        </form>

        <p class="text-center text-sm text-gray-500 mt-6">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-blue-600 font-700">Masuk</a>
        </p>
    </div>
</div>
</body>
</html>
