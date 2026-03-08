@extends('layouts.app')
@section('title', 'Edit Hutang')
@section('header')
@endsection

@section('header-left')
    <a href="{{ route('debts.show', $debt) }}" class="w-8 h-8 rounded-xl bg-gray-100 flex items-center justify-center">
        <svg class="w-4 h-4 text-gray-700" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
        </svg>
    </a>
@endsection

@section('header-title', 'Edit Info Hutang')
@section('header-right')
    <div class="w-8"></div>
@endsection

@section('content')
    <div class="px-4 pt-4">

        <!-- Preview status -->
        <div
            class="rounded-3xl p-4 mb-4 flex items-center gap-3
        @if ($debt->status === 'paid') bg-green-50 border border-green-200
        @elseif($debt->status === 'partial') bg-orange-50 border border-orange-200
        @else bg-red-50 border border-red-200 @endif">
            <div>
                <p
                    class="text-xs font-600
                @if ($debt->status === 'paid') text-green-600
                @elseif($debt->status === 'partial') text-orange-600
                @else text-red-600 @endif">
                    {{ $debt->status_label }}
                </p>
                <p class="text-sm font-800 text-gray-900">
                    Rp {{ number_format($debt->effective_amount, 0, ',', '.') }}
                </p>
                @if ($debt->remaining_amount > 0)
                    <p class="text-xs text-gray-500">
                        Sisa Rp {{ number_format($debt->remaining_amount, 0, ',', '.') }}
                    </p>
                @endif
            </div>
        </div>

        <form action="{{ route('debts.update', $debt) }}" method="POST" class="space-y-4">
            @csrf @method('PUT')

            <div class="bg-white rounded-3xl p-4 shadow-card space-y-4">
                <h3 class="text-sm font-700 text-gray-700">Info Penerima</h3>

                <div>
                    <label class="text-xs font-600 text-gray-500 uppercase tracking-wide mb-1.5 block">
                        Nama Penerima *
                    </label>
                    <input type="text" name="recipient_name" value="{{ old('recipient_name', $debt->recipient_name) }}"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 text-gray-800 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all"
                        required>
                </div>

                <div>
                    <label class="text-xs font-600 text-gray-500 uppercase tracking-wide mb-1.5 block">
                        No. Rekening
                    </label>
                    <input type="text" name="recipient_account"
                        value="{{ old('recipient_account', $debt->recipient_account) }}"
                        placeholder="Nomor rekening penerima"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 text-gray-800 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                </div>

                <div>
                    <label class="text-xs font-600 text-gray-500 uppercase tracking-wide mb-1.5 block">
                        Bank / Provider
                    </label>
                    <input type="text" name="recipient_bank" value="{{ old('recipient_bank', $debt->recipient_bank) }}"
                        placeholder="cth: BRI, DANA, OVO"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 text-gray-800 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                </div>
            </div>

            <div class="bg-white rounded-3xl p-4 shadow-card space-y-4">
                <h3 class="text-sm font-700 text-gray-700">Pengaturan</h3>

                <div>
                    <label class="text-xs font-600 text-gray-500 uppercase tracking-wide mb-1.5 block">
                        Jatuh Tempo
                    </label>
                    <input type="date" name="due_date" value="{{ old('due_date', $debt->due_date?->format('Y-m-d')) }}"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 text-gray-800 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                </div>

                <div>
                    <label class="text-xs font-600 text-gray-500 uppercase tracking-wide mb-1.5 block">
                        Catatan
                    </label>
                    <textarea name="notes" rows="3" placeholder="Catatan tambahan..."
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 text-gray-800 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all resize-none">{{ old('notes', $debt->notes) }}</textarea>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-700 py-4 rounded-2xl transition-all text-sm shadow-lg shadow-blue-200">
                Simpan Perubahan
            </button>

            <div class="h-4"></div>
        </form>
    </div>
@endsection
