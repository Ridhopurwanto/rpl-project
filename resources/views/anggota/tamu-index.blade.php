@extends('layouts.app')

@section('header-left')
    <a href="{{ route('anggota.tamu.index') }}" class="bg-[#2a4a6f] text-white text-sm font-semibold px-6 py-2 rounded-full shadow-md">
        TAMU
    </a>
@endsection

@section('content')
<div class="w-full min-h-screen bg-slate-100 p-4 pb-32" x-data="{ showCreateModal: false }">

    {{-- KOTAK FILTER RENTANG TANGGAL (DENGAN VALIDASI & TOOLTIP) --}}
    <div class="bg-white rounded-lg shadow-md p-5 mb-6">
        
        {{-- 
           x-data Disini:
           1. Menginisialisasi start & end dengan data dari controller.
           2. isInvalid: Fungsi pengecekan apakah tanggal mulai > tanggal akhir.
        --}}
        <form action="{{ route('anggota.tamu.index') }}" method="GET" 
              x-data="{ 
                  start: '{{ $startDate }}', 
                  end: '{{ $endDate }}',
                  get isInvalid() {
                      return this.start > this.end;
                  }
              }">
            
            <div class="flex flex-col md:flex-row md:items-end gap-4 relative">
                
                {{-- Input Tanggal Awal --}}
                <div class="flex-1">
                    <label for="start_date" class="block text-sm font-bold text-slate-600 mb-2 uppercase">Dari Tanggal :</label>
                    <input 
                        type="date" 
                        id="start_date"
                        name="start_date"
                        x-model="start" {{-- Binding ke Alpine --}}
                        class="w-full bg-[#2a4a6f] text-white px-4 py-2 rounded-lg shadow border-none focus:outline-none focus:ring-2 focus:ring-blue-400"
                        style="color-scheme: dark;"
                    >
                </div>

                {{-- Input Tanggal Akhir --}}
                <div class="flex-1">
                    <label for="end_date" class="block text-sm font-bold text-slate-600 mb-2 uppercase">Sampai Tanggal :</label>
                    <input 
                        type="date" 
                        id="end_date"
                        name="end_date"
                        x-model="end" {{-- Binding ke Alpine --}}
                        class="w-full bg-[#2a4a6f] text-white px-4 py-2 rounded-lg shadow border-none focus:outline-none focus:ring-2 focus:ring-blue-400"
                        style="color-scheme: dark;"
                    >
                </div>

                {{-- Tombol Filter & Tooltip Wrapper --}}
                <div class="md:mb-[1px] relative"> 
                    
                    {{-- TOOLTIP KUNING --}}
                    {{-- Muncul hanya jika isInvalid bernilai true --}}
                    <div x-show="isInvalid" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         class="absolute bottom-full right-0 mb-2 w-48 bg-yellow-100 border border-yellow-400 text-yellow-800 text-xs font-bold px-3 py-2 rounded shadow-lg z-10 text-center">
                        
                        ⚠️ Tanggal awal tidak boleh melebihi tanggal akhir!
                        
                        {{-- Panah kecil tooltip --}}
                        <div class="absolute top-full right-8 -mt-1 w-2 h-2 bg-yellow-100 border-b border-r border-yellow-400 transform rotate-45"></div>
                    </div>

                    {{-- Tombol Filter --}}
                    {{-- Disabled jika isInvalid --}}
                    <button type="submit" 
                            :disabled="isInvalid"
                            :class="isInvalid ? 'opacity-50 cursor-not-allowed bg-gray-400' : 'bg-blue-600 hover:bg-blue-700'"
                            class="w-full md:w-auto text-white font-bold py-2 px-8 rounded-lg shadow transition-colors duration-200 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        FILTER
                    </button>
                </div>

            </div>
        </form>
    </div>

    {{-- TABEL RIWAYAT --}}
    <div class="bg-white rounded-lg shadow-md p-4 overflow-x-auto">
        <table class="w-full min-w-[600px] text-sm text-left">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th class="py-3 px-4">No</th>
                    <th class="py-3 px-4">Nama</th>
                    <th class="py-3 px-4">Instansi</th>
                    <th class="py-3 px-4">No. Identitas</th>
                    <th class="py-3 px-4">Waktu</th>
                    <th class="py-3 px-4">Tujuan</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($riwayat_tamu as $tamu)
                <tr class="bg-white hover:bg-slate-50 transition-colors">
                    <td class="py-3 px-4 font-medium">{{ $loop->iteration }}.</td>
                    <td class="py-3 px-4 font-medium">{{ $tamu->nama_tamu }}</td>
                    <td class="py-3 px-4">{{ $tamu->instansi }}</td>
                    <td class="py-3 px-4 font-medium">{{ !empty($tamu->no_identitas) ? $tamu->no_identitas : '-' }}</td>
                    <td class="py-3 px-4">
                        <div class="flex flex-col">
                            <span class="font-bold">{{ $tamu->waktu_datang->format('H:i') }}</span>
                            <span class="text-xs text-gray-500">{{ $tamu->waktu_datang->format('d M Y') }}</span>
                        </div>
                    </td>
                    <td class="py-3 px-4">{{ $tamu->tujuan }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-8 px-4 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <p>Tidak ada riwayat tamu pada rentang tanggal ini.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Tombol Aksi Tambah (FAB) --}}
    {{-- MENGGUNAKAN @click untuk membuka modal --}}
    <button @click.prevent="showCreateModal = true" 
            class="fixed bottom-24 right-4 bg-[#2a4a6f] text-white rounded-full w-16 h-16 flex items-center justify-center shadow-lg transform hover:scale-110 transition-transform z-40 cursor-pointer">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
    </button>

    {{-- ================= MODAL CREATE TAMU (PERBAIKAN POSISI X) ================= --}}
    <div x-show="showCreateModal"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
         @click.away="showCreateModal = false"
         style="display: none;"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        {{-- Card Modal --}}
        <div class="w-full max-w-md bg-[#2a4a6f] rounded-xl shadow-lg p-6" @click.stop>
            
            {{-- Header: Tombol Close menggunakan Flexbox agar sejajar dengan input --}}
            <div class="flex justify-end mb-4">
                <button @click="showCreateModal = false" class="text-gray-300 hover:text-white transition-colors focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form action="{{ route('anggota.tamu.store') }}" method="POST">
                @csrf

                {{-- Grid Layout --}}
                <div class="grid grid-cols-3 gap-x-4 gap-y-5">

                    <label for="nama_tamu" class="col-span-1 text-gray-300 font-semibold text-sm self-center whitespace-nowrap">NAMA :</label>
                    <div class="col-span-2">
                        <input 
                            type="text" 
                            id="nama_tamu" 
                            name="nama_tamu" 
                            placeholder="Contoh: Pak Habibullah"
                            class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required>
                    </div>

                    <label for="instansi" class="col-span-1 text-gray-300 font-semibold text-sm self-center whitespace-nowrap">INSTANSI :</label>
                    <div class="col-span-2">
                        <input 
                            type="text" 
                            id="instansi" 
                            name="instansi" 
                            placeholder="Contoh: BPS Pusat"
                            class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required>
                    </div>

                    <label for="no_identitas" class="col-span-1 text-gray-300 font-semibold text-sm self-center whitespace-wrap">No. Identitas (NIK, NIP) :</label>
                    <div class="col-span-2">
                        <input 
                            type="text" 
                            id="no_identitas" 
                            name="no_identitas" 
                            placeholder="Contoh: 6402021212120001"
                            class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required>
                    </div>
                    
                    <label for="tanggal_kunjungan" class="col-span-1 text-gray-300 font-semibold text-sm self-center whitespace-nowrap">TANGGAL :</label>
                    <div class="col-span-2">
                        <div class="relative">
                            <input 
                                type="date" 
                                id="tanggal_kunjungan" 
                                name="tanggal_kunjungan" 
                                value="{{ date('Y-m-d') }}" 
                                class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>                        
                        </div>
                    </div>

                    <label for="jam_kunjungan" class="col-span-1 text-gray-300 font-semibold text-sm self-center whitespace-nowrap">JAM KUNJUNGAN :</label>
                    <div class="col-span-2">
                        <input 
                            type="time" 
                            id="jam_kunjungan" 
                            name="jam_kunjungan" 
                            value="{{ date('H:i') }}" 
                            class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required>
                    </div>

                    <label for="tujuan" class="col-span-1 text-gray-300 font-semibold text-sm self-start whitespace-nowrap">TUJUAN :</label>
                    <div class="col-span-2">
                        <textarea 
                            id="tujuan" 
                            name="tujuan" 
                            rows="3"
                            placeholder="Contoh: Wisuda"
                            class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required></textarea>
                    </div>

                </div>

                <div class="mt-6">
                    <button 
                        type="submit" 
                        class="w-full bg-green-600 text-white font-bold py-3 px-6 rounded-lg shadow-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-blue-700 transition-colors duration-300">
                        SUBMIT
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection