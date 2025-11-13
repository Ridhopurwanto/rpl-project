@extends('layouts.app')

@section('content')
{{-- 
  Kita bungkus dengan Alpine.js untuk mengelola state modal foto
  modalOpen = false (modal tertutup)
  modalImage = '' (path gambar yang akan ditampilkan)
--}}
<div class="w-full min-h-screen bg-slate-100 p-4 pb-32" x-data="{ modalOpen: false, modalImage: '' }">

    {{-- Form Filter --}}
    <form action="{{ route('anggota.gangguan.index') }}" method="GET" class="mb-4">
        <div class="grid grid-cols-2 gap-4">
            {{-- Filter Tanggal Mulai --}}
            <div>
                <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700">TANGGAL (Mulai):</label>
                <input 
                    type="date" 
                    id="tanggal_mulai"
                    name="tanggal_mulai"
                    value="{{ $tanggal_mulai }}"
                    class="mt-1 bg-blue-600 text-white px-4 py-2 rounded-lg shadow border-none w-full focus:outline-none focus:ring-2 focus:ring-blue-400"
                >
            </div>
            {{-- Filter Tanggal Selesai --}}
            <div>
                <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700">TANGGAL (Selesai):</label>
                <input 
                    type="date" 
                    id="tanggal_selesai"
                    name="tanggal_selesai"
                    value="{{ $tanggal_selesai }}"
                    class="mt-1 bg-blue-600 text-white px-4 py-2 rounded-lg shadow border-none w-full focus:outline-none focus:ring-2 focus:ring-blue-400"
                >
            </div>
        </div>

        {{-- Filter Kategori --}}
        <div class="mt-4">
            <label for="kategori" class="block text-sm font-medium text-gray-700">KATEGORI:</label>
            <select 
                id="kategori" 
                name="kategori"
                class="mt-1 bg-blue-600 text-white px-4 py-2 rounded-lg shadow border-none w-full focus:outline-none focus:ring-2 focus:ring-blue-400"
            >
                <option value="semua" @if($kategori_terpilih == 'semua') selected @endif>Semua Kategori</option>
                {{-- Kategori dari screenshot Anda --}}
                <option value="Unjuk Rasa" @if($kategori_terpilih == 'Unjuk Rasa') selected @endif>Unjuk Rasa</option>
                <option value="Pembakaran Lahan" @if($kategori_terpilih == 'Pembakaran Lahan') selected @endif>Pembakaran Lahan</option>
                <option value="Bentrokan Kepolisian" @if($kategori_terpilih == 'Bentrokan Kepolisian') selected @endif>Bentrokan Kepolisian</option>
                {{-- Tambahkan kategori lain di sini jika perlu --}}
            </select>
        </div>

        {{-- Tombol Submit Filter --}}
        <div class="mt-4">
            <button type="submit" class="w-full bg-slate-700 text-white py-2 rounded-lg shadow hover:bg-slate-800">
                Terapkan Filter
            </button>
        </div>
    </form>


    {{-- Kontainer tabel riwayat --}}
    <div class="bg-white rounded-lg shadow-md p-4 mt-4 overflow-x-auto">
        <table class="w-full min-w-[600px] text-sm text-left">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th class="py-3 px-4">No</th>
                    <th class="py-3 px-4">Foto</th>
                    <th class="py-3 px-4">Waktu</th>
                    <th class="py-3 px-4">Lokasi</th>
                    <th class="py-3 px-4">Ket</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($laporan_gangguan as $laporan)
                <tr class="bg-white">
                    <td class="py-3 px-4 font-medium">{{ $loop->iteration }}.</td>
                    <td class="py-3 px-4">
                        {{-- Tombol Buka - Memanggil Alpine.js --}}
                        <button 
                            @click.prevent="modalOpen = true; modalImage = '{{ Storage::url($laporan->foto) }}'"
                            class="text-blue-600 hover:underline">
                            Buka
                        </button>
                    </td>
                    <td class="py-3 px-4">{{ $laporan->waktu_lapor->format('H:i:s') }}</td>
                    <td class="py-3 px-4">{{ $laporan->lokasi }}</td>
                    <td class="py-3 px-4">{{ $laporan->deskripsi }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-4 px-4 text-center text-gray-500">
                        Tidak ada laporan pada filter ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Tombol Aksi Tambah (FAB) --}}
    <a href="{{ route('anggota.gangguan.create') }}" class="fixed bottom-24 right-4 bg-blue-800 text-white rounded-full w-16 h-16 flex items-center justify-center shadow-lg transform hover:scale-110 transition-transform">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
    </a>

    {{-- MODAL FOTO (POP-UP) --}}
    <div 
        x-show="modalOpen" 
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center p-4 z-50"
        style="display: none;"
    >
        {{-- Kontainer Modal --}}
        <div 
            @click.outside="modalOpen = false"
            class="bg-white rounded-lg shadow-xl w-full max-w-lg p-4 relative"
        >
            {{-- Tombol Close (X) --}}
            <button 
                @click.prevent="modalOpen = false" 
                class="absolute -top-4 -right-4 bg-red-600 text-white rounded-full w-10 h-10 flex items-center justify-center shadow-lg text-2xl font-bold">&times;</button>
            
            <h3 class="text-xl font-bold text-center mb-4">PHOTO</h3>
            
            {{-- Tempat Gambar --}}
            <img :src="modalImage" alt="Foto Gangguan" class="w-full h-auto max-h-[70vh] object-contain">
        </div>
    </div>

</div>
@endsection