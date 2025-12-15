@extends('layouts.app')

@section('header-left')
    <a class="flex items-center border-2 border-[#1a2847] text-[#1a2847] text-sm font-bold px-4 py-2 rounded-full">
        KENDARAAN
    </a>
@endsection

@section('content')
<div class="w-full mx-auto"
     x-data="{ 
        showEditModal: false, editAction: '', editPlat: '', editPemilik: '', editTipe: '',
        showDeleteModal: false, deleteAction: '',
        showPromoteModal: false, promoteAction: ''
     }"
     @edit-master.window="
        editAction = `/komandan/kendaraan/master/${$event.detail.id}`;
        editPlat = $event.detail.plat;
        editPemilik = $event.detail.pemilik;
        editTipe = $event.detail.tipe;
        showEditModal = true;
     "
     @delete-master.window="
        deleteAction = `/komandan/kendaraan/master/${$event.detail.id}`;
        showDeleteModal = true;
     "
     @promote-master.window="
        promoteAction = `/komandan/kendaraan/log/${$event.detail.id}/promote`;
        showPromoteModal = true;
     ">
    
    <h2 class="text-2xl font-bold text-slate-800 mb-4">Laporan Kendaraan</h2>

    {{-- Toast Notification --}}
    @if (session('success'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 5000)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-full"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 translate-x-full"
             class="fixed top-4 right-4 z-50 max-w-sm w-full bg-white rounded-lg shadow-lg border-l-4 border-green-500 p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium text-gray-900">{{ session('success') }}</p>
                </div>
                <button @click="show = false" class="ml-4 flex-shrink-0 text-gray-400 hover:text-gray-500">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    @endif
    @if (session('error'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 5000)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-full"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 translate-x-full"
             class="fixed top-4 right-4 z-50 max-w-sm w-full bg-white rounded-lg shadow-lg border-l-4 border-red-500 p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium text-gray-900">{{ session('error') }}</p>
                </div>
                <button @click="show = false" class="ml-4 flex-shrink-0 text-gray-400 hover:text-gray-500">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    @endif

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
            <form id="filterForm" action="{{ route('komandan.kendaraan') }}" method="GET" x-data="{}">
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
                @include('komandan.partials.riwayat-table', ['riwayat' => $riwayat, 'tanggalFilter' => $tanggalTerpilih, 'registeredPlates' => $registeredPlates])
            </div>

            <div id="riwayat-pagination-container">
                @include('komandan.partials.riwayat-pagination', ['riwayat' => $riwayat])
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
        <form id="filterFormMaster" action="{{ route('komandan.kendaraan') }}" method="GET">
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
            @include('komandan.partials.master-table', ['kendaraanMaster' => $kendaraanMaster])
        </div>

        <div id="master-pagination-container">
            @include('komandan.partials.master-pagination', ['kendaraanMaster' => $kendaraanMaster])
        </div>


        </div>
    </div>

    {{-- Modal Edit Kendaraan --}}
    <div x-show="showEditModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
        @click.away="showEditModal = false"
        style="display: none;">
        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full relative overflow-hidden" @click.stop>
            {{-- Header Biru --}}
            <div class="bg-[#1e3a5f] py-4 px-6 border-b border-[#1e3a5f] flex justify-between items-center">
                <h3 class="text-lg font-bold text-white flex items-center tracking-wide">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                        </path>
                    </svg>
                    EDIT DATA KENDARAAN
                </h3>
                <button @click="showEditModal = false" class="text-white/70 hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
            
            <form :action="editAction" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body max-h-[70vh] overflow-y-auto p-6">
                    <div class="space-y-5">
                        
                        {{-- GROUP: Informasi Kendaraan --}}
                        <div class="p-4 bg-blue-50/50 rounded-xl border border-blue-100">
                            
                            <div class="space-y-4">
                                {{-- Nomor Plat --}}
                                <div>
                                    <label for="edit_nomor_plat" class="block text-xs font-bold text-[#1e3a5f] uppercase tracking-wide mb-1">Nomor Plat <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-[#1e3a5f]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                        </div>
                                        <input type="text" id="edit_nomor_plat" name="nomor_plat" x-model="editPlat" required placeholder="Contoh: B 1234 XYZ"
                                               class="pl-10 w-full bg-white border border-gray-300 text-gray-800 text-sm font-medium rounded-lg shadow-sm focus:ring-[#1e3a5f] focus:border-[#1e3a5f] block p-2.5">
                                    </div>
                                </div>
                                
                                {{-- Pemilik --}}
                                <div>
                                    <label for="edit_pemilik" class="block text-xs font-bold text-[#1e3a5f] uppercase tracking-wide mb-1">Pemilik <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-[#1e3a5f]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        </div>
                                        <input type="text" id="edit_pemilik" name="pemilik" x-model="editPemilik" required placeholder="Nama pemilik kendaraan"
                                               class="pl-10 w-full bg-white border border-gray-300 text-gray-800 text-sm font-medium rounded-lg shadow-sm focus:ring-[#1e3a5f] focus:border-[#1e3a5f] block p-2.5">
                                    </div>
                                </div>

                                {{-- Tipe --}}
                                <div>
                                    <label for="edit_tipe" class="block text-xs font-bold text-[#1e3a5f] uppercase tracking-wide mb-1">Tipe <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-[#1e3a5f]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                        </div>
                                        <select id="edit_tipe" name="tipe" x-model="editTipe" required
                                                class="pl-10 pr-10 w-full bg-white border border-gray-300 text-[#1e3a5f] text-sm font-bold rounded-lg shadow-sm focus:ring-[#1e3a5f] focus:border-[#1e3a5f] block p-2.5 cursor-pointer appearance-none">
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
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer p-4 border-t bg-gray-50">
                    <button type="submit" class="w-full px-4 py-3 text-white font-bold bg-[#1e3a5f] rounded-xl hover:bg-[#2a4a6f] shadow-lg transition transform hover:-translate-y-0.5">
                        SIMPAN PERUBAHAN
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Hapus --}}
    <div x-show="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4" @click.away="showDeleteModal = false" style="display: none;">
        <div class="bg-white rounded-lg shadow-xl max-w-sm w-full p-6 relative" @click.stop>
            <h3 class="text-lg font-bold text-gray-900 mb-4">Konfirmasi Hapus</h3>
            <p class="text-gray-600 mb-6">
                Apakah Anda yakin ingin menghapus data kendaraan ini? Tindakan ini tidak dapat dibatalkan.
            </p>
            <form :action="deleteAction" method="POST" class="flex justify-end space-x-4">
                @csrf
                @method('DELETE')
                <button type="button" @click="showDeleteModal = false"
                    class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300">
                    Batal
                </button>
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                    Ya, Hapus
                </button>
            </form>
        </div>
    </div>

    {{-- Modal Promote --}}
    <div x-show="showPromoteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4" @click.away="showPromoteModal = false" style="display: none;">
        <div class="bg-white rounded-lg shadow-xl max-w-sm w-full p-6 relative" @click.stop>
            <h3 class="text-lg font-bold text-gray-900 mb-4">Konfirmasi Pendaftaran</h3>
            <p class="text-gray-600 mb-6">
                Tambahkan kendaraan ini ke Daftar Master?
            </p>
            <form :action="promoteAction" method="POST" class="flex justify-end space-x-4">
                @csrf
                <button type="button" @click="showPromoteModal = false"
                    class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300">
                    Batal
                </button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    OK
                </button>
            </form>
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
// AJAX Live Search untuk Riwayat
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
    toggleLoading(true);

    const formData = new FormData(document.getElementById('filterForm'));
    formData.append('page_riwayat', page);
    
    const params = new URLSearchParams();
    for (let [key, value] of formData) {
        params.append(key, value);
    }
    
    fetch('{{ route("komandan.kendaraan.searchRiwayat") }}?' + params.toString())
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

// Function untuk load data master via AJAX
function loadMasterData(page = 1) {
    toggleLoading(true);

    const formData = new FormData(document.getElementById('filterFormMaster'));
    formData.append('page_master', page);
    
    const params = new URLSearchParams();
    for (let [key, value] of formData) {
        params.append(key, value);
    }
    
    fetch('{{ route("komandan.kendaraan.searchMaster") }}?' + params.toString())
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
