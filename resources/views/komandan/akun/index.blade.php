{{-- 
  File: resources/views/komandan/akun/index.blade.php
  Revisi: Menyeragamkan semua warna ikon menjadi Biru Navy (#1e3a5f)
--}}

@extends('layouts.app')

@section('header-left')
    {{-- Update warna border dan text badge agar seragam --}}
    <a class="inline-block border-2 border-[#1e3a5f] text-[#1e3a5f] text-sm font-bold px-4 py-1 rounded-full mb-4">
        MANAJEMEN AKUN
    </a>
@endsection

@section('content')

{{-- Bungkusan Utama AlpineJS --}}
<div x-data="{ 
    openCreateModal: false, 
    openInfoModal: false, 
    openEditModal: false, 
    openHapusModal: false,
    infoUser: {},
    editUser: {}, 
    editFormAction: '',
    hapusUserName: '',
    hapusFormAction: '',
    defaultFoto: '{{ asset('images/default-profile.png') }}'
}"
@open-create-modal.window="openCreateModal = true" 
>

    {{-- Konten Halaman --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Filter Pencarian --}}
        <div class="mb-6 flex justify-end">
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
                                    <a href="{{ route('komandan.akun.shift', $user->id_pengguna) }}" class="bg-green-600 hover:bg-green-500 text-white text-[10px] font-bold px-2 py-1 rounded shadow transition">Shift</a>
                                @endif

                                {{-- Edit --}}
                                <button @click="
                                    openEditModal = true; 
                                    editUser = {{ json_encode($user) }}; 
                                    editFormAction = '{{ route('komandan.akun.update', $user->id_pengguna) }}';
                                    $dispatch('set-edit-data', {{ json_encode($user) }});
                                " class="bg-yellow-600 hover:bg-yellow-500 text-white text-[10px] font-bold px-2 py-1 rounded shadow transition">Edit</button>

                                {{-- Delete --}}
                                @if($isLocked)
                                    <button disabled class="bg-gray-400 text-white text-[10px] font-bold px-2 py-1 rounded shadow cursor-not-allowed opacity-60">Delete</button>
                                @else
                                    <button @click="openHapusModal = true; hapusUserName = '{{ $user->nama_lengkap }}'; hapusFormAction = '{{ route('komandan.akun.destroy', $user->id_pengguna) }}';" 
                                            class="bg-red-600 hover:bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded shadow transition">Delete</button>
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

    {{-- Floating Action Button (Create) --}}
    <div 
        x-show="!openCreateModal && !openInfoModal && !openEditModal && !openHapusModal" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90"
        class="fixed bottom-8 right-6 z-40"
    >
        <button 
            @click="openCreateModal = true"
            class="bg-[#1e3a5f] hover:bg-[#2a4a6f] text-white rounded-full h-16 w-16 shadow-2xl flex items-center justify-center transition-transform hover:scale-110"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
        </button>
    </div>

    {{-- 
      =================================
      KUMPULAN MODAL (IKON SERAGAM #1e3a5f)
      =================================
    --}}

    {{-- MODAL TAMBAH --}}
    <div x-show="openCreateModal" x-init="$watch('openCreateModal', value => { if(value) $dispatch('reset-create-data') })"
         class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-gray-900 bg-opacity-50" style="display: none;">
        <div class="relative w-full max-w-md mx-4 bg-white rounded-xl shadow-xl overflow-hidden" @click.away="openCreateModal = false">
            <form action="{{ route('komandan.akun.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                {{-- HEADER BIRU --}}
                <div class="bg-[#1e3a5f] py-4 px-6 border-b border-[#1e3a5f] flex justify-between items-center">
                    <h5 class="text-lg font-bold text-white flex items-center tracking-wide">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        TAMBAH AKUN
                    </h5>
                    <button type="button" @click="openCreateModal = false" class="text-white/70 hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="modal-body max-h-[70vh] overflow-y-auto p-6">
                    @include('komandan.akun.partials.form-fields', ['isEdit' => false])
                </div>

                <div class="modal-footer p-4 border-t bg-gray-50">
                    <button type="submit" class="w-full px-4 py-3 text-white font-bold bg-[#1e3a5f] rounded-xl hover:bg-[#2a4a6f] shadow-lg transition transform hover:-translate-y-0.5">BUAT AKUN BARU</button>
                </div>
            </form>
        </div>
    </div>

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

            <div class="modal-footer p-4 border-t border-gray-100 bg-gray-50">
                <button type="button" @click="openInfoModal = false" class="w-full px-4 py-3 text-white font-bold bg-[#1e3a5f] rounded-xl hover:bg-[#2a4a6f] shadow-lg transition transform hover:-translate-y-0.5">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT --}}
    <div x-show="openEditModal" class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-gray-900 bg-opacity-50" style="display: none;">
        <div class="relative w-full max-w-md mx-4 bg-white rounded-xl shadow-xl overflow-hidden" @click.away="openEditModal = false">
            <form :action="editFormAction" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                {{-- HEADER BIRU --}}
                <div class="bg-[#1e3a5f] py-4 px-6 border-b border-[#1e3a5f] flex justify-between items-center">
                    <h5 class="text-lg font-bold text-white flex items-center tracking-wide">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        EDIT PROFIL
                    </h5>
                    <button type="button" @click="openEditModal = false" class="text-white/70 hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="modal-body max-h-[70vh] overflow-y-auto p-6">
                    <div class="w-full bg-blue-50/50 rounded-xl p-4 text-center mb-6 border border-blue-100 border-dashed">
                        <img :src="editUser.foto_profil ? `/storage/${editUser.foto_profil}` : defaultFoto" alt="Foto Profil" 
                             class="w-24 h-24 mx-auto rounded-full object-cover border-4 border-white shadow-md">
                        <p class="text-xs text-gray-500 mt-2">Foto profil saat ini</p>
                    </div>
                    @include('komandan.akun.partials.form-fields', ['isEdit' => true])
                </div>

                <div class="modal-footer p-4 border-t bg-gray-50">
                    <button type="submit" class="w-full px-4 py-3 text-white font-bold bg-[#1e3a5f] rounded-xl hover:bg-[#2a4a6f] shadow-lg transition transform hover:-translate-y-0.5">SIMPAN PERUBAHAN</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL HAPUS --}}
    <div x-show="openHapusModal" class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-gray-900 bg-opacity-50" style="display: none;">
        <div class="relative w-full max-w-lg mx-4 bg-white rounded-xl shadow-xl overflow-hidden" @click.away="openHapusModal = false">
            <form :action="hapusFormAction" method="POST">
                @csrf @method('DELETE')
                
                {{-- Header Merah Solid (Tetap Merah untuk Indikasi Bahaya/Hapus) --}}
                <div class="bg-red-600 py-4 px-6 border-b border-red-600 flex justify-between items-center">
                    <h5 class="text-lg font-bold text-white flex items-center tracking-wide">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        KONFIRMASI HAPUS
                    </h5>
                    <button type="button" @click="openHapusModal = false" class="text-white/70 hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="modal-body p-6 text-center">
                    <div class="bg-red-50 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <p class="text-gray-800 text-lg">Apakah Anda yakin ingin menghapus akun <br><strong class="font-bold text-red-600" x-text="hapusUserName"></strong>?</p>
                    <p class="text-sm text-gray-500 mt-2">Tindakan ini permanen dan tidak dapat dibatalkan.</p>
                </div>

                <div class="modal-footer flex justify-end p-4 bg-gray-50 border-t border-gray-100 gap-3">
                    <button type="button" @click="openHapusModal = false" class="px-5 py-2.5 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 font-semibold shadow-sm transition">Batal</button>
                    <button type="submit" class="px-5 py-2.5 text-white bg-red-600 rounded-lg hover:bg-red-700 font-semibold shadow-md transition">Ya, Hapus</button>
                </div>
            </form>
        </div>
    </div>

</div>
</div>

<script>
function filterUsers(searchTerm) {
    const cards = document.querySelectorAll('.user-card');
    const search = searchTerm.toLowerCase().trim();
    
    cards.forEach(card => {
        const userName = card.getAttribute('data-name');
        const words = userName.split(' ');
        const match = words.some(word => word.startsWith(search));
        
        if (match) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}
</script>

@endsection