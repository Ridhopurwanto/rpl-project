@extends('layouts.app')

@section('header-left')
    <a class="flex items-center border-2 border-[#1a2847] text-[#1a2847] text-sm font-bold px-4 py-2 rounded-full">
        KENDARAAN
    </a>
@endsection

@section('content')
    {{-- Style untuk animasi timer notifikasi --}}
    <style>
        @keyframes countdown {
            from { stroke-dashoffset: 0; }
            to { stroke-dashoffset: 100; }
        }
        .timer-circle {
            fill: none;
            stroke-width: 3;
            stroke-linecap: round;
            stroke-dasharray: 100;
            stroke-dashoffset: 0;
            transform: rotate(-90deg);
            transform-origin: center;
        }
        /* Animasi berjalan 5 detik sesuai timeout javascript */
        .animate-timer {
            animation: countdown 5s linear forwards;
        }
    </style>

    <div class="w-full min-h-screen bg-slate-100 p-4 pb-32" x-data="{ 
                        modalCheckoutOpen: false, 
                        selectedVehicleId: null,
                        selectedVehicleNopol: '',
                        selectedVehicleStatus: '',
                        showCreateModal: false,
                        showSuccessNotif: {{ session('success') ? 'true' : 'false' }},
                        showErrorNotif: {{ session('error') ? 'true' : 'false' }}
                     }">

        {{-- Floating Notification Success --}}
        <div x-show="showSuccessNotif" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-x-full"
             x-transition:enter-end="opacity-100 transform translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-x-0"
             x-transition:leave-end="opacity-0 transform translate-x-full"
             x-init="if(showSuccessNotif) setTimeout(() => showSuccessNotif = false, 5000)"
             class="fixed top-4 right-4 z-50 bg-green-500 text-white pl-6 pr-2 py-1 rounded-lg shadow-2xl flex items-center gap-3 min-w-[300px] max-w-md"
             style="display: none;">
            <div class="flex-shrink-0">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="flex-1">
                <p class="font-semibold text-sm">{{ session('success') }}</p>
            </div>
            {{-- Tombol Close dengan Timer Circle --}}
            <button @click="showSuccessNotif = false" class="relative flex-shrink-0 text-white hover:text-green-100 transition-colors w-10 h-10 flex items-center justify-center">
                {{-- SVG Timer Circle --}}
                <svg class="absolute inset-0 w-full h-full p-1" viewBox="0 0 36 36">
                     <path class="text-green-700/40 timer-circle" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" style="stroke-dasharray: 100; stroke-dashoffset: 0;"></path>
                     <path class="text-white timer-circle animate-timer" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" stroke="currentColor"></path>
                </svg>
                {{-- Icon X --}}
                <svg class="w-4 h-4 relative z-10" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>

        {{-- Floating Notification Error --}}
        <div x-show="showErrorNotif" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-x-full"
             x-transition:enter-end="opacity-100 transform translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-x-0"
             x-transition:leave-end="opacity-0 transform translate-x-full"
             x-init="if(showErrorNotif) setTimeout(() => showErrorNotif = false, 5000)"
             class="fixed top-4 right-4 z-50 bg-red-500 text-white pl-6 pr-2 py-1 rounded-lg shadow-2xl flex items-center gap-3 min-w-[300px] max-w-md"
             style="display: none;">
            <div class="flex-shrink-0">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="flex-1">
                <p class="font-semibold text-sm">{{ session('error') }}</p>
            </div>
            {{-- Tombol Close dengan Timer Circle --}}
            <button @click="showErrorNotif = false" class="relative flex-shrink-0 text-white hover:text-red-100 transition-colors w-10 h-10 flex items-center justify-center">
                {{-- SVG Timer Circle --}}
                <svg class="absolute inset-0 w-full h-full p-1" viewBox="0 0 36 36">
                     <path class="text-red-800/40 timer-circle" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" style="stroke-dasharray: 100; stroke-dashoffset: 0;"></path>
                     <path class="text-white timer-circle animate-timer" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" stroke="currentColor"></path>
                </svg>
                <svg class="w-4 h-4 relative z-10" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>

        {{-- 1. BAGIAN KENDARAAN AKTIF --}}
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6" x-data="{ isOpen: true }">
            <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] p-3 border-b border-gray-200 cursor-pointer hover:opacity-90 transition" @click="isOpen = !isOpen">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-white mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2v0a2 2 0 00-2 2v0M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                        </svg>
                        <h3 class="font-bold text-white">KENDARAAN (DI DALAM)</h3>
                    </div>
                    <svg class="w-5 h-5 text-white transition-transform" :class="{ 'rotate-180': !isOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>

            <div x-show="isOpen" x-collapse>

                <div class="p-3 space-y-3">

                @forelse($kendaraan_aktif as $log)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden border-2 border-gray-300 relative">
                        {{-- Badge Tipe di Pojok Kanan Atas --}}
                        <div class="absolute top-2 right-2 z-10">
                            <span class="inline-block @if($log->tipe == 'Roda 4') bg-blue-500 @else bg-yellow-500 @endif text-white text-[10px] font-bold px-2 py-1 rounded-full shadow-md">{{ $log->tipe }}</span>
                        </div>
                        
                        <div class="p-3">
                            <div class="w-full">
                                {{-- Nomor Plat --}}
                                <h4 class="font-bold text-gray-800 text-sm mb-1 uppercase">{{ $log->nopol }}</h4>

                                {{-- Info Pemilik & Waktu --}}
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-blue-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                        </svg>
                                        <p class="text-gray-700 font-semibold text-xs">{{ $log->pemilik }}</p>
                                    </div>
                                    
                                    <div class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <p class="text-gray-700 font-semibold text-xs">{{ $log->waktu_masuk->format('H:i') }}</p>
                                    </div>
                                </div>

                                {{-- Keterangan & Tombol --}}
                                <div class="flex gap-1.5">
                                    <form action="{{ route('anggota.kendaraan.updateKeterangan', ['id_kendaraan_log' => $log->id_log]) }}" method="POST" class="flex-1">
                                        @csrf @method('PUT')
                                        <select name="keterangan" onchange="this.form.submit()" class="w-full border border-gray-300 rounded text-xs py-1 focus:border-blue-500 focus:ring-blue-500">
                                            <option value="Tidak Menginap" @if($log->keterangan == 'Tidak Menginap') selected @endif>Tidak Menginap</option>
                                            <option value="Menginap" @if($log->keterangan == 'Menginap') selected @endif>Menginap</option>
                                        </select>
                                    </form>
                                    <button @click.prevent="modalCheckoutOpen = true; selectedVehicleId = '{{ $log->id_log }}'; selectedVehicleNopol = '{{ $log->nopol }}'; selectedVehicleStatus = '{{ $log->keterangan }}';" class="bg-red-500 text-white font-bold py-1.5 px-3 rounded text-xs hover:bg-red-600 transition flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                        </svg>
                                        Keluar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-xl shadow-md p-8 text-center border-2 border-gray-300">
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
        </div>

        {{-- 2. BAGIAN RIWAYAT KENDARAAN (HYBRID: SUGGESTION + LIVE SEARCH) --}}
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6" x-data="{ isOpen: true }">
            <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] p-3 border-b border-gray-200 cursor-pointer hover:opacity-90 transition" @click="isOpen = !isOpen">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-white mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="font-bold text-white">RIWAYAT</h3>
                    </div>
                    <svg class="w-5 h-5 text-white transition-transform" :class="{ 'rotate-180': !isOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>

            <div x-show="isOpen" x-collapse>

                {{-- Filter dengan HYBRID: Suggestion + Live Search --}}
                <div class="p-4 border-b border-gray-200" x-data="{}">
                    <form action="{{ route('anggota.kendaraan.index') }}" method="GET" id="searchForm">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                            {{-- Filter Tanggal --}}
                            <div class="w-full">
                                <label for="tanggal" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                                    Tanggal
                                </label>
                                <div class="cursor-pointer" @click="$refs.dateInput.showPicker()">
                                    <input type="date" id="tanggal" name="tanggal" x-ref="dateInput" value="{{ $tanggal_terpilih }}"
                                        onchange="document.getElementById('searchForm').submit()"
                                        max="{{ date('Y-m-d') }}"
                                        class="block w-full h-[42px] px-4 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer">
                                </div>
                            </div>

                            {{-- Filter Keterangan (BARU) --}}
                            <div class="w-full">
                                <label for="keterangan" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                                    Keterangan
                                </label>
                                <div class="relative">
                                    <select id="keterangan" name="keterangan"
                                        onchange="document.getElementById('searchForm').submit()"
                                        class="block w-full h-[42px] px-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer appearance-none">
                                        <option value="" @if(empty($keterangan_filter)) selected @endif>Semua</option>
                                        <option value="Menginap" @if($keterangan_filter == 'Menginap') selected @endif>Menginap</option>
                                        <option value="Tidak Menginap" @if($keterangan_filter == 'Tidak Menginap') selected @endif>Tidak Menginap</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            {{-- Search Kendaraan: HYBRID AJAX (Suggestion + Live Search tanpa reload) --}}
                            <div class="w-full" x-data="{
                            nopolSearch: '{{ $nopol_filter ?? '' }}',
                            tanggalFilter: '{{ $tanggal_terpilih }}',
                            keteranganFilter: '{{ $keterangan_filter ?? '' }}',
                            suggestions: [],
                            loading: false,
                            showSuggestions: false,
                            liveSearchTimeout: null,
                            liveSearching: false,

                            async getSuggestions() {
                                // console.log('Mencari suggestion:', this.nopolSearch);

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
                                    // console.log('Suggestions:', data);
                                    this.suggestions = data;
                                } catch (error) {
                                    // console.error('Error:', error);
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
                                    if (this.nopolSearch.length === 0) {
                                        // console.log('Live Search triggered');
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
                                    url.searchParams.set('keterangan', this.keteranganFilter);
                                    window.history.pushState({}, '', url);

                                    // Fetch hasil riwayat via AJAX
                                    const response = await fetch(`{{ route('anggota.kendaraan.getRiwayat') }}?nopol=${encodeURIComponent(this.nopolSearch)}&tanggal=${this.tanggalFilter}&keterangan=${encodeURIComponent(this.keteranganFilter)}`);
                                    const html = await response.text();

                                    // Update container riwayat
                                    document.getElementById('riwayat-container').innerHTML = html;

                                    // console.log('Live search completed');
                                } catch (error) {
                                    // console.error('Live search error:', error);
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
                                        @input="triggerLiveSearch(); getSuggestions();"
                                        @focus="if(nopolSearch.length >= 1) getSuggestions()"
                                        @keydown.enter.prevent="triggerLiveSearchNow()"
                                        placeholder="Ketik untuk mencari..." autocomplete="off"
                                        class="block w-full h-[42px] px-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm placeholder-gray-400">

                                    {{-- Loading Indicator untuk Live Search --}}
                                    <div x-show="liveSearching" class="absolute right-3 top-1/2 -translate-y-1/2">
                                        <svg class="animate-spin h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                    </div>

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
                <div id="riwayat-container" class="p-3 space-y-3">
                    {{-- ▼▼▼ INITIAL DATA dari controller index() ▼▼▼ --}}
                    @forelse($riwayat_kendaraan as $log)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden border-2 border-gray-300 relative">
                            {{-- Badge Tipe di Pojok Kanan Atas --}}
                            <div class="absolute top-2 right-2 z-10">
                                <span class="inline-block @if($log->tipe == 'Roda 4') bg-blue-500 @else bg-yellow-500 @endif text-white text-[10px] font-bold px-2 py-1 rounded-full shadow-md">{{ $log->tipe }}</span>
                            </div>
                            
                            <div class="p-3">
                                <div class="w-full">
                                    {{-- Nomor Plat --}}
                                    <h4 class="font-bold text-gray-800 text-sm mb-1 uppercase">{{ $log->nopol }}</h4>
                                    
                                    {{-- Status Badge --}}
                                    <div class="mb-2">
                                        <span class="inline-block @if($log->keterangan == 'Menginap') bg-red-500 text-white @else bg-blue-500 text-white @endif text-[10px] font-bold px-2 py-1 rounded-full">{{ $log->keterangan }}</span>
                                    </div>

                                    {{-- Info Pemilik & Waktu --}}
                                    <div class="flex items-center gap-3 mb-2">
                                        <div class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-blue-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                            </svg>
                                            <p class="text-gray-700 font-semibold text-xs">{{ $log->pemilik }}</p>
                                        </div>
                                        
                                        <div class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                            </svg>
                                            <p class="text-gray-700 font-semibold text-xs">{{ $log->waktu_keluar->format('H:i') }}</p>
                                        </div>
                                    </div>

                                    {{-- Lama Parkir --}}
                                    <div class="text-xs text-gray-500">
                                        Lama parkir: <span class="font-semibold text-gray-700">{{ $log->waktu_masuk->diffForHumans($log->waktu_keluar, true) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white rounded-xl shadow-md p-8 text-center border-2 border-gray-300">
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

                            <div class="space-y-4">
                                {{-- PLAT NOMOR --}}
                                <div>
                                    <label for="nopol" class="text-gray-300 font-semibold text-sm block mb-2">
                                        PLAT NOMOR
                                    </label>
                                    <div class="relative">
                                        <input type="text" id="nopol" name="nopol" placeholder="AB 1234 XY" required autocomplete="off"
                                            x-model="nopol" @input.debounce.350ms="getSuggestions()"
                                            class="w-full h-[42px] px-4 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500 uppercase">

                                        <div x-show="suggestions.length > 0" x-transition
                                            class="absolute top-full left-0 right-0 bg-white border border-gray-300 rounded-md shadow-xl mt-1 z-[60] max-h-32 overflow-y-auto"
                                            @click.away="suggestions = []">
                                            <template x-for="suggestion in suggestions" :key="suggestion.id_kendaraan">
                                                <div @click="selectSuggestion(suggestion)"
                                                    class="px-3 py-2 text-gray-800 hover:bg-blue-50 cursor-pointer text-xs border-b border-gray-100 last:border-0 transition-colors">
                                                    <div class="font-semibold text-gray-900" x-text="suggestion.nomor_plat"></div>
                                                    <div class="text-gray-600 text-[10px]" x-text="suggestion.pemilik"></div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                {{-- PEMILIK --}}
                                <div>
                                    <label for="pemilik" class="text-gray-300 font-semibold text-sm block mb-2">
                                        PEMILIK
                                    </label>
                                    <input type="text" id="pemilik" name="pemilik" placeholder="Nama Pemilik" required x-model="pemilik"
                                        :readonly="isRegistered"
                                        :class="isRegistered ? 'bg-gray-300 text-gray-600 cursor-not-allowed' : 'bg-white text-gray-900'"
                                        class="w-full h-[42px] px-4 rounded-md border-none focus:ring-2 focus:ring-blue-500 transition-colors">
                                </div>

                                {{-- TIPE --}}
                                <div>
                                    <label for="tipe" class="text-gray-300 font-semibold text-sm block mb-2">
                                        TIPE
                                    </label>
                                    <div class="relative">
                                        <select id="tipe" name="tipe" x-model="tipe" required
                                            :class="isRegistered ? 'bg-gray-300 text-gray-600 pointer-events-none' : 'bg-white text-gray-900'"
                                            class="w-full h-[42px] px-4 pr-10 rounded-md border-none focus:ring-2 focus:ring-blue-500 transition-colors appearance-none">
                                            <option value="Roda 2">Roda 2</option>
                                            <option value="Roda 4">Roda 4</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                {{-- KETERANGAN --}}
                                <div>
                                    <label for="keterangan" class="text-gray-300 font-semibold text-sm block mb-2">
                                        KETERANGAN
                                    </label>
                                    <div class="relative">
                                        <select id="keterangan" name="keterangan" required
                                            class="w-full h-[42px] px-4 pr-10 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500 appearance-none">
                                            <option value="Tidak Menginap">Tidak Menginap</option>
                                            <option value="Menginap">Menginap</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                {{-- TANGGAL --}}
                                <div>
                                    <label for="tanggal" class="text-gray-300 font-semibold text-sm block mb-2">
                                        TANGGAL
                                    </label>
                                    <input type="date" id="tanggal" name="tanggal" value="{{ date('Y-m-d') }}" required
                                        class="w-full h-[42px] px-4 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500">
                                </div>

                                {{-- WAKTU --}}
                                <div>
                                    <label for="waktu" class="text-gray-300 font-semibold text-sm block mb-2">
                                        WAKTU
                                    </label>
                                    <input type="time" id="waktu" name="waktu" value="{{ date('H:i') }}" required
                                        class="w-full h-[42px] px-4 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500">
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