@extends('layouts.app')

@section('header-left')
    <a href="{{ route('anggota.presensi.index') }}" class="p-2">
        <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
    </a>
@endsection

@section('content')

{{-- 
  Struktur Alpine.js baru:
  - state: 'camera' (menampilkan video live), 'preview' (menampilkan snapshot)
  - stream: untuk menyimpan objek video stream (agar bisa di-stop)
  - imageBase64: untuk menyimpan data foto yang akan dikirim ke server
--}}
<div x-data="{
        state: 'camera', 
        stream: null,
        imageBase64: '',
        currentTime: '',

        updateTime() {
            const now = new Date();
            this.currentTime = [
                now.getHours().toString().padStart(2, '0'),
                now.getMinutes().toString().padStart(2, '0'),
                now.getSeconds().toString().padStart(2, '0')
            ].join(':');
        },

        init() {
            this.updateTime();
            setInterval(() => { this.updateTime() }, 1000);
        },

        // 1. Fungsi untuk MEMULAI KAMERA
        startCamera() {
            this.state = 'camera';
            this.imageBase64 = ''; // Hapus foto lama
            
            navigator.mediaDevices.getUserMedia({ 
                video: { facingMode: 'user' }, // Minta kamera depan
                audio: false 
            })
            .then(stream => {
                this.stream = stream;
                this.$refs.videoFeed.srcObject = stream;
            })
            .catch(err => {
                console.error('Error accessing camera:', err);
                alert('Tidak bisa mengakses kamera. Pastikan Anda memberi izin.');
            });
        },

        // 2. Fungsi untuk MENGHENTIKAN KAMERA
        stopCamera() {
            if (this.stream) {
                this.stream.getTracks().forEach(track => track.stop());
            }
        },

        // 3. Fungsi untuk MENGAMBIL FOTO (Snapshot)
        takeSnapshot() {
            const video = this.$refs.videoFeed;
            const canvas = this.$refs.canvas;
            

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            
        
            canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
            
            // Ubah gambar di canvas menjadi data Base64
            // Ini adalah data yang akan kita kirim ke server
            this.imageBase64 = canvas.toDataURL('image/jpeg', 0.8);
            
            // Ganti status ke 'preview'
            this.state = 'preview';
            
            // Matikan kamera setelah foto diambil
            this.stopCamera();
        },

        // 4. Fungsi untuk MENGULANG FOTO
        retakePhoto() {
            this.startCamera(); // Nyalakan lagi kameranya
        }
    }"
    {{-- Nyalakan kamera SETELAH DOM siap --}}
    x-init="init(); $nextTick(() => startCamera());"
    {{-- Matikan kamera jika pengguna pindah halaman (PENTING) --}}
    @beforeunload.window="stopCamera()"
>
    
    <form action="{{ route('anggota.presensi.store') }}" method="POST">
        @csrf
        {{-- TODO: Tambahkan input hidden untuk data lokasi (GPS) di sini --}}

        <input type="hidden" name="foto_base64" x-model="imageBase64">

        <div class="w-full h-80 bg-gray-200 rounded-lg shadow-inner overflow-hidden">
            
            <video 
                x-show="state === 'camera'" 
                x-ref="videoFeed" 
                autoplay playsinline 
                class="w-full h-full object-cover"
            ></video>

            <img 
                x-show="state === 'preview'" 
                :src="imageBase64" 
                alt="Preview Foto Presensi" 
                class="w-full h-full object-cover"
                style="display: none;"
            >
        </div>

        <canvas x-ref="canvas" class="hidden"></canvas>


        <div class="flex items-center space-x-2 mt-4">
            <img src="{{ asset('images/logo-siap.png') }}" alt="Logo" class="w-8 h-8">
            <div>
                <p class="text-xs font-semibold text-gray-700">SATUAN KEAMANAN</p>
                <p class="text-xs text-gray-500">POLITEKNIK STATISTIKA STIS</p>
            </div>
        </div>
        <div class="w-full bg-slate-800 text-white rounded-lg p-4 text-center my-4 shadow-lg">
            <p class="text-xs">WAKTU</p>
            <h2 class="text-3xl font-bold" x-text="currentTime + ' WIB'"></h2>
        </div>
        
        <div class="mt-6 space-y-3">
            
            <button 
                x-show="state === 'camera'" 
                type="button" 
                @click="takeSnapshot()" {{-- Panggil fungsi snapshot --}}
                class="w-full flex items-center justify-center text-base py-3 px-4 rounded-lg shadow-md font-medium text-white bg-slate-800 hover:bg-slate-700">
                AMBIL GAMBAR
            </button>

            <div x-show="state === 'preview'" class="space-y-3" style="display: none;">
                <button 
                    type="button" 
                    @click="retakePhoto()" {{-- Panggil fungsi retake --}}
                    class="w-full flex items-center justify-center text-base py-3 px-4 rounded-lg shadow-md font-medium text-white bg-blue-700 hover:bg-blue-800 gap-y-2">
                    AMBIL ULANG FOTO
                </button>
                
                <button 
                    type="submit" {{-- Ini tombol SUBMIT FORM yang sebenarnya --}}
                    class="w-full flex items-center justify-center text-base py-3 px-4 rounded-lg shadow-md font-medium text-white bg-blue-700 hover:bg-blue-800">
                    SUBMIT PRESENSI
                </button>
            </div>
            
        </div>
    </form>
</div>
@endsection