@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('header')
@endsection

@section('header-left')
    <a href="{{ url()->previous() }}" class="w-8 h-8 rounded-xl bg-gray-100 flex items-center justify-center">
        <svg class="w-4 h-4 text-gray-700" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
        </svg>
    </a>
@endsection

@section('header-title', 'Detail Transaksi')

@section('header-right')
    <form action="{{ route('transactions.destroy', $transaction) }}" method="POST"
          onsubmit="return confirm('Yakin hapus transaksi ini? Saldo akan dikembalikan.')">
        @csrf @method('DELETE')
        <button type="submit" class="w-8 h-8 rounded-xl bg-red-50 flex items-center justify-center">
            <svg class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
            </svg>
        </button>
    </form>
@endsection

@section('content')
<div class="px-4 pt-4">

    <!-- Amount card -->
    <div class="rounded-3xl p-6 mb-4 text-center relative overflow-hidden
        @if($transaction->type === 'income') bg-gradient-to-br from-green-500 to-emerald-600
        @elseif($transaction->type === 'expense') bg-gradient-to-br from-red-500 to-rose-600
        @elseif($transaction->type === 'transfer') bg-gradient-to-br from-blue-600 to-indigo-700
        @else bg-gradient-to-br from-orange-500 to-amber-600 @endif">

        <div class="absolute -top-6 -right-6 w-28 h-28 rounded-full bg-white/10"></div>
        <div class="absolute -bottom-8 -left-4 w-24 h-24 rounded-full bg-white/5"></div>

        <div class="relative z-10">
            <!-- Type badge -->
            <span class="inline-block bg-white/20 text-white text-xs font-700 px-3 py-1 rounded-full mb-3">
                {{ $transaction->type_label }}
                @if($transaction->transfer_type)
                    ({{ $transaction->transfer_type === 'debit' ? 'Keluar' : 'Masuk' }})
                @endif
            </span>

            <p class="text-white text-4xl font-800 mb-1">
                Rp {{ number_format($transaction->amount, 0, ',', '.') }}
            </p>

            @if($transaction->admin_fee > 0)
            <p class="text-white/70 text-sm">
                + Biaya admin Rp {{ number_format($transaction->admin_fee, 0, ',', '.') }}
                = Total Rp {{ number_format($transaction->amount + $transaction->admin_fee, 0, ',', '.') }}
            </p>
            @endif

            <p class="text-white/60 text-xs mt-2">
                {{ $transaction->transaction_date->locale('id')->translatedFormat('l, d F Y • H:i') }}
            </p>
        </div>
    </div>

    <!-- Details list -->
    <div class="bg-white rounded-3xl overflow-hidden shadow-card mb-4 divide-y divide-gray-50">

        <!-- Rekening -->
        <div class="flex items-center justify-between px-4 py-3.5">
            <span class="text-xs font-600 text-gray-400">
                @if($transaction->type === 'transfer' && $transaction->transfer_type === 'debit') Dari Rekening
                @elseif($transaction->type === 'transfer' && $transaction->transfer_type === 'credit') Ke Rekening
                @else Rekening @endif
            </span>
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-lg" style="background: {{ $transaction->account->color }};"></div>
                <span class="text-sm font-700 text-gray-900">{{ $transaction->account->name }}</span>
            </div>
        </div>

        <!-- Rekening tujuan (transfer) -->
        @if($transaction->type === 'transfer' && $transaction->destinationAccount)
        <div class="flex items-center justify-between px-4 py-3.5">
            <span class="text-xs font-600 text-gray-400">
                {{ $transaction->transfer_type === 'debit' ? 'Ke Rekening' : 'Dari Rekening' }}
            </span>
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-lg" style="background: {{ $transaction->destinationAccount->color }};"></div>
                <span class="text-sm font-700 text-gray-900">{{ $transaction->destinationAccount->name }}</span>
            </div>
        </div>
        @endif

        <!-- Category -->
        @if($transaction->category)
        <div class="flex items-center justify-between px-4 py-3.5">
            <span class="text-xs font-600 text-gray-400">Kategori</span>
            <span class="text-sm font-700 text-gray-900">{{ $transaction->category->name }}</span>
        </div>
        @endif

        <!-- Saldo sebelum / sesudah -->
        <div class="flex items-center justify-between px-4 py-3.5">
            <span class="text-xs font-600 text-gray-400">Saldo Sebelum</span>
            <span class="text-sm font-600 text-gray-500 font-mono">Rp {{ number_format($transaction->balance_before, 0, ',', '.') }}</span>
        </div>
        <div class="flex items-center justify-between px-4 py-3.5">
            <span class="text-xs font-600 text-gray-400">Saldo Sesudah</span>
            <span class="text-sm font-700 text-gray-900 font-mono">Rp {{ number_format($transaction->balance_after, 0, ',', '.') }}</span>
        </div>

        <!-- Penerima (expense) -->
        @if($transaction->recipient_name)
        <div class="flex items-center justify-between px-4 py-3.5">
            <span class="text-xs font-600 text-gray-400">Penerima</span>
            <span class="text-sm font-700 text-gray-900">{{ $transaction->recipient_name }}</span>
        </div>
        @endif
        @if($transaction->recipient_account)
        <div class="flex items-center justify-between px-4 py-3.5">
            <span class="text-xs font-600 text-gray-400">No. Rekening</span>
            <span class="text-sm font-600 text-gray-700 font-mono">{{ $transaction->recipient_account }}</span>
        </div>
        @endif
        @if($transaction->reference_code)
        <div class="flex items-center justify-between px-4 py-3.5">
            <span class="text-xs font-600 text-gray-400">Kode Ref.</span>
            <span class="text-sm font-600 text-gray-700 font-mono">{{ $transaction->reference_code }}</span>
        </div>
        @endif

        <!-- Keterangan -->
        @if($transaction->description)
        <div class="px-4 py-3.5">
            <span class="text-xs font-600 text-gray-400 block mb-1">Keterangan</span>
            <p class="text-sm font-500 text-gray-900">{{ $transaction->description }}</p>
        </div>
        @endif

        @if($transaction->notes)
        <div class="px-4 py-3.5">
            <span class="text-xs font-600 text-gray-400 block mb-1">Catatan</span>
            <p class="text-sm text-gray-600">{{ $transaction->notes }}</p>
        </div>
        @endif

        <!-- Transaction ID -->
        <div class="flex items-center justify-between px-4 py-3.5">
            <span class="text-xs font-600 text-gray-400">ID Transaksi</span>
            <span class="text-xs font-600 text-gray-400 font-mono">#{{ str_pad($transaction->id, 8, '0', STR_PAD_LEFT) }}</span>
        </div>
    </div>

    <!-- Transfer pair link -->
    @if($transaction->type === 'transfer' && $transaction->transferPair)
    <div class="bg-blue-50 rounded-3xl p-4 mb-4 border border-blue-100">
        <p class="text-xs font-600 text-blue-600 mb-1">Bagian dari transaksi transfer</p>
        <a href="{{ route('transactions.show', $transaction->transferPair) }}"
           class="text-sm font-700 text-blue-700 flex items-center gap-1">
            Lihat sisi {{ $transaction->transfer_type === 'debit' ? 'penerimaan' : 'pengiriman' }}
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
        </a>
    </div>
    @endif

</div>
@endsection
