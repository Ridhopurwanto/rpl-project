@extends('layouts.app')

@section('header-left')
    <a class="flex items-center border-2 border-[#1a2847] text-[#1a2847] text-sm font-bold px-4 py-2 rounded-full">
        GANGGUAN<br class="sm:hidden"> KAMTIBMAS
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

    <div class="w-full min-h-screen bg-slate-100 p-4 pb-32" x-data="gangguanData">

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

        {{-- BAGIAN RIWAYAT GANGGUAN KAMTIBMAS --}}
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] p-3 border-b border-gray-200 cursor-pointer hover:opacity-90 transition" @click="isOpen = !isOpen">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-white mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <h3 class="font-bold text-white">RIWAYAT GANGGUAN KAMTIBMAS</h3>
                    </div>
                    <svg class="w-5 h-5 text-white transition-transform" :class="{ 'rotate-180': !isOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>

            <div x-show="isOpen" x-transition>
                {{-- Filter --}}
                <div class="p-4 border-b border-gray-200">
                    <form action="{{ route('anggota.gangguan.index') }}" method="GET" id="filterForm">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            
                            {{-- Show Per Page --}}
                            <div class="w-full md:w-auto">
                                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Show</label>
                                <div class="flex items-center gap-2">
                                    <select x-model="itemsPerPage" @change="currentPage = 1" class="h-[42px] pl-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm appearance-none cursor-pointer" style="background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27currentColor%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3e%3cpolyline points=%276 9 12 15 18 9%27%3e%3c/polyline%3e%3c/svg%3e'); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 1.25em 1.25em;">
                                        <option value="5">5</option>
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                    </select>
                                    <span class="text-sm text-gray-600">rows</span>
                                </div>
                            </div>
                            
                            {{-- Filter Bulan (Custom Picker) --}}
                            <div class="w-full md:flex-1" x-data="{
                                showPicker: false,
                                month: parseInt('{{ \Carbon\Carbon::parse($bulan_terpilih)->format('m') }}'),
                                year: parseInt('{{ \Carbon\Carbon::parse($bulan_terpilih)->format('Y') }}'),
                                months: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                                shortMonths: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'],
                                
                                get displayValue() {
                                    return this.months[this.month - 1] + ' ' + this.year;
                                },
                                
                                selectMonth(m) {
                                    this.month = m;
                                    this.submitForm();
                                },
                                
                                changeYear(delta) {
                                    this.year += delta;
                                },
                                
                                submitForm() {
                                    let m = this.month.toString().padStart(2, '0');
                                    this.$refs.hiddenBulan.value = this.year + '-' + m;
                                    document.getElementById('filterForm').submit();
                                }
                            }" @click.away="showPicker = false">
                                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Bulan</label>
                                <div class="relative">
                                    {{-- Hidden Input --}}
                                    <input type="hidden" name="bulan" x-ref="hiddenBulan" value="{{ $bulan_terpilih }}">

                                    {{-- Trigger Button (Looks like Input) --}}
                                    <div @click="showPicker = !showPicker" 
                                         class="block w-full h-[42px] px-4 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg hover:border-[#1e3a5f] cursor-pointer flex items-center justify-between">
                                        <span x-text="displayValue"></span>
                                        <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>

                                    {{-- Dropdown Picker --}}
                                    <div x-show="showPicker" 
                                         style="display: none;"
                                         class="absolute z-50 top-full left-0 mt-1 w-64 bg-white rounded-lg shadow-xl border border-gray-200 p-4">
                                        
                                        {{-- Year Navigator --}}
                                        <div class="flex justify-between items-center mb-4">
                                            <button type="button" @click.stop="changeYear(-1)" class="p-1 hover:bg-gray-100 rounded-full">
                                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                            </button>
                                            <span class="font-bold text-gray-800" x-text="year"></span>
                                            <button type="button" @click.stop="changeYear(1)" class="p-1 hover:bg-gray-100 rounded-full">
                                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                            </button>
                                        </div>

                                        {{-- Months Grid --}}
                                        <div class="grid grid-cols-4 gap-2">
                                            <template x-for="(mName, index) in shortMonths">
                                                <button type="button"
                                                        @click.stop="selectMonth(index + 1)"
                                                        :class="{'bg-[#1e3a5f] text-white': month === (index + 1), 'hover:bg-blue-50 text-gray-700': month !== (index + 1)}"
                                                        class="text-xs font-medium py-2 rounded-md transition-colors"
                                                        x-text="mName">
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Filter Kategori --}}
                            <div class="w-full">
                                <label for="kategori" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                                    Kategori
                                </label>
                                <div class="relative">
                                    <select id="kategori" name="kategori" 
                                            onchange="this.form.submit()"
                                            class="block w-full h-[42px] px-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer appearance-none">
                                        <option value="semua" @if($kategori_terpilih == 'semua') selected @endif>Semua Kategori</option>
                                        @foreach($kategori_list as $kategori)
                                            <option value="{{ $kategori }}" @if($kategori_terpilih == $kategori) selected @endif>{{ $kategori }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Card Layout Gangguan Kamtibmas --}}
                <div class="p-3 space-y-3">
                    <template x-for="(gangguan, index) in paginatedGangguan" :key="'gangguan-' + index">
                        <div class="bg-white rounded-lg shadow-md overflow-hidden border-2 border-gray-300 relative">
                            {{-- Badge Kategori di Pojok Kanan Atas --}}
                            <div class="absolute top-3 right-3 z-10">
                                <span class="inline-block bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded-full shadow-md" x-text="gangguan.kategori"></span>
                            </div>
                            
                            <div class="p-4">
                                <div class="w-full">
                                    {{-- Lokasi sebagai judul utama --}}
                                    <h4 class="font-bold text-gray-800 text-sm mb-3 pr-20" x-text="gangguan.lokasi"></h4>

                                    {{-- Info Foto & Waktu --}}
                                    <div class="flex gap-4 mb-2">
                                        {{-- Foto di Kiri --}}
                                        <div class="flex-shrink-0">
                                            <div @click="showPhotoModal = true; photoUrl = gangguan.foto_url" 
                                                 class="w-16 h-16 rounded-lg overflow-hidden border-2 border-gray-200 cursor-pointer hover:border-blue-400 transition-colors">
                                                <img :src="gangguan.foto_url" 
                                                     alt="Foto Gangguan" 
                                                     class="w-full h-full object-cover">
                                            </div>
                                        </div>
                                        
                                        {{-- Info di Kanan --}}
                                        <div class="flex-1">
                                            <div class="flex items-center gap-1 mb-1">
                                                <svg class="w-3.5 h-3.5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <p class="text-gray-700 font-semibold text-xs" x-text="gangguan.waktu_formatted"></p>
                                            </div>
                                            
                                            <div class="text-xs text-gray-500">
                                                <span class="font-semibold">Keterangan:</span> <span x-text="gangguan.deskripsi_short"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                    
                    <div x-show="paginatedGangguan.length === 0">
                        <div class="bg-white rounded-xl shadow-md p-8 text-center border-2 border-gray-300">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            <p class="text-gray-500 font-semibold">Tidak ada laporan gangguan pada bulan & kategori ini.</p>
                        </div>
                    </div>
                </div>
                
                {{-- Pagination --}}
                <div class="mt-6 px-3 md:px-6 py-4 border-t border-gray-200" x-show="paginatedGangguan.length > 0 && totalPages > 1">
                    {{-- Desktop Layout --}}
                    <div class="hidden md:flex justify-between items-center">
                        <div class="text-sm text-gray-600">
                            Showing
                            <span x-text="(currentPage - 1) * itemsPerPage + 1"></span> to
                            <span x-text="Math.min(currentPage * itemsPerPage, allGangguan.length)"></span>
                            of <span x-text="allGangguan.length"></span> entries
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
                        <div class="text-xs text-gray-600 mb-3">
                            Showing <span x-text="(currentPage - 1) * itemsPerPage + 1"></span> to <span x-text="Math.min(currentPage * itemsPerPage, allGangguan.length)"></span> of <span x-text="allGangguan.length"></span> entries
                        </div>
                        <div class="flex justify-center items-center space-x-1">
                            <button
                                @click="currentPage--"
                                :disabled="currentPage === 1"
                                class="px-2 py-1 bg-gray-300 rounded hover:bg-gray-400 disabled:opacity-50 text-xs"
                            >
                                Previous
                            </button>
                            <template x-if="totalPages <= 4">
                                <template x-for="page in Array.from({length: totalPages}, (_, i) => i + 1)" :key="page">
                                    <button
                                        @click="currentPage = page"
                                        :class="currentPage === page ? 'bg-blue-500 text-white' : 'bg-gray-300 hover:bg-gray-400'"
                                        class="px-2 py-1 rounded text-xs"
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
                                            class="px-2 py-1 rounded text-xs"
                                            x-text="page"
                                        ></button>
                                    </template>
                                    <span x-show="(() => {
                                        let start = Math.max(1, currentPage - 1);
                                        let end = Math.min(totalPages, start + 2);
                                        if (end - start < 2) start = Math.max(1, end - 2);
                                        return start + 2 < totalPages;
                                    })()" class="text-xs text-gray-500">...</span>
                                    <button x-show="(() => {
                                        let start = Math.max(1, currentPage - 1);
                                        let end = Math.min(totalPages, start + 2);
                                        if (end - start < 2) start = Math.max(1, end - 2);
                                        return start + 2 < totalPages;
                                    })()"
                                        @click="currentPage = totalPages"
                                        :class="currentPage === totalPages ? 'bg-blue-500 text-white' : 'bg-gray-300 hover:bg-gray-400'"
                                        class="px-2 py-1 rounded text-xs"
                                        x-text="totalPages"
                                    ></button>
                                </div>
                            </template>
                            <button
                                @click="currentPage++"
                                :disabled="currentPage >= totalPages"
                                class="px-2 py-1 bg-gray-300 rounded hover:bg-gray-400 disabled:opacity-50 text-xs"
                            >
                                Next
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    {{-- TOMBOL FAB --}}
    <button @click.prevent="showCreateModal = true" 
            class="fixed bottom-24 right-4 bg-[#2a4a6f] text-white rounded-full w-16 h-16 flex items-center justify-center shadow-lg transform hover:scale-110 transition-transform z-40 cursor-pointer">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
    </button>

    {{-- ================= MODAL CREATE LAPORAN (SCROLL HALAMAN / OVERLAY SCROLL) ================= --}}
    <div x-show="showCreateModal"
         class="relative z-50" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true"
         style="display: none;">
        
        {{-- 1. Backdrop Hitam (Fixed, tidak ikut scroll) --}}
        <div x-show="showCreateModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black bg-opacity-75 transition-opacity"></div>

        {{-- 2. Wrapper Scroll (Fixed inset-0 + overflow-y-auto agar layar bisa discroll) --}}
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            {{-- Flex container untuk memusatkan modal secara vertikal & horizontal --}}
            {{-- 'min-h-full' memastikan kita bisa scroll jika konten panjang --}}
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                
                {{-- 3. Card Modal (HAPUS max-h dan overflow-y, biarkan tinggi otomatis) --}}
                <div x-show="showCreateModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     @click.away="showCreateModal = false"
                     class="relative transform overflow-hidden rounded-xl bg-[#2a4a6f] text-left shadow-xl transition-all sm:my-8 w-full max-w-md p-6"
                     
                     {{-- Logika Kamera tetap sama --}}
                     x-data="{
                        state: 'camera', 
                        stream: null,
                        imageBase64: '',
                        currentFacingMode: 'environment',
                        
                        startCamera() {
                            this.state = 'camera';
                            this.imageBase64 = '';
                            
                            this.stopCamera(); // Reset dulu

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

                            // Mirror logic jika kamera depan
                            if (this.currentFacingMode === 'user') {
                                ctx.translate(canvas.width, 0);
                                ctx.scale(-1, 1);
                            }

                            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                            
                            // Reset transform
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
                     }"
                     x-init="$watch('showCreateModal', value => value ? startCamera() : stopCamera())"
                >

                    {{-- Tombol Close (X) --}}
                    <div class="flex justify-end mb-4">
                        <button @click="showCreateModal = false" class="text-gray-300 hover:text-white transition-colors focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <form action="{{ route('anggota.gangguan.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="foto_base64" x-model="imageBase64">

                        {{-- AREA KAMERA --}}
                        <div class="mb-5 rounded-lg overflow-hidden border-2 border-slate-500 bg-black relative aspect-[4/3]">
                            
                            {{-- Video Feed (Pakai CSS Mirror jika kamera depan) --}}
                            <video x-show="state === 'camera'" x-ref="videoFeed" autoplay playsinline 
                                class="w-full h-full object-cover transition-transform duration-300"
                                :class="currentFacingMode === 'user' ? 'transform scale-x-[-1]' : ''">
                            </video>
                            
                            {{-- Hasil Foto (Tanpa class mirror) --}}
                            <img x-show="state === 'preview'" :src="imageBase64" 
                                class="w-full h-full object-cover transition-transform duration-300"
                                style="display: none;">
                            
                            <div x-show="state === 'camera' && !stream" class="absolute inset-0 flex items-center justify-center text-white text-xs">
                                Memuat Kamera...
                            </div>

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
                            <button type="button" x-show="state === 'camera'" @click="takeSnapshot()" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 rounded shadow">
                                AMBIL FOTO
                            </button>
                            <button type="button" x-show="state === 'preview'" @click="retakePhoto()" style="display: none;" class="w-full bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 rounded shadow">
                                FOTO ULANG
                            </button>
                        </div>

                        {{-- FORM DATA --}}
                        <div class="grid grid-cols-3 gap-x-4 gap-y-5">
                            
                            <label class="col-span-1 text-gray-300 font-semibold text-sm self-center">TANGGAL :</label>
                            <div class="col-span-2">
                                <input type="date" name="tanggal_lapor" value="{{ date('Y-m-d') }}" class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500" required>
                            </div>

                            <label class="col-span-1 text-gray-300 font-semibold text-sm self-center">WAKTU :</label>
                            <div class="col-span-2">
                                <input type="time" name="waktu_lapor_time" value="{{ date('H:i') }}" class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500" required>
                            </div>

                            <label class="col-span-1 text-gray-300 font-semibold text-sm self-center">KATEGORI :</label>
                            <div class="col-span-2">
                                <select name="kategori" class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500" required>
                                    @foreach($kategori_list as $kategori)
                                        <option value="{{ $kategori }}">{{ $kategori }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <label class="col-span-1 text-gray-300 font-semibold text-sm self-center">LOKASI :</label>
                            <div class="col-span-2">
                                <input type="text" name="lokasi" placeholder="Contoh: Jl. Sudirman" class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500" required>
                            </div>

                            <label class="col-span-1 text-gray-300 font-semibold text-sm self-start pt-2">KET :</label>
                            <div class="col-span-2">
                                <textarea name="deskripsi" rows="2" placeholder="Keterangan singkat..." class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500" required></textarea>
                            </div>
                        </div>

                        {{-- TOMBOL SUBMIT --}}
                        <div class="mt-8" x-show="state === 'preview'" style="display: none;">
                            <button type="submit" class="w-full bg-green-600 text-white font-bold py-3 px-6 rounded-lg shadow-md hover:bg-green-700 transition-colors duration-300">
                                SUBMIT LAPORAN
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    {{-- MODAL FOTO GANGGUAN --}}
    <div x-show="showPhotoModal" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
         @click.away="showPhotoModal = false"
         style="display: none;">
        <div class="bg-white rounded-lg shadow-xl w-auto max-w-[95vw] max-h-[90vh] relative overflow-hidden flex flex-col" @click.stop>
            <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] flex justify-between items-center p-4 shrink-0">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="text-xl font-bold text-white">FOTO GANGGUAN</h3>
                </div>
                <button @click="showPhotoModal = false" class="text-white hover:text-gray-200 text-3xl">&times;</button>
            </div>
            <div class="p-4 flex justify-center bg-gray-50 overflow-hidden">
                <img :src="photoUrl" alt="Foto Gangguan" class="max-h-[75vh] w-auto h-auto object-contain rounded shadow-md">
            </div>
        </div>
    </div>

</div>

{{-- Error Handling: Buka modal create jika ada error submit --}}
@if($errors->any())
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('showCreateModal', true);
    });
</script>
@endif

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('gangguanData', () => ({
            // Modal states
            showCreateModal: false,
            showPhotoModal: false,
            photoUrl: '',
            showSuccessNotif: {{ session('success') ? 'true' : 'false' }},
            showErrorNotif: {{ session('error') ? 'true' : 'false' }},
            
            // Pagination states
            isOpen: true,
            allGangguan: [],
            currentPage: 1,
            itemsPerPage: 5,
            
            get paginatedGangguan() {
                const start = (this.currentPage - 1) * this.itemsPerPage;
                return this.allGangguan.slice(start, start + this.itemsPerPage);
            },
            
            get totalPages() {
                return Math.ceil(this.allGangguan.length / this.itemsPerPage);
            },
            
            init() {
                // Process data from Laravel with formatted fields
                const rawData = @json($laporan_gangguan);
                this.allGangguan = rawData.map(gangguan => ({
                    ...gangguan,
                    foto_url: '{{ asset('storage') }}/' + gangguan.foto,
                    waktu_formatted: new Date(gangguan.waktu_lapor).toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    }),
                    deskripsi_short: gangguan.deskripsi && gangguan.deskripsi.length > 50 
                        ? gangguan.deskripsi.substring(0, 50) + '...'
                        : gangguan.deskripsi || ''
                }));
            }
        }))
    })
</script>

@endsection