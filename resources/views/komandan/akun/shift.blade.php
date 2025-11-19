{{-- 
  File: resources/views/komandan/akun/shift.blade.php
  Versi FINAL: Logic Axios dipindah ke dalam AlpineJS agar tombol Simpan 100% berfungsi
--}}

@extends('layouts.app')

@section('header-left')
    <a class="bg-[#2a4a6f] text-white text-sm font-semibold px-6 py-2 rounded-full shadow-md hover:bg-[#1e3a5c] transition">
        <i class="fas fa-arrow-left mr-2"></i> MANAJEMEN SHIFT
    </a>
@endsection

@section('content')

{{-- Load Axios Library --}}
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

{{-- 
  LOGIKA UTAMA ADA DI SINI
  Kita pindahkan semua fungsi updateShift ke dalam x-data
--}}
<div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20" 
     x-data="{
        isModalOpen: false,
        selectedDate: '',
        selectedDateFormatted: '',
        selectedShift: '',
        selectedUserId: {{ $user->id_pengguna }},
        feedbackMessage: '',
        isSuccess: false,
        isLoading: false,

        // Fungsi untuk membuka modal saat tanggal diklik
        openModal(fullDate, formattedDate, currentShift) {
            this.selectedDate = fullDate;
            this.selectedDateFormatted = formattedDate;
            this.selectedShift = currentShift;
            this.isModalOpen = true;
            this.feedbackMessage = ''; // Reset pesan
        },

        // FUNGSI SIMPAN (Langsung di dalam Alpine)
        saveShift() {
            this.isLoading = true;
            this.feedbackMessage = '';

            const payload = {
                id_pengguna: this.selectedUserId,
                tanggal: this.selectedDate,
                jenis_shift: this.selectedShift,
                _token: '{{ csrf_token() }}' // Token CSRF Laravel
            };

            console.log('Mengirim data:', payload); // Debugging

            axios.post('{{ route('komandan.akun.shift.update') }}', payload)
                .then(response => {
                    if (response.data.success) {
                        this.isSuccess = true;
                        this.feedbackMessage = 'Berhasil disimpan!';
                        
                        // Update UI Kotak Tanggal secara langsung (Tanpa Refresh)
                        // Kita cari elemen ID 'date-YYYY-MM-DD'
                        const cellId = 'date-' + this.selectedDate;
                        const cell = document.getElementById(cellId);
                        
                        if (cell) {
                            // Reset semua kelas warna
                            cell.className = 'rounded-xl flex flex-col items-center justify-center min-h-[50px] md:min-h-[120px] cursor-pointer shadow-sm transition-all transform hover:-translate-y-1 border';
                            
                            // Tambah kelas warna baru sesuai shift
                            if (this.selectedShift === 'Pagi') {
                                cell.classList.add('bg-yellow-400', 'text-gray-900', 'border-yellow-500', 'hover:ring-4', 'hover:ring-yellow-300');
                            } else if (this.selectedShift === 'Malam') {
                                cell.classList.add('bg-blue-500', 'text-white', 'border-blue-600', 'hover:ring-4', 'hover:ring-blue-300');
                            } else {
                                cell.classList.add('bg-red-500', 'text-white', 'border-red-600', 'hover:ring-4', 'hover:ring-red-300');
                            }

                            // Update teks label (span kedua)
                            const labelSpan = cell.querySelector('span.shift-label');
                            if (labelSpan) {
                                labelSpan.textContent = this.selectedShift;
                            }
                            
                            // Update atribut onclick agar shift saat ini terupdate jika dibuka lagi
                            // (Ini agak tricky di DOM murni, tapi visual sudah terupdate)
                        }

                        // Tutup modal setelah 1 detik
                        setTimeout(() => {
                            this.isModalOpen = false;
                            this.feedbackMessage = '';
                        }, 1000);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.isSuccess = false;
                    if (error.response && error.response.data && error.response.data.message) {
                        this.feedbackMessage = error.response.data.message;
                    } else {
                        this.feedbackMessage = 'Gagal menyimpan. Cek koneksi internet.';
                    }
                })
                .finally(() => {
                    this.isLoading = false;
                });
        }
     }">
    
    {{-- Header Profil User --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-start mb-6 md:mb-8 gap-4">
        <div class="flex-shrink-0 mx-auto md:mx-0">
            <div class="w-20 h-20 md:w-24 md:h-24 bg-white rounded-full p-1 shadow-md">
                 @if($user->foto_profil)
                    <img src="{{ asset('storage/' . $user->foto_profil) }}" class="w-full h-full rounded-full object-cover" alt="Foto">
                @else
                    <div class="w-full h-full bg-gray-200 rounded-full flex items-center justify-center text-gray-500">
                        <svg class="w-10 h-10 md:w-12 md:h-12" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                    </div>
                @endif
            </div>
        </div>
        
        <div class="text-center md:text-left">
            <h2 class="text-xl md:text-3xl font-bold text-gray-800 uppercase">{{ $user->nama_lengkap }}</h2>
            <p class="text-sm md:text-base font-bold text-gray-500 tracking-widest mt-1">MANAJEMEN JADWAL SHIFT</p>
        </div>
    </div>

    {{-- Navigasi Bulan --}}
    <div class="flex items-center justify-between mb-4 bg-white p-4 rounded-xl shadow-sm md:mb-6">
        <a href="{{ route('komandan.akun.shift', ['id_pengguna' => $user->id_pengguna, 'bulan' => $prevMonth]) }}" class="p-2 rounded-full hover:bg-gray-100 text-gray-600 hover:text-[#2a4a6f] transition">
            <svg class="w-6 h-6 md:w-8 md:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        
        <div class="text-lg md:text-2xl font-bold text-gray-800 uppercase tracking-wide">
            {{ $bulanTahun->translatedFormat('F Y') }}
        </div>
        
        <a href="{{ route('komandan.akun.shift', ['id_pengguna' => $user->id_pengguna, 'bulan' => $nextMonth]) }}" class="p-2 rounded-full hover:bg-gray-100 text-gray-600 hover:text-[#2a4a6f] transition">
            <svg class="w-6 h-6 md:w-8 md:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </a>
    </div>

    {{-- Grid Kalender --}}
    <div class="bg-white p-4 md:p-6 rounded-xl shadow-md">
        {{-- Header Hari --}}
        <div class="grid grid-cols-7 gap-2 md:gap-4 text-center text-sm md:text-base font-bold text-gray-400 mb-4">
            <div>MING</div><div>SEN</div><div>SEL</div><div>RAB</div><div>KAM</div><div>JUM</div><div>SAB</div>
        </div>

        {{-- Isi Tanggal --}}
        <div class="grid grid-cols-7 gap-2 md:gap-4 text-center text-sm md:text-lg font-bold">
            @foreach($kalender as $hari)
                @if($hari === null)
                    {{-- Kotak Kosong --}}
                    <div class="min-h-[50px] md:min-h-[100px]"></div>
                @else
                    @php
                        $bgClass = 'bg-gray-100 text-gray-800 border border-gray-200'; // Default
                        $hoverClass = 'hover:ring-4 hover:ring-opacity-50 hover:ring-gray-300'; 
                        
                        if ($hari['jenis_shift'] == 'Pagi') {
                            $bgClass = 'bg-yellow-400 text-gray-900 border border-yellow-500';
                            $hoverClass = 'hover:ring-4 hover:ring-opacity-50 hover:ring-yellow-300';
                        }
                        if ($hari['jenis_shift'] == 'Malam') {
                            $bgClass = 'bg-blue-500 text-white border border-blue-600';
                            $hoverClass = 'hover:ring-4 hover:ring-opacity-50 hover:ring-blue-300';
                        }
                        if ($hari['jenis_shift'] == 'Off') {
                            $bgClass = 'bg-red-500 text-white border border-red-600';
                            $hoverClass = 'hover:ring-4 hover:ring-opacity-50 hover:ring-red-300';
                        }
                    @endphp

                    {{-- Kotak Tanggal (Klik memanggil openModal di Alpine) --}}
                    <div @click="openModal('{{ $hari['full_date'] }}', '{{ $hari['tanggal'] }} {{ $bulanTahun->translatedFormat('F Y') }}', '{{ $hari['jenis_shift'] }}')"
                         id="date-{{ $hari['full_date'] }}"
                         class="{{ $bgClass }} {{ $hoverClass }} rounded-xl flex flex-col items-center justify-center min-h-[50px] md:min-h-[120px] cursor-pointer shadow-sm transition-all transform hover:-translate-y-1">
                        
                        <span class="text-lg md:text-2xl">{{ $hari['tanggal'] }}</span>
                        
                        {{-- Label Shift --}}
                        <span class="shift-label hidden md:block text-xs uppercase mt-1 font-normal opacity-80">
                            {{ $hari['jenis_shift'] }}
                        </span>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    {{-- Legenda --}}
    <div class="flex justify-center space-x-4 md:space-x-8 mt-6 text-xs md:text-sm font-bold text-gray-600 bg-white p-4 rounded-full shadow-sm mx-auto w-fit">
        <div class="flex items-center"><span class="w-3 h-3 md:w-4 md:h-4 rounded-full bg-yellow-400 mr-2"></span> Shift Pagi</div>
        <div class="flex items-center"><span class="w-3 h-3 md:w-4 md:h-4 rounded-full bg-blue-500 mr-2"></span> Shift Malam</div>
        <div class="flex items-center"><span class="w-3 h-3 md:w-4 md:h-4 rounded-full bg-red-500 mr-2"></span> Off</div>
    </div>

    {{-- 
      =================================
      MODAL EDIT SHIFT
      =================================
    --}}
    <div x-show="isModalOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-90"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-90"
         class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50 p-4" 
         style="display: none;">
         
        <div class="relative w-full max-w-xs bg-white rounded-2xl shadow-2xl" @click.away="isModalOpen = false">
            
            {{-- Header Modal --}}
            <div class="bg-[#2a4a6f] px-6 py-4 rounded-t-2xl flex justify-between items-center">
                <h5 class="text-lg font-bold text-white tracking-wide">UBAH SHIFT</h5>
                <button type="button" @click="isModalOpen = false" class="text-white hover:text-gray-200 text-2xl">&times;</button>
            </div>
            
            {{-- Body Modal --}}
            <div class="p-6">
                <div class="text-center mb-6">
                    <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Tanggal Terpilih</p>
                    <p class="text-2xl font-bold text-gray-800" x-text="selectedDateFormatted"></p>
                </div>

                {{-- Feedback Message dalam Modal --}}
                <div x-show="feedbackMessage" 
                     :class="isSuccess ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                     class="mb-4 p-2 rounded text-sm text-center font-bold">
                     <span x-text="feedbackMessage"></span>
                </div>
                
                <label class="block text-sm font-bold text-gray-700 mb-3">Pilih Shift:</label>
                <div class="space-y-3">
                    {{-- Pilihan Shift --}}
                    <button @click="selectedShift = 'Pagi'" 
                            :class="selectedShift == 'Pagi' ? 'ring-4 ring-yellow-200 border-yellow-500' : 'border-gray-200 hover:bg-gray-50'"
                            class="w-full flex items-center justify-between p-3 bg-white border-2 rounded-xl transition-all">
                        <div class="flex items-center">
                            <div class="w-4 h-4 rounded-full bg-yellow-400 mr-3"></div>
                            <span class="font-bold text-gray-800">Pagi</span>
                        </div>
                        <span x-show="selectedShift == 'Pagi'" class="text-yellow-600 text-xl font-bold">✓</span>
                    </button>

                    <button @click="selectedShift = 'Malam'" 
                            :class="selectedShift == 'Malam' ? 'ring-4 ring-blue-200 border-blue-500' : 'border-gray-200 hover:bg-gray-50'"
                            class="w-full flex items-center justify-between p-3 bg-white border-2 rounded-xl transition-all">
                        <div class="flex items-center">
                            <div class="w-4 h-4 rounded-full bg-blue-500 mr-3"></div>
                            <span class="font-bold text-gray-800">Malam</span>
                        </div>
                        <span x-show="selectedShift == 'Malam'" class="text-blue-600 text-xl font-bold">✓</span>
                    </button>

                    <button @click="selectedShift = 'Off'" 
                            :class="selectedShift == 'Off' ? 'ring-4 ring-red-200 border-red-500' : 'border-gray-200 hover:bg-gray-50'"
                            class="w-full flex items-center justify-between p-3 bg-white border-2 rounded-xl transition-all">
                        <div class="flex items-center">
                            <div class="w-4 h-4 rounded-full bg-red-500 mr-3"></div>
                            <span class="font-bold text-gray-800">Off</span>
                        </div>
                        <span x-show="selectedShift == 'Off'" class="text-red-600 text-xl font-bold">✓</span>
                    </button>
                </div>
            </div>
            
            {{-- Footer Modal --}}
            <div class="px-6 py-4 bg-gray-50 rounded-b-2xl flex justify-end space-x-3">
                <button type="button" @click="isModalOpen = false" class="px-4 py-2 text-gray-600 font-semibold hover:bg-gray-200 rounded-lg transition">Batal</button>
                
                {{-- Tombol Simpan memanggil saveShift() --}}
                <button type="button" 
                        @click="saveShift()" 
                        :disabled="isLoading"
                        class="px-6 py-2 text-white bg-[#2a4a6f] rounded-lg hover:bg-[#1e3a5c] shadow-lg font-bold transform hover:-translate-y-0.5 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center">
                    <span x-show="!isLoading">SIMPAN</span>
                    <span x-show="isLoading">Menyimpan...</span>
                </button>
            </div>
        </div>
    </div>

</div>
@endsection