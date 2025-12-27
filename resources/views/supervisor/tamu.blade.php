@extends('layouts.app')

{{-- Terapkan layout full-width jika ada --}}
@section('mobile_width', 'max-w-full')
@section('desktop_width', 'lg:max-w-full')

@section('header-left')
    <a class="flex items-center border-2 border-[#1a2847] text-[#1a2847] text-sm font-bold px-4 py-2 rounded-full">
        TAMU
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
        showEditModal: false, 
        editAction: '',
        editNama: '',
        editInstansi: '',
        editTujuan: '',
        editWaktuDatang: '',
        maxDate: '{{ now()->format('Y-m-d\T23:59') }}',
        showDeleteModal: false,
        deleteAction: '',
        showSuccessNotif: {{ session('success') ? 'true' : 'false' }},
        showErrorNotif: {{ session('error') ? 'true' : 'false' }},
        errorMessage: '{{ session('error') }}'
     }">




    
    <h2 class="text-2xl font-bold text-slate-800 mb-4">Laporan Kunjungan Tamu</h2>

    {{-- 1. Floating Notification Success --}}
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
        <button @click="showSuccessNotif = false" class="relative flex-shrink-0 text-white hover:text-green-100 transition-colors w-10 h-10 flex items-center justify-center">
            <svg class="absolute inset-0 w-full h-full p-1" viewBox="0 0 36 36">
                    <path class="text-green-700/40 timer-circle" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"></path>
                    <path class="text-white timer-circle animate-timer" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" stroke="currentColor"></path>
            </svg>
            <svg class="w-4 h-4 relative z-10" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
        </button>
    </div>

    {{-- 2. Floating Notification Error/Warning (Bisa dari Session ATAU Validasi JS) --}}
    <div x-show="showErrorNotif" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-x-full"
            x-transition:enter-end="opacity-100 transform translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-x-0"
            x-transition:leave-end="opacity-0 transform translate-x-full"
            x-init="if(showErrorNotif) setTimeout(() => showErrorNotif = false, 5000)"
            class="fixed top-4 right-4 z-50 bg-yellow-500 text-white pl-6 pr-2 py-1 rounded-lg shadow-2xl flex items-center gap-3 min-w-[300px] max-w-xs"
            style="display: none;">
        <div class="flex-shrink-0">
            {{-- Icon Warning Triangle --}}
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        </div>
        <div class="flex-1">
            {{-- Text Error Dinamis --}}
            <p class="font-bold text-sm" x-text="errorMessage"></p>
        </div>
        <button @click="showErrorNotif = false" class="relative flex-shrink-0 text-white hover:text-yellow-100 transition-colors w-10 h-10 flex items-center justify-center">
            {{-- Timer Circle (Warna Kuning Gelap) --}}
            <svg class="absolute inset-0 w-full h-full p-1" viewBox="0 0 36 36">
                    <path class="text-yellow-700/40 timer-circle" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"></path>
                    <path class="text-white timer-circle animate-timer" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" stroke="currentColor"></path>
            </svg>
            <svg class="w-4 h-4 relative z-10" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
        </button>
    </div>

    @if ($errors->any())
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
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

    {{-- Tabel Riwayat Tamu --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] p-3 border-b border-[#2a4a6f]">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <h3 class="font-bold text-white">RIWAYAT KUNJUNGAN</h3>
                </div>
        </div>

        {{-- Form Filter --}}
            <form id="filterFormTamu" action="{{ route('supervisor.tamu.index') }}" method="GET" class="p-4 border-b border-gray-200">
                <div class="flex flex-wrap gap-4">
                    {{-- Show Dropdown --}}
                    <div class="w-[calc(50%-0.5rem)] md:w-auto">
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Show</label>
                        <div class="flex items-center gap-2">
                            <select name="per_page" id="per_page"
                                class="h-[42px] pl-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm appearance-none cursor-pointer"
                                style="background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27currentColor%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3e%3cpolyline points=%276 9 12 15 18 9%27%3e%3c/polyline%3e%3c/svg%3e'); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 1.25em 1.25em;">
                                <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5</option>
                                <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                            </select>
                            <span class="text-sm text-gray-600">rows</span>
                        </div>
                    </div>

                    {{-- Filter Dari Tanggal --}}
                    <div class="w-[calc(50%-0.5rem)] md:flex-1">
                        <label for="start_date"
                            class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Dari
                            Tanggal</label>
                        <div class="cursor-pointer" @click="$refs.dateStart.showPicker()">
                            <input type="date" id="start_date" name="start_date" x-ref="dateStart"
                                class="block w-full h-[42px] px-4 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer"
                                value="{{ $startDate }}">
                        </div>
                    </div>

                    {{-- Filter Sampai Tanggal --}}
                    <div class="w-[calc(50%-0.5rem)] md:flex-1">
                        <label for="end_date"
                            class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Sampai
                            Tanggal</label>
                        <div class="cursor-pointer" @click="$refs.dateEnd.showPicker()">
                            <input type="date" id="end_date" name="end_date" x-ref="dateEnd"
                                class="block w-full h-[42px] px-4 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer"
                                value="{{ $endDate }}">
                        </div>
                    </div>

                    {{-- Filter Pencarian (Search) --}}
                    <div class="w-full md:flex-1">
                        <label for="cari"
                            class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Cari</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" id="cari" name="cari" value="{{ request('cari') }}"
                                class="block w-full h-[42px] pl-10 pr-4 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm"
                                placeholder="Nama atau Instansi...">
                        </div>
                    </div>
                </div>
            </form>
        
        <div id="tamu-results">
            @include('supervisor.partials.tamu-list')
        </div>
    </div>

    {{-- Modal Edit Tamu --}}
    <div x-show="showEditModal"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
         @click.away="showEditModal = false"
         style="display: none;">
        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full relative overflow-hidden" @click.stop>
            {{-- Header Biru --}}
            <div class="bg-[#1e3a5f] py-4 px-6 border-b border-[#1e3a5f] flex justify-between items-center">
                <h3 class="text-lg font-bold text-white flex items-center tracking-wide">
                    EDIT DATA TAMU
                </h3>
                <button @click="showEditModal = false" class="text-white/70 hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <form :action="editAction" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body max-h-[70vh] overflow-y-auto p-6">
                    <div class="space-y-5">
                        
                        {{-- GROUP: Informasi Tamu --}}
                        <div class="p-4 bg-blue-50/50 rounded-xl border border-blue-100">

                            
                            <div class="space-y-4">
                                {{-- Nama Tamu --}}
                                <div>
                                    <label for="nama_tamu" class="block text-xs font-bold text-[#1e3a5f] uppercase tracking-wide mb-1">Nama Tamu <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-[#1e3a5f]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        </div>
                                        <input type="text" id="nama_tamu" name="nama_tamu" x-model="editNama" required placeholder="Nama lengkap tamu"
                                               class="pl-10 w-full bg-white border border-gray-300 text-gray-800 text-sm font-medium rounded-lg shadow-sm focus:ring-[#1e3a5f] focus:border-[#1e3a5f] block p-2.5">
                                    </div>
                                </div>
                                
                                {{-- Instansi --}}
                                <div>
                                    <label for="instansi" class="block text-xs font-bold text-[#1e3a5f] uppercase tracking-wide mb-1">Instansi <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-[#1e3a5f]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        </div>
                                        <input type="text" id="instansi" name="instansi" x-model="editInstansi" required placeholder="Nama instansi/perusahaan"
                                               class="pl-10 w-full bg-white border border-gray-300 text-gray-800 text-sm font-medium rounded-lg shadow-sm focus:ring-[#1e3a5f] focus:border-[#1e3a5f] block p-2.5">
                                    </div>
                                </div>

                                {{-- Tujuan --}}
                                <div>
                                    <label for="tujuan" class="block text-xs font-bold text-[#1e3a5f] uppercase tracking-wide mb-1">Tujuan <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-[#1e3a5f]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                        </div>
                                        <input type="text" id="tujuan" name="tujuan" x-model="editTujuan" required placeholder="Tujuan kunjungan"
                                               class="pl-10 w-full bg-white border border-gray-300 text-gray-800 text-sm font-medium rounded-lg shadow-sm focus:ring-[#1e3a5f] focus:border-[#1e3a5f] block p-2.5">
                                    </div>
                                </div>
                                
                                {{-- Waktu Kunjungan --}}
                                <div>
                                    <label for="waktu_datang" class="block text-xs font-bold text-[#1e3a5f] uppercase tracking-wide mb-1">Waktu Kunjungan <span class="text-red-500">*</span></label>
                                    <div class="relative cursor-pointer" @click="$refs.waktuDatangInput.showPicker()">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-[#1e3a5f]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <input type="datetime-local" id="waktu_datang" name="waktu_datang" x-ref="waktuDatangInput" x-model="editWaktuDatang" :max="maxDate" required
                                               class="pl-10 w-full bg-white border border-gray-300 text-gray-800 text-sm font-medium rounded-lg shadow-sm focus:ring-[#1e3a5f] focus:border-[#1e3a5f] block p-2.5 cursor-pointer">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer p-4 border-t bg-gray-50">
                    <button type="submit" class="w-full px-4 py-3 text-white font-bold bg-[#1e3a5f] rounded-xl hover:bg-[#2a4a6f] shadow-lg transition transform hover:-translate-y-0.5">
                        SIMPAN PERUBAHAN
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Hapus --}}
    <div x-show="showDeleteModal"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
         @click.away="showDeleteModal = false"
         style="display: none;">
        <div class="bg-white rounded-lg shadow-xl max-w-sm w-full p-6 relative" @click.stop>
            <h3 class="text-lg font-bold text-gray-900 mb-4">Konfirmasi Hapus</h3>
            <p class="text-gray-600 mb-6">
                Apakah Anda yakin ingin menghapus data tamu ini? Tindakan ini tidak dapat dibatalkan.
            </p>
            <form :action="deleteAction" method="POST" class="flex justify-end space-x-4">
                @csrf
                @method('DELETE')
                <button type="button" @click="showDeleteModal = false" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300">
                    Batal
                </button>
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                    Ya, Hapus
                </button>
            </form>
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

{{-- SCRIPT AJAX SEARCH & PAGINATION --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('cari');
    const perPageSelect = document.getElementById('per_page');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const resultsContainer = document.getElementById('tamu-results');
    
    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    function fetchResults(url) {
        toggleLoading(true);
        if (!url) {
            const params = new URLSearchParams(window.location.search);
            if (searchInput.value) params.set('cari', searchInput.value); else params.delete('cari');
            if (perPageSelect.value) params.set('per_page', perPageSelect.value);
            if (startDateInput.value) params.set('start_date', startDateInput.value);
            if (endDateInput.value) params.set('end_date', endDateInput.value);
            
            params.delete('page');

            url = `${window.location.pathname}?${params.toString()}`;

            window.history.pushState({}, '', url);
        }

        fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                resultsContainer.innerHTML = html;
            })
            .catch(error => console.error('Error:', error))
            .finally(() => {
                toggleLoading(false);
            });
    }

    function toggleLoading(show) {
        const loader = document.getElementById('loading-indicator');
        if (loader) loader.style.display = show ? 'flex' : 'none';
    }

    const debouncedFetch = debounce(() => fetchResults(), 300);

    searchInput.addEventListener('input', debouncedFetch);
    perPageSelect.addEventListener('change', () => fetchResults());
    startDateInput.addEventListener('change', () => fetchResults());
    endDateInput.addEventListener('change', () => fetchResults());

    resultsContainer.addEventListener('click', function(e) {
        if (e.target.closest('.pagination-link')) {
            e.preventDefault();
            const link = e.target.closest('.pagination-link');
            fetchResults(link.href);
            window.history.pushState({}, '', link.href);
        }
    });

    window.addEventListener('popstate', function() {
        fetchResults(window.location.href);
    });
});
</script>
@endsection
