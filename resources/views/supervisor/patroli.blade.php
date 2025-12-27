@extends('layouts.app')

@section('header-left')
    <a class="flex items-center border-2 border-[#1a2847] text-[#1a2847] text-sm font-bold px-4 py-2 rounded-full">
        PATROLI
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
        .animate-timer {
            animation: countdown 5s linear forwards;
        }
    </style>

<div class="w-full mx-auto" 
     x-data="{ 
         showPhotoModal: false, 
         photoUrl: '', 
         showSuccessNotif: {{ session('success') ? 'true' : 'false' }},
         showErrorNotif: {{ session('error') ? 'true' : 'false' }}
     }">
    
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold text-slate-800">Laporan Patroli Anggota</h2>
    </div>

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

{{-- SHIFT PAGI - Daftar Patroli --}}
<div id="pagi-container" class="bg-white rounded-lg shadow-md overflow-hidden mb-6" x-data="{ showPagi: true }">
    <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] p-3 border-b border-[#2a4a6f] cursor-pointer hover:bg-[#2a4a6f] transition" @click="showPagi = !showPagi">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-300" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                </svg>
                <h3 class="font-bold text-white">SHIFT PAGI</h3>
            </div>
            <svg class="w-5 h-5 text-white transition-transform" :class="{ 'rotate-180': !showPagi }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </div>
    </div>

    <div x-show="showPagi" x-collapse>
        
        {{-- Form Filter Shift Pagi --}}
        <div class="px-6 py-5 border-b border-gray-200">
            <div class="flex flex-wrap gap-4">
                
                {{-- Show Entries --}}
                <div class="w-[calc(50%-0.5rem)] md:w-auto">
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Show</label>
                    <div class="flex items-center gap-2">
                        <div class="relative">
                            <select id="perPagePagi" name="per_page_pagi" class="filter-input block h-[42px] pl-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer appearance-none">
                                <option value="5" {{ $perPagePagi == 5 ? 'selected' : '' }}>5</option>
                                <option value="10" {{ $perPagePagi == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ $perPagePagi == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ $perPagePagi == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ $perPagePagi == 100 ? 'selected' : '' }}>100</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                        <span class="text-sm text-gray-600 whitespace-nowrap">rows</span>
                    </div>
                </div>
                
                {{-- Filter Tanggal --}}
                <div class="w-[calc(50%-0.5rem)] md:flex-1">
                    <label for="tanggal_pagi" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                        Tanggal
                    </label>
                    <div class="cursor-pointer" @click="$refs.dateInputPagi.showPicker()">
                        <input type="date" id="tanggal_pagi" name="tanggal_pagi" x-ref="dateInputPagi"
                            class="filter-input block w-full h-[42px] px-4 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer"
                            value="{{ $tanggalTerpilihPagi }}">
                    </div>
                </div>

                {{-- Filter Jenis Patroli --}}
                <div class="w-[calc(50%-0.5rem)] md:flex-1">
                    <label for="jenis_patroli_pagi" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                        Jenis Patroli
                    </label>
                    <div class="relative">
                        <select id="jenis_patroli_pagi" name="jenis_patroli_pagi" 
                                class="filter-input block w-full h-[42px] px-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer appearance-none">
                            @forelse($jenisPatroliOptions as $opsi)
                                <option value="{{ $opsi }}" {{ $jenisPatroliTerpilihPagi == $opsi ? 'selected' : '' }}>
                                    {{ $opsi }}
                                </option>
                            @empty
                                <option value="" disabled selected>Tidak ada data jenis patroli</option>
                            @endforelse
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="patroli-pagi-wrapper">
             @include('supervisor.partials.patroli-list', ['data' => $dataPatroliPagi, 'shift' => 'pagi'])
        </div>
        
    </div>
</div>

{{-- SHIFT MALAM - Daftar Patroli --}}
<div id="malam-container" class="bg-white rounded-lg shadow-md overflow-hidden mb-6" x-data="{ showMalam: true }">
    <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] p-3 border-b border-[#2a4a6f] cursor-pointer hover:bg-[#2a4a6f] transition" @click="showMalam = !showMalam">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-300" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                </svg>
                <h3 class="font-bold text-white">SHIFT MALAM</h3>
            </div>
            <svg class="w-5 h-5 text-white transition-transform" :class="{ 'rotate-180': !showMalam }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </div>
    </div>

    <div x-show="showMalam" x-collapse>
        
        {{-- Form Filter Shift Malam --}}
        <div class="px-6 py-5 border-b border-gray-200">
            <div class="flex flex-wrap gap-4">
                
                {{-- Show Entries --}}
                <div class="w-[calc(50%-0.5rem)] md:w-auto">
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Show</label>
                    <div class="flex items-center gap-2">
                        <div class="relative">
                            <select id="perPageMalam" name="per_page_malam" class="filter-input block h-[42px] pl-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer appearance-none">
                                <option value="5" {{ $perPageMalam == 5 ? 'selected' : '' }}>5</option>
                                <option value="10" {{ $perPageMalam == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ $perPageMalam == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ $perPageMalam == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ $perPageMalam == 100 ? 'selected' : '' }}>100</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                        <span class="text-sm text-gray-600 whitespace-nowrap">rows</span>
                    </div>
                </div>
                
                {{-- Filter Tanggal --}}
                <div class="w-[calc(50%-0.5rem)] md:flex-1">
                    <label for="tanggal_malam" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                        Tanggal
                    </label>
                    <div class="cursor-pointer" @click="$refs.dateInputMalam.showPicker()">
                        <input type="date" id="tanggal_malam" name="tanggal_malam" x-ref="dateInputMalam"
                            class="filter-input block w-full h-[42px] px-4 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer"
                            value="{{ $tanggalTerpilihMalam }}">
                    </div>
                </div>

                {{-- Filter Jenis Patroli --}}
                <div class="w-[calc(50%-0.5rem)] md:flex-1">
                    <label for="jenis_patroli_malam" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                        Jenis Patroli
                    </label>
                    <div class="relative">
                        <select id="jenis_patroli_malam" name="jenis_patroli_malam" 
                                class="filter-input block w-full h-[42px] px-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer appearance-none">
                            @forelse($jenisPatroliOptions as $opsi)
                                <option value="{{ $opsi }}" {{ $jenisPatroliTerpilihMalam == $opsi ? 'selected' : '' }}>
                                    {{ $opsi }}
                                </option>
                            @empty
                                <option value="" disabled selected>Tidak ada data jenis patroli</option>
                            @endforelse
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="patroli-malam-wrapper">
             @include('supervisor.partials.patroli-list', ['data' => $dataPatroliMalam, 'shift' => 'malam'])
        </div>
        
    </div>
</div>



    {{-- MODAL FOTO --}}
    <div x-show="showPhotoModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
        @click.away="showPhotoModal = false" style="display: none;">
        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full relative overflow-hidden" @click.stop>
            {{-- Header Biru --}}
            <div class="bg-[#1e3a5f] py-4 px-6 border-b border-[#1e3a5f] flex justify-between items-center">
                <h3 class="text-lg font-bold text-white flex items-center tracking-wide">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                    FOTO PATROLI
                </h3>
                <button @click="showPhotoModal = false" class="text-white/70 hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
            <div class="p-4">
                <img :src="photoUrl" alt="Foto Patroli" class="w-full h-auto rounded">
            </div>
        </div>
    </div>




    {{-- Loading Indicator --}}
    <div id="loading-indicator" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-30">
        <div class="bg-white p-4 rounded-lg shadow-xl flex items-center gap-3">
            <div class="animate-spin rounded-full h-8 w-8 border-4 border-[#1e3a5f] border-t-transparent"></div>
            <span class="font-bold text-[#1e3a5f]">Memuat Data...</span>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterInputs = document.querySelectorAll('.filter-input');

        filterInputs.forEach(input => {
            input.addEventListener('change', function() {
                fetchData();
            });
        });

        document.addEventListener('click', function(e) {
            if (e.target.closest('.pagination-link')) {
                e.preventDefault();
                const url = e.target.closest('.pagination-link').href;
                fetchData(url);
            }
        });

        function fetchData(url = null) {
            toggleLoading(true);

            if (!url) {
                const params = new URLSearchParams();

                params.append('tanggal_pagi', document.getElementById('tanggal_pagi').value);
                params.append('tanggal_malam', document.getElementById('tanggal_malam').value);
                
                params.append('per_page_pagi', document.getElementById('perPagePagi').value);
                params.append('jenis_patroli_pagi', document.getElementById('jenis_patroli_pagi').value);
                
                params.append('per_page_malam', document.getElementById('perPageMalam').value);
                params.append('jenis_patroli_malam', document.getElementById('jenis_patroli_malam').value);
                
                url = "{{ route('supervisor.patroli.index') }}?" + params.toString();
            }

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('patroli-pagi-wrapper').innerHTML = data.html_pagi;
                document.getElementById('patroli-malam-wrapper').innerHTML = data.html_malam;
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memuat data.');
            })
            .finally(() => {
                toggleLoading(false);
            });
        }
        
        function toggleLoading(show) {
            const loader = document.getElementById('loading-indicator');
            if (loader) loader.style.display = show ? 'flex' : 'none';
        }
    });
</script>
@endsection
