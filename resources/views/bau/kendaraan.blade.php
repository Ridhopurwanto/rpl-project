@extends('layouts.app')

@section('header-left')
    <a class="flex items-center border-2 border-[#1a2847] text-[#1a2847] text-sm font-bold px-4 py-2 rounded-full">
        KENDARAAN
    </a>
@endsection

@section('content')
<div class="w-full mx-auto">
    
    <h2 class="text-2xl font-bold text-slate-800 mb-4">Laporan Kendaraan</h2>

    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            {{ session('error') }}
        </div>
    @endif

    {{-- TABEL 1: RIWAYAT KELUAR/MASUK --}}
    <div id="riwayat-container" class="transition-opacity duration-200" x-data="{ showRiwayat: true }">
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="bg-gray-100 p-3 border-b border-gray-200 cursor-pointer hover:bg-gray-200 transition" @click="showRiwayat = !showRiwayat">
                <div class="flex justify-between items-center">
                    <h3 class="font-bold text-gray-800">RIWAYAT KELUAR/MASUK</h3>
                    <svg class="w-5 h-5 text-gray-600 transition-transform" :class="{ 'rotate-180': !showRiwayat }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>

            <div x-show="showRiwayat" x-collapse>

            {{-- Form Filter & Search --}}
            <form id="filterForm" action="{{ route('bau.kendaraan.index') }}" method="GET" x-data="{}">
                <div class="px-6 py-5 border-b border-gray-200">
                    <div class="flex flex-wrap gap-4">
                        
                        {{-- Show Entries --}}
                        <div class="w-[calc(50%-0.5rem)] md:w-auto">
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Show</label>
                            <div class="flex items-center gap-2">
                                <select name="per_page_riwayat" class="block h-[42px] pl-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer appearance-none" style="background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27currentColor%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3e%3cpolyline points=%276 9 12 15 18 9%27%3e%3c/polyline%3e%3c/svg%3e'); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 1.25em 1.25em;">
                                    <option value="5" {{ $perPageRiwayat == 5 ? 'selected' : '' }}>5</option>
                                    <option value="10" {{ $perPageRiwayat == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ $perPageRiwayat == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ $perPageRiwayat == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ $perPageRiwayat == 100 ? 'selected' : '' }}>100</option>
                                </select>
                                <span class="text-sm text-gray-600 whitespace-nowrap">rows</span>
                            </div>
                        </div>
                        
                        {{-- Filter Tanggal --}}
                        <div class="w-[calc(50%-0.5rem)] md:flex-1">
                            <label for="tanggal" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                                Tanggal
                            </label>
                            <input type="date" id="tanggal" name="tanggal"
                                   class="block w-full h-[42px] px-4 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer"
                                   value="{{ $tanggalTerpilih }}">
                        </div>

                        {{-- Filter Tipe --}}
                        <div class="w-[calc(50%-0.5rem)] md:flex-1">
                            <label for="tipe" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                                Tipe
                            </label>
                            <select id="tipe" name="tipe" 
                                    class="block w-full h-[42px] px-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer appearance-none" style="background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27currentColor%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3e%3cpolyline points=%276 9 12 15 18 9%27%3e%3c/polyline%3e%3c/svg%3e'); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 1.25em 1.25em;">
                                <option value="">Semua Tipe</option>
                                <option value="Roda 2" {{ $tipeTerpilih == 'Roda 2' ? 'selected' : '' }}>Roda 2</option>
                                <option value="Roda 4" {{ $tipeTerpilih == 'Roda 4' ? 'selected' : '' }}>Roda 4</option>
                            </select>
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
                @include('bau.partials.riwayat-table', ['riwayat' => $riwayat, 'tanggalFilter' => $tanggalTerpilih])
            </div>

            <div id="riwayat-pagination-container">
                @include('bau.partials.riwayat-pagination', ['riwayat' => $riwayat])
            </div>
            </div>
        </div>
    </div>

    {{-- TABEL 2: KENDARAAN YANG TERDAFTAR --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6" x-data="{ showMaster: true }">
        <div class="bg-gray-100 p-3 border-b border-gray-200 cursor-pointer hover:bg-gray-200 transition" @click="showMaster = !showMaster">
            <div class="flex justify-between items-center">
                <h3 class="font-bold text-gray-800">KENDARAAN YANG TERDAFTAR</h3>
                <svg class="w-5 h-5 text-gray-600 transition-transform" :class="{ 'rotate-180': !showMaster }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
        </div>

        <div x-show="showMaster" x-collapse>

        <form id="filterFormMaster" action="{{ route('bau.kendaraan.index') }}" method="GET">
            @foreach(request()->except(['per_page_master', 'page_master', 'tipe_master', 'search_master']) as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach
            <div class="px-6 py-5 border-b border-gray-200">
                <div class="flex flex-wrap gap-4">
                    
                    <div class="w-[calc(50%-0.5rem)] md:w-auto">
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Show</label>
                        <div class="flex items-center gap-2">
                            <select name="per_page_master" class="block h-[42px] pl-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer appearance-none" style="background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27currentColor%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3e%3cpolyline points=%276 9 12 15 18 9%27%3e%3c/polyline%3e%3c/svg%3e'); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 1.25em 1.25em;">
                                <option value="5" {{ $perPageMaster == 5 ? 'selected' : '' }}>5</option>
                                <option value="10" {{ $perPageMaster == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ $perPageMaster == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ $perPageMaster == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ $perPageMaster == 100 ? 'selected' : '' }}>100</option>
                            </select>
                            <span class="text-sm text-gray-600 whitespace-nowrap">rows</span>
                        </div>
                    </div>

                    {{-- Filter Tipe --}}
                    <div class="w-[calc(50%-0.5rem)] md:flex-1">
                        <label for="tipe_master" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                            Tipe
                        </label>
                        <select id="tipe_master" name="tipe_master" 
                                class="block w-full h-[42px] px-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer appearance-none" style="background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27currentColor%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3e%3cpolyline points=%276 9 12 15 18 9%27%3e%3c/polyline%3e%3c/svg%3e'); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 1.25em 1.25em;">
                            <option value="">Semua Tipe</option>
                            <option value="Roda 2" {{ $tipeMaster == 'Roda 2' ? 'selected' : '' }}>Roda 2</option>
                            <option value="Roda 4" {{ $tipeMaster == 'Roda 4' ? 'selected' : '' }}>Roda 4</option>
                        </select>
                    </div>

                    {{-- Live Search Input --}}
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
            @include('bau.partials.master-table', ['kendaraanMaster' => $kendaraanMaster])
        </div>

        <div id="master-pagination-container">
            @include('bau.partials.master-pagination', ['kendaraanMaster' => $kendaraanMaster])
        </div>
        </div>
    </div>

</div>

<script>
// AJAX Live Search untuk Riwayat
let searchTimeout;
const searchInput = document.getElementById('searchInput');
if (searchInput) {
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            loadRiwayatData();
        }, 300);
    });
}

// AJAX Live Search untuk Master
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

// Function untuk load data riwayat via AJAX
function loadRiwayatData(page = 1) {
    const formData = new FormData(document.getElementById('filterForm'));
    formData.append('page_riwayat', page);
    
    const params = new URLSearchParams();
    for (let [key, value] of formData) {
        params.append(key, value);
    }
    
    fetch('{{ route("bau.kendaraan.searchRiwayat") }}?' + params.toString())
        .then(response => response.json())
        .then(data => {
            document.getElementById('riwayat-table-container').innerHTML = data.html;
            document.getElementById('riwayat-pagination-container').innerHTML = data.pagination;
        })
        .catch(error => console.error('Error:', error));
}

// Function untuk load data master via AJAX
function loadMasterData(page = 1) {
    const formData = new FormData(document.getElementById('filterFormMaster'));
    formData.append('page_master', page);
    
    const params = new URLSearchParams();
    for (let [key, value] of formData) {
        params.append(key, value);
    }
    
    fetch('{{ route("bau.kendaraan.searchMaster") }}?' + params.toString())
        .then(response => response.json())
        .then(data => {
            document.getElementById('master-table-container').innerHTML = data.html;
            document.getElementById('master-pagination-container').innerHTML = data.pagination;
        })
        .catch(error => console.error('Error:', error));
}

// Function untuk pagination riwayat
function loadRiwayatPage(page) {
    loadRiwayatData(page);
}

// Function untuk pagination master
function loadMasterPage(page) {
    loadMasterData(page);
}

// Event listener untuk filter changes (tanggal, tipe)
document.getElementById('tanggal').addEventListener('change', loadRiwayatData);
document.getElementById('tipe').addEventListener('change', loadRiwayatData);
document.querySelector('select[name="per_page_riwayat"]').addEventListener('change', loadRiwayatData);

document.getElementById('tipe_master').addEventListener('change', loadMasterData);
document.querySelector('select[name="per_page_master"]').addEventListener('change', loadMasterData);
</script>
@endsection
