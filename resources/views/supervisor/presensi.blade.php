@extends('layouts.app')

{{-- Tombol KEMBALI --}}
@section('header-left')
    <a class="flex items-center border-2 border-[#1a2847] text-[#1a2847] text-sm font-bold px-4 py-2 rounded-full">
        PRESENSI
    </a>
@endsection

@section('content')
{{-- Style untuk animasi timer notifikasi --}}
    <style>
        @keyframes countdown {
            from { stroke-dashoffset: 0; }
            to { stroke-dashoffset: 100; }
        }
        .timer-circle {
            fill: none;
            stroke-width: 3;
            stroke-linecap: round;
            stroke-dasharray: 100;
            stroke-dashoffset: 0;
            transform: rotate(-90deg);
            transform-origin: center;
        }
        /* Animasi berjalan 5 detik sesuai timeout javascript */
        .animate-timer {
            animation: countdown 5s linear forwards;
        }
    </style>

<div class="w-full mx-auto"
     x-data="presensiData()">
    
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold text-slate-800">Laporan Presensi Anggota</h2>
    </div>

    {{-- Toast Notification --}}
    {{-- Floating Notification Success --}}
        <div x-show="showSuccessNotif" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-x-full"
             x-transition:enter-end="opacity-100 transform translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-x-0"
             x-transition:leave-end="opacity-0 transform translate-x-full"
             x-init="if(showSuccessNotif) setTimeout(() => showSuccessNotif = false, 5000)"
             class="fixed top-4 right-4 z-50 bg-green-500 text-white pl-6 pr-2 py-1 rounded-lg shadow-2xl flex items-center gap-3 min-w-[300px] max-w-md"
             style="display: none;">
            <div class="flex-shrink-0">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="flex-1">
                <p class="font-semibold text-sm">{{ session('success') }}</p>
            </div>
            {{-- Tombol Close dengan Timer Circle --}}
            <button @click="showSuccessNotif = false" class="relative flex-shrink-0 text-white hover:text-green-100 transition-colors w-10 h-10 flex items-center justify-center">
                {{-- SVG Timer Circle --}}
                <svg class="absolute inset-0 w-full h-full p-1" viewBox="0 0 36 36">
                     <path class="text-green-700/40 timer-circle" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" style="stroke-dasharray: 100; stroke-dashoffset: 0;"></path>
                     <path class="text-white timer-circle animate-timer" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" stroke="currentColor"></path>
                </svg>
                {{-- Icon X --}}
                <svg class="w-4 h-4 relative z-10" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>

        {{-- Floating Notification Error --}}
        <div x-show="showErrorNotif" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-x-full"
             x-transition:enter-end="opacity-100 transform translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-x-0"
             x-transition:leave-end="opacity-0 transform translate-x-full"
             x-init="if(showErrorNotif) setTimeout(() => showErrorNotif = false, 5000)"
             class="fixed top-4 right-4 z-50 bg-red-500 text-white pl-6 pr-2 py-1 rounded-lg shadow-2xl flex items-center gap-3 min-w-[300px] max-w-md"
             style="display: none;">
            <div class="flex-shrink-0">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="flex-1">
                <p class="font-semibold text-sm">{{ session('error') }}</p>
            </div>
            {{-- Tombol Close dengan Timer Circle --}}
            <button @click="showErrorNotif = false" class="relative flex-shrink-0 text-white hover:text-red-100 transition-colors w-10 h-10 flex items-center justify-center">
                {{-- SVG Timer Circle --}}
                <svg class="absolute inset-0 w-full h-full p-1" viewBox="0 0 36 36">
                     <path class="text-red-800/40 timer-circle" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" style="stroke-dasharray: 100; stroke-dashoffset: 0;"></path>
                     <path class="text-white timer-circle animate-timer" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" stroke="currentColor"></path>
                </svg>
                <svg class="w-4 h-4 relative z-10" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>

        @if ($errors->any())
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 10000)"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-full"
                x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-full"
                class="fixed top-4 right-4 z-50 max-w-sm w-full bg-white rounded-lg shadow-lg border-l-4 border-red-500 p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <ul class="text-sm text-gray-900 space-y-1">@foreach ($errors->all() as $error) <li>{{ $error }}</li>
                        @endforeach</ul>
                    </div>
                    <button @click="show = false" class="ml-4 flex-shrink-0 text-gray-400 hover:text-gray-500">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        @endif

    {{-- Form Filter --}}
        <form id="filterForm" action="{{ route('supervisor.presensi.index') }}" method="GET" x-data="{}">
            @csrf 
            <div class="bg-white px-6 py-5 rounded-xl shadow-sm mb-6 border border-gray-200">

                <div class="flex flex-wrap gap-4">

                    {{-- Show Entries --}}
                    <div class="w-[calc(50%-0.5rem)] md:w-auto">
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Show</label>
                        <div class="flex items-center gap-2">
                            <div class="relative">
                                <select name="per_page" id="perPage"
                                    class="block h-[42px] pl-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer appearance-none">
                                    <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5</option>
                                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                            <span class="text-sm text-gray-600 whitespace-nowrap">rows</span>
                        </div>
                    </div>

                    {{-- Filter Tanggal --}}
                    <div class="w-[calc(50%-0.5rem)] md:flex-1">
                        <label for="tanggal"
                            class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                            Tanggal
                        </label>
                        <div class="cursor-pointer" @click="$refs.dateInput.showPicker()">
                            <input type="date" id="tanggal" name="tanggal" x-ref="dateInput"
                                class="block w-full h-[42px] px-4 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer"
                                value="{{ $tanggalTerpilih }}">
                        </div>
                    </div>

                    {{-- Filter Jenis Shift --}}
                    <div class="w-[calc(50%-0.5rem)] md:flex-1">
                        <label for="shift" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                            Jenis Shift
                        </label>
                        <div class="relative">
                            <select id="shift" name="shift"
                                class="block w-full h-[42px] px-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer appearance-none">
                                <option value="semua" @if($shiftTerpilih == 'semua') selected @endif>Semua Shift</option>
                                <option value="Pagi" @if($shiftTerpilih == 'Pagi') selected @endif>Shift Pagi</option>
                                <option value="Malam" @if($shiftTerpilih == 'Malam') selected @endif>Shift Malam</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        {{-- AJAX Loading Indicator --}}
        <div id="loading" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-30">
            <div class="bg-white p-4 rounded-lg shadow-lg flex items-center gap-3">
                <svg class="animate-spin h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-sm font-semibold text-gray-700">Memuat data...</span>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const filterForm = document.getElementById('filterForm');
                const loadingComponent = document.getElementById('loading');
                const inputs = filterForm.querySelectorAll('select, input');

                function fetchData(url = null) {
                    loadingComponent.classList.remove('hidden');
                    
                    const formData = new FormData(filterForm);
                    // Jika URL tidak diberikan, gunakan URL form default
                    const fetchUrl = url || filterForm.action + '?' + new URLSearchParams(formData).toString();

                    // --- IMPLEMENTASI PERSISTENSI URL ---
                    // Mengubah URL browser tanpa reload agar saat refresh/back tetap di tanggal yang sama
                    if (!url) { // Hanya update jika trigger dari filter change, bukan pagination click (opsional, tapi lebih rapi)
                         history.pushState(null, '', fetchUrl);
                    } else {
                         // Jika pagination, kita juga update URL biar rapi
                         history.pushState(null, '', url);
                    }

                    fetch(fetchUrl, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Update DOM Wrappers
                        document.getElementById('presensi-masuk-wrapper').innerHTML = data.html_masuk;
                        document.getElementById('presensi-pulang-wrapper').innerHTML = data.html_pulang;
                        
                        // Re-initialize any necessary JS handlers here if not using event delegation
                    })
                    .catch(error => {
                        console.error('Error fetching data:', error);
                        alert('Gagal memuat data presensi.');
                    })
                    .finally(() => {
                        loadingComponent.classList.add('hidden');
                    });
                }

                // Handle Browser Back/Forward Button
                window.addEventListener('popstate', function() {
                    // Reload page to ensure server renders correct state or re-fetch via AJAX
                    // Simplest approach: Reload
                    window.location.reload(); 
                });

                // Handle Filter Changes
                inputs.forEach(input => {
                    input.addEventListener('change', () => {
                        fetchData();
                    });
                });

                // Handle Pagination Clicks (Event Delegation)
                document.addEventListener('click', function(e) {
                    if (e.target.closest('.pagination-link')) {
                        e.preventDefault();
                        const link = e.target.closest('.pagination-link');
                        const url = link.href;
                        fetchData(url);
                    }
                });
            });
        </script>

        {{-- DAFTAR PRESENSI MASUK --}}
        <div id="presensi-masuk-wrapper">
            @include('supervisor.partials.presensi-masuk')
        </div>

    {{-- DAFTAR PRESENSI PULANG --}}
    <div id="presensi-pulang-wrapper">
        @include('supervisor.partials.presensi-pulang')
    </div>

    {{-- MODAL FOTO --}}
    <div x-show="showPhotoModal" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
         @click.away="showPhotoModal = false"
         style="display: none;">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full relative overflow-hidden" @click.stop>
            <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] flex justify-between items-center p-4">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="text-xl font-bold text-white">FOTO PRESENSI</h3>
                </div>
                <button @click="showPhotoModal = false" class="text-white hover:text-gray-200 text-3xl">&times;</button>
            </div>
            <div class="mt-4">
                <img :src="photoUrl" alt="Foto Presensi" class="w-full h-auto rounded">
            </div>
        </div>
    </div>
    
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('presensiData', () => ({
            showPhotoModal: false, 
            photoUrl: '', 
            showSuccessNotif: {{ session('success') ? 'true' : 'false' }},
            showErrorNotif: {{ session('error') ? 'true' : 'false' }},
        }))
    })
</script>
@endsection
