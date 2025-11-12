@extends('layouts.app')

@section('header-left')
    <a href="{{ route('anggota.patroli.index') }}" class="p-2">
        <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
    </a>
@endsection

@section('content')
<div class="w-full min-h-screen bg-slate-100 p-4">

    {{-- 
      Kita bungkus card dengan 'x-data' untuk inisialisasi Alpine.js
      Kita akan kelola semua state form di sini.
    --}}
    <div 
        class="w-full max-w-md mx-auto bg-slate-800 rounded-xl shadow-lg p-6"
        x-data="formKendaraan()" 
        @click.outside="suggestions = []" {{-- Sembunyikan suggestion jika klik di luar --}}
    >

        <form action="{{ route('anggota.kendaraan.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-3 gap-x-4 gap-y-5">

                <label for="nopol" class="col-span-1 text-gray-300 font-semibold text-sm self-center">PLAT NOMOR :</label>
                <div class="col-span-2 relative">
                    <input 
                        type="text" 
                        id="nopol" 
                        name="nopol" {{-- 'name' tetap penting untuk submit --}}
                        placeholder="AB 4422 DC"
                        class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                        autocomplete="off"
                        x-model="nopol" {{-- Bind ke state 'nopol' --}}
                        @input.debounce.350ms="getSuggestions()" {{-- Panggil API saat mengetik --}}
                    >
                    
                    {{-- --- KOTAK SUGGESTION --- --}}
                    <div 
                        x-show="suggestions.length > 0" 
                        x-transition
                        class="absolute top-full left-0 right-0 bg-white border border-gray-300 rounded-md shadow-lg mt-1 z-10 max-h-48 overflow-y-auto"
                    >
                        <template x-for="suggestion in suggestions" :key="suggestion.id_kendaraan">
                            <div 
                                @click="selectSuggestion(suggestion)"
                                class="px-4 py-2 text-gray-800 hover:bg-blue-100 cursor-pointer"
                                x-text="suggestion.nomor_plat + ' - ' + suggestion.pemilik"
                            >
                                {{-- Teks diisi oleh x-text --}}
                            </div>
                        </template>
                    </div>
                </div>

                <label for="pemilik" class="col-span-1 text-gray-300 font-semibold text-sm self-center">PEMILIK :</label>
                <div class="col-span-2">
                    <input 
                        type="text" 
                        id="pemilik" 
                        name="pemilik" 
                        placeholder="PAK IBNU"
                        class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                        x-model="pemilik" {{-- Bind ke state 'pemilik' --}}
                    >
                </div>

                <label for="tipe" class="col-span-1 text-gray-300 font-semibold text-sm self-center">TIPE :</label>
                <div class="col-span-2">
                    <select 
                        id="tipe" 
                        name="tipe"
                        class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                        x-model="tipe" {{-- Bind ke state 'tipe' --}}
                    >
                        <option value="Roda 2">Roda 2</option>
                        <option value="Roda 4">Roda 4</option>
                    </select>
                </div>

                <label for="keterangan" class="col-span-1 text-gray-300 font-semibold text-sm self-center">KETERANGAN :</label>
                <div class="col-span-2">
                    <select 
                        id="keterangan" 
                        name="keterangan"
                        class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                        <option value="Tidak Menginap">Tidak Menginap</option>
                        <option value="Menginap">Menginap</option>
                    </select>
                </div>

                <label for="tanggal" class="col-span-1 text-gray-300 font-semibold text-sm self-center">TANGGAL :</label>
                <div class="col-span-2">
                    <input 
                        type="date" 
                        id="tanggal" 
                        name="tanggal"
                        value="{{ date('Y-m-d') }}"
                        class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>

                <label for="waktu" class="col-span-1 text-gray-300 font-semibold text-sm self-center">WAKTU :</label>
                <div class="col-span-2">
                    <input 
                        type="time" 
                        id="waktu" 
                        name="waktu"
                        value="{{ date('H:i') }}"
                        class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>

            </div>

            <div class="mt-8">
                <button 
                    type="submit" 
                    class="w-full bg-gray-500 text-white font-bold py-3 px-6 rounded-lg shadow-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-400 transition-colors duration-300">
                    SUBMIT
                </button>
            </div>

        </form>
    </div>
</div>

{{-- 
  Taruh script ini di akhir section content
  Ini adalah 'otak' dari Alpine.js
--}}
<script>
    function formKendaraan() {
        return {
            // 1. States (data)
            nopol: '',
            pemilik: '',
            tipe: 'Roda 2', // Default value
            suggestions: [],
            loading: false,

            // 2. Methods (fungsi)
            
            // Mengisi field saat suggestion dipilih
            selectSuggestion(suggestion) {
                this.nopol = suggestion.nomor_plat;
                this.pemilik = suggestion.pemilik;
                this.tipe = suggestion.tipe;
                this.suggestions = []; // Sembunyikan kotak suggestion
            },

            // Mengambil data dari server
            async getSuggestions() {
                // Jangan cari jika input terlalu pendek
                if (this.nopol.length < 3) {
                    this.suggestions = [];
                    // Reset pemilik/tipe jika nopol dihapus/diedit
                    this.pemilik = ''; 
                    this.tipe = 'Roda 2';
                    return;
                }

                this.loading = true;

                try {
                    const response = await fetch(`{{ route('anggota.kendaraan.searchNopol') }}?search=${this.nopol}`);
                    const data = await response.json();
                    
                    this.suggestions = data;

                    // --- LOGIKA AUTOFIL ---
                    // Cek jika teks yang diketik = persis salah satu suggestion
                    const exactMatch = data.find(s => s.nomor_plat.toLowerCase() === this.nopol.toLowerCase());
                    
                    if (exactMatch) {
                        // Jika ada, langsung isi (tanpa perlu klik)
                        this.selectSuggestion(exactMatch);
                    } else {
                        // Jika tidak ada yg cocok, pastikan field pemilik/tipe kosong
                        this.pemilik = '';
                        this.tipe = 'Roda 2';
                    }

                } catch (error) {
                    console.error('Error fetching suggestions:', error);
                } finally {
                    this.loading = false;
                }
            }
        }
    }
</script>
@endsection