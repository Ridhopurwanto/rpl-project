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

    <div class="flex flex-wrap justify-center items-center space-x-4 mt-4 text-xs">
        <div class="flex items-center space-x-1"><div class="w-3 h-3 bg-yellow-400 rounded-full"></div><span>Shift Pagi</span></div>
        <div class="flex items-center space-x-1"><div class="w-3 h-3 bg-blue-400 rounded-full"></div><span>Shift Malam</span></div>
        <div class="flex items-center space-x-1"><div class="w-3 h-3 bg-red-500 rounded-full"></div><span>Off</span></div>
    </div>

    {{-- === 2. FILTER & INFO === --}}
    <div class="mt-6 p-4 bg-white rounded-lg shadow border-l-4 border-[#2a4a6f]">
        <h3 class="text-xs font-bold text-slate-600 uppercase mb-3">RIWAYAT & JADWAL :</h3>
        
        <div class="flex flex-col space-y-3">
            <form action="{{ route('anggota.presensi.index') }}" method="GET" class="flex items-center justify-between">
                <label class="text-sm font-bold text-gray-700">TANGGAL :</label>
                <div class="relative">
                    <input type="date" name="tanggal"
                           value="{{ $tanggalTerpilih->format('Y-m-d') }}" onchange="this.form.submit()"
                           class="bg-[#2a4a6f] text-white text-sm font-bold px-4 py-2 rounded-lg shadow-md border-none cursor-pointer"
                           style="color-scheme: dark;">
                </div>
            </form>
            
            @php
                $namaShift = 'TIDAK ADA JADWAL'; $classShift = 'bg-gray-400 text-white';
                if ($shiftHariIni === 'pagi') { $namaShift = 'SHIFT PAGI'; $classShift = 'bg-yellow-400 text-black'; }
                elseif ($shiftHariIni === 'malam') { $namaShift = 'SHIFT MALAM'; $classShift = 'bg-blue-400 text-white'; }
                elseif ($shiftHariIni === 'off') { $namaShift = 'OFF'; $classShift = 'bg-red-500 text-white'; }
            @endphp
            <div class="flex items-center justify-between">
                <label class="text-sm font-bold text-gray-700">JENIS SHIFT :</label>
                <div class="flex items-center justify-center {{ $classShift }} text-xs font-bold px-4 py-2 rounded-full shadow-md w-[150px]">
                    {{ $namaShift }}
                </div>
            </div>
        </div>
    </div>

    {{-- === 3. TABEL RIWAYAT === --}}
    <div class="mt-4 mb-20 bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-[#2a4a6f] text-white">
                <tr>
                    <th class="p-3 text-center">Foto</th>
                    <th class="p-3 text-center">Waktu</th>
                    <th class="p-3 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 divide-y">
                @if($riwayatHariIni)
                    {{-- Baris Masuk --}}
                    <tr class="text-center bg-white">
                        <td class="p-3">
                            @if($riwayatHariIni->foto_masuk)
                                <button @click="showPhotoModal = true; modalPhoto = '{{ asset('storage/' . $riwayatHariIni->foto_masuk) }}'" class="text-blue-600 underline font-bold text-xs">Buka</button>
                            @else - @endif
                        </td>
                        <td class="p-3">
                            <div class="font-bold text-gray-800">{{ $riwayatHariIni->waktu_masuk }}</div>
                            <div class="text-[10px] text-gray-500 font-bold">MASUK</div>
                        </td>
                        <td class="p-3"><span class="bg-green-100 text-green-800 text-[10px] font-bold px-2 py-1 rounded-full">{{ $riwayatHariIni->status }}</span></td>
                    </tr>
                    {{-- Baris Pulang --}}
                    @if($riwayatHariIni->waktu_pulang)
                        <tr class="text-center bg-white">
                            <td class="p-3">
                                <button @click="showPhotoModal = true; modalPhoto = '{{ asset('storage/' . $riwayatHariIni->foto_pulang) }}'" class="text-blue-600 underline font-bold text-xs">Buka</button>
                            </td>
                            <td class="p-3">
                                <div class="font-bold text-gray-800">{{ $riwayatHariIni->waktu_pulang }}</div>
                                <div class="text-[10px] text-gray-500 font-bold">PULANG</div>
                            </td>
                            <td class="p-3"><span class="bg-blue-100 text-blue-800 text-[10px] font-bold px-2 py-1 rounded-full">{{ $riwayatHariIni->status }}</span></td>
                        </tr>
                    @endif
                @else
                    <tr><td colspan="3" class="p-6 text-center text-gray-500">Belum ada presensi hari ini.</td></tr>
                @endif
            </tbody>
        </table>
    </div>

    {{-- === 4. TOMBOL FAB === --}}
    <button @click="showCreateModal = true; $nextTick(() => startCamera());" 
            class="fixed z-40 bottom-24 right-6 p-4 bg-[#2a4a6f] rounded-full text-white shadow-lg transform hover:scale-110 transition-transform cursor-pointer">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
    </button>

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

                        <div class="mb-4">
                            <label class="block text-blue-200 text-xs font-bold uppercase mb-2 text-center">PILIH JENIS PRESENSI :</label>
                            <div class="flex bg-slate-800/50 p-1 rounded-lg border border-white/10">
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="jenis_presensi" value="masuk" x-model="jenisPresensi" class="hidden">
                                    <div class="text-center py-2 rounded-md text-sm font-bold transition-all duration-200" :class="jenisPresensi === 'masuk' ? 'bg-green-600 text-white shadow-md transform scale-105' : 'text-gray-400 hover:text-white'">MASUK</div>
                                </label>
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="jenis_presensi" value="pulang" x-model="jenisPresensi" class="hidden">
                                    <div class="text-center py-2 rounded-md text-sm font-bold transition-all duration-200" :class="jenisPresensi === 'pulang' ? 'bg-red-600 text-white shadow-md transform scale-105' : 'text-gray-400 hover:text-white'">PULANG</div>
                                </label>
                            </div>
                        </div>

                        <div class="mb-5 rounded-lg overflow-hidden border-2 border-white/20 bg-black relative aspect-[4/3] shadow-lg">
                            <video x-ref="videoFeed" x-show="cameraState === 'camera'" autoplay playsinline class="w-full h-full object-cover transform scale-x-[-1]"></video>
                            <img :src="imageBase64" x-show="cameraState === 'preview'" class="w-full h-full object-cover transform scale-x-[-1]" style="display: none;">
                            <div x-show="cameraState === 'camera' && !stream" class="absolute inset-0 flex items-center justify-center text-white text-xs">Memuat Kamera...</div>
                        </div>
                        <canvas x-ref="canvas" class="hidden"></canvas>

                        <div class="space-y-3">
                            <button type="button" x-show="cameraState === 'camera'" @click="takeSnapshot()" class="w-full bg-white text-[#2a4a6f] font-bold py-3 rounded-lg shadow hover:bg-gray-100 transition-colors">AMBIL FOTO</button>
                            <div x-show="cameraState === 'preview'" class="grid grid-cols-2 gap-3" style="display: none;">
                                <button type="button" @click="retakePhoto()" class="bg-slate-500 text-white font-bold py-3 rounded-lg shadow hover:bg-slate-600 transition-colors">ULANG</button>
                                <button type="submit" class="bg-green-500 text-white font-bold py-3 rounded-lg shadow hover:bg-green-600 transition-colors">SUBMIT</button>
                            </div>
                            <button type="button" @click="showCreateModal = false; stopCamera()" class="w-full text-blue-200 text-sm hover:text-white mt-2 underline">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection