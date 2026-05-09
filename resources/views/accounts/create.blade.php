@extends('layouts.app')

@section('title', 'Tambah Rekening')

@section('header')
@endsection

@section('header-left')
    <a href="{{ url()->previous() }}" class="w-8 h-8 rounded-xl bg-gray-100 flex items-center justify-center">
        <svg class="w-4 h-4 text-gray-700" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
        </svg>
    </a>
@endsection

@section('header-title', 'Tambah Rekening')

@section('header-right')
    <div class="w-8"></div>
@endsection

@section('content')
    <div class="px-4 pt-4" x-data="accountForm()">

        <form action="{{ route('accounts.store') }}" method="POST" class="space-y-4">
            @csrf

            {{-- ─── Presets ──────────────────────────────────────────────────────── --}}
            <div>
                <p class="text-xs font-600 text-gray-500 uppercase tracking-wide mb-2">Pilih Bank / Dompet</p>
                <div class="flex gap-2 overflow-x-auto pb-2 hide-scroll">
                    @foreach ($presets as $name => $preset)
                        <button type="button"
                            @click="applyPreset('{{ $name }}', '{{ $preset['color'] }}', '{{ $preset['icon'] }}')"
                            class="flex-shrink-0 flex flex-col items-center gap-1.5 p-2.5 rounded-2xl border-2 border-transparent transition-all"
                            :class="selectedName === '{{ $name }}' ? 'border-blue-500 bg-blue-50' :
                                'bg-white border-gray-100'">
                            <div class="w-8 h-8 rounded-xl flex items-center justify-center text-white text-xs font-800"
                                style="background: {{ $preset['color'] }};">
                                {{ substr($name, 0, 2) }}
                            </div>
                            <span class="text-xs text-gray-600 font-500">{{ $name }}</span>
                        </button>
                    @endforeach
                    <button type="button" @click="applyPreset('', '#6B7280', 'wallet')"
                        class="flex-shrink-0 flex flex-col items-center gap-1.5 p-2.5 rounded-2xl border-2 border-transparent bg-white border-gray-100">
                        <div class="w-8 h-8 rounded-xl bg-gray-400 flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                        </div>
                        <span class="text-xs text-gray-600 font-500">Custom</span>
                    </button>
                </div>
            </div>

            {{-- ─── Preview Card ─────────────────────────────────────────────────── --}}
            <div class="rounded-3xl p-5 relative overflow-hidden transition-all"
                :style="`background: linear-gradient(135deg, ${cardColor} 0%, ${cardColor}99 100%)`">
                <div class="absolute -top-4 -right-4 w-20 h-20 rounded-full bg-white/10"></div>
                <div class="relative z-10">
                    <p class="text-white/70 text-xs font-500">Preview Rekening</p>
                    <p class="text-white font-800 text-lg" x-text="cardName || 'Nama Rekening'"></p>
                    <p class="text-white/60 text-xs" x-text="cardNumber ? '•••• ' + cardNumber.slice(-4) : '•••• ----'"></p>
                </div>
            </div>

            {{-- ─── Informasi Rekening ───────────────────────────────────────────── --}}
            <div class="bg-white rounded-3xl p-4 shadow-card space-y-4">
                <h3 class="text-sm font-700 text-gray-700">Informasi Rekening</h3>

                {{-- Nama --}}
                <div>
                    <label class="text-xs font-600 text-gray-500 uppercase tracking-wide mb-1.5 block">Nama Rekening
                        *</label>
                    <input type="text" name="name" x-model="cardName" value="{{ old('name') }}"
                        placeholder="cth: BRI Tabungan, DANA, Cash"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 text-gray-800 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all"
                        required>
                </div>

                {{-- Jenis Rekening — custom dropdown ──────────────────────────────── --}}
                <div>
                    <label class="text-xs font-600 text-gray-500 uppercase tracking-wide mb-1.5 block">Jenis Rekening
                        *</label>

                    <input type="hidden" name="type" :value="selectedType">

                    <div x-data="typePicker('{{ old('type', 'cash') }}')" class="relative">

                        {{-- Trigger button --}}
                        <button type="button" @click="open = !open; if(open) $nextTick(() => $refs.searchInput?.focus())"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 flex items-center justify-between transition-all"
                            :class="open ? 'bg-white border-blue-500 ring-2 ring-blue-100' : ''">
                            <div class="flex items-center gap-3 min-w-0">
                                {{-- Icon bulat warna --}}
                                <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0"
                                    :style="`background: ${selectedMeta.color}`">
                                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" :d="selectedMeta.icon" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-600 text-gray-800" x-text="selectedMeta.label"></p>
                                    <p class="text-xs text-gray-400 truncate" x-text="selectedMeta.desc"></p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0 transition-transform"
                                :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>

                        {{-- Dropdown --}}
                        <div x-show="open" @click.outside="open = false"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 -translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-end="opacity-0 -translate-y-2"
                            class="absolute top-full left-0 right-0 mt-2 bg-white rounded-2xl shadow-lg border border-gray-100 z-50 overflow-hidden"
                            style="display:none;">

                            {{-- Search --}}
                            <div class="p-2 border-b border-gray-50">
                                <div class="flex items-center gap-2 bg-gray-50 rounded-xl px-3 py-2">
                                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                    </svg>
                                    <input type="text" x-model="search" x-ref="searchInput"
                                        placeholder="Cari jenis rekening..."
                                        class="bg-transparent text-sm outline-none w-full text-gray-700 placeholder-gray-400">
                                </div>
                            </div>

                            {{-- Options --}}
                            <ul class="py-1 max-h-72 overflow-y-auto">
                                <template x-for="t in filtered" :key="t.key">
                                    <li @click="pick(t)"
                                        class="flex items-center gap-3 px-4 py-3 cursor-pointer hover:bg-gray-50 active:bg-gray-100 transition-colors"
                                        :class="selectedType === t.key ? 'bg-blue-50' : ''">
                                        <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                                            :style="`background: ${t.color}`">
                                            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    :d="t.icon" />
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-600 text-gray-900" x-text="t.label"></p>
                                            <p class="text-xs text-gray-400 truncate" x-text="t.desc"></p>
                                        </div>
                                        <svg x-show="selectedType === t.key" class="w-4 h-4 text-blue-500 flex-shrink-0"
                                            fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M4.5 12.75l6 6 9-13.5" />
                                        </svg>
                                    </li>
                                </template>
                                <li x-show="filtered.length === 0" class="px-4 py-4 text-center text-sm text-gray-400">
                                    Jenis tidak ditemukan
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Saldo Awal --}}
                <div>
                    <label class="text-xs font-600 text-gray-500 uppercase tracking-wide mb-1.5 block">Saldo Awal</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-600 text-gray-500">Rp</span>
                        <input type="text" inputmode="numeric" id="initial_balance_display"
                            :value="initialBalanceFormatted" @input="onInitialBalanceInput($event)"
                            @focus="$event.target.select()" placeholder="0"
                            class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 text-gray-800 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all"
                            autocomplete="off">
                        <input type="hidden" name="initial_balance" :value="initialBalance">
                    </div>
                </div>
            </div>

            {{-- ─── Detail Opsional ──────────────────────────────────────────────── --}}
            <div class="bg-white rounded-3xl p-4 shadow-card space-y-4">
                <h3 class="text-sm font-700 text-gray-700">Detail (Opsional)</h3>

                <div>
                    <label class="text-xs font-600 text-gray-500 uppercase tracking-wide mb-1.5 block">Nomor
                        Rekening</label>
                    <input type="text" name="account_number" x-model="cardNumber"
                        value="{{ old('account_number') }}" placeholder="Nomor rekening / no HP"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 text-gray-800 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                </div>

                <div>
                    <label class="text-xs font-600 text-gray-500 uppercase tracking-wide mb-1.5 block">Nama Pemilik</label>
                    <input type="text" name="account_holder" value="{{ old('account_holder') }}"
                        placeholder="Nama pemilik rekening"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 text-gray-800 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                </div>

                <div>
                    <label class="text-xs font-600 text-gray-500 uppercase tracking-wide mb-1.5 block">Nama Bank /
                        Provider</label>
                    <input type="text" name="bank_name" value="{{ old('bank_name') }}"
                        placeholder="cth: Bank BRI, DANA Indonesia"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 text-gray-800 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                </div>

                <div>
                    <label class="text-xs font-600 text-gray-500 uppercase tracking-wide mb-1.5 block">Catatan</label>
                    <textarea name="description" rows="2" placeholder="Catatan tambahan..."
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 text-gray-800 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all resize-none">{{ old('description') }}</textarea>
                </div>
            </div>

            {{-- ─── Warna & Icon ─────────────────────────────────────────────────── --}}
            <div class="bg-white rounded-3xl p-4 shadow-card">
                <h3 class="text-sm font-700 text-gray-700 mb-3">Warna & Icon</h3>

                <input type="hidden" name="color" :value="cardColor">
                <input type="hidden" name="icon" :value="cardIcon">

                <p class="text-xs text-gray-400 mb-2">Warna</p>
                <div class="flex flex-wrap gap-2 mb-4">
                    @foreach (['#1E40AF', '#0369A1', '#065F46', '#7C3AED', '#9D174D', '#B45309', '#374151', '#DC2626', '#059669', '#D97706', '#6366F1', '#EC4899'] as $c)
                        <button type="button" @click="cardColor = '{{ $c }}'"
                            class="w-8 h-8 rounded-xl transition-all border-2"
                            :class="cardColor === '{{ $c }}' ? 'border-gray-800 scale-110' : 'border-transparent'"
                            style="background: {{ $c }};"></button>
                    @endforeach
                </div>

                <p class="text-xs text-gray-400 mb-2">Icon</p>
                <div class="flex flex-wrap gap-2">
                    @php
                        $iconOptions = [
                            'wallet' =>
                                'M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9M3 12V9m18-3a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6m18 0V5.25A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25V6',
                            'banknotes' =>
                                'M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z',
                            'building-library' =>
                                'M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z',
                            'device-phone-mobile' =>
                                'M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3',
                            'chart-bar-square' =>
                                'M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z',
                        ];
                    @endphp
                    @foreach ($iconOptions as $iconKey => $iconPath)
                        <button type="button" @click="cardIcon = '{{ $iconKey }}'"
                            class="w-10 h-10 rounded-2xl flex items-center justify-center transition-all border-2"
                            :class="cardIcon === '{{ $iconKey }}' ? 'border-blue-500 bg-blue-50' :
                                'border-gray-100 bg-gray-50'">
                            <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconPath }}" />
                            </svg>
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- ─── Include in Total ─────────────────────────────────────────────── --}}
            <div class="bg-white rounded-3xl p-4 shadow-card">
                <label class="flex items-center justify-between cursor-pointer">
                    <div>
                        <p class="text-sm font-600 text-gray-800">Masukkan ke Total Saldo</p>
                        <p class="text-xs text-gray-400 mt-0.5">Saldo rekening ini dihitung di total</p>
                    </div>
                    <div class="relative">
                        <input type="checkbox" name="include_in_total" value="1" class="sr-only" checked
                            id="include_toggle">
                        <div class="w-12 h-6 rounded-full transition-colors duration-200 ease-in-out"
                            :class="includeInTotal ? 'bg-blue-600' : 'bg-gray-300'"
                            @click="includeInTotal = !includeInTotal; document.getElementById('include_toggle').checked = includeInTotal">
                        </div>
                        <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform duration-200 ease-in-out"
                            :class="includeInTotal ? 'translate-x-6' : 'translate-x-0'"></div>
                    </div>
                </label>
            </div>

            {{-- ─── Submit ───────────────────────────────────────────────────────── --}}
            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-700 py-4 rounded-2xl transition-all text-sm shadow-lg shadow-blue-200">
                Simpan Rekening
            </button>

            <div class="h-4"></div>
        </form>
    </div>

    @push('scripts')
        <script>
            // ─── Data jenis rekening ──────────────────────────────────────────────────
            const accountTypes = [{
                    key: 'cash',
                    label: 'Tunai / Cash',
                    desc: 'Uang fisik, dompet',
                    color: '#16A34A',
                    icon: 'M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z',
                },
                {
                    key: 'bank',
                    label: 'Rekening Bank',
                    desc: 'Tabungan, giro, deposito',
                    color: '#2563EB',
                    icon: 'M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z',
                },
                {
                    key: 'e_wallet',
                    label: 'Dompet Digital',
                    desc: 'DANA, GoPay, OVO, ShopeePay',
                    color: '#7C3AED',
                    icon: 'M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3',
                },
                {
                    key: 'savings',
                    label: 'Tabungan',
                    desc: 'Dana darurat, tujuan khusus',
                    color: '#0891B2',
                    icon: 'M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 2.625c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125m16.5 2.625c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125',
                },
                {
                    key: 'investment',
                    label: 'Investasi',
                    desc: 'Saham, reksa dana, kripto',
                    color: '#D97706',
                    icon: 'M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z',
                },
                {
                    key: 'credit',
                    label: 'Kartu Kredit',
                    desc: 'Cicilan, tagihan bulanan',
                    color: '#DC2626',
                    icon: 'M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z',
                },
            ];

            // ─── typePicker component ─────────────────────────────────────────────────
            function typePicker(initKey) {
                return {
                    open: false,
                    search: '',
                    selectedType: initKey || 'cash',

                    get selectedMeta() {
                        return accountTypes.find(t => t.key === this.selectedType) || accountTypes[0];
                    },

                    get filtered() {
                        if (!this.search) return accountTypes;
                        const q = this.search.toLowerCase();
                        return accountTypes.filter(t =>
                            t.label.toLowerCase().includes(q) ||
                            t.desc.toLowerCase().includes(q)
                        );
                    },

                    pick(t) {
                        this.selectedType = t.key;
                        this.open = false;
                        this.search = '';

                        // Sync ke accountForm agar hidden input name="type" terupdate
                        const root = document.querySelector('[x-data*="accountForm"]');
                        if (root) Alpine.$data(root).selectedType = t.key;
                    },

                    init() {
                        // Sync nilai awal ke parent accountForm
                        const root = document.querySelector('[x-data*="accountForm"]');
                        if (root) Alpine.$data(root).selectedType = this.selectedType;
                    },
                };
            }

            // ─── accountForm ─────────────────────────────────────────────────────────
            function accountForm() {
                return {
                    cardName: '{{ old('name', '') }}',
                    cardNumber: '{{ old('account_number', '') }}',
                    cardColor: '{{ old('color', '#1E40AF') }}',
                    cardIcon: '{{ old('icon', 'wallet') }}',
                    selectedName: '',
                    selectedType: '{{ old('type', 'cash') }}',
                    includeInTotal: true,

                    // Saldo awal — format Rupiah
                    initialBalance: {{ old('initial_balance', 0) }},
                    get initialBalanceFormatted() {
                        return this.initialBalance ? this.initialBalance.toLocaleString('id-ID') : '';
                    },
                    onInitialBalanceInput(e) {
                        const raw = e.target.value.replace(/\D/g, '');
                        this.initialBalance = raw ? parseInt(raw) : 0;
                        e.target.value = raw ? parseInt(raw).toLocaleString('id-ID') : '';
                    },

                    applyPreset(name, color, icon) {
                        this.selectedName = name;
                        this.cardColor = color;
                        this.cardIcon = icon;
                        if (name) this.cardName = name;
                    },
                };
            }
        </script>
    @endpush
@endsection
