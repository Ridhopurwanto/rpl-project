@extends('layouts.app')

@section('header-left')
    <a href="{{ route('anggota.gangguan.index') }}" class="bg-[#2a4a6f] text-white text-sm font-semibold px-6 py-2 rounded-full shadow-md">
        GANGGUAN KAMTIBMAS
    </a>
@endsection

@section('content')
{{-- 
  MAIN ALPINE DATA:
  1. showCreateModal: Kontrol pop-up form tambah (kamera)
  2. showPhotoModal: Kontrol pop-up lihat foto riwayat
  3. photoUrl: URL foto untuk pop-up riwayat
--}}
<div class="w-full min-h-screen bg-slate-100 p-4 pb-32" 
     x-data="{ 
        showCreateModal: false, 
        showPhotoModal: false, 
        photoUrl: '' 
     }">

    {{-- KOTAK FILTER --}}
    <div class="bg-white rounded-lg shadow-md p-5 mb-6">
        <form action="{{ route('anggota.gangguan.index') }}" method="GET">
            <div class="flex flex-col md:flex-row md:items-end gap-4">
                {{-- Input Bulan --}}
                <div class="flex-1">
                    <label for="bulan" class="block text-sm font-bold text-slate-600 mb-2 uppercase">PILIH BULAN :</label>
                    <input type="month" id="bulan" name="bulan" value="{{ $bulan_terpilih }}"
                        class="w-full bg-[#2a4a6f] text-white px-4 py-2 rounded-lg shadow border-none focus:outline-none focus:ring-2 focus:ring-blue-400"
                        style="color-scheme: dark;">
                </div>
                {{-- Input Kategori --}}
                <div class="flex-1">
                    <label for="kategori" class="block text-sm font-bold text-slate-600 mb-2 uppercase">KATEGORI :</label>
                    <div class="relative">
                        <select id="kategori" name="kategori"
                            class="w-full bg-[#2a4a6f] text-white px-4 py-2 rounded-lg shadow border-none focus:outline-none focus:ring-2 focus:ring-blue-400 appearance-none">
                            <option value="semua" class="bg-white text-gray-900" @if($kategori_terpilih == 'semua') selected @endif>-- Semua Kategori --</option>
                            <option value="Unjuk Rasa" class="bg-white text-gray-900" @if($kategori_terpilih == 'Unjuk Rasa') selected @endif>Unjuk Rasa</option>
                            <option value="Pembakaran Lahan" class="bg-white text-gray-900" @if($kategori_terpilih == 'Pembakaran Lahan') selected @endif>Pembakaran Lahan</option>
                            <option value="Bentrokan Kepolisian" class="bg-white text-gray-900" @if($kategori_terpilih == 'Bentrokan Kepolisian') selected @endif>Bentrokan Kepolisian</option>
                            <option value="Kriminalitas" class="bg-white text-gray-900" @if($kategori_terpilih == 'Kriminalitas') selected @endif>Kriminalitas</option>
                            <option value="Kecelakaan" class="bg-white text-gray-900" @if($kategori_terpilih == 'Kecelakaan') selected @endif>Kecelakaan</option>
                            <option value="Lainnya" class="bg-white text-gray-900" @if($kategori_terpilih == 'Lainnya') selected @endif>Lainnya</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-white">
                            <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                        </div>
                    </div>
                </div>
                {{-- Tombol Filter --}}
                <div class="md:mb-[1px]">
                    <button type="submit" class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-8 rounded-lg shadow transition-colors duration-200 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                        FILTER
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- TABEL RIWAYAT --}}
    <div class="bg-white rounded-lg shadow-md p-4 overflow-x-auto">
        <table class="w-full min-w-[800px] text-sm text-left">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th class="py-3 px-4">No</th>
                    <th class="py-3 px-4">Foto</th>
                    <th class="py-3 px-4">Tanggal</th>
                    <th class="py-3 px-4">Lokasi</th>
                    <th class="py-3 px-4">Kategori</th>
                    <th class="py-3 px-4">Ket</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($laporan_gangguan as $laporan)
                <tr class="bg-white hover:bg-slate-50 transition-colors">
                    <td class="py-3 px-4 font-medium">{{ $loop->iteration }}.</td>
                    <td class="py-3 px-4">
                        <button @click.prevent="showPhotoModal = true; photoUrl = '{{ Storage::url($laporan->foto) }}'" class="flex items-center gap-1 text-blue-600 hover:text-blue-800 font-semibold transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Lihat
                        </button>
                    </td>
                    <td class="py-3 px-4 text-gray-600">{{ $laporan->waktu_lapor->format('d/m/Y H:i') }}</td>
                    <td class="py-3 px-4 text-gray-600">
                            {{ $laporan->kategori }}
                    </td>
                    <td class="py-3 px-4 text-slate-700">{{ $laporan->lokasi }}</td>
                    <td class="py-3 px-4 text-gray-600">{{ Str::limit($laporan->deskripsi, 50) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-8 px-4 text-center text-gray-500">
                        Tidak ada laporan gangguan pada bulan & kategori ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- TOMBOL FAB (Trigger Modal Create) --}}
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
                                    <option value="Unjuk Rasa">Unjuk Rasa</option>
                                    <option value="Pembakaran Lahan">Pembakaran Lahan</option>
                                    <option value="Bentrokan Kepolisian">Bentrokan Kepolisian</option>
                                    <option value="Kriminalitas">Kriminalitas</option>
                                    <option value="Kecelakaan">Kecelakaan</option>
                                    <option value="Lainnya">Lainnya</option>
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