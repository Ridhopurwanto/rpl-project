@extends('layouts.app')

@section('header-left')
    <a class="flex items-center border-2 border-[#1a2847] text-[#1a2847] text-sm font-bold px-4 py-2 rounded-full">
        KENDARAAN
    </a>
@endsection

@section('content')
    <div class="w-full min-h-screen bg-slate-100 p-4 pb-32" x-data="{ 
                        modalCheckoutOpen: false, 
                        selectedVehicleId: null,
                        selectedVehicleNopol: '',
                        selectedVehicleStatus: '',
                        showCreateModal: false 
                     }">

        {{-- 1. BAGIAN KENDARAAN AKTIF --}}
        <div class="mb-4" x-data="{ isOpen: true }">
            <div @click="isOpen = !isOpen"
                class="text-lg font-bold text-slate-700 uppercase cursor-pointer list-none flex items-center">
                <svg class="w-5 h-5 mr-2 transition-transform duration-300 ease-in-out"
                    :class="isOpen ? 'rotate-0' : '-rotate-90'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
                KENDARAAN (DI DALAM) :
            </div>

            <div x-show="isOpen" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform -translate-y-2"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform -translate-y-2" class="mt-2 space-y-3">

                @forelse($kendaraan_aktif as $log)
                    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                        <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] px-4 py-2.5 flex justify-between items-center">
                            <div>
                                <p class="text-xs text-blue-200 font-semibold uppercase">Nomor Kendaraan</p>
                                <p class="text-white font-bold text-base uppercase">{{ $log->nopol }}</p>
                            </div>
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
                                {{ $log->tipe }}
                            </span>
                        </div>

                        <div class="p-4 space-y-3">
                            <div class="flex items-center gap-3 pb-3 border-b border-gray-100">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-[10px] text-gray-500 font-semibold uppercase">Pemilik</p>
                                    <p class="text-gray-800 font-bold text-base">{{ $log->pemilik }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div>
                                    <p class="text-gray-500 text-xs uppercase mb-1">Waktu Masuk</p>
                                    <p class="text-gray-800 font-semibold">{{ $log->waktu_masuk->format('H:i') }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-xs uppercase mb-1">Keterangan</p>
                                    <form
                                        action="{{ route('anggota.kendaraan.updateKeterangan', ['id_kendaraan_log' => $log->id_log]) }}"
                                        method="POST">
                                        @csrf @method('PUT')
                                        <select name="keterangan" onchange="this.form.submit()"
                                            class="w-full border border-gray-300 rounded px-2 py-1 text-xs font-semibold focus:outline-blue-500 focus:ring-2 focus:ring-blue-300">
                                            <option value="Tidak Menginap" @if($log->keterangan == 'Tidak Menginap') selected
                                            @endif>Tidak Menginap</option>
                                            <option value="Menginap" @if($log->keterangan == 'Menginap') selected @endif>Menginap
                                            </option>
                                        </select>
                                    </form>
                                </div>
                            </div>

                            <button @click.prevent="
                                                                modalCheckoutOpen = true; 
                                                                selectedVehicleId = '{{ $log->id_log }}';
                                                                selectedVehicleNopol = '{{ $log->nopol }}';
                                                                selectedVehicleStatus = '{{ $log->keterangan }}';
                                                            "
                                class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold py-2.5 px-4 rounded-lg shadow-md transition-all transform hover:scale-[1.02] flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                    </path>
                                </svg>
                                <span class="text-sm">KELUAR</span>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-xl shadow-md p-8 text-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                </path>
                            </svg>
                        </div>
                        <p class="text-gray-500 font-semibold">Tidak ada kendaraan di dalam.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- 2. BAGIAN RIWAYAT KENDARAAN (HYBRID: SUGGESTION + LIVE SEARCH) --}}
        <div class="mb-4" x-data="{ isOpen: true }">
            <div @click="isOpen = !isOpen"
                class="text-lg font-bold text-slate-700 uppercase cursor-pointer flex items-center list-none select-none">
                <svg class="w-5 h-5 mr-2 transition-transform duration-300 ease-in-out"
                    :class="isOpen ? 'rotate-0' : '-rotate-90'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
                RIWAYAT :
            </div>

            <div x-show="isOpen" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-2">

                {{-- Filter dengan HYBRID: Suggestion + Live Search --}}
                <div class="bg-white px-6 py-5 rounded-xl shadow-sm mt-2 border border-gray-200" x-data="{}">
                    <form action="{{ route('anggota.kendaraan.index') }}" method="GET" id="searchForm">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- Filter Tanggal --}}
                            <div class="w-full">
                                <label for="tanggal" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                                    Tanggal
                                </label>
                                <div class="cursor-pointer" @click="$refs.dateInput.showPicker()">
                                    <input type="date" id="tanggal" name="tanggal" x-ref="dateInput" value="{{ $tanggal_terpilih }}"
                                        onchange="document.getElementById('searchForm').submit()"
                                        class="block w-full h-[42px] px-4 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer">
                                </div>
                            </div>

                            {{-- Search Kendaraan: HYBRID AJAX (Suggestion + Live Search tanpa reload) --}}
                            <div class="w-full" x-data="{
                nopolSearch: '{{ $nopol_filter ?? '' }}',
                tanggalFilter: '{{ $tanggal_terpilih }}',
                suggestions: [],
                loading: false,
                showSuggestions: false,
                liveSearchTimeout: null,
                liveSearching: false,

                async getSuggestions() {
                    console.log('Mencari suggestion:', this.nopolSearch);

                    if (this.nopolSearch.length < 1) {
                        this.suggestions = [];
                        this.showSuggestions = false;
                        return;
                    }

                    this.loading = true;
                    this.showSuggestions = true;

                    try {
                        const response = await fetch(`{{ route('anggota.kendaraan.searchNopol') }}?search=${encodeURIComponent(this.nopolSearch)}&tanggal=${this.tanggalFilter}`);
                        const data = await response.json();
                        console.log('Suggestions:', data);
                        this.suggestions = data;
                    } catch (error) {
                        console.error('Error:', error);
                        this.suggestions = [];
                    } finally {
                        this.loading = false;
                    }
                },

                selectNopol(nopol) {
                    this.nopolSearch = nopol;
                    this.showSuggestions = false;
                    this.triggerLiveSearchNow(); // Langsung trigger live search
                },

                // Live Search dengan AJAX (tanpa reload page)
                triggerLiveSearch() {
                    clearTimeout(this.liveSearchTimeout);

                    this.liveSearchTimeout = setTimeout(() => {
                        if (this.nopolSearch.length >= 2) {
                            console.log('Live Search triggered');
                            this.performLiveSearch();
                        }
                    }, 1000); // 1 detik delay
                },

                // Trigger instant (ketika user klik suggestion)
                triggerLiveSearchNow() {
                    clearTimeout(this.liveSearchTimeout);
                    this.performLiveSearch();
                },

                // AJAX Live Search
                async performLiveSearch() {
                    this.liveSearching = true;

                    try {
                        // Update URL tanpa reload
                        const url = new URL(window.location);
                        url.searchParams.set('nopol', this.nopolSearch);
                        url.searchParams.set('tanggal', this.tanggalFilter);
                        window.history.pushState({}, '', url);

                        // Fetch hasil riwayat via AJAX
                        const response = await fetch(`{{ route('anggota.kendaraan.getRiwayat') }}?nopol=${encodeURIComponent(this.nopolSearch)}&tanggal=${this.tanggalFilter}`);
                        const html = await response.text();

                        // Update container riwayat
                        document.getElementById('riwayat-container').innerHTML = html;

                        console.log('Live search completed');
                    } catch (error) {
                        console.error('Live search error:', error);
                    } finally {
                        this.liveSearching = false;
                    }
                },

                updateTanggal(newDate) {
                    this.tanggalFilter = newDate;
                    if (this.nopolSearch.length > 0) {
                        this.getSuggestions();
                        this.triggerLiveSearch();
                    }
                }
            }">

                                <label for="nopol" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                                    Cari Kendaraan
                                </label>
                                <div class="relative">
                                    <input type="text" id="nopol" name="nopol" x-model="nopolSearch"
                                        @input="getSuggestions(); triggerLiveSearch()"
                                        @focus="if(nopolSearch.length >= 1) getSuggestions()"
                                        @keydown.enter.prevent="triggerLiveSearchNow()"
                                        placeholder="Ketik untuk mencari..." autocomplete="off"
                                        class="block w-full h-[42px] px-4 pr-12 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm placeholder-gray-400">

                                    {{-- Loading Indicator untuk Live Search --}}
                                    <div x-show="liveSearching" class="absolute right-14 top-1/2 -translate-y-1/2">
                                        <svg class="animate-spin h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                    </div>

                                    {{-- Tombol Search Manual --}}
                                    <button type="button" @click="triggerLiveSearchNow()"
                                        class="absolute right-2 top-1/2 -translate-y-1/2 bg-blue-500 hover:bg-blue-600 text-white p-2 rounded-md transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </button>

                                    {{-- Dropdown Suggestions (TETAP TAMPIL!) --}}
                                    <div x-show="showSuggestions" @click.away="showSuggestions = false" x-transition
                                        class="absolute top-full left-0 right-0 bg-white border border-gray-300 rounded-lg shadow-2xl mt-1 z-50 max-h-80 overflow-y-auto"
                                        style="display: none;">

                                        {{-- Loading State --}}
                                        <div x-show="loading" class="px-4 py-3 text-center">
                                            <svg class="animate-spin h-5 w-5 text-blue-600 mx-auto" fill="none"
                                                viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                    stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                </path>
                                            </svg>
                                            <p class="text-xs text-gray-500 mt-1">Mencari...</p>
                                        </div>

                                        {{-- Suggestion Items --}}
                                        <template x-for="suggestion in suggestions" :key="suggestion.nopol">
                                            <div @click="selectNopol(suggestion.nopol)"
                                                class="px-4 py-3 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-0 transition-colors">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex-1">
                                                        <p class="font-bold text-gray-900 uppercase text-sm"
                                                            x-text="suggestion.nopol"></p>
                                                        <p class="text-xs text-gray-600" x-text="suggestion.pemilik"></p>
                                                    </div>
                                                    <span
                                                        class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full ml-2"
                                                        x-text="suggestion.tipe"></span>
                                                </div>
                                            </div>
                                        </template>

                                        {{-- No Results --}}
                                        <div x-show="!loading && suggestions.length === 0 && nopolSearch.length >= 1"
                                            class="px-4 py-4 text-center text-gray-500">
                                            <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                </path>
                                            </svg>
                                            <p class="text-sm font-semibold mb-1">Tidak ditemukan pada tanggal ini</p>
                                            <p class="text-xs text-gray-400">Coba kata kunci lain atau ganti tanggal</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>

                {{-- Card Riwayat (dengan ID untuk AJAX update + Initial Data) --}}
                <div id="riwayat-container" class="mt-2 space-y-3">
                    {{-- ▼▼▼ INITIAL DATA dari controller index() ▼▼▼ --}}
                    @forelse($riwayat_kendaraan as $log)
                        <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                            <div
                                class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] px-4 py-2.5 flex justify-between items-center">
                                <div>
                                    <p class="text-xs text-blue-200 font-semibold uppercase">Nomor Kendaraan</p>
                                    <p class="text-white font-bold text-base uppercase">{{ $log->nopol }}</p>
                                </div>
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-bold uppercase @if($log->keterangan == 'Menginap') bg-red-500 text-white @else bg-blue-100 text-blue-700 @endif">
                                    {{ $log->keterangan }}
                                </span>
                            </div>

                            <div class="p-4 space-y-3">
                                <div class="flex items-center gap-3 pb-3 border-b border-gray-100">
                                    <div
                                        class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-[10px] text-gray-500 font-semibold uppercase">Pemilik</p>
                                        <p class="text-gray-800 font-bold text-base">{{ $log->pemilik }}</p>
                                    </div>
                                    <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
                                        {{ $log->tipe }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 gap-3 text-sm">
                                    <div>
                                        <p class="text-gray-500 text-xs uppercase mb-1">Lama Parkir</p>
                                        <p class="text-gray-800 font-semibold">
                                            {{ $log->waktu_masuk->diffForHumans($log->waktu_keluar, true) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500 text-xs uppercase mb-1">Waktu Keluar</p>
                                        <p class="text-gray-800 font-semibold">{{ $log->waktu_keluar->format('H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white rounded-xl shadow-md p-8 text-center">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <p class="text-gray-500 font-semibold">Tidak ada riwayat kendaraan pada tanggal ini.</p>
                        </div>
                    @endforelse
                </div>


            </div>
        </div>


        {{-- 3. TOMBOL FAB (CREATE) --}}
        <button @click.prevent="showCreateModal = true"
            class="fixed bottom-24 right-4 bg-[#2a4a6f] text-white rounded-full w-16 h-16 flex items-center justify-center shadow-lg transform hover:scale-110 transition-transform z-40 cursor-pointer">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
        </button>

        {{-- 4. MODAL CREATE KENDARAAN --}}
        <div x-show="showCreateModal" class="relative z-50" style="display: none;">
            <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black bg-opacity-75 transition-opacity"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div x-show="showCreateModal" @click.away="showCreateModal = false"
                        class="relative transform overflow-visible rounded-xl bg-[#2a4a6f] text-left shadow-xl transition-all sm:my-8 w-full max-w-md p-6"
                        x-data="{
                                        nopol: '',
                                        pemilik: '',
                                        tipe: 'Roda 2',
                                        suggestions: [],
                                        loading: false,
                                        isRegistered: false,

                                        selectSuggestion(suggestion) {
                                            this.nopol = suggestion.nomor_plat;
                                            this.pemilik = suggestion.pemilik;
                                            this.tipe = suggestion.tipe;
                                            this.isRegistered = true;
                                            this.suggestions = []; 
                                        },

                                        async getSuggestions() {
                                            this.isRegistered = false;

                                            if (this.nopol.length < 3) {
                                                this.suggestions = [];
                                                if(this.nopol.length === 0) { 
                                                    this.pemilik = ''; 
                                                    this.tipe = 'Roda 2'; 
                                                }
                                                return;
                                            }

                                            this.loading = true;
                                            try {
                                                const response = await fetch(`{{ route('anggota.kendaraan.searchNopol') }}?search=${this.nopol}`);
                                                const data = await response.json();
                                                this.suggestions = data;

                                                const exactMatch = data.find(s => s.nomor_plat.toLowerCase() === this.nopol.toLowerCase());
                                                if (exactMatch) {
                                                    this.pemilik = exactMatch.pemilik;
                                                    this.tipe = exactMatch.tipe;
                                                    this.isRegistered = true;
                                                }
                                            } catch (error) {
                                                console.error(error);
                                            } finally {
                                                this.loading = false;
                                            }
                                        }
                                     }">

                        <div class="flex justify-end mb-4">
                            <button @click="showCreateModal = false"
                                class="text-gray-300 hover:text-white transition-colors focus:outline-none">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <form action="{{ route('anggota.kendaraan.store') }}" method="POST"
                            @click.outside="suggestions = []">
                            @csrf

                            <div class="grid grid-cols-3 gap-x-4 gap-y-5">
                                <label class="col-span-1 text-gray-300 font-semibold text-sm self-center">PLAT NOMOR
                                    :</label>
                                <div class="col-span-2 relative">
                                    <input type="text" name="nopol" placeholder="AB 1234 XY" required autocomplete="off"
                                        x-model="nopol" @input.debounce.350ms="getSuggestions()"
                                        class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500 uppercase">

                                    <div x-show="suggestions.length > 0" x-transition
                                        class="absolute top-full left-0 right-0 bg-white border border-gray-300 rounded-md shadow-lg mt-1 z-50 max-h-48 overflow-y-auto">
                                        <template x-for="suggestion in suggestions" :key="suggestion.id_kendaraan">
                                            <div @click="selectSuggestion(suggestion)"
                                                class="px-4 py-2 text-gray-800 hover:bg-blue-100 cursor-pointer text-sm font-semibold">
                                                <span x-text="suggestion.nomor_plat"></span> - <span
                                                    x-text="suggestion.pemilik" class="font-normal text-gray-600"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <label class="col-span-1 text-gray-300 font-semibold text-sm self-center">PEMILIK :</label>
                                <div class="col-span-2">
                                    <input type="text" name="pemilik" placeholder="Nama Pemilik" required x-model="pemilik"
                                        :readonly="isRegistered"
                                        :class="isRegistered ? 'bg-gray-300 text-gray-600 cursor-not-allowed' : 'bg-white text-gray-900'"
                                        class="w-full px-4 py-2 rounded-md border-none focus:ring-2 focus:ring-blue-500 transition-colors">
                                </div>

                                <label class="col-span-1 text-gray-300 font-semibold text-sm self-center">TIPE :</label>
                                <div class="col-span-2">
                                    <div class="relative">
                                        <select name="tipe" x-model="tipe" required
                                            :class="isRegistered ? 'bg-gray-300 text-gray-600 pointer-events-none' : 'bg-white text-gray-900'"
                                            class="w-full px-4 py-2 rounded-md border-none focus:ring-2 focus:ring-blue-500 transition-colors">
                                            <option value="Roda 2">Roda 2</option>
                                            <option value="Roda 4">Roda 4</option>
                                        </select>
                                        <input type="hidden" name="tipe" x-model="tipe" x-if="isRegistered">
                                    </div>
                                </div>

                                <label class="col-span-1 text-gray-300 font-semibold text-sm self-center">KETERANGAN
                                    :</label>
                                <div class="col-span-2">
                                    <select name="keterangan" required
                                        class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500">
                                        <option value="Tidak Menginap">Tidak Menginap</option>
                                        <option value="Menginap">Menginap</option>
                                    </select>
                                </div>

                                <label class="col-span-1 text-gray-300 font-semibold text-sm self-center">TANGGAL :</label>
                                <div class="col-span-2">
                                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required
                                        class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500">
                                </div>

                                <label class="col-span-1 text-gray-300 font-semibold text-sm self-center">WAKTU :</label>
                                <div class="col-span-2">
                                    <input type="time" name="waktu" value="{{ date('H:i') }}" required
                                        class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>

                            <div class="mt-8">
                                <button type="submit"
                                    class="w-full bg-green-600 text-white font-bold py-3 px-6 rounded-lg shadow-md hover:bg-green-700 transition-colors duration-300">
                                    SUBMIT
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- 5. MODAL KONFIRMASI KELUAR --}}
        <div x-show="modalCheckoutOpen" class="relative z-50" style="display: none;">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="modalCheckoutOpen = false"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center">
                    <div
                        class="relative transform overflow-hidden rounded-xl bg-[#2a4a6f] text-left shadow-xl transition-all sm:my-8 w-full max-w-sm">
                        <form :action="`/anggota/kendaraan/checkout/${selectedVehicleId}`" method="POST">
                            @csrf @method('PUT')
                            <input type="hidden" name="menginap" :value="selectedVehicleStatus === 'Menginap' ? '1' : '0'">

                            <div class="p-6 flex flex-col items-center text-white">
                                <h3 class="text-xl font-bold mb-2 uppercase">KONFIRMASI KELUAR</h3>

                                <div class="bg-white/10 rounded-lg p-4 w-full text-center mb-6 border border-white/20">
                                    <p class="text-sm text-gray-300 mb-1">Plat Nomor</p>
                                    <p class="text-2xl font-bold uppercase tracking-wider mb-3"
                                        x-text="selectedVehicleNopol"></p>

                                    <p class="text-sm text-gray-300 mb-1">Status</p>
                                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide"
                                        :class="selectedVehicleStatus === 'Menginap' ? 'bg-red-500 text-white' : 'bg-blue-500 text-white'"
                                        x-text="selectedVehicleStatus">
                                    </span>
                                </div>

                                <div class="w-full space-y-3">
                                    <button type="submit"
                                        class="w-full py-3 px-4 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg shadow-lg transition-colors duration-200 uppercase">
                                        YA, KENDARAAN KELUAR
                                    </button>

                                    <button type="button" @click="modalCheckoutOpen = false"
                                        class="w-full py-3 px-4 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg shadow-lg transition-colors duration-200 uppercase">
                                        Batal
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection