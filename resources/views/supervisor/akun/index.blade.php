{{-- 
  File: resources/views/komandan/akun/index.blade.php
  Revisi: Menyeragamkan semua warna ikon menjadi Biru Navy (#1e3a5f)
--}}

@extends('layouts.app')

@section('header-left')
    {{-- Update warna border dan text badge agar seragam --}}
    <a class="flex items-center border-2 border-[#1a2847] text-[#1a2847] text-sm font-bold px-4 py-2 rounded-full">
        DAFTAR AKUN
    </a>
@endsection

@section('content')

{{-- Bungkusan Utama AlpineJS --}}
<div x-data="{ 
    openInfoModal: false, 
    infoUser: {},
    defaultFoto: '{{ asset('images/default-profile.png') }}'
}">

    {{-- Konten Halaman --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header dengan Judul dan Filter --}}
        <div class="mb-6 flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800">Daftar Akun</h2>
            
            <div class="relative w-full max-w-md">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" 
                       id="searchName" 
                       placeholder="Cari nama anggota..."
                       class="block w-full h-[42px] pl-10 pr-4 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] placeholder-gray-400 shadow-sm"
                       @input="filterUsers($event.target.value)">
            </div>
        </div>
        
        {{-- Toast Notification --}}
        @if (session('success'))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 5000)"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-x-full"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-x-0"
                 x-transition:leave-end="opacity-0 translate-x-full"
                 class="fixed top-4 right-4 z-50 max-w-sm w-full bg-white rounded-lg shadow-lg border-l-4 border-green-500 p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-gray-900">{{ session('success') }}</p>
                    </div>
                    <button @click="show = false" class="ml-4 flex-shrink-0 text-gray-400 hover:text-gray-500">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        @endif
        @if ($errors->any())
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 5000)"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-x-full"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-x-0"
                 x-transition:leave-end="opacity-0 translate-x-full"
                 class="fixed top-4 right-4 z-50 max-w-sm w-full bg-white rounded-lg shadow-lg border-l-4 border-red-500 p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <ul class="text-sm text-gray-900 space-y-1">@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
                    </div>
                    <button @click="show = false" class="ml-4 flex-shrink-0 text-gray-400 hover:text-gray-500">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        {{-- Daftar Kartu Akun --}}
        <div class="space-y-4">
            {{-- Pesan Data Tidak Ditemukan --}}
            <div id="noDataMessage" class="hidden text-center py-12">
                <div class="bg-gray-100 rounded-2xl p-8 max-w-md mx-auto">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke-width="2"></circle>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9l-6 6m0-6l6 6"></path>
                    </svg>
                    <h3 class="text-lg font-bold text-gray-600 mb-2">Data Tidak Ditemukan</h3>
                    <p class="text-sm text-gray-500">Tidak ada anggota yang sesuai dengan pencarian Anda.</p>
                </div>
            </div>
            
            @foreach ($users as $user) 

                @php
                    $isLocked = in_array(strtolower($user->peran), ['komandan', 'bau']);
                    $isShiftLocked = (strtolower($user->peran) == 'bau');
                    // Menggunakan warna navy yang sama untuk background card aktif
                    $innerBg = $user->status == 'Tidak Aktif' ? 'bg-gray-500' : 'bg-[#1e3a5f]';
                @endphp

                <div class="bg-gray-300 rounded-[2.5rem] p-2 shadow-inner hover:scale-[1.01] transition-transform duration-300 user-card" data-name="{{ strtolower($user->nama_lengkap) }}">
                    <div class="relative {{ $innerBg }} rounded-[2.2rem] p-4 pr-14 shadow-lg flex items-center min-h-[100px]">
                        
                        {{-- 1. AVATAR (Kiri) --}}
                        <div class="flex-shrink-0 mr-4">
                            <div class="bg-white rounded-full h-16 w-16 flex items-center justify-center overflow-hidden border-2 border-white/50 shadow-sm">
                                @if($user->foto_profil)
                                    <img src="{{ asset('storage/' . $user->foto_profil) }}" class="h-full w-full object-cover">
                                @else
                                    {{-- Icon Avatar Placeholder (Disamakan warnanya) --}}
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-[#1e3a5f]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                @endif
                            </div>
                        </div>

                        {{-- 2. INFO & ACTIONS (Tengah) --}}
                        <div class="flex-1 min-w-0 flex flex-col justify-center">
                            <div class="mb-2">
                                <p class="text-white/80 text-[10px] font-bold uppercase tracking-wider leading-tight">
                                    {{ $user->peran }}
                                </p>
                                <h3 class="text-white text-lg font-bold truncate leading-tight tracking-wide">
                                    {{ $user->nama_lengkap }}
                                </h3>
                            </div>

                            <div class="flex flex-wrap gap-1">
                                {{-- Shift --}}
                                @if($isShiftLocked)
                                    <button disabled class="bg-gray-400 text-white text-[10px] font-bold px-2 py-1 rounded shadow cursor-not-allowed opacity-60">Shift</button>
                                @else
                                    <a href="{{ route('supervisor.akun.shift', $user->id_pengguna) }}" class="bg-green-600 hover:bg-green-500 text-white text-[10px] font-bold px-2 py-1 rounded shadow transition">Shift</a>
                                @endif
                            </div>
                        </div>

                        {{-- 3. TOMBOL INFO (Kanan Absolute) --}}
                        <div class="absolute right-4 top-1/2 transform -translate-y-1/2">
                            <button @click="openInfoModal = true; infoUser = {{ json_encode($user) }};" 
                                    class="bg-white/20 hover:bg-white/30 text-white rounded-full w-8 h-8 flex items-center justify-center backdrop-blur-sm transition border border-white/30" title="Lihat Detail">
                                <span class="font-serif italic font-bold text-lg">i</span>
                            </button>
                        </div>
                    </div>
                </div>

            @endforeach
        </div>
    </div>



    {{-- 
      =================================
      KUMPULAN MODAL (IKON SERAGAM #1e3a5f)
      =================================
    --}}



    {{-- MODAL INFO (DETAIL PROFIL) --}}
    <div x-show="openInfoModal" class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-gray-900 bg-opacity-70 backdrop-blur-sm" style="display: none;">
        <div class="relative w-full max-w-md mx-4 bg-white rounded-2xl shadow-2xl overflow-hidden" @click.away="openInfoModal = false">
            
            {{-- HEADER BIRU --}}
            <div class="bg-[#1e3a5f] py-4 px-6 border-b border-[#1e3a5f] flex justify-between items-start">
                <div>
                    <h5 class="text-lg font-bold text-white tracking-wide flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        DETAIL PROFIL
                    </h5>
                    <p class="text-xs text-blue-200 mt-1 ml-7">Informasi lengkap akun anggota</p>
                </div>
                <button type="button" @click="openInfoModal = false" class="text-white/70 hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="modal-body max-h-[65vh] overflow-y-auto p-6">
                
                {{-- Foto Profil Besar --}}
                <div class="flex flex-col items-center justify-center mb-6">
                    <div class="relative">
                        <div class="p-1 bg-white rounded-full border-2 border-dashed border-[#1e3a5f]/30 shadow-sm">
                            <img :src="infoUser.foto_profil ? `/storage/${infoUser.foto_profil}` : defaultFoto" 
                                 alt="Foto Profil" 
                                 class="w-28 h-28 rounded-full object-cover">
                        </div>
                        <div class="absolute bottom-0 right-0 bg-[#1e3a5f] text-white text-[10px] font-bold px-3 py-1 rounded-full border-2 border-white uppercase tracking-wider shadow-sm"
                             x-text="infoUser.peran">
                        </div>
                    </div>
                </div>

                {{-- Grid Informasi (SEMUA ICON DIUBAH MENJADI #1e3a5f) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Nama Lengkap --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Nama Lengkap</label>
                        <div class="flex items-center bg-blue-50/50 p-3 rounded-xl border border-blue-100">
                            {{-- Icon --}}
                            <svg class="w-5 h-5 text-[#1e3a5f] mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            <span class="text-sm font-bold text-gray-800" x-text="infoUser.nama_lengkap || '-'"></span>
                        </div>
                    </div>

                    {{-- Username --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Username</label>
                        <div class="flex items-center bg-gray-50 p-3 rounded-xl border border-gray-100">
                            {{-- Icon Uniform Color --}}
                            <svg class="w-5 h-5 text-[#1e3a5f] mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="text-sm font-semibold text-gray-700" x-text="infoUser.username || '-'"></span>
                        </div>
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Email</label>
                        <div class="flex items-center bg-gray-50 p-3 rounded-xl border border-gray-100">
                            <svg class="w-5 h-5 text-[#1e3a5f] mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            <span class="text-sm font-semibold text-gray-700" x-text="infoUser.email || '-'"></span>
                        </div>
                    </div>

                    {{-- No HP --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">No. Handphone</label>
                        <div class="flex items-center bg-gray-50 p-3 rounded-xl border border-gray-100">
                            {{-- Icon Uniform Color --}}
                            <svg class="w-5 h-5 text-[#1e3a5f] mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            <span class="text-sm font-semibold text-gray-700" x-text="infoUser.no_hp || '-'"></span>
                        </div>
                    </div>

                    {{-- Tanggal Lahir --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Tanggal Lahir</label>
                        <div class="flex items-center bg-gray-50 p-3 rounded-xl border border-gray-100">
                            {{-- Icon Uniform Color --}}
                            <svg class="w-5 h-5 text-[#1e3a5f] mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span class="text-sm font-semibold text-gray-700" x-text="infoUser.tanggal_lahir ? infoUser.tanggal_lahir.substring(0, 10) : '-'"></span>
                        </div>
                    </div>

                    {{-- Jenis Jadwal --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Jenis Jadwal</label>
                        <div class="flex items-center bg-gray-50 p-3 rounded-xl border border-gray-100">
                            {{-- Icon Uniform Color --}}
                            <svg class="w-5 h-5 text-[#1e3a5f] mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="text-sm font-bold text-[#1e3a5f]" 
                                  x-text="infoUser.jenis_jadwal === 'shift' ? 'SHIFT' : (infoUser.jenis_jadwal === 'non_shift' ? 'NON-SHIFT' : '-')"></span>
                        </div>
                    </div>

                    {{-- Alamat --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Alamat Domisili</label>
                        <div class="flex items-start bg-gray-50 p-3 rounded-xl border border-gray-100">
                            {{-- Icon Uniform Color --}}
                            <svg class="w-5 h-5 text-[#1e3a5f] mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span class="text-sm font-medium text-gray-600 leading-relaxed" x-text="infoUser.alamat || '-'"></span>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="md:col-span-2 mt-2">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Status Akun</label>
                        <div :class="infoUser.status === 'Aktif' ? 'bg-green-100 text-green-800 border-green-200' : 'bg-red-100 text-red-800 border-red-200'" 
                             class="flex justify-center items-center p-2 rounded-lg border font-bold text-sm">
                            <span x-text="infoUser.status"></span>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>



</div>
</div>

<script>
function filterUsers(searchTerm) {
    const cards = document.querySelectorAll('.user-card');
    const noDataMessage = document.getElementById('noDataMessage');
    const search = searchTerm.toLowerCase().trim();
    let visibleCount = 0;
    
    cards.forEach(card => {
        const userName = card.getAttribute('data-name');
        const words = userName.split(' ');
        const match = words.some(word => word.startsWith(search));
        
        if (match) {
            card.style.display = '';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    if (visibleCount === 0 && search !== '') {
        noDataMessage.classList.remove('hidden');
    } else {
        noDataMessage.classList.add('hidden');
    }
}
</script>

@endsection