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
        .animate-timer {
            animation: countdown 5s linear forwards;
        }
    </style>

<div class="w-full mx-auto"
     x-data="{ 
        showSuccessNotif: {{ session('success') ? 'true' : 'false' }},
        showErrorNotif: {{ session('error') ? 'true' : 'false' }} 
     }">
    
    <h2 class="text-2xl font-bold text-slate-800 mb-4">Laporan Kendaraan</h2>

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

    {{-- TABEL 1: RIWAYAT KELUAR/MASUK --}}
    <div id="riwayat-container" class="transition-opacity duration-200" x-data="{ showRiwayat: true }">
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] p-3 border-b border-[#2a4a6f] cursor-pointer hover:bg-[#2a4a6f] transition" @click="showRiwayat = !showRiwayat">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2v0a2 2 0 00-2 2v0M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                        </svg>
                        <h3 class="font-bold text-white">RIWAYAT KELUAR/MASUK</h3>
                    </div>
                    <svg class="w-5 h-5 text-white transition-transform" :class="{ 'rotate-180': !showRiwayat }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>

            <div x-show="showRiwayat" x-collapse>

            {{-- Form Filter & Search --}}
            <form id="filterForm" action="{{ route('supervisor.kendaraan.index') }}" method="GET" x-data="{}">
                <div class="px-6 py-5 border-b border-gray-200">
                    <div class="flex flex-wrap gap-4">
                        
                        {{-- Show Entries --}}
                        <div class="w-[calc(50%-0.5rem)] md:w-auto">
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Show</label>
                            <div class="flex items-center gap-2">
                                <div class="relative">
                                    <select name="per_page_riwayat" class="block h-[42px] pl-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer appearance-none">
                                        <option value="5" {{ $perPageRiwayat == 5 ? 'selected' : '' }}>5</option>
                                        <option value="10" {{ $perPageRiwayat == 10 ? 'selected' : '' }}>10</option>
                                        <option value="25" {{ $perPageRiwayat == 25 ? 'selected' : '' }}>25</option>
                                        <option value="50" {{ $perPageRiwayat == 50 ? 'selected' : '' }}>50</option>
                                        <option value="100" {{ $perPageRiwayat == 100 ? 'selected' : '' }}>100</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </div>
                                <span class="text-sm text-gray-600 whitespace-nowrap">rows</span>
                            </div>
                        </div>
                        
                        {{-- Filter Tanggal --}}
                        <div class="w-[calc(50%-0.5rem)] md:flex-1">
                            <label for="tanggal" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                                Tanggal
                            </label>
                            <div class="cursor-pointer" @click="$refs.dateInput.showPicker()">
                                <input type="date" id="tanggal" name="tanggal" x-ref="dateInput"
                                       class="block w-full h-[42px] px-4 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer"
                                       value="{{ $tanggalTerpilih }}">
                            </div>
                        </div>

                        {{-- Filter Tipe --}}
                        <div class="w-[calc(50%-0.5rem)] md:flex-1">
                            <label for="tipe" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                                Tipe
                            </label>
                            <div class="relative">
                                <select id="tipe" name="tipe" 
                                        class="block w-full h-[42px] px-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer appearance-none">
                                    <option value="">Semua Tipe</option>
                                    <option value="Roda 2" {{ $tipeTerpilih == 'Roda 2' ? 'selected' : '' }}>Roda 2</option>
                                    <option value="Roda 4" {{ $tipeTerpilih == 'Roda 4' ? 'selected' : '' }}>Roda 4</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Live Search Input --}}
                        <div class="w-[calc(50%-0.5rem)] md:flex-1">
                            <label for="searchInput" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                                Cari Kendaraan
                            </label>
                            <input type="text" id="searchInput" name="search" 
                                   class="block w-full h-[42px] px-4 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm placeholder-gray-400" 
                                   value="{{ $search ?? '' }}" 
                                   placeholder="Ketik untuk mencari...">
                        </div>
                    </div>
                </div>
            </form>
            
            <div id="riwayat-table-container">
                @include('supervisor.partials.riwayat-table', ['riwayat' => $riwayat, 'tanggalFilter' => $tanggalTerpilih, 'registeredPlates' => $registeredPlates])
            </div>

            <div id="riwayat-pagination-container">
                @include('supervisor.partials.riwayat-pagination', ['riwayat' => $riwayat])
            </div>


            </div>
        </div>
    </div>

    {{-- TABEL 2: KENDARAAN YANG TERDAFTAR --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6" x-data="{
        showMaster: true,
        masterTipe: '',
        masterSearch: '',
        filterMaster() {
            const rows = document.querySelectorAll('.master-row');
            rows.forEach(row => {
                const tipe = row.dataset.tipe;
                const text = row.dataset.searchtext.toLowerCase();
                const tipeMatch = !this.masterTipe || tipe === this.masterTipe;
                const searchMatch = !this.masterSearch || text.includes(this.masterSearch.toLowerCase());
                row.style.display = (tipeMatch && searchMatch) ? '' : 'none';
            });
            
            const cards = document.querySelectorAll('.master-card');
            cards.forEach(card => {
                const tipe = card.dataset.tipe;
                const text = card.dataset.searchtext.toLowerCase();
                const tipeMatch = !this.masterTipe || tipe === this.masterTipe;
                const searchMatch = !this.masterSearch || text.includes(this.masterSearch.toLowerCase());
                card.style.display = (tipeMatch && searchMatch) ? '' : 'none';
            });
        }
    }">
        <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] p-3 border-b border-[#2a4a6f] cursor-pointer hover:bg-[#2a4a6f] transition" @click="showMaster = !showMaster">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="font-bold text-white">KENDARAAN YANG TERDAFTAR</h3>
                </div>
                <svg class="w-5 h-5 text-white transition-transform" :class="{ 'rotate-180': !showMaster }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
        </div>

        <div x-show="showMaster" x-collapse>

        {{-- Filter untuk Kendaraan Terdaftar --}}
        <form id="filterFormMaster" action="{{ route('supervisor.kendaraan.index') }}" method="GET">
            @foreach(request()->except(['per_page_master', 'page_master', 'tipe_master', 'search_master']) as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach
            <div class="px-6 py-5 border-b border-gray-200">
                <div class="flex flex-wrap gap-4">
                    
                    {{-- Show Entries --}}
                    <div class="w-[calc(50%-0.5rem)] md:w-auto">
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Show</label>
                        <div class="flex items-center gap-2">
                            <div class="relative">
                                <select name="per_page_master" class="block h-[42px] pl-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer appearance-none">
                                    <option value="5" {{ $perPageMaster == 5 ? 'selected' : '' }}>5</option>
                                    <option value="10" {{ $perPageMaster == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ $perPageMaster == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ $perPageMaster == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ $perPageMaster == 100 ? 'selected' : '' }}>100</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                            <span class="text-sm text-gray-600 whitespace-nowrap">rows</span>
                        </div>
                    </div>
                    
                    {{-- Filter Tipe --}}
                    <div class="w-[calc(50%-0.5rem)] md:flex-1">
                        <label for="tipe_master" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                            Tipe
                        </label>
                        <div class="relative">
                            <select id="tipe_master" name="tipe_master" 
                                    class="block w-full h-[42px] px-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer appearance-none">
                                <option value="">Semua Tipe</option>
                                <option value="Roda 2" {{ $tipeMaster == 'Roda 2' ? 'selected' : '' }}>Roda 2</option>
                                <option value="Roda 4" {{ $tipeMaster == 'Roda 4' ? 'selected' : '' }}>Roda 4</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Search Input --}}
                    <div class="w-[calc(50%-0.5rem)] md:flex-1">
                        <label for="searchMasterInput" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                            Cari Kendaraan
                        </label>
                        <input type="text" id="searchMasterInput" name="search_master" 
                               class="block w-full h-[42px] px-4 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm placeholder-gray-400" 
                               value="{{ $searchMaster ?? '' }}" 
                               placeholder="Ketik untuk mencari...">
                    </div>
                </div>
            </div>
        </form>
        
        <div id="master-table-container">
            @include('supervisor.partials.master-table', ['kendaraanMaster' => $kendaraanMaster])
        </div>

        <div id="master-pagination-container">
            @include('supervisor.partials.master-pagination', ['kendaraanMaster' => $kendaraanMaster])
        </div>


        </div>
    </div>



    {{-- Loading Indicator --}}
    <div id="loading-indicator" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-30">
        <div class="bg-white p-4 rounded-lg shadow-xl flex items-center gap-3">
            <div class="animate-spin rounded-full h-8 w-8 border-4 border-[#1e3a5f] border-t-transparent"></div>
            <span class="font-bold text-[#1e3a5f]">Memuat Data...</span>
        </div>
    </div>

</div>

{{-- SCRIPT AJAX LIVE SEARCH --}}
<script>
let searchTimeout;
const searchInput = document.getElementById('searchInput');
if (searchInput) {
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            loadRiwayatData();
        }, 500);
    });
}

let searchMasterTimeout;
const searchMasterInput = document.getElementById('searchMasterInput');
if (searchMasterInput) {
    searchMasterInput.addEventListener('input', function() {
        clearTimeout(searchMasterTimeout);
        searchMasterTimeout = setTimeout(() => {
            loadMasterData();
        }, 300);
    });
}

function loadRiwayatData(page = 1) {
    toggleLoading(true);

    const formData = new FormData(document.getElementById('filterForm'));
    formData.append('page_riwayat', page);
    
    const params = new URLSearchParams();
    for (let [key, value] of formData) {
        params.append(key, value);
    }
    
    fetch('{{ route("supervisor.kendaraan.searchRiwayat") }}?' + params.toString())
        .then(response => response.json())
        .then(data => {
            document.getElementById('riwayat-table-container').innerHTML = data.html;
            document.getElementById('riwayat-pagination-container').innerHTML = data.pagination;
        })
        .catch(error => console.error('Error:', error))
        .finally(() => {
            toggleLoading(false);
        });
}

function loadMasterData(page = 1) {
    toggleLoading(true);

    const formData = new FormData(document.getElementById('filterFormMaster'));
    formData.append('page_master', page);
    
    const params = new URLSearchParams();
    for (let [key, value] of formData) {
        params.append(key, value);
    }
    
    fetch('{{ route("supervisor.kendaraan.searchMaster") }}?' + params.toString())
        .then(response => response.json())
        .then(data => {
            document.getElementById('master-table-container').innerHTML = data.html;
            document.getElementById('master-pagination-container').innerHTML = data.pagination;
        })
        .catch(error => console.error('Error:', error))
        .finally(() => {
            toggleLoading(false);
        });
}

function toggleLoading(show) {
    const loader = document.getElementById('loading-indicator');
    if (loader) loader.style.display = show ? 'flex' : 'none';
}

function loadRiwayatPage(page) {
    loadRiwayatData(page);
}

function loadMasterPage(page) {
    loadMasterData(page);
}

document.getElementById('tanggal').addEventListener('change', loadRiwayatData);
document.getElementById('tipe').addEventListener('change', loadRiwayatData);
document.querySelector('select[name="per_page_riwayat"]').addEventListener('change', loadRiwayatData);

document.getElementById('tipe_master').addEventListener('change', loadMasterData);
document.querySelector('select[name="per_page_master"]').addEventListener('change', loadMasterData);
</script>
@endsection
