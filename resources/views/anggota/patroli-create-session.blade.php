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
                    idClaim: @js($idClaim),
                    completedList: @js($completedCheckpoints), 
                    jenisPatroli: '{{ $jenisPatroliTerpilih }}',
                    
                    // --- VARIABEL COUNTDOWN ---
                    jamMulai: '{{ $jadwalPatroli[$jenisPatroliTerpilih][0] }}',
                    countdownText: '-- : -- : --',
                    isPending: {{ !empty($patroliPending) && $patroliPending ? 'true' : 'false' }},

                    get isCompleted() {
                        return this.completedList.length >= 17;
                    },
                    get progressText() {
                        return this.completedList.length + ' / 17 AREA SELESAI';
                    },
                    
                    init() {
                        if (this.isPending) {
                            this.startCountdown();
                        }
                    },

                    // --- LOGIC COUNTDOWN ---
                    startCountdown() {
                        const [h, m] = this.jamMulai.split(':');
                        let target = new Date();
                        target.setHours(parseInt(h), parseInt(m), 0, 0);

                        // Fix: Jika target < sekarang (misal target jam 01:00, sekarang jam 23:00), 
                        // berarti targetnya besok. Tambah 1 hari.
                        if (target < new Date()) {
                            target.setDate(target.getDate() + 1);
                        }

                        setInterval(() => {
                            const now = new Date();
                            const diff = target - now;

                            if (diff <= 0) {
                                window.location.reload(); // Reload otomatis saat waktu tiba
                                return;
                            }

                            const hours = Math.floor(diff / (1000 * 60 * 60));
                            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                            const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                            this.countdownText = 
                                (hours < 10 ? '0' + hours : hours) + ' : ' +
                                (minutes < 10 ? '0' + minutes : minutes) + ' : ' +
                                (seconds < 10 ? '0' + seconds : seconds);
                        }, 1000);
                    },

                    showAlertModal: false,
                    alertData: { area: '', petugas: '', waktu: '' },
                    async openModal(area) {
                        @if(!empty($patroliExpired) && $patroliExpired)
                            this.showErrorAlert('Patroli ini sudah terlewat. Silakan melakukan laporan ke Komandan.');
                            return;
                        @endif

                        try {
                            const response = await fetch(`{{ route('anggota.patroli.checkArea') }}?jenis_patroli=${this.jenisPatroli}&wilayah=${area}`, {
                                method: 'GET',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            });
                            const result = await response.json();

                            if (result.sudah_ada) {
                                this.alertData = {
                                    area: area,
                                    petugas: result.nama_petugas,
                                    waktu: result.waktu
                                };
                                this.showAlertModal = true;

                                if (!this.completedList.includes(area)) {
                                    this.completedList.push(area);
                                }
                                return;
                            }

                            this.currentArea = area;
                            this.showModal = true;
                            this.$nextTick(() => { this.startCamera(); });

                        } catch (e) {
                            console.error(e);
                            this.showErrorAlert('Gagal mengecek status area.');
                        }
                    },
                    showErrorAlert(message) {
                        this.alertData = { area: '', petugas: '', waktu: message };
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
                                .then(stream => { this.stream = stream; this.$refs.videoFeed.srcObject = stream; })
                                .catch(err => { alert('Gagal akses kamera: ' + err.message); });
                        } else { alert('Browser tidak support kamera'); }
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
                    retakePhoto() { this.startCamera(); },
                    async submitCheckpoint() {
                        if (!this.idClaim) { alert('Error: ID Sesi hilang. Refresh halaman.'); return; }
                        try {
                            const response = await fetch(`{{ route('anggota.patroli.storeCheckpoint') }}`, {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                body: JSON.stringify({ id_claim: this.idClaim, wilayah: this.currentArea, foto_base64: this.imageBase64 })
                            });
                            window.location.reload(); 
                        } catch (e) { alert('Gagal upload'); }
                    }
                 }" x-init="init()">

        {{-- FLASH ERROR --}}
        @if(session('error'))
            <div class="mb-4 bg-red-100 text-red-700 p-4 rounded-lg shadow-sm flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="font-bold">Info</p>
                    <p class="text-xs">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        {{-- INFO SHIFT --}}
        <div class="mb-4 bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
            <div class="bg-gradient-to-b from-[#243a5e] via-[#2a4a6f] to-[#365c82] px-4 py-3">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-xs font-semibold uppercase mb-1">Shift Anda</p>
                        <p class="text-white text-xl font-bold uppercase">Shift {{ $namaShift }}</p>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm rounded-full px-3 py-1.5 border border-white/30">
                        <p class="text-white text-xs font-bold uppercase">AKTIF</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- PILIH JENIS PATROLI (CLEAN DROPDOWN) --}}
        <form action="{{ route('anggota.patroli.createSession') }}" method="GET">
            <div class="flex items-center justify-between mt-4 mb-4">
                <label class="text-sm font-bold text-slate-700 uppercase">JENIS PATROLI</label>
                <select name="jenis_patroli" onchange="this.form.submit()"
                    class="bg-[#2a4a6f] text-white text-sm font-bold px-4 py-2 rounded-full shadow-md border-0 focus:ring-2 focus:ring-blue-500">
                    @foreach($opsiJenisPatroli as $opsi)
                        <option value="{{ $opsi }}" {{ $opsi == $jenisPatroliTerpilih ? 'selected' : '' }}>
                            {{ $opsi }}
                            {{-- Hanya tampilkan centang jika selesai --}}
                            @if(($statusPatroli[$opsi] ?? '') === 'completed') 
                                ✓
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>
        </form>


        {{-- CARD JADWAL + STATUS --}}
        <div class="mb-6 bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">

            {{-- Header --}}
            <div class="bg-gradient-to-b from-[#243a5e] via-[#2a4a6f] to-[#365c82] px-5 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-xs font-semibold uppercase mb-1">Shift {{ $namaShift }}</p>
                        <h2 class="text-white text-xl font-bold">{{ $jenisPatroliTerpilih }}</h2>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm rounded-full px-3 py-1.5 border border-white/30">
                        <p class="text-white text-xs font-bold">
                            @if(!empty($patroliPending) && $patroliPending)
                                MENUNGGU
                            @elseif(!empty($patroliExpired) && $patroliExpired)
                                TERLEWAT
                            @else
                                AKTIF
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            {{-- Body --}}
            <div class="p-5 space-y-4">
                {{-- Jadwal --}}
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

                {{-- Progress Area (Jika Owner/Claimed) --}}
                @if($isOwner || $isClaimed)
                    <div class="flex items-center gap-3 bg-purple-50 rounded-lg p-3 border border-purple-100">
                        <div class="w-10 h-10 bg-purple-500 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs text-purple-600 font-semibold uppercase mb-0.5">Progress Checkpoint</p>
                            <p class="text-base font-bold text-purple-900 font-mono tracking-wide">
                                <span x-text="completedList.length"></span> / 17 AREA SELESAI
                            </p>
                        </div>
                    </div>
                @endif
                
                {{-- STATUS BLOCK & TOMBOL --}}
                @if(!empty($patroliPending) && $patroliPending)
                    {{-- 1. STATUS PENDING (HITUNG MUNDUR) --}}
                    <div x-transition class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 bg-yellow-400 rounded-full flex items-center justify-center flex-shrink-0 animate-pulse">
                                <svg class="w-6 h-6 text-yellow-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-yellow-800 mb-1">Patroli Dimulai Dalam</p>
                                <p class="text-xs text-yellow-700">
                                    <span class="font-mono font-bold text-lg" x-text="countdownText"></span>
                                </p>
                            </div>
                        </div>
                    </div>

                @elseif(!empty($patroliExpired) && $patroliExpired)
                    {{-- 2. STATUS EXPIRED (SUDAH LEWAT) --}}
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-red-800">Waktu Habis</p>
                                <p class="text-xs text-red-600">Patroli ini sudah tidak dapat diakses.</p>
                            </div>
                        </div>
                    </div>

                @else
                    {{-- 3. STATUS AKTIF (BISA CLAIM / LIHAT PROGRESS) --}}
                    @if($isClaimed && $isOwner)
                            <div class="bg-green-50 rounded-lg p-4 border border-green-200 text-center">
                                <p class="text-sm font-bold text-green-800">Sedang Anda Kerjakan</p>
                            </div>
                    @elseif($isClaimed && !$isOwner)
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 text-center">
                            <p class="text-sm font-bold text-gray-700">Sedang dikerjakan oleh {{ $claimedBy }}</p>
                        </div>

                    @else
                        {{-- Tombol Claim --}}
                        <form action="{{ route('anggota.patroli.claim') }}" method="POST">
                            @csrf
                            <input type="hidden" name="jenis_patroli" value="{{ $jenisPatroliTerpilih }}">
                            <button type="submit"
                                class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-4 rounded-lg shadow-lg hover:shadow-xl transition-all transform hover:scale-[1.01] flex items-center justify-center gap-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                <span>Claim & Mulai Patroli</span>
                            </button>
                        </form>
                    @endif
                @endif
            </div>
        </div>


        {{-- GRID AREA LOGIC --}}
        @if(($isOwner || $totalCompleted >= 17) && (empty($patroliExpired) || !$patroliExpired))
            <div class="mt-2" x-data="{ showGrid: false }" x-init="$nextTick(() => { showGrid = true })">
                <h3 class="text-xs font-bold text-gray-500 uppercase mb-3">DAFTAR AREA CHECKPOINT :</h3>

                <div x-show="showGrid" class="grid grid-cols-2 md:grid-cols-3 gap-3">
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

            {{-- FOOTER PROGRESS --}}
            <div class="mt-8 bottom-20 left-0 right-0 px-4 md:static">
                <div class="w-full p-4 rounded-lg shadow-lg font-bold text-lg text-center transition-all duration-500 flex items-center justify-center gap-2 border-2"
                    :class="isCompleted ? 'bg-green-600 text-white border-green-700' : 'bg-white text-slate-600 border-slate-300'">
                    <span x-text="progressText"></span>
                </div>
            </div>
        @else
            {{-- BLOCKER JIKA TIDAK BISA AKSES GRID --}}
            @if(empty($patroliPending) && empty($patroliExpired) && !$isClaimed)
                <div class="mt-8 bg-gray-50 border-2 border-dashed border-gray-300 rounded-xl p-8 text-center">
                    <p class="text-sm text-gray-600">Tekan tombol "Claim" di atas untuk membuka area.</p>
                </div>
            @endif
        @endif

        {{-- MODAL KAMERA --}}
        <div x-show="showModal" class="relative z-50" style="display: none;">
             <div x-show="showModal" class="fixed inset-0 bg-black bg-opacity-75"></div>
             <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div class="relative w-full max-w-md bg-[#2a4a6f] rounded-xl p-6">
                        <button @click="closeModal()" class="absolute top-4 right-4 text-white"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                        <h3 class="text-white text-xl font-bold text-center mb-4" x-text="currentArea"></h3>
                        <div class="mb-4 bg-black aspect-[4/3] relative">
                            <video x-ref="videoFeed" x-show="cameraState==='camera'" autoplay playsinline class="w-full h-full object-cover"></video>
                            <img :src="imageBase64" x-show="cameraState==='preview'" class="w-full h-full object-cover">
                        </div>
                        <canvas x-ref="canvas" class="hidden"></canvas>
                        <div class="space-y-3">
                            <button x-show="cameraState==='camera'" @click="takeSnapshot()" class="w-full bg-white text-[#2a4a6f] font-bold py-3 rounded-lg">AMBIL FOTO</button>
                            <div x-show="cameraState==='preview'" class="grid grid-cols-2 gap-3">
                                <button @click="retakePhoto()" class="bg-amber-600 text-white font-bold py-3 rounded-lg">ULANG</button>
                                <button @click="submitCheckpoint()" class="bg-green-500 text-white font-bold py-3 rounded-lg">SIMPAN</button>
                            </div>
                        </div>
                    </div>
                </div>
             </div>
        </div>
        
        {{-- MODAL ALERT --}}
        <div x-show="showAlertModal" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/80">
            <div class="bg-white p-6 rounded-lg max-w-sm w-full text-center">
                <h3 class="text-lg font-bold mb-2">Info</h3>
                <p x-text="alertData.waktu" class="mb-4"></p>
                <button @click="showAlertModal=false" class="bg-blue-600 text-white px-4 py-2 rounded">OK</button>
            </div>
        </div>

    </div>
@endsection