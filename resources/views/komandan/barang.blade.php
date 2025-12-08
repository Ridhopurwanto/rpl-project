@extends('layouts.app')

{{-- Terapkan layout full-width --}}
@section('mobile_width', 'max-w-full')
@section('desktop_width', 'lg:max-w-full')

@section('header-left')
    <a class="inline-block border-2 border-[#1a2847] text-[#1a2847] text-sm font-bold px-4 py-1 rounded-full mb-4">
        BARANG
    </a>
@endsection

@section('content')
{{-- Wrapper Alpine.js untuk Modal Foto --}}
<div class="w-full mx-auto"
     x-data="{ 
        showPhotoModal: false, 
        photoUrl: ''
     }">
    
    <h2 class="text-2xl font-bold text-slate-800 mb-4">Laporan Barang</h2>

    {{-- Form Filter --}}
    <form id="filterForm" action="{{ route('komandan.barang') }}" method="GET" x-data="{}">
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

                {{-- Filter Kategori --}}
                <div class="w-full">
                    <label for="kategori" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                        Kategori
                    </label>
                    <div class="relative">
                        <select id="kategori" name="kategori" 
                                onchange="document.getElementById('filterForm').submit()"
                                class="block w-full h-[42px] px-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer appearance-none">
                            <option value="temuan" {{ $kategoriTerpilih == 'temuan' ? 'selected' : '' }}>Barang Temuan</option>
                            <option value="titipan" {{ $kategoriTerpilih == 'titipan' ? 'selected' : '' }}>Barang Titipan</option>
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
                        Cari Barang
                    </label>
                    <input type="text" id="searchInput" name="search" 
                           class="block w-full h-[42px] px-4 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm placeholder-gray-400" 
                           value="{{ $jenisTerpilih }}" 
                           placeholder="Ketik untuk mencari...">
                </div>
            </div>
        </div>
    </form>

    {{-- Tabel Riwayat Barang --}}
    <div id="table-container" class="bg-white rounded-lg shadow-md overflow-hidden mb-6 transition-opacity duration-200">
        <div class="bg-gray-100 p-3 border-b border-gray-200">
            <h3 class="font-bold text-gray-800">RIWAYAT ({{ $kategoriTerpilih == 'temuan' ? 'Barang Temuan' : 'Barang Titipan' }})</h3>
        </div>
        
        {{-- TABEL (Desktop) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full min-w-max">
                
                {{-- HEADER TABLE --}}
                @if($kategoriTerpilih == 'temuan')
                    <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500">
                        <tr>
                            <th class="py-3 px-4 text-left">No</th>
                            <th class="py-3 px-4 text-center">Foto</th>
                            <th class="py-3 px-4 text-left">Nama Barang</th>
                            <th class="py-3 px-4 text-left">Pelapor</th>
                            <th class="py-3 px-4 text-left">Lokasi Temuan</th>
                            <th class="py-3 px-4 text-left">Catatan</th>
                            <th class="py-3 px-4 text-left">Status</th>
                        </tr>
                    </thead>
                @else
                    <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500">
                        <tr>
                            <th class="py-3 px-4 text-left">No</th>
                            <th class="py-3 px-4 text-center">Foto</th>
                            <th class="py-3 px-4 text-left">Nama Barang</th>
                            <th class="py-3 px-4 text-left">Penitip</th>
                            <th class="py-3 px-4 text-left">Penerima</th>
                            <th class="py-3 px-4 text-left">Catatan</th>
                            <th class="py-3 px-4 text-left">Status</th>
                        </tr>
                    </thead>
                @endif

                {{-- BODY TABLE --}}
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse($riwayatBarang as $index => $barang)
                        @if($kategoriTerpilih == 'temuan')
                            <tr>
                                <td class="py-2 px-4">{{ $index + 1 }}.</td>
                                <td class="py-2 px-4 text-center">
                                    @if($barang->foto)
                                        <button @click="showPhotoModal = true; photoUrl = '{{ asset('storage/' . $barang->foto) }}'" 
                                                class="text-blue-500 hover:underline">
                                            Buka
                                        </button>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="py-2 px-4 font-medium">{{ $barang->nama_barang }}</td>
                                <td class="py-2 px-4">{{ $barang->nama_pelapor }}</td>
                                <td class="py-2 px-4">{{ $barang->lokasi_penemuan }}</td>
                                <td class="py-2 px-4">{{ $barang->catatan }}</td>
                                <td class="py-2 px-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                                        {{ $barang->status == 'belum selesai' ? 'bg-red-200 text-yellow-800' : 'bg-green-200 text-green-800' }}">
                                        {{ $barang->status }}
                                    </span>
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td class="py-2 px-4">{{ $index + 1 }}.</td>
                                <td class="py-2 px-4 text-center">
                                    @if($barang->foto)
                                        <button @click="showPhotoModal = true; photoUrl = '{{ asset('storage/' . $barang->foto) }}'" 
                                                class="text-blue-500 hover:underline">
                                            Buka
                                        </button>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="py-2 px-4 font-medium">{{ $barang->nama_barang }}</td>
                                <td class="py-2 px-4">{{ $barang->nama_penitip }}</td>
                                <td class="py-2 px-4">{{ $barang->tujuan }}</td>
                                <td class="py-2 px-4">{{ $barang->catatan }}</td>
                                <td class="py-2 px-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                                        {{ $barang->status == 'belum selesai' ? 'bg-red-200 text-yellow-800' : 'bg-green-200 text-green-800' }}">
                                        {{ $barang->status }}
                                    </span>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="7" class="py-4 px-4 text-center text-gray-500">
                                Tidak ada data barang yang ditemukan sesuai filter/pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- CARD LAYOUT (Mobile) --}}
        <div class="md:hidden space-y-3 p-3">
            @forelse($riwayatBarang as $index => $barang)
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                    
                    {{-- Header: Nama Barang & Status --}}
                    <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] px-4 py-2.5 flex justify-between items-center">
                        <div class="flex-1">
                            <p class="text-xs text-blue-200 font-semibold uppercase">{{ $kategoriTerpilih == 'temuan' ? 'Barang Temuan' : 'Barang Titipan' }}</p>
                            <p class="text-white font-bold text-base">{{ $barang->nama_barang }}</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold shadow-lg
                            {{ $barang->status == 'belum selesai' ? 'bg-yellow-500 text-white' : 'bg-green-500 text-white' }}">
                            {{ $barang->status }}
                        </span>
                    </div>

                    {{-- Body: Info Detail --}}
                    <div class="p-4 space-y-3">
                        
                        @if($kategoriTerpilih == 'temuan')
                            {{-- Pelapor & Lokasi (Temuan) --}}
                            <div class="grid grid-cols-2 gap-3 pb-2 border-b border-gray-100">
                                <div>
                                    <p class="text-[10px] text-gray-500 font-semibold uppercase">Pelapor</p>
                                    <p class="text-gray-800 font-bold text-sm">{{ $barang->nama_pelapor }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-500 font-semibold uppercase">Lokasi Temuan</p>
                                    <p class="text-gray-800 font-bold text-sm">{{ $barang->lokasi_penemuan }}</p>
                                </div>
                            </div>
                        @else
                            {{-- Penitip & Penerima (Titipan) --}}
                            <div class="grid grid-cols-2 gap-3 pb-2 border-b border-gray-100">
                                <div>
                                    <p class="text-[10px] text-gray-500 font-semibold uppercase">Penitip</p>
                                    <p class="text-gray-800 font-bold text-sm">{{ $barang->nama_penitip }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-500 font-semibold uppercase">Penerima</p>
                                    <p class="text-gray-800 font-bold text-sm">{{ $barang->tujuan }}</p>
                                </div>
                            </div>
                        @endif

                        {{-- Catatan --}}
                        <div class="pt-2">
                            <p class="text-[10px] text-gray-500 font-semibold uppercase mb-1">Catatan</p>
                            <p class="text-gray-800 text-sm leading-relaxed">{{ $barang->catatan }}</p>
                        </div>

                        {{-- Tombol Lihat Foto --}}
                        @if($barang->foto)
                            <div class="pt-2">
                                <button @click="showPhotoModal = true; photoUrl = '{{ asset('storage/' . $barang->foto) }}'" 
                                        class="w-full bg-blue-500 text-white font-bold py-2 rounded-lg hover:bg-blue-600 transition flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="text-xs">Lihat Foto</span>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl shadow-md p-8 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <p class="text-gray-500 font-semibold">Tidak ada data barang yang ditemukan sesuai filter/pencarian.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Modal Tampil Foto --}}
    <div x-show="showPhotoModal" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
         @click.away="showPhotoModal = false"
         style="display: none;"
         x-transition.opacity>
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full p-4 relative" @click.stop>
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 class="text-xl font-bold text-gray-800">FOTO BARANG</h3>
                <button @click="showPhotoModal = false" class="text-gray-500 hover:text-gray-800 text-3xl">&times;</button>
            </div>
            <div class="mt-4 flex justify-center bg-gray-100 rounded p-2">
                <img :src="photoUrl" alt="Foto Barang" class="max-w-full max-h-[70vh] rounded object-contain">
            </div>
        </div>
    </div>

</div>

{{-- SCRIPT LIVE SEARCH --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const tableContainer = document.getElementById('table-container');
        const form = document.getElementById('filterForm');
        let timeout = null;

        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);
            tableContainer.style.opacity = '0.5';

            // Debounce 500ms
            timeout = setTimeout(() => {
                fetchData();
            }, 500);
        });

        function fetchData() {
            const formData = new FormData(form);
            const searchParams = new URLSearchParams(formData);

            fetch(`${form.action}?${searchParams.toString()}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTable = doc.getElementById('table-container');

                if (newTable) {
                    tableContainer.innerHTML = newTable.innerHTML;
                }
                
                tableContainer.style.opacity = '1';
                
                // Update URL tanpa reload
                window.history.pushState(null, '', `?${searchParams.toString()}`);
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                tableContainer.style.opacity = '1';
            });
        }
    });
</script>
@endsection
