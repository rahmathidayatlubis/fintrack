@extends('layouts.app')

@section('title', $account->name)

@section('header-left')
    <a href="{{ route('accounts.index') }}" class="w-8 h-8 rounded-xl bg-gray-100 flex items-center justify-center">
        <svg class="w-4 h-4 text-gray-700" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
        </svg>
    </a>
@endsection

@section('header-title')
    {{ $account->name }}
@endsection

@section('header-right')
    <a href="{{ route('accounts.edit', $account) }}" class="w-8 h-8 rounded-xl bg-gray-100 flex items-center justify-center">
        <svg class="w-4 h-4 text-black" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" />
        </svg>
    </a>
@endsection

@section('content')

    <!-- Account Header Card -->
    <div class="relative overflow-hidden" style="background: {{ $account->color }}; min-height: 200px;">
        <!-- Decorations -->
        <div class="absolute -top-8 -right-8 w-40 h-40 rounded-full bg-white/10"></div>
        <div class="absolute bottom-0 left-0 w-32 h-32 rounded-full bg-white/5"></div>
        <div class="absolute top-8 right-32 w-20 h-20 rounded-full bg-white/5"></div>

        <div class="relative z-10 px-4 pt-16 pb-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <p class="text-white/70 text-xs font-500 mb-1">{{ $account->type_label }}</p>
                    <h1 class="text-white font-800 text-xl">{{ $account->name }}</h1>
                    @if ($account->account_number)
                        <p class="text-white/60 text-xs mt-1">
                            {{ $account->bank_name ? $account->bank_name . ' • ' : '' }}••••
                            {{ substr($account->account_number, -4) }}</p>
                    @endif
                </div>
            </div>

            <p class="text-white/70 text-xs uppercase tracking-widest">Saldo Saat Ini</p>
            <p class="text-white font-800 text-3xl mt-1">Rp {{ number_format($account->balance, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="mx-4 -mt-3 mb-4 relative z-10">
        <div class="bg-white rounded-3xl p-4 shadow-card grid grid-cols-2 gap-4">
            <div class="text-center">
                <p class="text-xs text-gray-400 mb-1">Total Masuk</p>
                <p class="text-green-600 font-800 text-sm">+Rp {{ number_format($stats['total_income'], 0, ',', '.') }}</p>
            </div>
            <div class="text-center border-l border-gray-100">
                <p class="text-xs text-gray-400 mb-1">Total Keluar</p>
                <p class="text-red-500 font-800 text-sm">-Rp {{ number_format($stats['total_expense'], 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="px-4 mb-4">
        <div class="grid grid-cols-3 gap-2">
            <a href="{{ route('transactions.create', ['type' => 'income', 'account_id' => $account->id]) }}"
                class="bg-green-50 rounded-2xl py-3 flex flex-col items-center gap-1 active:scale-95 transition-all">
                <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5L12 3m0 0l7.5 7.5M12 3v18" />
                </svg>
                <span class="text-xs font-600 text-green-700">Masuk</span>
            </a>
            <a href="{{ route('transactions.create', ['type' => 'expense', 'account_id' => $account->id]) }}"
                class="bg-red-50 rounded-2xl py-3 flex flex-col items-center gap-1 active:scale-95 transition-all">
                <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5L12 21m0 0l-7.5-7.5M12 21V3" />
                </svg>
                <span class="text-xs font-600 text-red-700">Keluar</span>
            </a>
            <a href="{{ route('transactions.create', ['type' => 'transfer', 'account_id' => $account->id]) }}"
                class="bg-blue-50 rounded-2xl py-3 flex flex-col items-center gap-1 active:scale-95 transition-all">
                <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                </svg>
                <span class="text-xs font-600 text-blue-700">Transfer</span>
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="px-4 mb-3">
        <div class="flex gap-2 overflow-x-auto hide-scroll pb-1">
            @php $types = ['all' => 'Semua', 'income' => 'Masuk', 'expense' => 'Keluar', 'transfer' => 'Transfer', 'adjustment' => 'Penyesuaian']; @endphp
            @foreach ($types as $key => $label)
                <a href="{{ route('accounts.show', ['account' => $account, 'type' => $key, 'month' => $filterMonth]) }}"
                    class="flex-shrink-0 px-4 py-1.5 rounded-full text-xs font-600 transition-all
               {{ $filterType === $key ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 border border-gray-200' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Transactions list -->
    <div class="px-4">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-700 text-gray-800">Riwayat Transaksi</h3>

            <div class="flex items-center gap-2">
                <a href="{{ route('accounts.show', ['account' => $account, 'type' => $filterType, 'month' => 'all']) }}"
                    class="text-xs px-2 py-1 rounded-lg border
           {{ $filterMonth === 'all' ? 'bg-blue-600 text-white' : 'text-gray-600 border-gray-200' }}">
                    Semua
                </a>

                <input type="month" value="{{ $filterMonth !== 'all' ? $filterMonth : '' }}"
                    onchange="window.location='{{ route('accounts.show', ['account' => $account, 'type' => $filterType]) }}&month='+this.value"
                    class="text-xs text-blue-600 font-600 border-0 outline-none bg-transparent">
            </div>
        </div>

        @if ($transactions->isEmpty())
            <div class="bg-white rounded-3xl p-8 text-center shadow-card">
                <p class="text-sm text-gray-400 font-500">Tidak ada transaksi</p>
            </div>
        @else
            <div class="bg-white rounded-3xl overflow-hidden shadow-card divide-y divide-gray-50 mb-4">
                @foreach ($transactions as $tx)
                    <a href="{{ route('transactions.show', $tx) }}"
                        class="flex items-center gap-3 px-4 py-3.5 active:bg-gray-50 transition-colors">
                        <div
                            class="w-10 h-10 rounded-2xl flex items-center justify-center flex-shrink-0
                @if ($tx->type === 'income') bg-green-50
                @elseif($tx->type === 'expense') bg-red-50
                @elseif($tx->type === 'transfer') bg-blue-50
                @else bg-orange-50 @endif">
                            @if ($tx->type === 'income')
                                <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M4.5 10.5L12 3m0 0l7.5 7.5M12 3v18" />
                                </svg>
                            @elseif($tx->type === 'expense')
                                <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 13.5L12 21m0 0l-7.5-7.5M12 21V3" />
                                </svg>
                            @elseif($tx->type === 'transfer')
                                <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" />
                                </svg>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-600 text-gray-900 truncate">
                                {{ $tx->description ?: ($tx->category?->name ?: $tx->type_label) }}
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                @if ($tx->type === 'transfer' && $tx->transfer_type === 'debit')
                                    ke {{ $tx->destinationAccount?->name }}
                                @elseif($tx->type === 'transfer' && $tx->transfer_type === 'credit')
                                    dari {{ $tx->destinationAccount?->name }}
                                @elseif($tx->recipient_name)
                                    {{ $tx->recipient_name }}
                                @endif
                                • {{ $tx->transaction_date->format('d M Y') }}
                            </p>
                        </div>

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
                            <p class="text-xs text-gray-300 mt-0.5">Rp
                                {{ number_format($tx->balance_after, 0, ',', '.') }}</p>
                        </div>
                    </a>
                @endforeach
            </div>

            {{ $transactions->withQueryString()->links() }}
        @endif
    </div>

@endsection
