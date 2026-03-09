@extends('layouts.app')
@section('title', 'Edit Transaksi')
@section('header', ' ')

@section('header-left')
    <a href="{{ route('transactions.show', $transaction) }}"
        class="w-8 h-8 rounded-xl bg-gray-100 flex items-center justify-center">
        <svg class="w-4 h-4 text-gray-700" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
        </svg>
    </a>
@endsection

@section('header-title', 'Edit Transaksi')
@section('header-right', '<div class="w-8"></div>')

@section('content')
    <div class="px-4 pt-4" x-data="editForm()">

        {{-- Badge tipe transaksi --}}
        <div class="mb-4 flex justify-center">
            <span
                class="px-4 py-1.5 rounded-full text-xs font-700 text-white
            @if ($transaction->type === 'income') bg-green-500
            @elseif($transaction->type === 'expense') bg-red-500
            @else bg-orange-500 @endif">
                {{ $transaction->type_label }}
            </span>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-2xl px-4 py-3 mb-4">
                @foreach ($errors->all() as $err)
                    <p class="text-xs text-red-600">{{ $err }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('transactions.update', $transaction) }}" method="POST" class="space-y-3">
            @csrf @method('PUT')

            {{-- Jumlah --}}
            <div class="bg-white rounded-3xl p-5 shadow-card text-center">
                <p class="text-xs text-gray-400 uppercase tracking-widest mb-3 font-600">
                    @if ($transaction->type === 'income')
                        Jumlah Diterima
                    @elseif($transaction->type === 'expense')
                        Jumlah Dibayar
                    @else
                        Saldo Baru
                    @endif
                </p>
                <div class="flex items-center justify-center gap-2">
                    <span class="text-2xl font-800 text-gray-400">Rp</span>
                    <input type="text" inputmode="numeric" x-ref="amountDisplay" :value="amountFormatted"
                        @input="onAmountInput($event)" @focus="$event.target.select()" placeholder="0"
                        class="text-4xl font-800 text-gray-900 text-center outline-none bg-transparent w-full max-w-xs placeholder-gray-200"
                        style="border: none;" autocomplete="off">
                </div>
                <input type="hidden" name="amount" :value="amount">
            </div>

            {{-- Rekening --}}
            <div class="bg-white rounded-3xl p-4 shadow-card">
                <label class="text-xs font-600 text-gray-500 uppercase tracking-wide mb-2 block">Rekening</label>
                <select name="account_id"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 text-gray-800 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all appearance-none">
                    @foreach ($accounts as $acc)
                        <option value="{{ $acc->id }}"
                            {{ old('account_id', $transaction->account_id) == $acc->id ? 'selected' : '' }}>
                            {{ $acc->name }} — Rp {{ number_format($acc->balance, 0, ',', '.') }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Kategori (income/expense) --}}
            @if (in_array($transaction->type, ['income', 'expense']))
                <div class="bg-white rounded-3xl p-4 shadow-card">
                    <label class="text-xs font-600 text-gray-500 uppercase tracking-wide mb-2 block">Kategori</label>
                    <div class="flex flex-wrap gap-2">
                        @php
                            $cats = $transaction->type === 'income' ? $incomeCategories : $expenseCategories;
                        @endphp
                        @foreach ($cats as $cat)
                            <label class="cursor-pointer">
                                <input type="radio" name="category_id" value="{{ $cat->id }}" class="sr-only"
                                    {{ old('category_id', $transaction->category_id) == $cat->id ? 'checked' : '' }}>
                                <span
                                    class="inline-block px-3 py-1.5 rounded-full text-xs font-600 border-2 transition-all cursor-pointer"
                                    :class="selectedCategory === {{ $cat->id }} ?
                                        'border-blue-500 bg-blue-50 text-blue-700' :
                                        'border-gray-100 bg-gray-50 text-gray-600'"
                                    @click="selectedCategory = {{ $cat->id }};
                              document.querySelector('[name=category_id][value=\'{{ $cat->id }}\']').checked = true">
                                    {{ $cat->name }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Admin fee (expense) --}}
            @if ($transaction->type === 'expense')
                <div class="bg-white rounded-3xl p-4 shadow-card space-y-3">
                    <div>
                        <label class="text-xs font-600 text-gray-500 uppercase tracking-wide mb-1.5 block">Biaya
                            Admin</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-600 text-gray-500">Rp</span>
                            <input type="number" name="admin_fee" value="{{ old('admin_fee', $transaction->admin_fee) }}"
                                placeholder="0" min="0" step="1"
                                class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-600 text-gray-500 uppercase tracking-wide mb-1.5 block">Nama
                            Penerima</label>
                        <input type="text" name="recipient_name"
                            value="{{ old('recipient_name', $transaction->recipient_name) }}" placeholder="Nama penerima"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                    </div>

                    <div>
                        <label class="text-xs font-600 text-gray-500 uppercase tracking-wide mb-1.5 block">No. Rekening
                            Tujuan</label>
                        <input type="text" name="recipient_account"
                            value="{{ old('recipient_account', $transaction->recipient_account) }}"
                            placeholder="Nomor rekening penerima"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                    </div>

                    <div>
                        <label class="text-xs font-600 text-gray-500 uppercase tracking-wide mb-1.5 block">Kode
                            Referensi</label>
                        <input type="text" name="reference_code"
                            value="{{ old('reference_code', $transaction->reference_code) }}"
                            placeholder="Kode referensi transaksi"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                    </div>
                </div>
            @endif

            {{-- Tanggal, keterangan, catatan --}}
            <div class="bg-white rounded-3xl p-4 shadow-card space-y-3">
                <div>
                    <label class="text-xs font-600 text-gray-500 uppercase tracking-wide mb-1.5 block">Tanggal &
                        Waktu</label>
                    <input type="datetime-local" name="transaction_date"
                        value="{{ old('transaction_date', $transaction->transaction_date->format('Y-m-d\TH:i')) }}"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all"
                        required>
                </div>
                <div>
                    <label class="text-xs font-600 text-gray-500 uppercase tracking-wide mb-1.5 block">Keterangan</label>
                    <input type="text" name="description" value="{{ old('description', $transaction->description) }}"
                        placeholder="Keterangan transaksi"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                </div>
                <div>
                    <label class="text-xs font-600 text-gray-500 uppercase tracking-wide mb-1.5 block">Catatan</label>
                    <textarea name="notes" rows="2" placeholder="Catatan tambahan..."
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all resize-none">{{ old('notes', $transaction->notes) }}</textarea>
                </div>
            </div>

            <button type="submit"
                class="w-full font-700 py-4 rounded-2xl transition-all text-sm active:scale-95 shadow-lg
                    @if ($transaction->type === 'income') bg-green-500 text-white shadow-green-200
                    @elseif($transaction->type === 'expense') bg-red-500 text-white shadow-red-200
                    @else bg-orange-500 text-white shadow-orange-200 @endif">
                Simpan Perubahan
            </button>

            <div class="h-4"></div>
        </form>
    </div>

    @push('scripts')
        <script>
            function editForm() {
                return {
                    amount: {{ old('amount', $transaction->amount) }},
                    selectedCategory: {{ old('category_id', $transaction->category_id ?? 'null') }},

                    get amountFormatted() {
                        if (!this.amount) return '';
                        return Number(this.amount).toLocaleString('id-ID');
                    },

                    onAmountInput(event) {
                        const raw = event.target.value.replace(/\D/g, '');
                        this.amount = raw ? parseInt(raw) : '';
                        event.target.value = raw ? parseInt(raw).toLocaleString('id-ID') : '';
                    },
                }
            }
        </script>
    @endpush
@endsection
