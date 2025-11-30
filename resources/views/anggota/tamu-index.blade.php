@extends('layouts.app')

@section('header-left')
    <a href="{{ route('anggota.tamu.index') }}" class="bg-[#2a4a6f] text-white text-sm font-semibold px-6 py-2 rounded-full shadow-md">
        TAMU
    </a>
@endsection

@section('content')
<div class="w-full min-h-screen bg-slate-100 p-4 pb-32" x-data="{ showCreateModal: false }">

    {{-- KOTAK FILTER RENTANG TANGGAL --}}
    <div class="bg-white rounded-lg shadow-md p-5 mb-6">
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
                        onchange="this.form.submit()"
                        type="date" 
                        id="start_date"
                        name="start_date"
                        x-model="start"
                        class="w-full bg-[#2a4a6f] text-white px-4 py-2 rounded-lg shadow border-none focus:outline-none focus:ring-2 focus:ring-blue-400"
                        style="color-scheme: dark;">
                </div>

                {{-- Input Tanggal Akhir --}}
                <div class="flex-1">
                    <label for="end_date" class="block text-sm font-bold text-slate-600 mb-2 uppercase">Sampai Tanggal :</label>
                    <input 
                        onchange="this.form.submit()"
                        type="date" 
                        id="end_date"
                        name="end_date"
                        x-model="end"
                        class="w-full bg-[#2a4a6f] text-white px-4 py-2 rounded-lg shadow border-none focus:outline-none focus:ring-2 focus:ring-blue-400"
                        style="color-scheme: dark;">
                </div>

                {{-- Tooltip Wrapper --}}
                <div class="md:mb-[1px] relative"> 
                    <div x-show="isInvalid" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         class="absolute bottom-full right-0 mb-2 w-48 bg-yellow-100 border border-yellow-400 text-yellow-800 text-xs font-bold px-3 py-2 rounded shadow-lg z-10 text-center">
                        ⚠️ Tanggal awal tidak boleh melebihi tanggal akhir!
                        <div class="absolute top-full right-8 -mt-1 w-2 h-2 bg-yellow-100 border-b border-r border-yellow-400 transform rotate-45"></div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- CARD LAYOUT RIWAYAT TAMU --}}
    <div class="space-y-3">
        @forelse($riwayat_tamu as $tamu)
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                {{-- Header Card dengan Gradient --}}
                <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] px-4 py-2.5 flex justify-between items-center">
                    <div>
                        <p class="text-xs text-blue-200 font-semibold uppercase">Nama Tamu</p>
                        <p class="text-white font-bold text-base">{{ $tamu->nama_tamu }}</p>
                    </div>
                    
                    {{-- Badge Nomor Urut --}}
                    <span class="bg-white text-[#2a4a6f] text-sm font-bold px-3 py-1 rounded-full">
                        {{ $loop->iteration }}
                    </span>
                </div>

                {{-- Body Card --}}
                <div class="p-4 space-y-3">
                    {{-- Instansi --}}
                    <div class="flex items-center gap-3 pb-3 border-b border-gray-100">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-[10px] text-gray-500 font-semibold uppercase">Instansi</p>
                            <p class="text-gray-800 font-bold text-base">{{ $tamu->instansi }}</p>
                        </div>
                    </div>

                    {{-- Info Grid 2 Kolom --}}
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <p class="text-gray-500 text-xs uppercase mb-1">No. Identitas</p>
                            <p class="text-gray-800 font-semibold">{{ !empty($tamu->no_identitas) ? $tamu->no_identitas : '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs uppercase mb-1">Waktu Kunjungan</p>
                            <p class="text-gray-800 font-bold">{{ $tamu->waktu_datang->format('H:i') }}</p>
                            <p class="text-xs text-gray-500">{{ $tamu->waktu_datang->format('d M Y') }}</p>
                        </div>
                    </div>

                    {{-- Tujuan --}}
                    <div class="pt-2 border-t border-gray-100">
                        <p class="text-gray-500 text-xs uppercase mb-1">Tujuan Kunjungan</p>
                        <p class="text-gray-800 text-sm leading-relaxed">{{ $tamu->tujuan }}</p>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-md p-8 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <p class="text-gray-500 font-semibold">Tidak ada riwayat tamu pada rentang tanggal ini.</p>
            </div>
        @endforelse
    </div>

    {{-- Tombol FAB Tambah --}}
    <button @click.prevent="showCreateModal = true" 
            class="fixed bottom-24 right-4 bg-[#2a4a6f] text-white rounded-full w-16 h-16 flex items-center justify-center shadow-lg transform hover:scale-110 transition-transform z-40 cursor-pointer">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
    </button>

    {{-- MODAL CREATE TAMU --}}
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

        <div class="w-full max-w-md bg-[#2a4a6f] rounded-xl shadow-lg p-6" @click.stop>
            
            <div class="flex justify-end mb-4">
                <button @click="showCreateModal = false" class="text-gray-300 hover:text-white transition-colors focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form action="{{ route('anggota.tamu.store') }}" method="POST">
                @csrf

                <div class="space-y-4 sm:space-y-5">

                    {{-- NAMA --}}
                    <div class="flex flex-col sm:flex-row gap-1 sm:gap-3">
                        <label for="nama_tamu" class="text-gray-300 font-semibold text-sm sm:w-40 sm:flex-shrink-0 sm:pt-2">
                            NAMA :
                        </label>
                        <div class="sm:flex-1">
                            <input 
                                type="text" 
                                id="nama_tamu" 
                                name="nama_tamu" 
                                placeholder="Contoh: Pak Habibullah"
                                class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500"
                                required>
                        </div>
                    </div>

                    {{-- INSTANSI --}}
                    <div class="flex flex-col sm:flex-row gap-1 sm:gap-3">
                        <label for="instansi" class="text-gray-300 font-semibold text-sm sm:w-40 sm:flex-shrink-0 sm:pt-2">
                            INSTANSI :
                        </label>
                        <div class="sm:flex-1">
                            <input 
                                type="text" 
                                id="instansi" 
                                name="instansi" 
                                placeholder="Contoh: BPS Pusat"
                                class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500"
                                required>
                        </div>
                    </div>

                    {{-- NO. IDENTITAS --}}
                    <div class="flex flex-col sm:flex-row gap-1 sm:gap-3">
                        <label for="no_identitas" class="text-gray-300 font-semibold text-sm sm:w-40 sm:flex-shrink-0 sm:pt-2 leading-tight">
                            No. Identitas (NIK, NIP) :
                        </label>
                        <div class="sm:flex-1">
                            <input 
                                type="text" 
                                id="no_identitas" 
                                name="no_identitas" 
                                placeholder="Contoh: 6402021212120001"
                                class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500"
                                >
                        </div>
                    </div>
                    
                    {{-- TANGGAL --}}
                    <div class="flex flex-col sm:flex-row gap-1 sm:gap-3">
                        <label for="tanggal_kunjungan" class="text-gray-300 font-semibold text-sm sm:w-40 sm:flex-shrink-0 sm:pt-2">
                            TANGGAL :
                        </label>
                        <div class="sm:flex-1">
                            <input 
                                type="date" 
                                id="tanggal_kunjungan" 
                                name="tanggal_kunjungan" 
                                value="{{ date('Y-m-d') }}" 
                                class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500"
                                required>
                        </div>
                    </div>

                    {{-- JAM KUNJUNGAN --}}
                    <div class="flex flex-col sm:flex-row gap-1 sm:gap-3">
                        <label for="jam_kunjungan" class="text-gray-300 font-semibold text-sm sm:w-40 sm:flex-shrink-0 sm:pt-2">
                            JAM KUNJUNGAN :
                        </label>
                        <div class="sm:flex-1">
                            <input 
                                type="time" 
                                id="jam_kunjungan" 
                                name="jam_kunjungan" 
                                value="{{ date('H:i') }}" 
                                class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500"
                                required>
                        </div>
                    </div>

                    {{-- TUJUAN --}}
                    <div class="flex flex-col sm:flex-row gap-1 sm:gap-3">
                        <label for="tujuan" class="text-gray-300 font-semibold text-sm sm:w-40 sm:flex-shrink-0 sm:pt-2">
                            TUJUAN :
                        </label>
                        <div class="sm:flex-1">
                            <textarea 
                                id="tujuan" 
                                name="tujuan" 
                                rows="3"
                                placeholder="Contoh: Wisuda"
                                class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500"
                                required></textarea>
                        </div>
                    </div>

                </div>

                <div class="mt-6 sm:mt-8">
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
