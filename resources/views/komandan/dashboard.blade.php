@extends('layouts.app') {{-- Menggunakan layout utama --}}

@section('content')
<div class="w-full max-w-sm mx-auto px-4 py-8">

    <div class="bg-slate-800 rounded-lg shadow-lg p-4 flex justify-between items-center mb-6">
        <div>
            <p class="text-sm text-gray-300">Selamat Datang,</p>
            <p class="text-lg font-semibold">Komandan</p>
        </div>
    </div>

    <div class="space-y-4">

        {{-- Helper untuk Tombol Menu --}}
        @php
        function renderMenuButton($text, $href = '#') {
            return '<a href="' . $href . '" class="w-full flex items-center justify-between text-left p-4 bg-blue-600 rounded-lg shadow-lg hover:bg-blue-700 transition duration-300">
                        <span class="text-lg font-semibold">' . $text . '</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
                        </svg>
                    </a>';
        }
        @endphp

        {{-- Menu untuk Komandan --}}
        {!! renderMenuButton('PRESENSI', route('laporan.presensi')) !!}
        {!! renderMenuButton('PATROLI') !!}
        {!! renderMenuButton('KENDARAAN') !!}
        {!! renderMenuButton('TAMU') !!}
        {!! renderMenuButton('BARANG') !!}
        {!! renderMenuButton('GANGGUAN KAMTIBMAS') !!}
        {!! renderMenuButton('MANAJEMEN AKUN') !!}
        {!! renderMenuButton('UNDUH LAPORAN') !!}
    </div>
</div>
@endsection