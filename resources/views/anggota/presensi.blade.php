@extends('layouts.anggota.app')

{{-- 1. Ganti Tombol Header --}}
@section('header-left')
    <a href="{{ route('anggota.presensi') }}" class="bg-blue-600 text-white text-sm font-semibold px-6 py-2 rounded-full shadow-md">
        PRESENSI
    </a>
@endsection

{{-- 2. Isi Konten Halaman --}}
@section('content')

{{-- Inisialisasi Alpine.js untuk modal --}}
<div x-data="{ showModal: false, modalPhoto: 'https://via.placeholder.com/400x300.png?text=Contoh+Foto' }">

    <div classs="text-center text-lg font-bold text-gray-800 mt-4">
        {{-- Variabel $namaBulan dari controller --}}
        {{ strtoupper($namaBulan) }}
    </div>

    <div class="grid grid-cols-7 gap-1 text-center text-sm mt-2 font-semibold">
        <div class="text-gray-500">Su</div>
        <div class="text-gray-500">Mo</div>
        <div class="text-gray-500">Tu</div>
        <div class="text-gray-500">We</div>
        <div class="text-gray-500">Th</div>
        <div class="text-gray-500">Tr</div>
        <div class="text-gray-500">Sa</div>

        {{-- Loop $dataKalender yang kita buat di controller --}}
        @foreach($dataKalender as $hari)
            @php
                // Logika untuk menentukan warna background
                $bgColor = 'bg-gray-100'; // Default jika shift null
                if ($hari['jenis_shift'] === 'pagi') {
                    $bgColor = 'bg-yellow-400';
                } elseif ($hari['jenis_shift'] === 'malam') {
                    $bgColor = 'bg-blue-400';
                } elseif ($hari['jenis_shift'] === 'off') {
                    $bgColor = 'bg-red-500 text-white';
                } elseif ($hari['tanggal'] === null) {
                    $bgColor = 'bg-transparent'; // Hari kosong (padding)
                }
            @endphp
            
            <div class="{{ $bgColor }} rounded-lg p-2">
                {{ $hari['tanggal'] }}
            </div>
        @endforeach
    </div>

    <div class="flex flex-wrap justify-center items-center space-x-4 mt-4 text-xs">
        <div class="flex items-center space-x-1">
            <div class="w-3 h-3 bg-yellow-400 rounded-full"></div>
            <span>Shift Pagi</span>
        </div>
        <div class="flex items-center space-x-1">
            <div class="w-3 h-3 bg-blue-400 rounded-full"></div>
            <span>Shift Malam</span>
        </div>
        <div class="flex items-center space-x-1">
            <div class="w-3 h-3 bg-red-500 rounded-full"></div>
            <span>Off</span>
        </div>
    </div>

    <div class="mt-6 p-4 bg-white rounded-lg shadow">
        <h3 class="text-xs font-semibold text-gray-500 uppercase">RIWAYAT :</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2">
            <div class="flex items-center justify-between space-x-2">
                <label for="riwayat-tanggal" class="text-sm font-semibold text-gray-700">TANGGAL :</label>
                <button id="riwayat-tanggal" class="flex-grow flex items-center justify-between bg-slate-800 text-white text-sm font-semibold px-4 py-2 rounded-full shadow-md w-full">
                    <span>10/10/2025</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </button>
            </div>
            <div class="flex items-center justify-between space-x-2">
                <label for="riwayat-shift" class="text-sm font-semibold text-gray-700">JENIS SHIFT :</label>
                <button id="riwayat-shift" class="flex-grow flex items-center justify-between bg-slate-800 text-white text-sm font-semibold px-4 py-2 rounded-full shadow-md w-full">
                    <span>SHIFT PAGI</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
            </div>
        </div>
    </div>

    <div class="mt-4 mb-20 bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-800 text-white">
                    <tr>
                        <th class="p-3">Foto</th>
                        <th class="p-3">Waktu</th>
                        <th class="p-3">Tempat</th>
                        <th class="p-3">Status</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    {{-- 
                    'riwayatHariIni' adalah variabel yang kita kirim dari Controller.
                    Kita cek apakah data hari ini ada.
                    --}}
                    @if($riwayatHariIni)
                        <tr class="border-b text-center">
                            <td class="p-3">
                                <a href="#" 
                                @click.prevent="showModal = true; modalPhoto = '{{ asset('storage/' . $riwayatHariIni->foto_masuk) }}'" 
                                class="text-blue-600 underline font-semibold">
                                    Buka
                                </a>
                            </td>
                            <td class="p-3 font-medium">{{ $riwayatHariIni->waktu_masuk }}</td>
                            <td class="p-3 text-xs text-left">{{ $riwayatHariIni->lokasi }}</td>
                            <td class="p-3 text-green-600 font-semibold">
                                {{ $riwayatHariIni->status }}
                            </td>
                        </tr>
                        
                        {{-- Kita cek apakah sudah ada data pulang --}}
                        @if($riwayatHariIni->waktu_pulang)
                            <tr class="border-b text-center">
                                <td class="p-3">
                                    <a href="#" 
                                    @click.prevent="showModal = true; modalPhoto = '{{ asset('storage/' . $riwayatHariIni->foto_pulang) }}'" 
                                    class="text-blue-600 underline font-semibold">
                                        Buka
                                    </a>
                                </td>
                                <td class="p-3 font-medium">{{ $riwayatHariIni->waktu_pulang }}</td>
                                <td class="p-3 text-xs text-left">{{ $riwayatHariIni->lokasi }}</td>
                                <td class="p-3 text-green-600 font-semibold">
                                    {{ $riwayatHariIni->status }}
                                </td>
                            </tr>
                        @endif
                    @else
                        <tr>
                            <td colspan="4" class="p-4 text-center text-gray-500">
                                Belum ada riwayat presensi untuk tanggal ini.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <div 
        x-show="showModal" 
        @keydown.escape.window="showModal = false"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
        style="display: none;"
    >
        <div 
            @click.outside="showModal = false"
            class="bg-white rounded-lg shadow-xl w-full max-w-md"
        >
            <div class="flex justify-between items-center p-4 border-b">
                <h3 class="text-lg font-bold text-gray-800">PHOTO</h3>
                <button @click="showModal = false" class="text-gray-500 hover:text-gray-800">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-4">
                <img :src="modalPhoto" alt="Foto Presensi" class="w-full h-auto rounded">
            </div>
        </div>
    </div>

</div>
@endsection

{{-- 3. Tambahkan Tombol FAB (+) --}}
@push('fab')
    {{-- Ini sekarang adalah LINK ke halaman 'create', bukan form --}}
    <a href="{{ route('anggota.presensi.create') }}" 
       class="fixed z-50 bottom-6 right-6 md:right-[calc((100vw-768px)/2+24px)] p-4 bg-blue-700 rounded-full text-white shadow-lg hover:bg-blue-800 transition">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
    </a>
@endpush