@extends('layouts.app')

@section('title', 'Beranda')

@section('header-title', 'Beranda')

@section('content')
    <div class="px-4 pt-4 pb-2">

        <!-- Greeting & Profile -->
        <div class="flex items-center justify-between mb-5">
            <div>
                <p class="text-xs text-gray-400 font-500">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</p>
                <h2 class="text-xl font-800 text-gray-900">Halo, {{ explode(' ', auth()->user()->name)[0] }} 👋</h2>
            </div>
            <div class="flex items-center gap-2">
                <!-- Notification bell -->
                <button class="w-10 h-10 rounded-2xl bg-white shadow-card flex items-center justify-center relative">
                    <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="1.8"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                    </svg>
                </button>
                <!-- Logout -->
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="w-10 h-10 rounded-2xl bg-white shadow-card flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="1.8"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        <!-- Total Balance Card -->
        <div class="rounded-3xl p-5 mb-5 relative overflow-hidden"
            style="background: linear-gradient(135deg, #1E40AF 0%, #3B82F6 60%, #60A5FA 100%);">
            <!-- Background decoration -->
            <div class="absolute -top-8 -right-8 w-40 h-40 rounded-full bg-white/10"></div>
            <div class="absolute -bottom-10 -left-6 w-32 h-32 rounded-full bg-white/5"></div>
            <div class="absolute top-4 right-20 w-16 h-16 rounded-full bg-white/10"></div>

            <div class="relative z-10">
                <p class="text-blue-200 text-xs font-600 uppercase tracking-widest mb-1">Total Saldo</p>
                <p class="text-white text-3xl font-800 tracking-tight mb-4">
                    Rp {{ number_format($totalBalance, 0, ',', '.') }}
                </p>

                <!-- Month summary pills -->
                <div class="flex gap-3">
                    <div class="flex-1 bg-white/15 backdrop-blur rounded-2xl px-3 py-2">
                        <div class="flex items-center gap-1.5 mb-0.5">
                            <div class="w-3 h-3 rounded-full bg-green-300 flex items-center justify-center">
                                <svg class="w-2 h-2 text-green-800" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 3l7 7H3l7-7z" />
                                </svg>
                            </div>
                            <span class="text-blue-100 text-xs">Masuk</span>
                        </div>
                        <p class="text-white font-700 text-sm">Rp {{ number_format($summary['income'], 0, ',', '.') }}</p>
                    </div>
                    <div class="flex-1 bg-white/15 backdrop-blur rounded-2xl px-3 py-2">
                        <div class="flex items-center gap-1.5 mb-0.5">
                            <div class="w-3 h-3 rounded-full bg-red-300 flex items-center justify-center">
                                <svg class="w-2 h-2 text-red-800" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 17l-7-7h14l-7 7z" />
                                </svg>
                            </div>
                            <span class="text-blue-100 text-xs">Keluar</span>
                        </div>
                        <p class="text-white font-700 text-sm">Rp {{ number_format($summary['expense'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-4 gap-3 mb-5">
            @php
                $quickActions = [
                    [
                        'href' => route('transactions.create', ['type' => 'income']),
                        'icon' =>
                            'M9 8.25H7.5a2.25 2.25 0 00-2.25 2.25v9a2.25 2.25 0 002.25 2.25h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25H15m0-3l-3-3m0 0l-3 3m3-3V15',
                        'label' => 'Masuk',
                        'color' => 'bg-green-50 text-green-600',
                    ],
                    [
                        'href' => route('transactions.create', ['type' => 'expense']),
                        'icon' =>
                            'M9 8.25H7.5a2.25 2.25 0 00-2.25 2.25v9a2.25 2.25 0 002.25 2.25h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25H15m0-3l-3-3m0 0l-3 3m3-3V15',
                        'label' => 'Keluar',
                        'color' => 'bg-red-50 text-red-600',
                    ],
                    [
                        'href' => route('transactions.create', ['type' => 'transfer']),
                        'icon' => 'M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5',
                        'label' => 'Transfer',
                        'color' => 'bg-blue-50 text-blue-600',
                    ],
                    [
                        'href' => route('accounts.create'),
                        'icon' => 'M12 4.5v15m7.5-7.5h-15',
                        'label' => 'Rekening',
                        'color' => 'bg-purple-50 text-purple-600',
                    ],
                ];
            @endphp

            @foreach ($quickActions as $action)
                <a href="{{ $action['href'] }}"
                    class="flex flex-col items-center gap-1.5 bg-white rounded-2xl py-3.5 px-2 shadow-card transition-all active:scale-95">
                    <div class="w-10 h-10 {{ $action['color'] }} rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $action['icon'] }}" />
                        </svg>
                    </div>
                    <span class="text-xs font-600 text-gray-700">{{ $action['label'] }}</span>
                </a>
            @endforeach
        </div>

        <!-- My Accounts -->
        <div class="mb-5">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-base font-800 text-gray-900">Rekening Saya</h3>
                <a href="{{ route('accounts.index') }}" class="text-xs text-blue-600 font-600">Lihat semua</a>
            </div>

            @if ($accounts->isEmpty())
                <div class="bg-white rounded-3xl p-6 text-center shadow-card">
                    <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-7 h-7 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                        </svg>
                    </div>
                    <p class="text-sm font-600 text-gray-500 mb-3">Belum ada rekening</p>
                    <a href="{{ route('accounts.create') }}"
                        class="inline-block bg-blue-600 text-white text-xs font-700 px-5 py-2 rounded-xl">
                        + Tambah Rekening
                    </a>
                </div>
            @else
                <!-- Horizontal scrollable account cards -->
                <div class="flex gap-3 overflow-x-auto pb-2 hide-scroll -mx-4 px-4">
                    @foreach ($accounts as $account)
                        <a href="{{ route('accounts.show', $account) }}"
                            class="flex-shrink-0 w-36 rounded-3xl p-4 relative overflow-hidden active:scale-95 transition-all shadow-card"
                            style="background: {{ $account->color }};">
                            <!-- Background decoration -->
                            <div class="absolute -top-4 -right-4 w-20 h-20 rounded-full bg-white/10"></div>
                            <div class="absolute -bottom-6 -left-2 w-16 h-16 rounded-full bg-white/10"></div>

                            <div class="relative z-10">
                                <!-- Account type icon -->
                                <div class="w-10 h-10 bg-white/20 rounded-2xl flex items-center justify-center mb-3">
                                    @php
                                        $icons = [
                                            'banknotes' =>
                                                'M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z',
                                            'building-library' =>
                                                'M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z',
                                            'device-phone-mobile' =>
                                                'M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3',
                                            'wallet' =>
                                                'M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9M3 12V9m18-3a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6m18 0V5.25A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25V6',
                                        ];
                                        $iconPath = $icons[$account->icon] ?? $icons['wallet'];
                                    @endphp
                                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconPath }}" />
                                    </svg>
                                </div>
                                <p class="text-white/80 text-xs font-500 mb-0.5 truncate">{{ $account->name }}</p>
                                <p class="text-white font-800 text-sm">Rp
                                    {{ number_format($account->balance, 0, ',', '.') }}</p>
                            </div>
                        </a>
                    @endforeach

                    <!-- Add new account card -->
                    <a href="{{ route('accounts.create') }}"
                        class="flex-shrink-0 w-36 rounded-3xl p-4 bg-white border-2 border-dashed border-gray-200 flex flex-col items-center justify-center gap-2 active:scale-95 transition-all">
                        <div class="w-10 h-10 bg-gray-100 rounded-2xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                        </div>
                        <span class="text-xs font-600 text-gray-400 text-center">Tambah Rekening</span>
                    </a>
                </div>
            @endif
        </div>

        <!-- Spending Chart (6 months) -->
        @if (array_sum(array_column($monthlyChart, 'income')) > 0 || array_sum(array_column($monthlyChart, 'expense')) > 0)
            <div class="bg-white rounded-3xl p-4 mb-5 shadow-card">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-800 text-gray-900">Arus Keuangan</h3>
                    <div class="flex gap-3 text-xs">
                        <span class="flex items-center gap-1 text-gray-500"><span
                                class="w-2.5 h-2.5 rounded-full bg-blue-500 inline-block"></span>Masuk</span>
                        <span class="flex items-center gap-1 text-gray-500"><span
                                class="w-2.5 h-2.5 rounded-full bg-red-400 inline-block"></span>Keluar</span>
                    </div>
                </div>

                @php
                    $maxVal = max(
                        array_merge(array_column($monthlyChart, 'income'), array_column($monthlyChart, 'expense'), [1]),
                    );
                @endphp

                <div class="flex items-end justify-between gap-1 h-24">
                    @foreach ($monthlyChart as $data)
                        @php
                            $incomeH = $maxVal > 0 ? round(($data['income'] / $maxVal) * 80) : 0;
                            $expenseH = $maxVal > 0 ? round(($data['expense'] / $maxVal) * 80) : 0;
                        @endphp
                        <div class="flex-1 flex flex-col items-center gap-1">
                            <div class="w-full flex items-end justify-center gap-0.5" style="height: 80px">
                                <div class="flex-1 bg-blue-500 rounded-t-lg transition-all"
                                    style="height: {{ $incomeH }}px; min-height: {{ $data['income'] > 0 ? 2 : 0 }}px">
                                </div>
                                <div class="flex-1 bg-red-400 rounded-t-lg transition-all"
                                    style="height: {{ $expenseH }}px; min-height: {{ $data['expense'] > 0 ? 2 : 0 }}px">
                                </div>
                            </div>
                            <span class="text-gray-400 text-xs">{{ $data['month'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Recent Transactions -->
        <div class="mb-4">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-base font-800 text-gray-900">Transaksi Terbaru</h3>
                <a href="{{ route('transactions.index') }}" class="text-xs text-blue-600 font-600">Lihat semua</a>
            </div>

            @if ($recentTransactions->isEmpty())
                <div class="bg-white rounded-3xl p-6 text-center shadow-card">
                    <p class="text-sm text-gray-400 font-500">Belum ada transaksi</p>
                </div>
            @else
                <div class="bg-white rounded-3xl overflow-hidden shadow-card divide-y divide-gray-50">
                    @foreach ($recentTransactions as $tx)
                        <a href="{{ route('transactions.show', $tx) }}"
                            class="flex items-center gap-3 px-4 py-3.5 active:bg-gray-50 transition-colors">
                            <!-- Icon -->
                            <div
                                class="w-10 h-10 rounded-2xl flex items-center justify-center flex-shrink-0
                    @if ($tx->type === 'income') bg-green-50
                    @elseif($tx->type === 'expense') bg-red-50
                    @elseif($tx->type === 'transfer') bg-blue-50
                    @else bg-orange-50 @endif">
                                @if ($tx->type === 'income')
                                    <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4.5 10.5L12 3m0 0l7.5 7.5M12 3v18" />
                                    </svg>
                                @elseif($tx->type === 'expense')
                                    <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 13.5L12 21m0 0l-7.5-7.5M12 21V3" />
                                    </svg>
                                @elseif($tx->type === 'transfer')
                                    <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-orange-500" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" />
                                    </svg>
                                @endif
                            </div>

                            <!-- Info -->
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-600 text-gray-900 truncate">
                                    {{ $tx->description ?: ($tx->category?->name ?: $tx->type_label) }}
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    {{ $tx->account->name }}
                                    @if ($tx->type === 'transfer' && $tx->transfer_type === 'debit' && $tx->destinationAccount)
                                        → {{ $tx->destinationAccount->name }}
                                    @endif
                                    • {{ $tx->transaction_date->format('d M') }}
                                </p>
                            </div>

                            <!-- Amount -->
                            <div class="text-right flex-shrink-0">
                                <p
                                    class="text-sm font-700
                        @if ($tx->type === 'income' || ($tx->type === 'transfer' && $tx->transfer_type === 'credit')) text-green-600
                        @elseif($tx->type === 'expense' || ($tx->type === 'transfer' && $tx->transfer_type === 'debit')) text-red-500
                        @else text-gray-600 @endif">
                                    @if ($tx->type === 'income' || ($tx->type === 'transfer' && $tx->transfer_type === 'credit'))
                                        +Rp {{ number_format($tx->amount, 0, ',', '.') }}
                                    @elseif($tx->type === 'expense' || ($tx->type === 'transfer' && $tx->transfer_type === 'debit'))
                                        -Rp {{ number_format($tx->amount + $tx->admin_fee, 0, ',', '.') }}
                                    @else
                                        Rp {{ number_format($tx->amount, 0, ',', '.') }}
                                    @endif
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
@endsection
