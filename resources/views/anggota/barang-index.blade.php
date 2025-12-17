@extends('layouts.app')

@section('header-left')
    <a class="flex items-center border-2 border-[#1a2847] text-[#1a2847] text-sm font-bold px-4 py-2 rounded-full">
        BARANG
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
             class="fixed top-4 right-4 z-50 bg-green-500 text-white pl-6 pr-2 py-1 rounded-lg shadow-2xl flex items-center gap-3 min-w-[300px] max-w-xs"
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


    <div class="w-full min-h-screen bg-slate-100 p-2 md:p-4 pb-32" x-data="{ 
            showPhotoModal: false,
            photoUrl: '',
            photoModalOpen: false,
            photos: [],
            currentPhotoIndex: 0,
            selesaiModalOpen: false,
            selesaiFormAction: '',
            namaPenerima: '',
            tanggalSelesai: '{{ now()->format('Y-m-d') }}',
            waktuSelesai: '{{ now()->format('H:i') }}',
            minTanggalSelesai: '',
            showCreateModal: false,
         }">


        {{-- 1. BAGIAN BARANG TITIPAN (AKTIF) --}}
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6" x-data="{ 
            isOpen: false,
            allBarangTitipan: @js($barang_titipan),
            itemsPerPage: 5,
            currentPage: 1,
            get totalPages() {
                return Math.ceil(this.allBarangTitipan.length / this.itemsPerPage);
            },
            get paginatedBarang() {
                const start = (this.currentPage - 1) * this.itemsPerPage;
                const end = start + this.itemsPerPage;
                return this.allBarangTitipan.slice(start, end);
            }
        }">
            <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] p-3 border-b border-gray-200 cursor-pointer hover:opacity-90 transition" @click="isOpen = !isOpen">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-white mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <h3 class="font-bold text-white">BARANG TITIPAN</h3>
                    </div>
                    <svg class="w-5 h-5 text-white transition-transform" :class="{ 'rotate-180': !isOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>

            <div x-show="isOpen" x-collapse>
                {{-- Per Page Controls --}}
                <div class="p-3 border-b border-gray-200" x-show="allBarangTitipan.length > 0">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">Show:</span>
                        <select x-model="itemsPerPage" @change="currentPage = 1" class="text-xs border border-gray-300 rounded px-2 py-1 focus:ring-[#1e3a5f] focus:border-[#1e3a5f]">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="15">15</option>
                            <option value="20">20</option>
                        </select>
                    </div>
                </div>
                
                <div class="p-1 md:p-3 space-y-2 md:space-y-3">
                <template x-for="barang in paginatedBarang" :key="barang.id_barang">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden border-2 border-gray-300 relative">
                        {{-- Badge Status di Pojok Kanan Atas --}}
                        <div class="absolute top-2 right-2 z-10">
                            <span class="inline-block bg-blue-500 text-white text-[10px] font-bold px-2 py-1 rounded-full shadow-md" x-text="new Date(barang.waktu_titip).toLocaleDateString('id-ID', {day: '2-digit', month: '2-digit', year: '2-digit'})"></span>
                        </div>
                        
                        <div class="p-3">
                            <div class="w-full">
                                {{-- Nama Barang sebagai judul utama --}}
                                <h4 class="font-bold text-gray-800 text-sm mb-3 pr-20" x-text="barang.nama_barang"></h4>

                                {{-- Info Foto & Detail --}}
                                <div class="flex gap-4 mb-4">
                                    {{-- Foto di Kiri --}}
                                    <div class="flex-shrink-0" x-show="barang.foto">
                                        <div @click="showPhotoModal = true; photoUrl = '/storage/' + barang.foto" 
                                             class="w-16 h-16 rounded-lg overflow-hidden border-2 border-gray-200 cursor-pointer hover:border-blue-400 transition-colors">
                                            <img :src="'/storage/' + barang.foto" 
                                                 alt="Foto Barang" 
                                                 class="w-full h-full object-cover">
                                        </div>
                                    </div>
                                    
                                    {{-- Info di Kanan --}}
                                    <div class="flex-1">
                                        <div class="flex items-center gap-1 mb-1">
                                            <svg class="w-3.5 h-3.5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            <p class="text-gray-700 font-semibold text-xs" x-text="barang.nama_penitip"></p>
                                        </div>
                                        
                                        <div class="flex items-center gap-1 mb-1">
                                            <svg class="w-3.5 h-3.5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <p class="text-gray-700 font-semibold text-xs" x-text="new Date(barang.waktu_titip).toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'})"></p>
                                        </div>
                                        
                                        <div class="text-xs text-gray-500">
                                            <span class="font-semibold">Tujuan:</span> <span x-text="barang.tujuan && barang.tujuan.length > 30 ? barang.tujuan.substring(0, 30) + '...' : barang.tujuan"></span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Catatan --}}
                                <div class="mb-4" x-show="barang.catatan">
                                    <div class="text-xs text-gray-500">
                                        <span class="font-semibold">Catatan:</span> <span x-text="barang.catatan"></span>
                                    </div>
                                </div>

                                {{-- Tombol Selesai Full Width --}}
                                <button
                                    @click.prevent="selesaiModalOpen = true; selesaiFormAction = '/anggota/barang-titipan/' + barang.id_barang + '/selesai'; minTanggalSelesai = new Date(barang.waktu_titip).toISOString().split('T')[0];"
                                    class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold py-2.5 px-4 rounded-lg shadow-md transition-all">
                                    SELESAI
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
                
                <div x-show="allBarangTitipan.length === 0">
                    <div class="bg-white rounded-xl shadow-md p-8 text-center border-2 border-gray-300">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                </path>
                            </svg>
                        </div>
                        <p class="text-gray-500 font-semibold">Tidak ada barang titipan aktif.</p>
                    </div>
                </div>
                </div>
                
                {{-- Pagination --}}
                <div class="mt-6 px-3 md:px-6 py-4 border-t border-gray-200" x-show="allBarangTitipan.length > 0 && totalPages > 1">
                    {{-- Desktop Layout --}}
                    <div class="hidden md:flex justify-between items-center">
                        <div class="text-sm text-gray-600">
                            Showing
                            <span x-text="(currentPage - 1) * itemsPerPage + 1"></span> to
                            <span x-text="Math.min(currentPage * itemsPerPage, allBarangTitipan.length)"></span>
                            of <span x-text="allBarangTitipan.length"></span> entries
                        </div>
                        <div class="flex space-x-1">
                            <button
                                @click="currentPage--"
                                :disabled="currentPage === 1"
                                class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400 disabled:opacity-50 text-sm"
                            >
                                Previous
                            </button>
                            <template x-if="totalPages <= 4">
                                <template x-for="page in Array.from({length: totalPages}, (_, i) => i + 1)" :key="page">
                                    <button
                                        @click="currentPage = page"
                                        :class="currentPage === page ? 'bg-blue-500 text-white' : 'bg-gray-300 hover:bg-gray-400'"
                                        class="px-3 py-1 rounded text-sm"
                                        x-text="page"
                                    ></button>
                                </template>
                            </template>
                            <template x-if="totalPages > 4">
                                <div class="flex items-center space-x-1">
                                    <template x-for="page in (() => {
                                        let start = Math.max(1, currentPage - 1);
                                        let end = Math.min(totalPages, start + 2);
                                        if (end - start < 2) start = Math.max(1, end - 2);
                                        return Array.from({length: 3}, (_, i) => start + i).filter(p => p <= totalPages);
                                    })()" :key="page">
                                        <button
                                            @click="currentPage = page"
                                            :class="currentPage === page ? 'bg-blue-500 text-white' : 'bg-gray-300 hover:bg-gray-400'"
                                            class="px-3 py-1 rounded text-sm"
                                            x-text="page"
                                        ></button>
                                    </template>
                                    <span x-show="(() => {
                                        let start = Math.max(1, currentPage - 1);
                                        let end = Math.min(totalPages, start + 2);
                                        if (end - start < 2) start = Math.max(1, end - 2);
                                        return start + 2 < totalPages;
                                    })()" class="text-sm text-gray-500">...</span>
                                    <button x-show="(() => {
                                        let start = Math.max(1, currentPage - 1);
                                        let end = Math.min(totalPages, start + 2);
                                        if (end - start < 2) start = Math.max(1, end - 2);
                                        return start + 2 < totalPages;
                                    })()"
                                        @click="currentPage = totalPages"
                                        :class="currentPage === totalPages ? 'bg-blue-500 text-white' : 'bg-gray-300 hover:bg-gray-400'"
                                        class="px-3 py-1 rounded text-sm"
                                        x-text="totalPages"
                                    ></button>
                                </div>
                            </template>
                            <button
                                @click="currentPage++"
                                :disabled="currentPage >= totalPages"
                                class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400 disabled:opacity-50 text-sm"
                            >
                                Next
                            </button>
                        </div>
                    </div>
                    
                    {{-- Mobile Layout --}}
                    <div class="md:hidden">
                        <div class="text-center text-xs text-gray-600 mb-3">
                            Hal <span x-text="currentPage"></span> dari <span x-text="totalPages"></span>
                        </div>
                        <div class="flex justify-center space-x-2">
                            <button
                                @click="currentPage--"
                                :disabled="currentPage === 1"
                                class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 disabled:opacity-50 text-sm font-medium"
                            >
                                Previous
                            </button>
                            <button
                                @click="currentPage++"
                                :disabled="currentPage >= totalPages"
                                class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 disabled:opacity-50 text-sm font-medium"
                            >
                                Next
                            </button>
                        </div>
                    </div>
                </div>
                

            </div>
        </div>

        {{-- 2. BAGIAN BARANG TEMUAN (AKTIF) --}}
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6" x-data="{ 
            isOpen: false,
            allBarangTemuan: @js($barang_temuan),
            itemsPerPage: 5,
            currentPage: 1,
            get totalPages() {
                return Math.ceil(this.allBarangTemuan.length / this.itemsPerPage);
            },
            get paginatedBarang() {
                const start = (this.currentPage - 1) * this.itemsPerPage;
                const end = start + this.itemsPerPage;
                return this.allBarangTemuan.slice(start, end);
            }
        }">
            <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] p-3 border-b border-gray-200 cursor-pointer hover:opacity-90 transition" @click="isOpen = !isOpen">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-white mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <h3 class="font-bold text-white">BARANG TEMUAN</h3>
                    </div>
                    <svg class="w-5 h-5 text-white transition-transform" :class="{ 'rotate-180': !isOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>

            <div x-show="isOpen" x-collapse>
                {{-- Per Page Controls --}}
                <div class="p-3 border-b border-gray-200" x-show="allBarangTemuan.length > 0">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider">Show:</span>
                        <select x-model="itemsPerPage" @change="currentPage = 1" class="text-xs border border-gray-300 rounded px-2 py-1 focus:ring-[#1e3a5f] focus:border-[#1e3a5f]">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="15">15</option>
                            <option value="20">20</option>
                        </select>
                    </div>
                </div>
                
                <div class="p-1 md:p-3 space-y-2 md:space-y-3">
                <template x-for="barang in paginatedBarang" :key="barang.id_barang">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden border-2 border-gray-300 relative">
                        {{-- Badge Status di Pojok Kanan Atas --}}
                        <div class="absolute top-2 right-2 z-10">
                            <span class="inline-block bg-blue-500 text-white text-[10px] font-bold px-2 py-1 rounded-full shadow-md" x-text="new Date(barang.waktu_lapor).toLocaleDateString('id-ID', {day: '2-digit', month: '2-digit', year: '2-digit'})"></span>
                        </div>
                        
                        <div class="p-3">
                            <div class="w-full">
                                {{-- Nama Barang sebagai judul utama --}}
                                <h4 class="font-bold text-gray-800 text-sm mb-3 pr-20" x-text="barang.nama_barang"></h4>

                                {{-- Info Foto & Detail --}}
                                <div class="flex gap-4 mb-4">
                                    {{-- Foto di Kiri --}}
                                    <div class="flex-shrink-0" x-show="barang.foto">
                                        <div @click="showPhotoModal = true; photoUrl = '/storage/' + barang.foto" 
                                             class="w-16 h-16 rounded-lg overflow-hidden border-2 border-gray-200 cursor-pointer hover:border-blue-400 transition-colors">
                                            <img :src="'/storage/' + barang.foto" 
                                                 alt="Foto Barang" 
                                                 class="w-full h-full object-cover">
                                        </div>
                                    </div>
                                    
                                    {{-- Info di Kanan --}}
                                    <div class="flex-1">
                                        <div class="flex items-center gap-1 mb-1">
                                            <svg class="w-3.5 h-3.5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            <p class="text-gray-700 font-semibold text-xs" x-text="barang.nama_pelapor"></p>
                                        </div>
                                        
                                        <div class="flex items-center gap-1 mb-1">
                                            <svg class="w-3.5 h-3.5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <p class="text-gray-700 font-semibold text-xs" x-text="new Date(barang.waktu_lapor).toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'})"></p>
                                        </div>
                                        
                                        <div class="text-xs text-gray-500">
                                            <span class="font-semibold">Lokasi:</span> <span x-text="barang.lokasi_penemuan && barang.lokasi_penemuan.length > 30 ? barang.lokasi_penemuan.substring(0, 30) + '...' : barang.lokasi_penemuan"></span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Catatan --}}
                                <div class="mb-4" x-show="barang.catatan">
                                    <div class="text-xs text-gray-500">
                                        <span class="font-semibold">Catatan:</span> <span x-text="barang.catatan"></span>
                                    </div>
                                </div>

                                {{-- Tombol Selesai Full Width --}}
                                <button
                                    @click.prevent="selesaiModalOpen = true; selesaiFormAction = '/anggota/barang-temuan/' + barang.id_barang + '/selesai'; minTanggalSelesai = new Date(barang.waktu_lapor).toISOString().split('T')[0];"
                                    class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold py-2.5 px-4 rounded-lg shadow-md transition-all">
                                    SELESAI
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
                
                <div x-show="allBarangTemuan.length === 0">
                    <div class="bg-white rounded-xl shadow-md p-8 text-center border-2 border-gray-300">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-500 font-semibold">Tidak ada barang temuan aktif.</p>
                    </div>
                </div>
                </div>
                
                {{-- Pagination --}}
                <div class="mt-6 px-3 md:px-6 py-4 border-t border-gray-200" x-show="allBarangTemuan.length > 0 && totalPages > 1">
                    {{-- Desktop Layout --}}
                    <div class="hidden md:flex justify-between items-center">
                        <div class="text-sm text-gray-600">
                            Showing
                            <span x-text="(currentPage - 1) * itemsPerPage + 1"></span> to
                            <span x-text="Math.min(currentPage * itemsPerPage, allBarangTemuan.length)"></span>
                            of <span x-text="allBarangTemuan.length"></span> entries
                        </div>
                        <div class="flex space-x-1">
                            <button
                                @click="currentPage--"
                                :disabled="currentPage === 1"
                                class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400 disabled:opacity-50 text-sm"
                            >
                                Previous
                            </button>
                            <template x-if="totalPages <= 4">
                                <template x-for="page in Array.from({length: totalPages}, (_, i) => i + 1)" :key="page">
                                    <button
                                        @click="currentPage = page"
                                        :class="currentPage === page ? 'bg-blue-500 text-white' : 'bg-gray-300 hover:bg-gray-400'"
                                        class="px-3 py-1 rounded text-sm"
                                        x-text="page"
                                    ></button>
                                </template>
                            </template>
                            <template x-if="totalPages > 4">
                                <div class="flex items-center space-x-1">
                                    <template x-for="page in (() => {
                                        let start = Math.max(1, currentPage - 1);
                                        let end = Math.min(totalPages, start + 2);
                                        if (end - start < 2) start = Math.max(1, end - 2);
                                        return Array.from({length: 3}, (_, i) => start + i).filter(p => p <= totalPages);
                                    })()" :key="page">
                                        <button
                                            @click="currentPage = page"
                                            :class="currentPage === page ? 'bg-blue-500 text-white' : 'bg-gray-300 hover:bg-gray-400'"
                                            class="px-3 py-1 rounded text-sm"
                                            x-text="page"
                                        ></button>
                                    </template>
                                    <span x-show="(() => {
                                        let start = Math.max(1, currentPage - 1);
                                        let end = Math.min(totalPages, start + 2);
                                        if (end - start < 2) start = Math.max(1, end - 2);
                                        return start + 2 < totalPages;
                                    })()" class="text-sm text-gray-500">...</span>
                                    <button x-show="(() => {
                                        let start = Math.max(1, currentPage - 1);
                                        let end = Math.min(totalPages, start + 2);
                                        if (end - start < 2) start = Math.max(1, end - 2);
                                        return start + 2 < totalPages;
                                    })()"
                                        @click="currentPage = totalPages"
                                        :class="currentPage === totalPages ? 'bg-blue-500 text-white' : 'bg-gray-300 hover:bg-gray-400'"
                                        class="px-3 py-1 rounded text-sm"
                                        x-text="totalPages"
                                    ></button>
                                </div>
                            </template>
                            <button
                                @click="currentPage++"
                                :disabled="currentPage >= totalPages"
                                class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400 disabled:opacity-50 text-sm"
                            >
                                Next
                            </button>
                        </div>
                    </div>
                    
                    {{-- Mobile Layout --}}
                    <div class="md:hidden">
                        <div class="text-center text-xs text-gray-600 mb-3">
                            Hal <span x-text="currentPage"></span> dari <span x-text="totalPages"></span>
                        </div>
                        <div class="flex justify-center space-x-2">
                            <button
                                @click="currentPage--"
                                :disabled="currentPage === 1"
                                class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 disabled:opacity-50 text-sm font-medium"
                            >
                                Previous
                            </button>
                            <button
                                @click="currentPage++"
                                :disabled="currentPage >= totalPages"
                                class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 disabled:opacity-50 text-sm font-medium"
                            >
                                Next
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- 3. RIWAYAT dengan HYBRID SEARCH + FILTER MULTI-KATEGORI --}}
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6" x-data="{ isOpen: true }">
            <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] p-3 border-b border-gray-200 cursor-pointer hover:opacity-90 transition" @click="isOpen = !isOpen">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-white mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="font-bold text-white">RIWAYAT BARANG</h3>
                    </div>
                    <svg class="w-5 h-5 text-white transition-transform" :class="{ 'rotate-180': !isOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>

            <div x-show="isOpen" x-collapse>

                {{-- Form Filter dengan HYBRID: Suggestion + Live Search --}}
                <div class="p-4 border-b border-gray-200">
                <form action="{{ route('anggota.barang.index') }}" method="GET" id="searchBarangForm" x-data="{}">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                        {{-- Filter Tanggal --}}
                        <div class="w-full">
                            <label for="tanggal" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                                Tanggal
                            </label>
                            <div class="cursor-pointer" @click="$refs.dateInput.showPicker()">
                                <input type="date" id="tanggal" name="tanggal" x-ref="dateInput" value="{{ $tanggal_terpilih }}"
                                    onchange="document.getElementById('searchBarangForm').submit()"
                                    max="{{ date('Y-m-d') }}"
                                    class="block w-full h-[42px] px-4 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer">
                            </div>
                        </div>

                        {{-- Filter Kategori (SEMUA, TITIPAN, TEMUAN) --}}
                        <div class="w-full">
                            <label for="kategori" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                                Kategori
                            </label>
                            <div class="relative">
                                <select id="kategori" name="kategori"
                                    onchange="document.getElementById('searchBarangForm').submit()"
                                    class="block w-full h-[42px] px-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer appearance-none">
                                    <option value="semua" @if($kategori_terpilih == 'semua') selected @endif>Semua Barang</option>
                                    <option value="titipan" @if($kategori_terpilih == 'titipan') selected @endif>Barang Titipan</option>
                                    <option value="temuan" @if($kategori_terpilih == 'temuan') selected @endif>Barang Temuan</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Search dengan Hybrid (Suggestion + Live Search) --}}
                        <div class="w-full" x-data="{
                            searchQuery: '{{ $search_filter ?? '' }}',
                            tanggalFilter: '{{ $tanggal_terpilih }}',
                            kategoriFilter: '{{ $kategori_terpilih }}',
                            suggestions: [],
                            loading: false,
                            showSuggestions: false,
                            liveSearchTimeout: null,
                            liveSearching: false,

                            async getSuggestions() {
                                if (!this.searchQuery || this.searchQuery.length < 1) {
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

                            selectBarang(idBarang, kategoriBarang, namaBarang) {
                                this.searchQuery = namaBarang;
                                this.showSuggestions = false;
                                this.triggerLiveSearchById(idBarang, kategoriBarang);
                            },

                            async triggerLiveSearchById(idBarang, kategoriBarang) {
                                this.liveSearching = true;

                                try {
                                    const url = new URL(window.location);
                                    url.searchParams.set('search', this.searchQuery);
                                    url.searchParams.set('tanggal', this.tanggalFilter);
                                    url.searchParams.set('kategori', this.kategoriFilter);
                                    window.history.pushState({}, '', url);

                                    const response = await fetch(`{{ route('anggota.barang.getRiwayat') }}?id_barang=${idBarang}&kategori_barang=${kategoriBarang}&tanggal=${this.tanggalFilter}`);
                                    const html = await response.text();

                                    document.getElementById('riwayat-container').innerHTML = html;
                                } catch (error) {
                                    console.error('Live search error:', error);
                                } finally {
                                    this.liveSearching = false;
                                }
                            },

                            triggerLiveSearch() {
                                clearTimeout(this.liveSearchTimeout);
                                this.liveSearchTimeout = setTimeout(() => {
                                    if (this.searchQuery.length === 0) {
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

                            <label for="search" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                                Cari Barang
                            </label>
                            <div class="relative">
                                <input type="text" id="search" name="search" x-model="searchQuery"
                                    @input="getSuggestions(); triggerLiveSearch();"
                                    @focus="if(searchQuery && searchQuery.length >= 1) getSuggestions()"
                                    @keydown.enter.prevent="triggerLiveSearchNow()"
                                    placeholder="Ketik untuk mencari..." autocomplete="off"
                                    class="block w-full h-[42px] px-4 pr-12 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm placeholder-gray-400">

                                {{-- Loading Indicator --}}
                                <div x-show="liveSearching" class="absolute right-14 top-1/2 -translate-y-1/2">
                                    <svg class="animate-spin h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                </div>



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
                                        <div @click="selectBarang(suggestion.id_barang, suggestion.kategori, suggestion.nama_barang)"
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

                                    <div x-show="!loading && suggestions.length === 0 && searchQuery && searchQuery.length >= 1"
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
                </div>

                {{-- Container Riwayat dengan Initial Data --}}
                <div id="riwayat-container" class="p-1 md:p-3 space-y-2 md:space-y-3">
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
                            currentFacingMode: 'environment',

                            startCamera() {
                                this.state = 'camera';
                                this.imageBase64 = '';
                                
                                this.stopCamera(); // Stop stream lama dulu

                                if(!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia){
                                    alert('Browser tidak support kamera'); return;
                                }
                                navigator.mediaDevices.getUserMedia({ 
                                    video: { facingMode: this.currentFacingMode }, 
                                    audio: false 
                                })
                                .then(stream => {
                                    this.stream = stream;
                                    this.$refs.videoFeed.srcObject = stream;
                                })
                                .catch(err => console.error('Error:', err));
                            },

                            switchCamera() {
                                this.currentFacingMode = (this.currentFacingMode === 'user') ? 'environment' : 'user';
                                this.startCamera();
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
                                
                                const ctx = canvas.getContext('2d');

                                // Jika kamera depan, balik (mirror) canvasnya agar hasil foto sesuai preview
                                if (this.currentFacingMode === 'user') {
                                    ctx.translate(canvas.width, 0);
                                    ctx.scale(-1, 1);
                                }

                                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                                
                                // Reset transform (opsional, untuk kebersihan)
                                if (this.currentFacingMode === 'user') {
                                    ctx.setTransform(1, 0, 0, 1, 0, 0);
                                }

                                this.imageBase64 = canvas.toDataURL('image/jpeg', 0.8);
                                this.state = 'preview';
                                this.stopCamera();
                            },

                            retakePhoto() {
                                this.startCamera();
                            }
                         }" x-init="$watch('showCreateModal', value => value ? startCamera() : stopCamera())">
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

                            {{-- AREA KAMERA CREATE --}}
                            <div class="mb-5 rounded-lg overflow-hidden border-2 border-slate-500 bg-black relative aspect-[4/3]">
                                
                                {{-- Video Feed (Pakai CSS Mirror jika kamera depan) --}}
                                <video x-show="state === 'camera'" x-ref="videoFeed" autoplay playsinline
                                    class="w-full h-full object-cover transition-transform duration-300"
                                    :class="currentFacingMode === 'user' ? 'transform scale-x-[-1]' : ''">
                                </video>
                                
                                {{-- Hasil Foto (JANGAN pakai CSS Mirror disini, data sudah dibalik canvas) --}}
                                <img x-show="state === 'preview'" :src="imageBase64" 
                                    class="w-full h-full object-cover"
                                    style="display: none;">

                                <div x-show="state === 'camera' && !stream"
                                    class="absolute inset-0 flex items-center justify-center text-white text-xs">Memuat Kamera...</div>

                                {{-- TOMBOL SWITCH CAMERA --}}
                                <button type="button" 
                                        x-show="state === 'camera'"
                                        @click="switchCamera()"
                                        class="absolute top-3 left-3 z-10 bg-black/40 hover:bg-black/60 text-white p-2 rounded-full backdrop-blur-sm border border-white/20 transition-all transform active:scale-95 shadow-md"
                                        title="Ganti Kamera">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                    </svg>
                                </button>
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

        {{-- 6. MODAL FOTO BARANG SIMPLE --}}
        <div x-show="showPhotoModal" 
             class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
             @click.away="showPhotoModal = false"
             style="display: none;">
            <div class="bg-white rounded-lg shadow-xl max-w-lg w-full p-4 relative" @click.stop>
                <div class="flex justify-between items-center pb-3 border-b">
                    <h3 class="text-xl font-bold text-gray-800">FOTO BARANG</h3>
                    <button @click="showPhotoModal = false" class="text-gray-500 hover:text-gray-800 text-3xl">&times;</button>
                </div>
                <div class="mt-4">
                    <img :src="photoUrl" alt="Foto Barang" class="w-full h-auto rounded">
                </div>
            </div>
        </div>

        {{-- 7. MODAL FOTO SLIDER --}}
        <div x-show="photoModalOpen" style="display: none;"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
             @click.away="photoModalOpen = false">
            <div class="bg-white rounded-lg shadow-xl max-w-lg w-full p-4 relative" @click.stop>
                <div class="flex justify-between items-center pb-3 border-b">
                    <h3 class="text-xl font-bold text-gray-800" x-text="currentPhotoIndex === 0 ? 'FOTO BARANG' : 'FOTO PENERIMA'"></h3>
                    <button @click="photoModalOpen = false" class="text-gray-500 hover:text-gray-800 text-3xl">&times;</button>
                </div>
                <div class="mt-4 relative">
                    <img :src="photos[currentPhotoIndex]" alt="Foto" class="w-full h-auto rounded">
                    <button x-show="currentPhotoIndex > 0" @click="currentPhotoIndex--" class="absolute left-2 top-1/2 -translate-y-1/2 bg-black bg-opacity-50 hover:bg-opacity-75 text-white rounded-full w-8 h-8 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </button>
                    <button x-show="currentPhotoIndex < photos.length - 1" @click="currentPhotoIndex++" class="absolute right-2 top-1/2 -translate-y-1/2 bg-black bg-opacity-50 hover:bg-opacity-75 text-white rounded-full w-8 h-8 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>
                <div class="flex justify-center gap-2 mt-3" x-show="photos.length > 1">
                    <template x-for="(photo, index) in photos" :key="index">
                        <button @click="currentPhotoIndex = index" :class="currentPhotoIndex === index ? 'bg-gray-800 w-6' : 'bg-gray-400 w-2'" class="h-2 rounded-full transition-all"></button>
                    </template>
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
                            currentFacingMode: 'environment',

                            startCamera() {
                                this.state = 'camera';
                                this.imageBase64 = '';
                                
                                this.stopCamera();

                                if(!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia){
                                    alert('Browser tidak support kamera'); return;
                                }
                                navigator.mediaDevices.getUserMedia({ 
                                    video: { facingMode: this.currentFacingMode }, 
                                    audio: false 
                                })
                                .then(stream => {
                                    this.stream = stream;
                                    // PERHATIKAN: Ref menggunakan 'videoFeedSelesai'
                                    this.$refs.videoFeedSelesai.srcObject = stream;
                                })
                                .catch(err => console.error('Error:', err));
                            },

                            switchCamera() {
                                this.currentFacingMode = (this.currentFacingMode === 'user') ? 'environment' : 'user';
                                this.startCamera();
                            },

                            stopCamera() {
                                if (this.stream) {
                                    this.stream.getTracks().forEach(track => track.stop());
                                    this.stream = null;
                                }
                            },

                            takeSnapshot() {
                                // PERHATIKAN REF
                                const video = this.$refs.videoFeedSelesai;
                                const canvas = this.$refs.canvasSelesai;
                                canvas.width = video.videoWidth;
                                canvas.height = video.videoHeight;
                                
                                const ctx = canvas.getContext('2d');

                                // Logic Mirroring di Canvas
                                if (this.currentFacingMode === 'user') {
                                    ctx.translate(canvas.width, 0);
                                    ctx.scale(-1, 1);
                                }

                                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                                
                                if (this.currentFacingMode === 'user') {
                                    ctx.setTransform(1, 0, 0, 1, 0, 0);
                                }

                                this.imageBase64 = canvas.toDataURL('image/jpeg', 0.8);
                                this.state = 'preview';
                                this.stopCamera();
                            },

                            retakePhoto() {
                                this.startCamera();
                            }
                         }" x-init="$watch('selesaiModalOpen', value => value ? startCamera() : stopCamera())">
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

                            {{-- AREA KAMERA SELESAI --}}
                            <div class="mb-5 rounded-lg overflow-hidden border-2 border-slate-500 bg-black relative aspect-[4/3]">
                                
                                {{-- Video Feed --}}
                                <video x-show="state === 'camera'" x-ref="videoFeedSelesai" autoplay playsinline
                                    class="w-full h-full object-cover transition-transform duration-300"
                                    :class="currentFacingMode === 'user' ? 'transform scale-x-[-1]' : ''">
                                </video>
                                
                                {{-- Hasil Foto (Tanpa class mirror) --}}
                                <img x-show="state === 'preview'" :src="imageBase64" 
                                    class="w-full h-full object-cover"
                                    style="display: none;">

                                <div x-show="state === 'camera' && !stream"
                                    class="absolute inset-0 flex items-center justify-center text-white text-xs">Memuat Kamera...</div>

                                {{-- TOMBOL SWITCH --}}
                                <button type="button" 
                                        x-show="state === 'camera'"
                                        @click="switchCamera()"
                                        class="absolute top-3 left-3 z-10 bg-black/40 hover:bg-black/60 text-white p-2 rounded-full backdrop-blur-sm border border-white/20 transition-all transform active:scale-95 shadow-md"
                                        title="Ganti Kamera">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                    </svg>
                                </button>
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
                                            :min="minTanggalSelesai"
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