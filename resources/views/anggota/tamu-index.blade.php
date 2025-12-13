@extends('layouts.app')

@section('header-left')
    <a class="flex items-center border-2 border-[#1a2847] text-[#1a2847] text-sm font-bold px-4 py-2 rounded-full">
        TAMU
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

    <div class="w-full min-h-screen bg-slate-100 p-4 pb-32" 
         x-data="{ 
            showCreateModal: false,
            showSuccessNotif: {{ session('success') ? 'true' : 'false' }},
            showErrorNotif: {{ session('error') ? 'true' : 'false' }},
            errorMessage: '{{ session('error') }}', 
            
            // Fungsi Trigger Error Client-Side (untuk Validasi Tanggal)
            triggerError(message) {
                this.errorMessage = message;
                this.showErrorNotif = true;
                setTimeout(() => this.showErrorNotif = false, 5000);
            }
    }">

        {{-- 1. Floating Notification Success --}}
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
            <button @click="showSuccessNotif = false" class="relative flex-shrink-0 text-white hover:text-green-100 transition-colors w-10 h-10 flex items-center justify-center">
                <svg class="absolute inset-0 w-full h-full p-1" viewBox="0 0 36 36">
                     <path class="text-green-700/40 timer-circle" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"></path>
                     <path class="text-white timer-circle animate-timer" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" stroke="currentColor"></path>
                </svg>
                <svg class="w-4 h-4 relative z-10" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>

        {{-- 2. Floating Notification Error/Warning (Bisa dari Session ATAU Validasi JS) --}}
        <div x-show="showErrorNotif" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-x-full"
             x-transition:enter-end="opacity-100 transform translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-x-0"
             x-transition:leave-end="opacity-0 transform translate-x-full"
             x-init="if(showErrorNotif) setTimeout(() => showErrorNotif = false, 5000)"
             class="fixed top-4 right-4 z-50 bg-yellow-500 text-white pl-6 pr-2 py-1 rounded-lg shadow-2xl flex items-center gap-3 min-w-[300px] max-w-xs"
             style="display: none;">
            <div class="flex-shrink-0">
                {{-- Icon Warning Triangle --}}
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <div class="flex-1">
                {{-- Text Error Dinamis --}}
                <p class="font-bold text-sm" x-text="errorMessage"></p>
            </div>
            <button @click="showErrorNotif = false" class="relative flex-shrink-0 text-white hover:text-yellow-100 transition-colors w-10 h-10 flex items-center justify-center">
                {{-- Timer Circle (Warna Kuning Gelap) --}}
                <svg class="absolute inset-0 w-full h-full p-1" viewBox="0 0 36 36">
                     <path class="text-yellow-700/40 timer-circle" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"></path>
                     <path class="text-white timer-circle animate-timer" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" stroke="currentColor"></path>
                </svg>
                <svg class="w-4 h-4 relative z-10" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>

    <div class="w-full min-h-screen bg-slate-100 p-4 pb-32" x-data="{ showCreateModal: false }">

        {{-- KOTAK FILTER RENTANG TANGGAL --}}
        <div class="bg-white px-6 py-5 rounded-xl shadow-sm mb-6 border border-gray-200">
            <form action="{{ route('anggota.tamu.index') }}" method="GET" id="filterForm"
                x-data="{ 
                      start: '{{ $startDate }}', 
                      end: '{{ $endDate }}',
                      
                      // Logika Validasi sebelum Submit
                      validateAndSubmit() {
                          if (this.start > this.end) {
                              // Panggil fungsi error dari parent component (x-data utama)
                              $data.triggerError('Tanggal awal tidak boleh melebihi tanggal akhir!');
                              return; // Batalkan submit
                          }
                          document.getElementById('filterForm').submit();
                      }
                  }">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative">
                    
                    {{-- Filter Dari Tanggal --}}
                    <div class="w-full">
                        <label for="start_date" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                            Dari Tanggal
                        </label>
                        <div class="cursor-pointer" @click="$refs.dateStart.showPicker()">
                            <input 
                                @change="validateAndSubmit()"
                                type="date" 
                                id="start_date"
                                name="start_date"
                                x-ref="dateStart"
                                x-model="start"
                                class="block w-full h-[42px] px-4 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer">
                        </div>
                    </div>

                    {{-- Filter Sampai Tanggal --}}
                    <div class="w-full">
                        <label for="end_date" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                            Sampai Tanggal
                        </label>
                        <div class="cursor-pointer" @click="$refs.dateEnd.showPicker()">
                            <input 
                                @change="validateAndSubmit()"
                                type="date" 
                                id="end_date"
                                name="end_date"
                                x-ref="dateEnd"
                                x-model="end"
                                class="block w-full h-[42px] px-4 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer">
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- CARD LAYOUT RIWAYAT TAMU --}}
        <div class="space-y-3">
            @forelse($riwayat_tamu as $tamu)
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                    
                    {{-- Header: Nama Tamu & Tanggal --}}
                    <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] px-4 py-2.5 flex justify-between items-center">
                        <div>
                            <p class="text-xs text-blue-200 font-semibold uppercase">Nama Tamu</p>
                            <p class="text-white font-bold text-base">{{ $tamu->nama_tamu }}</p>
                        </div>
                        <span class="bg-blue-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg">
                            {{ $tamu->waktu_datang->format('d/m/Y') }}
                        </span>
                    </div>

                    {{-- Body: Info Detail --}}
                    <div class="p-4 space-y-3">
                        
                        {{-- Instansi --}}
                        <div class="flex items-center gap-2 pb-2 border-b border-gray-100">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <div class="flex-1">
                                <p class="text-[10px] text-gray-500 font-semibold uppercase">Instansi</p>
                                <p class="text-gray-800 font-bold text-sm">{{ $tamu->instansi }}</p>
                            </div>
                        </div>

                        {{-- Waktu & No Identitas --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <p class="text-[10px] text-gray-500 font-semibold uppercase mb-1">Waktu</p>
                                <p class="text-gray-800 font-bold text-sm">{{ $tamu->waktu_datang->format('H:i') }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-500 font-semibold uppercase mb-1">No. Identitas</p>
                                <p class="text-gray-800 font-bold text-sm">{{ !empty($tamu->no_identitas) ? $tamu->no_identitas : '-' }}</p>
                            </div>
                        </div>

                        {{-- Tujuan --}}
                        <div class="pt-2 border-t border-gray-100">
                            <p class="text-[10px] text-gray-500 font-semibold uppercase mb-1">Tujuan Kunjungan</p>
                            <p class="text-gray-800 text-sm leading-relaxed">{{ $tamu->tujuan }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl shadow-md p-8 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-500 font-semibold">Tidak ada riwayat tamu pada rentang tanggal ini.</p>
                </div>
            @endforelse
        </div>

        {{-- Tombol FAB Tambah --}}
        <button @click.prevent="showCreateModal = true" 
                class="fixed bottom-24 right-4 bg-[#2a4a6f] text-white rounded-full w-16 h-16 flex items-center justify-center shadow-lg transform hover:scale-110 transition-transform z-40 cursor-pointer">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
        </button>

        {{-- MODAL CREATE TAMU --}}
        <div x-show="showCreateModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
            @click.away="showCreateModal = false"
            style="display: none;"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">

            <div class="w-full max-w-md bg-[#2a4a6f] rounded-xl shadow-lg p-6" @click.stop>
                
                <div class="flex justify-end mb-4">
                    <button @click="showCreateModal = false" class="text-gray-300 hover:text-white transition-colors focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form action="{{ route('anggota.tamu.store') }}" method="POST">
                    @csrf

                    <div class="space-y-4 sm:space-y-5">

                        {{-- NAMA --}}
                        <div class="flex flex-col sm:flex-row gap-1 sm:gap-3">
                            <label for="nama_tamu" class="text-gray-300 font-semibold text-sm sm:w-40 sm:flex-shrink-0 sm:pt-2">
                                NAMA :
                            </label>
                            <div class="sm:flex-1">
                                <input 
                                    type="text" 
                                    id="nama_tamu" 
                                    name="nama_tamu" 
                                    placeholder="Contoh: Pak Habibullah"
                                    class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500"
                                    required>
                            </div>
                        </div>

                        {{-- INSTANSI --}}
                        <div class="flex flex-col sm:flex-row gap-1 sm:gap-3">
                            <label for="instansi" class="text-gray-300 font-semibold text-sm sm:w-40 sm:flex-shrink-0 sm:pt-2">
                                INSTANSI :
                            </label>
                            <div class="sm:flex-1">
                                <input 
                                    type="text" 
                                    id="instansi" 
                                    name="instansi" 
                                    placeholder="Contoh: BPS Pusat"
                                    class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500"
                                    required>
                            </div>
                        </div>

                        {{-- NO. IDENTITAS --}}
                        <div class="flex flex-col sm:flex-row gap-1 sm:gap-3">
                            <label for="no_identitas" class="text-gray-300 font-semibold text-sm sm:w-40 sm:flex-shrink-0 sm:pt-2 leading-tight">
                                No. Identitas (NIK, NIP) :
                            </label>
                            <div class="sm:flex-1">
                                <input 
                                    type="text" 
                                    id="no_identitas" 
                                    name="no_identitas" 
                                    placeholder="Contoh: 6402021212120001"
                                    pattern="[0-9]*"
                                    inputmode="numeric"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                    class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        
                        {{-- TANGGAL --}}
                        <div class="flex flex-col sm:flex-row gap-1 sm:gap-3">
                            <label for="tanggal_kunjungan" class="text-gray-300 font-semibold text-sm sm:w-40 sm:flex-shrink-0 sm:pt-2">
                                TANGGAL :
                            </label>
                            <div class="sm:flex-1">
                                <input 
                                    type="date" 
                                    id="tanggal_kunjungan" 
                                    name="tanggal_kunjungan" 
                                    value="{{ date('Y-m-d') }}" 
                                    class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500"
                                    required>
                            </div>
                        </div>

                        {{-- JAM KUNJUNGAN --}}
                        <div class="flex flex-col sm:flex-row gap-1 sm:gap-3">
                            <label for="jam_kunjungan" class="text-gray-300 font-semibold text-sm sm:w-40 sm:flex-shrink-0 sm:pt-2">
                                JAM KUNJUNGAN :
                            </label>
                            <div class="sm:flex-1">
                                <input 
                                    type="time" 
                                    id="jam_kunjungan" 
                                    name="jam_kunjungan" 
                                    value="{{ date('H:i') }}" 
                                    class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500"
                                    required>
                            </div>
                        </div>

                        {{-- TUJUAN --}}
                        <div class="flex flex-col sm:flex-row gap-1 sm:gap-3">
                            <label for="tujuan" class="text-gray-300 font-semibold text-sm sm:w-40 sm:flex-shrink-0 sm:pt-2">
                                TUJUAN :
                            </label>
                            <div class="sm:flex-1">
                                <textarea 
                                    id="tujuan" 
                                    name="tujuan" 
                                    rows="3"
                                    placeholder="Contoh: Wisuda"
                                    class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500"
                                    required></textarea>
                            </div>
                        </div>

                    </div>

                    <div class="mt-6 sm:mt-8">
                        <button 
                            type="submit" 
                            class="w-full bg-green-600 text-white font-bold py-3 px-6 rounded-lg shadow-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-blue-700 transition-colors duration-300">
                            SUBMIT
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection
