@extends('layouts.app')

@section('header-left')
    <a href="{{ route('anggota.kendaraan.index') }}" class="p-2">
        <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
    </a>
@endsection

@section('content')
{{-- 
  Wrapper utama, memberikan background abu-abu muda
  dan padding seperti halaman sebelumnya.
--}}
<div class="w-full min-h-screen bg-slate-100 p-4">

    {{-- 
      Form Card Utama (Kotak Biru Tua)
      - max-w-md -> membatasi lebar agar tidak terlalu besar di desktop
      - mx-auto -> menengahkan card
      - bg-slate-800 -> warna biru tua/gelap sesuai desain
      - rounded-xl, shadow-lg, p-6 -> styling card
    --}}
    <div class="w-full max-w-md mx-auto bg-slate-800 rounded-xl shadow-lg p-6">

        {{-- Form akan di-POST ke controller 'store' --}}
        <form action="{{ route('anggota.kendaraan.store') }}" method="POST">
            @csrf

            {{-- 
              Kita gunakan Grid untuk layout label-input agar rapi.
              grid-cols-3 -> 1 kolom untuk label, 2 kolom untuk input
              items-center -> menengahkan label & input secara vertikal
            --}}
            <div class="grid grid-cols-3 gap-x-4 gap-y-5">

                <label for="nopol" class="col-span-1 text-gray-300 font-semibold text-sm self-center">PLAT NOMOR :</label>
                <div class="col-span-2">
                    <input 
                        type="text" 
                        id="nopol" 
                        name="nopol" 
                        placeholder="AB 4422 DC"
                        class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>

                <label for="pemilik" class="col-span-1 text-gray-300 font-semibold text-sm self-center">PEMILIK :</label>
                <div class="col-span-2">
                    <input 
                        type="text" 
                        id="pemilik" 
                        name="pemilik" 
                        placeholder="PAK IBNU"
                        class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>

                <label for="tipe" class="col-span-1 text-gray-300 font-semibold text-sm self-center">TIPE :</label>
                <div class="col-span-2">
                    <select 
                        id="tipe" 
                        name="tipe"
                        class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                        <option value="Roda 2">Roda 2</option>
                        <option value="Roda 4">Roda 4</option>
                    </select>
                </div>

                <label for="keterangan" class="col-span-1 text-gray-300 font-semibold text-sm self-center">KETERANGAN :</label>
                <div class="col-span-2">
                    <select 
                        id="keterangan" 
                        name="keterangan"
                        class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                        <option value="Tidak Menginap">Tidak Menginap</option>
                        <option value="Menginap">Menginap</option>
                    </select>
                </div>

                <label for="tanggal" class="col-span-1 text-gray-300 font-semibold text-sm self-center">TANGGAL :</label>
                <div class="col-span-2">
                    <input 
                        type="date" 
                        id="tanggal" 
                        name="tanggal"
                        value="{{ date('Y-m-d') }}" {{-- Otomatis isi tanggal hari ini --}}
                        class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>

                <label for="waktu" class="col-span-1 text-gray-300 font-semibold text-sm self-center">WAKTU :</label>
                <div class="col-span-2">
                    <input 
                        type="time" 
                        id="waktu" 
                        name="waktu"
                        value="{{ date('H:i') }}" {{-- Otomatis isi waktu sekarang --}}
                        class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>

            </div>

            <div class="mt-8">
                <button 
                    type="submit" 
                    class="w-full bg-gray-500 text-white font-bold py-3 px-6 rounded-lg shadow-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-400 transition-colors duration-300">
                    SUBMIT
                </button>
            </div>

        </form>

    </div>
</div>
@endsection