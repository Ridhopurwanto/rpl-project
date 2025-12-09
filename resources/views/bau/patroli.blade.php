@extends('layouts.app')

@section('header-left')
    <a class="flex items-center border-2 border-[#1a2847] text-[#1a2847] text-sm font-bold px-4 py-2 rounded-full">
        PATROLI
    </a>
@endsection

@section('content')
<div class="w-full mx-auto" 
     x-data="{ 
         showPhotoModal: false, 
         photoUrl: ''
     }">
    
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold text-slate-800">Laporan Patroli Anggota</h2>
    </div>

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

    {{-- Daftar Patroli --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="bg-gray-100 p-3 border-b border-gray-200">
            <h3 class="font-bold text-gray-800">DAFTAR PATROLI</h3>
        </div>
        
        {{-- Form Filter --}}
        <form action="{{ route('bau.patroli.index') }}" method="GET" x-data="{}">
            <div class="px-6 py-5 border-b border-gray-200">
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

                    {{-- Filter Jenis Patroli --}}
                    <div class="w-[calc(50%-0.5rem)] md:flex-1">
                        <label for="jenis_patroli" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                            Jenis Patroli
                        </label>
                        <select id="jenis_patroli" name="jenis_patroli" 
                                onchange="this.form.submit()"
                                class="block w-full h-[42px] px-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer appearance-none" style="background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27currentColor%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3e%3cpolyline points=%276 9 12 15 18 9%27%3e%3c/polyline%3e%3c/svg%3e'); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 1.25em 1.25em;">
                            @forelse($jenisPatroliOptions as $opsi)
                                <option value="{{ $opsi }}" {{ $jenisPatroliTerpilih == $opsi ? 'selected' : '' }}>
                                    {{ $opsi }}
                                </option>
                            @empty
                                <option value="" disabled selected>Tidak ada data jenis patroli</option>
                            @endforelse
                        </select>
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
                        <th class="py-3 px-4 text-center w-[15%]">Waktu</th>
                        <th class="py-3 px-4 text-center w-[15%]">Jenis</th>
                        <th class="py-3 px-4 text-center w-[30%]">Wilayah</th>
                        <th class="py-3 px-4 text-center w-[20%]">Nama</th>
                        <th class="py-3 px-4 text-center w-[12%]">Detail</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse($dataPatroli as $index => $item)
                    <tr>
                        <td class="py-2 px-4">{{ $index + 1 }}.</td>
                        <td class="py-2 px-4">{{ $item->waktu_exact->format('H:i:s') }}</td>
                        <td class="py-2 px-4 text-center">
                            <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                                {{ $item->jenis_patroli }}
                            </span>
                        </td>
                        <td class="py-2 px-4 font-medium">{{ $item->wilayah }}</td>
                        <td class="py-2 px-4">{{ $item->nama_lengkap }}</td>
                        <td class="py-2 px-4 text-center">
                            <button @click="showPhotoModal = true; photoUrl = '{{ asset('storage/' . $item->foto) }}'" class="text-blue-500 hover:underline">
                                Buka
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-4 px-4 text-center text-gray-500">
                            Tidak ada data patroli pada tanggal ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- CARD LAYOUT (Mobile) --}}
        <div class="md:hidden space-y-3 p-3">
            @forelse($dataPatroli as $index => $item)
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                    
                    {{-- Header: Jenis Patroli & Nama --}}
                    <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] px-4 py-2.5 flex justify-between items-center">
                        <div>
                            <p class="text-xs text-blue-200 font-semibold uppercase">{{ $item->jenis_patroli }}</p>
                            <p class="text-white font-bold text-base">{{ $item->nama_lengkap }}</p>
                        </div>
                        <span class="bg-blue-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg">
                            {{ $item->waktu_exact->format('H:i') }}
                        </span>
                    </div>

                    {{-- Body: Info Detail --}}
                    <div class="p-4 space-y-3">
                        
                        {{-- Wilayah --}}
                        <div class="flex items-center gap-2 pb-2 border-b border-gray-100">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <div class="flex-1">
                                <p class="text-[10px] text-gray-500 font-semibold uppercase">Wilayah</p>
                                <p class="text-gray-800 font-bold text-sm">{{ $item->wilayah }}</p>
                            </div>
                            
                            {{-- Tombol Foto --}}
                            <button @click="showPhotoModal = true; photoUrl = '{{ asset('storage/' . $item->foto) }}'" 
                                    class="text-blue-500 hover:text-blue-700 font-semibold text-sm underline">
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
                    <p class="text-gray-500 font-semibold">Tidak ada data patroli pada tanggal ini.</p>
                </div>
            @endforelse
        </div>
        
        {{-- Pagination --}}
        @if($dataPatroli->total() > 0)
            <div class="flex justify-between items-center px-6 py-4 border-t border-gray-200">
                <div class="text-sm text-gray-600">
                    Showing {{ $dataPatroli->firstItem() ?? 0 }} to {{ $dataPatroli->lastItem() ?? 0 }} of {{ $dataPatroli->total() }} entries
                </div>
                <div class="flex items-center gap-1">
                    @if ($dataPatroli->onFirstPage())
                        <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Previous</span>
                    @else
                        <a href="{{ $dataPatroli->appends(request()->query())->previousPageUrl() }}" class="px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Previous</a>
                    @endif
                    @foreach(range(1, $dataPatroli->lastPage()) as $page)
                        @if($page == $dataPatroli->currentPage())
                            <span class="px-3 py-1 text-white bg-[#1e3a5f] rounded font-bold">{{ $page }}</span>
                        @else
                            <a href="{{ $dataPatroli->appends(request()->query())->url($page) }}" class="px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">{{ $page }}</a>
                        @endif
                    @endforeach
                    @if ($dataPatroli->hasMorePages())
                        <a href="{{ $dataPatroli->appends(request()->query())->nextPageUrl() }}" class="px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Next</a>
                    @else
                        <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Next</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    {{-- Modal Foto --}}
    <div x-show="showPhotoModal" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
         @click.away="showPhotoModal = false"
         style="display: none;">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full p-4 relative" @click.stop>
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 class="text-xl font-bold text-gray-800">PHOTO</h3>
                <button @click="showPhotoModal = false" class="text-gray-500 hover:text-gray-800 text-3xl">&times;</button>
            </div>
            <div class="mt-4">
                <img :src="photoUrl" alt="Foto Patroli" class="w-full h-auto rounded">
            </div>
        </div>
    </div>

</div>
@endsection
