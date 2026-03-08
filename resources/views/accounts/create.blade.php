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

            <!-- Presets -->
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

            <!-- Preview Card -->
            <div class="rounded-3xl p-5 relative overflow-hidden transition-all"
                :style="`background: linear-gradient(135deg, ${cardColor} 0%, ${cardColor}99 100%)`">
                <div class="absolute -top-4 -right-4 w-20 h-20 rounded-full bg-white/10"></div>
                <div class="relative z-10">
                    <p class="text-white/70 text-xs font-500">Preview Rekening</p>
                    <p class="text-white font-800 text-lg" x-text="cardName || 'Nama Rekening'"></p>
                    <p class="text-white/60 text-xs" x-text="cardNumber ? '•••• ' + cardNumber.slice(-4) : '•••• ----'"></p>
                </div>
            </div>

            <!-- Name -->
            <div class="bg-white rounded-3xl p-4 shadow-card space-y-4">
                <h3 class="text-sm font-700 text-gray-700">Informasi Rekening</h3>

                <div>
                    <label class="text-xs font-600 text-gray-500 uppercase tracking-wide mb-1.5 block">Nama Rekening
                        *</label>
                    <input type="text" name="name" x-model="cardName" value="{{ old('name') }}"
                        placeholder="cth: BRI Tabungan, DANA, Cash"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 text-gray-800 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all"
                        required>
                </div>

                <div>
                    <label class="text-xs font-600 text-gray-500 uppercase tracking-wide mb-1.5 block">Jenis Rekening
                        *</label>
                    <select name="type"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 text-gray-800 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all appearance-none"
                        required>
                        @foreach ($types as $key => $type)
                            <option value="{{ $key }}" {{ old('type') === $key ? 'selected' : '' }}>
                                {{ $type['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs font-600 text-gray-500 uppercase tracking-wide mb-1.5 block">Saldo Awal</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-600 text-gray-500">Rp</span>
                        <input type="number" name="initial_balance" value="{{ old('initial_balance', 0) }}" min="0"
                            step="1"
                            class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-500 text-gray-800 outline-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all"
                            required>
                    </div>
                </div>
            </div>

            <!-- Optional details -->
            <div class="bg-white rounded-3xl p-4 shadow-card space-y-4">
                <h3 class="text-sm font-700 text-gray-700">Detail (Opsional)</h3>

                <div>
                    <label class="text-xs font-600 text-gray-500 uppercase tracking-wide mb-1.5 block">Nomor
                        Rekening</label>
                    <input type="text" name="account_number" x-model="cardNumber" value="{{ old('account_number') }}"
                        placeholder="Nomor rekening / no HP"
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

            <!-- Color & Icon picker -->
            <div class="bg-white rounded-3xl p-4 shadow-card">
                <h3 class="text-sm font-700 text-gray-700 mb-3">Warna & Icon</h3>

                <input type="hidden" name="color" :value="cardColor">
                <input type="hidden" name="icon" :value="cardIcon">

                <!-- Color swatches -->
                <p class="text-xs text-gray-400 mb-2">Warna</p>
                <div class="flex flex-wrap gap-2 mb-4">
                    @foreach (['#1E40AF', '#0369A1', '#065F46', '#7C3AED', '#9D174D', '#B45309', '#374151', '#DC2626', '#059669', '#D97706', '#6366F1', '#EC4899'] as $c)
                        <button type="button" @click="cardColor = '{{ $c }}'"
                            class="w-8 h-8 rounded-xl transition-all border-2"
                            :class="cardColor === '{{ $c }}' ? 'border-gray-800 scale-110' : 'border-transparent'"
                            style="background: {{ $c }};"></button>
                    @endforeach
                </div>

                <!-- Icon picker -->
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

            <!-- Include in total -->
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

            <!-- Submit -->
            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-700 py-4 rounded-2xl transition-all text-sm shadow-lg shadow-blue-200">
                Simpan Rekening
            </button>

            <div class="h-4"></div>
        </form>
    </div>

    @push('scripts')
        <script>
            function accountForm() {
                return {
                    cardName: '{{ old('name', '') }}',
                    cardNumber: '{{ old('account_number', '') }}',
                    cardColor: '{{ old('color', '#1E40AF') }}',
                    cardIcon: '{{ old('icon', 'wallet') }}',
                    selectedName: '',
                    includeInTotal: true,
                    applyPreset(name, color, icon) {
                        this.selectedName = name;
                        this.cardColor = color;
                        this.cardIcon = icon;
                        if (name) this.cardName = name;
                    }
                }
            }
        </script>
    @endpush
@endsection
