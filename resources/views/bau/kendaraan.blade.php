@extends('layouts.app')

@section('header-left')
    <a class="flex items-center border-2 border-[#1a2847] text-[#1a2847] text-sm font-bold px-4 py-2 rounded-full">
        KENDARAAN
    </a>
@endsection

@section('content')
<div class="w-full mx-auto">
    
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

    {{-- TABEL 1: RIWAYAT KELUAR/MASUK --}}
    <div id="riwayat-container" class="transition-opacity duration-200" x-data="{ showRiwayat: true }">
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="bg-gray-100 p-3 border-b border-gray-200 cursor-pointer hover:bg-gray-200 transition" @click="showRiwayat = !showRiwayat">
                <div class="flex justify-between items-center">
                    <h3 class="font-bold text-gray-800">RIWAYAT KELUAR/MASUK</h3>
                    <svg class="w-5 h-5 text-gray-600 transition-transform" :class="{ 'rotate-180': !showRiwayat }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>

            <div x-show="showRiwayat" x-collapse>

            {{-- Form Filter & Search --}}
            <form id="filterForm" action="{{ route('bau.kendaraan.index') }}" method="GET" x-data="{}">
                <div class="px-6 py-5 border-b border-gray-200">
                    <div class="flex flex-wrap gap-4">
                        
                        {{-- Show Entries --}}
                        <div class="w-[calc(50%-0.5rem)] md:w-auto">
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Show</label>
                            <div class="flex items-center gap-2">
                                <select name="per_page_riwayat" onchange="document.getElementById('filterForm').submit()" class="block h-[42px] pl-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer appearance-none" style="background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27currentColor%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3e%3cpolyline points=%276 9 12 15 18 9%27%3e%3c/polyline%3e%3c/svg%3e'); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 1.25em 1.25em;">
                                    <option value="5" {{ $perPageRiwayat == 5 ? 'selected' : '' }}>5</option>
                                    <option value="10" {{ $perPageRiwayat == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ $perPageRiwayat == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ $perPageRiwayat == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ $perPageRiwayat == 100 ? 'selected' : '' }}>100</option>
                                </select>
                                <span class="text-sm text-gray-600 whitespace-nowrap">rows</span>
                            </div>
                        </div>
                        
                        {{-- Filter Tanggal --}}
                        <div class="w-[calc(50%-0.5rem)] md:flex-1">
                            <label for="tanggal" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                                Tanggal
                            </label>
                            <input type="date" id="tanggal" name="tanggal"
                                   onchange="document.getElementById('filterForm').submit()"
                                   class="block w-full h-[42px] px-4 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer"
                                   value="{{ $tanggalTerpilih }}">
                        </div>

                        {{-- Filter Tipe --}}
                        <div class="w-[calc(50%-0.5rem)] md:flex-1">
                            <label for="tipe" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                                Tipe
                            </label>
                            <select id="tipe" name="tipe" 
                                    onchange="document.getElementById('filterForm').submit()"
                                    class="block w-full h-[42px] px-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer appearance-none" style="background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27currentColor%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3e%3cpolyline points=%276 9 12 15 18 9%27%3e%3c/polyline%3e%3c/svg%3e'); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 1.25em 1.25em;">
                                <option value="">Semua Tipe</option>
                                <option value="Roda 2" {{ $tipeTerpilih == 'Roda 2' ? 'selected' : '' }}>Roda 2</option>
                                <option value="Roda 4" {{ $tipeTerpilih == 'Roda 4' ? 'selected' : '' }}>Roda 4</option>
                            </select>
                        </div>

                        {{-- Live Search Input --}}
                        <div class="w-[calc(50%-0.5rem)] md:flex-1">
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
            
            {{-- TABEL (Desktop) --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full min-w-max table-fixed">
                    <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500">
                        <tr>
                            <th class="py-3 px-4 text-center w-[8%]">No</th>
                            <th class="py-3 px-4 text-center w-[18%]">Nopol</th>
                            <th class="py-3 px-4 text-center w-[24%]">Pemilik</th>
                            <th class="py-3 px-4 text-center w-[12%]">Tipe</th>
                            <th class="py-3 px-4 text-center w-[13%]">Masuk</th>
                            <th class="py-3 px-4 text-center w-[13%]">Keluar</th>
                            <th class="py-3 px-4 text-center w-[12%]">Ket.</th>
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
                            <td class="py-2 px-4">{{ $log->keterangan }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="py-4 px-4 text-center text-gray-500">Data tidak ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- CARD LAYOUT (Mobile) --}}
            <div class="md:hidden space-y-3 p-3">
                @forelse($riwayat as $index => $log)
                    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
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

                        <div class="p-4 space-y-3">
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

                            <div>
                                <p class="text-[10px] text-gray-500 font-semibold uppercase mb-1">Keterangan</p>
                                <p class="text-gray-800 font-semibold">{{ $log->keterangan }}</p>
                            </div>
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

            {{-- Pagination --}}
            @if($riwayat->total() > 0)
            <div class="flex justify-between items-center px-6 py-4 border-t border-gray-200">
                <div class="text-sm text-gray-600">
                    Showing {{ $riwayat->firstItem() ?? 0 }} to {{ $riwayat->lastItem() ?? 0 }} of {{ $riwayat->total() }} entries
                </div>
                <div class="flex items-center gap-1">
                    @if ($riwayat->onFirstPage())
                        <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Previous</span>
                    @else
                        <a href="{{ $riwayat->appends(request()->query())->previousPageUrl() }}" class="px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Previous</a>
                    @endif
                    @foreach(range(1, $riwayat->lastPage()) as $page)
                        @if($page == $riwayat->currentPage())
                            <span class="px-3 py-1 text-white bg-[#1e3a5f] rounded font-bold">{{ $page }}</span>
                        @else
                            <a href="{{ $riwayat->appends(request()->query())->url($page) }}" class="px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">{{ $page }}</a>
                        @endif
                    @endforeach
                    @if ($riwayat->hasMorePages())
                        <a href="{{ $riwayat->appends(request()->query())->nextPageUrl() }}" class="px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Next</a>
                    @else
                        <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Next</span>
                    @endif
                </div>
            </div>
            @endif
            </div>
        </div>
    </div>

    {{-- TABEL 2: KENDARAAN YANG TERDAFTAR --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6" x-data="{ showMaster: true }">
        <div class="bg-gray-100 p-3 border-b border-gray-200 cursor-pointer hover:bg-gray-200 transition" @click="showMaster = !showMaster">
            <div class="flex justify-between items-center">
                <h3 class="font-bold text-gray-800">KENDARAAN YANG TERDAFTAR</h3>
                <svg class="w-5 h-5 text-gray-600 transition-transform" :class="{ 'rotate-180': !showMaster }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
        </div>

        <div x-show="showMaster" x-collapse>

        <form id="filterFormMaster" action="{{ route('bau.kendaraan.index') }}" method="GET">
            @foreach(request()->except(['per_page_master', 'page_master', 'tipe_master', 'search_master']) as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach
            <div class="px-6 py-5 border-b border-gray-200">
                <div class="flex flex-wrap gap-4">
                    
                    <div class="w-[calc(50%-0.5rem)] md:w-auto">
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Show</label>
                        <div class="flex items-center gap-2">
                            <select name="per_page_master" onchange="this.form.submit()" class="block h-[42px] pl-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer appearance-none" style="background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27currentColor%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3e%3cpolyline points=%276 9 12 15 18 9%27%3e%3c/polyline%3e%3c/svg%3e'); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 1.25em 1.25em;">
                                <option value="5" {{ $perPageMaster == 5 ? 'selected' : '' }}>5</option>
                                <option value="10" {{ $perPageMaster == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ $perPageMaster == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ $perPageMaster == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ $perPageMaster == 100 ? 'selected' : '' }}>100</option>
                            </select>
                            <span class="text-sm text-gray-600 whitespace-nowrap">rows</span>
                        </div>
                    </div>

                    {{-- Filter Tipe --}}
                    <div class="w-[calc(50%-0.5rem)] md:flex-1">
                        <label for="tipe_master" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                            Tipe
                        </label>
                        <select id="tipe_master" name="tipe_master" 
                                onchange="document.getElementById('filterFormMaster').submit()"
                                class="block w-full h-[42px] px-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer appearance-none" style="background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27currentColor%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3e%3cpolyline points=%276 9 12 15 18 9%27%3e%3c/polyline%3e%3c/svg%3e'); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 1.25em 1.25em;">
                            <option value="">Semua Tipe</option>
                            <option value="Roda 2" {{ $tipeMaster == 'Roda 2' ? 'selected' : '' }}>Roda 2</option>
                            <option value="Roda 4" {{ $tipeMaster == 'Roda 4' ? 'selected' : '' }}>Roda 4</option>
                        </select>
                    </div>

                    {{-- Live Search Input --}}
                    <div class="w-[calc(50%-0.5rem)] md:flex-1">
                        <label for="searchMasterInput" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                            Cari Kendaraan
                        </label>
                        <input type="text" id="searchMasterInput" name="search_master" 
                               class="block w-full h-[42px] px-4 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm placeholder-gray-400" 
                               value="{{ $searchMaster ?? '' }}" 
                               placeholder="Ketik untuk mencari...">
                    </div>
                </div>
            </div>
        </form>
        
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full min-w-max table-fixed">
                <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500">
                    <tr>
                        <th class="py-3 px-4 text-center w-[10%]">No</th>
                        <th class="py-3 px-4 text-center w-[30%]">Nopol</th>
                        <th class="py-3 px-4 text-center w-[40%]">Pemilik</th>
                        <th class="py-3 px-4 text-center w-[20%]">Tipe</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse($kendaraanMaster as $index => $kendaraan)
                    <tr>
                        <td class="py-2 px-4">{{ $index + 1 }}.</td>
                        <td class="py-2 px-4 font-medium">{{ $kendaraan->nomor_plat }}</td>
                        <td class="py-2 px-4">{{ $kendaraan->pemilik }}</td>
                        <td class="py-2 px-4 text-center">
                            <span class="text-xs font-semibold px-2 py-1 rounded-full 
                                {{ $kendaraan->tipe == 'Roda 4' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                {{ $kendaraan->tipe }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="py-4 px-4 text-center text-gray-500">Tidak ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="md:hidden space-y-3 p-3">
            @forelse($kendaraanMaster as $index => $kendaraan)
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
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

        @if($kendaraanMaster->total() > 0)
        <div class="flex justify-between items-center px-6 py-4 border-t border-gray-200">
            <div class="text-sm text-gray-600">
                Showing {{ $kendaraanMaster->firstItem() ?? 0 }} to {{ $kendaraanMaster->lastItem() ?? 0 }} of {{ $kendaraanMaster->total() }} entries
            </div>
            <div class="flex items-center gap-1">
                @if ($kendaraanMaster->onFirstPage())
                    <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Previous</span>
                @else
                    <a href="{{ $kendaraanMaster->appends(request()->query())->previousPageUrl() }}" class="px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Previous</a>
                @endif
                @foreach(range(1, $kendaraanMaster->lastPage()) as $page)
                    @if($page == $kendaraanMaster->currentPage())
                        <span class="px-3 py-1 text-white bg-[#1e3a5f] rounded font-bold">{{ $page }}</span>
                    @else
                        <a href="{{ $kendaraanMaster->appends(request()->query())->url($page) }}" class="px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">{{ $page }}</a>
                    @endif
                @endforeach
                @if ($kendaraanMaster->hasMorePages())
                    <a href="{{ $kendaraanMaster->appends(request()->query())->nextPageUrl() }}" class="px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Next</a>
                @else
                    <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Next</span>
                @endif
            </div>
        </div>
        @endif
        </div>
    </div>

</div>

<script>
// Live search untuk Riwayat
let searchTimeout;
const searchInput = document.getElementById('searchInput');
if (searchInput) {
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            document.getElementById('filterForm').submit();
        }, 500);
    });
}

// Live search untuk Master
let searchMasterTimeout;
const searchMasterInput = document.getElementById('searchMasterInput');
if (searchMasterInput) {
    searchMasterInput.addEventListener('input', function() {
        clearTimeout(searchMasterTimeout);
        searchMasterTimeout = setTimeout(() => {
            document.getElementById('filterFormMaster').submit();
        }, 500);
    });
}
</script>
@endsection
