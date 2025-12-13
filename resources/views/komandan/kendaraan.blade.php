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
                                    <select name="per_page_riwayat" onchange="document.getElementById('filterForm').submit()" class="block h-[42px] pl-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer appearance-none">
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
                                       onchange="document.getElementById('filterForm').submit()"
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
                                        onchange="document.getElementById('filterForm').submit()"
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

            {{-- CARD LAYOUT (Mobile) --}}
            <div class="md:hidden space-y-2 p-3">
                @forelse($riwayat as $index => $log)
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
                        <div class="flex gap-3 p-3">
                            {{-- Info Kendaraan di Sebelah Kiri --}}
                            <div class="flex-1 min-w-0">
                                {{-- Nopol & Tipe Badge --}}
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <h4 class="font-bold text-gray-800 text-sm">{{ $log->nopol ?? 'N/A' }}</h4>
                                        <p class="text-gray-600 text-xs">{{ $log->pemilik ?? 'N/A' }}</p>
                                    </div>
                                    <span class="text-xs font-bold px-2 py-1 rounded-full 
                                        {{ $log->tipe == 'Roda 4' ? 'bg-blue-500 text-white' : 'bg-green-500 text-white' }}">
                                        {{ $log->tipe ?? '-' }}
                                    </span>
                                </div>

                                {{-- Waktu Masuk & Keluar (Sejajar) --}}
                                <div class="flex items-center gap-3 mb-2">
                                    {{-- Waktu Masuk --}}
                                    <div class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                        </svg>
                                        <p class="text-gray-700 font-semibold text-xs">
                                            @if($log->waktu_masuk && $log->waktu_masuk->format('Y-m-d') == $tanggalTerpilih)
                                                {{ $log->waktu_masuk->format('H:i:s') }}
                                            @else <span class="text-gray-400">-</span> @endif
                                        </p>
                                    </div>
                                    
                                    {{-- Waktu Keluar --}}
                                    <div class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                        </svg>
                                        <p class="text-gray-700 font-semibold text-xs">
                                            @if($log->waktu_keluar && $log->waktu_keluar->format('Y-m-d') == $tanggalTerpilih)
                                                {{ $log->waktu_keluar->format('H:i:s') }}
                                            @else <span class="text-gray-400">-</span> @endif
                                        </p>
                                    </div>
                                </div>

                                {{-- Keterangan --}}
                                <div class="mb-2">
                                    @if(Auth::user()->peran == 'komandan')
                                        <form action="{{ route('komandan.kendaraan.log.updateKeterangan', $log->id_log) }}" method="POST">
                                            @csrf @method('PUT')
                                            <select name="keterangan" onchange="this.form.submit()" class="w-full border-gray-300 rounded text-xs py-1 focus:border-blue-500 focus:ring-blue-500">
                                                <option value="tidak menginap" {{ $log->keterangan == 'tidak menginap' ? 'selected' : '' }}>Tidak Menginap</option>
                                                <option value="menginap" {{ $log->keterangan == 'Menginap' ? 'selected' : '' }}>Menginap</option>
                                            </select>
                                        </form>
                                    @else
                                        <p class="text-gray-700 font-semibold text-xs">{{ $log->keterangan }}</p>
                                    @endif
                                </div>

                                {{-- Tombol Aksi --}}
                                @if(Auth::user()->peran == 'komandan')
                                    <div>
                                        @if($log->kendaraan)
                                            <div class="flex items-center justify-center gap-1 bg-green-50 text-green-700 font-bold py-1.5 rounded border border-green-300">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                                <span class="text-xs">Terdaftar</span>
                                            </div>
                                        @else
                                            <button @click.prevent="showPromoteModal = true; promoteAction = '{{ route('komandan.kendaraan.log.promote', $log->id_log) }}'" 
                                                    class="w-full bg-blue-500 text-white font-bold py-1.5 rounded text-xs hover:bg-blue-600 transition flex items-center justify-center gap-1">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"></path></svg>
                                                Daftarkan
                                            </button>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-500 text-sm font-semibold">Data tidak ditemukan.</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($riwayat->total() > 0)
                <div class="flex justify-between items-center px-6 py-4 border-t border-gray-200">
                    <div class="text-sm text-gray-600">
                        Showing {{ $riwayat->firstItem() ?? 0 }} to {{ $riwayat->lastItem() ?? 0 }} of {{ $riwayat->total() }} entries
                    </div>
                    <div class="flex items-center gap-1">
                        @if ($riwayat->onFirstPage())
                            <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Previous</span>
                        @else
                            <a href="{{ $riwayat->appends(request()->except('page_riwayat'))->previousPageUrl() }}" class="px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Previous</a>
                        @endif
                        @foreach(range(1, $riwayat->lastPage()) as $page)
                            @if($page == $riwayat->currentPage())
                                <span class="px-3 py-1 text-white bg-[#1e3a5f] rounded font-bold">{{ $page }}</span>
                            @else
                                <a href="{{ $riwayat->appends(request()->except('page_riwayat'))->url($page) }}" class="px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">{{ $page }}</a>
                            @endif
                        @endforeach
                        @if ($riwayat->hasMorePages())
                            <a href="{{ $riwayat->appends(request()->except('page_riwayat'))->nextPageUrl() }}" class="px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Next</a>
                        @else
                            <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Next</span>
                        @endif
                    </div>
                </div>
            @endif
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
                                <select name="per_page_master" onchange="this.form.submit()" class="block h-[42px] pl-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer appearance-none">
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

        <!-- {{-- CARD LAYOUT (Mobile) --}}
        <div class="md:hidden space-y-2 p-3">
            @forelse($kendaraanMaster as $index => $kendaraan)
                <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200 master-card" data-tipe="{{ $kendaraan->tipe }}" data-searchtext="{{ strtolower($kendaraan->nomor_plat . ' ' . $kendaraan->pemilik) }}">
                    <div class="flex gap-3 p-3">
                        {{-- Info Kendaraan --}}
                        <div class="flex-1 min-w-0">
                            {{-- Nopol & Tipe Badge --}}
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h4 class="font-bold text-gray-800 text-sm">{{ $kendaraan->nomor_plat }}</h4>
                                    <p class="text-gray-600 text-xs">{{ $kendaraan->pemilik }}</p>
                                </div>
                                <span class="text-xs font-bold px-2 py-1 rounded-full 
                                    {{ $kendaraan->tipe == 'Roda 4' ? 'bg-blue-500 text-white' : 'bg-green-500 text-white' }}">
                                    {{ $kendaraan->tipe }}
                                </span>
                            </div>

                            {{-- Tombol Aksi --}}
                            @if(Auth::user()->peran == 'komandan')
                                <div class="flex gap-1.5">
                                    <button @click="showEditModal = true; editAction = '{{ route('komandan.kendaraan.master.update', $kendaraan->id_kendaraan) }}'; editPlat = '{{ $kendaraan->nomor_plat }}'; editPemilik = '{{ $kendaraan->pemilik }}'; editTipe = '{{ $kendaraan->tipe }}';" 
                                            class="flex-1 bg-blue-500 text-white font-bold py-1.5 rounded text-xs hover:bg-blue-600 transition flex items-center justify-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828zM5 12V7a2 2 0 012-2h2.586l-4 4H5zM3 15a2 2 0 00-2 2v2h16v-2a2 2 0 00-2-2H3z"></path></svg>
                                        Edit
                                    </button>
                                    <button @click.prevent="showDeleteModal = true; deleteAction = '{{ route('komandan.kendaraan.master.destroy', $kendaraan->id_kendaraan) }}'" 
                                            class="flex-1 bg-red-500 text-white font-bold py-1.5 rounded text-xs hover:bg-red-600 transition flex items-center justify-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                        Hapus
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-500 text-sm font-semibold">Tidak ada data.</p>
                </div>
            @endforelse
        </div>
        
        {{-- Pagination --}}
        @if($kendaraanMaster->total() > 0)
            <div class="flex justify-between items-center px-6 py-4 border-t border-gray-200">
                <div class="text-sm text-gray-600">
                    Showing {{ $kendaraanMaster->firstItem() ?? 0 }} to {{ $kendaraanMaster->lastItem() ?? 0 }} of {{ $kendaraanMaster->total() }} entries
                </div>
                <div class="flex items-center gap-1">
                    @if ($kendaraanMaster->onFirstPage())
                        <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Previous</span>
                    @else
                        <a href="{{ $kendaraanMaster->appends(request()->except('page_master'))->previousPageUrl() }}" class="px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Previous</a>
                    @endif
                    @foreach(range(1, $kendaraanMaster->lastPage()) as $page)
                        @if($page == $kendaraanMaster->currentPage())
                            <span class="px-3 py-1 text-white bg-[#1e3a5f] rounded font-bold">{{ $page }}</span>
                        @else
                            <a href="{{ $kendaraanMaster->appends(request()->except('page_master'))->url($page) }}" class="px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">{{ $page }}</a>
                        @endif
                    @endforeach
                    @if ($kendaraanMaster->hasMorePages())
                        <a href="{{ $kendaraanMaster->appends(request()->except('page_master'))->nextPageUrl() }}" class="px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Next</a>
                    @else
                        <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Next</span>
                    @endif
                </div>
            </div>
        @endif
        </div> -->
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
    
    fetch('{{ route("komandan.kendaraan.searchRiwayat") }}?' + params.toString())
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
    
    fetch('{{ route("komandan.kendaraan.searchMaster") }}?' + params.toString())
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
