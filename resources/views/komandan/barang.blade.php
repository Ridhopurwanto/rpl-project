@extends('layouts.app')

{{-- Terapkan layout full-width --}}
@section('mobile_width', 'max-w-full')
@section('desktop_width', 'lg:max-w-full')

@section('header-left')
    <a class="bg-slate-800 text-white text-sm font-semibold px-6 py-2 rounded-full shadow-md hover:bg-slate-700 transition">
        BARANG
    </a>
@endsection

@section('content')
<div class="w-full mx-auto">
    
    <h2 class="text-2xl font-bold text-slate-800 mb-4">Laporan Barang</h2>

    {{-- Form Filter --}}
    <form action="{{ route('komandan.barang') }}" method="GET">
        <div class="bg-white p-4 rounded-lg shadow-md mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                {{-- Filter Tanggal --}}
                <div class="sm:col-span-1">
                    <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">TANGGAL:</label>
                    <input type="date" id="tanggal" name="tanggal" 
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                           value="{{ $tanggalTerpilih }}">
                </div>

                {{-- Filter Kategori --}}
                <div class="sm:col-span-1">
                    <label for="kategori" class="block text-sm font-medium text-gray-700 mb-1">KATEGORI:</label>
                    <select id="kategori" name="kategori" 
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="temuan" {{ $kategoriTerpilih == 'temuan' ? 'selected' : '' }}>Barang Temuan</option>
                        <option value="titipan" {{ $kategoriTerpilih == 'titipan' ? 'selected' : '' }}>Barang Titipan</option>
                    </select>
                </div>

                {{-- Filter Jenis (Nama Barang) --}}
                <div class="sm:col-span-1">
                    <label for="jenis" class="block text-sm font-medium text-gray-700 mb-1">JENIS (NAMA BARANG):</label>
                    <input type="text" id="jenis" name="jenis" 
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                           value="{{ $jenisTerpilih }}" placeholder="Cth: Atribut / Dompet">
                </div>

                <button type="submit" class="w-full sm:w-auto sm:self-end bg-blue-600 text-white px-5 py-2 rounded-lg shadow hover:bg-blue-700 transition">
                    Tampilkan
                </button>
            </div>
        </div>
    </form>

    {{-- Tabel Riwayat Barang (Dinamis) --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="bg-gray-100 p-3 border-b border-gray-200">
            <h3 class="font-bold text-gray-800">RIWAYAT ({{ $kategoriTerpilih == 'temuan' ? 'Barang Temuan' : 'Barang Titipan' }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-max">
                
                {{-- Header Tabel dinamis berdasarkan kategori --}}
                @if($kategoriTerpilih == 'temuan')
                    {{-- Header untuk Barang Temuan [cite: 2441] --}}
                    <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500">
                        <tr>
                            <th class="py-3 px-4 text-left">No</th>
                            <th class="py-3 px-4 text-left">Jenis (Nama Barang)</th>
                            <th class="py-3 px-4 text-left">Pelapor</th>
                            <th class="py-3 px-4 text-left">Lokasi Temuan</th>
                            <th class="py-3 px-4 text-left">Catatan</th>
                            <th class="py-3 px-4 text-left">Status</th>
                        </tr>
                    </thead>
                @else
                    {{-- Header untuk Barang Titipan --}}
                    <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500">
                        <tr>
                            <th class="py-3 px-4 text-left">No</th>
                            <th class="py-3 px-4 text-left">Jenis (Nama Barang)</th>
                            <th class="py-3 px-4 text-left">Penitip</th>
                            <th class="py-3 px-4 text-left">Tujuan (Penerima)</th>
                            <th class="py-3 px-4 text-left">Catatan</th>
                            <th class="py-3 px-4 text-left">Status</th>
                        </tr>
                    </thead>
                @endif

                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse($riwayatBarang as $index => $barang)
                        {{-- Body Tabel dinamis --}}
                        @if($kategoriTerpilih == 'temuan')
                            {{-- Data untuk Barang Temuan --}}
                            <tr>
                                <td class="py-2 px-4">{{ $index + 1 }}.</td>
                                <td class="py-2 px-4 font-medium">{{ $barang->nama_barang }}</td>
                                <td class="py-2 px-4">{{ $barang->nama_pelapor }}</td>
                                <td class="py-2 px-4">{{ $barang->lokasi_penemuan }}</td>
                                <td class="py-2 px-4">{{ $barang->catatan }}</td>
                                <td class="py-2 px-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                                        {{ $barang->status == 'belum selesai' ? 'bg-red-200 text-yellow-800' : 'bg-green-200 text-green-800' }}">
                                        {{ $barang->status }}
                                    </span>
                                </td>
                            </tr>
                        @else
                            {{-- Data untuk Barang Titipan --}}
                            <tr>
                                <td class="py-2 px-4">{{ $index + 1 }}.</td>
                                <td class="py-2 px-4 font-medium">{{ $barang->nama_barang }}</td>
                                <td class="py-2 px-4">{{ $barang->nama_penitip }}</td>
                                <td class="py-2 px-4">{{ $barang->tujuan }}</td>
                                <td class="py-2 px-4">{{ $barang->catatan }}</td>
                                <td class="py-2 px-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                                        {{ $barang->status == 'belum selesai' ? 'bg-red-200 text-yellow-800' : 'bg-green-200 text-green-800' }}">
                                        {{ $barang->status }}
                                    </span>
                                </td>
                            </tr>
                        @endif
                    @empty
                    <tr>
                        <td colspan="6" class="py-4 px-4 text-center text-gray-500">
                            Tidak ada data barang pada tanggal dan kategori ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Tidak ada modal karena Komandan/BAU read-only untuk menu ini [cite: 2360, 2363, 2367, 2442] --}}
</div>
@endsection