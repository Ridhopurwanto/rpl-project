@extends('layouts.app')

@section('content')
<div class="w-full min-h-screen bg-gray-100 flex flex-col items-center py-8 px-4"
     x-data="{
        kategoriBarang: 'temuan', 
        cameraState: 'idle', 
        stream: null,
        imageBase64: '',
        isCameraReady: false, 
        
        tanggalLapor: '{{ \Carbon\Carbon::now()->format('Y-m-d') }}',
        namaPelapor: '{{ auth()->user()->nama ?? '' }}', 
        jenisBarang: '', 
        lokasiPenemuan: '', 
        tujuanTitipan: '', 
        catatan: '',
        
        startCamera() {
            this.cameraState = 'camera'; // Langsung ubah state untuk tampilkan video feed
            this.imageBase64 = '';
            this.isCameraReady = false;
            
            navigator.mediaDevices.getUserMedia({ 
                video: { facingMode: 'environment' }, 
                audio: false 
            })
            .then(stream => {
                this.stream = stream;
                let video = this.$refs.cameraFeed;
                
                if (video) {
                    video.srcObject = stream;
                    video.oncanplay = () => {
                        video.play();
                        this.isCameraReady = true;
                    };
                } else {
                    console.error('Video ref (cameraFeed) not found.');
                    this.cameraState = 'idle'; // Gagal, kembali ke idle
                }
            })
            .catch(err => {
                console.error('Error accessing camera:', err);
                // Jika user menolak izin, state akan 'idle'
                this.cameraState = 'idle'; 
            });
        },
        
        takeSnapshot() {
            const video = this.$refs.cameraFeed;
            const canvas = this.$refs.cameraCanvas;
            
            if (!video || !canvas || video.videoWidth === 0 || video.videoHeight === 0) {
                alert('Kamera belum siap. Mohon tunggu sesaat.');
                return;
            }
            
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            this.imageBase64 = canvas.toDataURL('image/jpeg', 0.8);
            this.cameraState = 'preview';
            this.stopCamera(); 
        },
        
        retakePhoto() {
            this.imageBase64 = '';
            this.startCamera(); 
        },
        
        stopCamera() {
            if (this.stream) {
                this.stream.getTracks().forEach(track => track.stop());
            }
            this.stream = null;
            this.isCameraReady = false;
        }
    }"
    x-init="
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('type') && (urlParams.get('type') === 'titipan' || urlParams.get('type') === 'temuan')) {
            kategoriBarang = urlParams.get('type');
        }
        // Tetap panggil startCamera() otomatis saat load
        $nextTick(() => startCamera());
    "
    @beforeunload.window="stopCamera()">

    {{-- Header --}}
    <div class="w-full max-w-md bg-blue-800 text-white p-4 rounded-t-lg flex justify-between items-center">
        <h2 class="text-xl font-bold">TAMBAH BARANG</h2>
        <div>
            <a href="{{ route('anggota.barang.index') }}" class="text-white hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </a>
        </div>
    </div>

    {{-- Form Konten --}}
    <div class="w-full max-w-md bg-white shadow-lg rounded-b-lg p-6 flex flex-col items-center">
        {{-- Radio Button Kategori Barang --}}
        <div class="flex space-x-4 mb-6">
            <label class="inline-flex items-center">
                <input type="radio" class="form-radio text-blue-600" name="kategori_barang" value="temuan" x-model="kategoriBarang">
                <span class="ml-2 text-gray-700 font-semibold">BARANG TEMUAN</span>
            </label>
            <label class="inline-flex items-center">
                <input type="radio" class="form-radio text-blue-600" name="kategori_barang" value="titipan" x-model="kategoriBarang">
                <span class="ml-2 text-gray-700 font-semibold">BARANG TITIPAN</span>
            </label>
        </div>

        {{-- Area Kamera/Foto --}}
        <div class="w-full bg-gray-200 rounded-lg overflow-hidden mb-6 flex items-center justify-center relative" style="height: 250px;">
            {{-- Video Feed Kamera --}}
            <video x-ref="cameraFeed" class="w-full h-full object-cover" x-show="cameraState === 'camera'" autoplay playsinline></video>
            
            {{-- Preview Foto --}}
            <img :src="imageBase64" x-show="cameraState === 'preview'" class="w-full h-full object-cover" alt="Preview Foto" style="display: none;">

            {{-- 
                PERBAIKAN 1:
                Placeholder 'idle' sekarang @clickable dan memanggil startCamera()
            --}}
            <div x-show="cameraState === 'idle'" 
                 @click="startCamera()" 
                 class="text-gray-500 text-center p-4 cursor-pointer" 
                 style="display: none;">
                <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <p class="mt-2 text-sm font-semibold">Kamera tidak aktif</p>
                <p class="text-xs text-blue-600">Klik di sini untuk mengaktifkan</p>
            </div>

            {{-- Canvas untuk mengambil gambar (tersembunyi) --}}
            <canvas x-ref="cameraCanvas" class="hidden"></canvas>
        </div>

        <div class="w-full mb-6">
            {{-- Tombol "AMBIL GAMBAR" (saat kamera live) --}}
            <button type="button" 
                    x-show="cameraState === 'camera'"
                    @click="takeSnapshot()"
                    :disabled="!isCameraReady"
                    :class="isCameraReady ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-400 cursor-not-allowed'"
                    class="w-full text-white text-md font-bold uppercase px-4 py-3 rounded-md shadow-lg transition duration-150 ease-in-out">
                <span x-show="!isCameraReady">Memuat Kamera...</span>
                <span x-show="isCameraReady">AMBIL GAMBAR</span>
            </button>

            {{-- Tombol "AMBIL ULANG FOTO" (saat preview) --}}
            <button type="button" 
                    x-show="cameraState === 'preview'"
                    @click="retakePhoto()"
                    class="w-full bg-blue-700 text-white text-md font-bold uppercase px-4 py-3 rounded-md shadow-lg hover:bg-blue-800 transition duration-150 ease-in-out" style="display: none;">
                AMBIL ULANG FOTO
            </button>
            
            {{-- 
                PERBAIKAN 2:
                Tombol 'AKTIFKAN KAMERA' (saat 'idle') DIHAPUS 
            --}}
        </div>

        {{-- Form Input --}}
        <form action="{{ route('anggota.barang.store') }}" method="POST" class="w-full space-y-4">
            @csrf
            
            {{-- Controller Anda 'BarangController.php' tidak menangani 'foto_base64', 
                 tetapi 'foto' (sebagai file upload). 
                 Kita harus menyesuaikan controller ATAU view ini.
                 
                 Untuk sekarang, saya akan ganti nama 'foto_base64' menjadi 'foto'
                 dan kita perlu pastikan controller 'store' Anda diubah
                 untuk menangani Base64, BUKAN file upload.
            --}}
            <input type="hidden" name="foto" :value="imageBase64"> {{-- DIUBAH: name="foto" --}}
            <input type="hidden" name="kategori" :value="kategoriBarang">

            {{-- Field Tanggal (Opsional, hapus jika tidak ada di controller store) --}}
            <div>
                <label for="tanggal" class="block text-sm font-semibold text-gray-700 mb-1">TANGGAL:</label>
                <input type="date" id="tanggal" name="tanggal" x-model="tanggalLapor"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 p-2">
            </div>

            {{-- Field Nama Pelapor / Nama Penitip (dinamis) --}}
            <div>
                <label for="nama_pelapor" class="block text-sm font-semibold text-gray-700 mb-1" 
                       x-text="kategoriBarang === 'temuan' ? 'PELAPOR:' : 'PENITIP:'"></label>
                <input type="text" id="nama_pelapor" name="nama_pelapor" x-model="namaPelapor"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 p-2"
                       required>
            </div>

            {{-- Field Nama Barang --}}
            <div>
                <label for="nama_barang" class="block text-sm font-semibold text-gray-700 mb-1">NAMA BARANG:</label>
                 <input type="text" id="nama_barang" name="nama_barang"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 p-2"
                       placeholder="Contoh: Kunci Motor, Dompet, Laptop"
                       required>
            </div>

            {{-- Field Lokasi Penemuan (Text Field) --}}
            <div x-show="kategoriBarang === 'temuan'">
                <label for="lokasi_penemuan" class="block text-sm font-semibold text-gray-700 mb-1">LOKASI:</label>
                <input type="text" id="lokasi_penemuan" name="lokasi_penemuan" x-model="lokasiPenemuan"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 p-2"
                       placeholder="Masukkan lokasi penemuan"
                       :required="kategoriBarang === 'temuan'">
            </div>

            {{-- Field Tujuan (Text Field) --}}
            <div x-show="kategoriBarang === 'titipan'" style="display: none;">
                <label for="tujuan" class="block text-sm font-semibold text-gray-700 mb-1">TUJUAN:</label>
                <input type="text" id="tujuan" name="tujuan" x-model="tujuanTitipan"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 p-2"
                       placeholder="Contoh: Dititipkan ke Pak Budi"
                       :required="kategoriBarang === 'titipan'">
            </div>

            {{-- Field Catatan --}}
            <div>
                <label for="catatan" class="block text-sm font-semibold text-gray-700 mb-1">CATATAN:</label>
                <textarea id="catatan" name="catatan" x-model="catatan" rows="3"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 p-2"></textarea>
            </div>

            {{-- Tombol Submit --}}
            <div class="mt-6">
                <button type="submit" 
                        :disabled="!imageBase64"
                        :class="imageBase64 ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-400 cursor-not-allowed'"
                        class="w-full text-white text-lg font-bold uppercase px-4 py-3 rounded-md shadow-lg transition duration-150 ease-in-out">
                    SUBMIT
                </button>
            </div>
        </form>
    </div>

    {{-- Footer Versi --}}
    <div class="w-full max-w-md bg-blue-800 text-white p-2 text-center rounded-b-lg mt-4">
        Siap v 1.0.0
    </div>

</div>
@endsection