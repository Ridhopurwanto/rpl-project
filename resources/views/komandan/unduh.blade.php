@extends('layouts.app')

{{-- Tombol KEMBALI (atau judul) --}}
@section('header-left')
    <a class="bg-slate-800 text-white text-sm font-semibold px-6 py-2 rounded-full shadow-md hover:bg-slate-700 transition">
        UNDUH
    </a>
@endsection

@section('content')
<div class="w-full mx-auto"
     {{-- Inisialisasi AlpineJS. 'reportType' akan melacak dropdown. --}}
     x-data="{ 
         reportType: 'harian',
         laporanTerpilih: []
     }"
>
    
    <h2 class="text-2xl font-bold text-slate-800 mb-4">Unduh Laporan Gabungan</h2>

    {{-- 
      CATATAN PENTING: 
      Fitur PDF/Excel & "Tambah" ke antrian sangat kompleks.
      Formulir ini akan mengirim SEMUA data yang dicentang sekaligus 
      saat "UNDUH LAPORAN GABUNGAN" ditekan.
    --}}
    <form action="{{ route('komandan.laporan.download') }}" method="POST">
        @csrf

        {{-- BAGIAN FILTER ATAS --}}
        <div class="bg-white p-4 rounded-lg shadow-md mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                
                {{-- 1. Dropdown Harian/Bulanan --}}
                <div>
                    <label for="report_type" class="block text-sm font-medium text-gray-700 mb-1">JENIS LAPORAN:</label>
                    <select id="report_type" name="report_type" x-model="reportType"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="harian">Laporan Harian</option>
                        <option value="bulanan">Laporan Bulanan</option>
                    </select>
                </div>
                
                {{-- 2. Filter Tanggal Mulai --}}
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">DARI TANGGAL:</label>
                    <input type="date" id="date_from" name="date_from" 
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                           value="{{ now()->format('Y-m-d') }}">
                </div>

                {{-- 3. Filter Tanggal Selesai --}}
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">SAMPAI TANGGAL:</label>
                    <input type="date" id="date_to" name="date_to" 
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                           value="{{ now()->format('Y-m-d') }}">
                </div>
            </div>
        </div>

        {{-- BAGIAN CHECKBOX PREVIEW --}}
        <div class="bg-white p-4 rounded-lg shadow-md mb-6">
            <h3 class="font-bold text-gray-800 mb-3 border-b pb-2">PREVIEW (PILIH LAPORAN)</h3>
            
            {{-- Daftar Checkbox Laporan Harian (Muncul jika reportType == 'harian') --}}
            <div x-show="reportType === 'harian'" class="space-y-3">
                <label class="flex items-center">
                    <input type="checkbox" name="laporan[]" value="presensi" class="rounded text-blue-600">
                    <span class="ml-2 text-gray-700">Laporan Presensi</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="laporan[]" value="patroli" class="rounded text-blue-600">
                    <span class="ml-2 text-gray-700">Laporan Patroli</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="laporan[]" value="barang" class="rounded text-blue-600">
                    <span class="ml-2 text-gray-700">Laporan Pengelolaan Barang</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="laporan[]" value="kendaraan" class="rounded text-blue-600">
                    <span class="ml-2 text-gray-700">Laporan Kendaraan Keluar Masuk</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="laporan[]" value="tamu" class="rounded text-blue-600">
                    <span class="ml-2 text-gray-700">Laporan Kedatangan Tamu</span>
                </label>
            </div>

            {{-- Daftar Checkbox Laporan Bulanan (Muncul jika reportType == 'bulanan') --}}
            <div x-show="reportType === 'bulanan'" class="space-y-3" style="display: none;">
                <label class="flex items-center">
                    <input type="checkbox" name="laporan[]" value="gangguan" class="rounded text-blue-600">
                    <span class="ml-2 text-gray-700">Laporan Gangguan Kamtibmas</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="laporan[]" value="shift" class="rounded text-blue-600">
                    <span class="ml-2 text-gray-700">Laporan Shift Anggota</span>
                </label>
            </div>
        </div>

        {{-- Tombol Download Gabungan --}}
        <div class="mt-6">
            <button type="submit" name="format" value="excel" class="w-full bg-green-600 text-white font-bold py-3 px-4 rounded-lg shadow hover:bg-green-700 transition mb-4">
                UNDUH LAPORAN GABUNGAN (EXCEL)
            </button>
            <button type="submit" name="format" value="pdf" class="w-full bg-red-600 text-white font-bold py-3 px-4 rounded-lg shadow hover:bg-red-700 transition">
                UNDUH LAPORAN GABUNGAN (PDF)
            </button>
        </div>

    </form>
</div>
@endsection