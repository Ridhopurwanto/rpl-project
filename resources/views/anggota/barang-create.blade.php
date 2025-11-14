@extends('layouts.app')

@section('content')
<div class="w-full min-h-screen bg-slate-100 p-4">

    <div class="w-full max-w-md mx-auto" x-data="{ kategori: 'titip' }">
        <h2 class="text-slate-800 text-2xl font-bold text-center mb-6">Catat Barang Baru</h2>

        <form action="{{ route('anggota.barang.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="bg-slate-800 rounded-xl shadow-lg p-6 space-y-4">
                
                {{-- 1. Kategori (Pilihan Utama) --}}
                <div>
                    <label class="block text-gray-300 font-semibold text-sm mb-1">KATEGORI :</label>
                    <select x-model="kategori" name="kategori" class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border border-gray-300" required>
                        <option value="titip">Barang Titipan</option>
                        <option value="temu">Barang Temuan</option>
                    </select>
                </div>
                
                {{-- 2. Nama Barang (Umum) --}}
                <div>
                    <label for="nama_barang" class="block text-gray-300 font-semibold text-sm mb-1">NAMA BARANG :</label>
                    <input type="text" id="nama_barang" name="nama_barang" placeholder="Contoh: Paket, Kunci, HP, Dompet" class="w-full px-4 py-2 bg-white text-gray-900 rounded-md" required>
                </div>

                {{-- --- FIELD KHUSUS 'TITIPAN' --- --}}
                <div x-show="kategori === 'titip'" style="display: none;" class="space-y-4">
                    <div>
                        <label for="nama_pelapor_titip" class="block text-gray-300 font-semibold text-sm mb-1">NAMA PENITIP :</label>
                        <input type="text" id="nama_pelapor_titip" :name="kategori === 'titip' ? 'nama_pelapor' : ''" placeholder="Contoh: Alfagift, JNE" class="w-full px-4 py-2 bg-white text-gray-900 rounded-md">
                    </div>
                    <div>
                        <label for="tujuan" class="block text-gray-300 font-semibold text-sm mb-1">TUJUAN (PENERIMA) :</Dlabel>
                        <input type="text" id="tujuan" name="tujuan" placeholder="Contoh: Kessya" class="w-full px-4 py-2 bg-white text-gray-900 rounded-md">
                    </div>
                </div>

                {{-- --- FIELD KHUSUS 'TEMUAN' --- --}}
                <div x-show="kategori === 'temu'" style="display: none;" class="space-y-4">
                    <div>
                        <label for="nama_pelapor_temu" class="block text-gray-300 font-semibold text-sm mb-1">NAMA PELAPOR :</label>
                        <input type="text" id="nama_pelapor_temu" :name="kategori === 'temu' ? 'nama_pelapor' : ''" placeholder="Contoh: Zidan, Rei" class="w-full px-4 py-2 bg-white text-gray-900 rounded-md">
                    </div>
                    <div>
                        <label for="lokasi_penemuan" class="block text-gray-300 font-semibold text-sm mb-1">LOKASI PENEMUAN :</label>
                        <input type="text" id="lokasi_penemuan" name="lokasi_penemuan" placeholder="Contoh: Maskam, Audit" class="w-full px-4 py-2 bg-white text-gray-900 rounded-md">
                    </div>
                </div>

                {{-- 3. Foto (Umum) --}}
                <div>
                    <label for="foto" class="block text-gray-300 font-semibold text-sm mb-1">FOTO (Opsional) :</label>
                    <input type="file" id="foto" name="foto" class="w-full text-sm text-gray-300 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-100 file:text-blue-700 hover:file:bg-blue-200">
                </div>

                {{-- 4. Catatan (Umum) --}}
                <div>
                    <label for="catatan" class="block text-gray-300 font-semibold text-sm mb-1">CATATAN :</label>
                    <textarea id="catatan" name="catatan" rows="3" placeholder="Contoh: Box, Ayam Geprek, Ganci Kucing" class="w-full px-4 py-2 bg-white text-gray-900 rounded-md"></textarea>
                </div>

            </div>

            <div class="mt-6">
                <button type="submit" class="w-full bg-blue-800 text-white font-bold py-3 px-6 rounded-lg shadow-md hover:bg-blue-900 transition-colors duration-300">
                    SUBMIT
                </button>
            </div>
            
            {{-- Menampilkan error validasi --}}
            @if($errors->any())
                <div class="text-red-500 text-sm mt-4 p-4 bg-red-100 rounded-lg">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </form>
    </div>
</div>
@endsection