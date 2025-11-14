@extends('layouts.app')

@section('content')
{{-- 
  Setup Alpine.js
  DISEDERHANAKAN: Semua state dan fungsi kamera telah dihapus.
--}}
<div class="w-full min-h-screen bg-slate-100 p-4 pb-32" 
     x-data="{ 
        photoModalOpen: false, 
        photoModalImage: '',
        
        selesaiModalOpen: false,
        selesaiFormAction: '',
        
        // Properti untuk Form Selesai (Hanya ini yang tersisa)
        namaPenerima: '',
        tanggalSelesai: '{{ now()->format('Y-m-d') }}',
        waktuSelesai: '{{ now()->format('H:i') }}'
     }"
>

    {{-- 1. BAGIAN BARANG TITIPAN (AKTIF) --}}
    <details open class="mb-4">
        <summary class="text-lg font-bold text-slate-700 uppercase cursor-pointer list-none flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            BARANG TITIPAN :
        </summary>
        <div class="bg-white rounded-lg shadow-md p-4 mt-2 overflow-x-auto">
            <table class="w-full min-w-[700px] text-sm text-left">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="py-3 px-4">No</th>
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">Nama Barang</th>
                        <th class="py-3 px-4">Penitip</th>
                        <th class="py-3 px-4">Tujuan</th>
                        <th class="py-3 px-4">Catatan</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($barang_titipan as $barang)
                    <tr class="bg-white hover:bg-gray-50">
                        <td class="py-3 px-4">{{ $loop->iteration }}.</td>
                        <td class="py-3 px-4">{{ $barang->waktu_titip->format('d/m/y') }}</td>
                        <td class="py-3 px-4 font-medium">{{ $barang->nama_barang }}</td>
                        <td class="py-3 px-4">{{ $barang->nama_penitip }}</td>
                        <td class="py-3 px-4">{{ $barang->tujuan }}</td>
                        <td class="py-3 px-4">{{ $barang->catatan }}</td>
                        <td class="py-3 px-4 text-center">
                            {{-- 
                                PERUBAHAN:
                                $nextTick untuk kamera dihapus
                            --}}
                            <button 
                                @click.prevent="
                                    selesaiModalOpen = true; 
                                    selesaiFormAction = '{{ route('anggota.barang.selesaiTitipan', $barang->id_barang) }}';
                                    tanggalSelesai = '{{ now()->format('Y-m-d') }}';
                                    waktuSelesai = '{{ now()->format('H:i') }}';
                                "
                                class="bg-blue-600 text-white text-xs font-bold uppercase px-4 py-2 rounded-md shadow hover:bg-blue-700">
                                Selesai
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="py-4 px-4 text-center text-gray-500">Tidak ada barang titipan aktif.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </details>

    {{-- 2. BAGIAN BARANG TEMUAN (AKTIF) --}}
    <details open class="mb-4">
        <summary class="text-lg font-bold text-slate-700 uppercase cursor-pointer list-none flex items-center">
             <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            BARANG TEMUAN :
        </summary>
        <div class="bg-white rounded-lg shadow-md p-4 mt-2 overflow-x-auto">
            <table class="w-full min-w-[700px] text-sm text-left">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="py-3 px-4">No</th>
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">Nama Barang</th>
                        <th class="py-3 px-4">Pelapor</th>
                        <th class="py-3 px-4">Lokasi Penemuan</th>
                        <th class="py-3 px-4">Catatan</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($barang_temuan as $barang)
                    <tr class="bg-white hover:bg-gray-50">
                        <td class="py-3 px-4">{{ $loop->iteration }}.</td>
                        <td class="py-3 px-4">{{ $barang->waktu_lapor->format('d/m/y') }}</td>
                        <td class="py-3 px-4 font-medium">{{ $barang->nama_barang }}</td>
                        <td class="py-3 px-4">{{ $barang->nama_pelapor }}</td>
                        <td class="py-3 px-4">{{ $barang->lokasi_penemuan }}</td>
                        <td class="py-3 px-4">{{ $barang->catatan }}</td>
                        <td class="py-3 px-4 text-center">
                            {{-- 
                                PERUBAHAN:
                                $nextTick untuk kamera dihapus
                            --}}
                            <button 
                                @click.prevent="
                                    selesaiModalOpen = true; 
                                    selesaiFormAction = '{{ route('anggota.barang.selesaiTemuan', $barang->id_barang) }}';
                                    tanggalSelesai = '{{ now()->format('Y-m-d') }}';
                                    waktuSelesai = '{{ now()->format('H:i') }}';
                                "
                                class="bg-blue-600 text-white text-xs font-bold uppercase px-4 py-2 rounded-md shadow hover:bg-blue-700">
                                Selesai
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="py-4 px-4 text-center text-gray-500">Tidak ada barang temuan aktif.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </details>

    {{-- 3. BAGIAN RIWAYAT (SELESAI) --}}
    <details open class="mb-4">
        <summary class="text-lg font-bold text-slate-700 uppercase cursor-pointer list-none flex items-center">
             <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            RIWAYAT :
        </summary>
        
        {{-- Form Filter Riwayat (Tidak Berubah) --}}
        <form action="{{ route('anggota.barang.index') }}" method="GET" class="bg-white p-4 rounded-lg shadow-md mt-2">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="tanggal" class="text-sm font-semibold text-slate-600">TANGGAL :</label>
                    <input type="date" name="tanggal" value="{{ $tanggal_terpilih }}" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg shadow border-none focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div>
                    <label for="kategori_riwayat" class="text-sm font-semibold text-slate-600">KATEGORI :</label>
                    <select name="kategori_riwayat" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg shadow border-none focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="semua" @if($kategori_terpilih == 'semua') selected @endif>Semua</option>
                        <option value="titip" @if($kategori_terpilih == 'titip') selected @endif>Barang Titipan</option>
                        <option value="temu" @if($kategori_terpilih == 'temu') selected @endif>Barang Temuan</option>
                    </select>
                </div>
                <div class="self-end">
                    <button type="submit" class="w-full bg-slate-700 text-white py-2 px-4 rounded-lg shadow hover:bg-slate-800">
                        Filter Riwayat
                    </button>
                </div>
            </div>
        </form>

        {{-- Tabel Riwayat (Tidak Berubah) --}}
        <div class="bg-white rounded-lg shadow-md p-4 mt-4 overflow-x-auto">
            <table class="w-full min-w-[700px] text-sm text-left">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="py-3 px-4">No</th>
                        <th class="py-3 px-4">Foto Bar.</th>
                        <th class="py-3 px-4">Nama Barang</th>
                        <th class="py-3 px-4">Pelapor/Penitip</th>
                        <th class="py-3 px-4">Lokasi/Tujuan</th>
                        <th class="py-3 px-4">Catatan</th>
                        <th class="py-3 px-4">Penerima</th>
                        <th class="py-3 px-4">Foto Penerima</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($riwayat_barang as $barang)
                    <tr class="bg-white">
                        <td class="py-3 px-4">{{ $loop->iteration }}.</td>
                        <td class="py-3 px-4">
                            @if($barang->foto)
                            <button 
                                @click.prevent="
                                    photoModalOpen = true; 
                                    photoModalImage = '{{ Storage::url($barang->foto) }}'
                                "
                                class="text-blue-600 hover:underline">
                                Buka
                            </button>
                            @else - @endif
                        </td>
                        <td class="py-3 px-4 font-medium">{{ $barang->nama_barang }}</td>
                        <td class="py-3 px-4">
                            @if($barang instanceof \App\Models\BarangTitipan)
                                {{ $barang->nama_penitip }}
                            @else
                                {{ $barang->nama_pelapor }}
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            @if($barang instanceof \App\Models\BarangTitipan)
                                {{ $barang->tujuan }}
                            @else
                                {{ $barang->lokasi_penemuan }}
                            @endif
                        </td>
                        <td class="py-3 px-4">{{ $barang->catatan }}</td>
                        <td class="py-3 px-4 font-semibold">{{ $barang->nama_penerima }}</td>
                        <td class="py-3 px-4">
                            @if($barang->foto_penerima)
                            <button 
                                @click.prevent="
                                    photoModalOpen = true; 
                                    photoModalImage = '{{ Storage::url($barang->foto_penerima) }}'
                                "
                                class="text-blue-600 hover:underline">
                                Buka
                            </button>
                            @else - @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="py-4 px-4 text-center text-gray-500">Tidak ada riwayat pada filter ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </details>

    {{-- Tombol Aksi Tambah (FAB) --}}
    <a href="{{ route('anggota.barang.create') }}" class="fixed bottom-24 right-4 bg-blue-800 text-white rounded-full w-16 h-16 flex items-center justify-center shadow-lg transform hover:scale-110 transition-transform">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
    </a>

    {{-- 5. MODAL FOTO (POP-UP) (Tidak Berubah) --}}
    <div x-show="photoModalOpen" style="display: none;" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center p-4 z-50" x-transition>
        <div @click.outside="photoModalOpen = false" class="bg-white rounded-lg shadow-xl w-full max-w-lg p-4 relative">
            <button @click.prevent="photoModalOpen = false" class="absolute -top-4 -right-4 bg-white rounded-full p-1 text-3xl text-gray-700 leading-none">&times;</button>
            <h3 class="text-xl font-bold text-center mb-4">PHOTO</h3>
            <img :src="photoModalImage" alt="Foto Barang" class="w-full h-auto max-h-[70vh] object-contain">
        </div>
    </div>

    {{-- 
        6. MODAL KONFIRMASI "SELESAI" (DISEDERHANAKAN) 
    --}}
    <div 
        x-show="selesaiModalOpen" 
        x-transition
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
        style="display: none;"
    >
        <div 
            @click.outside="selesaiModalOpen = false"
            class="bg-white rounded-lg shadow-xl w-full max-w-sm relative overflow-hidden"
        >
            {{-- Tombol close tidak perlu mematikan kamera lagi --}}
            <button @click.prevent="selesaiModalOpen = false" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-2xl font-bold z-10 p-1">&times;</button>
            
            <form :action="selesaiFormAction" method="POST" class="p-6">
                @csrf
                @method('PUT')
                
                {{-- 
                    PERUBAHAN:
                    Semua elemen kamera (input hidden, canvas, video, img, tombol) DIHAPUS.
                --}}
                
                <h3 class="text-xl font-bold text-gray-800 text-center mb-6">PENGAMBILAN BARANG</h3>
                
                {{-- Input Nama --}}
                <div class="mb-4">
                    <label for="nama_penerima" class="block text-sm font-semibold text-gray-700 mb-1">NAMA :</label>
                    <input type="text" x-model="namaPenerima" name="nama_penerima" id="nama_penerima" required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-gray-900">
                </div>

                {{-- Input Tanggal --}}
                <div class="mb-4">
                    <label for="tanggal_selesai_manual" class="block text-sm font-semibold text-gray-700 mb-1">TANGGAL :</label>
                    <input type="date" x-model="tanggalSelesai" name="tanggal_selesai_manual" id="tanggal_selesai_manual" required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-gray-900">
                </div>

                {{-- Input Waktu --}}
                <div class="mb-6">
                    <label for="waktu_selesai_jam_manual" class="block text-sm font-semibold text-gray-700 mb-1">WAKTU :</label>
                    <input type="time" x-model="waktuSelesai" name="waktu_selesai_jam_manual" id="waktu_selesai_jam_manual" required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-gray-900">
                </div>

                <div class="flex justify-end space-x-3">
                    {{-- Tombol Batal --}}
                    <button type="button" 
                            @click.prevent="selesaiModalOpen = false" 
                            class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                        Batal
                    </button>
                    
                    {{-- 
                        PERUBAHAN:
                        Tombol "Simpan" sekarang selalu aktif (tidak ada :disabled)
                    --}}
                    <button 
                        type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                        Simpan & Selesai
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection