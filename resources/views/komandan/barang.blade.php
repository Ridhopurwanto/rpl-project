@extends('layouts.app')

{{-- Terapkan layout full-width --}}
@section('mobile_width', 'max-w-full')
@section('desktop_width', 'lg:max-w-full')

@section('header-left')
    <a class="bg-slate-800 text-white text-sm font-semibold px-6 py-2 rounded-full shadow-md hover:bg-slate-700 transition">
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
    {{-- ID filterForm digunakan oleh Javascript untuk mengambil data saat Live Search --}}
    <form id="filterForm" action="{{ route('komandan.barang') }}" method="GET">
        <div class="bg-white p-4 rounded-lg shadow-md mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                
                {{-- Filter Tanggal --}}
                <div class="sm:col-span-1">
                    <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">TANGGAL:</label>
                    <input type="date" id="tanggal" name="tanggal" 
                           onchange="document.getElementById('filterForm').submit()"
                           class="w-full bg-[#2a4a6f] text-white px-4 py-2 rounded-lg shadow border-none focus:outline-none focus:ring-2 focus:ring-blue-400"
                           style="color-scheme: dark;"
                           value="{{ $tanggalTerpilih }}">
                </div>

                {{-- Filter Kategori --}}
                <div class="sm:col-span-1">
                    <label for="kategori" class="block text-sm font-medium text-gray-700 mb-1">KATEGORI:</label>
                    <select id="kategori" name="kategori" 
                            onchange="document.getElementById('filterForm').submit()"
                            class="w-full bg-[#2a4a6f] text-white px-4 py-2 rounded-lg shadow border-none focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="temuan" {{ $kategoriTerpilih == 'temuan' ? 'selected' : '' }}>Barang Temuan</option>
                        <option value="titipan" {{ $kategoriTerpilih == 'titipan' ? 'selected' : '' }}>Barang Titipan</option>
                    </select>
                </div>

                {{-- Live Search Input --}}
                <div class="sm:col-span-2">
                    <label for="searchInput" class="block text-sm font-medium text-gray-700 mb-1">CARI:</label>
                    <input type="text" id="searchInput" name="search" 
                           class="w-full bg-[#2a4a6f] text-white px-4 py-2 rounded-lg shadow border-none focus:outline-none focus:ring-2 focus:ring-blue-400 placeholder-gray-300" 
                           value="{{ $jenisTerpilih }}" 
                           placeholder="Ketik untuk mencari...">
                </div>
            </div>
        </div>
    </form>

    {{-- Tabel Riwayat Barang (Container ID untuk AJAX Replacement) --}}
    <div id="table-container" class="bg-white rounded-lg shadow-md overflow-hidden mb-6 transition-opacity duration-200">
        <div class="bg-gray-100 p-3 border-b border-gray-200">
            <h3 class="font-bold text-gray-800">RIWAYAT ({{ $kategoriTerpilih == 'temuan' ? 'Barang Temuan' : 'Barang Titipan' }})</h3>
        </div>
        <div class="overflow-x-auto">
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
                            {{-- Data Barang Temuan --}}
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
                            {{-- Data Barang Titipan --}}
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