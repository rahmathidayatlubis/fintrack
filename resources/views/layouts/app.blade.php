<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'FinTrack') — Kelola Keuanganmu</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap"
        rel="stylesheet">

    <style>
        /* Chrome, Edge, Safari */
        ::-webkit-scrollbar {
            display: none;
        }

        /* Firefox */
        html {
            scrollbar-width: none;
        }

        /* IE/Edge lama */
        body {
            -ms-overflow-style: none;
        }
    </style>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        primary: {
                            50: '#EFF6FF',
                            100: '#DBEAFE',
                            200: '#BFDBFE',
                            300: '#93C5FD',
                            400: '#60A5FA',
                            500: '#3B82F6',
                            600: '#2563EB',
                            700: '#1D4ED8',
                            800: '#1E40AF',
                            900: '#1E3A8A',
                        },
                        surface: '#F8FAFC',
                        card: '#FFFFFF',
                    },
                    borderRadius: {
                        '2xl': '1rem',
                        '3xl': '1.5rem',
                        '4xl': '2rem'
                    },
                    boxShadow: {
                        'card': '0 2px 16px rgba(0,0,0,0.06)',
                        'card-hover': '0 8px 32px rgba(0,0,0,0.12)',
                        'float': '0 -4px 20px rgba(0,0,0,0.08)',
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Heroicons (inline via CDN) -->
    <style>
        * {
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #F0F4F8;
        }

        .safe-bottom {
            padding-bottom: env(safe-area-inset-bottom, 16px);
        }

        .hide-scroll::-webkit-scrollbar {
            display: none;
        }

        .hide-scroll {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Bottom nav active state */
        .nav-item.active svg {
            stroke: #2563EB;
        }

        .nav-item.active span {
            color: #2563EB;
        }

        .nav-item svg {
            stroke: #94A3B8;
        }

        .nav-item span {
            color: #94A3B8;
        }

        /* Gradient text */
        .gradient-text {
            background: linear-gradient(135deg, #1D4ED8, #3B82F6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Card gradient */
        .card-gradient {
            background: linear-gradient(135deg, #1E40AF 0%, #3B82F6 60%, #60A5FA 100%);
        }

        /* Animated skeleton */
        @keyframes shimmer {
            0% {
                background-position: -200% 0;
            }

            100% {
                background-position: 200% 0;
            }
        }

        .shimmer {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }

        /* Smooth transitions */
        .transition-smooth {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Input focus */
        .input-field {
            @apply w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-medium text-gray-800 outline-none focus:bg-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 transition-all duration-200 placeholder-gray-400;
        }

        /* Rupiah format */
        .rupiah::before {
            content: 'Rp ';
        }
    </style>
</head>

<body class="h-full bg-surface">

    <!-- Max width container (mobile simulation on desktop) -->
    <div class="max-w-md mx-auto min-h-screen bg-surface relative flex flex-col shadow-2xl">

        <!-- Top Header -->
        <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-lg border-b border-gray-100">
            <div class="flex items-center justify-between px-4 h-14"> @yield('header-left') <h1
                    class="text-base font-700 text-gray-900 absolute left-1/2 -translate-x-1/2"> @yield('header-title') </h1>
                @yield('header-right') </div>
        </header>

        <!-- Content area -->
        <main class="flex-1 overflow-y-auto pb-24 hide-scroll">
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-2"
                    class="mx-4 mt-3 bg-green-50 border border-green-200 text-green-700 text-sm font-500 px-4 py-3 rounded-2xl flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mx-4 mt-3 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-2xl">
                    @foreach ($errors->all() as $error)
                        <p class="flex items-start gap-1"><span class="mt-0.5">•</span> {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Bottom Navigation -->
        <nav
            class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-md bg-white border-t border-gray-100 shadow-float z-50 safe-bottom">
            <div class="flex items-center justify-around px-2 py-2">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}"
                    class="nav-item flex flex-col items-center gap-0.5 py-1 px-4 rounded-2xl transition-smooth {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    <span class="text-xs font-600">Beranda</span>
                </a>

                <!-- Accounts -->
                <a href="{{ route('accounts.index') }}"
                    class="nav-item flex flex-col items-center gap-0.5 py-1 px-4 rounded-2xl transition-smooth {{ request()->routeIs('accounts.*') ? 'active' : '' }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                    </svg>
                    <span class="text-xs font-600">Rekening</span>
                </a>

                <!-- FAB Add Transaction -->
                <a href="{{ route('transactions.create') }}" class="flex flex-col items-center -mt-5">
                    <div
                        class="w-14 h-14 bg-primary-600 rounded-full flex items-center justify-center shadow-lg shadow-primary-300 transition-smooth hover:scale-105 active:scale-95">
                        <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </div>
                    <span class="text-xs font-600 text-primary-600 mt-1">Tambah</span>
                </a>

                <!-- Transactions -->
                <a href="{{ route('transactions.index') }}"
                    class="nav-item flex flex-col items-center gap-0.5 py-1 px-4 rounded-2xl transition-smooth {{ request()->routeIs('transactions.*') && !request()->routeIs('transactions.create') ? 'active' : '' }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z" />
                    </svg>
                    <span class="text-xs font-600">Transaksi</span>
                </a>

                {{-- <!-- Profile -->
                <a href="#"
                    class="nav-item flex flex-col items-center gap-0.5 py-1 px-4 rounded-2xl transition-smooth">
                    <div class="w-6 h-6 rounded-full bg-primary-100 flex items-center justify-center overflow-hidden">
                        <span class="text-xs font-700 text-primary-600">{{ substr(auth()->user()->name, 0, 1) }}</span>
                    </div>
                    <span class="text-xs font-600">Profil</span>
                </a> --}}
                <!-- Hutang -->
                <a href="{{ route('debts.index') }}"
                    class="nav-item flex flex-col items-center gap-0.5 py-1 px-4 rounded-2xl transition-smooth {{ request()->routeIs('debts.*') ? 'active' : '' }}">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                    <span class="text-xs font-600">Piutang</span>
                </a>
            </div>
        </nav>
    </div>

    @stack('scripts')
</body>

</html>
