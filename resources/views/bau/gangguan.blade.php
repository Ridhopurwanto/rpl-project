@extends('layouts.app')

{{-- Terapkan layout full-width --}}
@section('mobile_width', 'max-w-full')
@section('desktop_width', 'lg:max-w-full')

@section('header-left')
    <a class="flex items-center border-2 border-[#1a2847] text-[#1a2847] text-sm font-bold px-4 py-2 rounded-full">
        GANGGUAN<br class="sm:hidden"> KAMTIBMAS
    </a>
@endsection

@section('content')
<div class="w-full mx-auto"
     x-data="{ 
        showPhotoModal: false, 
        photoUrl: ''
     }">
    
    <h2 class="text-2xl font-bold text-slate-800 mb-4">Laporan Gangguan Kamtibmas</h2>

    {{-- Tampilkan Notifikasi Sukses/Error --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span>{{ session('error') }}</span>
        </div>
    @endif
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Oops! Terjadi kesalahan:</strong>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Tabel Riwayat Gangguan --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="bg-gray-100 p-3 border-b border-gray-200">
            <h3 class="font-bold text-gray-800">RIWAYAT GANGGUAN</h3>
        </div>

        {{-- Form Filter --}}
        <form action="{{ route('bau.gangguan.index') }}" method="GET" class="p-4 border-b border-gray-200" x-data="{}">
            <div class="flex flex-wrap gap-4">
                {{-- Show Entries --}}
                <div class="w-[calc(50%-0.5rem)] md:w-auto">
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Show</label>
                    <div class="flex items-center gap-2">
                        <div class="relative">
                            <select name="per_page" onchange="this.form.submit()" class="block h-[42px] pl-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer appearance-none">
                                <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5</option>
                                <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
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
                
                {{-- Filter Bulan --}}
                <div class="w-[calc(50%-0.5rem)] md:flex-1">
                    <label for="bulan" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Bulan</label>
                    <div class="cursor-pointer" @click="$refs.monthInput.showPicker()">
                        <input type="month" id="bulan" name="bulan" x-ref="monthInput" onchange="this.form.submit()" class="block w-full h-[42px] px-4 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer" value="{{ $bulanTerpilih }}">
                    </div>
                </div>

                {{-- Filter Kategori --}}
                <div class="w-[calc(50%-0.5rem)] md:flex-1">
                    <label for="kategori" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Kategori</label>
                    <div class="relative">
                        <select id="kategori" name="kategori" onchange="this.form.submit()" class="block w-full h-[42px] px-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer appearance-none">
                            <option value="semua">Semua Kategori</option>
                            @foreach($kategoriOptions as $kategori)
                                <option value="{{ $kategori }}" {{ $kategoriTerpilih == $kategori ? 'selected' : '' }}>{{ $kategori }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        
        {{-- TABEL (Desktop) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full min-w-max table-fixed">
                <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500">
                    <tr>
                        <th class="py-3 px-4 text-center w-[6%]">No</th>
                        <th class="py-3 px-4 text-center w-[10%]">Foto</th>
                        <th class="py-3 px-4 text-center w-[14%]">Tanggal</th>
                        <th class="py-3 px-4 text-center w-[18%]">Lokasi</th>
                        <th class="py-3 px-4 text-center w-[15%]">Kategori</th>
                        <th class="py-3 px-4 text-center w-[37%]">Ket. (Deskripsi)</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse($riwayatGangguan as $index => $gangguan)
                    <tr>
                        <td class="py-2 px-4">{{ $index + 1 }}.</td>
                        <td class="py-2 px-4 text-center">
                            <button @click="showPhotoModal = true; photoUrl = '{{ asset('storage/' . $gangguan->foto) }}'" 
                                    class="text-blue-500 hover:underline">
                                Buka
                            </button>
                        </td>
                        <td class="py-2 px-4">{{ $gangguan->waktu_lapor->format('d/m/Y H:i') }}</td>
                        <td class="py-2 px-4">{{ $gangguan->lokasi }}</td>
                        <td class="py-2 px-4">{{ $gangguan->kategori }}</td>
                        <td class="py-2 px-4">{{ $gangguan->deskripsi }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-4 px-4 text-center text-gray-500">
                            Tidak ada data gangguan kamtibmas pada bulan ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- CARD LAYOUT (Mobile) - HEADER SAMA PERSIS DENGAN PRESENSI --}}
        <div class="md:hidden space-y-3 p-3">
            @forelse($riwayatGangguan as $index => $gangguan)
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                    
                    {{-- Header: SAMA PERSIS DENGAN PRESENSI --}}
                    <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] px-4 py-2.5 flex justify-between items-center">
                        <div>
                            <p class="text-xs text-blue-200 font-semibold uppercase">Lokasi</p>
                            <p class="text-white font-bold text-base">{{ $gangguan->lokasi }}</p>
                        </div>

                        {{-- Badge Kategori --}}
                        <span class="bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg uppercase">
                            {{ $gangguan->kategori }}
                        </span>
                    </div>

                    {{-- Body: Foto Thumbnail (Kiri) + Info Detail (Kanan) --}}
                    <div class="p-4 flex gap-4">

                        {{-- Foto Thumbnail (Kiri) --}}
                        <div class="flex-shrink-0">
                            <img src="{{ asset('storage/' . $gangguan->foto) }}" 
                                 alt="Foto Gangguan"
                                 class="w-20 h-20 object-cover rounded-lg shadow-md cursor-pointer hover:opacity-80 transition-opacity"
                                 @click="showPhotoModal = true; photoUrl = '{{ asset('storage/' . $gangguan->foto) }}'">
                        </div>

                        {{-- Info Detail (Kanan) --}}
                        <div class="flex-1 space-y-3">
                            
                            {{-- Tanggal & Waktu --}}
                            <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <div>
                                        <p class="text-[10px] text-gray-500 font-semibold uppercase">Waktu Lapor</p>
                                        <p class="text-gray-800 font-bold text-base">{{ $gangguan->waktu_lapor->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Deskripsi --}}
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <div>
                                        <p class="text-[10px] text-gray-500 font-semibold uppercase">Deskripsi</p>
                                        <p class="text-gray-800 font-bold text-base">{{ Str::limit($gangguan->deskripsi, 30) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            @empty
                <div class="bg-white rounded-xl shadow-md p-8 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-500 font-semibold">Tidak ada data gangguan kamtibmas pada bulan ini.</p>
                </div>
            @endforelse
        </div>
        
        {{-- Pagination --}}
        @if($riwayatGangguan->total() > 0)
            <div class="flex justify-between items-center px-6 py-4 border-t border-gray-200">
                <div class="text-sm text-gray-600">
                    Showing {{ $riwayatGangguan->firstItem() ?? 0 }} to {{ $riwayatGangguan->lastItem() ?? 0 }} of {{ $riwayatGangguan->total() }} entries
                </div>
                <div class="flex items-center gap-1">
                    @if ($riwayatGangguan->onFirstPage())
                        <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Previous</span>
                    @else
                        <a href="{{ $riwayatGangguan->appends(request()->query())->previousPageUrl() }}" class="px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Previous</a>
                    @endif
                    @foreach(range(1, $riwayatGangguan->lastPage()) as $page)
                        @if($page == $riwayatGangguan->currentPage())
                            <span class="px-3 py-1 text-white bg-[#1e3a5f] rounded font-bold">{{ $page }}</span>
                        @else
                            <a href="{{ $riwayatGangguan->appends(request()->query())->url($page) }}" class="px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">{{ $page }}</a>
                        @endif
                    @endforeach
                    @if ($riwayatGangguan->hasMorePages())
                        <a href="{{ $riwayatGangguan->appends(request()->query())->nextPageUrl() }}" class="px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Next</a>
                    @else
                        <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Next</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    {{-- Modal Tampil Foto (Zoom) --}}
    <div x-show="showPhotoModal" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
         @click.away="showPhotoModal = false"
         style="display: none;">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full p-4 relative" @click.stop>
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 class="text-xl font-bold text-gray-800">FOTO GANGGUAN</h3>
                <button @click="showPhotoModal = false" class="text-gray-500 hover:text-gray-800 text-3xl">&times;</button>
            </div>
            <div class="mt-4 flex justify-center bg-gray-100 rounded p-2">
                <img :src="photoUrl" alt="Foto Gangguan" class="max-w-full max-h-[70vh] rounded object-contain">
            </div>
        </div>
    </div>



</div>
@endsection
