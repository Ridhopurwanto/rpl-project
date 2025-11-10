{{-- Menggunakan layout 'app' yang baru saja diperbarui --}}
@extends('layouts.anggota.app')

@section('content')
<div class="w-full">
    
    <div class="bg-slate-800 text-white rounded-2xl shadow-lg p-4 my-4">
        <p class="text-sm">SELAMAT DATANG,</p>
        <h2 class="text-xl font-bold">{{ Auth::user()->nama_lengkap}}</h2>
    </div>

    <div 
    {{-- 1. Inisialisasi komponen Alpine --}}
    x-data="{ 
        currentDate: '', 
        currentTime: '',
        
        {{-- 2. Fungsi untuk mengambil & memformat waktu --}}
        updateDateTime() {
            const now = new Date();
            
            this.currentDate = now.toLocaleDateString('id-ID', {
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            }).toUpperCase();

            this.currentTime = [
                now.getHours().toString().padStart(2, '0'),
                now.getMinutes().toString().padStart(2, '0')
            ].join(':');
        }
    }"
    {{-- 3. Jalankan fungsi saat dimuat, dan ulangi tiap 1 detik --}}
    x-init="
        updateDateTime(); 
        setInterval(() => { updateDateTime() }, 1000);
    "
    class="flex justify-between items-center mb-6"
>
    
    <div x-text="currentDate" 
         class="bg-slate-800 text-white text-xs font-semibold px-4 py-2 rounded-full">
        </div>
    
    <div x-text="currentTime" 
         class="bg-slate-800 text-white text-xs font-semibold px-4 py-2 rounded-full">
        </div>
</div>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        
        {{-- 
            Helper untuk Tombol Menu 3D
            Kita buat tombol biru di dalam container abu-abu
            untuk menciptakan efek "timbul" sesuai desain.
        --}}
        @php
        function renderMenuButton($text, $href = '#') {
            // Container luar (abu-abu)
            echo '<div class="bg-gray-300 rounded-full p-1 shadow-inner">';
            
            // Tombol dalam (biru)
            echo '<a href="' . $href . '" class="w-full flex items-center justify-between text-left p-4 bg-blue-700 text-white rounded-full shadow-lg hover:bg-blue-800 transition duration-300">';
            echo '<span class="text-lg font-bold tracking-wider">' . $text . '</span>';
            echo '<span class="bg-white/30 p-1 rounded-full">';
            echo '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>';
            echo '</span>';
            echo '</a>';
            
            echo '</div>';
        }
        @endphp

        {{-- Panggil helper untuk setiap tombol --}}
        {!! renderMenuButton('PRESENSI', route('anggota.presensi')) !!}
        {!! renderMenuButton('PATROLI', route('anggota.patroli.index')) !!}
        {!! renderMenuButton('KENDARAAN') !!}
        {!! renderMenuButton('TAMU') !!}
        {!! renderMenuButton('BARANG') !!}
        {!! renderMenuButton('GANGGUAN KAMTIBMAS') !!}

    </div>
</div>
@endsection