@extends('layouts.app')

@section('header-left')
    <a class="bg-slate-800 text-white text-sm font-semibold px-6 py-2 rounded-full shadow-md hover:bg-slate-700 transition">
        KENDARAAN
    </a>
@endsection

@section('content')
<div class="w-full max-w-lg mx-auto px-4 py-8">
    
    <h2 class="text-2xl font-bold text-slate-800 mb-6">Edit Kendaraan Terdaftar</h2>

    {{-- Tampilkan Error Validasi --}}
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

    {{-- Form Edit, meniru [cite: image_80f35e.png] --}}
    <form action="{{ route('laporan.kendaraan.master.update', $kendaraan->id_kendaraan) }}" method="POST" class="bg-white p-6 rounded-lg shadow-lg space-y-4">
        @csrf
        @method('PUT') {{-- Method PUT untuk update --}}

        {{-- Nomor Polisi --}}
        <div>
            <label for="nomor_plat" class="block text-sm font-medium text-gray-700">Nomor Polisi:</label>
            <input type="text" id="nomor_plat" name="nomor_plat" 
                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                   value="{{ old('nomor_plat', $kendaraan->nomor_plat) }}" required>
        </div>

        {{-- Pemilik --}}
        <div>
            <label for="pemilik" class="block text-sm font-medium text-gray-700">Pemilik:</label>
            <input type="text" id="pemilik" name="pemilik" 
                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                   value="{{ old('pemilik', $kendaraan->pemilik) }}" required>
        </div>

        {{-- Tipe --}}
        <div>
            <label for="tipe" class="block text-sm font-medium text-gray-700">Tipe:</label>
            <select id="tipe" name="tipe" 
                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                <option value="Roda 2" {{ (old('tipe', $kendaraan->tipe) == 'Roda 2') ? 'selected' : '' }}>
                    Roda 2
                </option>
                <option value="Roda 4" {{ (old('tipe', $kendaraan->tipe) == 'Roda 4') ? 'selected' : '' }}>
                    Roda 4
                </option>
            </select>
        </div>

        {{-- PERUBAHAN: Field Keterangan dihapus [cite: kendaraan.sql] --}}

        {{-- Tombol Simpan --}}
        <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 px-4 rounded-lg shadow-lg hover:bg-blue-700 transition duration-300">
            SIMPAN PERUBAHAN
        </button>

    </form>

</div>
@endsection