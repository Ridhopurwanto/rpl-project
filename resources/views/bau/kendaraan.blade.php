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
    <form id="filterForm" action="{{ route('bau.kendaraan.index') }}" method="GET">
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

                {{-- Filter Tipe --}}
                <div class="sm:col-span-1">
                    <label for="tipe" class="block text-sm font-medium text-gray-700 mb-1">TIPE:</label>
                    <select id="tipe" name="tipe" 
                            onchange="document.getElementById('filterForm').submit()"
                            class="w-full bg-[#2a4a6f] text-white px-4 py-2 rounded-lg shadow border-none focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">Semua Tipe</option>
                        <option value="Roda 2" {{ $tipeTerpilih == 'Roda 2' ? 'selected' : '' }}>Roda 2</option>
                        <option value="Roda 4" {{ $tipeTerpilih == 'Roda 4' ? 'selected' : '' }}>Roda 4</option>
                    </select>
                </div>

                {{-- LIVE SEARCH INPUT --}}
                <div class="sm:col-span-2">
                    <label for="searchInput" class="block text-sm font-medium text-gray-700 mb-1">CARI:</label>
                    <input type="text" id="searchInput" name="search" 
                           class="w-full bg-[#2a4a6f] text-white px-4 py-2 rounded-lg shadow border-none focus:outline-none focus:ring-2 focus:ring-blue-400 placeholder-gray-300" 
                           value="{{ $search ?? '' }}" 
                           placeholder="Ketik Nopol atau Nama...">
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
                <table class="w-full min-w-max">
                    <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500">
                        <tr>
                            <th class="py-3 px-4 text-left w-16">No</th>
                            <th class="py-3 px-4 text-left w-32">Nopol</th>
                            <th class="py-3 px-4 text-left">Pemilik</th>
                            <th class="py-3 px-4 text-left w-24">Tipe</th>
                            <th class="py-3 px-4 text-left w-28">Masuk</th>
                            <th class="py-3 px-4 text-left w-28">Keluar</th>
                            <th class="py-3 px-4 text-left w-40">Ket.</th>

                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-200">
                        @forelse($riwayat as $index => $log)
                        <tr>
                            <td class="py-2 px-4">{{ $index + 1 }}.</td>
                            <td class="py-2 px-4 font-medium">{{ $log->nopol ?? 'N/A' }}</td>
                            <td class="py-2 px-4">{{ $log->pemilik ?? 'N/A' }}</td>
                            <td class="py-2 px-4">
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
                                {{ $log->keterangan }}
                            </td>

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
                                <p class="text-gray-800 font-semibold">{{ $log->keterangan }}</p>
                            </div>

                            {{-- Tombol Aksi (Jika Komandan) --}}

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
            <table class="w-full min-w-max">
                <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500">
                    <tr>
                        <th class="py-3 px-4 text-left w-16">No</th>
                        <th class="py-3 px-4 text-left w-40">Nopol</th>
                        <th class="py-3 px-4 text-left">Pemilik</th>
                        <th class="py-3 px-4 text-left w-32">Tipe</th>

                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse($kendaraanMaster as $index => $kendaraan)
                    <tr>
                        <td class="py-2 px-4">{{ $index + 1 }}.</td>
                        <td class="py-2 px-4 font-medium">{{ $kendaraan->nomor_plat }}</td>
                        <td class="py-2 px-4">{{ $kendaraan->pemilik }}</td>
                        <td class="py-2 px-4">{{ $kendaraan->tipe }}</td>

                    </tr>
                    @empty
                    <tr><td colspan="4" class="py-4 px-4 text-center text-gray-500">Tidak ada data.</td></tr>
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
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-4 relative" @click.stop>
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-xl font-bold text-gray-800">EDIT DATA KENDARAAN</h3>
            <button @click="showEditModal = false" class="text-gray-500 hover:text-gray-800 text-3xl">&times;</button>
        </div>
        <form :action="editAction" method="POST" class="mt-4">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label for="edit_nomor_plat" class="block text-sm font-medium text-gray-700 mb-1">Nomor Plat:</label>
                    <input type="text" id="edit_nomor_plat" name="nomor_plat" x-model="editPlat" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label for="edit_pemilik" class="block text-sm font-medium text-gray-700 mb-1">Pemilik:</label>
                    <input type="text" id="edit_pemilik" name="pemilik" x-model="editPemilik" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label for="edit_tipe" class="block text-sm font-medium text-gray-700 mb-1">Tipe:</label>
                    <select id="edit_tipe" name="tipe" x-model="editTipe" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="Roda 2">Roda 2</option>
                        <option value="Roda 4">Roda 4</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-green-500 text-white font-bold py-2 px-4 rounded-lg shadow hover:bg-green-600 transition">SUBMIT</button>
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
