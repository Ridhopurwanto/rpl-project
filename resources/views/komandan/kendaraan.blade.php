@extends('layouts.app')

@section('header-left')
    <a class="inline-block border-2 border-[#1a2847] text-[#1a2847] text-sm font-bold px-4 py-1 rounded-full mb-4">
        KENDARAAN
    </a>
@endsection

@section('content')
<div class="w-full mx-auto"
     x-data="{ 
        showEditModal: false, editAction: '', editPlat: '', editPemilik: '', editTipe: '',
        showDeleteModal: false, deleteAction: '',
        showPromoteModal: false, promoteAction: ''
     }">
    
    <h2 class="text-2xl font-bold text-slate-800 mb-4">Laporan Kendaraan</h2>

    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            {{ session('error') }}
        </div>
    @endif

    {{-- Form Filter & Search --}}
    <form id="filterForm" action="{{ route('komandan.kendaraan') }}" method="GET" x-data="{}">
        <div class="bg-white px-6 py-5 rounded-xl shadow-sm mb-6 border border-gray-200">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                {{-- Filter Tanggal --}}
                <div class="w-full">
                    <label for="tanggal" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                        Tanggal
                    </label>
                    <div class="cursor-pointer" @click="$refs.dateInput.showPicker()">
                        <input type="date" id="tanggal" name="tanggal" x-ref="dateInput"
                               onchange="document.getElementById('filterForm').submit()"
                               class="block w-full h-[42px] px-4 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer"
                               value="{{ $tanggalTerpilih }}">
                    </div>
                </div>

                {{-- Filter Tipe --}}
                <div class="w-full">
                    <label for="tipe" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                        Tipe
                    </label>
                    <div class="relative">
                        <select id="tipe" name="tipe" 
                                onchange="document.getElementById('filterForm').submit()"
                                class="block w-full h-[42px] px-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer appearance-none">
                            <option value="">Semua Tipe</option>
                            <option value="Roda 2" {{ $tipeTerpilih == 'Roda 2' ? 'selected' : '' }}>Roda 2</option>
                            <option value="Roda 4" {{ $tipeTerpilih == 'Roda 4' ? 'selected' : '' }}>Roda 4</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Live Search Input --}}
                <div class="w-full">
                    <label for="searchInput" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                        Cari Kendaraan
                    </label>
                    <input type="text" id="searchInput" name="search" 
                           class="block w-full h-[42px] px-4 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm placeholder-gray-400" 
                           value="{{ $search ?? '' }}" 
                           placeholder="Ketik untuk mencari...">
                </div>
            </div>
        </div>
    </form>

    {{-- TABEL 1: RIWAYAT KELUAR/MASUK --}}
    <div id="riwayat-container" class="transition-opacity duration-200">
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="bg-gray-100 p-3 border-b border-gray-200">
                <h3 class="font-bold text-gray-800">RIWAYAT KELUAR/MASUK</h3>
            </div>
            
            {{-- TABEL (Desktop) --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full min-w-max table-fixed">
                    <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500">
                        <tr>
                            <th class="py-3 px-4 text-center w-[6%]">No</th>
                            <th class="py-3 px-4 text-center w-[14%]">Nopol</th>
                            <th class="py-3 px-4 text-center w-[20%]">Pemilik</th>
                            <th class="py-3 px-4 text-center w-[10%]">Tipe</th>
                            <th class="py-3 px-4 text-center w-[11%]">Masuk</th>
                            <th class="py-3 px-4 text-center w-[11%]">Keluar</th>
                            <th class="py-3 px-4 text-center w-[16%]">Ket.</th>
                            @if(Auth::user()->peran == 'komandan')
                                <th class="py-3 px-4 text-center w-[12%]">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-200">
                        @forelse($riwayat as $index => $log)
                        <tr>
                            <td class="py-2 px-4">{{ $index + 1 }}.</td>
                            <td class="py-2 px-4 font-medium">{{ $log->nopol ?? 'N/A' }}</td>
                            <td class="py-2 px-4">{{ $log->pemilik ?? 'N/A' }}</td>
                            <td class="py-2 px-4 text-center">
                                <span class="text-xs font-semibold px-2 py-1 rounded-full 
                                    {{ $log->tipe == 'Roda 4' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                    {{ $log->tipe ?? '-' }}
                                </span>
                            </td>
                            <td class="py-2 px-4 text-gray-700">
                                @if($log->waktu_masuk && $log->waktu_masuk->format('Y-m-d') == $tanggalTerpilih)
                                    {{ $log->waktu_masuk->format('H:i:s') }}
                                @else <span class="text-gray-400">-</span> @endif
                            </td>
                            <td class="py-2 px-4 text-gray-700">
                                @if($log->waktu_keluar && $log->waktu_keluar->format('Y-m-d') == $tanggalTerpilih)
                                    {{ $log->waktu_keluar->format('H:i:s') }}
                                @else <span class="text-gray-400">-</span> @endif
                            </td>
                            <td class="py-2 px-4">
                                @if(Auth::user()->peran == 'komandan')
                                    <form action="{{ route('komandan.kendaraan.log.updateKeterangan', $log->id_log) }}" method="POST">
                                        @csrf @method('PUT')
                                        <select name="keterangan" onchange="this.form.submit()" class="border-gray-300 rounded-lg shadow-sm text-xs py-1 focus:border-blue-500 focus:ring-blue-500">
                                            <option value="tidak menginap" {{ $log->keterangan == 'tidak menginap' ? 'selected' : '' }}>Tidak Menginap</option>
                                            <option value="menginap" {{ $log->keterangan == 'Menginap' ? 'selected' : '' }}>Menginap</option>
                                        </select>
                                    </form>
                                @else {{ $log->keterangan }} @endif
                            </td>
                            @if(Auth::user()->peran == 'komandan')
                                <td class="py-2 px-4 text-center">
                                    @if($log->kendaraan)
                                        <span class="text-green-500" title="Terdaftar"><svg class="w-6 h-6 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg></span>
                                    @else
                                        <button @click.prevent="showPromoteModal = true; promoteAction = '{{ route('komandan.kendaraan.log.promote', $log->id_log) }}'" class="text-blue-500 hover:text-blue-700"><svg class="w-6 h-6 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"></path></svg></button>
                                    @endif
                                </td>
                            @endif
                        </tr>
                        @empty
                        <tr><td colspan="8" class="py-4 px-4 text-center text-gray-500">Data tidak ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- CARD LAYOUT (Mobile) --}}
            <div class="md:hidden space-y-3 p-3">
                @forelse($riwayat as $index => $log)
                    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                        {{-- Header --}}
                        <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] px-4 py-2.5 flex justify-between items-center">
                            <div>
                                <p class="text-xs text-blue-200 font-semibold uppercase">{{ $log->nopol ?? 'N/A' }}</p>
                                <p class="text-white font-bold text-base">{{ $log->pemilik ?? 'N/A' }}</p>
                            </div>
                            <span class="text-xs font-bold px-2.5 py-1 rounded-full 
                                {{ $log->tipe == 'Roda 4' ? 'bg-blue-500 text-white' : 'bg-green-500 text-white' }}">
                                {{ $log->tipe ?? '-' }}
                            </span>
                        </div>

                        {{-- Body --}}
                        <div class="p-4 space-y-3">
                            {{-- Waktu Masuk & Keluar --}}
                            <div class="grid grid-cols-2 gap-3 pb-2 border-b border-gray-100">
                                <div>
                                    <p class="text-[10px] text-gray-500 font-semibold uppercase">Masuk</p>
                                    <p class="text-gray-800 font-bold text-sm">
                                        @if($log->waktu_masuk && $log->waktu_masuk->format('Y-m-d') == $tanggalTerpilih)
                                            {{ $log->waktu_masuk->format('H:i:s') }}
                                        @else <span class="text-gray-400">-</span> @endif
                                    </p>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-500 font-semibold uppercase">Keluar</p>
                                    <p class="text-gray-800 font-bold text-sm">
                                        @if($log->waktu_keluar && $log->waktu_keluar->format('Y-m-d') == $tanggalTerpilih)
                                            {{ $log->waktu_keluar->format('H:i:s') }}
                                        @else <span class="text-gray-400">-</span> @endif
                                    </p>
                                </div>
                            </div>

                            {{-- Keterangan --}}
                            <div>
                                <p class="text-[10px] text-gray-500 font-semibold uppercase mb-1">Keterangan</p>
                                @if(Auth::user()->peran == 'komandan')
                                    <form action="{{ route('komandan.kendaraan.log.updateKeterangan', $log->id_log) }}" method="POST">
                                        @csrf @method('PUT')
                                        <select name="keterangan" onchange="this.form.submit()" class="w-full border-gray-300 rounded-lg shadow-sm text-sm py-2 focus:border-blue-500 focus:ring-blue-500">
                                            <option value="tidak menginap" {{ $log->keterangan == 'tidak menginap' ? 'selected' : '' }}>Tidak Menginap</option>
                                            <option value="menginap" {{ $log->keterangan == 'Menginap' ? 'selected' : '' }}>Menginap</option>
                                        </select>
                                    </form>
                                @else
                                    <p class="text-gray-800 font-semibold">{{ $log->keterangan }}</p>
                                @endif
                            </div>

                            {{-- Tombol Aksi (Jika Komandan) --}}
                            @if(Auth::user()->peran == 'komandan')
                                <div class="pt-2">
                                    @if($log->kendaraan)
                                        <div class="flex items-center justify-center gap-2 bg-green-50 text-green-700 font-bold py-2 rounded-lg border border-green-300">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                            <span class="text-xs">Sudah Terdaftar</span>
                                        </div>
                                    @else
                                        <button @click.prevent="showPromoteModal = true; promoteAction = '{{ route('komandan.kendaraan.log.promote', $log->id_log) }}'" 
                                                class="w-full bg-blue-500 text-white font-bold py-2 rounded-lg hover:bg-blue-600 transition flex items-center justify-center gap-1">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"></path></svg>
                                            <span class="text-xs">Daftarkan</span>
                                        </button>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-xl shadow-md p-8 text-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-500 font-semibold">Data tidak ditemukan.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- TABEL 2: KENDARAAN YANG TERDAFTAR --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="bg-gray-100 p-3 border-b border-gray-200">
            <h3 class="font-bold text-gray-800">KENDARAAN YANG TERDAFTAR</h3>
        </div>
        
        {{-- TABEL (Desktop) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full min-w-max table-fixed">
                <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500">
                    <tr>
                        <th class="py-3 px-4 text-center w-[8%]">No</th>
                        <th class="py-3 px-4 text-center w-[25%]">Nopol</th>
                        <th class="py-3 px-4 text-center w-[37%]">Pemilik</th>
                        <th class="py-3 px-4 text-center w-[15%]">Tipe</th>
                        @if(Auth::user()->peran == 'komandan')
                            <th class="py-3 px-4 text-center w-[15%]">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse($kendaraanMaster as $index => $kendaraan)
                    <tr>
                        <td class="py-2 px-4">{{ $index + 1 }}.</td>
                        <td class="py-2 px-4 font-medium">{{ $kendaraan->nomor_plat }}</td>
                        <td class="py-2 px-4">{{ $kendaraan->pemilik }}</td>
                        <td class="py-2 px-4">{{ $kendaraan->tipe }}</td>
                        @if(Auth::user()->peran == 'komandan')
                            <td class="py-2 px-4 text-center">
                                <div class="flex justify-center space-x-3">
                                    <button @click="showEditModal = true; editAction = '{{ route('komandan.kendaraan.master.update', $kendaraan->id_kendaraan) }}'; editPlat = '{{ $kendaraan->nomor_plat }}'; editPemilik = '{{ $kendaraan->pemilik }}'; editTipe = '{{ $kendaraan->tipe }}';" class="text-blue-500 hover:text-blue-700">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828zM5 12V7a2 2 0 012-2h2.586l-4 4H5zM3 15a2 2 0 00-2 2v2h16v-2a2 2 0 00-2-2H3z"></path></svg>
                                    </button>
                                    <button @click.prevent="showDeleteModal = true; deleteAction = '{{ route('komandan.kendaraan.master.destroy', $kendaraan->id_kendaraan) }}'" class="text-red-500 hover:text-red-700">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                    </button>
                                </div>
                            </td>
                        @endif
                    </tr>
                    @empty
                    <tr><td colspan="5" class="py-4 px-4 text-center text-gray-500">Tidak ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- CARD LAYOUT (Mobile) --}}
        <div class="md:hidden space-y-3 p-3">
            @forelse($kendaraanMaster as $index => $kendaraan)
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                    {{-- Header --}}
                    <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] px-4 py-2.5 flex justify-between items-center">
                        <div>
                            <p class="text-xs text-blue-200 font-semibold uppercase">{{ $kendaraan->nomor_plat }}</p>
                            <p class="text-white font-bold text-base">{{ $kendaraan->pemilik }}</p>
                        </div>
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full 
                            {{ $kendaraan->tipe == 'Roda 4' ? 'bg-blue-500 text-white' : 'bg-green-500 text-white' }}">
                            {{ $kendaraan->tipe }}
                        </span>
                    </div>

                    {{-- Body --}}
                    @if(Auth::user()->peran == 'komandan')
                        <div class="p-4">
                            <div class="flex gap-2">
                                <button @click="showEditModal = true; editAction = '{{ route('komandan.kendaraan.master.update', $kendaraan->id_kendaraan) }}'; editPlat = '{{ $kendaraan->nomor_plat }}'; editPemilik = '{{ $kendaraan->pemilik }}'; editTipe = '{{ $kendaraan->tipe }}';" 
                                        class="flex-1 bg-blue-500 text-white font-bold py-2 rounded-lg hover:bg-blue-600 transition flex items-center justify-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828zM5 12V7a2 2 0 012-2h2.586l-4 4H5zM3 15a2 2 0 00-2 2v2h16v-2a2 2 0 00-2-2H3z"></path></svg>
                                    <span class="text-xs">Edit</span>
                                </button>
                                <button @click.prevent="showDeleteModal = true; deleteAction = '{{ route('komandan.kendaraan.master.destroy', $kendaraan->id_kendaraan) }}'" 
                                        class="flex-1 bg-red-500 text-white font-bold py-2 rounded-lg hover:bg-red-600 transition flex items-center justify-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                    <span class="text-xs">Hapus</span>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-white rounded-xl shadow-md p-8 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-500 font-semibold">Tidak ada data.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Modal Edit Kendaraan --}}
    <div x-show="showEditModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
        @click.away="showEditModal = false"
        style="display: none;">
        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full relative overflow-hidden" @click.stop>
            {{-- Header Biru --}}
            <div class="bg-[#1e3a5f] py-4 px-6 border-b border-[#1e3a5f] flex justify-between items-center">
                <h3 class="text-lg font-bold text-white flex items-center tracking-wide">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                    EDIT DATA KENDARAAN
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
                        
                        {{-- GROUP: Informasi Kendaraan --}}
                        <div class="p-4 bg-blue-50/50 rounded-xl border border-blue-100">
                            
                            <div class="space-y-4">
                                {{-- Nomor Plat --}}
                                <div>
                                    <label for="edit_nomor_plat" class="block text-xs font-bold text-[#1e3a5f] uppercase tracking-wide mb-1">Nomor Plat <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-[#1e3a5f]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                        </div>
                                        <input type="text" id="edit_nomor_plat" name="nomor_plat" x-model="editPlat" required placeholder="Contoh: B 1234 XYZ"
                                               class="pl-10 w-full bg-white border border-gray-300 text-gray-800 text-sm font-medium rounded-lg shadow-sm focus:ring-[#1e3a5f] focus:border-[#1e3a5f] block p-2.5">
                                    </div>
                                </div>
                                
                                {{-- Pemilik --}}
                                <div>
                                    <label for="edit_pemilik" class="block text-xs font-bold text-[#1e3a5f] uppercase tracking-wide mb-1">Pemilik <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-[#1e3a5f]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        </div>
                                        <input type="text" id="edit_pemilik" name="pemilik" x-model="editPemilik" required placeholder="Nama pemilik kendaraan"
                                               class="pl-10 w-full bg-white border border-gray-300 text-gray-800 text-sm font-medium rounded-lg shadow-sm focus:ring-[#1e3a5f] focus:border-[#1e3a5f] block p-2.5">
                                    </div>
                                </div>

                                {{-- Tipe --}}
                                <div>
                                    <label for="edit_tipe" class="block text-xs font-bold text-[#1e3a5f] uppercase tracking-wide mb-1">Tipe <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-[#1e3a5f]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                        </div>
                                        <select id="edit_tipe" name="tipe" x-model="editTipe" required
                                                class="pl-10 w-full bg-white border border-gray-300 text-[#1e3a5f] text-sm font-bold rounded-lg shadow-sm focus:ring-[#1e3a5f] focus:border-[#1e3a5f] block p-2.5 cursor-pointer">
                                            <option value="Roda 2">Roda 2</option>
                                            <option value="Roda 4">Roda 4</option>
                                        </select>
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
    <div x-show="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4" @click.away="showDeleteModal = false" style="display: none;">
    <div class="bg-white rounded-lg shadow-xl max-w-sm w-full p-6 relative" @click.stop>
        <h3 class="text-lg font-bold text-gray-900 mb-4">Konfirmasi Hapus</h3>
        <p class="text-gray-600 mb-6">Apakah Anda yakin ingin menghapus data kendaraan ini? Tindakan ini tidak dapat dibatalkan.</p>
        <form :action="deleteAction" method="POST" class="flex justify-end space-x-4">
            @csrf
            @method('DELETE')
            <button type="button" @click="showDeleteModal = false" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300">Batal</button>
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">Ya, Hapus</button>
        </form>
    </div>
    </div>

    {{-- Modal Promote --}}
    <div x-show="showPromoteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4" @click.away="showPromoteModal = false" style="display: none;">
    <div class="bg-white rounded-lg shadow-xl max-w-sm w-full p-6 relative" @click.stop>
        <h3 class="text-lg font-bold text-gray-900 mb-4">Konfirmasi Pendaftaran</h3>
        <p class="text-gray-600 mb-6">Tambahkan kendaraan ini ke Daftar Master?</p>
        <form :action="promoteAction" method="POST" class="flex justify-end space-x-4">
            @csrf
            <button type="button" @click="showPromoteModal = false" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300">Batal</button>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">OK</button>
        </form>
    </div>
    </div>

</div>

{{-- SCRIPT AJAX LIVE SEARCH --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const riwayatContainer = document.getElementById('riwayat-container');
        const form = document.getElementById('filterForm');
        let timeout = null;

        if(searchInput && riwayatContainer && form) {
            searchInput.addEventListener('input', function() {
                clearTimeout(timeout);
                riwayatContainer.style.opacity = '0.5';

                timeout = setTimeout(() => {
                    fetchData();
                }, 500);
            });

            function fetchData() {
                const formData = new FormData(form);
                const searchParams = new URLSearchParams(formData);

                fetch(`${form.action}?${searchParams.toString()}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newContent = doc.getElementById('riwayat-container');

                    if (newContent) {
                        riwayatContainer.innerHTML = newContent.innerHTML;
                    }
                    
                    riwayatContainer.style.opacity = '1';
                    window.history.pushState(null, '', `?${searchParams.toString()}`);
                })
                .catch(error => {
                    console.error('Error:', error);
                    riwayatContainer.style.opacity = '1';
                });
            }
        }
    });
</script>
@endsection
