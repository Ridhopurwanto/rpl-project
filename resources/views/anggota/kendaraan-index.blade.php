@extends('layouts.app')

@section('header-left')
    <a href="{{ route('anggota.kendaraan.index') }}" class="bg-[#2a4a6f] text-white text-sm font-semibold px-6 py-2 rounded-full shadow-md">
        KENDARAAN
    </a>
@endsection

@section('content')
{{-- 
  Alpine Data Utama:
  1. modalCheckoutOpen: Untuk pop-up konfirmasi keluar
  2. selectedVehicleId: ID kendaraan yang akan keluar
  3. showCreateModal: Untuk pop-up tambah kendaraan baru
--}}
<div class="w-full min-h-screen bg-slate-100 p-4 pb-32" 
     x-data="{ 
        modalCheckoutOpen: false, 
        selectedVehicleId: null,
        selectedVehicleNopol: '',
        selectedVehicleStatus: '',
        showCreateModal: false 
     }">

    {{-- 1. BAGIAN KENDARAAN AKTIF --}}
    <details open class="mb-4">
        <summary class="text-lg font-bold text-slate-700 uppercase cursor-pointer list-none flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            KENDARAAN (DI DALAM) :
        </summary>
        
        <div class="bg-white rounded-lg shadow-md p-4 mt-2 overflow-x-auto">
            <table class="w-full min-w-[600px] text-sm text-left">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="py-3 px-4">No</th>
                        <th class="py-3 px-4">Nopol</th>
                        <th class="py-3 px-4">Pemilik</th>
                        <th class="py-3 px-4">Tipe</th>
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4">Ket.</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($kendaraan_aktif as $log)
                    <tr class="bg-white hover:bg-gray-50">
                        <td class="py-3 px-4 font-medium">{{ $loop->iteration }}.</td>
                        <td class="py-3 px-4 font-medium uppercase">{{ $log->nopol }}</td>
                        <td class="py-3 px-4">{{ $log->pemilik }}</td>
                        <td class="py-3 px-4">{{ $log->tipe }}</td>
                        <td class="py-3 px-4">{{ $log->waktu_masuk->format('H:i') }}</td>
                        
                        {{-- Dropdown Ubah Keterangan --}}
                        <td class="py-3 px-4">
                            <form action="{{ route('anggota.kendaraan.updateKeterangan', ['id_kendaraan_log' => $log->id_log]) }}" method="POST">
                                @csrf @method('PUT')
                                <select name="keterangan" onchange="this.form.submit()" class="border border-gray-300 rounded px-2 py-1 text-xs focus:outline-blue-500">
                                    <option value="Tidak Menginap" @if($log->keterangan == 'Tidak Menginap') selected @endif>Tidak Menginap</option>
                                    <option value="Menginap" @if($log->keterangan == 'Menginap') selected @endif>Menginap</option>
                                </select>
                            </form>
                        </td>
                        
                        <td class="py-3 px-4 text-center">
                            <button 
                                @click.prevent="
                                    modalCheckoutOpen = true; 
                                    selectedVehicleId = '{{ $log->id_log }}';
                                    selectedVehicleNopol = '{{ $log->nopol }}';
                                    selectedVehicleStatus = '{{ $log->keterangan }}';
                                "
                                class="bg-[#2a4a6f] text-white text-xs font-bold uppercase px-4 py-2 rounded-md shadow hover:bg-blue-700 transition-colors">
                                Keluar
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="py-4 px-4 text-center text-gray-500">Tidak ada kendaraan di dalam.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </details>

    {{-- 2. BAGIAN RIWAYAT KENDARAAN --}}
    <details open class="mb-4">
        <summary class="text-lg font-bold text-slate-700 uppercase cursor-pointer list-none flex items-center">
             <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            RIWAYAT :
        </summary>
        
        {{-- Filter Riwayat (Tanggal & Nopol) --}}
        <div class="bg-white rounded-lg shadow-md p-4 mt-2">
            <form action="{{ route('anggota.kendaraan.index') }}" method="GET">
                <div class="flex flex-col md:flex-row gap-4 items-end">
                    
                    {{-- Input Tanggal --}}
                    <div class="flex-1 w-full">
                        <label for="tanggal" class="block text-sm font-bold text-slate-600 uppercase mb-1">TANGGAL :</label>
                        <input 
                            type="date" 
                            id="tanggal"
                            name="tanggal"
                            value="{{ $tanggal_terpilih }}"
                            class="w-full bg-[#2a4a6f] text-white px-4 py-2 rounded-lg shadow border-none focus:outline-none focus:ring-2 focus:ring-blue-400"
                            style="color-scheme: dark;"
                        >
                    </div>

                    {{-- Input Cari Nopol (Baru) --}}
                    <div class="flex-1 w-full">
                        <label for="nopol" class="block text-sm font-bold text-slate-600 uppercase mb-1">CARI NOPOL :</label>
                        <input 
                            type="text" 
                            id="nopol"
                            name="nopol"
                            value="{{ $nopol_filter ?? '' }}" 
                            placeholder="Contoh: AB 1234"
                            class="w-full bg-[#2a4a6f] text-white px-4 py-2 rounded-lg shadow border-none focus:outline-none focus:ring-2 focus:ring-blue-400 placeholder-gray-300 uppercase"
                        >
                    </div>

                    {{-- Tombol Filter --}}
                    <div class="w-full md:w-auto">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow transition-colors duration-200 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            FILTER
                        </button>
                    </div>

                </div>
            </form>
        </div>

        {{-- Tabel Riwayat --}}
        <div class="bg-white rounded-lg shadow-md p-4 mt-2 overflow-x-auto">
            <table class="w-full min-w-[600px] text-sm text-left">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="py-3 px-4">No</th>
                        <th class="py-3 px-4">Nopol</th>
                        <th class="py-3 px-4">Pemilik</th>
                        <th class="py-3 px-4">Tipe</th>
                        <th class="py-3 px-4">Lama</th>
                        <th class="py-3 px-4">Keluar</th>
                        <th class="py-3 px-4">Ket.</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($riwayat_kendaraan as $log)
                    <tr class="bg-white">
                        <td class="py-3 px-4 font-medium">{{ $loop->iteration }}.</td>
                        <td class="py-3 px-4 font-medium uppercase">{{ $log->nopol }}</td>
                        <td class="py-3 px-4">{{ $log->pemilik }}</td>
                        <td class="py-3 px-4">{{ $log->tipe }}</td>
                        <td class="py-3 px-4">{{ $log->waktu_masuk->diffForHumans($log->waktu_keluar, true) }}</td>
                        <td class="py-3 px-4">{{ $log->waktu_keluar->format('H:i') }}</td>
                        <td class="py-3 px-4 font-semibold @if($log->keterangan == 'Menginap') text-red-600 @else text-blue-600 @endif">
                            {{ $log->keterangan }}
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="py-4 px-4 text-center text-gray-500">Tidak ada riwayat kendaraan pada tanggal ini.</td></tr>
                    @endforelse
                </tbody>        
            </table>
        </div>
    </details>


    {{-- 3. TOMBOL FAB (CREATE) --}}
    <button @click.prevent="showCreateModal = true" 
            class="fixed bottom-24 right-4 bg-[#2a4a6f] text-white rounded-full w-16 h-16 flex items-center justify-center shadow-lg transform hover:scale-110 transition-transform z-40 cursor-pointer">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
    </button>


    {{-- ================= 4. MODAL CREATE KENDARAAN (POP-UP) ================= --}}
    <div x-show="showCreateModal" class="relative z-50" style="display: none;">
        
        {{-- Backdrop --}}
        <div x-show="showCreateModal"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black bg-opacity-75 transition-opacity"></div>

        {{-- Scroll Wrapper --}}
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                
                {{-- Card Modal --}}
                <div x-show="showCreateModal"
                     @click.away="showCreateModal = false"
                     class="relative transform overflow-visible rounded-xl bg-[#2a4a6f] text-left shadow-xl transition-all sm:my-8 w-full max-w-md p-6"
                     
                     {{-- LOGIKA ALPINE --}}
                     x-data="{
                        nopol: '',
                        pemilik: '',
                        tipe: 'Roda 2',
                        suggestions: [],
                        loading: false,
                        isRegistered: false, // STATE BARU: Penanda jika kendaraan sudah terdaftar

                        // Fungsi pilih saran
                        selectSuggestion(suggestion) {
                            this.nopol = suggestion.nomor_plat;
                            this.pemilik = suggestion.pemilik;
                            this.tipe = suggestion.tipe;
                            this.isRegistered = true; // Kunci input karena data dari database
                            this.suggestions = []; 
                        },

                        // Fungsi ambil data ke server
                        async getSuggestions() {
                            // Reset status registered setiap kali mengetik
                            // Agar jika user menghapus/mengganti karakter, form kembali terbuka
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

                                // Cek exact match (sama persis)
                                const exactMatch = data.find(s => s.nomor_plat.toLowerCase() === this.nopol.toLowerCase());
                                if (exactMatch) {
                                    this.pemilik = exactMatch.pemilik;
                                    this.tipe = exactMatch.tipe;
                                    this.isRegistered = true; // Kunci input otomatis jika cocok persis
                                }
                            } catch (error) {
                                console.error(error);
                            } finally {
                                this.loading = false;
                            }
                        }
                     }"
                >
                    {{-- Tombol Close (X) --}}
                    <div class="flex justify-end mb-4">
                        <button @click="showCreateModal = false" class="text-gray-300 hover:text-white transition-colors focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <form action="{{ route('anggota.kendaraan.store') }}" method="POST" @click.outside="suggestions = []">
                        @csrf

                        <div class="grid grid-cols-3 gap-x-4 gap-y-5">

                            {{-- Input NOPOL (Autocomplete) --}}
                            <label class="col-span-1 text-gray-300 font-semibold text-sm self-center">PLAT NOMOR :</label>
                            <div class="col-span-2 relative">
                                <input 
                                    type="text" name="nopol" placeholder="AB 1234 XY" required autocomplete="off"
                                    x-model="nopol" 
                                    @input.debounce.350ms="getSuggestions()"
                                    class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500 uppercase"
                                >
                                {{-- Dropdown Suggestion --}}
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

                            {{-- Input PEMILIK --}}
                            <label class="col-span-1 text-gray-300 font-semibold text-sm self-center">PEMILIK :</label>
                            <div class="col-span-2">
                                <input 
                                    type="text" name="pemilik" placeholder="Nama Pemilik" required 
                                    x-model="pemilik"
                                    :readonly="isRegistered"
                                    :class="isRegistered ? 'bg-gray-300 text-gray-600 cursor-not-allowed' : 'bg-white text-gray-900'"
                                    class="w-full px-4 py-2 rounded-md border-none focus:ring-2 focus:ring-blue-500 transition-colors"
                                >
                            </div>

                            {{-- Input TIPE --}}
                            <label class="col-span-1 text-gray-300 font-semibold text-sm self-center">TIPE :</label>
                            <div class="col-span-2">
                                {{-- 
                                    Kita gunakan pointer-events-none untuk select jika registered, 
                                    karena readonly tidak bekerja penuh pada tag select di beberapa browser.
                                    Ditambah input hidden agar data tetap terkirim jika select disabled.
                                --}}
                                <div class="relative">
                                    <select 
                                        name="tipe" x-model="tipe" required 
                                        :class="isRegistered ? 'bg-gray-300 text-gray-600 pointer-events-none' : 'bg-white text-gray-900'"
                                        class="w-full px-4 py-2 rounded-md border-none focus:ring-2 focus:ring-blue-500 transition-colors"
                                    >
                                        <option value="Roda 2">Roda 2</option>
                                        <option value="Roda 4">Roda 4</option>
                                    </select>
                                    
                                    {{-- Input hidden jika select dikunci, agar value tetap terkirim --}}
                                    <input type="hidden" name="tipe" x-model="tipe" x-if="isRegistered">
                                </div>
                            </div>

                            {{-- Input KETERANGAN --}}
                            <label class="col-span-1 text-gray-300 font-semibold text-sm self-center">KETERANGAN :</label>
                            <div class="col-span-2">
                                <select name="keterangan" required class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500">
                                    <option value="Tidak Menginap">Tidak Menginap</option>
                                    <option value="Menginap">Menginap</option>
                                </select>
                            </div>

                            {{-- Input TANGGAL --}}
                            <label class="col-span-1 text-gray-300 font-semibold text-sm self-center">TANGGAL :</label>
                            <div class="col-span-2">
                                <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            {{-- Input WAKTU --}}
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


    {{-- ================= 5. MODAL KONFIRMASI KELUAR (CHECKOUT) ================= --}}
    {{-- 
         Update: Modal hanya menampilkan konfirmasi dengan detail kendaraan.
         Hanya ada 1 tombol "YA, KELUAR". Status Menginap/Tidak diambil dari selectedVehicleStatus.
    --}}
    <div x-show="modalCheckoutOpen" 
         class="relative z-50"
         style="display: none;">
        
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="modalCheckoutOpen = false"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center">
                <div class="relative transform overflow-hidden rounded-xl bg-[#2a4a6f] text-left shadow-xl transition-all sm:my-8 w-full max-w-sm">
                    
                    <form :action="`/anggota/kendaraan/checkout/${selectedVehicleId}`" method="POST">
                        @csrf @method('PUT')
                        
                        {{-- Hidden input untuk memastikan status 'menginap' terkirim ke controller sesuai logika sebelumnya --}}
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