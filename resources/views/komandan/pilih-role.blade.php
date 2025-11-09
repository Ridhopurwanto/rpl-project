@extends('layouts.app')

@section('content')
{{-- Wrapper konsisten dengan dashboard.blade.php --}}
<div class="w-full px-4 py-8">
    
    {{-- Header (Logo, Judul) - Meniru style gambar & kode Anda --}}
    <div class="bg-slate-800 text-center p-8 rounded-xl shadow-lg mb-6">
        
        {{-- Ganti path logo ini sesuai dengan lokasi logo Anda di folder /public --}}
        <img src="{{ asset('images/logo-siap.png') }}" alt="Logo SIAP" class="w-24 h-24 mx-auto mb-4">
        
        <h1 class="text-white text-4xl font-bold">SIAP</h1>
        <p class="text-gray-300">Sistem Informasi Administrasi dan Pelaporan</p>
    </div>

    {{-- Box Pemilihan Menu (Meniru area krem/putih pada gambar) --}}
    <div class="bg-white p-6 rounded-xl shadow-lg mb-6">
        <h2 class="text-center text-xl font-bold text-gray-800 mb-6">PILIH PERAN :</h2>
        
        <div class="flex justify-around items-center">
            
            {{-- Tombol Anggota (Kuning dari gambar) --}}
            <a href="{{ route('anggota.dashboard') }}" class="text-center group">
                <div class="w-28 h-28 bg-yellow-400 rounded-full flex items-center justify-center shadow-lg transform group-hover:scale-105 transition duration-300">
                    <svg class="w-16 h-16 text-gray-800" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                </div>
                <span class="mt-3 block text-lg font-semibold text-gray-800">Anggota</span>
            </a>
            
            {{-- Tombol Komandan (Coklat dari gambar) --}}
            <a href="{{ route('komandan.dashboard') }}" class="text-center group">
                <div class="w-28 h-28 bg-amber-700 rounded-full flex items-center justify-center shadow-lg transform group-hover:scale-105 transition duration-300">
                    <svg class="w-16 h-16 text-white" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                </div>
                <span class="mt-3 block text-lg font-semibold text-gray-800">Komandan</span>
            </a>

        </div>
    </div>
</div>
@endsection