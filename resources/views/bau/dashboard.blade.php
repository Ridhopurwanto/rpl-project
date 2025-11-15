@extends('layouts.app')

@section('content')
<div class="w-full">
    
    {{-- 1. KARTU SELAMAT DATANG --}}
    {{-- Ini akan otomatis menampilkan nama username Komandan --}}
    <div 
        {{-- WARNA DIUBAH DI BARIS INI --}}
        class="bg-gradient-to-b from-[#2a4a6f] via-[#365c82] to-[#476c94] text-white rounded-2xl shadow-lg p-4 my-4 flex justify-between items-start"
        
        {{-- 1. Inisialisasi komponen Alpine dipindahkan ke sini --}}
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
    >
        {{-- Bagian Kiri: Teks Selamat Datang --}}
        <div>
            <p class="text-sm">SELAMAT DATANG,</p>
            <h2 class="text-xl font-bold">{{ Auth::user()->nama_lengkap }}</h2>
        </div>

        {{-- Bagian Kanan: Tanggal & Waktu (bertumpuk) --}}
        <div class="text-right"> {{-- Dibuat text-right agar rata kanan --}}
            
            {{-- Tampilkan tanggal (tanpa background) --}}
            <div x-text="currentDate" class="text-xs font-semibold"></div>
            
            {{-- Tampilkan waktu (tanpa background) --}}
            <div x-text="currentTime" class="text-xs font-semibold"></div>
        
        </div>
    </div>

    {{-- 3. DAFTAR MENU KOMANDAN --}}
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        
        {{-- Helper untuk Tombol Menu 3D (dicopy dari dashboard anggota) --}}
        @php
        function renderMenuButton($text, $href = '#') {
            // Container luar (abu-abu)
            echo '<div class="bg-gray-300 rounded-full p-1 shadow-inner">';
            
            // Tombol dalam (biru)
            // WARNA DIUBAH DI BARIS INI
            echo '<a href="' . $href . '" class="w-full flex items-center justify-between text-left p-4 bg-[#2a4a6f] text-white rounded-full shadow-lg hover:bg-[#365c82] transition duration-300">';
            echo '<span class="text-lg font-bold tracking-wider">' . $text . '</span>';
            echo '<span class="bg-white/30 p-1 rounded-full">';
            echo '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>';
            echo '</span>';
            echo '</a>';
            
            echo '</div>';
        }
        @endphp

        {{-- Panggil helper untuk setiap tombol --}}
        {{-- Ini adalah tombol yang sama dengan anggota --}}
        {!! renderMenuButton('PRESENSI') !!}
        {!! renderMenuButton('PATROLI') !!}
        {!! renderMenuButton('KENDARAAN') !!}
        {!! renderMenuButton('TAMU') !!}
        {!! renderMenuButton('BARANG') !!}
        {!! renderMenuButton('GANGGUAN KAMTIBMAS') !!}
        {!! renderMenuButton('DAFTAR AKUN') !!}
        {!! renderMenuButton('UNDUH LAPORAN') !!}

    </div>
</div>
@endsection