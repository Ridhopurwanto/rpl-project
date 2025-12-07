@extends('layouts.app')

@section('header-left')
    <a class="inline-block border-2 border-[#1a2847] text-[#1a2847] text-sm font-bold px-4 py-1 rounded-full mb-4">
        BARANG
    </a>
@endsection

@section('content')
    <div class="w-full min-h-screen bg-slate-100 p-4 pb-32" x-data="{ 
            photoModalOpen: false, 
            photos: [],
            currentPhotoIndex: 0,
            touchStartX: 0,
            touchEndX: 0,
            selesaiModalOpen: false,
            selesaiFormAction: '',
            namaPenerima: '',
            tanggalSelesai: '{{ now()->format('Y-m-d') }}',
            waktuSelesai: '{{ now()->format('H:i') }}',
            showCreateModal: false,
         }">


        {{-- 1. BAGIAN BARANG TITIPAN (AKTIF) --}}
        <div class="mb-4" x-data="{ isOpen: true }">
            <div @click="isOpen = !isOpen"
                class="text-lg font-bold text-slate-700 uppercase cursor-pointer list-none flex items-center select-none">
                <svg class="w-5 h-5 mr-2 transition-transform duration-300 ease-in-out"
                    :class="isOpen ? 'rotate-0' : '-rotate-90'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
                BARANG TITIPAN :
            </div>

            <div x-show="isOpen" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform -translate-y-2"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform -translate-y-2" class="mt-2 space-y-3">

                @forelse($barang_titipan as $barang)
                    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                        {{-- Header Card --}}
                        <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] px-4 py-2.5 flex justify-between items-center">
                            <div>
                                <p class="text-xs text-blue-200 font-semibold uppercase">Tanggal</p>
                                <p class="text-white font-bold text-base">{{ $barang->waktu_titip->format('d/m/Y') }}</p>
                            </div>
                            <span class="bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                                TITIPAN
                            </span>
                        </div>

                        {{-- Body Card dengan Foto di Kiri & Info Sejajar --}}
                        <div class="p-4">
                            <div class="flex gap-4 mb-4">
                                {{-- Foto Barang di Kiri --}}
                                <div class="flex-shrink-0">
                                    @if($barang->foto)
                                        <div @click="photoModalOpen = true; photos = ['{{ Storage::url($barang->foto) }}']; currentPhotoIndex = 0;"
                                            class="w-24 h-24 rounded-lg overflow-hidden border-2 border-gray-200 cursor-pointer hover:border-blue-400 transition-colors">
                                            <img src="{{ Storage::url($barang->foto) }}" alt="Foto Barang"
                                                class="w-full h-full object-cover">
                                        </div>
                                    @else
                                        <div
                                            class="w-24 h-24 rounded-lg bg-gray-100 flex items-center justify-center border-2 border-gray-200">
                                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                {{-- Info Barang di Kanan (Sejajar dengan Foto) --}}
                                <div class="flex-1 flex flex-col justify-center space-y-2">
                                    {{-- Nama Barang --}}
                                    <div>
                                        <p class="text-xs text-gray-500 font-semibold uppercase">Nama Barang</p>
                                        <p class="text-gray-900 font-bold text-base">{{ $barang->nama_barang }}</p>
                                    </div>

                                    {{-- Penitip --}}
                                    <div>
                                        <p class="text-xs text-gray-500 font-semibold uppercase">Penitip</p>
                                        <p class="text-gray-800 font-semibold">{{ $barang->nama_penitip }}</p>
                                    </div>

                                    {{-- Tujuan --}}
                                    <div>
                                        <p class="text-xs text-gray-500 font-semibold uppercase">Tujuan</p>
                                        <p class="text-gray-800 font-semibold">{{ $barang->tujuan }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Tombol Selesai Full Width --}}
                            <button
                                @click.prevent="selesaiModalOpen = true; selesaiFormAction = '{{ route('anggota.barang.selesaiTitipan', $barang->id_barang) }}';"
                                class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold py-2.5 px-4 rounded-lg shadow-md transition-all">
                                SELESAI
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
                        <p class="text-gray-500 font-semibold">Tidak ada barang titipan aktif.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- 2. BAGIAN BARANG TEMUAN (AKTIF) --}}
        <div class="mb-4" x-data="{ isOpen: true }">
            <div @click="isOpen = !isOpen"
                class="text-lg font-bold text-slate-700 uppercase cursor-pointer list-none flex items-center select-none">
                <svg class="w-5 h-5 mr-2 transition-transform duration-300 ease-in-out"
                    :class="isOpen ? 'rotate-0' : '-rotate-90'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
                BARANG TEMUAN :
            </div>

            <div x-show="isOpen" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform -translate-y-2"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform -translate-y-2" class="mt-2 space-y-3">

                @forelse($barang_temuan as $barang)
                    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                        {{-- Header Card --}}
                        <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] px-4 py-2.5 flex justify-between items-center">
                            <div>
                                <p class="text-xs text-blue-200 font-semibold uppercase">Tanggal</p>
                                <p class="text-white font-bold text-base">{{ $barang->waktu_lapor->format('d/m/Y') }}</p>
                            </div>
                            <span class="bg-yellow-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                                TEMUAN
                            </span>
                        </div>

                        {{-- Body Card dengan Foto di Kiri & Info Sejajar --}}
                        <div class="p-4">
                            <div class="flex gap-4 mb-4">
                                {{-- Foto Barang di Kiri --}}
                                <div class="flex-shrink-0">
                                    @if($barang->foto)
                                        <div @click="photoModalOpen = true; photos = ['{{ Storage::url($barang->foto) }}']; currentPhotoIndex = 0;"
                                            class="w-24 h-24 rounded-lg overflow-hidden border-2 border-gray-200 cursor-pointer hover:border-blue-400 transition-colors">
                                            <img src="{{ Storage::url($barang->foto) }}" alt="Foto Barang"
                                                class="w-full h-full object-cover">
                                        </div>
                                    @else
                                        <div
                                            class="w-24 h-24 rounded-lg bg-gray-100 flex items-center justify-center border-2 border-gray-200">
                                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                {{-- Info Barang di Kanan (Sejajar dengan Foto) --}}
                                <div class="flex-1 flex flex-col justify-center space-y-2">
                                    {{-- Nama Barang --}}
                                    <div>
                                        <p class="text-xs text-gray-500 font-semibold uppercase">Nama Barang</p>
                                        <p class="text-gray-900 font-bold text-base">{{ $barang->nama_barang }}</p>
                                    </div>

                                    {{-- Pelapor --}}
                                    <div>
                                        <p class="text-xs text-gray-500 font-semibold uppercase">Pelapor</p>
                                        <p class="text-gray-800 font-semibold">{{ $barang->nama_pelapor }}</p>
                                    </div>

                                    {{-- Lokasi Penemuan --}}
                                    <div>
                                        <p class="text-xs text-gray-500 font-semibold uppercase">Lokasi Penemuan</p>
                                        <p class="text-gray-800 font-semibold">{{ $barang->lokasi_penemuan }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Tombol Selesai Full Width --}}
                            <button
                                @click.prevent="selesaiModalOpen = true; selesaiFormAction = '{{ route('anggota.barang.selesaiTemuan', $barang->id_barang) }}';"
                                class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold py-2.5 px-4 rounded-lg shadow-md transition-all">
                                SELESAI
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-xl shadow-md p-8 text-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-500 font-semibold">Tidak ada barang temuan aktif.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- 3. RIWAYAT dengan HYBRID SEARCH + FILTER MULTI-KATEGORI --}}
        <div class="mb-4" x-data="{ isOpen: true }">
            <div @click="isOpen = !isOpen"
                class="text-lg font-bold text-slate-700 uppercase cursor-pointer list-none flex items-center select-none">
                <svg class="w-5 h-5 mr-2 transition-transform duration-300 ease-in-out"
                    :class="isOpen ? 'rotate-0' : '-rotate-90'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
                RIWAYAT :
            </div>

            <div x-show="isOpen" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-2">

                {{-- Form Filter dengan HYBRID: Suggestion + Live Search --}}
                <form action="{{ route('anggota.barang.index') }}" method="GET" id="searchBarangForm"
                    class="bg-white p-4 rounded-lg shadow-md mt-2 mb-4">
                    <div class="flex flex-col md:flex-row gap-4">

                        {{-- Filter Tanggal --}}
                        <div class="flex-1 w-full">
                            <label for="tanggal" class="block text-sm font-bold text-slate-600 uppercase mb-1">TANGGAL
                                :</label>
                            <input type="date" id="tanggal" name="tanggal" value="{{ $tanggal_terpilih }}"
                                onchange="document.getElementById('searchBarangForm').submit()"
                                class="w-full bg-[#2a4a6f] text-white px-4 py-2 rounded-lg shadow border-none focus:outline-none focus:ring-2 focus:ring-blue-400"
                                style="color-scheme: dark;">
                        </div>

                        {{-- Filter Kategori (SEMUA, TITIPAN, TEMUAN) --}}
                        <div class="flex-1 w-full">
                            <label for="kategori" class="block text-sm font-bold text-slate-600 uppercase mb-1">KATEGORI
                                :</label>
                            <select id="kategori" name="kategori"
                                onchange="document.getElementById('searchBarangForm').submit()"
                                class="w-full bg-[#2a4a6f] text-white px-4 py-2 rounded-lg shadow border-none focus:outline-none focus:ring-2 focus:ring-blue-400 cursor-pointer">
                                <option value="semua" @if($kategori_terpilih == 'semua') selected @endif>Semua Barang</option>
                                <option value="titipan" @if($kategori_terpilih == 'titipan') selected @endif>Barang Titipan
                                </option>
                                <option value="temuan" @if($kategori_terpilih == 'temuan') selected @endif>Barang Temuan
                                </option>
                            </select>
                        </div>

                        {{-- Search dengan Hybrid (Suggestion + Live Search) --}}
                        <div class="flex-1 w-full" x-data="{
                            searchQuery: '{{ $search_filter ?? '' }}',
                            tanggalFilter: '{{ $tanggal_terpilih }}',
                            kategoriFilter: '{{ $kategori_terpilih }}',
                            suggestions: [],
                            loading: false,
                            showSuggestions: false,
                            liveSearchTimeout: null,
                            liveSearching: false,

                            async getSuggestions() {
                                if (this.searchQuery.length < 1) {
                                    this.suggestions = [];
                                    this.showSuggestions = false;
                                    return;
                                }

                                this.loading = true;
                                this.showSuggestions = true;

                                try {
                                    const response = await fetch(`{{ route('anggota.barang.search') }}?search=${encodeURIComponent(this.searchQuery)}&tanggal=${this.tanggalFilter}&kategori=${this.kategoriFilter}`);
                                    const data = await response.json();
                                    this.suggestions = data;
                                } catch (error) {
                                    console.error('Error:', error);
                                    this.suggestions = [];
                                } finally {
                                    this.loading = false;
                                }
                            },

                            selectBarang(namaBarang) {
                                this.searchQuery = namaBarang;
                                this.showSuggestions = false;
                                this.triggerLiveSearchNow();
                            },

                            triggerLiveSearch() {
                                clearTimeout(this.liveSearchTimeout);
                                this.liveSearchTimeout = setTimeout(() => {
                                    if (this.searchQuery.length >= 2) {
                                        this.performLiveSearch();
                                    }
                                }, 1000);
                            },

                            triggerLiveSearchNow() {
                                clearTimeout(this.liveSearchTimeout);
                                this.performLiveSearch();
                            },

                            async performLiveSearch() {
                                this.liveSearching = true;

                                try {
                                    const url = new URL(window.location);
                                    url.searchParams.set('search', this.searchQuery);
                                    url.searchParams.set('tanggal', this.tanggalFilter);
                                    url.searchParams.set('kategori', this.kategoriFilter);
                                    window.history.pushState({}, '', url);

                                    const response = await fetch(`{{ route('anggota.barang.getRiwayat') }}?search=${encodeURIComponent(this.searchQuery)}&tanggal=${this.tanggalFilter}&kategori=${this.kategoriFilter}`);
                                    const html = await response.text();

                                    document.getElementById('riwayat-container').innerHTML = html;
                                } catch (error) {
                                    console.error('Live search error:', error);
                                } finally {
                                    this.liveSearching = false;
                                }
                            }
                        }">

                            <label for="search" class="block text-sm font-bold text-slate-600 uppercase mb-1">CARI BARANG
                                :</label>
                            <div class="relative">
                                <input type="text" id="search" name="search" x-model="searchQuery"
                                    @input="getSuggestions(); triggerLiveSearch()"
                                    @focus="if(searchQuery.length >= 1) getSuggestions()"
                                    @keydown.enter.prevent="triggerLiveSearchNow()"
                                    placeholder="Nama barang, pelapor, atau penerima" autocomplete="off"
                                    class="w-full bg-[#2a4a6f] text-white px-4 py-2 pr-12 rounded-lg shadow border-none focus:outline-none focus:ring-2 focus:ring-blue-400 placeholder-gray-300">

                                {{-- Loading Indicator --}}
                                <div x-show="liveSearching" class="absolute right-14 top-1/2 -translate-y-1/2">
                                    <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                </div>

                                {{-- Tombol Search --}}
                                <button type="button" @click="triggerLiveSearchNow()"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 bg-blue-500 hover:bg-blue-600 text-white p-2 rounded-md transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </button>

                                {{-- Dropdown Suggestions --}}
                                <div x-show="showSuggestions" @click.away="showSuggestions = false" x-transition
                                    class="absolute top-full left-0 right-0 bg-white border border-gray-300 rounded-lg shadow-2xl mt-1 z-50 max-h-80 overflow-y-auto"
                                    style="display: none;">

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

                                    <template x-for="suggestion in suggestions" :key="suggestion.id_barang">
                                        <div @click="selectBarang(suggestion.nama_barang)"
                                            class="px-4 py-3 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-0 transition-colors">
                                            <div class="flex items-center justify-between">
                                                <div class="flex-1">
                                                    <p class="font-bold text-gray-900 text-sm"
                                                        x-text="suggestion.nama_barang"></p>
                                                    <p class="text-xs text-gray-600">
                                                        <span x-text="suggestion.nama_pelapor"></span>
                                                        <span x-show="suggestion.nama_penerima"> → <span
                                                                x-text="suggestion.nama_penerima"></span></span>
                                                    </p>
                                                </div>
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full ml-2"
                                                    :class="suggestion.kategori === 'titipan' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'"
                                                    x-text="suggestion.kategori === 'titipan' ? 'TITIPAN' : 'TEMUAN'"></span>
                                            </div>
                                        </div>
                                    </template>

                                    <div x-show="!loading && suggestions.length === 0 && searchQuery.length >= 1"
                                        class="px-4 py-4 text-center text-gray-500">
                                        <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                            </path>
                                        </svg>
                                        <p class="text-sm font-semibold mb-1">Tidak ditemukan</p>
                                        <p class="text-xs text-gray-400">Coba kata kunci lain</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                {{-- Container Riwayat dengan Initial Data --}}
                <div id="riwayat-container" class="space-y-3">
                    @include('anggota.barang-riwayat-cards', ['riwayat_barang' => $riwayat_barang])
                </div>
            </div>
        </div>

        {{-- 4. TOMBOL FAB --}}
        <button @click.prevent="showCreateModal = true"
            class="fixed bottom-24 right-4 bg-[#2a4a6f] text-white rounded-full w-16 h-16 flex items-center justify-center shadow-lg transform hover:scale-110 transition-transform z-40 cursor-pointer">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
        </button>

        {{-- ================= 5. MODAL CREATE BARANG (POP-UP) ================= --}}
        <div x-show="showCreateModal" class="relative z-50" style="display: none;">

            {{-- Backdrop --}}
            <div x-show="showCreateModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black bg-opacity-75 transition-opacity"></div>

            {{-- Scroll Wrapper --}}
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">

                    {{-- Card Modal --}}
                    <div x-show="showCreateModal" @click.away="showCreateModal = false"
                        class="relative transform overflow-hidden rounded-xl bg-[#2a4a6f] text-left shadow-xl transition-all sm:my-8 w-full max-w-md p-6"
                        {{-- Logic Kamera & Form State --}} x-data="{
                            state: 'camera', 
                            stream: null,
                            imageBase64: '',
                            kategori: 'temuan', // Default kategori

                            startCamera() {
                                this.state = 'camera';
                                this.imageBase64 = '';
                                if(!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia){
                                    alert('Browser tidak support kamera'); return;
                                }
                                navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' }, audio: false })
                                .then(stream => {
                                    this.stream = stream;
                                    this.$refs.videoFeed.srcObject = stream;
                                })
                                .catch(err => console.error('Error:', err));
                            },

                            stopCamera() {
                                if (this.stream) {
                                    this.stream.getTracks().forEach(track => track.stop());
                                    this.stream = null;
                                }
                            },

                            takeSnapshot() {
                                const video = this.$refs.videoFeed;
                                const canvas = this.$refs.canvas;
                                canvas.width = video.videoWidth;
                                canvas.height = video.videoHeight;
                                canvas.getContext('2d').drawImage(video, 0, 0);
                                this.imageBase64 = canvas.toDataURL('image/jpeg', 0.8);
                                this.state = 'preview';
                                this.stopCamera();
                            },

                            retakePhoto() {
                                this.startCamera();
                            }
                         }" x-effect="showCreateModal ? startCamera() : stopCamera()">
                        {{-- Tombol Close --}}
                        <div class="flex justify-end mb-4">
                            <button @click="showCreateModal = false"
                                class="text-gray-300 hover:text-white transition-colors focus:outline-none">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <form action="{{ route('anggota.barang.store') }}" method="POST">
                            @csrf
                            {{-- Input Hidden --}}
                            <input type="hidden" name="foto_base64" x-model="imageBase64">
                            <input type="hidden" name="kategori" x-model="kategori">

                            {{-- AREA KAMERA --}}
                            <div
                                class="mb-5 rounded-lg overflow-hidden border-2 border-slate-500 bg-black relative aspect-[4/3]">
                                <video x-show="state === 'camera'" x-ref="videoFeed" autoplay playsinline
                                    class="w-full h-full object-cover"></video>
                                <img x-show="state === 'preview'" :src="imageBase64" class="w-full h-full object-cover"
                                    style="display: none;">
                                <div x-show="state === 'camera' && !stream"
                                    class="absolute inset-0 flex items-center justify-center text-white text-xs">Memuat
                                    Kamera...</div>
                            </div>
                            <canvas x-ref="canvas" class="hidden"></canvas>

                            {{-- TOMBOL AMBIL FOTO --}}
                            <div class="mb-6">
                                <button type="button" x-show="state === 'camera'" @click="takeSnapshot()"
                                    class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 rounded shadow">AMBIL
                                    FOTO</button>
                                <button type="button" x-show="state === 'preview'" @click="retakePhoto()"
                                    style="display: none;"
                                    class="w-full bg-amber-600 hover:bg-amber-700 text-white font-bold py-2 rounded shadow">FOTO ULANG
                                </button>
                            </div>

                            {{-- PILIHAN KATEGORI (Tab Style) --}}
                            <div class="flex mb-4 bg-slate-700 rounded-lg p-1">
                                <button type="button" @click="kategori = 'temuan'"
                                    :class="kategori === 'temuan' ? 'bg-blue-500 text-white' : 'text-gray-300 hover:text-white'"
                                    class="flex-1 py-2 rounded-md text-sm font-bold transition-colors">
                                    TEMUAN
                                </button>
                                <button type="button" @click="kategori = 'titipan'"
                                    :class="kategori === 'titipan' ? 'bg-blue-500 text-white' : 'text-gray-300 hover:text-white'"
                                    class="flex-1 py-2 rounded-md text-sm font-bold transition-colors">
                                    TITIPAN
                                </button>
                            </div>

                            {{-- FORM FIELDS --}}
                            <div class="grid grid-cols-1 gap-y-4">
                                {{-- === TAMBAHAN BARU: TANGGAL & WAKTU === --}}
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-gray-300 font-semibold text-sm mb-1 uppercase">TANGGAL
                                            :</label>
                                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" {{-- Default Hari Ini
                                            --}}
                                            class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500"
                                            required>
                                    </div>
                                    <div>
                                        <label class="block text-gray-300 font-semibold text-sm mb-1 uppercase">WAKTU
                                            :</label>
                                        <input type="time" name="waktu" value="{{ date('H:i') }}" {{-- Default Jam Saat Ini
                                            --}}
                                            class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500"
                                            required>
                                    </div>
                                </div>

                                {{-- Nama Barang --}}
                                <div>
                                    <label class="block text-gray-300 font-semibold text-sm mb-1 uppercase">NAMA BARANG
                                        :</label>
                                    <input type="text" name="nama_barang" placeholder="Contoh: Kunci, Dompet, Laptop"
                                        class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500"
                                        required>
                                </div>

                                {{-- Nama Pelapor / Penitip (Dinamis) --}}
                                <div>
                                    <label class="block text-gray-300 font-semibold text-sm mb-1 uppercase"
                                        x-text="kategori === 'temuan' ? 'NAMA PELAPOR :' : 'NAMA PENITIP :'"></label>
                                    <input type="text" name="nama_pelapor"
                                        :value="kategori === 'temuan' ? '{{ Auth::user()->nama }}' : ''"
                                        placeholder="Nama Lengkap"
                                        class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500"
                                        required>
                                </div>

                                {{-- Lokasi / Tujuan (Dinamis) --}}
                                <div>
                                    <label class="block text-gray-300 font-semibold text-sm mb-1 uppercase"
                                        x-text="kategori === 'temuan' ? 'LOKASI PENEMUAN :' : 'TUJUAN TITIPAN :'"></label>
                                    <input type="text" name="lokasi_tujuan"
                                        :placeholder="kategori === 'temuan' ? 'Contoh: Parkiran Depan' : 'Contoh: Untuk Pak Budi'"
                                        class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500"
                                        required>
                                </div>

                                {{-- Catatan --}}
                                <div>
                                    <label class="block text-gray-300 font-semibold text-sm mb-1 uppercase">CATATAN
                                        :</label>
                                    <textarea name="catatan" rows="2" placeholder="Keterangan tambahan..."
                                        class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500"></textarea>
                                </div>
                            </div>

                            {{-- TOMBOL SUBMIT --}}
                            <div class="mt-8" x-show="state === 'preview'" style="display: none;">
                                <button type="submit"
                                    class="w-full bg-green-600 text-white font-bold py-3 px-6 rounded-lg shadow-md hover:bg-green-700 transition-colors duration-300">
                                    SIMPAN DATA
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- 6. MODAL LIHAT FOTO dengan SLIDER --}}
        <div x-show="photoModalOpen" style="display: none;"
            class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center p-4 z-[60]" x-transition
            @touchstart="touchStartX = $event.changedTouches[0].screenX" @touchend="
                touchEndX = $event.changedTouches[0].screenX;
                if (touchStartX - touchEndX > 50 && currentPhotoIndex < photos.length - 1) currentPhotoIndex++;
                if (touchEndX - touchStartX > 50 && currentPhotoIndex > 0) currentPhotoIndex--;
            ">

            <div @click.outside="photoModalOpen = false" class="relative max-w-4xl w-full">

                {{-- Header Modal --}}
                <div class="flex justify-between items-center mb-4">
                    <div class="text-white">
                        <p class="text-sm text-gray-300">Foto <span x-text="currentPhotoIndex + 1"></span> dari <span
                                x-text="photos.length"></span></p>
                        <p class="text-xs text-gray-400 mt-1"
                            x-text="currentPhotoIndex === 0 ? 'Foto Barang' : 'Foto Penerima'"></p>
                    </div>
                    <button @click="photoModalOpen = false"
                        class="text-white hover:text-gray-300 text-2xl font-bold bg-gray-800 hover:bg-gray-700 rounded-full w-10 h-10 flex items-center justify-center transition-colors">
                        ×
                    </button>
                </div>

                {{-- Image Container --}}
                <div class="relative">
                    <img :src="photos[currentPhotoIndex]"
                        class="w-full h-auto max-h-[70vh] object-contain rounded-lg border-4 border-gray-700">

                    {{-- Previous Button --}}
                    <button x-show="currentPhotoIndex > 0" @click="currentPhotoIndex--"
                        class="absolute left-4 top-1/2 -translate-y-1/2 bg-black bg-opacity-50 hover:bg-opacity-75 text-white rounded-full w-12 h-12 flex items-center justify-center transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                            </path>
                        </svg>
                    </button>

                    {{-- Next Button --}}
                    <button x-show="currentPhotoIndex < photos.length - 1" @click="currentPhotoIndex++"
                        class="absolute right-4 top-1/2 -translate-y-1/2 bg-black bg-opacity-50 hover:bg-opacity-75 text-white rounded-full w-12 h-12 flex items-center justify-center transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>

                {{-- Indicator Dots --}}
                <div class="flex justify-center gap-2 mt-4" x-show="photos.length > 1">
                    <template x-for="(photo, index) in photos" :key="index">
                        <button @click="currentPhotoIndex = index"
                            :class="currentPhotoIndex === index ? 'bg-white w-8' : 'bg-gray-500 w-2'"
                            class="h-2 rounded-full transition-all"></button>
                    </template>
                </div>

                {{-- Hint --}}
                <div class="text-center mt-4 text-gray-400 text-xs" x-show="photos.length > 1">
                    Swipe atau gunakan tombol panah untuk navigasi
                </div>
            </div>
        </div>


        {{-- 7. MODAL SELESAI (SERAH TERIMA BARANG) --}}
        <div x-show="selesaiModalOpen" class="relative z-50" style="display: none;">

            {{-- Backdrop --}}
            <div x-show="selesaiModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black bg-opacity-75 transition-opacity"></div>

            {{-- Scroll Wrapper --}}
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">

                    {{-- Card Modal Selesai --}}
                    <div x-show="selesaiModalOpen" @click.away="selesaiModalOpen = false"
                        class="relative transform overflow-hidden rounded-xl bg-[#2a4a6f] text-left shadow-xl transition-all sm:my-8 w-full max-w-md p-6"
                        {{-- LOGIKA KAMERA KHUSUS MODAL SELESAI --}} x-data="{
                            state: 'camera', 
                            stream: null,
                            imageBase64: '',

                            startCamera() {
                                this.state = 'camera';
                                this.imageBase64 = '';
                                if(!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia){
                                    alert('Browser tidak support kamera'); return;
                                }
                                navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' }, audio: false })
                                .then(stream => {
                                    this.stream = stream;
                                    this.$refs.videoFeedSelesai.srcObject = stream; // Ref berbeda dengan create
                                })
                                .catch(err => console.error('Error:', err));
                            },

                            stopCamera() {
                                if (this.stream) {
                                    this.stream.getTracks().forEach(track => track.stop());
                                    this.stream = null;
                                }
                            },

                            takeSnapshot() {
                                const video = this.$refs.videoFeedSelesai;
                                const canvas = this.$refs.canvasSelesai;
                                canvas.width = video.videoWidth;
                                canvas.height = video.videoHeight;
                                canvas.getContext('2d').drawImage(video, 0, 0);
                                this.imageBase64 = canvas.toDataURL('image/jpeg', 0.8);
                                this.state = 'preview';
                                this.stopCamera();
                            },

                            retakePhoto() {
                                this.startCamera();
                            }
                         }" {{-- Jalankan kamera saat modal 'selesaiModalOpen' bernilai true --}}
                        x-effect="selesaiModalOpen ? startCamera() : stopCamera()">
                        {{-- Tombol Close --}}
                        <div class="flex justify-end mb-4">
                            <button @click="selesaiModalOpen = false"
                                class="text-gray-300 hover:text-white transition-colors focus:outline-none">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <form :action="selesaiFormAction" method="POST">
                            @csrf @method('PUT')
                            <input type="hidden" name="foto_penerima_base64" x-model="imageBase64">

                            <h3 class="text-xl font-bold text-white text-center mb-6 uppercase">BUKTI SERAH TERIMA</h3>

                            {{-- AREA KAMERA (PENERIMA) --}}
                            <div
                                class="mb-5 rounded-lg overflow-hidden border-2 border-slate-500 bg-black relative aspect-[4/3]">
                                <video x-show="state === 'camera'" x-ref="videoFeedSelesai" autoplay playsinline
                                    class="w-full h-full object-cover"></video>
                                <img x-show="state === 'preview'" :src="imageBase64" class="w-full h-full object-cover"
                                    style="display: none;">
                                <div x-show="state === 'camera' && !stream"
                                    class="absolute inset-0 flex items-center justify-center text-white text-xs">Memuat
                                    Kamera...</div>
                            </div>
                            <canvas x-ref="canvasSelesai" class="hidden"></canvas>

                            {{-- TOMBOL AMBIL FOTO --}}
                            <div class="mb-6">
                                <button type="button" x-show="state === 'camera'" @click="takeSnapshot()"
                                    class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 rounded shadow">AMBIL
                                    FOTO PENERIMA</button>
                                <button type="button" x-show="state === 'preview'" @click="retakePhoto()"
                                    style="display: none;"
                                    class="w-full bg-amber-600 hover:bg-amber-700 text-white font-bold py-2 rounded shadow">FOTO ULANG</button>
                            </div>

                            {{-- FORM FIELDS --}}
                            <div class="grid grid-cols-1 gap-y-4">

                                {{-- Tanggal & Waktu --}}
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-gray-300 font-semibold text-sm mb-1 uppercase">TANGGAL
                                            :</label>
                                        <input type="date" name="tanggal_ambil" value="{{ date('Y-m-d') }}"
                                            class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500"
                                            required>
                                    </div>
                                    <div>
                                        <label class="block text-gray-300 font-semibold text-sm mb-1 uppercase">WAKTU
                                            :</label>
                                        <input type="time" name="waktu_ambil" value="{{ date('H:i') }}"
                                            class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500"
                                            required>
                                    </div>
                                </div>

                                {{-- Nama Penerima --}}
                                <div>
                                    <label class="block text-gray-300 font-semibold text-sm mb-1 uppercase">NAMA PENERIMA
                                        :</label>
                                    <input type="text" name="nama_penerima" x-model="namaPenerima"
                                        placeholder="Nama orang yang mengambil"
                                        class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500"
                                        required>
                                </div>
                            </div>

                            {{-- TOMBOL SUBMIT --}}
                            <div class="mt-8" x-show="state === 'preview'" style="display: none;">
                                <button type="submit"
                                    class="w-full bg-green-600 text-white font-bold py-3 px-6 rounded-lg shadow-md hover:bg-green-700 transition-colors duration-300">
                                    SELESAIKAN & SIMPAN
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Error Handling --}}
    @if($errors->any())
        <script>
            document.addEventListener('alpine:init', () => { Alpine.store('showCreateModal', true); });
        </script>
    @endif
@endsection