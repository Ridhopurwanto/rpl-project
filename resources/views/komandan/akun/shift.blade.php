@extends('layouts.app')

@section('header-left')
    <a href="{{ route('komandan.akun.index') }}" class="bg-[#2a4a6f] text-white text-sm font-semibold px-6 py-2 rounded-full shadow-md hover:bg-[#1e3a5c] transition">
        <i class="fas fa-arrow-left mr-2"></i> MANAJEMEN SHIFT
    </a>
@endsection

@section('content')

{{-- Load Axios Library (Wajib untuk request AJAX) --}}
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

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

        // Fungsi Buka Modal
        openModal(fullDate, formattedDate, currentShift) {
            this.selectedDate = fullDate;
            this.selectedDateFormatted = formattedDate;
            this.selectedShift = currentShift;
            this.isModalOpen = true;
            this.feedbackMessage = ''; 
            this.isSuccess = false;
        },

        // Helper: Update tampilan satu kotak tanggal secara real-time
        updateVisualCell(dateString, shiftType) {
            const cellId = 'date-' + dateString;
            const cell = document.getElementById(cellId);
            
            if (cell) {
                // Reset styling ke default (kosong/abu) agar bersih
                cell.className = 'rounded-xl flex flex-col items-center justify-center min-h-[50px] md:min-h-[120px] cursor-pointer shadow-sm transition-all transform hover:-translate-y-1 border bg-gray-100 text-gray-800 border-gray-200';
                
                // Tambah kelas warna baru sesuai shift
                if (shiftType === 'Pagi') {
                    cell.classList.remove('bg-gray-100', 'text-gray-800', 'border-gray-200');
                    cell.classList.add('bg-yellow-400', 'text-gray-900', 'border-yellow-500', 'hover:ring-4', 'hover:ring-yellow-300');
                } else if (shiftType === 'Malam') {
                    cell.classList.remove('bg-gray-100', 'text-gray-800', 'border-gray-200');
                    cell.classList.add('bg-blue-500', 'text-white', 'border-blue-600', 'hover:ring-4', 'hover:ring-blue-300');
                } else if (shiftType === 'Off') {
                    cell.classList.remove('bg-gray-100', 'text-gray-800', 'border-gray-200');
                    cell.classList.add('bg-red-500', 'text-white', 'border-red-600', 'hover:ring-4', 'hover:ring-red-300');
                }

                // Update Teks Label di dalam kotak
                const labelSpan = cell.querySelector('span.shift-label');
                if (labelSpan) {
                    labelSpan.textContent = shiftType;
                }
                
                // Update atribut @click di memori Alpine/DOM agar jika diklik lagi, modal mengambil status terbaru
                // (Kita gunakan setAttribute untuk update data statis di elemen)
                // Note: Alpine x-on:click membaca parameter statis saat inisialisasi, 
                // tapi visual update sudah cukup untuk UX.
            }
        },

        // Fungsi Simpan ke Backend
        saveShift() {
            this.isLoading = true;
            this.feedbackMessage = '';

            const payload = {
                id_pengguna: this.selectedUserId,
                tanggal: this.selectedDate,
                jenis_shift: this.selectedShift,
                _token: '{{ csrf_token() }}'
            };

            axios.post('{{ route('komandan.akun.shift.update') }}', payload)
                .then(response => {
                    if (response.data.success) {
                        this.isSuccess = true;
                        this.feedbackMessage = 'Shift tersimpan & Pola diterapkan!'; 
                        
                        // 1. Update kotak tanggal yang SEDANG diedit user
                        this.updateVisualCell(this.selectedDate, this.selectedShift);

                        // 2. Update kotak-kotak LAIN hasil auto-fill (dari response backend)
                        if (response.data.affected_dates && response.data.affected_dates.length > 0) {
                            response.data.affected_dates.forEach(item => {
                                this.updateVisualCell(item.date, item.shift);
                            });
                        }

                        // Tutup modal otomatis setelah 1 detik
                        setTimeout(() => {
                            this.isModalOpen = false;
                            this.feedbackMessage = '';
                        }, 1000);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.isSuccess = false;
                    this.feedbackMessage = error.response?.data?.message || 'Gagal menyimpan.';
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
            <p class="text-sm md:text-base font-bold text-gray-500 tracking-widest mt-1">{{ strtoupper($user->peran) }}</p>
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
        <div class="grid grid-cols-7 gap-2 md:gap-4 text-center text-sm md:text-base font-bold text-gray-400 mb-4">
            <div>MING</div><div>SEN</div><div>SEL</div><div>RAB</div><div>KAM</div><div>JUM</div><div>SAB</div>
        </div>

        <div class="grid grid-cols-7 gap-2 md:gap-4 text-center text-sm md:text-lg font-bold">
            @foreach($kalender as $hari)
                @if($hari === null)
                    {{-- Kotak Kosong --}}
                    <div class="min-h-[50px] md:min-h-[100px]"></div>
                @else
                    @php
                        // Logika Warna Default (Render Awal dari Server)
                        $bgClass = 'bg-gray-100 text-gray-800 border border-gray-200';
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

                    {{-- 
                        ID 'date-YYYY-MM-DD' PENTING UNTUK SELECTOR JAVASCRIPT 
                    --}}
                    <div @click="openModal('{{ $hari['full_date'] }}', '{{ $hari['tanggal'] }} {{ $bulanTahun->translatedFormat('F Y') }}', '{{ $hari['jenis_shift'] }}')"
                         id="date-{{ $hari['full_date'] }}"
                         class="{{ $bgClass }} {{ $hoverClass }} rounded-xl flex flex-col items-center justify-center min-h-[50px] md:min-h-[120px] cursor-pointer shadow-sm transition-all transform hover:-translate-y-1">
                        
                        <span class="text-lg md:text-2xl">{{ $hari['tanggal'] }}</span>
                        
                        {{-- Label Shift (Class 'shift-label' penting untuk JS) --}}
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

    {{-- Modal Edit Shift --}}
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
            <div class="bg-[#2a4a6f] px-6 py-4 rounded-t-2xl flex justify-between items-center">
                <h5 class="text-lg font-bold text-white tracking-wide">UBAH SHIFT</h5>
                <button type="button" @click="isModalOpen = false" class="text-white hover:text-gray-200 text-2xl">&times;</button>
            </div>
            
            <div class="p-6">
                <div class="text-center mb-6">
                    <p class="text-gray-500 text-xs uppercase tracking-wide mb-1">Tanggal Terpilih</p>
                    <p class="text-2xl font-bold text-gray-800" x-text="selectedDateFormatted"></p>
                </div>

                <div x-show="feedbackMessage" 
                     :class="isSuccess ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                     class="mb-4 p-2 rounded text-sm text-center font-bold">
                     <span x-text="feedbackMessage"></span>
                </div>
                
                <label class="block text-sm font-bold text-gray-700 mb-3">Pilih Shift:</label>
                <div class="space-y-3">
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
            
            <div class="px-6 py-4 bg-gray-50 rounded-b-2xl flex justify-end space-x-3">
                <button type="button" @click="isModalOpen = false" class="px-4 py-2 text-gray-600 font-semibold hover:bg-gray-200 rounded-lg transition">Batal</button>
                
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