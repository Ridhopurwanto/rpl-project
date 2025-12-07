@extends('layouts.app')

@section('header-left')
    <a href="{{ route('komandan.akun.index') }}" class="inline-block border-2 border-[#1a2847] text-[#1a2847] text-sm font-bold px-4 py-1 rounded-full mb-4">
        <i class="fas fa-arrow-left mr-2"></i> MANAJEMEN SHIFT
    </a>
@endsection

@section('content')

{{-- Library Penting --}}
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20" 
     x-data="shiftManager({{ $user->id_pengguna }})">
    
    {{-- 1. Header Profil & Tombol Reset --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 md:mb-8 gap-4">
        <div class="flex items-center gap-4">
            <div class="flex-shrink-0">
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
            
            <div class="text-left">
                <h2 class="text-xl md:text-3xl font-bold text-gray-800 uppercase">{{ $user->nama_lengkap }}</h2>
                <p class="text-sm md:text-base font-bold text-gray-500 tracking-widest mt-1">{{ strtoupper($user->peran) }}</p>
                <span class="inline-block mt-1 px-2 py-1 text-xs font-semibold rounded bg-gray-200 text-gray-700">
                    {{ $user->jenis_jadwal == 'non_shift' ? 'NON-SHIFT' : 'SHIFT' }}
                </span>
            </div>
        </div>

        {{-- Tombol Reset --}}
        <div>
            <button @click="resetSchedule()" class="bg-red-100 text-red-600 hover:bg-red-200 px-4 py-2 rounded-lg font-bold shadow-sm transition flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                RESET JADWAL
            </button>
            <p class="text-[10px] text-gray-400 mt-1 text-right max-w-[150px]">Kosongkan jadwal mulai besok.</p>
        </div>
    </div>

    {{-- 2. Navigasi Bulan (CUSTOM DROPDOWN - EYE CATCHING) --}}
    <div class="flex items-center justify-end mb-6">
        
        {{-- KOTAK 1: Navigation Controls (Today, Prev, Next) --}}
        <div class="flex items-center gap-1 bg-white p-1 rounded-full shadow-sm border border-gray-200">
            {{-- Tombol Today --}}
            <a href="{{ route('komandan.akun.shift', ['id_pengguna' => $user->id_pengguna, 'bulan' => \Carbon\Carbon::now()->format('Y-m')]) }}" 
               class="px-4 py-1.5 text-sm font-semibold text-gray-700 rounded-full hover:bg-gray-100 transition border border-transparent hover:border-gray-300">
                Today
            </a>

            <div class="h-6 w-px bg-gray-300 mx-1"></div>

            {{-- Tombol Previous (<) --}}
            <a href="{{ route('komandan.akun.shift', ['id_pengguna' => $user->id_pengguna, 'bulan' => $prevMonth]) }}" 
               class="w-9 h-9 flex items-center justify-center rounded-full text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>

            {{-- Tombol Next (>) --}}
            <a href="{{ route('komandan.akun.shift', ['id_pengguna' => $user->id_pengguna, 'bulan' => $nextMonth]) }}" 
               class="w-9 h-9 flex items-center justify-center rounded-full text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>

        {{-- KOTAK 2: Custom Dropdown Bulan & Tahun --}}
        <div class="flex items-center gap-1 bg-white p-1 rounded-full shadow-sm border border-gray-200 ml-2 md:ml-4 relative">
            
            {{-- 1. CUSTOM DROPDOWN BULAN --}}
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" @click.outside="open = false" 
                        class="flex items-center justify-center px-4 py-1.5 text-lg md:text-xl font-bold text-gray-800 rounded-full hover:bg-gray-100 transition focus:outline-none">
                    {{ $bulanTahun->translatedFormat('F') }}
                    <svg class="w-4 h-4 ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                {{-- Menu Popover --}}
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute top-full left-0 mt-2 w-48 bg-white rounded-xl shadow-2xl border border-gray-100 z-50 overflow-hidden">
                    
                    <div class="max-h-64 overflow-y-auto py-2 custom-scrollbar">
                        @php
                            $bulanIni = $bulanTahun->format('m');
                            $listBulan = [
                                '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                                '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                                '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                            ];
                        @endphp
                        @foreach($listBulan as $key => $val)
                            <button onclick="pilihBulan('{{ $key }}')" 
                                    class="w-full text-left px-4 py-2 text-sm font-medium transition-colors duration-150
                                    {{ $bulanIni == $key ? 'bg-blue-50 text-[#2a4a6f] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                {{ strtoupper($val) }}
                                @if($bulanIni == $key) <span class="float-right text-[#2a4a6f]">✓</span> @endif
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Garis Pemisah --}}
            <div class="h-6 w-px bg-gray-300 mx-1"></div>

            {{-- 2. CUSTOM DROPDOWN TAHUN --}}
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" @click.outside="open = false" 
                        class="flex items-center justify-center px-4 py-1.5 text-lg md:text-xl font-normal text-gray-500 rounded-full hover:bg-gray-100 transition focus:outline-none">
                    {{ $bulanTahun->format('Y') }}
                    <svg class="w-4 h-4 ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="absolute top-full right-0 mt-2 w-32 bg-white rounded-xl shadow-2xl border border-gray-100 z-50 overflow-hidden">
                    
                    <div class="max-h-64 overflow-y-auto py-2 custom-scrollbar">
                        @php
                            $tahunIni = $bulanTahun->format('Y');
                            $tahunStart = 2024; 
                            $tahunEnd = $tahunIni + 5; 
                        @endphp
                        @for($y = $tahunStart; $y <= $tahunEnd; $y++)
                            <button onclick="pilihTahun('{{ $y }}')" 
                                    class="w-full text-left px-4 py-2 text-sm font-medium transition-colors duration-150
                                    {{ $tahunIni == $y ? 'bg-blue-50 text-[#2a4a6f] font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                {{ $y }}
                                @if($tahunIni == $y) <span class="float-right text-[#2a4a6f]">✓</span> @endif
                            </button>
                        @endfor
                    </div>
                </div>
            </div>

            {{-- Hidden Inputs --}}
            <input type="hidden" id="currentBulan" value="{{ $bulanTahun->format('m') }}">
            <input type="hidden" id="currentTahun" value="{{ $bulanTahun->format('Y') }}">

        </div>
    </div>

    {{-- Script JavaScript Navigasi (UPDATE) --}}
    <script>
        function pilihBulan(bulanBaru) {
            const tahunSaatIni = document.getElementById('currentTahun').value;
            redirectPage(tahunSaatIni, bulanBaru);
        }

        function pilihTahun(tahunBaru) {
            const bulanSaatIni = document.getElementById('currentBulan').value;
            redirectPage(tahunBaru, bulanSaatIni);
        }

        function redirectPage(tahun, bulan) {
            const paramBulan = `${tahun}-${bulan}`;
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('bulan', paramBulan);
            window.location.href = currentUrl.toString();
        }
    </script>

    {{-- Grid Kalender --}}
    <div class="bg-white p-4 md:p-6 rounded-xl shadow-md">
        <div class="grid grid-cols-7 gap-2 md:gap-4 text-center text-sm md:text-base font-bold text-gray-400 mb-4">
            <div>MING</div><div>SEN</div><div>SEL</div><div>RAB</div><div>KAM</div><div>JUM</div><div>SAB</div>
        </div>

        <div class="grid grid-cols-7 gap-2 md:gap-4 text-center text-sm md:text-lg font-bold">
            @foreach($kalender as $hari)
                @if($hari === null)
                    <div class="min-h-[50px] md:min-h-[100px]"></div>
                @else
                    @php
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

                    <div @click="openModal('{{ $hari['full_date'] }}', '{{ $hari['tanggal'] }} {{ $bulanTahun->translatedFormat('F Y') }}', '{{ $hari['jenis_shift'] }}')"
                         id="date-{{ $hari['full_date'] }}"
                         class="{{ $bgClass }} {{ $hoverClass }} rounded-xl flex flex-col items-center justify-center min-h-[50px] md:min-h-[120px] cursor-pointer shadow-sm transition-all transform hover:-translate-y-1">
                        
                        <span class="text-lg md:text-2xl">{{ $hari['tanggal'] }}</span>
                        
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
                    <button @click="selectedShift = 'Pagi'" :class="selectedShift == 'Pagi' ? 'ring-4 ring-yellow-200 border-yellow-500' : 'border-gray-200 hover:bg-gray-50'" class="w-full flex items-center justify-between p-3 bg-white border-2 rounded-xl transition-all">
                        <div class="flex items-center"><div class="w-4 h-4 rounded-full bg-yellow-400 mr-3"></div><span class="font-bold text-gray-800">Pagi</span></div>
                        <span x-show="selectedShift == 'Pagi'" class="text-yellow-600 text-xl font-bold">✓</span>
                    </button>

                    <button @click="selectedShift = 'Malam'" :class="selectedShift == 'Malam' ? 'ring-4 ring-blue-200 border-blue-500' : 'border-gray-200 hover:bg-gray-50'" class="w-full flex items-center justify-between p-3 bg-white border-2 rounded-xl transition-all">
                        <div class="flex items-center"><div class="w-4 h-4 rounded-full bg-blue-500 mr-3"></div><span class="font-bold text-gray-800">Malam</span></div>
                        <span x-show="selectedShift == 'Malam'" class="text-blue-600 text-xl font-bold">✓</span>
                    </button>

                    <button @click="selectedShift = 'Off'" :class="selectedShift == 'Off' ? 'ring-4 ring-red-200 border-red-500' : 'border-gray-200 hover:bg-gray-50'" class="w-full flex items-center justify-between p-3 bg-white border-2 rounded-xl transition-all">
                        <div class="flex items-center"><div class="w-4 h-4 rounded-full bg-red-500 mr-3"></div><span class="font-bold text-gray-800">Off</span></div>
                        <span x-show="selectedShift == 'Off'" class="text-red-600 text-xl font-bold">✓</span>
                    </button>
                </div>

                {{-- Checkbox Terapkan ke Masa Depan --}}
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <label class="flex items-start space-x-2 cursor-pointer group">
                        <input type="checkbox" x-model="applyToFuture" class="mt-1 w-4 h-4 text-[#2a4a6f] border-gray-300 rounded focus:ring-[#2a4a6f]">
                        <div class="text-xs text-gray-600 group-hover:text-gray-800">
                            <span class="font-bold block">Terapkan pola ini ke depan?</span>
                            <span class="opacity-75">Centang untuk memperbaiki pola shift tanggal berikutnya.</span>
                        </div>
                    </label>
                </div>
            </div>
            
            <div class="px-6 py-4 bg-gray-50 rounded-b-2xl flex justify-end space-x-3">
                <button type="button" @click="isModalOpen = false" class="px-4 py-2 text-gray-600 font-semibold hover:bg-gray-200 rounded-lg transition">Batal</button>
                <button type="button" @click="saveShift()" :disabled="isLoading" class="px-6 py-2 text-white bg-[#2a4a6f] rounded-lg hover:bg-[#1e3a5c] shadow-lg font-bold transform hover:-translate-y-0.5 transition-all flex items-center">
                    <span x-show="!isLoading">SIMPAN</span>
                    <span x-show="isLoading">...</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- 
    LOGIKA JAVASCRIPT DIPISAHKAN KE SINI 
    Agar tidak bentrok tanda kutip dengan atribut HTML.
--}}
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('shiftManager', (userId) => ({
            isModalOpen: false,
            selectedDate: '',
            selectedDateFormatted: '',
            selectedShift: '',
            selectedUserId: userId,
            applyToFuture: false, 
            feedbackMessage: '',
            isSuccess: false,
            isLoading: false,

            // Buka Modal Edit
            openModal(fullDate, formattedDate, currentShift) {
                this.selectedDate = fullDate;
                this.selectedDateFormatted = formattedDate;
                this.selectedShift = currentShift;
                this.applyToFuture = false; 
                this.isModalOpen = true;
                this.feedbackMessage = ''; 
                this.isSuccess = false;
            },

            // Update Visual Kotak (Tanpa Reload)
            updateVisualCell(dateString, shiftType) {
                const cellId = 'date-' + dateString;
                const cell = document.getElementById(cellId);
                
                if (cell) {
                    cell.className = 'rounded-xl flex flex-col items-center justify-center min-h-[50px] md:min-h-[120px] cursor-pointer shadow-sm transition-all transform hover:-translate-y-1 border bg-gray-100 text-gray-800 border-gray-200';
                    
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

                    const labelSpan = cell.querySelector('span.shift-label');
                    if (labelSpan) labelSpan.textContent = shiftType;
                }
            },

            // Simpan Shift
            saveShift() {
                this.isLoading = true;
                this.feedbackMessage = '';

                const payload = {
                    id_pengguna: this.selectedUserId,
                    tanggal: this.selectedDate,
                    jenis_shift: this.selectedShift,
                    apply_pattern: this.applyToFuture, 
                    _token: '{{ csrf_token() }}'
                };

                axios.post('{{ route("komandan.akun.shift.update") }}', payload)
                    .then(response => {
                        if (response.data.success) {
                            this.isSuccess = true;
                            this.feedbackMessage = response.data.message; 
                            
                            this.updateVisualCell(this.selectedDate, this.selectedShift);

                            if (response.data.affected_dates && response.data.affected_dates.length > 0) {
                                response.data.affected_dates.forEach(item => {
                                    this.updateVisualCell(item.date, item.shift);
                                });
                            }

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
            },

            // Fungsi Reset Jadwal
            resetSchedule() {
                Swal.fire({
                    title: 'Reset Jadwal Masa Depan?',
                    text: 'Semua jadwal mulai BESOK ke depan akan dihapus. Jadwal hari ini dan masa lalu aman.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Reset!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.post('{{ route("komandan.akun.shift.reset") }}', {
                            id_pengguna: this.selectedUserId,
                            _token: '{{ csrf_token() }}'
                        })
                        .then(response => {
                            Swal.fire('Berhasil!', 'Jadwal masa depan berhasil dikosongkan.', 'success')
                            .then(() => {
                                window.location.reload(); 
                            });
                        })
                        .catch(error => {
                            Swal.fire('Gagal!', 'Terjadi kesalahan saat reset.', 'error');
                        });
                    }
                })
            }
        }));
    });
</script>
@endsection