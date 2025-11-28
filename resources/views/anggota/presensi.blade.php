@extends('layouts.app')

@section('header-left')
    <a href="{{ route('anggota.presensi.index') }}" class="bg-[#2a4a6f] text-white text-sm font-semibold px-6 py-2 rounded-full shadow-md">
        PRESENSI
    </a>
@endsection

@section('content')

<div class="w-full min-h-screen bg-slate-100 p-4 pb-32"
     x-data="{ 
        showPhotoModal: false, 
        modalPhoto: '',
        
        showCreateModal: false,
        cameraState: 'camera', 
        stream: null,
        imageBase64: '',
        currentTime: '',
        jenisPresensi: 'masuk', 

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

        startCamera() {
            this.cameraState = 'camera';
            this.imageBase64 = '';
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } })
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
            const ctx = canvas.getContext('2d');
            ctx.translate(canvas.width, 0);
            ctx.scale(-1, 1);
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            this.imageBase64 = canvas.toDataURL('image/jpeg', 0.8);
            this.cameraState = 'preview';
            this.stopCamera();
        },
        retakePhoto() {
            this.startCamera();
        }
     }"
     x-init="init()"
>

    {{-- === 1. KALENDER === --}}
    <div class="text-center text-lg font-bold text-gray-800 mt-4">
        {{ strtoupper($namaBulan) }}
    </div>

    <div class="grid grid-cols-7 gap-1 text-center text-sm mt-2 font-semibold">
        <div class="text-gray-500">Su</div><div class="text-gray-500">Mo</div><div class="text-gray-500">Tu</div>
        <div class="text-gray-500">We</div><div class="text-gray-500">Th</div><div class="text-gray-500">Tr</div><div class="text-gray-500">Sa</div>

        @foreach($dataKalender as $hari)
            @php
                $bgColor = 'bg-gray-100';
                if ($hari['jenis_shift'] === 'pagi') $bgColor = 'bg-yellow-400';
                elseif ($hari['jenis_shift'] === 'malam') $bgColor = 'bg-blue-400';
                elseif ($hari['jenis_shift'] === 'off') $bgColor = 'bg-red-500 text-white';
                elseif ($hari['tanggal'] === null) $bgColor = 'bg-transparent';
            @endphp
            <div class="{{ $bgColor }} rounded-lg p-2">{{ $hari['tanggal'] }}</div>
        @endforeach
    </div>

    {{-- Legenda --}}
    <div class="flex justify-center space-x-4 md:space-x-8 mt-6 text-xs md:text-sm font-bold text-gray-600 bg-white p-4 rounded-full shadow-sm mx-auto w-fit">
        <div class="flex items-center"><span class="w-3 h-3 md:w-4 md:h-4 rounded-full bg-yellow-400 mr-2"></span> Shift Pagi</div>
        <div class="flex items-center"><span class="w-3 h-3 md:w-4 md:h-4 rounded-full bg-blue-500 mr-2"></span> Shift Malam</div>
        <div class="flex items-center"><span class="w-3 h-3 md:w-4 md:h-4 rounded-full bg-red-500 mr-2"></span> Off</div>
    </div>

    {{-- === 2. INFO SHIFT HARI INI (BLUE CARD + WAKTU) === --}}
    @php
        // Logika Warna Badge Shift
        $badgeClass = 'bg-gray-200 text-gray-600'; 
        // Kita gunakan strtolower agar cocok dengan controller
        $shiftName = strtolower($shiftHariIni);

        if ($shiftName == 'pagi') { 
            $badgeClass = 'bg-yellow-400 text-slate-900 border border-yellow-500'; 
        } elseif ($shiftName == 'malam') { 
            $badgeClass = 'bg-blue-500 text-white border border-blue-300'; 
        } elseif ($shiftName == 'off') { 
            $badgeClass = 'bg-red-500 text-white border border-red-300'; 
        }
    @endphp

    <div class="mt-6 bg-[#2a4a6f] rounded-xl shadow-lg p-4 flex items-center justify-between relative overflow-hidden border border-blue-800">
        {{-- Dekorasi Latar Belakang --}}
        <div class="absolute -left-4 -bottom-4 text-white opacity-5 pointer-events-none">
            <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20a2 2 0 0 0 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zM9 14H7v-2h2v2zm4 0h-2v-2h2v2zm4 0h-2v-2h2v2zm-8 4H7v-2h2v2zm4 0h-2v-2h2v2zm4 0h-2v-2h2v2z"></path></svg>
        </div>

        {{-- Label Kiri --}}
        <div class="flex items-center gap-3 z-10">
            <div class="p-2 bg-white/10 rounded-full text-blue-200 border border-white/10 shadow-inner">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-[10px] text-blue-300 font-bold uppercase tracking-wider leading-tight">Jadwal Shift</p>
                <p class="text-white font-bold text-lg leading-tight">HARI INI</p>
            </div>
        </div>
        
        {{-- Bagian Kanan: Badge Shift & Status Waktu --}}
        <div class="flex flex-col items-end z-10 gap-1">
            {{-- Badge Shift --}}
            <div class="{{ $badgeClass }} px-3 py-1 rounded-full shadow-md flex items-center gap-2">
                <span class="text-xs font-bold uppercase tracking-wide">
                    {{ $shiftHariIni ? $shiftHariIni : 'TIDAK ADA' }}
                </span>
            </div>

            {{-- Info Waktu Terdekat (BARU) --}}
            <div class="flex items-center gap-1 text-[10px] font-semibold text-blue-100 bg-black/20 px-2 py-0.5 rounded-md mt-1">
                <span>{{ $jadwalAbsen['info_terdekat'] }}</span>
            </div>
        </div>
    </div>

    {{-- === 3. FILTER RIWAYAT (STYLE SERAGAM) === --}}
    <div class="bg-white rounded-lg shadow-md p-5 mt-4 mb-6">
        <h3 class="text-sm font-bold text-slate-600 uppercase mb-3">FILTER RIWAYAT :</h3>
        
        <form action="{{ route('anggota.presensi.index') }}" method="GET">
            <div class="flex flex-col md:flex-row md:items-end gap-4">
                
                {{-- Input Tanggal Awal --}}
                <div class="flex-1">
                    <label for="start_date" class="block text-xs font-bold text-slate-500 mb-1 uppercase">DARI TANGGAL :</label>
                    <input 
                        type="date" 
                        id="start_date"
                        name="start_date"
                        value="{{ $startDate }}"
                        class="w-full bg-[#2a4a6f] text-white px-4 py-2 rounded-lg shadow border-none focus:outline-none focus:ring-2 focus:ring-blue-400"
                        style="color-scheme: dark;"
                    >
                </div>

                {{-- Input Tanggal Akhir --}}
                <div class="flex-1">
                    <label for="end_date" class="block text-xs font-bold text-slate-500 mb-1 uppercase">SAMPAI TANGGAL :</label>
                    <input 
                        type="date" 
                        id="end_date"
                        name="end_date"
                        value="{{ $endDate }}"
                        class="w-full bg-[#2a4a6f] text-white px-4 py-2 rounded-lg shadow border-none focus:outline-none focus:ring-2 focus:ring-blue-400"
                        style="color-scheme: dark;"
                    >
                </div>

                {{-- Tombol Filter --}}
                <div class="md:mb-[1px]"> 
                    <button type="submit" class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-8 rounded-lg shadow transition-colors duration-200 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        FILTER
                    </button>
                </div>

            </div>
        </form>
    </div>

    {{-- === 3. RIWAYAT PRESENSI (CARD LAYOUT) === --}}
    <div class="mt-4 mb-20 space-y-3">
        @forelse($riwayatPresensi as $log)
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                
                {{-- Header: Tanggal & Status --}}
                <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] px-4 py-2.5 flex justify-between items-center">
                    <div>
                        <p class="text-xs text-blue-200 font-semibold uppercase">Tanggal</p>
                        <p class="text-white font-bold text-base">{{ $log->waktu->format('d/m/Y') }}</p>
                    </div>
                    
                    {{-- Badge Status --}}
                    @if($log->status == 'Terlambat')
                        <span class="bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg uppercase">
                            Telat
                        </span>
                    @elseif($log->status == 'Tepat Waktu' || $log->status == 'Hadir')
                        <span class="bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg uppercase">
                            Hadir
                        </span>
                    @else
                        <span class="bg-gray-400 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg uppercase">
                            {{ $log->status }}
                        </span>
                    @endif
                </div>

                {{-- Body: Foto + Info Grid --}}
                <div class="p-4 flex gap-4">
                    
                    {{-- Foto Thumbnail (Kiri) --}}
                    <div class="flex-shrink-0">
                        <img 
                            src="{{ asset('storage/' . $log->foto) }}" 
                            alt="Foto Presensi" 
                            class="w-20 h-20 object-cover rounded-lg shadow-md cursor-pointer hover:opacity-80 transition-opacity"
                            @click="showPhotoModal = true; modalPhoto = '{{ asset('storage/' . $log->foto) }}'"
                        >
                    </div>

                    {{-- Info Detail (Kanan) --}}
                    <div class="flex-1 space-y-3">
                        
                        {{-- Waktu --}}
                        <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <p class="text-[10px] text-gray-500 font-semibold uppercase">Waktu</p>
                                    <p class="text-gray-800 font-bold text-base">{{ $log->waktu->format('H:i') }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Jenis Presensi --}}
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 {{ $log->jenis_presensi == 'masuk' ? 'text-green-500' : 'text-orange-500' }}" 
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if($log->jenis_presensi == 'masuk')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    @endif
                                </svg>
                                <div>
                                    <p class="text-[10px] text-gray-500 font-semibold uppercase">Jenis Presensi</p>
                                    <p class="text-gray-800 font-bold text-base uppercase">{{ $log->jenis_presensi }}</p>
                                </div>
                            </div>
                            <span class="text-xs font-bold px-2.5 py-1 rounded-full 
                                {{ $log->jenis_presensi == 'masuk' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                                {{ strtoupper($log->jenis_presensi) }}
                            </span>
                        </div>

                    </div>
                </div>
            </div>
        @empty
            {{-- Empty State --}}
            <div class="bg-white rounded-xl shadow-md p-8 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <p class="text-gray-500 font-semibold">Tidak ada data presensi pada rentang tanggal ini.</p>
            </div>
        @endforelse
    </div>





    {{-- === 4. TOMBOL FAB (SMART BUTTON) === --}}
    <div x-data="{ 
            canPresensi: {{ $jadwalAbsen['can_presensi'] ? 'true' : 'false' }},
            pesanError: '{{ $jadwalAbsen['pesan_error'] }}'
         }">
        
        <button 
            @click="
                if(!canPresensi) { 
                    alert(pesanError); 
                } else { 
                    showCreateModal = true; 
                    $nextTick(() => startCamera()); 
                }
            " 
            :class="canPresensi ? 'bg-[#2a4a6f] hover:scale-110 shadow-lg' : 'bg-gray-400 cursor-not-allowed shadow-none opacity-80'"
            class="fixed z-40 bottom-24 right-6 p-4 rounded-full text-white transition-all duration-300 flex items-center justify-center"
        >
            {{-- Ikon Berubah (Plus jika bisa, Gembok jika tidak) --}}
            <template x-if="canPresensi">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            </template>
            <template x-if="!canPresensi">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            </template>
        </button>
    </div>

    {{-- ================= MODAL FOTO & CREATE (SAMA SEPERTI SEBELUMNYA) ================= --}}
    {{-- (Kode Modal Foto & Modal Create Kamera disini sama persis dengan yang di atas, 
         hanya perlu memastikan input name="jenis_presensi" ada di form) --}}
    
    <div x-show="showPhotoModal" class="relative z-[60]" style="display: none;">
        <div class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center p-4" @click="showPhotoModal = false">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md overflow-hidden relative">
                <button @click="showPhotoModal = false" class="absolute top-2 right-2 text-gray-500 hover:text-gray-800 text-2xl font-bold bg-white rounded-full px-2 shadow">&times;</button>
                <div class="bg-[#2a4a6f] p-3 text-white font-bold text-center">BUKTI FOTO</div>
                <div class="p-4 bg-black flex justify-center">
                    <img :src="modalPhoto" class="max-h-[70vh] object-contain">
                </div>
            </div>
        </div>
    </div>

    <div x-show="showCreateModal" class="relative z-50" style="display: none;">
        <div x-show="showCreateModal" class="fixed inset-0 bg-black bg-opacity-75 transition-opacity" @click="showCreateModal = false; stopCamera()"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="showCreateModal" class="relative transform overflow-hidden rounded-xl bg-[#2a4a6f] text-left shadow-2xl transition-all w-full max-w-md p-6">
                    
                {{-- === [BARU] TOMBOL CLOSE (X) === --}}
                <button type="button" @click="showCreateModal = false; stopCamera()" 
                        class="absolute top-4 right-4 text-white/50 hover:text-white transition-colors focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                {{-- ============================== --}}
                
                    <div class="flex flex-col items-center mb-5 space-y-2">
                        <h3 class="text-white text-xl font-bold uppercase tracking-wide text-center">FORM PRESENSI</h3>
                        <div class="inline-flex items-center gap-2 bg-black/30 border border-white/10 rounded-full px-4 py-1.5 backdrop-blur-sm shadow-sm">
                            <svg class="w-4 h-4 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="text-lg font-mono font-bold text-white tracking-widest" x-text="currentTime"></span>
                        </div>
                    </div>

                    <form action="{{ route('anggota.presensi.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="foto_base64" x-model="imageBase64">

                        {{-- PILIHAN JENIS PRESENSI (SMART DISABLED) --}}
                        <div class="mb-4" x-data="{ 
                            initJenis() {
                                // Set default jenis sesuai status (jika belum masuk -> masuk, jika sudah -> pulang)
                                // Data diambil dari controller via $jadwalAbsen
                                this.jenisPresensi = '{{ $jadwalAbsen['default_jenis'] }}';
                            }
                        }" x-init="initJenis()">
                            
                            <label class="block text-blue-200 text-xs font-bold uppercase mb-2 text-center">PILIH JENIS PRESENSI :</label>
                            
                            <div class="flex bg-slate-800/50 p-1 rounded-lg border border-white/10">
                                
                                {{-- TOMBOL MASUK --}}
                                <label class="flex-1 cursor-pointer relative">
                                    {{-- Input Radio (Disabled jika sudah masuk) --}}
                                    <input type="radio" name="jenis_presensi" value="masuk" x-model="jenisPresensi" class="hidden"
                                        @if($jadwalAbsen['disable_masuk']) disabled @endif>
                                    
                                    {{-- Visual Tombol --}}
                                    <div class="text-center py-2 rounded-md text-sm font-bold transition-all duration-200"
                                        :class="jenisPresensi === 'masuk' 
                                            ? 'bg-green-600 text-white shadow-md transform scale-105' 
                                            : ({{ $jadwalAbsen['disable_masuk'] ? 'true' : 'false' }} ? 'text-gray-600 cursor-not-allowed opacity-50' : 'text-gray-400 hover:text-white')">
                                        MASUK
                                    </div>
                                </label>

                                {{-- TOMBOL PULANG --}}
                                <label class="flex-1 cursor-pointer relative">
                                    {{-- Input Radio (Disabled jika belum masuk) --}}
                                    <input type="radio" name="jenis_presensi" value="pulang" x-model="jenisPresensi" class="hidden"
                                        @if($jadwalAbsen['disable_pulang']) disabled @endif>
                                    
                                    {{-- Visual Tombol --}}
                                    <div class="text-center py-2 rounded-md text-sm font-bold transition-all duration-200"
                                        :class="jenisPresensi === 'pulang' 
                                            ? 'bg-red-600 text-white shadow-md transform scale-105' 
                                            : ({{ $jadwalAbsen['disable_pulang'] ? 'true' : 'false' }} ? 'text-gray-600 cursor-not-allowed opacity-50' : 'text-gray-400 hover:text-white')">
                                        PULANG
                                    </div>
                                </label>

                            </div>
                        </div>

                        <div class="mb-5 rounded-lg overflow-hidden border-2 border-white/20 bg-black relative aspect-[4/3] shadow-lg">
                            <video x-ref="videoFeed" x-show="cameraState === 'camera'" autoplay playsinline class="w-full h-full object-cover transform scale-x-[-1]"></video>
                            <img :src="imageBase64" x-show="cameraState === 'preview'" class="w-full h-full object-cover transform" style="display: none;">
                            <div x-show="cameraState === 'camera' && !stream" class="absolute inset-0 flex items-center justify-center text-white text-xs">Memuat Kamera...</div>
                        </div>
                        <canvas x-ref="canvas" class="hidden"></canvas>

                        <div class="space-y-3">
                            <button type="button" x-show="cameraState === 'camera'" @click="takeSnapshot()" class="w-full bg-white text-[#2a4a6f] font-bold py-3 rounded-lg shadow hover:bg-gray-100 transition-colors">AMBIL FOTO</button>
                            <div x-show="cameraState === 'preview'" class="grid grid-cols-2 gap-3" style="display: none;">
                                <button type="button" @click="retakePhoto()" class="bg-slate-500 text-white font-bold py-3 rounded-lg shadow hover:bg-slate-600 transition-colors">ULANG</button>
                                <button type="submit" class="bg-green-500 text-white font-bold py-3 rounded-lg shadow hover:bg-green-600 transition-colors">SUBMIT</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection