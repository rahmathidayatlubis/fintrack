@extends('layouts.app')

@section('title', 'Rekening')

@section('header')
@endsection

@section('header-left')
    <div class="w-8"></div>
@endsection

@section('header-title', 'Rekening Saya')

@section('header-right')
    <a href="{{ route('accounts.create') }}"
       class="w-8 h-8 bg-blue-600 rounded-xl flex items-center justify-center">
        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
    </a>
@endsection

@section('content')
<div class="px-4 pt-4">

    <!-- Total Summary Card -->
    <div class="rounded-3xl p-5 mb-5 relative overflow-hidden" style="background: linear-gradient(135deg, #0F172A 0%, #1E3A8A 80%);">
        <div class="absolute -top-6 -right-6 w-32 h-32 rounded-full bg-white/5"></div>
        <div class="absolute bottom-0 left-0 w-24 h-24 rounded-full bg-blue-400/10"></div>
        <div class="relative z-10">
            <p class="text-blue-300 text-xs font-600 uppercase tracking-widest mb-1">Total Aset</p>
            <p class="text-white text-3xl font-800 tracking-tight">Rp {{ number_format($totalBalance, 0, ',', '.') }}</p>
            <p class="text-blue-300 text-xs mt-2">{{ $accounts->count() }} rekening aktif</p>
        </div>
    </div>

    <!-- Account Grid -->
    @if($accounts->isEmpty())
    <div class="bg-white rounded-3xl p-8 text-center shadow-card">
        <div class="w-16 h-16 bg-gray-100 rounded-3xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
            </svg>
        </div>
        <h3 class="font-700 text-gray-700 mb-2">Belum ada rekening</h3>
        <p class="text-sm text-gray-400 mb-4">Tambahkan rekening bank, e-wallet, atau cash kamu</p>
        <a href="{{ route('accounts.create') }}"
           class="inline-block bg-blue-600 text-white text-sm font-700 px-6 py-3 rounded-2xl">
            + Tambah Rekening
        </a>
    </div>
    @else
    <div class="grid grid-cols-2 gap-3">
        @foreach($accounts as $account)
        <a href="{{ route('accounts.show', $account) }}"
           class="rounded-3xl p-4 relative overflow-hidden active:scale-95 transition-all shadow-card block"
           style="background: {{ $account->color }};">
            <!-- Decoration circles -->
            <div class="absolute -top-4 -right-4 w-20 h-20 rounded-full bg-white/10"></div>
            <div class="absolute -bottom-5 -left-2 w-14 h-14 rounded-full bg-white/10"></div>

            <div class="relative z-10">
                <!-- Icon + type badge -->
                <div class="flex items-start justify-between mb-3">
                    <div class="w-10 h-10 bg-white/20 rounded-2xl flex items-center justify-center">
                        @php
                        $icons = [
                            'banknotes' => 'M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z',
                            'building-library' => 'M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z',
                            'device-phone-mobile' => 'M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3',
                            'wallet' => 'M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9M3 12V9m18-3a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6m18 0V5.25A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25V6',
                            'chart-bar-square' => 'M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z',
                        ];
                        $iconPath = $icons[$account->icon] ?? $icons['wallet'];
                        @endphp
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconPath }}" />
                        </svg>
                    </div>
                    <span class="text-white/70 text-xs">{{ $account->type_label }}</span>
                </div>

                <p class="text-white/80 text-xs font-500 mb-0.5 truncate">{{ $account->name }}</p>
                @if($account->account_number)
                <p class="text-white/60 text-xs mb-2">•••• {{ substr($account->account_number, -4) }}</p>
                @endif
                <p class="text-white font-800 text-base leading-tight">
                    Rp {{ number_format($account->balance, 0, ',', '.') }}
                </p>
            </div>
        </a>
        @endforeach

        <!-- Add new -->
        <a href="{{ route('accounts.create') }}"
           class="rounded-3xl p-4 bg-white border-2 border-dashed border-gray-200 flex flex-col items-center justify-center gap-2 min-h-36 active:scale-95 transition-all">
            <div class="w-10 h-10 bg-gray-100 rounded-2xl flex items-center justify-center">
                <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
            </div>
            <span class="text-xs font-600 text-gray-400 text-center">Tambah Rekening</span>
        </a>
    </div>
    @endif

    <div class="h-4"></div>
</div>
@endsection
