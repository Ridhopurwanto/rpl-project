@extends('layouts.app')

@section('header-left')
    <a href="{{ route('anggota.kendaraan.index') }}" class="bg-[#2a4a6f] text-white text-sm font-semibold px-6 py-2 rounded-full shadow-md">
        KENDARAAN
    </a>
@endsection

@section('content')
<div class="w-full min-h-screen bg-slate-100 p-4 pb-32" 
     x-data="{ 
        modalCheckoutOpen: false, 
        selectedVehicleId: null,
        selectedVehicleNopol: '',
        selectedVehicleStatus: '',
        showCreateModal: false 
     }">

    {{-- 1. BAGIAN KENDARAAN AKTIF --}}
    <div class="mb-4" x-data="{ isOpen: true }">
        <div @click="isOpen = !isOpen" class="text-lg font-bold text-slate-700 uppercase cursor-pointer list-none flex items-center">
            <svg class="w-5 h-5 mr-2 transition-transform duration-300 ease-in-out" 
                 :class="isOpen ? 'rotate-0' : '-rotate-90'" 
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
            KENDARAAN (DI DALAM) :
        </div>
        
        <div x-show="isOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform -translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform -translate-y-2"
             class="mt-2 space-y-3">
            
            @forelse($kendaraan_aktif as $log)
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                    {{-- Header Card dengan Gradient --}}
                    <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] px-4 py-2.5 flex justify-between items-center">
                        <div>
                            <p class="text-xs text-blue-200 font-semibold uppercase">Nomor Kendaraan</p>
                            <p class="text-white font-bold text-base uppercase">{{ $log->nopol }}</p>
                        </div>
                        
                        {{-- Badge Tipe --}}
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
                            {{ $log->tipe }}
                        </span>
                    </div>

                    {{-- Body Card --}}
                    <div class="p-4 space-y-3">
                        {{-- Nama Pemilik --}}
                        <div class="flex items-center gap-3 pb-3 border-b border-gray-100">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-[10px] text-gray-500 font-semibold uppercase">Pemilik</p>
                                <p class="text-gray-800 font-bold text-base">{{ $log->pemilik }}</p>
                            </div>
                        </div>

                        {{-- Info Grid --}}
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <p class="text-gray-500 text-xs uppercase mb-1">Waktu Masuk</p>
                                <p class="text-gray-800 font-semibold">{{ $log->waktu_masuk->format('H:i') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs uppercase mb-1">Keterangan</p>
                                <form action="{{ route('anggota.kendaraan.updateKeterangan', ['id_kendaraan_log' => $log->id_log]) }}" method="POST">
                                    @csrf @method('PUT')
                                    <select name="keterangan" onchange="this.form.submit()" 
                                            class="w-full border border-gray-300 rounded px-2 py-1 text-xs font-semibold focus:outline-blue-500 focus:ring-2 focus:ring-blue-300">
                                        <option value="Tidak Menginap" @if($log->keterangan == 'Tidak Menginap') selected @endif>Tidak Menginap</option>
                                        <option value="Menginap" @if($log->keterangan == 'Menginap') selected @endif>Menginap</option>
                                    </select>
                                </form>
                            </div>
                        </div>

                        {{-- Tombol Keluar --}}
                        <button 
                            @click.prevent="
                                modalCheckoutOpen = true; 
                                selectedVehicleId = '{{ $log->id_log }}';
                                selectedVehicleNopol = '{{ $log->nopol }}';
                                selectedVehicleStatus = '{{ $log->keterangan }}';
                            "
                            class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold py-2.5 px-4 rounded-lg shadow-md transition-all transform hover:scale-[1.02] flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            <span class="text-sm">KELUAR</span>
                        </button>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl shadow-md p-8 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                    </div>
                    <p class="text-gray-500 font-semibold">Tidak ada kendaraan di dalam.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- 2. BAGIAN RIWAYAT KENDARAAN --}}
    <div class="mb-4" x-data="{ isOpen: true }">
        <div @click="isOpen = !isOpen" class="text-lg font-bold text-slate-700 uppercase cursor-pointer flex items-center list-none select-none">
            <svg class="w-5 h-5 mr-2 transition-transform duration-300 ease-in-out"
                :class="isOpen ? 'rotate-0' : '-rotate-90'"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
            RIWAYAT :
        </div>
        
        <div x-show="isOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2">
            
            {{-- Filter Riwayat --}}
            <div class="bg-white rounded-lg shadow-md p-4 mt-2">
                <form action="{{ route('anggota.kendaraan.index') }}" method="GET" id="filterForm">
                    <div class="flex flex-col md:flex-row gap-4 items-end">
                        <div class="flex-1 w-full">
                            <label for="tanggal" class="block text-sm font-bold text-slate-600 uppercase mb-1">TANGGAL :</label>
                            <input 
                                type="date" 
                                id="tanggal"
                                name="tanggal"
                                value="{{ $tanggal_terpilih }}"
                                @change="document.getElementById('filterForm').submit()"
                                class="w-full bg-[#2a4a6f] text-white px-4 py-2 rounded-lg shadow border-none focus:outline-none focus:ring-2 focus:ring-blue-400"
                                style="color-scheme: dark;">
                        </div>
                        <div class="flex-1 w-full">
                            <label for="nopol" class="block text-sm font-bold text-slate-600 uppercase mb-1">CARI NOPOL :</label>
                            <input 
                                type="text" 
                                id="nopol"
                                name="nopol"
                                value="{{ $nopol_filter ?? '' }}" 
                                placeholder="Contoh: AB 1234"
                                @input.debounce.500ms="document.getElementById('filterForm').submit()"
                                class="w-full bg-[#2a4a6f] text-white px-4 py-2 rounded-lg shadow border-none focus:outline-none focus:ring-2 focus:ring-blue-400 placeholder-gray-300 uppercase">
                        </div>
                    </div>
                </form>
            </div>

            {{-- Card Riwayat --}}
            <div class="mt-2 space-y-3">
                @forelse($riwayat_kendaraan as $log)
                    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                        {{-- Header Card dengan Gradient --}}
                        <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] px-4 py-2.5 flex justify-between items-center">
                            <div>
                                <p class="text-xs text-blue-200 font-semibold uppercase">Nomor Kendaraan</p>
                                <p class="text-white font-bold text-base uppercase">{{ $log->nopol }}</p>
                            </div>
                            
                            {{-- Badge Status Keterangan --}}
                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase 
                                        @if($log->keterangan == 'Menginap') bg-red-500 text-white @else bg-blue-100 text-blue-700 @endif">
                                {{ $log->keterangan }}
                            </span>
                        </div>

                        {{-- Body Card --}}
                        <div class="p-4 space-y-3">
                            {{-- Nama Pemilik --}}
                            <div class="flex items-center gap-3 pb-3 border-b border-gray-100">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
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

                            {{-- Info Grid --}}
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div>
                                    <p class="text-gray-500 text-xs uppercase mb-1">Lama Parkir</p>
                                    <p class="text-gray-800 font-semibold">{{ $log->waktu_masuk->diffForHumans($log->waktu_keluar, true) }}</p>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
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
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
    </button>

    {{-- 4. MODAL CREATE KENDARAAN - SAMA SEPERTI SEBELUMNYA --}}
    <div x-show="showCreateModal" class="relative z-50" style="display: none;">
        <div x-show="showCreateModal"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black bg-opacity-75 transition-opacity"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="showCreateModal"
                     @click.away="showCreateModal = false"
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
                        <button @click="showCreateModal = false" class="text-gray-300 hover:text-white transition-colors focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <form action="{{ route('anggota.kendaraan.store') }}" method="POST" @click.outside="suggestions = []">
                        @csrf

                        <div class="grid grid-cols-3 gap-x-4 gap-y-5">
                            <label class="col-span-1 text-gray-300 font-semibold text-sm self-center">PLAT NOMOR :</label>
                            <div class="col-span-2 relative">
                                <input 
                                    type="text" name="nopol" placeholder="AB 1234 XY" required autocomplete="off"
                                    x-model="nopol" 
                                    @input.debounce.350ms="getSuggestions()"
                                    class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500 uppercase">
                                
                                <div x-show="suggestions.length > 0" 
                                     x-transition
                                     class="absolute top-full left-0 right-0 bg-white border border-gray-300 rounded-md shadow-lg mt-1 z-50 max-h-48 overflow-y-auto">
                                    <template x-for="suggestion in suggestions" :key="suggestion.id_kendaraan">
                                        <div @click="selectSuggestion(suggestion)" class="px-4 py-2 text-gray-800 hover:bg-blue-100 cursor-pointer text-sm font-semibold">
                                            <span x-text="suggestion.nomor_plat"></span> - <span x-text="suggestion.pemilik" class="font-normal text-gray-600"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <label class="col-span-1 text-gray-300 font-semibold text-sm self-center">PEMILIK :</label>
                            <div class="col-span-2">
                                <input 
                                    type="text" name="pemilik" placeholder="Nama Pemilik" required 
                                    x-model="pemilik"
                                    :readonly="isRegistered"
                                    :class="isRegistered ? 'bg-gray-300 text-gray-600 cursor-not-allowed' : 'bg-white text-gray-900'"
                                    class="w-full px-4 py-2 rounded-md border-none focus:ring-2 focus:ring-blue-500 transition-colors">
                            </div>

                            <label class="col-span-1 text-gray-300 font-semibold text-sm self-center">TIPE :</label>
                            <div class="col-span-2">
                                <div class="relative">
                                    <select 
                                        name="tipe" x-model="tipe" required 
                                        :class="isRegistered ? 'bg-gray-300 text-gray-600 pointer-events-none' : 'bg-white text-gray-900'"
                                        class="w-full px-4 py-2 rounded-md border-none focus:ring-2 focus:ring-blue-500 transition-colors">
                                        <option value="Roda 2">Roda 2</option>
                                        <option value="Roda 4">Roda 4</option>
                                    </select>
                                    <input type="hidden" name="tipe" x-model="tipe" x-if="isRegistered">
                                </div>
                            </div>

                            <label class="col-span-1 text-gray-300 font-semibold text-sm self-center">KETERANGAN :</label>
                            <div class="col-span-2">
                                <select name="keterangan" required class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500">
                                    <option value="Tidak Menginap">Tidak Menginap</option>
                                    <option value="Menginap">Menginap</option>
                                </select>
                            </div>

                            <label class="col-span-1 text-gray-300 font-semibold text-sm self-center">TANGGAL :</label>
                            <div class="col-span-2">
                                <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <label class="col-span-1 text-gray-300 font-semibold text-sm self-center">WAKTU :</label>
                            <div class="col-span-2">
                                <input type="time" name="waktu" value="{{ date('H:i') }}" required class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="mt-8">
                            <button type="submit" class="w-full bg-green-600 text-white font-bold py-3 px-6 rounded-lg shadow-md hover:bg-green-700 transition-colors duration-300">
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
                <div class="relative transform overflow-hidden rounded-xl bg-[#2a4a6f] text-left shadow-xl transition-all sm:my-8 w-full max-w-sm">
                    <form :action="`/anggota/kendaraan/checkout/${selectedVehicleId}`" method="POST">
                        @csrf @method('PUT')
                        <input type="hidden" name="menginap" :value="selectedVehicleStatus === 'Menginap' ? '1' : '0'">

                        <div class="p-6 flex flex-col items-center text-white">
                            <h3 class="text-xl font-bold mb-2 uppercase">KONFIRMASI KELUAR</h3>
                            
                            <div class="bg-white/10 rounded-lg p-4 w-full text-center mb-6 border border-white/20">
                                <p class="text-sm text-gray-300 mb-1">Plat Nomor</p>
                                <p class="text-2xl font-bold uppercase tracking-wider mb-3" x-text="selectedVehicleNopol"></p>
                                
                                <p class="text-sm text-gray-300 mb-1">Status</p>
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide"
                                      :class="selectedVehicleStatus === 'Menginap' ? 'bg-red-500 text-white' : 'bg-blue-500 text-white'"
                                      x-text="selectedVehicleStatus">
                                </span>
                            </div>

                            <div class="w-full space-y-3">
                                <button type="submit" class="w-full py-3 px-4 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg shadow-lg transition-colors duration-200">
                                    YA, KENDARAAN KELUAR
                                </button>
                                
                                <button type="button" @click="modalCheckoutOpen = false" class="w-full py-2 text-gray-300 hover:text-white text-sm underline">
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
