@extends('layouts.app')

{{-- Terapkan layout full-width --}}
@section('mobile_width', 'max-w-full')
@section('desktop_width', 'lg:max-w-full')

@section('header-left')
    <a class="flex items-center border-2 border-[#1a2847] text-[#1a2847] text-sm font-bold px-4 py-2 rounded-full">
        BARANG
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

{{-- Wrapper Alpine.js untuk Modal Foto --}}
<div class="w-full mx-auto"
     x-data="{ 
        showPhotoModal: false, 
        photos: [],
        currentPhotoIndex: 0,
        touchStartX: 0,
        touchEndX: 0,
        showTemuan: true,
        showTitipan: true,
        showSuccessNotif: {{ session('success') ? 'true' : 'false' }},
        showErrorNotif: {{ session('error') ? 'true' : 'false' }}
     }">
    
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
    
    <h2 class="text-2xl font-bold text-slate-800 mb-4">Laporan Barang</h2>

    <div id="barang-results" class="space-y-6">
        {{-- Tabel Barang Temuan --}}
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] p-3 border-b border-[#2a4a6f] cursor-pointer hover:bg-[#2a4a6f] transition" @click="showTemuan = !showTemuan">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <h3 class="font-bold text-white">BARANG TEMUAN</h3>
                    </div>
                    <svg class="w-5 h-5 text-white transition-transform" :class="{ 'rotate-180': !showTemuan }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>

            <div x-show="showTemuan" x-collapse>
                {{-- Form Filter Temuan --}}
                <form action="{{ route('komandan.barang') }}" method="GET" class="p-4 border-b border-gray-200" onsubmit="return false;">
                    <div class="flex flex-wrap gap-4">
                        <div class="w-[calc(50%-0.5rem)] md:w-auto">
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Show</label>
                            <div class="flex items-center gap-2">
                                <select name="per_page_temuan" id="per_page_temuan" class="w-full h-[42px] pl-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm appearance-none cursor-pointer" style="background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27currentColor%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3e%3cpolyline points=%276 9 12 15 18 9%27%3e%3c/polyline%3e%3c/svg%3e'); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 1.25em 1.25em;">
                                    <option value="5" {{ $perPageTemuan == 5 ? 'selected' : '' }}>5</option>
                                    <option value="10" {{ $perPageTemuan == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ $perPageTemuan == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ $perPageTemuan == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ $perPageTemuan == 100 ? 'selected' : '' }}>100</option>
                                </select>
                                <span class="text-sm text-gray-600">rows</span>
                            </div>
                        </div>
                        <div class="w-[calc(50%-0.5rem)] md:flex-1">
                            <label for="tanggal_temuan" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Tanggal</label>
                            <div class="cursor-pointer" @click="$refs.dateInputTemuan.showPicker()">
                                <input type="date" id="tanggal_temuan" name="tanggal_temuan" x-ref="dateInputTemuan" class="block w-full h-[42px] px-4 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer" value="{{ $tanggalTemuan }}">
                            </div>
                        </div>
                        <div class="w-[calc(50%-0.5rem)] md:flex-1">
                            <label for="status_temuan" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Status</label>
                            <select name="status_temuan" id="status_temuan" class="w-full h-[42px] pl-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm appearance-none cursor-pointer" style="background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27currentColor%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3e%3cpolyline points=%276 9 12 15 18 9%27%3e%3c/polyline%3e%3c/svg%3e'); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 1.25em 1.25em;">
                                <option value="" {{ ($statusTemuan ?? '') == '' ? 'selected' : '' }}>Semua Status</option>
                                <option value="belum selesai" {{ ($statusTemuan ?? '') == 'belum selesai' ? 'selected' : '' }}>Belum Selesai</option>
                                <option value="selesai" {{ ($statusTemuan ?? '') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            </select>
                        </div>
                        <div class="w-[calc(50%-0.5rem)] md:flex-1">
                            <label for="searchInputTemuan" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Cari Barang</label>
                            <input type="text" id="searchInputTemuan" name="search_temuan" class="block w-full h-[42px] px-4 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm placeholder-gray-400" value="{{ $searchTemuan }}" placeholder="Ketik untuk mencari...">
                        </div>
                    </div>
                </form>

                {{-- Container Data Temuan --}}
                <div id="barang-temuan-container">
                    @include('komandan.partials.barang-table-temuan')
                </div>
            </div>
        </div>

        {{-- Tabel Barang Titipan --}}
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] p-3 border-b border-[#2a4a6f] cursor-pointer hover:bg-[#2a4a6f] transition" @click="showTitipan = !showTitipan">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <h3 class="font-bold text-white">BARANG TITIPAN</h3>
                    </div>
                    <svg class="w-5 h-5 text-white transition-transform" :class="{ 'rotate-180': !showTitipan }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>

            <div x-show="showTitipan" x-collapse>
                {{-- Form Filter Titipan --}}
                <form action="{{ route('komandan.barang') }}" method="GET" class="p-4 border-b border-gray-200" onsubmit="return false;">
                    <div class="flex flex-wrap gap-4">
                        <div class="w-[calc(50%-0.5rem)] md:w-auto">
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Show</label>
                            <div class="flex items-center gap-2">
                                <select name="per_page_titipan" id="per_page_titipan" class="w-full h-[42px] pl-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm appearance-none cursor-pointer" style="background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27currentColor%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3e%3cpolyline points=%276 9 12 15 18 9%27%3e%3c/polyline%3e%3c/svg%3e'); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 1.25em 1.25em;">
                                    <option value="5" {{ $perPageTitipan == 5 ? 'selected' : '' }}>5</option>
                                    <option value="10" {{ $perPageTitipan == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ $perPageTitipan == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ $perPageTitipan == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ $perPageTitipan == 100 ? 'selected' : '' }}>100</option>
                                </select>
                                <span class="text-sm text-gray-600">rows</span>
                            </div>
                        </div>
                        <div class="w-[calc(50%-0.5rem)] md:flex-1">
                            <label for="tanggal_titipan" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Tanggal</label>
                            <div class="cursor-pointer" @click="$refs.dateInputTitipan.showPicker()">
                                <input type="date" id="tanggal_titipan" name="tanggal_titipan" x-ref="dateInputTitipan" class="block w-full h-[42px] px-4 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer" value="{{ $tanggalTitipan }}">
                            </div>
                        </div>
                        <div class="w-[calc(50%-0.5rem)] md:flex-1">
                            <label for="status_titipan" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Status</label>
                            <select name="status_titipan" id="status_titipan" class="w-full h-[42px] pl-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm appearance-none cursor-pointer" style="background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27currentColor%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3e%3cpolyline points=%276 9 12 15 18 9%27%3e%3c/polyline%3e%3c/svg%3e'); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 1.25em 1.25em;">
                                <option value="" {{ ($statusTitipan ?? '') == '' ? 'selected' : '' }}>Semua Status</option>
                                <option value="belum selesai" {{ ($statusTitipan ?? '') == 'belum selesai' ? 'selected' : '' }}>Belum Selesai</option>
                                <option value="selesai" {{ ($statusTitipan ?? '') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            </select>
                        </div>
                        <div class="w-[calc(50%-0.5rem)] md:flex-1">
                            <label for="searchInputTitipan" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Cari Barang</label>
                            <input type="text" id="searchInputTitipan" name="search_titipan" class="block w-full h-[42px] px-4 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm placeholder-gray-400" value="{{ $searchTitipan }}" placeholder="Ketik untuk mencari...">
                        </div>
                    </div>
                </form>

                {{-- Container Data Titipan --}}
                <div id="barang-titipan-container">
                    @include('komandan.partials.barang-table-titipan')
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT PENERIMA --}}
    <div id="editModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4" style="display: none;">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md" onclick="event.stopPropagation()">
            <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] flex justify-between items-center p-4">
                <h3 class="text-xl font-bold text-white">Edit Nama Penerima</h3>
                <button onclick="closeEditModal()" class="text-white hover:text-gray-200 text-3xl">&times;</button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="p-6">
                    <label for="tujuan" class="block text-sm font-semibold text-gray-700 mb-2">Nama Penerima:</label>
                    <input type="text" id="tujuan" name="tujuan" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="flex justify-end gap-3 p-6 pt-0">
                    <button type="button" onclick="closeEditModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL EDIT COMPREHENSIVE --}}
    <div id="editComprehensiveModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4" style="display: none;">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg" onclick="event.stopPropagation()">
            <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] flex justify-between items-center p-4">
                <h3 id="editComprehensiveTitle" class="text-xl font-bold text-white">Edit Barang</h3>
                <button onclick="closeEditComprehensiveModal()" class="text-white hover:text-gray-200 text-3xl">&times;</button>
            </div>
            <form id="editComprehensiveForm" method="POST">
                @csrf
                @method('PUT')
                <div class="p-6 space-y-4">
                    <div>
                        <label for="edit_tanggal" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal:</label>
                        <input type="date" id="edit_tanggal" name="tanggal" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="edit_nama_barang" class="block text-sm font-semibold text-gray-700 mb-2">Nama Barang:</label>
                        <input type="text" id="edit_nama_barang" name="nama_barang" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div id="edit_pelapor_field">
                        <label for="edit_nama_pelapor" class="block text-sm font-semibold text-gray-700 mb-2">Pelapor:</label>
                        <input type="text" id="edit_nama_pelapor" name="nama_pelapor"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div id="edit_penitip_field">
                        <label for="edit_nama_penitip" class="block text-sm font-semibold text-gray-700 mb-2">Penitip:</label>
                        <input type="text" id="edit_nama_penitip" name="nama_penitip"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div id="edit_lokasi_field">
                        <label for="edit_lokasi_penemuan" class="block text-sm font-semibold text-gray-700 mb-2">Lokasi Penemuan:</label>
                        <input type="text" id="edit_lokasi_penemuan" name="lokasi_penemuan"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div id="edit_penerima_field">
                        <label for="edit_nama_penerima" class="block text-sm font-semibold text-gray-700 mb-2">Nama Penerima:</label>
                        <input type="text" id="edit_nama_penerima" name="nama_penerima"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="edit_catatan" class="block text-sm font-semibold text-gray-700 mb-2">Catatan:</label>
                        <textarea id="edit_catatan" name="catatan" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-3 p-6 pt-0">
                    <button type="button" onclick="closeEditComprehensiveModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL FOTO --}}
    <div x-show="showPhotoModal" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
         @click.away="showPhotoModal = false"
         style="display: none;">
        <div class="bg-white rounded-lg shadow-xl w-auto max-w-[95vw] max-h-[90vh] relative overflow-hidden flex flex-col" @click.stop>
            <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] flex justify-between items-center p-4 shrink-0">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="text-xl font-bold text-white" x-text="currentPhotoIndex === 0 ? 'FOTO BARANG' : 'FOTO PENERIMA'"></h3>
                </div>
                <button @click="showPhotoModal = false" class="text-white hover:text-gray-200 text-3xl">&times;</button>
            </div>
            <div class="p-4 flex flex-col items-center bg-gray-50 overflow-auto">
                <div class="relative">
                    <img :src="photos[currentPhotoIndex]" alt="Foto" class="max-h-[75vh] w-auto h-auto object-contain rounded shadow-md">
                    <button x-show="currentPhotoIndex > 0" @click="currentPhotoIndex--" class="absolute left-2 top-1/2 -translate-y-1/2 bg-black bg-opacity-50 hover:bg-opacity-75 text-white rounded-full w-8 h-8 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </button>
                    <button x-show="currentPhotoIndex < photos.length - 1" @click="currentPhotoIndex++" class="absolute right-2 top-1/2 -translate-y-1/2 bg-black bg-opacity-50 hover:bg-opacity-75 text-white rounded-full w-8 h-8 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>
                <div class="flex justify-center gap-2 mt-3 shrink-0" x-show="photos.length > 1">
                    <template x-for="(photo, index) in photos" :key="index">
                        <button @click="currentPhotoIndex = index" :class="currentPhotoIndex === index ? 'bg-gray-800 w-6' : 'bg-gray-400 w-2'" class="h-2 rounded-full transition-all"></button>
                    </template>
                </div>
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

{{-- SCRIPT AJAX SEARCH & PAGINATION --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const resultsContainer = document.getElementById('barang-results');

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
            const searchInputTemuan = document.getElementById('searchInputTemuan');
            const searchInputTitipan = document.getElementById('searchInputTitipan');
            const dateInputTemuan = document.getElementById('tanggal_temuan');
            const dateInputTitipan = document.getElementById('tanggal_titipan');
            const perPageTemuan = document.getElementById('per_page_temuan');
            const perPageTitipan = document.getElementById('per_page_titipan');
            const statusTemuan = document.getElementById('status_temuan');
            const statusTitipan = document.getElementById('status_titipan');

            const params = new URLSearchParams(window.location.search);
            
            if (searchInputTemuan && searchInputTemuan.value) params.set('search_temuan', searchInputTemuan.value); else params.delete('search_temuan');
            if (searchInputTitipan && searchInputTitipan.value) params.set('search_titipan', searchInputTitipan.value); else params.delete('search_titipan');
            if (dateInputTemuan && dateInputTemuan.value) params.set('tanggal_temuan', dateInputTemuan.value);
            if (dateInputTitipan && dateInputTitipan.value) params.set('tanggal_titipan', dateInputTitipan.value);
            if (perPageTemuan && perPageTemuan.value) params.set('per_page_temuan', perPageTemuan.value);
            if (perPageTitipan && perPageTitipan.value) params.set('per_page_titipan', perPageTitipan.value);
            if (statusTemuan && statusTemuan.value) params.set('status_temuan', statusTemuan.value); else params.delete('status_temuan');
            if (statusTitipan && statusTitipan.value) params.set('status_titipan', statusTitipan.value); else params.delete('status_titipan');

            params.delete('page_temuan');
            params.delete('page_titipan');

            url = `${window.location.pathname}?${params.toString()}`;

            window.history.pushState({}, '', url);
        }

        fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(async response => {
                if (!response.ok) {
                    const text = await response.text();
                    try {
                        const json = JSON.parse(text);
                        throw new Error(json.message || json.exception || 'Server Error');
                    } catch (e) {
                        throw new Error('Server Error: ' + response.statusText);
                    }
                }
                return response.json();
            })
            .then(data => {
                if (!data.html_temuan || !data.html_titipan) {
                    throw new Error('Response missing HTML data');
                }
                document.getElementById('barang-temuan-container').innerHTML = data.html_temuan;
                document.getElementById('barang-titipan-container').innerHTML = data.html_titipan;
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('Terjadi kesalahan: ' + error.message);
            })
            .finally(() => {
                toggleLoading(false);
            });
    }

    function toggleLoading(show) {
        const loader = document.getElementById('loading-indicator');
        if (loader) loader.style.display = show ? 'flex' : 'none';
    }

    function attachListeners() {
        const debouncedFetch = debounce(() => fetchResults(), 500);

        const searchInputTemuan = document.getElementById('searchInputTemuan');
        if (searchInputTemuan) searchInputTemuan.addEventListener('input', debouncedFetch);
        
        const searchInputTitipan = document.getElementById('searchInputTitipan');
        if (searchInputTitipan) searchInputTitipan.addEventListener('input', debouncedFetch);

        const dateInputTemuan = document.getElementById('tanggal_temuan');
        if (dateInputTemuan) dateInputTemuan.addEventListener('change', () => fetchResults());

        const dateInputTitipan = document.getElementById('tanggal_titipan');
        if (dateInputTitipan) dateInputTitipan.addEventListener('change', () => fetchResults());

        const perPageTemuan = document.getElementById('per_page_temuan');
        if (perPageTemuan) perPageTemuan.addEventListener('change', () => fetchResults());

        const perPageTitipan = document.getElementById('per_page_titipan');
        if (perPageTitipan) perPageTitipan.addEventListener('change', () => fetchResults());

        const statusTemuan = document.getElementById('status_temuan');
        if (statusTemuan) statusTemuan.addEventListener('change', () => fetchResults());

        const statusTitipan = document.getElementById('status_titipan');
        if (statusTitipan) statusTitipan.addEventListener('change', () => fetchResults());
    }

    attachListeners();

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

// Use event delegation to handle edit button clicks
document.addEventListener('click', function(e) {
    if (e.target.closest('.edit-penerima-btn')) {
        e.preventDefault();
        const button = e.target.closest('.edit-penerima-btn');
        const id = button.dataset.id;
        const tujuan = button.dataset.tujuan;
        console.log('Edit button clicked - ID:', id, 'Tujuan:', tujuan); // Debug log
        if (id && id !== '' && id !== 'undefined') {
            openEditModal(id, tujuan);
        } else {
            console.error('ID barang tidak valid:', id);
            alert('Error: ID barang tidak ditemukan');
        }
    }
    
    if (e.target.closest('.edit-comprehensive-btn')) {
        e.preventDefault();
        const button = e.target.closest('.edit-comprehensive-btn');
        const data = {
            id: button.dataset.id,
            type: button.dataset.type,
            tanggal: button.dataset.tanggal,
            nama_barang: button.dataset.namaBarang,
            nama_pelapor: button.dataset.namaPelapor,
            nama_penitip: button.dataset.namaPenitip,
            lokasi_penemuan: button.dataset.lokasiPenemuan,
            nama_penerima: button.dataset.namaPenerima,
            catatan: button.dataset.catatan
        };
        if (data.id && data.id !== '' && data.id !== 'undefined') {
            openEditComprehensiveModal(data);
        } else {
            console.error('ID barang tidak valid:', data.id);
            alert('Error: ID barang tidak ditemukan');
        }
    }
});

// Edit Modal Functions
function openEditModal(id, currentTujuan) {
    console.log('Opening modal with ID:', id, 'Tujuan:', currentTujuan); // Debug log
    if (!id || id === '' || id === 'undefined') {
        console.error('Invalid ID passed to openEditModal:', id);
        alert('Error: ID barang tidak valid');
        return;
    }
    document.getElementById('editModal').style.display = 'flex';
    document.getElementById('tujuan').value = currentTujuan || '';
    document.getElementById('editForm').action = `/komandan/barang/titipan/${id}/edit-penerima`;
    console.log('Form action set to:', document.getElementById('editForm').action); // Debug log
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

// Comprehensive Edit Modal Functions
function openEditComprehensiveModal(data) {
    console.log('Opening comprehensive modal with data:', data);
    if (!data.id || data.id === '' || data.id === 'undefined') {
        console.error('Invalid ID passed to openEditComprehensiveModal:', data.id);
        alert('Error: ID barang tidak valid');
        return;
    }
    
    const modal = document.getElementById('editComprehensiveModal');
    const title = document.getElementById('editComprehensiveTitle');
    const form = document.getElementById('editComprehensiveForm');
    
    // Set title and form action
    title.textContent = data.type === 'temuan' ? 'Edit Barang Temuan' : 'Edit Barang Titipan';
    form.action = `/komandan/barang/${data.type}/${data.id}/edit`;
    
    // Fill form fields
    document.getElementById('edit_tanggal').value = data.tanggal || '';
    document.getElementById('edit_nama_barang').value = data.nama_barang || '';
    document.getElementById('edit_catatan').value = data.catatan || '';
    
    // Show/hide fields based on type
    const pelapor = document.getElementById('edit_pelapor_field');
    const penitip = document.getElementById('edit_penitip_field');
    const lokasi = document.getElementById('edit_lokasi_field');
    const penerima = document.getElementById('edit_penerima_field');
    
    if (data.type === 'temuan') {
        pelapor.style.display = 'block';
        lokasi.style.display = 'block';
        penitip.style.display = 'none';
        penerima.style.display = 'none';
        
        document.getElementById('edit_nama_pelapor').value = data.nama_pelapor || '';
        document.getElementById('edit_lokasi_penemuan').value = data.lokasi_penemuan || '';
        document.getElementById('edit_nama_pelapor').required = true;
        document.getElementById('edit_lokasi_penemuan').required = true;
        document.getElementById('edit_nama_penitip').required = false;
        document.getElementById('edit_nama_penerima').required = false;
    } else {
        pelapor.style.display = 'none';
        lokasi.style.display = 'none';
        penitip.style.display = 'block';
        penerima.style.display = 'block';
        
        document.getElementById('edit_nama_penitip').value = data.nama_penitip || '';
        document.getElementById('edit_nama_penerima').value = data.nama_penerima || '';
        document.getElementById('edit_nama_pelapor').required = false;
        document.getElementById('edit_lokasi_penemuan').required = false;
        document.getElementById('edit_nama_penitip').required = true;
        document.getElementById('edit_nama_penerima').required = true;
    }
    
    modal.style.display = 'flex';
}

function closeEditComprehensiveModal() {
    document.getElementById('editComprehensiveModal').style.display = 'none';
}

// Initialize modal event listeners when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Close modal when clicking outside
    const editModal = document.getElementById('editModal');
    if (editModal) {
        editModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });
    }
    
    const editComprehensiveModal = document.getElementById('editComprehensiveModal');
    if (editComprehensiveModal) {
        editComprehensiveModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditComprehensiveModal();
            }
        });
    }

    // Handle form submission
    const editForm = document.getElementById('editForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeEditModal();
                    window.location.reload();
                } else {
                    alert(data.message || 'Terjadi kesalahan');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menyimpan data');
            });
        });
    }
    
    // Handle comprehensive edit form submission
    const editComprehensiveForm = document.getElementById('editComprehensiveForm');
    if (editComprehensiveForm) {
        editComprehensiveForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeEditComprehensiveModal();
                    window.location.reload();
                } else {
                    alert(data.message || 'Terjadi kesalahan');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menyimpan data');
            });
        });
    }
});
</script>
@endsection
