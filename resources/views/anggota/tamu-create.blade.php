@extends('layouts.app')

{{-- 1. Header (Tombol Kembali) --}}
@section('header-left')
    {{-- Tombol kembali ini mengarah ke Grid (createSession) --}}
    <a href="{{ route('anggota.tamu.index') }}" class="p-2">
        <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
    </a>
@endsection
@section('content')
<div class="w-full min-h-screen bg-slate-100 p-4">

    {{-- Form Card Utama (Kotak Biru Tua) --}}
    <div class="w-full max-w-md mx-auto bg-slate-800 rounded-xl shadow-lg p-6">
        {{-- Hapus judul "Tambah Data Tamu" karena tidak ada di desain --}}

        <form action="{{ route('anggota.tamu.store') }}" method="POST">
            @csrf

            {{-- 
              Menggunakan CSS Grid untuk layout label dan input
              grid-cols-3: satu kolom untuk label (1fr), dua kolom untuk input (2fr)
            --}}
            <div class="grid grid-cols-3 gap-x-4 gap-y-5">

                <label for="nama_tamu" class="col-span-1 text-gray-300 font-semibold text-sm self-center">NAMA :</label>
                <div class="col-span-2">
                    <input 
                        type="text" 
                        id="nama_tamu" 
                        name="nama_tamu" 
                        placeholder="Contoh: Pak Habibullah"
                        class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>

                <label for="instansi" class="col-span-1 text-gray-300 font-semibold text-sm self-center">INSTANSI :</label>
                <div class="col-span-2">
                    <input 
                        type="text" 
                        id="instansi" 
                        name="instansi" 
                        placeholder="Contoh: BPS Pusat"
                        class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>
                
                {{-- --- FIELD TANGGAL (BARU) --- --}}
                <label for="tanggal_kunjungan" class="col-span-1 text-gray-300 font-semibold text-sm self-center">TANGGAL :</label>
                <div class="col-span-2">
                    <div class="relative">
                        <input 
                            type="date" 
                            id="tanggal_kunjungan" 
                            name="tanggal_kunjungan" 
                            value="{{ date('Y-m-d') }}" {{-- Isi otomatis tanggal hari ini --}}
                            class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required>
                        {{-- Ikon kalender di desain --}}
                        <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- --- FIELD JAM KUNJUNGAN (BARU) --- --}}
                <label for="jam_kunjungan" class="col-span-1 text-gray-300 font-semibold text-sm self-center">JAM KUNJUNGAN :</label>
                <div class="col-span-2">
                    <input 
                        type="time" 
                        id="jam_kunjungan" 
                        name="jam_kunjungan" 
                        value="{{ date('H:i') }}" {{-- Isi otomatis jam sekarang --}}
                        class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>

                <label for="tujuan" class="col-span-1 text-gray-300 font-semibold text-sm self-center">TUJUAN :</label>
                <div class="col-span-2">
                    <input 
                        type="text" 
                        id="tujuan" 
                        name="tujuan" 
                        placeholder="Contoh: Wisuda"
                        class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>

            </div>

            <div class="mt-8">
                <button 
                    type="submit" 
                    class="w-full bg-blue-800 text-white font-bold py-3 px-6 rounded-lg shadow-md hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-700 transition-colors duration-300">
                    SUBMIT
                </button>
            </div>

        </form>

    </div>
</div>
@endsection