@extends('layouts.app')

@section('header-left')
    <a class="flex items-center border-2 border-[#1a2847] text-[#1a2847] text-sm font-bold px-4 py-2 rounded-full">
        GANGGUAN<br class="sm:hidden"> KAMTIBMAS
    </a>
@endsection


@section('content')
<div class="w-full min-h-screen bg-slate-100 p-4 pb-32" 
     x-data="{ 
        showCreateModal: false, 
        showPhotoModal: false, 
        photoUrl: '' 
     }">

    {{-- KOTAK FILTER --}}
    <form action="{{ route('anggota.gangguan.index') }}" method="GET" x-data="{}">
        <div class="bg-white px-6 py-5 rounded-xl shadow-sm mb-6 border border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- Filter Bulan --}}
                <div class="w-full">
                    <label for="bulan" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                        Pilih Bulan
                    </label>
                    <div class="cursor-pointer" @click="$refs.monthInput.showPicker()">
                        <input type="month" id="bulan" name="bulan" x-ref="monthInput"
                               onchange="this.form.submit()"
                               class="block w-full h-[42px] px-4 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer"
                               value="{{ $bulan_terpilih }}">
                    </div>
                </div>

                {{-- Filter Kategori --}}
                <div class="w-full">
                    <label for="kategori" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                        Kategori
                    </label>
                    <div class="relative">
                        <select id="kategori" name="kategori" 
                                onchange="this.form.submit()"
                                class="block w-full h-[42px] px-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer appearance-none">
                            <option value="semua" @if($kategori_terpilih == 'semua') selected @endif>Semua Kategori</option>
                            <option value="Curat" @if($kategori_terpilih == 'Curat') selected @endif>Curat</option>
                            <option value="Curas" @if($kategori_terpilih == 'Curas') selected @endif>Curas</option>
                            <option value="Curanmor" @if($kategori_terpilih == 'Curanmor') selected @endif>Curanmor</option>
                            <option value="Narkoba" @if($kategori_terpilih == 'Narkoba') selected @endif>Narkoba</option>
                            <option value="Laka Lantas" @if($kategori_terpilih == 'Laka Lantas') selected @endif>Laka Lantas</option>
                            <option value="Pembunuhan" @if($kategori_terpilih == 'Pembunuhan') selected @endif>Pembunuhan</option>
                            <option value="Perkelahian" @if($kategori_terpilih == 'Perkelahian') selected @endif>Perkelahian</option>
                            <option value="Mabok" @if($kategori_terpilih == 'Mabok') selected @endif>Mabok</option>
                            <option value="Unjuk Rasa" @if($kategori_terpilih == 'Unjuk Rasa') selected @endif>Unjuk Rasa</option>
                            <option value="Penyerobotan Tanah" @if($kategori_terpilih == 'Penyerobotan Tanah') selected @endif>Penyerobotan Tanah</option>
                            <option value="Kenakalan Remaja" @if($kategori_terpilih == 'Kenakalan Remaja') selected @endif>Kenakalan Remaja</option>
                            <option value="Kebakaran" @if($kategori_terpilih == 'Kebakaran') selected @endif>Kebakaran</option>
                            <option value="Bencana Alam" @if($kategori_terpilih == 'Bencana Alam') selected @endif>Bencana Alam</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- CARD LAYOUT GANGGUAN KAMTIBMAS --}}
    <div class="space-y-3">
        @forelse($laporan_gangguan as $laporan)
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                {{-- Header Card --}}
                <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] px-4 py-2.5 flex justify-between items-center">
                    <div>
                        <p class="text-xs text-blue-200 font-semibold uppercase">Waktu Lapor</p>
                        <p class="text-white font-bold text-base">{{ $laporan->waktu_lapor->format('d/m/Y H:i') }}</p>
                    </div>
                    
                    {{-- Badge Kategori dengan Warna Dinamis --}}
                    <span class="
                        @if($laporan->kategori == 'Curat') bg-red-500
                        @elseif($laporan->kategori == 'Curas') bg-orange-500
                        @elseif($laporan->kategori == 'Curanmor') bg-yellow-500
                        @elseif($laporan->kategori == 'Narkoba') bg-purple-500
                        @elseif($laporan->kategori == 'Laka Lantas') bg-pink-500
                        @elseif($laporan->kategori == 'Pembunuhan') bg-red-700
                        @elseif($laporan->kategori == 'Perkelahian') bg-orange-600
                        @elseif($laporan->kategori == 'Mabok') bg-indigo-500
                        @elseif($laporan->kategori == 'Unjuk Rasa') bg-blue-500
                        @elseif($laporan->kategori == 'Penyerobotan Tanah') bg-green-600
                        @elseif($laporan->kategori == 'Kenakalan Remaja') bg-teal-500
                        @elseif($laporan->kategori == 'Kebakaran') bg-red-600
                        @elseif($laporan->kategori == 'Bencana Alam') bg-gray-600
                        @else bg-gray-500
                        @endif
                        text-white text-xs font-bold px-3 py-1 rounded-full">
                        {{ $laporan->kategori }}
                    </span>
                </div>

                {{-- Body Card dengan Foto di Kiri --}}
                <div class="p-4 flex gap-4">
                    {{-- Foto di Kiri --}}
                    <div class="flex-shrink-0">
                        <div @click="showPhotoModal = true; photoUrl = '{{ Storage::url($laporan->foto) }}'" 
                             class="w-24 h-24 rounded-lg overflow-hidden border-2 border-gray-200 cursor-pointer hover:border-blue-400 transition-colors">
                            <img src="{{ Storage::url($laporan->foto) }}" 
                                 alt="Foto Gangguan" 
                                 class="w-full h-full object-cover">
                        </div>
                    </div>

                    {{-- Info di Kanan --}}
                    <div class="flex-1 space-y-2">
                        {{-- Lokasi --}}
                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase">Lokasi</p>
                            <p class="text-gray-900 font-bold text-base">{{ $laporan->lokasi }}</p>
                        </div>

                        {{-- Keterangan --}}
                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase">Keterangan</p>
                            <p class="text-gray-800 text-sm leading-relaxed">{{ $laporan->deskripsi }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-md p-8 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <p class="text-gray-500 font-semibold">Tidak ada laporan gangguan pada bulan & kategori ini.</p>
            </div>
        @endforelse
    </div>

    {{-- TOMBOL FAB --}}
    <button @click.prevent="showCreateModal = true" 
            class="fixed bottom-24 right-4 bg-[#2a4a6f] text-white rounded-full w-16 h-16 flex items-center justify-center shadow-lg transform hover:scale-110 transition-transform z-40 cursor-pointer">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
    </button>

    {{-- ================= MODAL CREATE LAPORAN (SCROLL HALAMAN / OVERLAY SCROLL) ================= --}}
    <div x-show="showCreateModal"
         class="relative z-50" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true"
         style="display: none;">
        
        {{-- 1. Backdrop Hitam (Fixed, tidak ikut scroll) --}}
        <div x-show="showCreateModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black bg-opacity-75 transition-opacity"></div>

        {{-- 2. Wrapper Scroll (Fixed inset-0 + overflow-y-auto agar layar bisa discroll) --}}
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            {{-- Flex container untuk memusatkan modal secara vertikal & horizontal --}}
            {{-- 'min-h-full' memastikan kita bisa scroll jika konten panjang --}}
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                
                {{-- 3. Card Modal (HAPUS max-h dan overflow-y, biarkan tinggi otomatis) --}}
                <div x-show="showCreateModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     @click.away="showCreateModal = false"
                     class="relative transform overflow-hidden rounded-xl bg-[#2a4a6f] text-left shadow-xl transition-all sm:my-8 w-full max-w-md p-6"
                     
                     {{-- Logika Kamera tetap sama --}}
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

                    {{-- Tombol Close (X) --}}
                    <div class="flex justify-end mb-4">
                        <button @click="showCreateModal = false" class="text-gray-300 hover:text-white transition-colors focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <form action="{{ route('anggota.gangguan.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="foto_base64" x-model="imageBase64">

                        {{-- AREA KAMERA --}}
                        <div class="mb-5 rounded-lg overflow-hidden border-2 border-slate-500 bg-black relative aspect-[4/3]">
                            <video x-show="state === 'camera'" x-ref="videoFeed" autoplay playsinline class="w-full h-full object-cover"></video>
                            <img x-show="state === 'preview'" :src="imageBase64" class="w-full h-full object-cover" style="display: none;">
                            
                            <div x-show="state === 'camera' && !stream" class="absolute inset-0 flex items-center justify-center text-white text-xs">
                                Memuat Kamera...
                            </div>
                        </div>
                        <canvas x-ref="canvas" class="hidden"></canvas>

                        {{-- TOMBOL AMBIL FOTO --}}
                        <div class="mb-6">
                            <button type="button" x-show="state === 'camera'" @click="takeSnapshot()" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 rounded shadow">
                                AMBIL FOTO
                            </button>
                            <button type="button" x-show="state === 'preview'" @click="retakePhoto()" style="display: none;" class="w-full bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 rounded shadow">
                                FOTO ULANG
                            </button>
                        </div>

                        {{-- FORM DATA --}}
                        <div class="grid grid-cols-3 gap-x-4 gap-y-5">
                            
                            <label class="col-span-1 text-gray-300 font-semibold text-sm self-center">TANGGAL :</label>
                            <div class="col-span-2">
                                <input type="date" name="tanggal_lapor" value="{{ date('Y-m-d') }}" class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500" required>
                            </div>

                            <label class="col-span-1 text-gray-300 font-semibold text-sm self-center">WAKTU :</label>
                            <div class="col-span-2">
                                <input type="time" name="waktu_lapor_time" value="{{ date('H:i') }}" class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500" required>
                            </div>

                            <label class="col-span-1 text-gray-300 font-semibold text-sm self-center">KATEGORI :</label>
                            <div class="col-span-2">
                                <select name="kategori" class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500" required>
                                    <option value="Curat">Curat</option>
                                    <option value="Curas">Curas</option>
                                    <option value="Curanmor">Curanmor</option>
                                    <option value="Narkoba">Narkoba</option>
                                    <option value="Laka Lantas">Laka Lantas</option>
                                    <option value="Pembunuhan">Pembunuhan</option>
                                    <option value="Perkelahian">Perkelahian</option>
                                    <option value="Mabok">Mabok</option>
                                    <option value="Unjuk Rasa">Unjuk Rasa</option>
                                    <option value="Penyerobotan Tanah">Penyerobotan Tanah</option>
                                    <option value="Kenakalan Remaja">Kenakalan Remaja</option>
                                    <option value="Kebakaran">Kebakaran</option>
                                    <option value="Bencana Alam">Bencana Alam</option>
                                </select>
                            </div>

                            <label class="col-span-1 text-gray-300 font-semibold text-sm self-center">LOKASI :</label>
                            <div class="col-span-2">
                                <input type="text" name="lokasi" placeholder="Contoh: Jl. Sudirman" class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500" required>
                            </div>

                            <label class="col-span-1 text-gray-300 font-semibold text-sm self-start pt-2">KET :</label>
                            <div class="col-span-2">
                                <textarea name="deskripsi" rows="2" placeholder="Keterangan singkat..." class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border-none focus:ring-2 focus:ring-blue-500" required></textarea>
                            </div>
                        </div>

                        {{-- TOMBOL SUBMIT --}}
                        <div class="mt-8" x-show="state === 'preview'" style="display: none;">
                            <button type="submit" class="w-full bg-green-600 text-white font-bold py-3 px-6 rounded-lg shadow-md hover:bg-green-700 transition-colors duration-300">
                                SUBMIT LAPORAN
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    {{-- ================= MODAL LIHAT FOTO RIWAYAT ================= --}}
    <div x-show="showPhotoModal" 
         class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center p-4 z-[60]"
         style="display: none;"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        
        <div @click.away="showPhotoModal = false" class="relative max-w-3xl w-full">
            <button @click="showPhotoModal = false" class="absolute -top-10 right-0 text-white text-xl font-bold">TUTUP [X]</button>
            <img :src="photoUrl" class="w-full h-auto max-h-[80vh] object-contain rounded-lg border border-gray-600">
        </div>
    </div>

</div>

{{-- Error Handling: Buka modal create jika ada error submit --}}
@if($errors->any())
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('showCreateModal', true);
    });
</script>
@endif

@endsection