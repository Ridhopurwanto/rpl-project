@extends('layouts.app')

@section('header-left')
    <a href="{{ route('anggota.barang.index') }}" class="bg-[#2a4a6f] text-white text-sm font-semibold px-6 py-2 rounded-full shadow-md">
        BARANG
    </a>
@endsection

@section('content')
<div class="w-full min-h-screen bg-slate-100 p-4 pb-32" 
     x-data="{ 
        {{-- State untuk Modal Foto & Selesai --}}
        photoModalOpen: false, 
        photoModalImage: '',
        selesaiModalOpen: false,
        selesaiFormAction: '',
        namaPenerima: '',
        tanggalSelesai: '{{ now()->format('Y-m-d') }}',
        waktuSelesai: '{{ now()->format('H:i') }}',

        {{-- State untuk Modal Create (Tambah Barang) --}}
        showCreateModal: false,
     }"
>

    {{-- 1. BAGIAN BARANG TITIPAN (AKTIF) --}}
    <details open class="mb-4">
        <summary class="text-lg font-bold text-slate-700 uppercase cursor-pointer list-none flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            BARANG TITIPAN :
        </summary>
        <div class="bg-white rounded-lg shadow-md p-4 mt-2 overflow-x-auto">
            <table class="w-full min-w-[700px] text-sm text-left">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="py-3 px-4">No</th>
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">Nama Barang</th>
                        <th class="py-3 px-4">Penitip</th>
                        <th class="py-3 px-4">Tujuan</th>
                        <th class="py-3 px-4">Foto</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($barang_titipan as $barang)
                    <tr class="bg-white hover:bg-gray-50">
                        <td class="py-3 px-4">{{ $loop->iteration }}.</td>
                        <td class="py-3 px-4">{{ $barang->waktu_titip->format('d/m/y') }}</td>
                        <td class="py-3 px-4 font-medium">{{ $barang->nama_barang }}</td>
                        <td class="py-3 px-4">{{ $barang->nama_penitip }}</td>
                        <td class="py-3 px-4">{{ $barang->tujuan }}</td>
                        <td class="py-3 px-4">
                            @if($barang->foto)
                            <button @click.prevent="photoModalOpen = true; photoModalImage = '{{ Storage::url($barang->foto) }}'" class="text-blue-600 hover:underline text-xs font-bold">Lihat</button>
                            @else - @endif
                        </td>
                        <td class="py-3 px-4 text-center">
                            <button 
                                @click.prevent="selesaiModalOpen = true; selesaiFormAction = '{{ route('anggota.barang.selesaiTitipan', $barang->id_barang) }}';"
                                class="bg-blue-600 text-white text-xs font-bold uppercase px-4 py-2 rounded-md shadow hover:bg-blue-700">
                                Selesai
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="py-4 px-4 text-center text-gray-500">Tidak ada barang titipan aktif.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </details>

    {{-- 2. BAGIAN BARANG TEMUAN (AKTIF) --}}
    <details open class="mb-4">
        <summary class="text-lg font-bold text-slate-700 uppercase cursor-pointer list-none flex items-center">
             <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            BARANG TEMUAN :
        </summary>
        <div class="bg-white rounded-lg shadow-md p-4 mt-2 overflow-x-auto">
            <table class="w-full min-w-[700px] text-sm text-left">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="py-3 px-4">No</th>
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">Nama Barang</th>
                        <th class="py-3 px-4">Pelapor</th>
                        <th class="py-3 px-4">Lokasi</th>
                        <th class="py-3 px-4">Foto</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($barang_temuan as $barang)
                    <tr class="bg-white hover:bg-gray-50">
                        <td class="py-3 px-4">{{ $loop->iteration }}.</td>
                        <td class="py-3 px-4">{{ $barang->waktu_lapor->format('d/m/y') }}</td>
                        <td class="py-3 px-4 font-medium">{{ $barang->nama_barang }}</td>
                        <td class="py-3 px-4">{{ $barang->nama_pelapor }}</td>
                        <td class="py-3 px-4">{{ $barang->lokasi_penemuan }}</td>
                        <td class="py-3 px-4">
                            @if($barang->foto)
                            <button @click.prevent="photoModalOpen = true; photoModalImage = '{{ Storage::url($barang->foto) }}'" class="text-blue-600 hover:underline text-xs font-bold">Lihat</button>
                            @else - @endif
                        </td>
                        <td class="py-3 px-4 text-center">
                            <button 
                                @click.prevent="selesaiModalOpen = true; selesaiFormAction = '{{ route('anggota.barang.selesaiTemuan', $barang->id_barang) }}';"
                                class="bg-blue-600 text-white text-xs font-bold uppercase px-4 py-2 rounded-md shadow hover:bg-blue-700">
                                Selesai
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="py-4 px-4 text-center text-gray-500">Tidak ada barang temuan aktif.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </details>

    {{-- 3. RIWAYAT --}}
    <details open class="mb-4">
        <summary class="text-lg font-bold text-slate-700 uppercase cursor-pointer list-none flex items-center">
             <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            RIWAYAT :
        </summary>
        
        {{-- Form Filter Riwayat --}}
        <form action="{{ route('anggota.barang.index') }}" method="GET" class="bg-white p-4 rounded-lg shadow-md mt-2 mb-4">
            <div class="flex flex-col md:flex-row gap-4 items-end">
                <div class="flex-1 w-full">
                    <label class="text-sm font-bold text-slate-600 uppercase">TANGGAL :</label>
                    <input type="date" name="tanggal" value="{{ $tanggal_terpilih }}" class="w-full bg-[#2a4a6f] text-white px-4 py-2 rounded-lg mt-1 shadow border-none focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div class="flex-1 w-full">
                    <label class="text-sm font-bold text-slate-600 uppercase">KATEGORI :</label>
                    <select name="kategori_riwayat" class="w-full bg-[#2a4a6f] text-white px-4 py-2 rounded-lg mt-1 shadow border-none focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="titip" @if($kategori_terpilih == 'titip') selected @endif>Barang Titipan</option>
                        <option value="temu" @if($kategori_terpilih == 'temu') selected @endif>Barang Temuan</option>
                    </select>
                </div>
                <button type="submit" class="w-full md:w-auto bg-blue-600 text-white font-bold py-2 px-6 rounded-lg shadow hover:bg-blue-700">
                    FILTER
                </button>
            </div>
        </form>

        {{-- Tabel Riwayat --}}
        <div class="bg-white rounded-lg shadow-md p-4 overflow-x-auto">
            <table class="w-full min-w-[700px] text-sm text-left">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="py-3 px-4">No</th>
                        <th class="py-3 px-4">Foto</th>
                        <th class="py-3 px-4">Nama Barang</th>
                        @if($kategori_terpilih == 'titip')
                            <th class="py-3 px-4">Penitip</th>
                            <th class="py-3 px-4">Penerima</th>
                            <th class="py-3 px-4">Tujuan</th>
                        @else 
                            <th class="py-3 px-4">Pelapor</th>
                            <th class="py-3 px-4">Penerima</th>
                            <th class="py-3 px-4">Lokasi Temuan</th>
                        @endif
                        <th class="py-3 px-4">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($riwayat_barang as $barang)
                    <tr class="bg-white">
                        <td class="py-3 px-4">{{ $loop->iteration }}.</td>
                        <td class="py-3 px-4">
                            @if($barang->foto)
                            <button @click.prevent="photoModalOpen = true; photoModalImage = '{{ Storage::url($barang->foto) }}'" class="text-blue-600 hover:underline text-xs font-bold">Lihat</button>
                            @else - @endif
                        </td>
                        <td class="py-3 px-4 font-medium">{{ $barang->nama_barang }}</td>
                        <td class="py-3 px-4">
                            @if($barang instanceof \App\Models\BarangTitipan) {{ $barang->nama_penitip }}
                            @else {{ $barang->nama_pelapor }} @endif
                        </td>
                        <td class="py-3 px-4 font-medium">{{ $barang->nama_penerima }}</td>
                        <td class="py-3 px-4">
                            @if($barang instanceof \App\Models\BarangTitipan) {{ $barang->tujuan }}
                            @else {{ $barang->lokasi_penemuan}} @endif
                        </td>
                        <td class="py-3 px-4 font-medium">{{ $barang->catatan }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="py-4 px-4 text-center text-gray-500">Tidak ada riwayat.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </details>

    {{-- 4. TOMBOL FAB (CREATE BARANG) --}}
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

    {{-- 6. MODAL LIHAT FOTO (Reuse Logic) --}}
    <div x-show="photoModalOpen" style="display: none;" class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center p-4 z-[60]" x-transition>
        <div @click.outside="photoModalOpen = false" class="relative max-w-3xl w-full">
            <button @click="photoModalOpen = false" class="absolute -top-10 right-0 text-white text-xl font-bold">TUTUP [X]</button>
            <img :src="photoModalImage" class="w-full h-auto max-h-[80vh] object-contain rounded-lg border border-gray-600">
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