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
    userLatitude: null,
    userLongitude: null,
    locationError: '',
    showLocationAlert: false,
    locationDistance: null,
    
    // Koordinat kampus Anda
    campusLat: -6.2315465,
    campusLng: 106.8666516,
    maxDistance: 80,

    init() {
        this.updateTime();
        setInterval(() => { this.updateTime() }, 1000);
        
        // ✅ LANGSUNG MINTA IZIN LOKASI SAAT HALAMAN DIBUKA
        this.requestLocationOnLoad();
    },
    
    updateTime() {
        const now = new Date();
        this.currentTime = now.toLocaleTimeString('id-ID', { 
            hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false 
        }).replace(/\./g, ':') + ' WIB';
    },
    
    // ✅ FUNGSI BARU: Request lokasi otomatis saat load
    requestLocationOnLoad() {
        if (!navigator.geolocation) {
            console.warn('Browser tidak mendukung geolocation');
            return;
        }
        
        // Langsung request permission
        navigator.geolocation.getCurrentPosition(
            (position) => {
                this.userLatitude = position.coords.latitude;
                this.userLongitude = position.coords.longitude;
                console.log('✅ Lokasi terdeteksi:', this.userLatitude, this.userLongitude);
                
                // Cek jarak dari kampus
                const distance = this.calculateDistance(
                    this.userLatitude, 
                    this.userLongitude,
                    this.campusLat,
                    this.campusLng
                );
                this.locationDistance = Math.round(distance);
                console.log('📍 Jarak dari kampus:', this.locationDistance, 'meter');
                
                // Tampilkan alert jika di luar radius
                if (distance > this.maxDistance) {
                    this.showLocationAlert = true;
                    setTimeout(() => {
                        this.showLocationAlert = false;
                    }, 8000);
                }
            },
            (error) => {
                let errorMsg = 'Tidak dapat mengakses lokasi';
                if (error.code === 1) {
                    errorMsg = 'Lokasi belum terdeteksi. Mohon aktifkan GPS dan refresh halaman.';
                } else if (error.code === 2) {
                    errorMsg = 'Lokasi tidak tersedia. Pastikan GPS aktif.';
                } else if (error.code === 3) {
                    errorMsg = 'Timeout saat mendapatkan lokasi.';
                }
                this.locationError = errorMsg;
                this.showLocationAlert = true;
                console.error('❌ Error geolocation:', errorMsg);
                
                setTimeout(() => {
                    this.showLocationAlert = false;
                }, 5000);
            },
            { 
                enableHighAccuracy: true, 
                timeout: 10000, 
                maximumAge: 0 
            }
        );
    },
    
    // Calculate distance between two coordinates (Haversine formula)
    calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371e3; // Earth radius in meters
        const φ1 = lat1 * Math.PI / 180;
        const φ2 = lat2 * Math.PI / 180;
        const Δφ = (lat2 - lat1) * Math.PI / 180;
        const Δλ = (lon2 - lon1) * Math.PI / 180;

        const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
                Math.cos(φ1) * Math.cos(φ2) *
                Math.sin(Δλ/2) * Math.sin(Δλ/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

        return R * c; // Distance in meters
    },
    
    checkLocation() {
        return new Promise((resolve, reject) => {
            if (!navigator.geolocation) {
                reject('Browser tidak mendukung geolocation');
                return;
            }
            
            // Jika sudah ada koordinat dari requestLocationOnLoad, gunakan itu
            if (this.userLatitude && this.userLongitude) {
                const distance = this.calculateDistance(
                    this.userLatitude, 
                    this.userLongitude,
                    this.campusLat,
                    this.campusLng
                );
                
                if (distance <= this.maxDistance) {
                    resolve(true);
                } else {
                    this.locationDistance = Math.round(distance);
                    this.showLocationAlert = true;
                    reject(`Anda berada ${Math.round(distance)}m dari kampus. Harap berada dalam radius ${this.maxDistance}m.`);
                }
                return;
            }
            
            // Kalau belum ada, request lagi
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    this.userLatitude = position.coords.latitude;
                    this.userLongitude = position.coords.longitude;
                    
                    const distance = this.calculateDistance(
                        this.userLatitude, 
                        this.userLongitude,
                        this.campusLat,
                        this.campusLng
                    );
                    
                    if (distance <= this.maxDistance) {
                        resolve(true);
                    } else {
                        this.locationDistance = Math.round(distance);
                        this.showLocationAlert = true;
                        reject(`Anda berada ${Math.round(distance)}m dari kampus. Harap berada dalam radius ${this.maxDistance}m.`);
                    }
                },
                (error) => {
                    let errorMsg = 'Tidak dapat mengakses lokasi';
                    if (error.code === 1) {
                        errorMsg = 'Izin lokasi ditolak. Mohon izinkan akses lokasi di pengaturan browser.';
                        this.showLocationAlert = true;
                    }
                    else if (error.code === 2) errorMsg = 'Lokasi tidak tersedia';
                    else if (error.code === 3) errorMsg = 'Timeout mendapatkan lokasi';
                    reject(errorMsg);
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        });
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
                // Notifikasi error kamera yang lebih baik
                this.showCustomAlert('Kamera Tidak Dapat Diakses', 'Mohon izinkan akses kamera untuk melanjutkan presensi.', 'error');
            });
        } else {
            this.showCustomAlert('Browser Tidak Mendukung', 'Browser Anda tidak mendukung akses kamera.', 'warning');
        }
    },
    
    showCustomAlert(title, message, type = 'info') {
        // Implementasi alert custom jika diperlukan
        alert(title + '\n\n' + message);
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
    },
    closeLocationAlert() {
        this.showLocationAlert = false;
    }
     }"
     x-init="init()"
>

    {{-- ================= NOTIFIKASI LOKASI CUSTOM ================= --}}
    <div x-show="showLocationAlert" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-[-20px]"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-[-20px]"
         class="fixed top-20 left-1/2 transform -translate-x-1/2 z-[9999] w-full max-w-md px-4"
         style="display: none;">
         
        <div class="bg-gradient-to-r from-red-500/95 to-red-600/95 backdrop-blur-lg rounded-2xl shadow-2xl border border-red-300/30 overflow-hidden animate-pulse-subtle">
            
            {{-- Header Notifikasi --}}
            <div class="flex items-center justify-between p-4 border-b border-red-300/20">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-white/20 rounded-full">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.342 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-white font-bold text-lg tracking-wide">LOKASI TIDAK VALID</h3>
                        <p class="text-red-100 text-xs font-semibold uppercase tracking-wider">Presensi Dibatasi</p>
                    </div>
                </div>
                
                {{-- Tombol Close --}}
                <button @click="closeLocationAlert()" 
                        class="p-1 hover:bg-white/20 rounded-full transition-colors">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            {{-- Body Notifikasi --}}
            <div class="p-5">
                {{-- Ikon Lokasi dengan Animasi --}}
                <div class="flex justify-center mb-4">
                    <div class="relative">
                        <div class="absolute inset-0 bg-red-400 rounded-full animate-ping opacity-20"></div>
                        <div class="relative p-4 bg-gradient-to-br from-white to-red-100 rounded-full shadow-inner">
                            <svg class="w-12 h-12 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </div>
                
                {{-- Pesan Error --}}
                <div class="text-center space-y-3">
                    <p class="text-white font-bold text-lg" x-text="locationError || 'Lokasi tidak dapat diakses'"></p>
                    
                    <template x-if="locationDistance">
                        <div class="bg-white/10 rounded-xl p-3 border border-white/20">
                            <div class="flex items-center justify-center gap-2 mb-1">
                                <svg class="w-5 h-5 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span class="text-white font-bold text-xl" x-text="locationDistance + 'm'"></span>
                            </div>
                            <p class="text-red-100 text-sm">Dari lokasi kampus</p>
                        </div>
                    </template>
                    
                    <div class="flex items-center justify-center gap-2 text-red-100 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Radius presensi: <span class="font-bold">80m</span> dari kampus</span>
                    </div>
                </div>
            </div>
            
            {{-- Footer dengan Tombol Aksi --}}
            <div class="p-4 bg-red-600/50 border-t border-red-300/20">
                <div class="flex gap-3">
                    <button @click="closeLocationAlert()" 
                            class="flex-1 bg-white/20 hover:bg-white/30 text-white font-bold py-2.5 rounded-lg transition-colors text-sm">
                        TUTUP
                    </button>
                    <button @click="requestLocationOnLoad(); closeLocationAlert();" 
                            class="flex-1 bg-white text-red-600 font-bold py-2.5 rounded-lg hover:bg-gray-100 transition-colors text-sm shadow-md">
                        COBA LAGI
                    </button>
                </div>
            </div>
        </div>
    </div>

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
        
        // Check if this is today
        $isToday = false;
        if ($hari['tanggal']) {
            $currentDate = now()->format('d');
            $currentMonth = now()->month;
            $currentYear = now()->year;
            $calendarMonth = Carbon\Carbon::parse($startDate)->month;
            $calendarYear = Carbon\Carbon::parse($startDate)->year;
            
            $isToday = ($hari['tanggal'] == $currentDate) && 
                       ($currentMonth == $calendarMonth) && 
                       ($currentYear == $calendarYear);
        }
    @endphp
    <div class="relative {{ $bgColor }} rounded-lg p-2 {{ $isToday ? 'ring-2 ring-black font-extrabold shadow-md' : '' }}">
        {{ $hari['tanggal'] }}
        
    </div>
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

    {{-- === 3. FILTER RIWAYAT (LIVE SEARCH) === --}}
    <div class="bg-white rounded-lg shadow-md p-5 mt-4 mb-6">
        <h3 class="text-sm font-bold text-slate-600 uppercase mb-3">FILTER RIWAYAT :</h3>
        
        <div class="flex flex-col md:flex-row md:items-end gap-4">
            
            {{-- Input Tanggal Awal --}}
            <div class="flex-1">
                <label for="start_date" class="block text-xs font-bold text-slate-500 mb-1 uppercase">DARI TANGGAL :</label>
                <input 
                    type="date" 
                    id="start_date"
                    name="start_date"
                    value="{{ $startDate }}"
                    @change="window.location.href = '{{ route('anggota.presensi.index') }}?start_date=' + $event.target.value + '&end_date=' + document.getElementById('end_date').value"
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
                    @change="window.location.href = '{{ route('anggota.presensi.index') }}?start_date=' + document.getElementById('start_date').value + '&end_date=' + $event.target.value"
                    class="w-full bg-[#2a4a6f] text-white px-4 py-2 rounded-lg shadow border-none focus:outline-none focus:ring-2 focus:ring-blue-400"
                    style="color-scheme: dark;"
                >
            </div>

        </div>
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
                showCustomAlert('Presensi Dibatasi', pesanError, 'warning'); 
            } else {
                checkLocation()
                    .then(() => {
                        showCreateModal = true; 
                        $nextTick(() => startCamera());
                    })
                    .catch((error) => {
                        showLocationAlert = true;
                        setTimeout(() => {
                            showLocationAlert = false;
                        }, 8000);
                    });
            }
        " 
        :class="canPresensi ? 'bg-[#2a4a6f] cursor-pointer' : 'bg-gray-400 cursor-not-allowed opacity-90'"
        class="fixed bottom-24 right-6 text-white rounded-full w-16 h-16 flex items-center justify-center shadow-lg transform hover:scale-110 transition-transform z-40"
    >
        {{-- Ikon tanpa efek tambahan --}}
        <template x-if="canPresensi">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
        </template>
        <template x-if="!canPresensi">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
        </template>
    </button>
</div>

    {{-- ================= MODAL FOTO & CREATE ================= --}}
    {{-- (Modal foto dan create sama seperti sebelumnya) --}}
    
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

    {{-- MODAL AMBIL FOTO (CREATE PRESENSI) --}}
<div x-show="showCreateModal" class="relative z-50" style="display: none;">
    <div x-show="showCreateModal" class="fixed inset-0 bg-black bg-opacity-75 transition-opacity" @click="showCreateModal = false; stopCamera()"></div>
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="showCreateModal" class="relative transform overflow-hidden rounded-xl bg-[#2a4a6f] text-left shadow-2xl transition-all w-full max-w-md p-6">
                
            {{-- === TOMBOL CLOSE (X) === --}}
            <button type="button" @click="showCreateModal = false; stopCamera()" 
                    class="absolute top-4 right-4 text-white/50 hover:text-white transition-colors focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            
                <div class="flex flex-col items-center mb-5 space-y-2">
                    <h3 class="text-white text-xl font-bold uppercase tracking-wide text-center">FORM PRESENSI</h3>
                    <div class="inline-flex items-center gap-2 bg-black/30 border border-white/10 rounded-full px-4 py-1.5 backdrop-blur-sm shadow-sm">
                        <svg class="w-4 h-4 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="text-lg font-mono font-bold text-white tracking-widest" x-text="currentTime"></span>
                    </div>
                </div>


                {{-- ========== FORM DENGAN VALIDASI LOKASI ========== --}}
                <form action="{{ route('anggota.presensi.store') }}" 
                      method="POST" 
                      @submit.prevent="
                          if (!userLatitude || !userLongitude) {
                              showLocationAlert = true; 
                              return false;
                          }
                          if (cameraState === 'camera') {
                              alert('⚠️ HARAP AMBIL FOTO TERLEBIH DAHULU!');
                              return false;
                          }
                          if (!imageBase64) {
                              alert('⚠️ FOTO BELUM DIAMBIL!');
                              return false;
                          }
                          $el.submit();
                      ">
                    @csrf
                    
                    {{-- ===== HIDDEN INPUTS (TERMASUK LOKASI) ===== --}}
                    <input type="hidden" name="foto_base64" x-model="imageBase64">
                    <input type="hidden" name="latitude" x-model="userLatitude">
                    <input type="hidden" name="longitude" x-model="userLongitude">


                    {{-- ===== INDIKATOR STATUS LOKASI ===== --}}
                    <div class="mb-3">
                        <div class="flex items-center justify-center gap-2 bg-black/30 border rounded-lg px-3 py-2 mb-1"
                             :class="userLatitude && userLongitude ? 'border-green-500/50' : 'border-red-500/50'">
                            <svg class="w-5 h-5" 
                                 :class="userLatitude && userLongitude ? 'text-green-400' : 'text-red-400 animate-pulse'" 
                                 fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm font-semibold" 
                                  :class="userLatitude && userLongitude ? 'text-green-300' : 'text-red-300'">
                                <span x-show="userLatitude && userLongitude">📍 Lokasi Terdeteksi</span>
                                <span x-show="!userLatitude || !userLongitude">🔍 Mencari Lokasi...</span>
                            </span>
                        </div>
                        
                        {{-- Info Radius --}}
                        <div class="text-center text-xs text-blue-200 font-medium mt-1">
                            <span x-show="userLatitude && userLongitude">📍 Dalam radius presensi</span>
                            <span x-show="!userLatitude || !userLongitude">⚠️ Aktifkan GPS/Lokasi</span>
                        </div>
                    </div>


                    {{-- ===== PILIHAN JENIS PRESENSI ===== --}}
                    <div class="mb-4" x-data="{ 
                        initJenis() {
                            this.jenisPresensi = '{{ $jadwalAbsen['default_jenis'] }}';
                        }
                    }" x-init="initJenis()">
                        
                        <label class="block text-blue-200 text-xs font-bold uppercase mb-2 text-center">PILIH JENIS PRESENSI :</label>
                        
                        <div class="flex bg-slate-800/50 p-1 rounded-lg border border-white/10">
                            
                            {{-- TOMBOL MASUK --}}
                            <label class="flex-1 cursor-pointer relative">
                                <input type="radio" name="jenis_presensi" value="masuk" x-model="jenisPresensi" class="hidden"
                                    @if($jadwalAbsen['disable_masuk']) disabled @endif>
                                
                                <div class="text-center py-2 rounded-md text-sm font-bold transition-all duration-200"
                                    :class="jenisPresensi === 'masuk' 
                                        ? 'bg-gradient-to-r from-green-600 to-green-500 text-white shadow-md transform scale-105' 
                                        : ({{ $jadwalAbsen['disable_masuk'] ? 'true' : 'false' }} ? 'text-gray-600 cursor-not-allowed opacity-50' : 'text-gray-400 hover:text-white hover:bg-white/10')">
                                    MASUK
                                </div>
                            </label>


                            {{-- TOMBOL PULANG --}}
                            <label class="flex-1 cursor-pointer relative">
                                <input type="radio" name="jenis_presensi" value="pulang" x-model="jenisPresensi" class="hidden"
                                    @if($jadwalAbsen['disable_pulang']) disabled @endif>
                                
                                <div class="text-center py-2 rounded-md text-sm font-bold transition-all duration-200"
                                    :class="jenisPresensi === 'pulang' 
                                        ? 'bg-gradient-to-r from-red-600 to-red-500 text-white shadow-md transform scale-105' 
                                        : ({{ $jadwalAbsen['disable_pulang'] ? 'true' : 'false' }} ? 'text-gray-600 cursor-not-allowed opacity-50' : 'text-gray-400 hover:text-white hover:bg-white/10')">
                                    PULANG
                                </div>
                            </label>

                        </div>
                    </div>


                    {{-- ===== AREA KAMERA / PREVIEW ===== --}}
                    <div class="mb-5 rounded-lg overflow-hidden border-2 border-white/20 bg-black relative aspect-[4/3] shadow-lg">
                        <video x-ref="videoFeed" x-show="cameraState === 'camera'" autoplay playsinline class="w-full h-full object-cover transform scale-x-[-1]"></video>
                        <img :src="imageBase64" x-show="cameraState === 'preview'" class="w-full h-full object-cover transform" style="display: none;">
                        <div x-show="cameraState === 'camera' && !stream" class="absolute inset-0 flex items-center justify-center text-white text-xs">
                            <div class="text-center">
                                <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-white mb-2"></div>
                                <p>Memuat Kamera...</p>
                            </div>
                        </div>
                    </div>
                    <canvas x-ref="canvas" class="hidden"></canvas>


                    {{-- ===== TOMBOL AKSI ===== --}}
                    <div class="space-y-3">
                        <button type="button" 
                                x-show="cameraState === 'camera'" 
                                @click="takeSnapshot()" 
                                class="w-full bg-gradient-to-r from-white to-gray-100 text-[#2a4a6f] font-bold py-3 rounded-lg shadow-lg hover:from-gray-100 hover:to-white hover:shadow-xl transition-all duration-200">
                            AMBIL FOTO
                        </button>
                        
                        <div x-show="cameraState === 'preview'" class="grid grid-cols-2 gap-3" style="display: none;">
                            <button type="button" 
                                    @click="retakePhoto()" 
                                    class="bg-gradient-to-r from-slate-500 to-slate-600 text-white font-bold py-3 rounded-lg shadow hover:from-slate-600 hover:to-slate-700 transition-all duration-200">
                                ULANG
                            </button>
                            <button type="submit" 
                                    class="bg-gradient-to-r from-green-500 to-green-600 text-white font-bold py-3 rounded-lg shadow hover:from-green-600 hover:to-green-700 transition-all duration-200">
                                SUBMIT
                            </button>
                        </div>
                    </div>
                </form>
                {{-- ========== END FORM ========== --}}

            </div>
        </div>
    </div>
</div>


</div>
@endsection

<style>
    @keyframes pulse-subtle {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.95; }
    }
    
    .animate-pulse-subtle {
        animation: pulse-subtle 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    /* Custom scrollbar untuk modal */
    .modal-scroll::-webkit-scrollbar {
        width: 6px;
    }
    
    .modal-scroll::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.1);
        border-radius: 10px;
    }
    
    .modal-scroll::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.3);
        border-radius: 10px;
    }
</style>