@extends('layouts.app')

@section('title', 'Transaksi Baru')

@section('header')
@endsection

@section('header-left')
    <a href="{{ url()->previous() }}" class="w-8 h-8 rounded-xl bg-gray-100 flex items-center justify-center">
        <svg class="w-4 h-4 text-gray-700" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
        </svg>
    </a>
@endsection

@section('header-title', 'Transaksi Baru')

@section('header-right')
    <div class="w-8"></div>
@endsection

@section('content')
    <div class="px-4 pt-4" x-data="transactionForm()">

        <!-- Type Selector Tabs -->
        <div class="bg-white rounded-3xl p-1.5 shadow-card mb-4 grid grid-cols-4 gap-1">
            @php
                $tabs = [
                    'income' => ['label' => 'Masuk', 'color' => 'green'],
                    'expense' => ['label' => 'Keluar', 'color' => 'red'],
                    'transfer' => ['label' => 'Transfer', 'color' => 'blue'],
                    'adjustment' => ['label' => 'Sesuaikan', 'color' => 'orange'],
                ];
            @endphp
            @foreach ($tabs as $tabKey => $tab)
                <button type="button" @click="txType = '{{ $tabKey }}'"
                    class="py-2.5 rounded-2xl text-xs font-700 transition-all"
                    :class="txType === '{{ $tabKey }}'
                        ?
                        '@if ($tab['color'] === 'green') bg-green-500 text-white shadow-sm @elseif($tab['color'] === 'red') bg-red-500 text-white shadow-sm @elseif($tab['color'] === 'blue') bg-blue-500 text-white shadow-sm @else bg-orange-500 text-white shadow-sm @endif' :
                        'text-gray-400'">
                    {{ $tab['label'] }}
                </button>
            @endforeach
        </div>

        <!-- Form -->
        <form action="{{ route('transactions.store') }}" method="POST" class="space-y-3">
            @csrf
            <input type="hidden" name="type" :value="txType">

            <!-- Amount Input - Big & prominent -->
            <div class="bg-white rounded-3xl p-5 shadow-card text-center">
                <p class="text-xs text-gray-400 uppercase tracking-widest mb-3 font-600"
                    x-text="txType === 'income' ? 'Jumlah Diterima' : txType === 'expense' ? 'Jumlah Dibayar' : txType === 'transfer' ? 'Jumlah Transfer' : 'Saldo Baru'">
                </p>

                <div class="flex items-center justify-center gap-2">
                    <span class="text-2xl font-800 text-gray-400">Rp</span>

                    <!-- Display input: menampilkan format ribuan, tidak di-submit -->
                    <input type="text" inputmode="numeric" x-ref="amountDisplay" :value="amountFormatted"
                        @input="onAmountInput($event)" @focus="$event.target.select()" placeholder="0"
                        class="text-4xl font-800 text-gray-900 text-center outline-none bg-transparent w-full max-w-xs placeholder-gray-200"
                        style="border: none;" autocomplete="off">
                </div>

                <!-- Hidden input yang benar-benar di-submit -->
                <input type="hidden" name="amount" :value="amount">

                <!-- Admin fee display -->
                <div x-show="(txType === 'expense' || txType === 'transfer') && adminFee > 0"
                    class="mt-2 text-xs text-gray-400">
                    + Biaya admin Rp <span x-text="Number(adminFee).toLocaleString('id-ID')"></span>
                    = Total Rp <span x-text="(Number(amount) + Number(adminFee)).toLocaleString('id-ID')"></span>
                </div>
            </div>

            <!-- Account Selection -->
            <div class="bg-white rounded-3xl p-4 shadow-card">
                <label class="text-xs font-600 text-gray-500 uppercase tracking-wide mb-2 block"
                    x-text="txType === 'transfer' ? 'Rekening Asal' : 'Rekening'"></label>

                <div x-data="accountSearch('account_id', '{{ request('account_id', old('account_id', '')) }}')" class="relative">

                    <!-- Hidden input untuk form submit -->
                    <input type="hidden" name="account_id" :value="selectedId" x-model="accountId">

                    <!-- Trigger button -->
                    <button type="button" @click="open = !open"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 text-gray-800 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all flex items-center justify-between"
                        :class="open ? 'bg-white border-blue-500 ring-2 ring-blue-100' : ''">
                        <span x-text="selectedLabel || 'Pilih rekening...'"
                            :class="selectedLabel ? 'text-gray-800' : 'text-gray-400'"></span>
                        <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''"
                            fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>

                    <!-- Dropdown -->
                    <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2"
                        class="absolute top-full left-0 right-0 mt-2 bg-white rounded-2xl shadow-lg border border-gray-100 z-50 overflow-hidden"
                        style="display: none;">

                        <!-- Search input -->
                        <div class="p-2 border-b border-gray-50">
                            <div class="flex items-center gap-2 bg-gray-50 rounded-xl px-3 py-2">
                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                </svg>
                                <input type="text" x-model="search" x-ref="searchInput" placeholder="Cari rekening..."
                                    class="bg-transparent text-sm outline-none w-full text-gray-700 placeholder-gray-400">
                            </div>
                        </div>

                        <!-- Options list -->
                        <ul class="max-h-48 overflow-y-auto py-1">
                            <template x-for="acc in filtered" :key="acc.id">
                                <li @click="select(acc)"
                                    class="flex items-center gap-3 px-4 py-2.5 cursor-pointer transition-colors hover:bg-gray-50 active:bg-gray-100"
                                    :class="selectedId == acc.id ? 'bg-blue-50' : ''">

                                    <!-- Color dot -->
                                    <div class="w-8 h-8 rounded-xl flex-shrink-0 flex items-center justify-center"
                                        :style="`background: ${acc.color}`">
                                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9M3 12V9m18-3a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6m18 0V5.25A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25V6" />
                                        </svg>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-600 text-gray-900 truncate" x-text="acc.name"></p>
                                        <p class="text-xs text-gray-400"
                                            x-text="'Rp ' + Number(acc.balance).toLocaleString('id-ID')"></p>
                                    </div>

                                    <!-- Checkmark -->
                                    <svg x-show="selectedId == acc.id" class="w-4 h-4 text-blue-500 flex-shrink-0"
                                        fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                </li>
                            </template>

                            <!-- Empty state -->
                            <li x-show="filtered.length === 0" class="px-4 py-4 text-center text-sm text-gray-400">
                                Rekening tidak ditemukan
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Balance warning -->
                <div x-show="accountId && (txType === 'expense' || txType === 'transfer') && amount > 0 && amount > getBalance()"
                    class="mt-2 text-xs text-red-500 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                    Saldo tidak mencukupi
                </div>
            </div>

            <!-- Destination Account (Transfer only) -->
            <div x-show="txType === 'transfer'" class="bg-white rounded-3xl p-4 shadow-card">
                <label class="text-xs font-600 text-gray-500 uppercase tracking-wide mb-2 block">Rekening Tujuan</label>

                <div x-data="accountSearch('destination_account_id', '{{ old('destination_account_id', '') }}')" class="relative">

                    <input type="hidden" name="destination_account_id" :value="selectedId">

                    <!-- Trigger button -->
                    <button type="button" @click="open = !open"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 text-gray-800 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all flex items-center justify-between"
                        :class="open ? 'bg-white border-blue-500 ring-2 ring-blue-100' : ''">
                        <span x-text="selectedLabel || 'Pilih rekening tujuan...'"
                            :class="selectedLabel ? 'text-gray-800' : 'text-gray-400'"></span>
                        <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''"
                            fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>

                    <!-- Dropdown -->
                    <div x-show="open" @click.outside="open = false"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2"
                        class="absolute top-full left-0 right-0 mt-2 bg-white rounded-2xl shadow-lg border border-gray-100 z-50 overflow-hidden"
                        style="display: none;">

                        <!-- Search -->
                        <div class="p-2 border-b border-gray-50">
                            <div class="flex items-center gap-2 bg-gray-50 rounded-xl px-3 py-2">
                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                </svg>
                                <input type="text" x-model="search" x-ref="searchInput"
                                    placeholder="Cari rekening tujuan..."
                                    class="bg-transparent text-sm outline-none w-full text-gray-700 placeholder-gray-400">
                            </div>
                        </div>

                        <!-- Options -->
                        <ul class="max-h-48 overflow-y-auto py-1">
                            <template x-for="acc in filteredDestination" :key="acc.id">
                                <li @click="select(acc)"
                                    class="flex items-center gap-3 px-4 py-2.5 cursor-pointer transition-colors hover:bg-gray-50 active:bg-gray-100"
                                    :class="selectedId == acc.id ? 'bg-blue-50' : ''">

                                    <div class="w-8 h-8 rounded-xl flex-shrink-0 flex items-center justify-center"
                                        :style="`background: ${acc.color}`">
                                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9M3 12V9m18-3a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6m18 0V5.25A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25V6" />
                                        </svg>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-600 text-gray-900 truncate" x-text="acc.name"></p>
                                        <p class="text-xs text-gray-400"
                                            x-text="'Rp ' + Number(acc.balance).toLocaleString('id-ID')"></p>
                                    </div>

                                    <svg x-show="selectedId == acc.id" class="w-4 h-4 text-blue-500 flex-shrink-0"
                                        fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                </li>
                            </template>

                            <li x-show="filteredDestination.length === 0"
                                class="px-4 py-4 text-center text-sm text-gray-400">
                                Rekening tidak ditemukan
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Category -->
            <div x-show="txType === 'income' || txType === 'expense'" class="bg-white rounded-3xl p-4 shadow-card">

                <label class="text-xs font-600 text-gray-500 uppercase tracking-wide mb-2 block">
                    Kategori
                </label>

                <!-- Income Categories -->
                <div x-show="txType === 'income'" class="flex flex-wrap gap-2">
                    @foreach ($incomeCategories as $category)
                        <label class="cursor-pointer">
                            <input type="radio" name="category_id" value="{{ $category->id }}" class="sr-only">

                            <span class="inline-block px-3 py-1.5 rounded-full text-xs font-600 border-2 transition-all"
                                :class="selectedCategory === {{ $category->id }} ?
                                    'border-blue-500 bg-blue-50 text-blue-700' :
                                    'border-gray-100 bg-gray-50 text-gray-600'"
                                @click="selectedCategory = {{ $category->id }};
                        document.querySelector('[name=category_id][value=\'{{ $category->id }}\']').checked = true">
                                {{ $category->name }}
                            </span>
                        </label>
                    @endforeach
                </div>

                <!-- Expense Categories -->
                <div x-show="txType === 'expense'" class="flex flex-wrap gap-2">
                    @foreach ($expenseCategories as $category)
                        <label class="cursor-pointer">
                            <input type="radio" name="category_id" value="{{ $category->id }}" class="sr-only">

                            <span class="inline-block px-3 py-1.5 rounded-full text-xs font-600 border-2 transition-all"
                                :class="selectedCategory === {{ $category->id }} ?
                                    'border-blue-500 bg-blue-50 text-blue-700' :
                                    'border-gray-100 bg-gray-50 text-gray-600'"
                                @click="selectedCategory = {{ $category->id }};
                        document.querySelector('[name=category_id][value=\'{{ $category->id }}\']').checked = true">
                                {{ $category->name }}
                            </span>
                        </label>
                    @endforeach
                </div>

            </div>

            <!-- Status Pembayaran -->
            <div x-show="txType === 'expense'" class="bg-white rounded-3xl p-4 shadow-card">
                <label class="text-xs font-600 text-gray-500 uppercase tracking-wide mb-3 block">
                    Status Pembayaran
                </label>
                <div class="grid grid-cols-2 gap-2">
                    <label class="cursor-pointer">
                        <input type="radio" name="payment_status" value="paid" x-model="paymentStatus"
                            class="sr-only">
                        <div class="flex items-center gap-2 px-4 py-3 rounded-2xl border-2 transition-all"
                            :class="paymentStatus === 'paid'
                                ?
                                'border-green-500 bg-green-50' :
                                'border-gray-200 bg-gray-50'">
                            <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center flex-shrink-0"
                                :class="paymentStatus === 'paid' ? 'border-green-500' : 'border-gray-300'">
                                <div x-show="paymentStatus === 'paid'" class="w-2 h-2 rounded-full bg-green-500"></div>
                            </div>
                            <div>
                                <p class="text-sm font-700"
                                    :class="paymentStatus === 'paid' ? 'text-green-700' : 'text-gray-600'">
                                    Lunas
                                </p>
                                <p class="text-xs text-gray-400">Sudah dibayar</p>
                            </div>
                        </div>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" name="payment_status" value="debt" x-model="paymentStatus"
                            class="sr-only">
                        <div class="flex items-center gap-2 px-4 py-3 rounded-2xl border-2 transition-all"
                            :class="paymentStatus === 'debt'
                                ?
                                'border-orange-500 bg-orange-50' :
                                'border-gray-200 bg-gray-50'">
                            <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center flex-shrink-0"
                                :class="paymentStatus === 'debt' ? 'border-orange-500' : 'border-gray-300'">
                                <div x-show="paymentStatus === 'debt'" class="w-2 h-2 rounded-full bg-orange-500"></div>
                            </div>
                            <div>
                                <p class="text-sm font-700"
                                    :class="paymentStatus === 'debt' ? 'text-orange-700' : 'text-gray-600'">
                                    Hutang
                                </p>
                                <p class="text-xs text-gray-400">Belum dibayar</p>
                            </div>
                        </div>
                    </label>
                </div>

                <!-- Info preview hutang -->
                <div x-show="paymentStatus === 'debt' && amount > 0"
                    class="mt-3 bg-orange-50 border border-orange-200 rounded-2xl px-4 py-3">
                    <p class="text-xs font-600 text-orange-600 mb-1">Hutang yang akan dicatat</p>
                    <p class="text-sm font-800 text-orange-700">
                        Rp <span x-text="(Number(amount) + Number(adminFee)).toLocaleString('id-ID')"></span>
                    </p>
                    <p class="text-xs text-orange-500 mt-0.5">
                        = Jumlah (Rp <span x-text="Number(amount).toLocaleString('id-ID')"></span>)
                        <span x-show="adminFee > 0">
                            + Admin (Rp <span x-text="Number(adminFee).toLocaleString('id-ID')"></span>)
                        </span>
                    </p>
                    <p class="text-xs text-orange-400 mt-1">
                        atas nama: <span x-text="recipientName || '(belum diisi)'" class="font-600"></span>
                    </p>
                </div>

                <!-- Due date jika hutang -->
                <div x-show="paymentStatus === 'debt'" class="mt-3">
                    <label class="text-xs font-600 text-gray-500 mb-1.5 block">Jatuh Tempo (Opsional)</label>
                    <input type="date" name="due_date" :min="new Date().toISOString().split('T')[0]"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 text-gray-800 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                </div>
            </div>

            <!-- Admin Fee (expense/transfer) -->
            <div x-show="txType === 'expense' || txType === 'transfer'" class="bg-white rounded-3xl p-4 shadow-card">
                <label class="text-xs font-600 text-gray-500 uppercase tracking-wide mb-2 block">Biaya Admin
                    (Operasional)</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-600 text-gray-500">Rp</span>
                    <input type="number" name="admin_fee" x-model="adminFee" placeholder="(Tidak ada biaya)"
                        min="0" step="1"
                        class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 text-gray-800 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                </div>

                <div class="mt-3">
                    <label class="text-xs font-600 text-gray-500 mb-1.5 block uppercase">Komisi/jasa
                        transfer</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-600 text-gray-500">Rp</span>
                        <input type="number" name="fee_income_amount" placeholder="Jumlah pendapatan jasa"
                            min="0" step="1"
                            class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 text-gray-800 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all appearance-none">
                    </div>
                </div>

                <!-- Fee income account (if admin fee > 0) -->
                <div x-show="txType === 'expense'" class="mt-3">
                    <label class="text-xs font-600 text-gray-500 mb-1.5 block uppercase">Biaya jasa transfer masuk ke
                        rekening</label>
                    <select name="fee_income_account_id"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 text-gray-800 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all appearance-none">
                        <option value="">Tidak dicatat</option>
                        @foreach ($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Recipient Info (expense only) -->
            <div x-show="txType === 'expense'" class="bg-white rounded-3xl p-4 shadow-card space-y-3">
                <h3 class="text-xs font-600 text-gray-500 uppercase tracking-wide">Info Penerima (Opsional)</h3>
                <div>
                    <input type="text" name="recipient_name" x-model="recipientName"
                        value="{{ old('recipient_name') }}" placeholder="Nama penerima transfer"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 text-gray-800 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                </div>
                <div>
                    <input type="text" name="recipient_account" value="{{ old('recipient_account') }}"
                        placeholder="Nomor rekening tujuan"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 text-gray-800 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                </div>
                <div>
                    <input type="text" name="reference_code" value="{{ old('reference_code') }}"
                        placeholder="Kode referensi / ID transaksi"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 text-gray-800 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                </div>
            </div>

            <!-- Date & Description -->
            <div class="bg-white rounded-3xl p-4 shadow-card space-y-3">
                <div>
                    <label class="text-xs font-600 text-gray-500 uppercase tracking-wide mb-1.5 block">Tanggal &
                        Waktu</label>
                    <input type="datetime-local" name="transaction_date"
                        value="{{ old('transaction_date', now()->format('Y-m-d\TH:i')) }}"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 text-gray-800 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all"
                        required>
                </div>

                <div>
                    <label class="text-xs font-600 text-gray-500 uppercase tracking-wide mb-1.5 block">Keterangan</label>
                    <input type="text" name="description" value="{{ old('description') }}"
                        placeholder="cth: Gaji bulan Januari, Makan siang..."
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 text-gray-800 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                </div>

                <div>
                    <label class="text-xs font-600 text-gray-500 uppercase tracking-wide mb-1.5 block">Catatan
                        (Opsional)</label>
                    <textarea name="notes" rows="2" placeholder="Tambahan catatan..."
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 text-gray-800 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all resize-none">{{ old('notes') }}</textarea>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit"
                class="w-full font-700 py-4 rounded-2xl transition-all text-sm shadow-lg active:scale-95"
                :class="{
                    'bg-green-500 hover:bg-green-600 text-white shadow-green-200': txType === 'income',
                    'bg-red-500 hover:bg-red-600 text-white shadow-red-200': txType === 'expense',
                    'bg-blue-600 hover:bg-blue-700 text-white shadow-blue-200': txType === 'transfer',
                    'bg-orange-500 hover:bg-orange-600 text-white shadow-orange-200': txType === 'adjustment',
                }">
                <span
                    x-text="{
                'income': 'Catat Pemasukan',
                'expense': 'Catat Pengeluaran',
                'transfer': 'Proses Transfer',
                'adjustment': 'Sesuaikan Saldo'
            }[txType]"></span>
            </button>

            <div class="h-4"></div>
        </form>
    </div>
    @push('scripts')
        <script>
            // Data rekening dari PHP ke JS
            const accountsData = {!! json_encode(
                $accounts->map(
                        fn($a) => [
                            'id' => $a->id,
                            'name' => $a->name,
                            'balance' => (float) $a->balance,
                            'color' => $a->color,
                        ],
                    )->values(),
            ) !!};

            function accountSearch(fieldName, defaultId = '') {
                return {
                    open: false,
                    search: '',
                    selectedId: defaultId,
                    selectedLabel: '',
                    accounts: accountsData,

                    get filtered() {
                        if (!this.search) return this.accounts;
                        return this.accounts.filter(a =>
                            a.name.toLowerCase().includes(this.search.toLowerCase())
                        );
                    },

                    get filteredDestination() {
                        return this.accounts.filter(a => {
                            const sourceId = document.querySelector('[name=account_id]')?.value;
                            if (sourceId && a.id == sourceId) return false;
                            if (!this.search) return true;
                            return a.name.toLowerCase().includes(this.search.toLowerCase());
                        });
                    },

                    select(acc) {
                        this.selectedId = acc.id;
                        this.accountId = acc.id; // sync ke transactionForm()
                        this.selectedLabel = acc.name + ' — Rp ' + Number(acc.balance).toLocaleString('id-ID');
                        this.open = false;
                        this.search = '';
                    },

                    init() {
                        // Set label awal jika ada default value (misal dari old() atau query param)
                        if (this.selectedId) {
                            const acc = this.accounts.find(a => a.id == this.selectedId);
                            if (acc) this.select(acc);
                        }
                        // Fokus search saat dropdown dibuka
                        this.$watch('open', val => {
                            if (val) this.$nextTick(() => this.$refs.searchInput.focus());
                        });
                    }
                }
            }

            function transactionForm() {
                return {
                    txType: '{{ old('type', $type) }}',
                    amount: '{{ old('amount', '') }}',
                    adminFee: '{{ old('admin_fee', 0) }}',
                    accountId: '{{ old('account_id', request('account_id', '')) }}',
                    selectedCategory: {{ old('category_id', 'null') }},
                    paymentStatus: 'paid',
                    recipientName: '{{ old('recipient_name', '') }}',

                    // Format angka ke string dengan titik ribuan untuk display
                    get amountFormatted() {
                        if (!this.amount) return '';
                        return Number(this.amount).toLocaleString('id-ID');
                    },


                    // Saat user mengetik di display input
                    onAmountInput(event) {
                        // Hapus semua karakter selain angka
                        const raw = event.target.value.replace(/\D/g, '');
                        this.amount = raw ? parseInt(raw) : '';

                        // Format ulang display dengan titik ribuan
                        event.target.value = raw ?
                            parseInt(raw).toLocaleString('id-ID') :
                            '';
                    },

                    getBalance() {
                        const acc = accountsData.find(a => a.id == this.accountId);
                        return acc ? parseFloat(acc.balance) : 0;
                    }
                }
            }
        </script>
    @endpush
@endsection
