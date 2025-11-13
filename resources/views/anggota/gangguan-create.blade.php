@extends('layouts.app')

{{-- 1. Header (Tombol Kembali) --}}
@section('header-left')
    {{-- Tombol kembali ini mengarah ke Grid (createSession) --}}
    <a href="{{ route('anggota.gangguan.index') }}" class="p-2">
        <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
    </a>
@endsection

@section('content')
<div class="w-full min-h-screen bg-slate-100 p-4">

    {{-- 
      Bungkus SELURUH HALAMAN dengan x-data dari kode presensi Anda.
      Kita akan gabungkan form ke dalamnya.
    --}}
    <div 
        class="w-full max-w-md mx-auto"
        x-data="{
            state: 'camera', 
            stream: null,
            imageBase64: '',

            // 1. Fungsi untuk MEMULAI KAMERA
            startCamera() {
                this.state = 'camera';
                this.imageBase64 = ''; // Hapus foto lama
                
                navigator.mediaDevices.getUserMedia({ 
                    // Ganti ke 'environment' (kamera belakang)
                    video: { facingMode: 'environment' }, 
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
                
                // Mirror kamera depan (jika pakai 'user'), tapi tidak perlu untuk 'environment'
                // const ctx = canvas.getContext('2d');
                // ctx.translate(canvas.width, 0);
                // ctx.scale(-1, 1);
                // ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                
                // Gambar normal untuk kamera belakang
                canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
                
                this.imageBase64 = canvas.toDataURL('image/jpeg', 0.8);
                this.state = 'preview';
                this.stopCamera();
            },

            // 4. Fungsi untuk MENGULANG FOTO
            retakePhoto() {
                this.startCamera();
            }
        }"
        {{-- Nyalakan kamera SETELAH DOM siap --}}
        x-init="$nextTick(() => startCamera());"
        {{-- Matikan kamera jika pengguna pindah halaman --}}
        @beforeunload.window="stopCamera()"
    >
        
        <form action="{{ route('anggota.gangguan.store') }}" method="POST">
            @csrf
            
            {{-- Input tersembunyi untuk data foto Base64 --}}
            <input type="hidden" name="foto_base64" x-model="imageBase64">

            {{-- 1. AREA KAMERA (dari kode presensi) --}}
            <div class="mb-4 rounded-lg shadow-md overflow-hidden">
                <div class="w-full h-56 bg-gray-900">
                    
                    {{-- Tampilan Video Live --}}
                    <video 
                        x-show="state === 'camera'" 
                        x-ref="videoFeed" 
                        autoplay playsinline 
                        class="w-full h-full object-cover"
                    ></video>

                    {{-- Tampilan Preview Snapshot --}}
                    <img 
                        x-show="state === 'preview'" 
                        :src="imageBase64" 
                        alt="Preview Foto" 
                        class="w-full h-full object-cover"
                        style="display: none;"
                    >
                </div>
            </div>

            {{-- Canvas tersembunyi untuk mengambil snapshot --}}
            <canvas x-ref="canvas" class="hidden"></canvas>

            {{-- 
              Tombol Aksi Kamera (AMBIL GAMBAR / AMBIL ULANG)
              Tombol Submit akan muncul SETELAH foto diambil
            --}}
            
            {{-- Tombol AMBIL GAMBAR (state: 'camera') --}}
            <button 
                x-show="state === 'camera'" 
                type="button" 
                @click="takeSnapshot()"
                class="w-full bg-blue-800 text-white font-bold py-3 px-6 rounded-lg shadow-md hover:bg-blue-900 transition-colors duration-300 mb-4"
            >
                AMBIL GAMBAR
            </button>

            {{-- Tombol AMBIL ULANG (state: 'preview') --}}
            <button 
                x-show="state === 'preview'" 
                type="button" 
                @click="retakePhoto()"
                style="display: none;"
                class="w-full bg-gray-600 text-white font-bold py-3 px-6 rounded-lg shadow-md hover:bg-gray-700 transition-colors duration-300 mb-2"
            >
                AMBIL ULANG FOTO
            </button>


            {{-- 3. KOTAK FORM (dari kode gangguan) --}}
            <div class="w-full bg-slate-800 rounded-xl shadow-lg p-6">
                <div class="grid grid-cols-3 gap-x-4 gap-y-5">

                    <label for="tanggal_lapor" class="col-span-1 text-gray-300 font-semibold text-sm self-center">TANGGAL :</label>
                    <div class="col-span-2">
                        <input type="date" name="tanggal_lapor" value="{{ date('Y-m-d') }}" class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border border-gray-300" required>
                    </div>

                    <label for="waktu_lapor_time" class="col-span-1 text-gray-300 font-semibold text-sm self-center">WAKTU :</label>
                    <div class="col-span-2">
                        <input type="time" name="waktu_lapor_time" value="{{ date('H:i') }}" class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border border-gray-300" required>
                    </div>

                    <label for="kategori" class="col-span-1 text-gray-300 font-semibold text-sm self-center">KATEGORI :</label>
                    <div class="col-span-2">
                        <select name="kategori" class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border border-gray-300" required>
                            <option value="Unjuk Rasa">Unjuk Rasa</option>
                            <option value="Pembakaran Lahan">Pembakaran Lahan</option>
                            <option value="Bentrokan Kepolisian">Bentrokan Kepolisian</option>
                            <option value="Kriminalitas">Kriminalitas</option>
                            <option value="Kecelakaan">Kecelakaan</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <label for="lokasi" class="col-span-1 text-gray-300 font-semibold text-sm self-center">LOKASI :</label>
                    <div class="col-span-2">
                        <input type="text" name="lokasi" placeholder="Lampu Merah Otista" class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border border-gray-300" required>
                    </div>

                    <label for="deskripsi" class="col-span-1 text-gray-300 font-semibold text-sm self-start pt-2">KET :</A></label>
                    <div class="col-span-2">
                        <textarea name="deskripsi" rows="3" placeholder="Massa berangsur reda" class="w-full px-4 py-2 bg-white text-gray-900 rounded-md border border-gray-300"></textarea>
                    </div>

                </div>
            </div>

            {{-- 4. TOMBOL SUBMIT (hanya muncul setelah foto diambil) --}}
            <div class="mt-4" x-show="state === 'preview'" style="display: none;">
                <button 
                    type="submit" 
                    class="w-full bg-blue-800 hover:bg-blue-900 text-white font-bold py-3 px-6 rounded-lg shadow-md transition-colors duration-300">
                    SUBMIT LAPORAN
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