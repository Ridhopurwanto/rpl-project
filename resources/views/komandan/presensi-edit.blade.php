@extends('layouts.app')

{{-- Tombol KEMBALI ke halaman laporan --}}
@section('header-left')
    <a href="{{ route('laporan.presensi') }}" class="bg-slate-800 text-white text-sm font-semibold px-6 py-2 rounded-full shadow-md hover:bg-slate-700 transition">
        KEMBALI
    </a>
@endsection

@section('content')
<div class="w-full max-w-lg mx-auto px-4 py-8">
    
    <h2 class="text-2xl font-bold text-slate-800 mb-6">Edit Data Presensi</h2>

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

    <form action="{{ route('laporan.presensi.update', $presensi->id_presensi) }}" method="POST" class="bg-white p-6 rounded-lg shadow-lg space-y-4">
        @csrf
        @method('PUT') {{-- Method PUT untuk update --}}

        {{-- Nama Lengkap (tidak bisa diedit) --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Nama Anggota:</label>
            <input type="text" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm bg-gray-100" 
                   value="{{ $presensi->nama_lengkap }}" disabled>
        </div>

        {{-- Lokasi --}}
        <div>
            <label for="lokasi" class="block text-sm font-medium text-gray-700">Lokasi:</label>
            <input type="text" id="lokasi" name="lokasi" 
                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                   value="{{ old('lokasi', $presensi->lokasi) }}" required>
        </div>

        {{-- Waktu Masuk --}}
        <div>
            <label for="waktu_masuk" class="block text-sm font-medium text-gray-700">Waktu Masuk:</label>
            <input type="datetime-local" id="waktu_masuk" name="waktu_masuk" 
                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                   value="{{ old('waktu_masuk', $presensi->waktu_masuk ? $presensi->waktu_masuk->format('Y-m-d\TH:i') : '') }}" required>
        </div>

        {{-- Waktu Pulang --}}
        <div>
            <label for="waktu_pulang" class="block text-sm font-medium text-gray-700">Waktu Pulang:</label>
            <input type="datetime-local" id="waktu_pulang" name="waktu_pulang" 
                   class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                   value="{{ old('waktu_pulang', $presensi->waktu_pulang ? $presensi->waktu_pulang->format('Y-m-d\TH:i') : '') }}">
        </div>

        {{-- Status --}}
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700">Status:</label>
            <select id="status" name="status" 
                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                <option value="tepat waktu" {{ (old('status', $presensi->status) == 'tepat waktu') ? 'selected' : '' }}>
                    Tepat Waktu
                </option>
                <option value="terlambat" {{ (old('status', $presensi->status) == 'terlambat') ? 'selected' : '' }}>
                    Terlambat
                </option>
                <option value="terlalu cepat" {{ (old('status', $presensi->status) == 'terlalu cepat') ? 'selected' : '' }}>
                    Terlalu Cepat
                </option>
            </select>
        </div>

        {{-- Tombol Simpan --}}
        <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 px-4 rounded-lg shadow-lg hover:bg-blue-700 transition duration-300">
            SIMPAN PERUBAHAN
        </button>

    </form>

</div>
@endsection