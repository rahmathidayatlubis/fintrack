<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="{{ asset('img/logo.png') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>Login — FinTrack</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        * {
            -webkit-tap-highlight-color: transparent;
        }
    </style>
</head>

<body class="min-h-screen bg-gradient-to-br from-slate-900 via-primary-950 to-slate-900"
    style="background: linear-gradient(135deg, #0F172A 0%, #1E3A8A 50%, #0F172A 100%);">

    <div class="max-w-md mx-auto min-h-screen flex flex-col">

        <!-- Top illustration area -->
        <div class="flex-1 flex flex-col items-center justify-center px-6 pt-16 pb-8">
            <!-- Logo -->
            <div
                class="w-20 h-20 rounded-3xl bg-blue-500/20 backdrop-blur border border-blue-400/30 flex items-center justify-center mb-6 shadow-2xl">
                <svg class="w-10 h-10 text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>

            <h1 class="text-3xl font-800 text-white mb-1 tracking-tight">FinTrack</h1>
            <p class="text-blue-300 text-sm font-500 mb-10">Kelola keuangan lebih cerdas</p>

            <!-- Decorative floating cards -->
            <div class="w-full relative mb-8">
                <div class="absolute inset-0 flex items-center justify-center opacity-20">
                    <div class="w-48 h-28 rounded-2xl bg-gradient-to-r from-blue-400 to-blue-600 rotate-12 transform">
                    </div>
                    <div
                        class="w-48 h-28 rounded-2xl bg-gradient-to-r from-indigo-400 to-purple-600 -rotate-6 transform ml-4 -mt-4">
                    </div>
                </div>
            </div>
        </div>

        <!-- Login card -->
        <div class="bg-white rounded-t-4xl px-6 pt-8 pb-10 shadow-2xl" x-data="{ showPass: false }">
            <h2 class="text-2xl font-800 text-gray-900 mb-1">Masuk</h2>
            <p class="text-gray-500 text-sm mb-6">Selamat datang kembali 👋</p>

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-2xl px-4 py-3 mb-4">
                    @foreach ($errors->all() as $err)
                        <p class="text-red-600 text-sm">{{ $err }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="text-xs font-600 text-gray-500 uppercase tracking-wide mb-1.5 block">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="email@kamu.com"
                        class="w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 text-gray-800 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all placeholder-gray-400"
                        required autocomplete="email">
                </div>

                <div>
                    <label class="text-xs font-600 text-gray-500 uppercase tracking-wide mb-1.5 block">Password</label>
                    <div class="relative">
                        <input :type="showPass ? 'text' : 'password'" name="password" placeholder="••••••••"
                            class="w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 text-gray-800 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all placeholder-gray-400 pr-12"
                            required autocomplete="current-password">
                        <button type="button" @click="showPass = !showPass"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg x-show="!showPass" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <svg x-show="showPass" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" style="display:none">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-blue-600 rounded">
                        Ingat saya
                    </label>
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-700 py-4 rounded-2xl transition-all duration-200 text-sm shadow-lg shadow-blue-200 mt-2">
                    Masuk ke Akun
                </button>
            </form>

            <p class="text-center text-sm text-gray-500 mt-6">
                Belum punya akun?
                <a href="{{ route('register') }}" class="text-blue-600 font-700">Daftar sekarang</a>
            </p>
        </div>
    </div>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif']
                    },
                    borderRadius: {
                        '4xl': '2rem'
                    }
                }
            }
        }
    </script>
</body>

</html>
