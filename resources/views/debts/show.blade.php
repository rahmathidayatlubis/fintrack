@extends('layouts.app')
@section('title', 'Detail Hutang')
@section('header', ' ')

@section('header-left')
    <a href="{{ route('debts.index') }}" class="w-8 h-8 rounded-xl bg-gray-100 flex items-center justify-center">
        <svg class="w-4 h-4 text-gray-700" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
        </svg>
    </a>
@endsection

@section('header-title', 'Detail Hutang')

@section('header-right')
    <div class="flex items-center gap-2">
        <a href="{{ route('debts.edit', $debt) }}" class="w-8 h-8 rounded-xl bg-blue-50 flex items-center justify-center">
            <svg class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
            </svg>
        </a>
        <form action="{{ route('debts.destroy', $debt) }}" method="POST"
            onsubmit="return confirm('Hapus data hutang ini?')">
            @csrf @method('DELETE')
            <button class="w-8 h-8 rounded-xl bg-red-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                </svg>
            </button>
        </form>
    </div>
@endsection

@section('content')
    <div class="px-4 pt-4" x-data="{ showPayForm: false, showAdjustForm: false }">

        {{-- Status & Amount Card --}}
        <div
            class="rounded-3xl p-5 mb-4 relative overflow-hidden
        @if ($debt->status === 'paid') bg-gradient-to-br from-green-500 to-emerald-600
        @elseif($debt->status === 'partial') bg-gradient-to-br from-orange-500 to-amber-600
        @else bg-gradient-to-br from-red-500 to-rose-600 @endif">

            <div class="absolute -top-6 -right-6 w-28 h-28 rounded-full bg-white/10"></div>
            <div class="absolute -bottom-8 -left-4 w-24 h-24 rounded-full bg-white/5"></div>

            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <span class="bg-white/20 text-white text-xs font-700 px-3 py-1 rounded-full">
                        {{ $debt->status_label }}
                    </span>
                    @if ($debt->is_overdue)
                        <span class="bg-red-900/40 text-red-100 text-xs font-700 px-3 py-1 rounded-full">
                            ⚠️ Jatuh Tempo
                        </span>
                    @elseif($debt->due_date && $debt->status !== 'paid')
                        <span class="text-white/70 text-xs">
                            Jatuh tempo {{ $debt->due_date->format('d M Y') }}
                        </span>
                    @endif
                </div>

                <p class="text-white/70 text-xs uppercase tracking-widest mb-1">Total Hutang</p>
                <p class="text-white text-3xl font-800">{{ $debt->formatted_effective_amount }}</p>

                @if ($debt->status !== 'paid')
                    {{-- Progress bar --}}
                    <div class="mt-3 mb-1">
                        <div class="flex justify-between text-xs text-white/70 mb-1">
                            <span>Terbayar {{ $debt->payment_percentage }}%</span>
                            <span>Sisa {{ $debt->formatted_remaining_amount }}</span>
                        </div>
                        <div class="w-full bg-white/20 rounded-full h-1.5">
                            <div class="bg-white rounded-full h-1.5 transition-all"
                                style="width: {{ $debt->payment_percentage }}%"></div>
                        </div>
                    </div>
                @else
                    <p class="text-white/70 text-sm mt-1">
                        Lunas pada {{ $debt->paid_at?->format('d M Y') }}
                    </p>
                @endif
            </div>
        </div>

        {{-- Info Detail --}}
        <div class="bg-white rounded-3xl overflow-hidden shadow-card mb-4 divide-y divide-gray-50">
            <div class="flex justify-between items-center px-4 py-3.5">
                <span class="text-xs font-600 text-gray-400">Nama Penerima</span>
                <span class="text-sm font-700 text-gray-900">{{ $debt->recipient_name }}</span>
            </div>

            @if ($debt->recipient_account)
                <div class="flex justify-between items-center px-4 py-3.5">
                    <span class="text-xs font-600 text-gray-400">No. Rekening</span>
                    <span class="text-sm font-600 text-gray-700 font-mono">{{ $debt->recipient_account }}</span>
                </div>
            @endif

            @if ($debt->recipient_bank)
                <div class="flex justify-between items-center px-4 py-3.5">
                    <span class="text-xs font-600 text-gray-400">Bank / Provider</span>
                    <span class="text-sm font-600 text-gray-700">{{ $debt->recipient_bank }}</span>
                </div>
            @endif

            <div class="flex justify-between items-center px-4 py-3.5">
                <span class="text-xs font-600 text-gray-400">Nominal Awal</span>
                <span class="text-sm font-600 text-gray-700">
                    Rp {{ number_format($debt->original_amount, 0, ',', '.') }}
                </span>
            </div>

            @if ($debt->adjusted_amount && $debt->adjusted_amount != $debt->original_amount)
                <div class="flex justify-between items-center px-4 py-3.5">
                    <span class="text-xs font-600 text-gray-400">Setelah Penyesuaian</span>
                    <span class="text-sm font-700 text-blue-600">
                        Rp {{ number_format($debt->adjusted_amount, 0, ',', '.') }}
                    </span>
                </div>
            @endif

            <div class="flex justify-between items-center px-4 py-3.5">
                <span class="text-xs font-600 text-gray-400">Sudah Dibayar</span>
                <span class="text-sm font-700 text-green-600">
                    Rp {{ number_format($debt->paid_amount, 0, ',', '.') }}
                </span>
            </div>

            @if ($debt->notes)
                <div class="px-4 py-3.5">
                    <span class="text-xs font-600 text-gray-400 block mb-1">Catatan</span>
                    <p class="text-sm text-gray-600">{{ $debt->notes }}</p>
                </div>
            @endif

            @if ($debt->transaction)
                <a href="{{ route('transactions.show', $debt->transaction) }}"
                    class="flex justify-between items-center px-4 py-3.5 active:bg-gray-50 transition-colors">
                    <span class="text-xs font-600 text-gray-400">Transaksi Asal</span>
                    <span class="text-sm font-600 text-blue-600 flex items-center gap-1">
                        {{ $debt->transaction->transaction_date->format('d M Y') }}
                        • Rp {{ number_format($debt->transaction->amount + $debt->transaction->admin_fee, 0, ',', '.') }}
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                    </span>
                </a>
            @endif
        </div>

        {{-- Action Buttons --}}
        @if ($debt->status !== 'paid')
            <div class="space-y-2 mb-4">

                {{-- Tandai Lunas Sekaligus --}}
                <form action="{{ route('debts.markPaid', $debt) }}" method="POST"
                    onsubmit="return confirm('Tandai hutang ini sebagai lunas sepenuhnya?')" x-data="{ showAccountPick: false }">
                    @csrf

                    <div class="my-5">
                        <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-600 px-1">
                            <input type="checkbox" @change="showAccountPick = $event.target.checked"
                                class="w-4 h-4 text-green-600 rounded">
                            Tambahkan pelunasan ke rekening
                        </label>
                    </div>

                    <div x-show="showAccountPick" class="mb-3">
                        <select name="account_id"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 outline-none focus:bg-white focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all appearance-none">
                            <option value="">Pilih rekening...</option>
                            @foreach ($accounts as $acc)
                                <option value="{{ $acc->id }}">
                                    {{ $acc->name }} — Rp {{ number_format($acc->balance, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit"
                        class="w-full bg-green-600 text-white font-700 py-3.5 rounded-2xl text-sm active:scale-95 transition-all flex items-center justify-center gap-2 shadow-lg shadow-green-200">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        Tandai Lunas Sekarang
                        <span class="text-green-200 font-500 text-xs">
                            (Rp {{ number_format($debt->remaining_amount, 0, ',', '.') }})
                        </span>
                    </button>
                </form>

                {{-- Catat Cicilan & Sesuaikan --}}
                <div class="grid grid-cols-1 gap-3">
                    <button @click="showAdjustForm = !showAdjustForm; showPayForm = false"
                        class="py-3 rounded-2xl font-700 text-sm transition-all active:scale-95"
                        :class="showAdjustForm
                            ?
                            'bg-orange-500 text-white shadow-lg shadow-orange-200' :
                            'bg-orange-50 text-orange-700'">
                        ✏️ Sesuaikan
                    </button>
                </div>
            </div>

            {{-- Form Sesuaikan Nominal --}}
            <div x-show="showAdjustForm" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                class="bg-white rounded-3xl p-4 shadow-card mb-4" style="display: none;">
                <h3 class="text-sm font-700 text-gray-800 mb-1">Sesuaikan Nominal Hutang</h3>
                <p class="text-xs text-gray-400 mb-3">
                    Ubah total hutang yang harus dibayar. Nominal saat ini:
                    <span class="font-700 text-gray-700">{{ $debt->formatted_effective_amount }}</span>
                </p>

                <form action="{{ route('debts.adjust', $debt) }}" method="POST" class="space-y-3">
                    @csrf

                    <div>
                        <label class="text-xs font-600 text-gray-500 uppercase tracking-wide mb-1.5 block">
                            Nominal Baru *
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-600 text-gray-500">Rp</span>
                            <input type="number" name="adjusted_amount"
                                value="{{ old('adjusted_amount', $debt->effective_amount) }}" min="0"
                                step="1"
                                class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all"
                                required>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-600 text-gray-500 uppercase tracking-wide mb-1.5 block">
                            Alasan Penyesuaian
                        </label>
                        <input type="text" name="notes" value="{{ old('notes') }}"
                            placeholder="cth: Potongan diskon, kesepakatan baru..."
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                    </div>

                    <button type="submit"
                        class="w-full bg-orange-500 text-white font-700 py-3.5 rounded-2xl text-sm active:scale-95 transition-all">
                        Simpan Penyesuaian
                    </button>
                </form>
            </div>
        @endif

        {{-- Riwayat Pembayaran --}}
        @if ($debt->payments->isNotEmpty())
            <div class="bg-white rounded-3xl overflow-hidden shadow-card mb-4">
                <div class="px-4 py-3 border-b border-gray-50">
                    <h3 class="text-sm font-700 text-gray-800">Riwayat Pembayaran</h3>
                </div>
                <div class="divide-y divide-gray-50">
                    @foreach ($debt->payments as $payment)
                        <div class="flex items-center gap-3 px-4 py-3">
                            <div
                                class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0
                    {{ $payment->type === 'adjustment' ? 'bg-orange-50' : 'bg-green-50' }}">
                                @if ($payment->type === 'adjustment')
                                    <svg class="w-4 h-4 text-orange-500" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" />
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 text-green-500" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                @endif
                            </div>

                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-600 text-gray-700">
                                    {{ $payment->type === 'adjustment' ? 'Penyesuaian' : 'Pembayaran' }}
                                    @if ($payment->notes)
                                        <span class="text-gray-400 font-400">— {{ $payment->notes }}</span>
                                    @endif
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    {{ $payment->paid_at->format('d M Y') }}
                                    @if ($payment->account)
                                        • {{ $payment->account->name }}
                                    @endif
                                </p>
                            </div>

                            <div class="flex items-center gap-2 flex-shrink-0">
                                <p
                                    class="text-sm font-700 {{ $payment->type === 'adjustment' ? 'text-orange-600' : 'text-green-600' }}">
                                    {{ $payment->formatted_amount }}
                                </p>
                                <form action="{{ route('debts.deletePayment', $payment) }}" method="POST"
                                    onsubmit="return confirm('Hapus riwayat ini? Saldo hutang akan dikembalikan.')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="w-6 h-6 rounded-lg bg-red-50 flex items-center justify-center">
                                        <svg class="w-3 h-3 text-red-400" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="h-4"></div>
    </div>
@endsection
