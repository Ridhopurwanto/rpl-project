{{-- 
  File: resources/views/komandan/akun/shift.blade.php
  Perbaikan: Memindahkan blok <style> ke @push('styles')
--}}

@extends('layouts.app')

{{-- Ganti tombol "HOME" di header menjadi "MANAJEMEN AKUN" --}}
@section('header-left')
    {{-- 
      Anda bisa mengganti teks 'MANAJEMEN AKUN' menjadi 'M. SONY SHIFT' 
      jika ingin sesuai dengan mockup
    --}}
    <a href="{{ route('komandan.akun.index') }}" class="bg-slate-800 text-white text-sm font-semibold px-6 py-2 rounded-full shadow-md hover:bg-slate-700 transition">
        {{ $user->nama_lengkap }} - SHIFT
    </a>
@endsection

@section('content')

{{-- Override layout 'max-w-sm' agar lebih lebar --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    
    {{-- Tombol Kembali --}}
    <a href="{{ route('komandan.akun.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-gray-900 mb-2">
        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        Kembali ke Manajemen Akun
    </a>

    {{-- 
      Header Halaman Kalender
      (Ini sudah diwakili oleh @section('header-left') di atas)
    --}}
    
    {{-- 
      =================================
      KALENDER SHIFT
      =================================
    --}}
    <div class="bg-white p-4 rounded-lg shadow-md">
        <div id="calendar"></div>
    </div>
    
    {{-- Legenda Warna (Sesuai Mockup) --}}
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
    /* Kustomisasi event agar sesuai mockup */
    .fc .fc-daygrid-event {
        border-radius: 4px;
        padding: 2px 4px;
        font-size: 0.875rem; /* text-sm */
        font-weight: 700; /* font-bold */
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 2. Ambil data shift dari Controller
        const shiftsData = @json($shifts);

        // 3. Definisikan Warna (Sesuai Mockup)
        const colors = {
            'Pagi': '#FACC15', // Kuning (bg-yellow-400)
            'Malam': '#60A5FA', // Biru Muda (bg-blue-400)
            'Off': '#EF4444'   // Merah (bg-red-500)
        };

        // 4. Ubah format data agar bisa dibaca FullCalendar
        const events = shiftsData.map(shift => {
            // Asumsi nama kolom 'jenis_shift' dari model Shift.php
            const shiftType = shift.jenis_shift; 
            const color = colors[shiftType] || '#808080'; // Default ke abu-abu jika tidak cocok

            return {
                title: shiftType,
                start: shift.tanggal, // Asumsi nama kolom 'tanggal'
                backgroundColor: color,
                borderColor: color,
                // Teks hitam untuk kuning, putih untuk lainnya (agar terbaca)
                textColor: (shiftType === 'Pagi') ? '#1F2937' : '#FFFFFF' 
            };
        });

        // 5. Inisialisasi Kalender
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'id', // Bahasa Indonesia
            height: 'auto', // Biarkan tinggi menyesuaikan

            // Header kalender (Sesuai Mockup)
            headerToolbar: {
                left: '',
                center: 'prev title next',
                right: ''
            },
            
            // Masukkan data shift ke kalender
            events: events,

            // Kustomisasi tampilan
            eventDidMount: function(info) {
                // Membuat teks tebal
                info.el.style.fontWeight = 'bold';
            },

            // Kustomisasi tanggal di kalender (misal: "26", "27")
            dayCellDidMount: function(info) {
                info.el.style.padding = '4px';
            }
        });
        
        // 6. Tampilkan kalender
        calendar.render();
    });
</script>
@endpush