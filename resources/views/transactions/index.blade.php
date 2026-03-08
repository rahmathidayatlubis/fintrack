@extends('layouts.app')

@section('title', 'Transaksi')

@section('header')
@endsection

@section('header-left')
    <div class="w-8"></div>
@endsection

@section('header-title', 'Transaksi')

@section('header-right')
    <a href="{{ route('transactions.create') }}"
       class="w-8 h-8 bg-blue-600 rounded-xl flex items-center justify-center">
        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
    </a>
@endsection

@section('content')
<div class="px-4 pt-4">

    <!-- Month Summary -->
    <div class="grid grid-cols-3 gap-2 mb-4">
        <div class="bg-white rounded-2xl p-3 shadow-card text-center">
            <p class="text-xs text-gray-400 mb-1">Masuk</p>
            <p class="text-green-600 font-800 text-xs">+Rp {{ number_format($summary['income'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-3 shadow-card text-center">
            <p class="text-xs text-gray-400 mb-1">Keluar</p>
            <p class="text-red-500 font-800 text-xs">-Rp {{ number_format($summary['expense'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-3 shadow-card text-center">
            <p class="text-xs text-gray-400 mb-1">Selisih</p>
            <p class="font-800 text-xs {{ $summary['net'] >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                {{ $summary['net'] >= 0 ? '+' : '' }}Rp {{ number_format($summary['net'], 0, ',', '.') }}
            </p>
        </div>
    </div>

    <!-- Filter bar -->
    <div class="bg-white rounded-2xl p-3 shadow-card mb-4" x-data="{ showFilter: false }">
        <div class="flex items-center gap-2">
            <div class="flex-1 flex items-center gap-2 bg-gray-50 rounded-xl px-3 py-2">
                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
                <form action="{{ route('transactions.index') }}" method="GET" class="flex-1">
                    @foreach(request()->except('search') as $key => $val)
                    <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                    @endforeach
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari transaksi..."
                           class="bg-transparent text-sm outline-none w-full text-gray-700 placeholder-gray-400">
                </form>
            </div>
            <button @click="showFilter = !showFilter"
                    class="w-9 h-9 bg-gray-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" />
                </svg>
            </button>
        </div>

        <!-- Expanded filters -->
        <div x-show="showFilter" x-transition class="mt-3 space-y-2 border-t border-gray-100 pt-3">
            <form action="{{ route('transactions.index') }}" method="GET" class="space-y-2">
                @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif

                <select name="type" class="w-full px-3 py-2 bg-gray-50 rounded-xl text-sm text-gray-700 outline-none border border-gray-200">
                    <option value="">Semua Jenis</option>
                    <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Pemasukan</option>
                    <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                    <option value="transfer" {{ request('type') === 'transfer' ? 'selected' : '' }}>Transfer</option>
                    <option value="adjustment" {{ request('type') === 'adjustment' ? 'selected' : '' }}>Penyesuaian</option>
                </select>

                <select name="account_id" class="w-full px-3 py-2 bg-gray-50 rounded-xl text-sm text-gray-700 outline-none border border-gray-200">
                    <option value="">Semua Rekening</option>
                    @foreach($accounts as $account)
                    <option value="{{ $account->id }}" {{ request('account_id') == $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                    @endforeach
                </select>

                <input type="month" name="month" value="{{ request('month', now()->format('Y-m')) }}"
                       class="w-full px-3 py-2 bg-gray-50 rounded-xl text-sm text-gray-700 outline-none border border-gray-200">

                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-blue-600 text-white text-sm font-600 py-2 rounded-xl">Filter</button>
                    <a href="{{ route('transactions.index') }}" class="flex-1 bg-gray-100 text-gray-600 text-sm font-600 py-2 rounded-xl text-center">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Type filter pills -->
    <div class="flex gap-2 overflow-x-auto hide-scroll pb-2 mb-3">
        @php
        $typeFilters = ['all' => 'Semua', 'income' => 'Masuk', 'expense' => 'Keluar', 'transfer' => 'Transfer'];
        @endphp
        @foreach($typeFilters as $key => $label)
        <a href="{{ route('transactions.index', array_merge(request()->except('type'), $key !== 'all' ? ['type' => $key] : [])) }}"
           class="flex-shrink-0 px-4 py-1.5 rounded-full text-xs font-600 transition-all
               {{ (request('type') === $key || ($key === 'all' && !request('type'))) ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 border border-gray-200' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    <!-- Transactions grouped by date -->
    @if($transactions->isEmpty())
    <div class="bg-white rounded-3xl p-10 text-center shadow-card">
        <div class="w-16 h-16 bg-gray-100 rounded-3xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z" />
            </svg>
        </div>
        <p class="font-700 text-gray-500 text-sm">Belum ada transaksi</p>
        <a href="{{ route('transactions.create') }}" class="inline-block mt-3 bg-blue-600 text-white text-xs font-700 px-5 py-2.5 rounded-xl">
            Tambah Transaksi
        </a>
    </div>
    @else
    @php $currentDate = null; @endphp
    @foreach($transactions as $tx)
        @php $txDate = $tx->transaction_date->format('Y-m-d'); @endphp
        @if($txDate !== $currentDate)
            @if($currentDate !== null)</div>@endif
            <div class="mb-1">
            <p class="text-xs font-700 text-gray-400 uppercase tracking-widest mb-2 mt-3">
                {{ $tx->transaction_date->locale('id')->translatedFormat('l, d F Y') }}
                @if($txDate === now()->format('Y-m-d')) <span class="text-blue-500">· Hari ini</span>
                @elseif($txDate === now()->subDay()->format('Y-m-d')) <span class="text-gray-500">· Kemarin</span>
                @endif
            </p>
            <div class="bg-white rounded-3xl overflow-hidden shadow-card divide-y divide-gray-50">
            @php $currentDate = $txDate; @endphp
        @endif

        <a href="{{ route('transactions.show', $tx) }}" class="flex items-center gap-3 px-4 py-3.5 active:bg-gray-50 transition-colors">
            <!-- Icon -->
            <div class="w-10 h-10 rounded-2xl flex items-center justify-center flex-shrink-0
                @if($tx->type === 'income') bg-green-50
                @elseif($tx->type === 'expense') bg-red-50
                @elseif($tx->type === 'transfer') bg-blue-50
                @else bg-orange-50 @endif">
                @if($tx->category && ($tx->type === 'income' || $tx->type === 'expense'))
                    <span class="text-base" title="{{ $tx->category->name }}">
                        @php
                        $emojiMap = [
                            'Makan & Minum' => '🍔', 'Transportasi' => '🚗', 'Belanja' => '🛍️',
                            'Tagihan' => '📄', 'Kesehatan' => '❤️', 'Hiburan' => '🎬',
                            'Pendidikan' => '🎓', 'Gaji' => '💼', 'Bonus' => '🎁',
                            'Investasi' => '📈', 'Freelance' => '💻',
                        ];
                        echo $emojiMap[$tx->category->name] ?? ($tx->type === 'income' ? '↙️' : '↗️');
                        @endphp
                    </span>
                @elseif($tx->type === 'transfer')
                    <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" /></svg>
                @elseif($tx->type === 'income')
                    <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5L12 3m0 0l7.5 7.5M12 3v18" /></svg>
                @elseif($tx->type === 'expense')
                    <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5L12 21m0 0l-7.5-7.5M12 21V3" /></svg>
                @else
                    <svg class="w-5 h-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" /></svg>
                @endif
            </div>

            <div class="flex-1 min-w-0">
                <p class="text-sm font-600 text-gray-900 truncate">
                    {{ $tx->description ?: ($tx->category?->name ?: $tx->type_label) }}
                </p>
                <p class="text-xs text-gray-400 mt-0.5 truncate">
                    {{ $tx->account->name }}
                    @if($tx->type === 'transfer')
                        @if($tx->transfer_type === 'debit') → {{ $tx->destinationAccount?->name }}
                        @else ← {{ $tx->destinationAccount?->name }} @endif
                    @endif
                    @if($tx->admin_fee > 0) · Adm Rp {{ number_format($tx->admin_fee, 0, ',', '.') }}@endif
                </p>
            </div>

            <p class="text-sm font-700 flex-shrink-0
                @if($tx->type === 'income' || ($tx->type === 'transfer' && $tx->transfer_type === 'credit')) text-green-600
                @elseif($tx->type === 'expense' || ($tx->type === 'transfer' && $tx->transfer_type === 'debit')) text-red-500
                @else text-orange-500 @endif">
                @if($tx->type === 'income' || ($tx->type === 'transfer' && $tx->transfer_type === 'credit'))
                    +Rp {{ number_format($tx->amount, 0, ',', '.') }}
                @elseif($tx->type === 'expense' || ($tx->type === 'transfer' && $tx->transfer_type === 'debit'))
                    -Rp {{ number_format($tx->amount + $tx->admin_fee, 0, ',', '.') }}
                @else
                    ~Rp {{ number_format($tx->amount, 0, ',', '.') }}
                @endif
            </p>
        </a>
    @endforeach
    @if($currentDate !== null)</div></div>@endif

    <!-- Pagination -->
    <div class="mt-4">{{ $transactions->withQueryString()->links() }}</div>
    @endif

    <div class="h-4"></div>
</div>
@endsection
