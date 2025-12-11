@extends('layouts.app')

{{-- Tombol KEMBALI --}}
@section('header-left')
    <a class="flex items-center border-2 border-[#1a2847] text-[#1a2847] text-sm font-bold px-4 py-2 rounded-full">
        PRESENSI
    </a>
@endsection

@section('content')
<div class="w-full mx-auto"
     x-data="{ 
         showPhotoModal: false, 
         photoUrl: ''
     }">
    
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold text-slate-800">Laporan Presensi Anggota</h2>
    </div>

    {{-- Tampilkan Notifikasi --}}
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

    {{-- Form Filter --}}
    <form action="{{ route('bau.presensi.index') }}" method="GET" x-data="{}">
        <div class="bg-white px-6 py-5 rounded-xl shadow-sm mb-6 border border-gray-200">
            
            <div class="flex flex-wrap gap-4">
                
                {{-- Show Entries --}}
                <div class="w-[calc(50%-0.5rem)] md:w-auto">
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Show</label>
                    <div class="flex items-center gap-2">
                        <select name="per_page" onchange="this.form.submit()" class="block h-[42px] pl-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer appearance-none" style="background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27currentColor%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3e%3cpolyline points=%276 9 12 15 18 9%27%3e%3c/polyline%3e%3c/svg%3e'); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 1.25em 1.25em;">
                            <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5</option>
                            <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
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
                           onchange="this.form.submit()"
                           class="block w-full h-[42px] px-4 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer"
                           value="{{ $tanggalTerpilih }}">
                </div>

                {{-- Filter Jenis Shift --}}
                <div class="w-[calc(50%-0.5rem)] md:flex-1">
                    <label for="shift" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                        Jenis Shift
                    </label>
                    <select id="shift" name="shift" onchange="this.form.submit()" class="block w-full h-[42px] px-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer appearance-none" style="background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27currentColor%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3e%3cpolyline points=%276 9 12 15 18 9%27%3e%3c/polyline%3e%3c/svg%3e'); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 1.25em 1.25em;">
                        <option value="semua" @if($shiftTerpilih == 'semua') selected @endif>Semua Shift</option>
                        <option value="Pagi" @if($shiftTerpilih == 'Pagi') selected @endif>Shift Pagi</option>
                        <option value="Malam" @if($shiftTerpilih == 'Malam') selected @endif>Shift Malam</option>
                        <option value="Non Shift" @if($shiftTerpilih == 'Non Shift') selected @endif>Non Shift</option>
                        <option value="Off" @if($shiftTerpilih == 'Off') selected @endif>Off</option>
                    </select>
                </div>
            </div>
        </div>
    </form>

    {{-- DAFTAR PRESENSI MASUK --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6" x-data="{ showMasuk: true }">
        <div class="bg-gray-100 p-3 border-b border-gray-200 cursor-pointer hover:bg-gray-200 transition" @click="showMasuk = !showMasuk">
            <div class="flex justify-between items-center">
                <h3 class="font-bold text-gray-800">DAFTAR PRESENSI MASUK</h3>
                <svg class="w-5 h-5 text-gray-600 transition-transform" :class="{ 'rotate-180': !showMasuk }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
        </div>

        <div x-show="showMasuk" x-collapse>
        
        {{-- TABEL (Desktop) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full min-w-max table-fixed">
                <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500">
                    <tr>
                        <th class="py-3 px-4 text-center w-[10%]">No</th>
                        <th class="py-3 px-4 text-center w-[35%]">Nama</th>
                        <th class="py-3 px-4 text-center w-[20%]">Waktu</th>
                        <th class="py-3 px-4 text-center w-[15%]">Foto</th>
                        <th class="py-3 px-4 text-center w-[20%]">Status</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse($dataMasuk as $index => $presensi)
                    <tr>
                        <td class="py-2 px-4">{{ $index + 1 }}.</td>
                        <td class="py-2 px-4 font-medium">{{ $presensi->nama_lengkap }}</td>
                        <td class="py-2 px-4">{{ $presensi->waktu->format('H:i:s') }}</td>
                        <td class="py-2 px-4 text-center">
                            <button @click="showPhotoModal = true; photoUrl = '{{ asset('storage/' . $presensi->foto) }}'" class="text-blue-500 hover:underline">
                                Buka
                            </button>
                        </td>
                        <td class="py-2 px-4 text-center">
                            @if($presensi->status == 'tepat waktu')
                                <span class="text-green-600 font-semibold">Tepat Waktu</span>
                            @elseif($presensi->status == 'terlambat')
                                <span class="text-red-500 font-semibold">Terlambat</span>
                            @else
                                <span class="text-yellow-500 font-semibold">{{ ucfirst($presensi->status) }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-4 px-4 text-center text-gray-500">
                            Tidak ada data presensi masuk pada tanggal ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- CARD LAYOUT (Mobile) --}}
        <div class="md:hidden space-y-3 p-3">
            @forelse($dataMasuk as $index => $presensi)
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                    {{-- Header --}}
                    <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] px-4 py-2.5 flex justify-between items-center">
                        <div>
                            <p class="text-xs text-blue-200 font-semibold uppercase">Nama Anggota</p>
                            <p class="text-white font-bold text-base">{{ $presensi->nama_lengkap }}</p>
                        </div>
                        @if($presensi->status == 'tepat waktu')
                            <span class="bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg uppercase">Tepat Waktu</span>
                        @elseif($presensi->status == 'terlambat')
                            <span class="bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg uppercase">Telat</span>
                        @else
                            <span class="bg-yellow-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg uppercase">{{ ucfirst($presensi->status) }}</span>
                        @endif
                    </div>

                    {{-- Body --}}
                    <div class="p-4 space-y-3">
                        <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <p class="text-[10px] text-gray-500 font-semibold uppercase">Waktu</p>
                                    <p class="text-gray-800 font-bold text-base">{{ $presensi->waktu->format('H:i:s') }}</p>
                                </div>
                            </div>
                            <button @click="showPhotoModal = true; photoUrl = '{{ asset('storage/' . $presensi->foto) }}'" class="text-blue-500 hover:text-blue-700 font-semibold text-sm underline">
                                Lihat Foto
                            </button>
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
                    <p class="text-gray-500 font-semibold">Tidak ada data presensi masuk pada tanggal ini.</p>
                </div>
            @endforelse
        </div>
        
        {{-- Pagination Masuk --}}
        @if($dataMasuk->total() > 0)
            <div class="flex justify-between items-center px-6 py-4 border-t border-gray-200">
                <div class="text-sm text-gray-600">
                    Showing {{ $dataMasuk->firstItem() ?? 0 }} to {{ $dataMasuk->lastItem() ?? 0 }} of {{ $dataMasuk->total() }} entries
                </div>
                <div class="flex items-center gap-1">
                    @if ($dataMasuk->onFirstPage())
                        <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Previous</span>
                    @else
                        <a href="{{ $dataMasuk->appends(request()->query())->previousPageUrl() }}" class="px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Previous</a>
                    @endif
                    @foreach(range(1, $dataMasuk->lastPage()) as $page)
                        @if($page == $dataMasuk->currentPage())
                            <span class="px-3 py-1 text-white bg-[#1e3a5f] rounded font-bold">{{ $page }}</span>
                        @else
                            <a href="{{ $dataMasuk->appends(request()->query())->url($page) }}" class="px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">{{ $page }}</a>
                        @endif
                    @endforeach
                    @if ($dataMasuk->hasMorePages())
                        <a href="{{ $dataMasuk->appends(request()->query())->nextPageUrl() }}" class="px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Next</a>
                    @else
                        <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Next</span>
                    @endif
                </div>
            </div>
        @endif
        </div>
    </div>
    
    {{-- DAFTAR PRESENSI PULANG --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6" x-data="{ showPulang: true }">
        <div class="bg-gray-100 p-3 border-b border-gray-200 cursor-pointer hover:bg-gray-200 transition" @click="showPulang = !showPulang">
            <div class="flex justify-between items-center">
                <h3 class="font-bold text-gray-800">DAFTAR PRESENSI PULANG</h3>
                <svg class="w-5 h-5 text-gray-600 transition-transform" :class="{ 'rotate-180': !showPulang }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
        </div>

        <div x-show="showPulang" x-collapse>
        
        {{-- TABEL (Desktop) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full min-w-max table-fixed">
                <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500">
                    <tr>
                        <th class="py-3 px-4 text-center w-[10%]">No</th>
                        <th class="py-3 px-4 text-center w-[35%]">Nama</th>
                        <th class="py-3 px-4 text-center w-[20%]">Waktu</th>
                        <th class="py-3 px-4 text-center w-[15%]">Foto</th>
                        <th class="py-3 px-4 text-center w-[20%]">Status</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse($dataPulang as $index => $presensi)
                    <tr>
                        <td class="py-2 px-4">{{ $index + 1 }}.</td>
                        <td class="py-2 px-4 font-medium">{{ $presensi->nama_lengkap }}</td>
                        <td class="py-2 px-4">{{ $presensi->waktu->format('H:i:s') }}</td>
                        <td class="py-2 px-4 text-center">
                            <button @click="showPhotoModal = true; photoUrl = '{{ asset('storage/' . $presensi->foto) }}'" class="text-blue-500 hover:underline">
                                Buka
                            </button>
                        </td>
                        <td class="py-2 px-4 text-center">
                            @if($presensi->status == 'tepat waktu')
                                <span class="text-green-600 font-semibold">Tepat Waktu</span>
                            @elseif($presensi->status == 'terlambat')
                                <span class="text-red-500 font-semibold">Terlambat</span>
                            @else
                                <span class="text-yellow-500 font-semibold">{{ ucfirst($presensi->status) }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-4 px-4 text-center text-gray-500">
                            Tidak ada data presensi pulang pada tanggal ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- CARD LAYOUT (Mobile) --}}
        <div class="md:hidden space-y-3 p-3">
            @forelse($dataPulang as $index => $presensi)
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                    <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] px-4 py-2.5 flex justify-between items-center">
                        <div>
                            <p class="text-xs text-blue-200 font-semibold uppercase">Nama Anggota</p>
                            <p class="text-white font-bold text-base">{{ $presensi->nama_lengkap }}</p>
                        </div>
                        @if($presensi->status == 'tepat waktu')
                            <span class="bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg uppercase">Tepat Waktu</span>
                        @elseif($presensi->status == 'terlambat')
                            <span class="bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg uppercase">Telat</span>
                        @else
                            <span class="bg-yellow-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg uppercase">{{ ucfirst($presensi->status) }}</span>
                        @endif
                    </div>

                    <div class="p-4 space-y-3">
                        <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <p class="text-[10px] text-gray-500 font-semibold uppercase">Waktu</p>
                                    <p class="text-gray-800 font-bold text-base">{{ $presensi->waktu->format('H:i:s') }}</p>
                                </div>
                            </div>
                            <button @click="showPhotoModal = true; photoUrl = '{{ asset('storage/' . $presensi->foto) }}'" class="text-blue-500 hover:text-blue-700 font-semibold text-sm underline">
                                Lihat Foto
                            </button>
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
                    <p class="text-gray-500 font-semibold">Tidak ada data presensi pulang pada tanggal ini.</p>
                </div>
            @endforelse
        </div>
        
        {{-- Pagination Pulang --}}
        @if($dataPulang->total() > 0)
            <div class="flex justify-between items-center px-6 py-4 border-t border-gray-200">
                <div class="text-sm text-gray-600">
                    Showing {{ $dataPulang->firstItem() ?? 0 }} to {{ $dataPulang->lastItem() ?? 0 }} of {{ $dataPulang->total() }} entries
                </div>
                <div class="flex items-center gap-1">
                    @if ($dataPulang->onFirstPage())
                        <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Previous</span>
                    @else
                        <a href="{{ $dataPulang->appends(request()->query())->previousPageUrl() }}" class="px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Previous</a>
                    @endif
                    @foreach(range(1, $dataPulang->lastPage()) as $page)
                        @if($page == $dataPulang->currentPage())
                            <span class="px-3 py-1 text-white bg-[#1e3a5f] rounded font-bold">{{ $page }}</span>
                        @else
                            <a href="{{ $dataPulang->appends(request()->query())->url($page) }}" class="px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">{{ $page }}</a>
                        @endif
                    @endforeach
                    @if ($dataPulang->hasMorePages())
                        <a href="{{ $dataPulang->appends(request()->query())->nextPageUrl() }}" class="px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Next</a>
                    @else
                        <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Next</span>
                    @endif
                </div>
            </div>
        @endif
        </div>
    </div>

    {{-- MODAL FOTO --}}
    <div x-show="showPhotoModal" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
         @click.away="showPhotoModal = false"
         style="display: none;">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full p-4 relative" @click.stop>
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 class="text-xl font-bold text-gray-800">FOTO PRESENSI</h3>
                <button @click="showPhotoModal = false" class="text-gray-500 hover:text-gray-800 text-3xl">&times;</button>
            </div>
            <div class="mt-4">
                <img :src="photoUrl" alt="Foto Presensi" class="w-full h-auto rounded">
            </div>
        </div>
    </div>
    
</div>
@endsection
