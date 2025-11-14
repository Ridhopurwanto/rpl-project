@extends('layouts.app')

@section('content')
<div class="w-full min-h-screen bg-slate-100 p-4 pb-32" 
     x-data="{ 
        photoModalOpen: false, 
        photoModalImage: '',
        selesaiModalOpen: false,
        selesaiFormAction: ''
     }">

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
                        <th class="py-3 px-4">Nama Barang</th> {{-- Diperbarui --}}
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
                        <td class="py-3 px-4">{{ $barang->waktu_lapor->format('d/m/y') }}</td>
                        <td class="py-3 px-4 font-medium">{{ $barang->nama_barang }}</td> {{-- Diperbarui --}}
                        <td class="py-3 px-4">{{ $barang->nama_pelapor }}</td>
                        <td class="py-3 px-4">{{ $barang->tujuan }}</td>
                        <td class="py-3 px-4">{{ $barang->catatan }}</td>
                        <td class="py-3 px-4 text-center">
                            <button 
                                @click.prevent="
                                    selesaiModalOpen = true; 
                                    selesaiFormAction = '{{ route('anggota.barang.selesai', $barang->id_barang) }}'
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
                        <th class="py-3 px-4">Nama Barang</th> {{-- Diperbarui --}}
                        <th class="py-3 px-4">Pelapor</th>
                        <th class="py-3 px-4">Lokasi Penemuan</th> {{-- Diperbarui --}}
                        <th class="py-3 px-4">Catatan</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($barang_temuan as $barang)
                    <tr class="bg-white hover:bg-gray-50">
                        <td class="py-3 px-4">{{ $loop->iteration }}.</td>
                        <td class="py-3 px-4">{{ $barang->waktu_lapor->format('d/m/y') }}</td>
                        <td class="py-3 px-4 font-medium">{{ $barang->nama_barang }}</td> {{-- Diperbarui --}}
                        <td class="py-3 px-4">{{ $barang->nama_pelapor }}</td>
                        <td class="py-3 px-4">{{ $barang->lokasi_penemuan }}</td> {{-- Diperbarui --}}
                        <td class="py-3 px-4">{{ $barang->catatan }}</td>
                        <td class="py-3 px-4 text-center">
                            <button 
                                @click.prevent="
                                    selesaiModalOpen = true; 
                                    selesaiFormAction = '{{ route('anggota.barang.selesai', $barang->id_barang) }}'
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

        <div class="bg-white rounded-lg shadow-md p-4 mt-4 overflow-x-auto">
            <table class="w-full min-w-[700px] text-sm text-left">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="py-3 px-4">No</th>
                        <th class="py-3 px-4">Foto</th>
                        <th class="py-3 px-4">Nama Barang</th> {{-- Diperbarui --}}
                        <th class="py-3 px-4">Pelapor/Penitip</th>
                        <th class="py-3 px-4">Lokasi/Tujuan</th>
                        <th class="py-3 px-4">Catatan</th>
                        <th class="py-3 px-4">Penerima</th>
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
                            @else
                            -
                            @endif
                        </td>
                        <td class="py-3 px-4 font-medium">{{ $barang->nama_barang }}</td> {{-- Diperbarui --}}
                        <td class="py-3 px-4">{{ $barang->nama_pelapor }}</td>
                        {{-- Logika diperbarui --}}
                        <td class="py-3 px-4">{{ $barang->kategori == 'titip' ? $barang->tujuan : $barang->lokasi_penemuan }}</td>
                        <td class="py-3 px-4">{{ $barang->catatan }}</td>
                        <td class="py-3 px-4 font-semibold">{{ $barang->nama_penerima }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="py-4 px-4 text-center text-gray-500">Tidak ada riwayat pada filter ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </details>

    {{-- Tombol Aksi Tambah (FAB) --}}
    <a href="{{ route('anggota.barang.create') }}" class="fixed bottom-24 right-4 bg-blue-800 text-white rounded-full w-16 h-16 flex items-center justify-center shadow-lg transform hover:scale-110 transition-transform">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
    </a>

    {{-- 5. MODAL FOTO (POP-UP) --}}
    <div 
        x-show="photoModalOpen" 
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center p-4 z-50"
        style="display: none;"
    >
        <div 
            @click.outside="photoModalOpen = false"
            class="bg-white rounded-lg shadow-xl w-full max-w-lg p-4 relative"
        >
            <button 
                @click.prevent="photoModalOpen = false" 
                class="absolute -top-4 -right-4 bg-red-600 text-white rounded-full w-10 h-10 flex items-center justify-center shadow-lg text-2xl font-bold">&times;</button>
            <h3 class="text-xl font-bold text-center mb-4">PHOTO</h3>
            <img :src="photoModalImage" alt="Foto Barang" class="w-full h-auto max-h-[70vh] object-contain">
        </div>
    </div>

    {{-- 6. MODAL KONFIRMASI "SELESAI" --}}
    <div 
        x-show="selesaiModalOpen" 
        x-transition
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
        style="display: none;"
    >
        <div 
            @click.outside="selesaiModalOpen = false"
            class="bg-white rounded-lg shadow-xl w-full max-w-sm"
        >
            <form :action="selesaiFormAction" method="POST" class="p-6">
                @csrf
                @method('PUT')
                <h3 class="text-lg font-bold text-gray-800 mb-4">Konfirmasi Pengambilan Barang</h3>
                <p class="text-sm text-gray-600 mb-4">Silakan masukkan nama penerima barang sebelum menyelesaikan.</p>
                <div>
                    <label for="nama_penerima" class="block text-sm font-medium text-gray-700">Nama Penerima</label>
                    <input type="text" name="nama_penerima" id="nama_penerima" required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" @click.prevent="selesaiModalOpen = false" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                        Batal
                    </button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        Simpan & Selesai
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection