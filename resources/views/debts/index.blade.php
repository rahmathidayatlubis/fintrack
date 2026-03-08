@extends('layouts.app')

@section('title', 'Piutang')

@section('header')
@endsection

@section('header-left')
    <a href="{{ route('dashboard') }}" class="w-8 h-8 rounded-xl bg-gray-100 flex items-center justify-center">
        <svg class="w-4 h-4 text-gray-700" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
        </svg>
    </a>
@endsection

@section('header-title', 'Piutang')

@section('header-right')
    <div class="w-8"></div>
@endsection

@section('content')
    <div class="px-4 pt-4">

        <!-- Summary -->
        <div class="rounded-3xl p-5 mb-4 relative overflow-hidden"
            style="background: linear-gradient(135deg, #92400E 0%, #D97706 100%);">
            <div class="absolute -top-6 -right-6 w-28 h-28 rounded-full bg-white/10"></div>
            <div class="relative z-10">
                <p class="text-amber-200 text-xs font-600 uppercase tracking-widest mb-1">Total Belum Lunas</p>
                <p class="text-white text-3xl font-800">Rp {{ number_format($summary['total_unpaid'], 0, ',', '.') }}</p>
                <p class="text-amber-200 text-xs mt-2">{{ $summary['count_unpaid'] }} transaksi aktif</p>
            </div>
        </div>

        <!-- Grouped list -->
        @if ($grouped->isEmpty())
            <div class="bg-white rounded-3xl p-10 text-center shadow-card">
                <p class="text-2xl mb-2">🎉</p>
                <p class="font-700 text-gray-600">Tidak ada hutang</p>
                <p class="text-sm text-gray-400 mt-1">Semua transaksi sudah lunas</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach ($grouped as $recipientName => $group)
                    <div class="bg-white rounded-3xl overflow-hidden shadow-card" x-data="{ expanded: {{ $group['has_unpaid'] ? 'true' : 'false' }} }">

                        <!-- Group header -->
                        <button type="button" @click="expanded = !expanded"
                            class="w-full flex items-center gap-3 px-4 py-4 text-left">
                            <!-- Avatar -->
                            <div class="w-10 h-10 rounded-2xl flex items-center justify-center flex-shrink-0 font-800 text-white text-sm"
                                style="background: {{ '#' . substr(md5($recipientName), 0, 6) }};">
                                {{ strtoupper(substr($recipientName, 0, 2)) }}
                            </div>

                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-700 text-gray-900">{{ $recipientName }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    {{ $group['items']->count() }} transaksi •
                                    Sisa Rp {{ number_format($group['total_remaining'], 0, ',', '.') }}
                                </p>
                            </div>

                            <div class="text-right flex-shrink-0">
                                <p
                                    class="text-sm font-800 {{ $group['total_remaining'] > 0 ? 'text-orange-600' : 'text-green-600' }}">
                                    Rp {{ number_format($group['total_remaining'], 0, ',', '.') }}
                                </p>
                                <svg class="w-4 h-4 text-gray-400 ml-auto mt-1 transition-transform"
                                    :class="expanded ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </div>
                        </button>

                        <!-- Detail items -->
                        <div x-show="expanded" x-transition class="border-t border-gray-50 divide-y divide-gray-50">
                            @foreach ($group['items'] as $debt)
                                <a href="{{ route('debts.show', $debt) }}"
                                    class="flex items-center gap-3 px-4 py-3 active:bg-gray-50 transition-colors">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-0.5">
                                            <span
                                                class="text-xs font-700 px-2 py-0.5 rounded-full
                                @if ($debt->status === 'paid') bg-green-100 text-green-700
                                @elseif($debt->status === 'partial') bg-orange-100 text-orange-700
                                @else bg-red-100 text-red-700 @endif">
                                                {{ $debt->status_label }}
                                            </span>
                                            @if ($debt->due_date && $debt->status !== 'paid')
                                                <span class="text-xs text-gray-400">
                                                    Jatuh tempo {{ $debt->due_date->format('d M') }}
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-400">
                                            {{ $debt->transaction?->transaction_date->format('d M Y') ?? '-' }}
                                            @if ($debt->status === 'partial')
                                                • Dibayar Rp {{ number_format($debt->paid_amount, 0, ',', '.') }}
                                            @endif
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-700 text-gray-900">
                                            Rp {{ number_format($debt->effective_amount, 0, ',', '.') }}
                                        </p>
                                        @if ($debt->remaining_amount > 0)
                                            <p class="text-xs text-orange-500 font-600">
                                                Sisa Rp {{ number_format($debt->remaining_amount, 0, ',', '.') }}
                                            </p>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="h-4"></div>
    </div>
@endsection
