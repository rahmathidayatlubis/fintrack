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

        {{-- ─── Type Tabs ──────────────────────────────────────────────────────── --}}
        <div class="bg-white rounded-3xl p-1.5 shadow-card mb-4 grid grid-cols-4 gap-1">
            @php
                $tabs = [
                    'income' => ['label' => 'Masuk', 'active' => 'bg-green-500 text-white shadow-sm'],
                    'expense' => ['label' => 'Keluar', 'active' => 'bg-red-500 text-white shadow-sm'],
                    'transfer' => ['label' => 'Transfer', 'active' => 'bg-blue-500 text-white shadow-sm'],
                    'adjustment' => ['label' => 'Sesuaikan', 'active' => 'bg-orange-500 text-white shadow-sm'],
                ];
            @endphp
            @foreach ($tabs as $key => $tab)
                <button type="button" @click="txType = '{{ $key }}'"
                    class="py-2.5 rounded-2xl text-xs font-700 transition-all"
                    :class="txType === '{{ $key }}' ? '{{ $tab['active'] }}' : 'text-gray-400'">
                    {{ $tab['label'] }}
                </button>
            @endforeach
        </div>

        <form action="{{ route('transactions.store') }}" method="POST" class="space-y-3">
            @csrf
            <input type="hidden" name="type" :value="txType">

            {{-- ─── TOP UP Toggle (Expense only) ───────────────────────────────── --}}
            <div x-show="txType === 'expense'" class="bg-white rounded-3xl p-4 shadow-card">

                <label class="flex items-center justify-between cursor-pointer">
                    <div>
                        <p class="text-sm font-700 text-gray-800">Ini adalah transaksi Top Up</p>
                        <p class="text-xs text-gray-400 mt-0.5">Nominal otomatis masuk ke rekening tujuan</p>
                    </div>
                    <div class="relative flex-shrink-0 ml-3" @click="isTopUp = !isTopUp">
                        <div class="w-12 h-6 rounded-full transition-colors duration-200"
                            :class="isTopUp ? 'bg-blue-600' : 'bg-gray-300'"></div>
                        <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform duration-200"
                            :class="isTopUp ? 'translate-x-6' : 'translate-x-0'"></div>
                    </div>
                </label>


            </div>

            {{-- ─── Jumlah ─────────────────────────────────────────────────────── --}}
            <div class="bg-white rounded-3xl p-5 shadow-card text-center">
                <p class="text-xs text-gray-400 uppercase tracking-widest mb-3 font-600"
                    x-text="{ income: 'Jumlah Diterima', expense: 'Jumlah Dibayar', transfer: 'Jumlah Transfer', adjustment: 'Saldo Baru' }[txType]">
                </p>
                <div class="flex items-center justify-center gap-2">
                    <span class="text-2xl font-800 text-gray-400">Rp</span>
                    <input type="text" inputmode="numeric" x-ref="amountDisplay" :value="amountFormatted"
                        @input="onAmountInput($event)" @focus="$event.target.select()" placeholder="0"
                        class="text-4xl font-800 text-gray-900 text-center outline-none bg-transparent w-full max-w-xs placeholder-gray-200"
                        style="border:none;" autocomplete="off">
                </div>
                <input type="hidden" name="amount" :value="amount">
                <div x-show="(txType === 'expense' || txType === 'transfer') && adminFee > 0"
                    class="mt-2 text-xs text-gray-400">
                    + Biaya admin Rp <span x-text="rupiahFmt(adminFee)"></span>
                    = Total Rp <span x-text="rupiahFmt(+amount + +adminFee)"></span>
                </div>
            </div>

            {{-- ─── Rekening Asal ───────────────────────────────────────────────── --}}
            <div class="bg-white rounded-3xl p-4 shadow-card">
                <label class="text-xs font-600 text-gray-500 uppercase tracking-wide mb-2 block"
                    x-text="txType === 'transfer' ? 'Rekening Asal' : 'Rekening'"></label>

                <div x-data="accountPicker('account_id', {{ old('account_id', request('account_id', 'null')) }}, null)" class="relative">
                    <input type="hidden" name="account_id" :value="selectedId">

                    <button type="button" @click="toggle()"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 flex items-center justify-between transition-all"
                        :class="open ? 'bg-white border-blue-500 ring-2 ring-blue-100' : ''">
                        <div class="flex items-center gap-2 min-w-0">
                            <div x-show="selectedId" class="w-5 h-5 rounded-lg flex-shrink-0"
                                :style="`background:${selectedColor}`"></div>
                            <span x-text="selectedLabel || 'Pilih rekening...'"
                                :class="selectedLabel ? 'text-gray-800' : 'text-gray-400'" class="truncate text-sm"></span>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0 transition-transform"
                            :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>

                    <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0 -translate-y-2"
                        class="absolute top-full left-0 right-0 mt-2 bg-white rounded-2xl shadow-lg border border-gray-100 z-50 overflow-hidden"
                        style="display:none;">
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
                        <ul class="max-h-48 overflow-y-auto py-1">
                            <template x-for="acc in filteredAccounts()" :key="acc.id">
                                <li @click="pick(acc); $dispatch('fintrack:source-changed', {id: acc.id})"
                                    class="flex items-center gap-3 px-4 py-2.5 cursor-pointer hover:bg-gray-50"
                                    :class="selectedId == acc.id ? 'bg-blue-50' : ''">
                                    <div class="w-8 h-8 rounded-xl flex-shrink-0" :style="`background:${acc.color}`">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-600 text-gray-900 truncate" x-text="acc.name"></p>
                                        <p class="text-xs text-gray-400" x-text="'Rp ' + rupiahFmt(acc.balance)"></p>
                                    </div>
                                    <svg x-show="selectedId == acc.id" class="w-4 h-4 text-blue-500 flex-shrink-0"
                                        fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                </li>
                            </template>
                            <li x-show="filteredAccounts().length === 0"
                                class="px-4 py-4 text-center text-sm text-gray-400">Rekening tidak ditemukan</li>
                        </ul>
                    </div>
                </div>

                <div x-show="sourceId && (txType === 'expense' || txType === 'transfer') && +amount > 0 && +amount > getSourceBalance()"
                    class="mt-2 text-xs text-red-500 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                    Saldo tidak mencukupi
                </div>
            </div>

            {{-- ─── Rekening Tujuan (Transfer) ──────────────────────────────────── --}}
            <div x-show="txType === 'transfer'" class="bg-white rounded-3xl p-4 shadow-card">
                <label class="text-xs font-600 text-gray-500 uppercase tracking-wide mb-2 block">Rekening Tujuan</label>

                <div x-data="accountPicker('destination_account_id', null, 'destination')" @fintrack:source-changed.window="onSourceChanged($event.detail.id)"
                    class="relative">
                    <input type="hidden" name="destination_account_id" :value="selectedId">

                    <button type="button" @click="toggle()"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 flex items-center justify-between transition-all"
                        :class="open ? 'bg-white border-blue-500 ring-2 ring-blue-100' : ''">
                        <div class="flex items-center gap-2 min-w-0">
                            <div x-show="selectedId" class="w-5 h-5 rounded-lg flex-shrink-0"
                                :style="`background:${selectedColor}`"></div>
                            <span x-text="selectedLabel || 'Pilih rekening tujuan...'"
                                :class="selectedLabel ? 'text-gray-800' : 'text-gray-400'"
                                class="truncate text-sm"></span>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0 transition-transform"
                            :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>

                    <div x-show="open" @click.outside="open = false"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0 -translate-y-2"
                        class="absolute top-full left-0 right-0 mt-2 bg-white rounded-2xl shadow-lg border border-gray-100 z-50 overflow-hidden"
                        style="display:none;">
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
                        <ul class="max-h-48 overflow-y-auto py-1">
                            <template x-for="acc in filteredAccounts()" :key="acc.id">
                                <li @click="pick(acc)"
                                    class="flex items-center gap-3 px-4 py-2.5 cursor-pointer hover:bg-gray-50"
                                    :class="selectedId == acc.id ? 'bg-blue-50' : ''">
                                    <div class="w-8 h-8 rounded-xl flex-shrink-0" :style="`background:${acc.color}`">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-600 text-gray-900 truncate" x-text="acc.name"></p>
                                        <p class="text-xs text-gray-400" x-text="'Rp ' + rupiahFmt(acc.balance)"></p>
                                    </div>
                                    <svg x-show="selectedId == acc.id" class="w-4 h-4 text-blue-500 flex-shrink-0"
                                        fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                </li>
                            </template>
                            <li x-show="filteredAccounts().length === 0"
                                class="px-4 py-4 text-center text-sm text-gray-400">Rekening tidak ditemukan</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- ─── Kategori ────────────────────────────────────────────────────── --}}
            <div x-show="txType === 'income' || txType === 'expense'" class="bg-white rounded-3xl p-4 shadow-card">
                <label class="text-xs font-600 text-gray-500 uppercase tracking-wide mb-2 block">Kategori</label>

                <div x-show="txType === 'income'" class="flex flex-wrap gap-2">
                    @foreach ($incomeCategories as $category)
                        <label class="cursor-pointer">
                            <input type="radio" name="category_id" value="{{ $category->id }}" class="sr-only">
                            <span
                                class="inline-block px-3 py-1.5 rounded-full text-xs font-600 border-2 transition-all cursor-pointer"
                                :class="selectedCategory === {{ $category->id }} ? 'border-blue-500 bg-blue-50 text-blue-700' :
                                    'border-gray-100 bg-gray-50 text-gray-600'"
                                @click="selectedCategory = {{ $category->id }}; document.querySelector('[name=category_id][value=\'{{ $category->id }}\']').checked = true">
                                {{ $category->name }}
                            </span>
                        </label>
                    @endforeach
                </div>

                <div x-show="txType === 'expense'" class="flex flex-wrap gap-2">
                    @foreach ($expenseCategories as $category)
                        <label class="cursor-pointer">
                            <input type="radio" name="category_id" value="{{ $category->id }}" class="sr-only">
                            <span
                                class="inline-block px-3 py-1.5 rounded-full text-xs font-600 border-2 transition-all cursor-pointer"
                                :class="selectedCategory === {{ $category->id }} ? 'border-blue-500 bg-blue-50 text-blue-700' :
                                    'border-gray-100 bg-gray-50 text-gray-600'"
                                @click="selectedCategory = {{ $category->id }}; document.querySelector('[name=category_id][value=\'{{ $category->id }}\']').checked = true">
                                {{ $category->name }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- ─── Biaya Admin + Komisi ─────────────────────────────────────────── --}}
            <div x-show="txType === 'expense' || txType === 'transfer'"
                class="bg-white rounded-3xl p-4 shadow-card space-y-3">
                <h3 class="text-xs font-600 text-gray-500 uppercase tracking-wide">Biaya & Komisi</h3>

                <div>
                    <label class="text-xs font-500 text-gray-500 mb-1.5 block">Biaya Admin / Operasional</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-600 text-gray-400">Rp</span>
                        <input type="text" inputmode="numeric" x-ref="adminFeeDisplay" :value="adminFeeFormatted"
                            @input="onAdminFeeInput($event)" @focus="$event.target.select()" placeholder="0"
                            class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-600 text-gray-800 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all"
                            autocomplete="off">
                        <input type="hidden" name="admin_fee" :value="adminFee">
                    </div>
                </div>

                <div>
                    <label class="text-xs font-500 text-gray-500 mb-1.5 block">Komisi / Jasa Transfer</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-600 text-gray-400">Rp</span>
                        <input type="text" inputmode="numeric" x-ref="feeIncomeDisplay" :value="feeIncomeFormatted"
                            @input="onFeeIncomeInput($event)" @focus="$event.target.select()" placeholder="0"
                            class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-600 text-gray-800 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all"
                            autocomplete="off">
                        <input type="hidden" name="fee_income_amount" :value="feeIncomeAmount">
                    </div>
                </div>

                <div x-show="txType === 'expense'" class="sr-only">
                    <label class="text-xs font-500 text-gray-500 mb-1.5 block">Komisi masuk ke rekening</label>
                    <select name="fee_income_account_id"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 text-gray-800 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all appearance-none">
                        <option value="">Tidak dicatat</option>
                        @foreach ($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- ─── Status Pembayaran ───────────────────────────────────────────── --}}
            <div x-show="txType === 'expense'" class="bg-white rounded-3xl p-4 shadow-card">
                <label class="text-xs font-600 text-gray-500 uppercase tracking-wide mb-3 block">Status Pembayaran</label>
                <div class="grid grid-cols-2 gap-2">
                    <label class="cursor-pointer">
                        <input type="radio" name="payment_status" value="paid" x-model="paymentStatus"
                            class="sr-only">
                        <div class="flex items-center gap-2 px-4 py-3 rounded-2xl border-2 transition-all"
                            :class="paymentStatus === 'paid' ? 'border-green-500 bg-green-50' :
                                'border-gray-200 bg-gray-50'">
                            <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center flex-shrink-0"
                                :class="paymentStatus === 'paid' ? 'border-green-500' : 'border-gray-300'">
                                <div x-show="paymentStatus === 'paid'" class="w-2 h-2 rounded-full bg-green-500"></div>
                            </div>
                            <div>
                                <p class="text-sm font-700"
                                    :class="paymentStatus === 'paid' ? 'text-green-700' : 'text-gray-600'">Lunas</p>
                                <p class="text-xs text-gray-400">Sudah dibayar</p>
                            </div>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="payment_status" value="debt" x-model="paymentStatus"
                            class="sr-only">
                        <div class="flex items-center gap-2 px-4 py-3 rounded-2xl border-2 transition-all"
                            :class="paymentStatus === 'debt' ? 'border-orange-500 bg-orange-50' :
                                'border-gray-200 bg-gray-50'">
                            <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center flex-shrink-0"
                                :class="paymentStatus === 'debt' ? 'border-orange-500' : 'border-gray-300'">
                                <div x-show="paymentStatus === 'debt'" class="w-2 h-2 rounded-full bg-orange-500"></div>
                            </div>
                            <div>
                                <p class="text-sm font-700"
                                    :class="paymentStatus === 'debt' ? 'text-orange-700' : 'text-gray-600'">Hutang</p>
                                <p class="text-xs text-gray-400">Belum dibayar</p>
                            </div>
                        </div>
                    </label>
                </div>

                <div x-show="paymentStatus === 'debt' && amount > 0"
                    class="mt-3 bg-orange-50 border border-orange-200 rounded-2xl px-4 py-3">
                    <p class="text-xs font-600 text-orange-600 mb-1">Hutang yang akan dicatat</p>
                    <p class="text-sm font-800 text-orange-700">Rp <span x-text="rupiahFmt(+amount + +adminFee)"></span>
                    </p>
                    <p class="text-xs text-orange-500 mt-0.5">
                        = Jumlah (Rp <span x-text="rupiahFmt(amount)"></span>)
                        <span x-show="adminFee > 0"> + Admin (Rp <span x-text="rupiahFmt(adminFee)"></span>)</span>
                    </p>
                    <p class="text-xs text-orange-400 mt-1">atas nama: <span x-text="recipientName || '(belum diisi)'"
                            class="font-600"></span></p>
                </div>

                <div x-show="paymentStatus === 'debt'" class="mt-3">
                    <label class="text-xs font-600 text-gray-500 mb-1.5 block">Jatuh Tempo (Opsional)</label>
                    <input type="date" name="due_date" :min="new Date().toISOString().split('T')[0]"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 text-gray-800 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                </div>
            </div>

            {{-- Keterangan broo --}}
            <div class="bg-white rounded-3xl p-4 shadow-card">
                <div x-show="isTopUp && paymentStatus === 'paid'" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                    class="mt-4">
                    <label class="text-xs font-600 text-gray-500 mb-2 block">Rekening yang menerima saldo</label>
                    <select x-model="topUpAccountId"
                        :name="(isTopUp && paymentStatus === 'paid') ? 'top_up_account_id' : ''"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 text-gray-800 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all appearance-none">
                        <option value="">Pilih rekening tujuan...</option>
                        @foreach ($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->name }} — Rp
                                {{ number_format($account->balance, 0, ',', '.') }}</option>
                        @endforeach
                    </select>

                    <div x-show="amount > 0" class="mt-3 bg-blue-50 border border-blue-200 rounded-2xl px-4 py-3">
                        <p class="text-xs font-600 text-blue-600 mb-1">Saldo yang akan masuk ke rekening tujuan</p>
                        <p class="text-sm font-800 text-blue-700">Rp <span
                                x-text="rupiahFmt(+amount + +adminFee + +feeIncomeAmount)"></span></p>
                        <p class="text-xs text-blue-500 mt-1">
                            = Nominal (Rp <span x-text="rupiahFmt(amount)"></span>)
                            <span x-show="adminFee > 0"> + Admin (Rp <span x-text="rupiahFmt(adminFee)"></span>)</span>
                            <span x-show="feeIncomeAmount > 0"> + Komisi (Rp <span
                                    x-text="rupiahFmt(feeIncomeAmount)"></span>)</span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- ─── Info Penerima (Expense) ─────────────────────────────────────── --}}
            <div x-show="txType === 'expense'" class="bg-white rounded-3xl p-4 shadow-card space-y-3">
                <h3 class="text-xs font-600 text-gray-500 uppercase tracking-wide">Info Penerima (Opsional)</h3>
                <input type="text" name="recipient_name" x-model="recipientName" value="{{ old('recipient_name') }}"
                    placeholder="Nama penerima transfer"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 text-gray-800 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                <input type="text" name="recipient_account" value="{{ old('recipient_account') }}"
                    placeholder="Nomor rekening tujuan"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 text-gray-800 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                <input type="text" name="reference_code" value="{{ old('reference_code') }}"
                    placeholder="Kode referensi / ID transaksi"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 text-gray-800 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
            </div>

            {{-- ─── Tanggal & Keterangan ────────────────────────────────────────── --}}
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

            {{-- ─── Submit ──────────────────────────────────────────────────────── --}}
            <button type="submit"
                class="w-full font-700 py-4 rounded-2xl transition-all text-sm shadow-lg active:scale-95"
                :class="{
                    'bg-green-500 hover:bg-green-600 text-white shadow-green-200': txType === 'income',
                    'bg-red-500 hover:bg-red-600 text-white shadow-red-200': txType === 'expense',
                    'bg-blue-600 hover:bg-blue-700 text-white shadow-blue-200': txType === 'transfer',
                    'bg-orange-500 hover:bg-orange-600 text-white shadow-orange-200': txType === 'adjustment',
                }">
                <span
                    x-text="{ income: 'Catat Pemasukan', expense: 'Catat Pengeluaran', transfer: 'Proses Transfer', adjustment: 'Sesuaikan Saldo' }[txType]"></span>
            </button>

            <div class="h-4"></div>
        </form>
    </div>

    @push('scripts')
        <script>
            // ─── Data rekening dari PHP ───────────────────────────────────────────────────
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

            // ─── Helper format Rupiah ─────────────────────────────────────────────────────
            function rupiahFmt(val) {
                return (parseFloat(val) || 0).toLocaleString('id-ID');
            }

            // ─── accountPicker: komponen dropdown rekening yang reusable ──────────────────
            //   fieldName : nama hidden input yang di-submit
            //   initId    : id awal (dari old() / query param), atau null
            //   role      : 'destination' → dengarkan event source-changed untuk exclude rekening asal
            function accountPicker(fieldName, initId, role) {
                return {
                    open: false,
                    search: '',
                    selectedId: initId || null,
                    selectedLabel: '',
                    selectedColor: '',
                    excludeId: null, // khusus rekening tujuan transfer

                    filteredAccounts() {
                        return accountsData.filter(a => {
                            if (this.excludeId && a.id == this.excludeId) return false;
                            if (!this.search) return true;
                            return a.name.toLowerCase().includes(this.search.toLowerCase());
                        });
                    },

                    pick(acc) {
                        this.selectedId = acc.id;
                        this.selectedLabel = acc.name + ' — Rp ' + rupiahFmt(acc.balance);
                        this.selectedColor = acc.color;
                        this.open = false;
                        this.search = '';

                        // Sync sourceId ke transactionForm agar cek saldo berfungsi
                        if (fieldName === 'account_id') {
                            const root = document.querySelector('[x-data*="transactionForm"]');
                            if (root) Alpine.$data(root).sourceId = acc.id;
                        }
                    },

                    // Dipanggil oleh event fintrack:source-changed (hanya untuk rekening tujuan)
                    onSourceChanged(newSourceId) {
                        this.excludeId = newSourceId;
                        // Batalkan pilihan jika rekening tujuan = rekening asal baru
                        if (this.selectedId == newSourceId) {
                            this.selectedId = null;
                            this.selectedLabel = '';
                            this.selectedColor = '';
                        }
                    },

                    toggle() {
                        this.open = !this.open;
                        if (this.open) this.$nextTick(() => this.$refs.searchInput?.focus());
                    },

                    init() {
                        if (this.selectedId) {
                            const acc = accountsData.find(a => a.id == this.selectedId);
                            if (acc) this.pick(acc);
                        }
                    }
                };
            }

            // ─── transactionForm: state form utama ───────────────────────────────────────
            function transactionForm() {
                return {
                    txType: '{{ old('type', $type) }}',

                    // Jumlah
                    amount: '{{ old('amount', '') }}',
                    get amountFormatted() {
                        return this.amount ? rupiahFmt(this.amount) : '';
                    },
                    onAmountInput(e) {
                        const raw = e.target.value.replace(/\D/g, '');
                        this.amount = raw ? parseInt(raw) : '';
                        e.target.value = raw ? parseInt(raw).toLocaleString('id-ID') : '';
                    },

                    // Biaya admin
                    adminFee: {{ old('admin_fee', 0) }},
                    get adminFeeFormatted() {
                        return this.adminFee ? rupiahFmt(this.adminFee) : '';
                    },
                    onAdminFeeInput(e) {
                        const raw = e.target.value.replace(/\D/g, '');
                        this.adminFee = raw ? parseInt(raw) : 0;
                        e.target.value = raw ? parseInt(raw).toLocaleString('id-ID') : '';
                    },

                    // Komisi / jasa
                    feeIncomeAmount: 0,
                    get feeIncomeFormatted() {
                        return this.feeIncomeAmount ? rupiahFmt(this.feeIncomeAmount) : '';
                    },
                    onFeeIncomeInput(e) {
                        const raw = e.target.value.replace(/\D/g, '');
                        this.feeIncomeAmount = raw ? parseInt(raw) : 0;
                        e.target.value = raw ? parseInt(raw).toLocaleString('id-ID') : '';
                    },

                    // Misc
                    sourceId: {{ old('account_id', request('account_id', 'null')) }},
                    selectedCategory: {{ old('category_id', 'null') }},
                    paymentStatus: 'paid',
                    recipientName: '{{ old('recipient_name', '') }}',
                    isTopUp: false,
                    topUpAccountId: null,

                    getSourceBalance() {
                        const acc = accountsData.find(a => a.id == this.sourceId);
                        return acc ? acc.balance : 0;
                    },

                    init() {
                        this.$watch('paymentStatus', (val) => {
                            if (val !== 'paid') {
                                this.topUpAccountId = null
                            }
                        })

                        this.$watch('isTopUp', (val) => {
                            if (!val) {
                                this.topUpAccountId = null
                            }
                        })
                    }
                };
            }
        </script>
    @endpush
@endsection
