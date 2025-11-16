@extends('layouts.app')

@section('header-left')
    <a href="{{ route('anggota.kendaraan.index') }}" class="bg-[#2a4a6f] text-white text-sm font-semibold px-6 py-2 rounded-full shadow-md">
        KENDARAAN
    </a>
@endsection

@section('content')
{{-- 
  Kita bungkus semua dengan div x-data untuk mengelola state modal.
  layouts.app Anda HARUS memuat Alpine.js agar ini berfungsi.
  Jika belum, tambahkan <script src="//unpkg.com/alpinejs" defer></script> 
  di <head> layout Anda.
--}}
<div class="w-full min-h-screen bg-slate-100 p-4 pb-32" x-data="{ modalOpen: false, selectedVehicleId: null }">

    {{-- 1. BAGIAN KENDARAAN AKTIF --}}
    <details open class="mb-4">
        <summary class="text-lg font-bold text-slate-700 uppercase cursor-pointer list-none flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            KENDARAAN :
        </summary>
        
        {{-- Kontainer tabel dengan shadow & corner radius --}}
        <div class="bg-white rounded-lg shadow-md p-4 mt-2 overflow-x-auto">
            <table class="w-full min-w-[600px] text-sm text-left">
                {{-- Head Sesuai Gambar (No, Nopol, Pemilik, Tipe, Waktu, Ket., Aksi) --}}
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="py-3 px-4">No</th>
                        <th class="py-3 px-4">Nopol</th>
                        <th class="py-3 px-4">Pemilik</th>
                        <th class="py-3 px-4">Tipe</th>
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4">Ket.</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                {{-- Ganti <tbody> Teras tabel KENDARAAN (AKTIF) --}}
                <tbody class="divide-y">
                    @forelse($kendaraan_aktif as $log)
                    <tr class="bg-white hover:bg-gray-50">
                        <td class="py-3 px-4 font-medium">{{ $loop->iteration }}.</td>
                        <td class="py-3 px-4 font-medium">{{ $log->nopol }}</td>
                        <td class="py-3 px-4">{{ $log->pemilik }}</td>
                        <td class="py-3 px-4">{{ $log->tipe }}</td>
                        <td class="py-3 px-4">{{ $log->waktu_masuk->format('H:i:s') }}</td>
                        
                        {{-- --- INI PERUBAHAN UTAMA --- --}}
                        {{-- Kolom Keterangan kini menjadi Form Dropdown --}}
                        <td class="py-3 px-4">
                            <form action="{{ route('anggota.kendaraan.updateKeterangan', ['id_kendaraan_log' => $log->id_log]) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <select 
                                    name="keterangan" 
                                    onchange="this.form.submit()" {{-- Auto-submit saat diganti --}}
                                    class="border border-gray-300 rounded px-2 py-1 text-sm focus:outline-blue-500
                                    {{-- Ganti warna teks berdasarkan pilihan --}}"
                                >
                                    <option value="Tidak Menginap" @if($log->keterangan == 'Tidak Menginap') selected @endif>
                                        Tidak Menginap
                                    </option>
                                    <option value="Menginap" @if($log->keterangan == 'Menginap') selected @endif>
                                        Menginap
                                    </option>
                                </select>
                            </form>
                        </td>
                        
                        <td class="py-3 px-4 text-center">
                            {{-- Tombol Keluar memicu modal --}}
                            <button 
                                @click.prevent="modalOpen = true; selectedVehicleId = '{{ $log->id_log }}'"
                                class="bg-blue-600 text-white text-xs font-bold uppercase px-4 py-2 rounded-md shadow hover:bg-blue-700 transition-colors">
                                Keluar
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-4 px-4 text-center text-gray-500">
                            Tidak ada kendaraan di dalam.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </details>

    {{-- 2. BAGIAN RIWAYAT KENDARAAN --}}
    <details open class="mb-4">
        <summary class="text-lg font-bold text-slate-700 uppercase cursor-pointer list-none flex items-center">
             <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            RIWAYAT :
        </summary>
        
        {{-- Filter Tanggal --}}
        <div class="flex justify-between items-center my-3 px-2">
        <label for="tanggal" class="text-lg font-bold text-slate-700 uppercase">RIWAYAT :</label>
        
        <form action="{{ route('anggota.kendaraan.index') }}" method="GET">
            <input 
                type="date" 
                id="tanggal"
                name="tanggal"
                value="{{ $tanggal_terpilih }}"
                onchange="this.form.submit()" {{-- Auto submit saat tanggal ganti --}}
                class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow border-none focus:outline-none focus:ring-2 focus:ring-blue-400"
            >
        </form>
    </div>

        {{-- Kontainer tabel riwayat --}}
        <div class="bg-white rounded-lg shadow-md p-4 mt-2 overflow-x-auto">
            <table class="w-full min-w-[600px] text-sm text-left">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="py-3 px-4">No</th>
                        <th class="py-3 px-4">Nopol</th>
                        <th class="py-3 px-4">Pemilik</th>
                        <th class="py-3 px-4">Tipe</th>
                        <th class="py-3 px-4">Lama</th>
                        <th class="py-3 px-4">Waktu Keluar</th>
                        <th class="py-3 px-4">Ket.</th>
                    </tr>
                </thead>
                {{-- Ganti <tbody> dari tabel RIWAYAT --}}
                <tbody class="divide-y">
                    @forelse($riwayat_kendaraan as $log)
                    <tr class="bg-white">
                        <td class="py-3 px-4 font-medium">{{ $loop->iteration }}.</td>
                        <td class="py-3 px-4 font-medium">{{ $log->nopol }}</td>
                        <td class="py-3 px-4">{{ $log->pemilik }}</td>
                        <td class="py-3 px-4">{{ $log->tipe }}</td>
                        
                        {{-- Hitung lama parkir --}}
                        <td class="py-3 px-4">
                            {{ $log->waktu_masuk->diffForHumans($log->waktu_keluar, true) }}
                        </td>
                        
                        <td class="py-3 px-4">{{ $log->waktu_keluar->format('H:i:s') }}</td>
                        
                        {{-- --- PERUBAHAN DI SINI --- --}}
                        {{-- Menampilkan 'keterangan' yang sudah tersimpan --}}
                        <td class="py-3 px-4 font-semibold 
                            @if($log->keterangan == 'Menginap') text-red-600 @else text-blue-600 @endif">
                            {{ $log->keterangan }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-4 px-4 text-center text-gray-500">
                            Tidak ada riwayat kendaraan pada tanggal ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>        
            </table>
        </div>
    </details>


    {{-- 3. TOMBOL AKSI TAMBAH (FAB) --}}
    {{-- Ganti href ke route 'anggota.kendaraan.create' --}}
    <a href="{{ route('anggota.kendaraan.create') }}" class="fixed bottom-24 right-4 bg-blue-800 text-white rounded-full w-16 h-16 flex items-center justify-center shadow-lg transform hover:scale-110 transition-transform">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
    </a>


    {{-- 4. MODAL KONFIRMASI KELUAR --}}
    <div 
        x-show="modalOpen" 
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
        style="display: none;" {{-- Mencegah FOUC (Flash of Unstyled Content) --}}
    >
        {{-- Kontainer Modal --}}
        <div 
            @click.outside="modalOpen = false"
            x-show="modalOpen"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-90"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-90"
            class="bg-white rounded-lg shadow-xl w-full max-w-xs"
        >
            {{-- 
                Ini adalah form yang akan di-submit.
                Action-nya dinamis berdasarkan `selectedVehicleId`
                Contoh: /anggota/kendaraan/1/checkout
                Anda perlu membuat route PUT/POST untuk ini.
            --}}
            <form :action="`/anggota/kendaraan/checkout/${selectedVehicleId}`" method="POST">
                @csrf
                @method('PUT') {{-- Atau POST, sesuaikan dengan route Anda --}}

                <div class="p-4 flex flex-col items-center">
                    <p class="text-lg font-semibold text-gray-800 mb-4">Konfirmasi Kendaraan Keluar</p>
                    
                    {{-- Pilihan Menginap / Tidak --}}
                    <div class="w-full">
                        {{-- Kirim 'menginap' = 1 --}}
                        <button 
                            type="submit" name="menginap" value="1"
                            class="w-full text-center py-3 px-4 bg-red-600 text-white font-semibold rounded-md shadow hover:bg-red-700 transition-colors mb-2">
                            MENGINAP
                        </button>
                        
                        {{-- Kirim 'menginap' = 0 --}}
                        <button 
                            type="submit" name="menginap" value="0"
                            class="w-full text-center py-3 px-4 bg-gray-600 text-white font-semibold rounded-md shadow hover:bg-gray-700 transition-colors">
                            TIDAK MENGINAP
                        </button>
                    </div>

                    {{-- Tombol Batal (opsional tapi bagus) --}}
                    <button 
                        type="button" @click.prevent="modalOpen = false"
                        class="w-full text-center py-2 px-4 text-gray-600 hover:text-gray-900 transition-colors mt-3">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection