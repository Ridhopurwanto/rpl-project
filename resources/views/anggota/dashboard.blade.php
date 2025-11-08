{{-- Menggunakan layout 'app' yang baru saja dibuat --}}
@extends('layouts.app')

@section('content')
<div class="w-full max-w-sm mx-auto px-4 py-8">

    <div class="bg-slate-800 rounded-lg shadow-lg p-4 flex justify-between items-center mb-6">
        <div>
            <p class="text-sm text-gray-300">Absensi Kehadiran</p>
            <p class="text-lg font-semibold">27/06</p>
        </div>
        <button class="bg-green-500 text-white font-bold py-2 px-4 rounded-lg">
            CHECK-IN
        </button>
    </div>

    <div class="space-y-4">

        {{-- Helper untuk Tombol Menu --}}
        @php
        function renderMenuButton($text, $href = '#') {
            echo '<a href="' . $href . '" class="w-full flex items-center justify-between text-left p-4 bg-blue-600 rounded-lg shadow-lg hover:bg-blue-700 transition duration-300">';
            echo '<span class="text-lg font-semibold">' . $text . '</span>';
            echo '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>';
            echo '</a>';
        }
        @endphp

        {{-- Panggil helper untuk setiap tombol --}}
        {!! renderMenuButton('PRESENSI') !!}
        {!! renderMenuButton('PATROLI') !!}
        {!! renderMenuButton('KENDARAAN') !!}
        {!! renderMenuButton('TAMU') !!}
        {!! renderMenuButton('BARANG') !!}
        {!! renderMenuButton('GANGGUAN KAMTIBMAS') !!}

    </div>
</div>
@endsection