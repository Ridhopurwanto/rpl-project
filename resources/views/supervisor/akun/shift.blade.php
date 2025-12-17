@extends('layouts.app')

@section('header-left')
    <a href="{{ route('supervisor.akun.index') }}" 
       class="inline-flex items-center justify-center w-10 h-10 bg-white text-[#1a2847] rounded-full shadow-md hover:bg-gray-100 transition-colors mb-4"
       title="Kembali">
        {{-- Ikon Panah Kiri (SVG) --}}
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
    </a>
@endsection

@section('content')

<div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
    
    {{-- 1. Header Profil --}}
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
    </div>

    {{-- 2. Navigasi Bulan --}}
    <div class="flex flex-col-reverse md:flex-row items-end md:justify-end gap-3 mb-6">
        
        {{-- Navigation Controls --}}
        <div class="flex items-center justify-center gap-1 bg-white p-1 rounded-full shadow-sm border border-gray-200 w-auto md:w-auto">
            {{-- Tombol Today --}}
            <a href="{{ route('supervisor.akun.shift', ['id_pengguna' => $user->id_pengguna, 'bulan' => \Carbon\Carbon::now()->format('Y-m')]) }}" 
               class="px-4 py-1.5 text-sm font-semibold text-gray-700 rounded-full hover:bg-gray-100 transition border border-transparent hover:border-gray-300">
                Today
            </a>

            <div class="h-6 w-px bg-gray-300 mx-1"></div>

            {{-- Tombol Previous --}}
            <a href="{{ route('supervisor.akun.shift', ['id_pengguna' => $user->id_pengguna, 'bulan' => $prevMonth]) }}" 
               class="w-9 h-9 flex items-center justify-center rounded-full text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>

            {{-- Tombol Next --}}
            <a href="{{ route('supervisor.akun.shift', ['id_pengguna' => $user->id_pengguna, 'bulan' => $nextMonth]) }}" 
               class="w-9 h-9 flex items-center justify-center rounded-full text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>

        {{-- Custom Dropdown Bulan & Tahun --}}
        <div class="flex items-center justify-center gap-1 bg-white p-1 rounded-full shadow-sm border border-gray-200 md:ml-4 relative w-auto md:w-auto z-20" x-data="{ openBulan: false, openTahun: false }">
            
            {{-- Dropdown Bulan --}}
            <div class="relative">
                <button @click="openBulan = !openBulan" @click.outside="openBulan = false" 
                        class="flex items-center justify-center px-4 py-1.5 text-lg md:text-xl font-bold text-gray-800 rounded-full hover:bg-gray-100 transition focus:outline-none">
                    {{ $bulanTahun->translatedFormat('F') }}
                    <svg class="w-4 h-4 ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                <div x-show="openBulan" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="absolute top-full right-0 mt-2 w-32 bg-white rounded-xl shadow-2xl border border-gray-100 z-50 overflow-hidden">
                    
                    <div class="max-h-64 overflow-y-auto py-2">
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

            <div class="h-6 w-px bg-gray-300 mx-1"></div>

            {{-- Dropdown Tahun --}}
            <div class="relative">
                <button @click="openTahun = !openTahun" @click.outside="openTahun = false" 
                        class="flex items-center justify-center px-4 py-1.5 text-lg md:text-xl font-normal text-gray-500 rounded-full hover:bg-gray-100 transition focus:outline-none">
                    {{ $bulanTahun->format('Y') }}
                    <svg class="w-4 h-4 ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                <div x-show="openTahun" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="absolute top-full right-0 mt-2 w-32 bg-white rounded-xl shadow-2xl border border-gray-100 z-50 overflow-hidden">
                    
                    <div class="max-h-64 overflow-y-auto py-2">
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

            <input type="hidden" id="currentBulan" value="{{ $bulanTahun->format('m') }}">
            <input type="hidden" id="currentTahun" value="{{ $bulanTahun->format('Y') }}">

        </div>
    </div>

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

    {{-- Grid Kalender (View Only) --}}
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
                        
                        if ($hari['jenis_shift'] == 'Pagi') {
                            $bgClass = 'bg-yellow-400 text-gray-900 border border-yellow-500';
                        }
                        if ($hari['jenis_shift'] == 'Malam') {
                            $bgClass = 'bg-blue-500 text-white border border-blue-600';
                        }
                        if ($hari['jenis_shift'] == 'Off') {
                            $bgClass = 'bg-red-500 text-white border border-red-600';
                        }
                    @endphp

                    <div class="{{ $bgClass }} rounded-xl flex flex-col items-center justify-center min-h-[50px] md:min-h-[120px] shadow-sm">
                        <span class="text-lg md:text-2xl">{{ $hari['tanggal'] }}</span>
                        <span class="hidden md:block text-xs uppercase mt-1 font-normal opacity-80">
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
        @if($user->jenis_jadwal !== 'non_shift')
        <div class="flex items-center"><span class="w-3 h-3 md:w-4 md:h-4 rounded-full bg-blue-500 mr-2"></span> Shift Malam</div>
        @endif
        <div class="flex items-center"><span class="w-3 h-3 md:w-4 md:h-4 rounded-full bg-red-500 mr-2"></span> Off</div>
    </div>
</div>

@endsection