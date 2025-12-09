@extends('layouts.app')

{{-- Terapkan layout full-width jika ada --}}
@section('mobile_width', 'max-w-full')
@section('desktop_width', 'lg:max-w-full')

@section('header-left')
    <a class="inline-block border-2 border-[#1a2847] text-[#1a2847] text-sm font-bold px-4 py-1 rounded-full mb-4">
        TAMU
    </a>
@endsection

@section('content')
<div class="w-full mx-auto"
     x-data="{ 
        showEditModal: false, 
        editAction: '',
        editNama: '',
        editInstansi: '',
        editTujuan: '',
        editWaktuDatang: '',
        showDeleteModal: false,
        deleteAction: '' 
     }">
    
    <h2 class="text-2xl font-bold text-slate-800 mb-4">Laporan Kunjungan Tamu</h2>

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

    {{-- Form Filter Tanggal --}}
    <form action="{{ route('bau.tamu.index') }}" method="GET">
        <div class="bg-white p-4 rounded-lg shadow-md mb-6">
            <div class="flex flex-col sm:flex-row sm:items-end sm:space-x-4 space-y-4 sm:space-y-0">
                
                {{-- Input Rentang Tanggal (Grid 2 Kolom) --}}
                <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">DARI TANGGAL:</label>
                        <input type="date" id="start_date" name="start_date" 
                               onchange="this.form.submit()"
                               class="w-full bg-[#2a4a6f] text-white px-4 py-2 rounded-lg shadow border-none focus:outline-none focus:ring-2 focus:ring-blue-400" 
                               style="color-scheme: dark;"
                               value="{{ $startDate }}">
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">SAMPAI TANGGAL:</label>
                        <input type="date" id="end_date" name="end_date" 
                               onchange="this.form.submit()"
                               class="w-full bg-[#2a4a6f] text-white px-4 py-2 rounded-lg shadow border-none focus:outline-none focus:ring-2 focus:ring-blue-400" 
                               style="color-scheme: dark;"
                               value="{{ $endDate }}">
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Tabel Riwayat Tamu --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="bg-gray-100 p-3 border-b border-gray-200">
            <h3 class="font-bold text-gray-800">RIWAYAT KUNJUNGAN</h3>
        </div>
        
        {{-- TABEL (Desktop) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full min-w-max">
                <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500">
                    <tr>
                        <th class="py-3 px-4 text-left w-16">No</th>
                        <th class="py-3 px-4 text-left">Nama</th>
                        <th class="py-3 px-4 text-left w-48">Instansi</th>
                        <th class="py-3 px-4 text-left w-32">Waktu Kunjungan</th>
                        <th class="py-3 px-4 text-left">Tujuan</th>

                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse($riwayatTamu as $index => $tamu)
                    <tr>
                        <td class="py-2 px-4">{{ $index + 1 }}.</td>
                        <td class="py-2 px-4 font-medium">{{ $tamu->nama_tamu }}</td>
                        <td class="py-2 px-4">{{ $tamu->instansi }}</td>
                        <td class="py-2 px-4 text-gray-700 whitespace-nowrap">
                            {{ $tamu->waktu_datang->format('d/m/Y H:i') }}
                        </td>
                        <td class="py-2 px-4">{{ $tamu->tujuan }}</td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-4 px-4 text-center text-gray-500">
                            Tidak ada data kunjungan tamu pada tanggal ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- CARD LAYOUT (Mobile) --}}
        <div class="md:hidden space-y-3 p-3">
            @forelse($riwayatTamu as $index => $tamu)
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                    {{-- Header --}}
                    <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] px-4 py-2.5 flex justify-between items-center">
                        <div class="flex-1">
                            <p class="text-xs text-blue-200 font-semibold uppercase">Tamu</p>
                            <p class="text-white font-bold text-base">{{ $tamu->nama_tamu }}</p>
                        </div>
                        <span class="bg-blue-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg">
                            {{ $tamu->waktu_datang->format('d/m/Y') }}
                        </span>
                    </div>

                    {{-- Body --}}
                    <div class="p-4 space-y-3">
                        {{-- Instansi --}}
                        <div class="flex items-center gap-2 pb-2 border-b border-gray-100">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <div class="flex-1">
                                <p class="text-[10px] text-gray-500 font-semibold uppercase">Instansi</p>
                                <p class="text-gray-800 font-bold text-sm">{{ $tamu->instansi }}</p>
                            </div>
                        </div>

                        {{-- Waktu & Tujuan --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <p class="text-[10px] text-gray-500 font-semibold uppercase mb-1">Waktu</p>
                                <p class="text-gray-800 font-bold text-xs">{{ $tamu->waktu_datang->format('H:i') }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-500 font-semibold uppercase mb-1">Tujuan</p>
                                <p class="text-gray-800 font-bold text-xs">{{ $tamu->tujuan }}</p>
                            </div>
                        </div>

                        {{-- Tombol Aksi (Jika Komandan) --}}

                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl shadow-md p-8 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-500 font-semibold">Tidak ada data kunjungan tamu pada tanggal ini.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Modal Edit Tamu --}}
    <div x-show="showEditModal"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
         @click.away="showEditModal = false"
         style="display: none;">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-4 relative" @click.stop>
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 class="text-xl font-bold text-gray-800">EDIT DATA TAMU</h3>
                <button @click="showEditModal = false" class="text-gray-500 hover:text-gray-800 text-3xl">&times;</button>
            </div>
            
            <form :action="editAction" method="POST" class="mt-4">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    
                    <div>
                        <label for="nama_tamu" class="block text-sm font-medium text-gray-700 mb-1">Nama:</label>
                        <input type="text" id="nama_tamu" name="nama_tamu" x-model="editNama"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="instansi" class="block text-sm font-medium text-gray-700 mb-1">Instansi:</label>
                        <input type="text" id="instansi" name="instansi" x-model="editInstansi"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="tujuan" class="block text-sm font-medium text-gray-700 mb-1">Tujuan:</label>
                        <input type="text" id="tujuan" name="tujuan" x-model="editTujuan"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="waktu_datang" class="block text-sm font-medium text-gray-700 mb-1">Waktu Kunjungan:</label>
                        <input type="datetime-local" id="waktu_datang" name="waktu_datang" x-model="editWaktuDatang"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <button type="submit" class="w-full bg-green-500 text-white font-bold py-2 px-4 rounded-lg shadow hover:bg-green-600 transition">
                        SUBMIT
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

</div>
@endsection
