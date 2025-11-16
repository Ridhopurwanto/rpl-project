{{-- 
  File: resources/views/komandan/akun/shift.blade.php
  Versi FINAL: Kalender Interaktif dengan Modal Edit
--}}

@extends('layouts.app')

{{-- Ganti tombol "HOME" di header --}}
@section('header-left')
    <a href="{{ route('komandan.akun.index') }}" class="bg-slate-800 text-white text-sm font-semibold px-6 py-2 rounded-full shadow-md hover:bg-slate-700 transition">
        {{ $user->nama_lengkap }} - SHIFT
    </a>
@endsection

@section('content')

{{-- 
  =================================
  BUNGKUSAN ALPINEJS
  Untuk mengelola state modal
  =================================
--}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{
    isModalOpen: false,
    selectedDate: '',
    selectedShift: '',
    selectedUserId: {{ $user->id_pengguna }},
    feedbackMessage: '',
    isSuccess: false
}">
    
    {{-- Tombol Kembali --}}
    <a href="{{ route('komandan.akun.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-gray-900 mb-2">
        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        Kembali ke Manajemen Akun
    </a>

    {{-- Feedback Message (untuk notifikasi sukses/error) --}}
    <div x-show="feedbackMessage"
         :class="isSuccess ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700'"
         class="border px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline" x-text="feedbackMessage"></span>
        <button @click="feedbackMessage = ''" class="absolute top-0 bottom-0 right-0 px-4 py-3">&times;</button>
    </div>
    
    {{-- KALENDER SHIFT --}}
    <div class="bg-white p-4 rounded-lg shadow-md">
        {{-- Div ini akan diisi oleh FullCalendar --}}
        <div id="calendar"></div>
    </div>
    
    {{-- Legenda Warna --}}
    <div class="flex justify-center flex-wrap space-x-4 mt-4 text-sm">
        <div class="flex items-center">
            <span class="w-4 h-4 rounded-full bg-yellow-400 mr-2"></span>
            <span>Shift Pagi</span>
        </div>
        <div class="flex items-center">
            <span class="w-4 h-4 rounded-full bg-blue-400 mr-2"></span>
            <span>Shift Malam</span>
        </div>
        <div class="flex items-center">
            <span class="w-4 h-4 rounded-full bg-red-500 mr-2"></span>
            <span>Off</span>
        </div>
    </div>

    {{-- 
      =================================
      MODAL EDIT SHIFT (SESUAI PERMINTAAN ANDA)
      =================================
    --}}
    <div x-show="isModalOpen" @keydown.escape.window="isModalOpen = false" class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-gray-900 bg-opacity-50" style="display: none;">
        <div class="relative w-full max-w-sm p-6 mx-4 bg-white rounded-lg shadow-xl" @click.away="isModalOpen = false">
            
            {{-- Header Modal --}}
            <div class="flex justify-between items-center pb-3 border-b">
                <h5 class="text-xl font-semibold text-gray-800">Ubah Shift</h5>
                <button type="button" @click="isModalOpen = false" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
            </div>
            
            {{-- Body Modal --}}
            <div class="modal-body py-4">
                <p class="mb-2">Ubah shift untuk tanggal:</p>
                <p class="text-lg font-bold text-blue-900 mb-4" x-text="selectedDate"></p>
                
                <label for="jenis_shift" class="block text-sm font-medium text-gray-700">Pilih Shift Baru:</label>
                <select id="jenis_shift" x-model="selectedShift"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="Pagi">Pagi</option>
                    <option value="Malam">Malam</option>
                    <option value="Off">Off</option>
                </select>
            </div>
            
            {{-- Footer Modal --}}
            <div class="modal-footer flex justify-end pt-3 border-t">
                <button type="button" @click="isModalOpen = false" class="px-4 py-2 mr-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">Batal</button>
                {{-- 
                  Tombol "Simpan" ini akan memanggil fungsi 'updateShift' 
                  yang kita definisikan di @push('scripts')
                --}}
                <button type="button" @click="updateShift()" class="px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                    Simpan
                </button>
            </div>
        </div>
    </div>

</div>
@endsection


{{-- 
  =================================
  PUSH CSS KE <HEAD>
  =================================
--}}
@push('styles')
<style>
    :root {
        --fc-border-color: #E5E7EB; /* gray-200 */
        --fc-daygrid-event-dot-width: 8px;
    }
    .fc .fc-toolbar-title {
        font-size: 1.25rem; /* text-xl */
        font-weight: 700; /* font-bold */
        color: #1F2937; /* gray-800 */
        text-transform: uppercase;
    }
    .fc .fc-button {
        background-color: #3B82F6 !important; /* bg-blue-500 */
        border: none !important;
        color: white !important;
        box-shadow: none !important;
    }
    .fc .fc-button:hover {
        background-color: #2563EB !important; /* bg-blue-600 */
    }
    .fc .fc-daygrid-day-number {
        font-size: 0.875rem;
        color: #374151; /* gray-700 */
        padding: 4px;
    }
    .fc .fc-day-today {
        background-color: #EFF6FF !important; /* bg-blue-50 */
    }
    .fc .fc-daygrid-event {
        border-radius: 4px;
        padding: 2px 4px;
        font-size: 0.875rem; /* text-sm */
        font-weight: 700; /* font-bold */
    }
    /* Membuat tanggal bisa diklik */
    .fc .fc-daygrid-day {
        cursor: pointer;
    }
    .fc .fc-daygrid-day:hover {
        background-color: #F9FAFB; /* gray-50 */
    }
</style>
@endpush


{{-- 
  =================================
  PUSH JAVASCRIPT KE <body>
  =================================
--}}
@push('scripts')
{{-- 1. Load library FullCalendar --}}
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
{{-- 2. Load library Axios (untuk kirim data ke controller) --}}
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
    // Variabel kalender kita buat global agar bisa di-refetch
    let calendar;

    // Ambil data shift dari Controller
    const shiftsData = @json($shifts);

    // Definisikan Warna
    const colors = {
        'Pagi': '#FACC15', // Kuning
        'Malam': '#60A5FA', // Biru Muda
        'Off': '#EF4444'   // Merah
    };
    
    // Ubah format data agar bisa dibaca FullCalendar
    const events = shiftsData.map(shift => {
        const shiftType = shift.jenis_shift;
        const color = colors[shiftType] || '#808080'; 

        return {
            id: shift.tanggal, // Kita beri ID agar mudah di-update
            title: shiftType,
            start: shift.tanggal,
            backgroundColor: color,
            borderColor: color,
            textColor: (shiftType === 'Pagi') ? '#1F2937' : '#FFFFFF' 
        };
    });

    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'id',
            height: 'auto',
            headerToolbar: {
                left: '',
                center: 'prev title next',
                right: ''
            },
            
            events: events, // Masukkan data shift

            // ======================================================
            // FUNGSI UTAMA: Saat tanggal diklik
            // ======================================================
            dateClick: function(info) {
                // Ambil scope AlpineJS
                let alpineScope = document.querySelector('[x-data]')._x_dataStack[0];

                // Set data di Alpine untuk modal
                alpineScope.selectedDate = info.dateStr; // Format 'YYYY-MM-DD'
                
                // Cek apakah sudah ada shift di tanggal itu
                let existingEvent = calendar.getEventById(info.dateStr);
                if (existingEvent) {
                    alpineScope.selectedShift = existingEvent.title; // 'Pagi', 'Malam', atau 'Off'
                } else {
                    alpineScope.selectedShift = 'Off'; // Default jika belum di-set
                }

                // Buka modal
                alpineScope.isModalOpen = true;
            },

            eventDidMount: function(info) {
                info.el.style.fontWeight = 'bold';
            },
            dayCellDidMount: function(info) {
                info.el.style.padding = '4px';
            }
        });
        
        calendar.render();
    });

    /**
     * Fungsi ini dipanggil oleh tombol "Simpan" di modal AlpineJS
     */
    function updateShift() {
        // Ambil scope AlpineJS
        let alpineScope = document.querySelector('[x-data]')._x_dataStack[0];
        
        // Ambil data dari modal
        const data = {
            id_pengguna: alpineScope.selectedUserId,
            tanggal: alpineScope.selectedDate,
            jenis_shift: alpineScope.selectedShift,
            _token: "{{ csrf_token() }}" // Jangan lupa CSRF token
        };

        // Kirim data ke controller menggunakan Axios
        axios.post("{{ route('komandan.akun.shift.update') }}", data)
            .then(function(response) {
                // Jika SUKSES
                if (response.data.success) {
                    // 1. Tampilkan notifikasi sukses
                    alpineScope.isSuccess = true;
                    alpineScope.feedbackMessage = response.data.message;

                    // 2. Hapus event lama (jika ada)
                    let oldEvent = calendar.getEventById(data.tanggal);
                    if (oldEvent) {
                        oldEvent.remove();
                    }

                    // 3. Tambahkan event baru ke kalender (tanpa reload halaman)
                    const newShiftType = response.data.jenis_shift;
                    const newColor = colors[newShiftType] || '#808080';
                    
                    calendar.addEvent({
                        id: data.tanggal,
                        title: newShiftType,
                        start: data.tanggal,
                        backgroundColor: newColor,
                        borderColor: newColor,
                        textColor: (newShiftType === 'Pagi') ? '#1F2937' : '#FFFFFF'
                    });

                    // 4. Tutup modal
                    alpineScope.isModalOpen = false;
                }
            })
            .catch(function(error) {
                // Jika GAGAL
                alpineScope.isSuccess = false;
                if (error.response && error.response.data && error.response.data.message) {
                    alpineScope.feedbackMessage = error.response.data.message;
                } else {
                    alpineScope.feedbackMessage = "Terjadi kesalahan. Silakan coba lagi.";
                }
            });
    }
</script>
@endpush