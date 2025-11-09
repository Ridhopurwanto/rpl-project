{{-- Halaman ini akan menampilkan daftar presensi semua anggota --}}
@extends('layouts.app')

{{-- 
  Kita ganti tombol 'HOME' di header 
  menjadi tombol 'KEMBALI' ke dashboard komandan 
--}}
@section('header-left')
    <a href="{{ route('komandan.dashboard') }}" class="bg-slate-800 text-white text-sm font-semibold px-6 py-2 rounded-full shadow-md hover:bg-slate-700 transition">
        KEMBALI
    </a>
@endsection

@section('content')
<div class="w-full mx-auto">
    
    {{-- Judul Halaman --}}
    <h2 class="text-2xl font-bold text-slate-800 mb-4">Laporan Presensi Anggota</h2>

    {{-- 
      Area Filter (Mirip di gambar)
      Komandan perlu filter tanggal dan shift 
    --}}
    <div class="bg-white p-4 rounded-lg shadow-md mb-6">
        <div class="flex flex-col sm:flex-row sm:items-end sm:space-x-4 space-y-4 sm:space-y-0">
            
            {{-- Filter Tanggal --}}
            <div class="flex-1">
                <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">TANGGAL:</label>
                {{-- 
                  Kita gunakan 'date' agar muncul kalender. 
                  Nilai defaultnya adalah hari ini.
                --}}
                <input type="date" id="tanggal" name="tanggal" 
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                       value="{{ now()->format('Y-m-d') }}">
            </div>

            {{-- Filter Shift --}}
            <div class="flex-1">
                <label for="shift" class="block text-sm font-medium text-gray-700 mb-1">JENIS SHIFT:</label>
                <select id="shift" name="shift" 
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="semua">Semua Shift</option>
                    <option value="pagi">Shift Pagi</option>
                    <option value="siang">Shift Siang</option>
                    <option value="malam">Shift Malam</option>
                </select>
            </div>

            {{-- Tombol Filter --}}
            <button class="w-full sm:w-auto bg-blue-600 text-white px-5 py-2 rounded-lg shadow hover:bg-blue-700 transition">
                Tampilkan
            </button>
        </div>
    </div>

    {{-- 
      Tabel Daftar Presensi Masuk
      (Sama seperti di gambar, tapi ini untuk data semua anggota) 
    --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        {{-- Header Tabel --}}
        <div class="bg-gray-100 p-3 border-b border-gray-200">
            <h3 class="font-bold text-gray-800">DAFTAR PRESENSI MASUK</h3>
        </div>
        
        {{-- Konten Tabel --}}
        <div class="overflow-x-auto">
            <table class="w-full min-w-max">
                <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500">
                    <tr>
                        <th class="py-3 px-4 text-left">No</th>
                        <th class="py-3 px-4 text-left">Nama</th>
                        <th class="py-3 px-4 text-left">Waktu</th>
                        <th class="py-3 px-4 text-center">Foto</th>
                        <th class="py-3 px-4 text-left">Status</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    {{-- 
                      Nanti di sini kamu bisa @foreach data dari database.
                      Ini hanya contoh data statis.
                    --}}
                    <tr>
                        <td class="py-2 px-4">1.</td>
                        <td class="py-2 px-4 font-medium">Sabrina</td>
                        <td class="py-2 px-4">07:59:45</td>
                        <td class="py-2 px-4 text-center">
                            <a href="#" class="text-blue-500 hover:underline">Buka</a>
                        </td>
                        <td class="py-2 px-4">
                            <span class="text-red-500 font-semibold">Terlambat</span>
                        </td>
                        <td class="py-2 px-4 text-center">
                            <button class="text-red-500 hover:text-red-700">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2 px-4">2.</td>
                        <td class="py-2 px-4 font-medium">M. Sony</td>
                        <td class="py-2 px-4">06:55:35</td>
                        <td class="py-2 px-4 text-center">
                            <a href="#" class="text-blue-500 hover:underline">Buka</a>
                        </td>
                        <td class="py-2 px-4">
                            <span class="text-green-600 font-semibold">Tepat Waktu</span>
                        </td>
                        <td class="py-2 px-4 text-center">
                            <button class="text-red-500 hover:text-red-700">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    {{-- Kamu bisa tambahkan tabel 'DAFTAR PRESENSI KELUAR' di sini --}}

</div>
@endsection