@extends('layouts.app')

@section('header-left')
    <a href="{{ route('anggota.patroli.index') }}" class="p-2">
        <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
    </a>
@endsection

@section('content')
<div class="w-full min-h-screen bg-slate-100 p-4 pb-32"
     x-data="{
        showModal: false,
        cameraState: 'camera', 
        stream: null,
        imageBase64: '',
        currentArea: '',
        completedList: @js($completedCheckpoints), 
        jenisPatroli: '{{ $jenisPatroliTerpilih }}',
        currentTime: '',

        // --- COMPUTED PROPERTIES (Untuk Logika Tampilan) ---
        get isCompleted() {
            return this.completedList.length >= 17;
        },

        get progressText() {
            return this.completedList.length + ' / 17 AREA SELESAI';
        },

        // --- INIT WAKTU ---
        init() {
            this.updateTime();
            setInterval(() => { this.updateTime() }, 1000);
        },

        updateTime() {
            const now = new Date();
            this.currentTime = now.toLocaleTimeString('id-ID', { 
                hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false 
            }).replace(/\./g, ':') + ' WIB';
        },

        // --- FUNGSI MODAL ---
        openModal(area) {
            this.currentArea = area;
            this.showModal = true;
            this.$nextTick(() => { this.startCamera(); });
        },
        
        closeModal() {
            this.stopCamera();
            this.showModal = false;
        },

        // --- FUNGSI KAMERA ---
        startCamera() {
            this.cameraState = 'camera';
            this.imageBase64 = '';
            
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
                .then(stream => {
                    this.stream = stream;
                    this.$refs.videoFeed.srcObject = stream;
                })
                .catch(err => {
                    console.error(err);
                    alert('Gagal akses kamera: ' + err.message);
                });
            } else {
                alert('Browser tidak support kamera');
            }
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
            this.cameraState = 'preview';
            this.stopCamera();
        },

        retakePhoto() {
            this.startCamera();
        },

        // --- AJAX SUBMIT CHECKPOINT ---
        async submitCheckpoint() {
            try {
                const response = await fetch(`{{ route('anggota.patroli.storeCheckpoint') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        jenis_patroli: this.jenisPatroli,
                        wilayah: this.currentArea,
                        foto_base64: this.imageBase64
                    })
                });

                const result = await response.json();

                if (result.status === 'success') {
                    this.completedList.push(this.currentArea);
                    this.closeModal();
                } else {
                    alert('Gagal: ' + result.message);
                }
            } catch (e) {
                console.error(e);
                alert('Terjadi kesalahan jaringan.');
            }
        }
     }"
     x-init="init()"
>

    {{-- 1. PILIH JENIS PATROLI --}}
    <form action="{{ route('anggota.patroli.createSession') }}" method="GET">
        <div class="flex items-center justify-between mt-4 mb-4">
            <label class="text-sm font-bold text-slate-700 uppercase">JENIS PATROLI</label>
            <select name="jenis_patroli" 
                    onchange="this.form.submit()"
                    class="bg-[#2a4a6f] text-white text-sm font-bold px-4 py-2 rounded-full shadow-md border-0 focus:ring-2 focus:ring-blue-500">
                @foreach($opsiJenisPatroli as $opsi)
                    <option value="{{ $opsi }}" {{ $opsi == $jenisPatroliTerpilih ? 'selected' : '' }}>
                        {{ $opsi }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    {{-- 2. PESAN SUKSES (Muncul jika 17/17) --}}
    {{-- Menggunakan x-show agar responsif tanpa reload --}}
    <div x-show="isCompleted" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform scale-90"
         x-transition:enter-end="opacity-100 transform scale-100"
         class="mb-4 bg-green-100 text-green-700 p-4 rounded-lg shadow-sm">
        <div class="flex items-center">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div>
                <p class="font-bold">Patroli Selesai!</p>
                <p class="text-xs">Seluruh 17 titik area telah berhasil didokumentasikan.</p>
            </div>
        </div>
    </div>

    {{-- 3. GRID AREA (Tetap Muncul Walau Selesai) --}}
    <div class="mt-2">
        <h3 class="text-xs font-bold text-gray-500 uppercase mb-3">DAFTAR AREA CHECKPOINT :</h3>
        
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
            @foreach($semuaArea as $area)
                <button 
                    type="button"
                    {{-- Disable jika sudah ada di completedList --}}
                    :disabled="completedList.includes('{{ strtoupper($area) }}')"
                    @click="openModal('{{ strtoupper($area) }}')"
                    class="p-2 rounded-lg text-xs font-bold shadow transition-all relative h-20 flex items-center justify-center text-center break-words leading-tight border-2"
                    :class="completedList.includes('{{ strtoupper($area) }}') 
                        ? 'bg-slate-800 text-white border-slate-800 cursor-default opacity-90' 
                        : 'bg-white text-slate-700 border-slate-200 hover:border-blue-400 hover:text-blue-600'"
                >
                    {{ $area }}
                    
                    {{-- Icon Centang --}}
                    <div x-show="completedList.includes('{{ strtoupper($area) }}')" class="absolute -top-2 -right-2 bg-green-500 text-white rounded-full p-1 border-2 border-white shadow-sm">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                </button>
            @endforeach
        </div>
    </div>

    {{-- 4. INDIKATOR PROGRESS (Pengganti Tombol Submit) --}}
    <div class="mt-8 fixed bottom-20 left-0 right-0 px-4 md:static">
        <div 
            class="w-full p-4 rounded-lg shadow-lg font-bold text-lg text-center transition-all duration-500 flex items-center justify-center gap-2 border-2"
            :class="isCompleted 
                ? 'bg-green-600 text-white border-green-700' 
                : 'bg-white text-slate-600 border-slate-300'"
        >
            {{-- Ikon berubah sesuai status --}}
            <template x-if="isCompleted">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </template>
            <template x-if="!isCompleted">
                <svg class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </template>

            <span x-text="progressText"></span>
        </div>
    </div>

    {{-- ================= MODAL KAMERA (POP-UP) ================= --}}
    <div x-show="showModal" class="relative z-50" style="display: none;">
        <div x-show="showModal"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black bg-opacity-75 transition-opacity"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                
                {{-- Card Modal (Blue Theme) --}}
                <div x-show="showModal"
                     @click.away="closeModal()"
                     class="relative transform overflow-hidden rounded-xl bg-[#2a4a6f] text-left shadow-2xl transition-all w-full max-w-md p-6">
                    
                    {{-- Header Modal --}}
                    <div class="flex flex-col items-center mb-5 space-y-2">
                        <h3 class="text-white text-xl font-bold uppercase tracking-wide text-center leading-tight" x-text="currentArea"></h3>
                        
                        {{-- Jam Digital Badge Style --}}
                        <div class="inline-flex items-center gap-2 bg-black/30 border border-white/10 rounded-full px-4 py-1.5 backdrop-blur-sm shadow-sm">
                            <svg class="w-4 h-4 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="text-lg font-mono font-bold text-white tracking-widest" x-text="currentTime"></span>
                        </div>
                    </div>

                    {{-- Area Video (4:3 Aspect Ratio) --}}
                    <div class="mb-5 rounded-lg overflow-hidden border-2 border-white/20 bg-black relative aspect-[4/3] shadow-lg">
                        <video x-ref="videoFeed" x-show="cameraState === 'camera'" autoplay playsinline class="w-full h-full object-cover"></video>
                        <img :src="imageBase64" x-show="cameraState === 'preview'" class="w-full h-full object-cover" style="display: none;">
                        
                        <div x-show="cameraState === 'camera' && !stream" class="absolute inset-0 flex items-center justify-center text-white text-xs">
                            Memuat Kamera...
                        </div>
                    </div>
                    <canvas x-ref="canvas" class="hidden"></canvas>

                    {{-- Tombol Aksi --}}
                    <div class="space-y-3">
                        <button type="button" 
                                x-show="cameraState === 'camera'" 
                                @click="takeSnapshot()" 
                                class="w-full bg-white text-[#2a4a6f] font-bold py-3 rounded-lg shadow hover:bg-gray-100 transition-colors">
                            AMBIL FOTO
                        </button>
                        
                        <div x-show="cameraState === 'preview'" class="grid grid-cols-2 gap-3" style="display: none;">
                            <button type="button" @click="retakePhoto()" class="bg-slate-500 text-white font-bold py-3 rounded-lg shadow hover:bg-slate-600 transition-colors">
                                ULANG
                            </button>
                            <button type="button" @click="submitCheckpoint()" class="bg-green-500 text-white font-bold py-3 rounded-lg shadow hover:bg-green-600 transition-colors">
                                SIMPAN
                            </button>
                        </div>
                        
                        <button type="button" @click="closeModal()" class="w-full text-blue-200 text-sm hover:text-white mt-2 underline">
                            Batal
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>
@endsection