@extends('layouts.app')

@section('header-left')
    <a href="{{ route('anggota.patroli.index') }}" class="p-2">
        <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
    </a>
@endsection

@section('content')
    <div class="w-full min-h-screen bg-slate-100 p-4 pb-32" x-data="{
                                        showModal: false,
                                        cameraState: 'camera', 
                                        stream: null,
                                        imageBase64: '',
                                        currentArea: '',
                                        completedList: @js($completedCheckpoints), 
                                        jenisPatroli: '{{ $jenisPatroliTerpilih }}',
                                        currentTime: '',
                                        statusPatroli: @js($statusPatroli),

                                        get isCompleted() {
                                            return this.completedList.length >= 17;
                                        },

                                        get progressText() {
                                            return this.completedList.length + ' / 17 AREA SELESAI';
                                        },

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

                                        showAlertModal: false,
                                        alertData: {
                                            area: '',
                                            petugas: '',
                                            waktu: ''
                                        },

                                        // ===== PERBAIKAN: CEK STATUS AREA SEBELUM BUKA MODAL =====
                                        async openModal(area) {
                                            // Cek apakah area sudah dikerjakan orang lain
                                            try {
                                                const response = await fetch(`{{ route('anggota.patroli.checkArea') }}?jenis_patroli=${this.jenisPatroli}&wilayah=${area}`, {
                                                    method: 'GET',
                                                    headers: {
                                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                    }
                                                });

                                                const result = await response.json();

                                                if (result.sudah_ada) {
                                                    // Area sudah dikerjakan oleh orang lain - tampilkan popup cantik
                                                    this.alertData = {
                                                        area: area,
                                                        petugas: result.nama_petugas,
                                                        waktu: result.waktu
                                                    };
                                                    this.showAlertModal = true;

                                                    // Update completedList agar area jadi hijau
                                                    if (!this.completedList.includes(area)) {
                                                        this.completedList.push(area);
                                                    }

                                                    return; // Tidak buka modal
                                                }

                                                // Jika belum dikerjakan, buka modal seperti biasa
                                                this.currentArea = area;
                                                this.showModal = true;
                                                this.$nextTick(() => { this.startCamera(); });

                                            } catch (e) {
                                                console.error(e);
                                                this.showErrorAlert('Gagal mengecek status area.');
                                            }
                                        },

                                        showErrorAlert(message) {
                                            this.alertData = {
                                                area: '',
                                                petugas: '',
                                                waktu: message
                                            };
                                            this.showAlertModal = true;
                                        },

                                        closeModal() {
                                            this.stopCamera();
                                            this.showModal = false;
                                        },

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
                                     }" x-init="init()">

        {{-- PESAN ERROR/SUCCESS --}}
        @if(session('error'))
            <div class="mb-4 bg-red-100 text-red-700 p-4 rounded-lg shadow-sm flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="font-bold">Tidak Bisa Patroli Sekarang</p>
                    <p class="text-xs">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        {{-- 1. INFO SHIFT & PILIH JENIS PATROLI --}}
        <div class="mb-4 bg-white p-4 rounded-lg shadow-sm border-l-4 border-blue-500">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <p class="text-xs text-gray-500 uppercase font-bold">Shift Anda</p>
                    <p class="text-lg font-bold text-gray-800 uppercase">Shift {{ $namaShift }}</p>
                </div>
                <div class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold">
                    AKTIF
                </div>
            </div>
        </div>

        <form action="{{ route('anggota.patroli.createSession') }}" method="GET">
            <div class="flex items-center justify-between mt-4 mb-4">
                <label class="text-sm font-bold text-slate-700 uppercase">JENIS PATROLI</label>
                <select name="jenis_patroli" onchange="this.form.submit()"
                    class="bg-[#2a4a6f] text-white text-sm font-bold px-4 py-2 rounded-full shadow-md border-0 focus:ring-2 focus:ring-blue-500">
                    @foreach($opsiJenisPatroli as $opsi)
                        <option value="{{ $opsi }}" {{ $opsi == $jenisPatroliTerpilih ? 'selected' : '' }}>
                            {{ $opsi }}
                            @if($statusPatroli[$opsi] === 'completed')
                                ✓
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>
        </form>

        {{-- CARD BOX PATROLI DENGAN COUNTDOWN & STATUS --}}
        <div class="mb-6 bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200" x-data="{
        jamMulai: '{{ $jadwalPatroli[$jenisPatroliTerpilih][0] }}',
        jamSelesai: '{{ $jadwalPatroli[$jenisPatroliTerpilih][1] }}',
        countdown: '',
        isTimeReady: false,
        isClaimed: {{ $isClaimed ? 'true' : 'false' }},
        isOwner: {{ $isOwner ? 'true' : 'false' }},
        claimedBy: '{{ $claimedBy ?? '' }}',
        isCompleted: {{ $totalCompleted >= 17 ? 'true' : 'false' }},
        intervalId: null,

        initCountdown() {
            // Cek dulu apakah sudah lewat waktu mulai
            const now = new Date();
            const [jamStr, menitStr] = this.jamMulai.split(':');
            const targetTime = new Date();
            targetTime.setHours(parseInt(jamStr), parseInt(menitStr), 0, 0);

            // Jika sudah lewat waktu, langsung set isTimeReady = true
            if (now >= targetTime) {
                this.isTimeReady = true;
                this.countdown = '';
                return;
            }

            // Jika belum waktunya, jalankan countdown
            this.intervalId = setInterval(() => {
                const now = new Date();
                const [jamStr, menitStr] = this.jamMulai.split(':');
                const targetTime = new Date();
                targetTime.setHours(parseInt(jamStr), parseInt(menitStr), 0, 0);

                const diff = targetTime - now;

                if (diff <= 0) {
                    this.isTimeReady = true;
                    this.countdown = '';
                    clearInterval(this.intervalId); // Stop interval setelah waktu tiba
                    return;
                }

                const minutes = Math.floor(diff / 60000);
                const seconds = Math.floor((diff % 60000) / 1000);
                this.countdown = minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
            }, 1000);
        }
     }" x-init="initCountdown()">


            {{-- Header Card --}}
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-5 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-xs font-semibold uppercase mb-1">Shift {{ $namaShift }}</p>
                        <h2 class="text-white text-xl font-bold">{{ $jenisPatroliTerpilih }}</h2>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm rounded-full px-3 py-1.5 border border-white/30">
                        <p class="text-white text-xs font-bold">
                        <p class="text-white text-xs font-bold">
                            @if($statusPatroli[$jenisPatroliTerpilih] === 'completed')
                                SELESAI
                            @else
                                AKTIF
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            {{-- Body Card --}}
            <div class="p-5 space-y-4">

                {{-- Jadwal Waktu --}}
                <div class="flex items-center gap-3 bg-blue-50 rounded-lg p-3 border border-blue-100">
                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-blue-600 font-semibold uppercase mb-0.5">Jadwal Patroli</p>
                        <p class="text-base font-bold text-blue-900 font-mono tracking-wide">
                            {{ $jadwalPatroli[$jenisPatroliTerpilih][0] }} - {{ $jadwalPatroli[$jenisPatroliTerpilih][1] }}
                            WIB
                        </p>
                    </div>
                </div>

                {{-- Countdown Timer (Jika Belum Waktunya) --}}
                <div x-show="!isTimeReady && countdown !== ''" x-transition
                    class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-12 h-12 bg-yellow-400 rounded-full flex items-center justify-center flex-shrink-0 animate-pulse">
                            <svg class="w-6 h-6 text-yellow-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-yellow-800 mb-1">Patroli Belum Dimulai</p>
                            <p class="text-xs text-yellow-700">Waktu tersisa: <span class="font-mono font-bold text-lg"
                                    x-text="countdown"></span></p>
                        </div>
                    </div>
                </div>

                {{-- Status Patroli --}}
                <div>
                    {{-- Jika Sudah Selesai (17/17) --}}
                    <template x-if="isCompleted && isClaimed">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-start gap-3">
                                <div
                                    class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-bold text-green-800 mb-1">Patroli Sudah Selesai</p>
                                    <p class="text-xs text-green-700">
                                        <span class="font-semibold" x-text="'{{ $jenisPatroliTerpilih }}'"></span> sudah
                                        dilakukan oleh
                                        <span class="font-bold" x-text="isOwner ? 'Anda' : claimedBy"></span>
                                    </p>
                                    <div class="mt-2 bg-white rounded px-2 py-1 inline-block">
                                        <p class="text-xs font-mono font-bold text-green-700">17 / 17 Area ✓</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- Jika Sedang Berjalan (Sudah Claim Tapi Belum Selesai) --}}
                    <template x-if="!isCompleted && isClaimed">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-start gap-3">
                                <div
                                    class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-white animate-pulse" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-bold text-blue-800 mb-1">Patroli Sedang Dilakukan</p>
                                    <p class="text-xs text-blue-700">
                                        <span class="font-semibold" x-text="'{{ $jenisPatroliTerpilih }}'"></span> sedang
                                        dilakukan oleh
                                        <span class="font-bold" x-text="isOwner ? 'Anda' : claimedBy"></span>
                                    </p>
                                    <div class="mt-2 bg-white rounded px-2 py-1 inline-block">
                                        <p class="text-xs font-mono font-bold text-blue-700">{{ $totalCompleted }} / 17 Area
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- Tombol Claim (Jika Belum Ada Yang Claim & Sudah Waktunya) --}}
                    <template x-if="!isClaimed && isTimeReady">
                        <form action="{{ route('anggota.patroli.claim') }}" method="POST">
                            @csrf
                            <input type="hidden" name="jenis_patroli" value="{{ $jenisPatroliTerpilih }}">
                            <button type="submit"
                                class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-4 rounded-lg shadow-lg hover:shadow-xl transition-all transform hover:scale-[1.01] flex items-center justify-center gap-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                <span>Claim Patroli - Mulai Sekarang</span>
                            </button>
                        </form>
                    </template>
                </div>

            </div>
        </div>

        {{-- Alert Session Messages --}}
        @if(session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 rounded-lg p-3">
                <p class="text-sm text-green-800">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-3">
                <p class="text-sm text-red-800">{{ session('error') }}</p>
            </div>
        @endif

        {{-- 3. GRID AREA (Hanya Tampil Jika Sudah Claim) --}}
        @if($isOwner || $totalCompleted >= 17)
            <div class="mt-2" x-data="{ showGrid: false }" x-init="$nextTick(() => { showGrid = true })">
                <h3 class="text-xs font-bold text-gray-500 uppercase mb-3">DAFTAR AREA CHECKPOINT :</h3>

                <div x-show="showGrid" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100" class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach($semuaArea as $area)
                        <button type="button" :disabled="completedList.includes('{{ strtoupper($area) }}')"
                            @click="openModal('{{ strtoupper($area) }}')"
                            class="p-2 rounded-lg text-xs font-bold shadow transition-all relative h-20 flex items-center justify-center text-center break-words leading-tight border-2"
                            :class="completedList.includes('{{ strtoupper($area) }}') 
                                                                                                ? 'bg-slate-800 text-white border-slate-800 cursor-default opacity-90' 
                                                                                                : 'bg-white text-slate-700 border-slate-200 hover:border-blue-400 hover:text-blue-600'">
                            {{ $area }}

                            <div x-show="completedList.includes('{{ strtoupper($area) }}')"
                                class="absolute -top-2 -right-2 bg-green-500 text-white rounded-full p-1 border-2 border-white shadow-sm">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- 4. INDIKATOR PROGRESS (Hanya Tampil Jika Sudah Claim) --}}
            <div class="mt-8 bottom-20 left-0 right-0 px-4 md:static">
                <div class="w-full p-4 rounded-lg shadow-lg font-bold text-lg text-center transition-all duration-500 flex items-center justify-center gap-2 border-2"
                    :class="isCompleted 
                                                                ? 'bg-green-600 text-white border-green-700' 
                                                                : 'bg-white text-slate-600 border-slate-300'">
                    <template x-if="isCompleted">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </template>
                    <template x-if="!isCompleted">
                        <svg class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </template>

                    <span x-text="progressText"></span>
                </div>
            </div>
        @else
            {{-- Pesan Jika Belum Claim atau Locked --}}
            <div class="mt-8 bg-gray-50 border-2 border-dashed border-gray-300 rounded-xl p-8 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                    </path>
                </svg>
                @if($isClaimed)
                    <h3 class="text-lg font-bold text-gray-700 mb-2">Area Tidak Dapat Diakses</h3>
                    <p class="text-sm text-gray-600">{{ $claimedBy }} sedang melakukan patroli ini.</p>
                @else
                    <h3 class="text-lg font-bold text-gray-700 mb-2">Claim Patroli Untuk Memulai</h3>
                    <p class="text-sm text-gray-600">Tekan tombol "Claim Patroli" di atas untuk mulai melakukan patroli.</p>
                @endif
            </div>
        @endif

        {{-- MODAL KAMERA --}}

        {{-- MODAL KAMERA --}}
<div x-show="showModal" class="relative z-50" style="display: none;">
    <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black bg-opacity-75 transition-opacity"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">

            <div x-show="showModal" @click.away="closeModal()"
                class="relative transform overflow-hidden rounded-xl bg-[#2a4a6f] text-left shadow-2xl transition-all w-full max-w-md p-6">

                {{-- Tombol Close (X) di Pojok Kanan Atas --}}
                <button @click="closeModal()" type="button"
                    class="absolute top-4 right-4 z-10 group">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>

                <div class="flex flex-col items-center mb-5 space-y-2">
                    <h3 class="text-white text-xl font-bold uppercase tracking-wide text-center leading-tight"
                        x-text="currentArea"></h3>

                    <div
                        class="inline-flex items-center gap-2 bg-black/30 border border-white/10 rounded-full px-4 py-1.5 backdrop-blur-sm shadow-sm">
                        <svg class="w-4 h-4 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-lg font-mono font-bold text-white tracking-widest"
                            x-text="currentTime"></span>
                    </div>
                </div>

                <div
                    class="mb-5 rounded-lg overflow-hidden border-2 border-white/20 bg-black relative aspect-[4/3] shadow-lg">
                    <video x-ref="videoFeed" x-show="cameraState === 'camera'" autoplay playsinline
                        class="w-full h-full object-cover"></video>
                    <img :src="imageBase64" x-show="cameraState === 'preview'" class="w-full h-full object-cover"
                        style="display: none;">

                    <div x-show="cameraState === 'camera' && !stream"
                        class="absolute inset-0 flex items-center justify-center text-white text-xs">
                        Memuat Kamera...
                    </div>
                </div>
                <canvas x-ref="canvas" class="hidden"></canvas>

                <div class="space-y-3">
                    <button type="button" x-show="cameraState === 'camera'" @click="takeSnapshot()"
                        class="w-full bg-white text-[#2a4a6f] font-bold py-3 rounded-lg shadow hover:bg-gray-100 transition-colors">
                        AMBIL FOTO
                    </button>

                    <div x-show="cameraState === 'preview'" class="grid grid-cols-2 gap-3" style="display: none;">
                        <button type="button" @click="retakePhoto()"
                            class="bg-amber-600 hover:bg-amber-700 text-white font-bold py-3 rounded-lg shadow transition-colors">
                            FOTO ULANG
                        </button>
                        <button type="button" @click="submitCheckpoint()"
                            class="bg-green-500 text-white font-bold py-3 rounded-lg shadow hover:bg-green-600 transition-colors">
                            SIMPAN
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

    </div>
@endsection