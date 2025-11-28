@extends('layouts.app')

@section('header-left')
    <a href="{{ route('anggota.barang.index') }}" class="bg-[#2a4a6f] text-white text-sm font-semibold px-6 py-2 rounded-full shadow-md">
        BARANG
    </a>
@endsection

@section('content')
<div class="w-full min-h-screen bg-slate-100 p-4 pb-32" 
     x-data="{ 
        photoModalOpen: false, 
        photos: [],
        currentPhotoIndex: 0,
        touchStartX: 0,
        touchEndX: 0,
        selesaiModalOpen: false,
        selesaiFormAction: '',
        namaPenerima: '',
        tanggalSelesai: '{{ now()->format('Y-m-d') }}',
        waktuSelesai: '{{ now()->format('H:i') }}',
        showCreateModal: false,
     }">


    {{-- 1. BAGIAN BARANG TITIPAN (AKTIF) --}}
    <div class="mb-4" x-data="{ isOpen: true }">
        <div @click="isOpen = !isOpen" class="text-lg font-bold text-slate-700 uppercase cursor-pointer list-none flex items-center select-none">
            <svg class="w-5 h-5 mr-2 transition-transform duration-300 ease-in-out" 
                :class="isOpen ? 'rotate-0' : '-rotate-90'" 
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
            BARANG TITIPAN :
        </div>

        <div x-show="isOpen" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform -translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform -translate-y-2"
            class="mt-2 space-y-3">
            
            @forelse($barang_titipan as $barang)
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                    {{-- Header Card --}}
                    <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] px-4 py-2.5 flex justify-between items-center">
                        <div>
                            <p class="text-xs text-blue-200 font-semibold uppercase">Tanggal</p>
                            <p class="text-white font-bold text-base">{{ $barang->waktu_titip->format('d/m/Y') }}</p>
                        </div>
                        <span class="bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                            TITIPAN
                        </span>
                    </div>

                    {{-- Body Card dengan Foto di Kiri & Info Sejajar --}}
                    <div class="p-4">
                        <div class="flex gap-4 mb-4">
                            {{-- Foto Barang di Kiri --}}
                            <div class="flex-shrink-0">
                                @if($barang->foto)
                                    <div @click="photoModalOpen = true; photos = ['{{ Storage::url($barang->foto) }}']; currentPhotoIndex = 0;" 
                                        class="w-24 h-24 rounded-lg overflow-hidden border-2 border-gray-200 cursor-pointer hover:border-blue-400 transition-colors">
                                        <img src="{{ Storage::url($barang->foto) }}" 
                                            alt="Foto Barang" 
                                            class="w-full h-full object-cover">
                                    </div>
                                @else
                                    <div class="w-24 h-24 rounded-lg bg-gray-100 flex items-center justify-center border-2 border-gray-200">
                                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            {{-- Info Barang di Kanan (Sejajar dengan Foto) --}}
                            <div class="flex-1 flex flex-col justify-center space-y-2">
                                {{-- Nama Barang --}}
                                <div>
                                    <p class="text-xs text-gray-500 font-semibold uppercase">Nama Barang</p>
                                    <p class="text-gray-900 font-bold text-base">{{ $barang->nama_barang }}</p>
                                </div>

                                {{-- Penitip --}}
                                <div>
                                    <p class="text-xs text-gray-500 font-semibold uppercase">Penitip</p>
                                    <p class="text-gray-800 font-semibold">{{ $barang->nama_penitip }}</p>
                                </div>

                                {{-- Tujuan --}}
                                <div>
                                    <p class="text-xs text-gray-500 font-semibold uppercase">Tujuan</p>
                                    <p class="text-gray-800 font-semibold">{{ $barang->tujuan }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Tombol Selesai Full Width --}}
                        <button 
                            @click.prevent="selesaiModalOpen = true; selesaiFormAction = '{{ route('anggota.barang.selesaiTitipan', $barang->id_barang) }}';"
                            class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold py-2.5 px-4 rounded-lg shadow-md transition-all">
                            SELESAI
                        </button>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl shadow-md p-8 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                    </div>
                    <p class="text-gray-500 font-semibold">Tidak ada barang titipan aktif.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- 2. BAGIAN BARANG TEMUAN (AKTIF) --}}
    <div class="mb-4" x-data="{ isOpen: true }">
        <div @click="isOpen = !isOpen" class="text-lg font-bold text-slate-700 uppercase cursor-pointer list-none flex items-center select-none">
            <svg class="w-5 h-5 mr-2 transition-transform duration-300 ease-in-out" 
                :class="isOpen ? 'rotate-0' : '-rotate-90'" 
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
            BARANG TEMUAN :
        </div>

        <div x-show="isOpen" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform -translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform -translate-y-2"
            class="mt-2 space-y-3">

            @forelse($barang_temuan as $barang)
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                    {{-- Header Card --}}
                    <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] px-4 py-2.5 flex justify-between items-center">
                        <div>
                            <p class="text-xs text-blue-200 font-semibold uppercase">Tanggal</p>
                            <p class="text-white font-bold text-base">{{ $barang->waktu_lapor->format('d/m/Y') }}</p>
                        </div>
                        <span class="bg-yellow-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                            TEMUAN
                        </span>
                    </div>

                    {{-- Body Card dengan Foto di Kiri & Info Sejajar --}}
                    <div class="p-4">
                        <div class="flex gap-4 mb-4">
                            {{-- Foto Barang di Kiri --}}
                            <div class="flex-shrink-0">
                                @if($barang->foto)
                                    <div @click="photoModalOpen = true; photos = ['{{ Storage::url($barang->foto) }}']; currentPhotoIndex = 0;" 
                                        class="w-24 h-24 rounded-lg overflow-hidden border-2 border-gray-200 cursor-pointer hover:border-blue-400 transition-colors">
                                        <img src="{{ Storage::url($barang->foto) }}" 
                                            alt="Foto Barang" 
                                            class="w-full h-full object-cover">
                                    </div>
                                @else
                                    <div class="w-24 h-24 rounded-lg bg-gray-100 flex items-center justify-center border-2 border-gray-200">
                                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            {{-- Info Barang di Kanan (Sejajar dengan Foto) --}}
                            <div class="flex-1 flex flex-col justify-center space-y-2">
                                {{-- Nama Barang --}}
                                <div>
                                    <p class="text-xs text-gray-500 font-semibold uppercase">Nama Barang</p>
                                    <p class="text-gray-900 font-bold text-base">{{ $barang->nama_barang }}</p>
                                </div>

                                {{-- Pelapor --}}
                                <div>
                                    <p class="text-xs text-gray-500 font-semibold uppercase">Pelapor</p>
                                    <p class="text-gray-800 font-semibold">{{ $barang->nama_pelapor }}</p>
                                </div>

                                {{-- Lokasi Penemuan --}}
                                <div>
                                    <p class="text-xs text-gray-500 font-semibold uppercase">Lokasi Penemuan</p>
                                    <p class="text-gray-800 font-semibold">{{ $barang->lokasi_penemuan }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Tombol Selesai Full Width --}}
                        <button 
                            @click.prevent="selesaiModalOpen = true; selesaiFormAction = '{{ route('anggota.barang.selesaiTemuan', $barang->id_barang) }}';"
                            class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold py-2.5 px-4 rounded-lg shadow-md transition-all">
                            SELESAI
                        </button>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl shadow-md p-8 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-500 font-semibold">Tidak ada barang temuan aktif.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- 3. RIWAYAT (Layout yang sama) --}}
    <div class="mb-4" x-data="{ isOpen: true, searchQuery: '' }">
        <div @click="isOpen = !isOpen" class="text-lg font-bold text-slate-700 uppercase cursor-pointer list-none flex items-center select-none">
            <svg class="w-5 h-5 mr-2 transition-transform duration-300 ease-in-out" 
                :class="isOpen ? 'rotate-0' : '-rotate-90'" 
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
            RIWAYAT :
        </div>
        
        <div x-show="isOpen" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform -translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform -translate-y-2">

            {{-- Form Filter (Sama seperti sebelumnya) --}}
            <form action="{{ route('anggota.barang.index') }}" method="GET" class="bg-white p-4 rounded-lg shadow-md mt-2 mb-4">
                <div class="mb-4">
                    <label class="text-sm font-bold text-slate-600 uppercase">PENCARIAN (LIVE) :</label>
                    <div class="relative mt-1">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-blue-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </span>
                        <input type="text" 
                            x-model="searchQuery" 
                            placeholder="Ketik nama barang, pelapor, atau penerima..." 
                            class="w-full bg-[#2a4a6f] text-white placeholder-blue-200 px-4 py-2 pl-10 rounded-lg shadow border-none focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                </div>

                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1 w-full">
                        <label class="text-sm font-bold text-slate-600 uppercase">TANGGAL :</label>
                        <input type="date" 
                            name="tanggal" 
                            value="{{ $tanggal_terpilih ?? date('Y-m-d') }}" 
                            onchange="this.form.submit()"
                            class="w-full bg-[#2a4a6f] text-white px-4 py-2 rounded-lg mt-1 shadow border-none focus:outline-none focus:ring-2 focus:ring-blue-400 cursor-pointer"
                            style="color-scheme: dark;">
                    </div>

                    <div class="flex-1 w-full">
                        <label class="text-sm font-bold text-slate-600 uppercase">KATEGORI :</label>
                        <select name="kategori_riwayat" 
                                onchange="this.form.submit()"
                                class="w-full bg-[#2a4a6f] text-white px-4 py-2 rounded-lg mt-1 shadow border-none focus:outline-none focus:ring-2 focus:ring-blue-400 cursor-pointer">
                            <option value="titip" @if($kategori_terpilih == 'titip') selected @endif>Barang Titipan</option>
                            <option value="temu" @if($kategori_terpilih == 'temu') selected @endif>Barang Temuan</option>
                        </select>
                    </div>
                </div>
            </form>

            {{-- Card Riwayat dengan Layout yang Sama --}}
            <div class="space-y-3">
                @forelse($riwayat_barang as $barang)
                    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200"
                        x-show="$el.innerText.toLowerCase().includes(searchQuery.toLowerCase())"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform scale-95"
                        x-transition:enter-end="opacity-100 transform scale-100">
                        
                        {{-- Header Card --}}
                        <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] px-4 py-2.5 flex justify-between items-center">
                            <div>
                                <p class="text-xs text-blue-200 font-semibold uppercase">Nama Barang</p>
                                <p class="text-white font-bold text-base">{{ $barang->nama_barang }}</p>
                            </div>
                            <span class="bg-gray-200 text-gray-700 text-xs font-bold px-3 py-1 rounded-full">
                                SELESAI
                            </span>
                        </div>

                        {{-- Body Card dengan Foto di Kiri & Info Sejajar --}}
                        <div class="p-4 flex gap-4">
                            {{-- Foto Barang di Kiri --}}
                            <div class="flex-shrink-0">
                                @if($barang->foto)
                                    <div @click="
        photoModalOpen = true; 
        photos = [
            '{{ Storage::url($barang->foto) }}'
            @if($barang->foto_penerima)
            , '{{ Storage::url($barang->foto_penerima) }}'
            @endif
        ]; 
        currentPhotoIndex = 0;
    " 
     class="w-24 h-24 rounded-lg overflow-hidden border-2 border-gray-200 cursor-pointer hover:border-blue-400 transition-colors relative">
    <img src="{{ Storage::url($barang->foto) }}" 
         alt="Foto Barang" 
         class="w-full h-full object-cover">
    
    {{-- Badge +1 jika ada 2 foto --}}
    @if($barang->foto_penerima)
    <div class="absolute bottom-1 right-1 bg-blue-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded">
        +1
    </div>
    @endif
</div>

                                @else
                                    <div class="w-24 h-24 rounded-lg bg-gray-100 flex items-center justify-center border-2 border-gray-200">
                                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            {{-- Info Barang di Kanan (Sejajar dengan Foto) --}}
                            <div class="flex-1 flex flex-col justify-center space-y-2">
                                {{-- Pelapor/Penitip --}}
                                <div>
                                    <p class="text-xs text-gray-500 font-semibold uppercase">
                                        @if($barang instanceof \App\Models\BarangTitipan) Penitip @else Pelapor @endif
                                    </p>
                                    <p class="text-gray-800 font-semibold">
                                        @if($barang instanceof \App\Models\BarangTitipan) {{ $barang->nama_penitip }}
                                        @else {{ $barang->nama_pelapor }} @endif
                                    </p>
                                </div>

                                {{-- Penerima --}}
                                <div>
                                    <p class="text-xs text-gray-500 font-semibold uppercase">Penerima</p>
                                    <p class="text-gray-800 font-semibold">{{ $barang->nama_penerima }}</p>
                                </div>

                                {{-- Lokasi/Tujuan --}}
                                <div>
                                    <p class="text-xs text-gray-500 font-semibold uppercase">
                                        @if($barang instanceof \App\Models\BarangTitipan) Tujuan @else Lokasi @endif
                                    </p>
                                    <p class="text-gray-800 font-semibold text-sm">
                                        @if($barang instanceof \App\Models\BarangTitipan) {{ $barang->tujuan }}
                                        @else {{ $barang->lokasi_penemuan}} @endif
                                    </p>
                                </div>
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
                        <p class="text-gray-500 font-semibold">Tidak ada riwayat.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>



    {{-- 4. TOMBOL FAB --}}
    <button @click.prevent="showCreateModal = true" 
            class="fixed bottom-24 right-4 bg-[#2a4a6f] text-white rounded-full w-16 h-16 flex items-center justify-center shadow-lg transform hover:scale-110 transition-transform z-40 cursor-pointer">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
    </button>

    {{-- ================= 5. MODAL CREATE BARANG (POP-UP) ================= --}}
    <div x-show="showCreateModal"
         class="relative z-50" 
         style="display: none;">
        
        {{-- Backdrop --}}
        <div x-show="showCreateModal"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black bg-opacity-75 transition-opacity"></div>

        {{-- Scroll Wrapper --}}
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                
                {{-- Card Modal --}}
                <div x-show="showCreateModal"
                     @click.away="showCreateModal = false"
                     class="relative transform overflow-hidden rounded-xl bg-[#2a4a6f] text-left shadow-xl transition-all sm:my-8 w-full max-w-md p-6"
                     
                     {{-- Logic Kamera & Form State --}}
                     x-data="{
                        state: 'camera', 
                        stream: null,
                        imageBase64: '',
                        kategori: 'temuan', // Default kategori
                        
                        startCamera() {
                            this.state = 'camera';
                            this.imageBase64 = '';
                            if(!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia){
                                alert('Browser tidak support kamera'); return;
                            }
                            navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' }, audio: false })
                            .then(stream => {
                                this.stream = stream;
                                this.$refs.videoFeed.srcObject = stream;
                            })
                            .catch(err => console.error('Error:', err));
                        },

                        stopCamera() {
                            if (this.stream) {
                                this.stream.getTracks().forEach(track => track.stop());
                                this.stream = null;
                            }
                        },

                        takeSnapshot() {
                            const video = this.$refs.videoFeed;
                            const canvas = this.$refs.canvas;
                            canvas.width = video.videoWidth;
                            canvas.height = video.videoHeight;
                            canvas.getContext('2d').drawImage(video, 0, 0);
                            this.imageBase64 = canvas.toDataURL('image/jpeg', 0.8);
                            this.state = 'preview';
                            this.stopCamera();
                        },

                        retakePhoto() {
                            this.startCamera();
                        }
                     }"
                     x-effect="showCreateModal ? startCamera() : stopCamera()"
                >
                    {{-- Tombol Close --}}
                    <div class="flex justify-end mb-4">
                        <button @click="showCreateModal = false" class="text-gray-300 hover:text-white transition-colors focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <form action="{{ route('anggota.barang.store') }}" method="POST">
                        @csrf
                        {{-- Input Hidden --}}
                        <input type="hidden" name="foto_base64" x-model="imageBase64">
                        <input type="hidden" name="kategori" x-model="kategori">

                        {{-- AREA KAMERA --}}
                        <div class="mb-5 rounded-lg overflow-hidden border-2 border-slate-500 bg-black relative aspect-[4/3]">
                            <video x-show="state === 'camera'" x-ref="videoFeed" autoplay playsinline class="w-full h-full object-cover"></video>
                            <img x-show="state === 'preview'" :src="imageBase64" class="w-full h-full object-cover" style="display: none;">
                            <div x-show="state === 'camera' && !stream" class="absolute inset-0 flex items-center justify-center text-white text-xs">Memuat Kamera...</div>
                        </div>
                        <canvas x-ref="canvas" class="hidden"></canvas>

                        {{-- TOMBOL AMBIL FOTO --}}
                        <div class="mb-6">
                            <button type="button" x-show="state === 'camera'" @click="takeSnapshot()" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 rounded shadow">AMBIL FOTO</button>
                            <button type="button" x-show="state === 'preview'" @click="retakePhoto()" style="display: none;" class="w-full bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 rounded shadow">FOTO ULANG</button>
                        </div>

                        {{-- PILIHAN KATEGORI (Tab Style) --}}
                        <div class="flex mb-4 bg-slate-700 rounded-lg p-1">
                            <button type="button" @click="kategori = 'temuan'" 
                                :class="kategori === 'temuan' ? 'bg-blue-500 text-white' : 'text-gray-300 hover:text-white'"
                                class="flex-1 py-2 rounded-md text-sm font-bold transition-colors">
                                TEMUAN
                            </button>
                            <button type="button" @click="kategori = 'titipan'" 
                                :class="kategori === 'titipan' ? 'bg-blue-500 text-white' : 'text-gray-300 hover:text-white'"
                                class="flex-1 py-2 rounded-md text-sm font-bold transition-colors">
                                TITIPAN
                            </button>
                        </div>

                        {{-- FORM FIELDS --}}
                        <div class="grid grid-cols-1 gap-y-4">
                            {{-- === TAMBAHAN BARU: TANGGAL & WAKTU === --}}
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-gray-300 font-semibold text-sm mb-1 uppercase">TANGGAL :</label>
                                    <input 
                                        type="date" 
                                        name="tanggal" 
                                        value="{{ date('Y-m-d') }}" {{-- Default Hari Ini --}}
                                        class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500" 
                                        required>
                                </div>
                                <div>
                                    <label class="block text-gray-300 font-semibold text-sm mb-1 uppercase">WAKTU :</label>
                                    <input 
                                        type="time" 
                                        name="waktu" 
                                        value="{{ date('H:i') }}" {{-- Default Jam Saat Ini --}}
                                        class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500" 
                                        required>
                                </div>
                            </div>
                            
                            {{-- Nama Barang --}}
                            <div>
                                <label class="block text-gray-300 font-semibold text-sm mb-1 uppercase">NAMA BARANG :</label>
                                <input type="text" name="nama_barang" placeholder="Contoh: Kunci, Dompet, Laptop" class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500" required>
                            </div>

                            {{-- Nama Pelapor / Penitip (Dinamis) --}}
                            <div>
                                <label class="block text-gray-300 font-semibold text-sm mb-1 uppercase" x-text="kategori === 'temuan' ? 'NAMA PELAPOR :' : 'NAMA PENITIP :'"></label>
                                <input type="text" name="nama_pelapor" 
                                    :value="kategori === 'temuan' ? '{{ Auth::user()->nama }}' : ''" 
                                    placeholder="Nama Lengkap" class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500" required>
                            </div>

                            {{-- Lokasi / Tujuan (Dinamis) --}}
                            <div>
                                <label class="block text-gray-300 font-semibold text-sm mb-1 uppercase" x-text="kategori === 'temuan' ? 'LOKASI PENEMUAN :' : 'TUJUAN TITIPAN :'"></label>
                                <input type="text" name="lokasi_tujuan" 
                                    :placeholder="kategori === 'temuan' ? 'Contoh: Parkiran Depan' : 'Contoh: Untuk Pak Budi'"
                                    class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500" required>
                            </div>

                            {{-- Catatan --}}
                            <div>
                                <label class="block text-gray-300 font-semibold text-sm mb-1 uppercase">CATATAN :</label>
                                <textarea name="catatan" rows="2" placeholder="Keterangan tambahan..." class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500"></textarea>
                            </div>
                        </div>

                        {{-- TOMBOL SUBMIT --}}
                        <div class="mt-8" x-show="state === 'preview'" style="display: none;">
                            <button type="submit" class="w-full bg-green-600 text-white font-bold py-3 px-6 rounded-lg shadow-md hover:bg-green-700 transition-colors duration-300">
                                SIMPAN DATA
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- 6. MODAL LIHAT FOTO dengan SLIDER --}}
    <div x-show="photoModalOpen" 
        style="display: none;" 
        class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center p-4 z-[60]" 
        x-transition
        @touchstart="touchStartX = $event.changedTouches[0].screenX"
        @touchend="
            touchEndX = $event.changedTouches[0].screenX;
            if (touchStartX - touchEndX > 50 && currentPhotoIndex < photos.length - 1) currentPhotoIndex++;
            if (touchEndX - touchStartX > 50 && currentPhotoIndex > 0) currentPhotoIndex--;
        ">
        
        <div @click.outside="photoModalOpen = false" class="relative max-w-4xl w-full">
            
            {{-- Header Modal --}}
            <div class="flex justify-between items-center mb-4">
                <div class="text-white">
                    <p class="text-sm text-gray-300">Foto <span x-text="currentPhotoIndex + 1"></span> dari <span x-text="photos.length"></span></p>
                    <p class="text-xs text-gray-400 mt-1" x-text="currentPhotoIndex === 0 ? 'Foto Barang' : 'Foto Penerima'"></p>
                </div>
                <button @click="photoModalOpen = false" 
                        class="text-white hover:text-gray-300 text-2xl font-bold bg-gray-800 hover:bg-gray-700 rounded-full w-10 h-10 flex items-center justify-center transition-colors">
                    ×
                </button>
            </div>

            {{-- Image Container --}}
            <div class="relative">
                <img :src="photos[currentPhotoIndex]" 
                    class="w-full h-auto max-h-[70vh] object-contain rounded-lg border-4 border-gray-700">
                
                {{-- Previous Button --}}
                <button x-show="currentPhotoIndex > 0" 
                        @click="currentPhotoIndex--"
                        class="absolute left-4 top-1/2 -translate-y-1/2 bg-black bg-opacity-50 hover:bg-opacity-75 text-white rounded-full w-12 h-12 flex items-center justify-center transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>

                {{-- Next Button --}}
                <button x-show="currentPhotoIndex < photos.length - 1" 
                        @click="currentPhotoIndex++"
                        class="absolute right-4 top-1/2 -translate-y-1/2 bg-black bg-opacity-50 hover:bg-opacity-75 text-white rounded-full w-12 h-12 flex items-center justify-center transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>

            {{-- Indicator Dots --}}
            <div class="flex justify-center gap-2 mt-4" x-show="photos.length > 1">
                <template x-for="(photo, index) in photos" :key="index">
                    <button @click="currentPhotoIndex = index"
                            :class="currentPhotoIndex === index ? 'bg-white w-8' : 'bg-gray-500 w-2'"
                            class="h-2 rounded-full transition-all"></button>
                </template>
            </div>

            {{-- Hint --}}
            <div class="text-center mt-4 text-gray-400 text-xs" x-show="photos.length > 1">
                Swipe atau gunakan tombol panah untuk navigasi
            </div>
        </div>
    </div>


    {{-- 7. MODAL SELESAI (SERAH TERIMA BARANG) --}}
    <div x-show="selesaiModalOpen" 
         class="relative z-50" 
         style="display: none;">
        
        {{-- Backdrop --}}
        <div x-show="selesaiModalOpen"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black bg-opacity-75 transition-opacity"></div>

        {{-- Scroll Wrapper --}}
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                
                {{-- Card Modal Selesai --}}
                <div x-show="selesaiModalOpen"
                     @click.away="selesaiModalOpen = false"
                     class="relative transform overflow-hidden rounded-xl bg-[#2a4a6f] text-left shadow-xl transition-all sm:my-8 w-full max-w-md p-6"
                     
                     {{-- LOGIKA KAMERA KHUSUS MODAL SELESAI --}}
                     x-data="{
                        state: 'camera', 
                        stream: null,
                        imageBase64: '',
                        
                        startCamera() {
                            this.state = 'camera';
                            this.imageBase64 = '';
                            if(!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia){
                                alert('Browser tidak support kamera'); return;
                            }
                            navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' }, audio: false })
                            .then(stream => {
                                this.stream = stream;
                                this.$refs.videoFeedSelesai.srcObject = stream; // Ref berbeda dengan create
                            })
                            .catch(err => console.error('Error:', err));
                        },

                        stopCamera() {
                            if (this.stream) {
                                this.stream.getTracks().forEach(track => track.stop());
                                this.stream = null;
                            }
                        },

                        takeSnapshot() {
                            const video = this.$refs.videoFeedSelesai;
                            const canvas = this.$refs.canvasSelesai;
                            canvas.width = video.videoWidth;
                            canvas.height = video.videoHeight;
                            canvas.getContext('2d').drawImage(video, 0, 0);
                            this.imageBase64 = canvas.toDataURL('image/jpeg', 0.8);
                            this.state = 'preview';
                            this.stopCamera();
                        },

                        retakePhoto() {
                            this.startCamera();
                        }
                     }"
                     {{-- Jalankan kamera saat modal 'selesaiModalOpen' bernilai true --}}
                     x-effect="selesaiModalOpen ? startCamera() : stopCamera()"
                >
                    {{-- Tombol Close --}}
                    <div class="flex justify-end mb-4">
                        <button @click="selesaiModalOpen = false" class="text-gray-300 hover:text-white transition-colors focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <form :action="selesaiFormAction" method="POST">
                        @csrf @method('PUT')
                        <input type="hidden" name="foto_penerima_base64" x-model="imageBase64">

                        <h3 class="text-xl font-bold text-white text-center mb-6 uppercase">BUKTI SERAH TERIMA</h3>

                        {{-- AREA KAMERA (PENERIMA) --}}
                        <div class="mb-5 rounded-lg overflow-hidden border-2 border-slate-500 bg-black relative aspect-[4/3]">
                            <video x-show="state === 'camera'" x-ref="videoFeedSelesai" autoplay playsinline class="w-full h-full object-cover"></video>
                            <img x-show="state === 'preview'" :src="imageBase64" class="w-full h-full object-cover" style="display: none;">
                            <div x-show="state === 'camera' && !stream" class="absolute inset-0 flex items-center justify-center text-white text-xs">Memuat Kamera...</div>
                        </div>
                        <canvas x-ref="canvasSelesai" class="hidden"></canvas>

                        {{-- TOMBOL AMBIL FOTO --}}
                        <div class="mb-6">
                            <button type="button" x-show="state === 'camera'" @click="takeSnapshot()" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 rounded shadow">AMBIL FOTO PENERIMA</button>
                            <button type="button" x-show="state === 'preview'" @click="retakePhoto()" style="display: none;" class="w-full bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 rounded shadow">FOTO ULANG</button>
                        </div>

                        {{-- FORM FIELDS --}}
                        <div class="grid grid-cols-1 gap-y-4">
                            
                            {{-- Tanggal & Waktu --}}
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-gray-300 font-semibold text-sm mb-1 uppercase">TANGGAL :</label>
                                    <input type="date" name="tanggal_ambil" value="{{ date('Y-m-d') }}" class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500" required>
                                </div>
                                <div>
                                    <label class="block text-gray-300 font-semibold text-sm mb-1 uppercase">WAKTU :</label>
                                    <input type="time" name="waktu_ambil" value="{{ date('H:i') }}" class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500" required>
                                </div>
                            </div>

                            {{-- Nama Penerima --}}
                            <div>
                                <label class="block text-gray-300 font-semibold text-sm mb-1 uppercase">NAMA PENERIMA :</label>
                                <input type="text" name="nama_penerima" x-model="namaPenerima" placeholder="Nama orang yang mengambil" class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500" required>
                            </div>
                        </div>

                        {{-- TOMBOL SUBMIT --}}
                        <div class="mt-8" x-show="state === 'preview'" style="display: none;">
                            <button type="submit" class="w-full bg-green-600 text-white font-bold py-3 px-6 rounded-lg shadow-md hover:bg-green-700 transition-colors duration-300">
                                SELESAIKAN & SIMPAN
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Error Handling --}}
@if($errors->any())
<script>
    document.addEventListener('alpine:init', () => { Alpine.store('showCreateModal', true); });
</script>
@endif
@endsection