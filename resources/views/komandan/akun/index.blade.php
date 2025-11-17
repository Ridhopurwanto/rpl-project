{{-- 
  File: resources/views/komandan/akun/index.blade.php
  Perbaikan: Logic data binding AlpineJS untuk Edit Modal
--}}

@extends('layouts.app')

{{-- Ganti tombol "HOME" di header menjadi "MANAJEMEN AKUN" --}}
@section('header-left')
    <a href="{{ route('komandan.akun.index') }}" class="bg-slate-800 text-white text-sm font-semibold px-6 py-2 rounded-full shadow-md hover:bg-slate-700 transition">
        MANAJEMEN AKUN
    </a>
@endsection

@section('content')

{{-- 
  ==================================================================
  BUNGKUSAN UTAMA ALPINEJS
  Mendefinisikan semua variabel (openCreateModal, openInfoModal, etc.)
  ==================================================================
--}}
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
    defaultFoto: '{{ asset('images/default-profile.png') }}' {{-- Ganti jika path foto default Anda berbeda --}}
}"
@open-create-modal.window="openCreateModal = true" {{-- Listener untuk FAB --}}
>

    {{-- Konten Halaman (Card List) --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Tampilkan notifikasi sukses --}}
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        {{-- Tampilkan error validasi --}}
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Terjadi Kesalahan:</strong>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Daftar Kartu Akun (Sesuai Mockup) --}}
        <div class="space-y-3">
            @foreach ($users as $user) 
                <div class="bg-[#2a4a6f] rounded-lg shadow-md p-4 flex items-center justify-between">
                    
                    {{-- Bagian Kiri: Ikon + Teks --}}
                    <div class="flex items-center space-x-3">
                        <div class="bg-gray-600 rounded-full p-2 flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div>
                            <h5 class="text-base font-semibold text-white uppercase">{{ $user->peran }}</h5>
                            <p class="text-sm text-gray-300">{{ $user->nama_lengkap }}</p>
                        </div>
                    </div>

                    {{-- Bagian Kanan: Tombol Aksi --}}
                    <div class="flex flex-shrink-0 space-x-1.5">
                        
                        <a href="{{ route('komandan.akun.shift', $user->id_pengguna) }}" class="px-2 py-1 bg-blue-500 rounded-md shadow-sm" title="Shift">
                            <span class="text-white text-xs font-semibold">Shift</span>
                        </a>
                        
                        <button @click="openInfoModal = true; infoUser = {{ json_encode($user) }};" 
                                class="px-2 py-1 bg-green-500 rounded-md shadow-sm" title="Info">
                            <span class="text-white text-xs font-semibold">Info</span>
                        </button>
                        
                        {{-- 
                          PERBAIKAN: 
                          Saat tombol edit diklik, kita set 'editUser' di scope global.
                          Modal 'Edit' nanti akan mengambil data dari 'editUser' ini.
                        --}}
                        <button @click="openEditModal = true; editUser = {{ json_encode($user) }}; editFormAction = '{{ route('komandan.akun.update', $user->id_pengguna) }}';" 
                                class="px-2 py-1 bg-yellow-500 rounded-md shadow-sm" title="Edit">
                            <span class="text-white text-xs font-semibold">Edit</span>
                        </button>
                        
                        <button @click="openHapusModal = true; hapusUserName = '{{ $user->nama_lengkap }}'; hapusFormAction = '{{ route('komandan.akun.destroy', $user->id_pengguna) }}';" 
                                class="px-2 py-1 bg-red-600 rounded-md shadow-sm" title="Delete">
                            <span class="text-white text-xs font-semibold">Delete</span>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div> {{-- Penutup .max-w-7xl --}}

    {{-- 
      =================================
      KUMPULAN MODAL (Pop-up)
      =================================
    --}}

    {{-- =================================== --}}
    {{-- MODAL UNTUK TAMBAH AKUN (SESUAI MOCKUP) --}}
    {{-- =================================== --}}
    <div x-show="openCreateModal" @keydown.escape.window="openCreateModal = false" class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-gray-900 bg-opacity-50" style="display: none;">
        <div class="relative w-full max-w-md p-4 mx-4 bg-white rounded-lg shadow-xl" @click.away="openCreateModal = false">
            <form action="{{ route('komandan.akun.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="flex justify-between items-center pb-3 mb-3 border-b">
                    <h5 class="text-xl font-semibold text-gray-800 uppercase">Tambah Akun</h5>
                    <button type="button" @click="openCreateModal = false" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
                </div>
                <div class="modal-body max-h-[70vh] overflow-y-auto p-2">
                    @include('komandan.akun.partials.form-fields')
                </div>
                <div class="modal-footer pt-3 mt-3 border-t">
                    <button type="submit" class="w-full px-4 py-2.5 text-white bg-[#2a4a6f] rounded-lg hover:bg-opacity-90">
                        BUAT AKUN
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ================================= --}}
    {{-- MODAL UNTUK INFO AKUN (SESUAI MOCKUP) --}}
    {{-- ================================= --}}
    <div x-show="openInfoModal" class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-gray-900 bg-opacity-50" style="display: none;">
        <div class="relative w-full max-w-md p-4 mx-4 bg-white rounded-lg shadow-xl" @click.away="openInfoModal = false">
            <div class="flex justify-between items-center pb-3 mb-3 border-b">
                <h5 class="text-xl font-semibold text-gray-800 uppercase">Detail Profil</h5>
                <button type="button" @click="openInfoModal = false" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
            </div>
            <div class="modal-body max-h-[70vh] overflow-y-auto p-2">
                <div class="w-full bg-gray-200 rounded-lg p-4 text-center mb-4">
                    <img :src="infoUser.foto_profil ? `/storage/${infoUser.foto_profil}` : defaultFoto" alt="Foto Profil" 
                         class="w-32 h-32 mx-auto rounded-full object-cover border-4 border-white shadow-md">
                </div>
                <div class="space-y-3">
                    <div class="flex items-center">
                        <label class="w-1/3 text-sm font-medium text-gray-700">NAMA</label>
                        <div class="w-2/3 bg-[#2a4a6f] text-white text-sm font-semibold rounded-md shadow-sm px-4 py-2.5" x-text="infoUser.nama_lengkap || '-'"></div>
                    </div>
                    <div class="flex items-center">
                        <label class="w-1/3 text-sm font-medium text-gray-700">TANGGAL LAHIR</label>
                        <div class="w-2/3 bg-[#2a4a6f] text-white text-sm font-semibold rounded-md shadow-sm px-4 py-2.5" x-text="infoUser.tanggal_lahir ? infoUser.tanggal_lahir.substring(0, 10) : '-'"></div>
                    </div>
                    <div class="flex items-center">
                        <label class="w-1/3 text-sm font-medium text-gray-700">NO. HP</label>
                        <div class="w-2/3 bg-[#2a4a6f] text-white text-sm font-semibold rounded-md shadow-sm px-4 py-2.5" x-text="infoUser.no_hp || '-'"></div>
                    </div>
                    <div class="flex items-start">
                        <label class="w-1/3 text-sm font-medium text-gray-700 pt-2.5">ALAMAT</label>
                        <div class="w-2/3 bg-[#2a4a6f] text-white text-sm font-semibold rounded-md shadow-sm px-4 py-2.5" x-text="infoUser.alamat || '-'"></div>
                    </div>
                    <div class="flex items-center">
                        <label class="w-1/3 text-sm font-medium text-gray-700">STATUS</label>
                        <div class="w-2/3 bg-[#2a4a6f] text-white text-sm font-semibold rounded-md shadow-sm px-4 py-2.5" x-text="infoUser.status || '-'"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer flex justify-end pt-3 mt-3 border-t">
                <button type="button" @click="openInfoModal = false" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">Tutup</button>
            </div>
        </div>
    </div>

    {{-- ================================= --}}
    {{-- MODAL UNTUK EDIT AKUN (SESUAI MOCKUP) --}}
    {{-- ================================= --}}
    <div x-show="openEditModal" @keydown.escape.window="openEditModal = false" class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-gray-900 bg-opacity-50" style="display: none;">
        <div class="relative w-full max-w-md p-4 mx-4 bg-white rounded-lg shadow-xl" @click.away="openEditModal = false">
            <form :action="editFormAction" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="flex justify-between items-center pb-3 mb-3 border-b">
                    <h5 class="text-xl font-semibold text-gray-800 uppercase">Edit Profil</h5>
                    <button type="button" @click="openEditModal = false" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
                </div>
                <div class="modal-body max-h-[70vh] overflow-y-auto p-2">
                    <div class="w-full bg-gray-200 rounded-lg p-4 text-center mb-4">
                        <img :src="editUser.foto_profil ? /storage/${editUser.foto_profil} : defaultFoto" alt="Foto Profil" 
                             class="w-32 h-32 mx-auto rounded-full object-cover border-4 border-white shadow-md">
                    </div>
                    @include('komandan.akun.partials.form-fields', ['isEdit' => true])
                </div>
                <div class="modal-footer pt-3 mt-3 border-t">
                    <button type="submit" class="w-full px-4 py-2.5 text-white bg-[#2a4a6f] rounded-lg hover:bg-opacity-90">
                        APPLY
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ========================================= --}}
    {{-- MODAL KONFIRMASI HAPUS AKUN --}}
    {{-- ========================================= --}}
    <div x-show="openHapusModal" class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-gray-900 bg-opacity-50" style="display: none;">
        <div class="relative w-full max-w-lg p-6 mx-4 bg-white rounded-lg shadow-xl" @click.away="openHapusModal = false">
            <form :action="hapusFormAction" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header flex items-center pb-3 border-b border-red-200">
                    <span class="p-2 bg-red-100 rounded-full text-red-600 mr-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </span>
                    <h5 class="text-xl font-semibold text-red-800">Konfirmasi Hapus Akun</h5>
                </div>
                <div class="modal-body py-4">
                    <p>Apakah Anda yakin ingin menghapus akun <strong class="font-semibold" x-text="hapusUserName"></strong>?</p>
                    <p class="text-sm text-red-600 mt-2">Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="modal-footer flex justify-end pt-3 border-t">
                    <button type="button" @click="openHapusModal = false" class="px-4 py-2 mr-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">Batal</button>
                    <button type="submit" class="px-4 py-2 text-white bg-red-600 rounded-lg hover:bg-red-700">Ya, Hapus</button>
                </div>
            </form>
        </div>
    </div>

</div> {{-- <-- Penutup div x-data utama --}}

@endsection


{{-- 
  =================================
  TOMBOL TAMBAH AKUN (FAB)
  =================================
--}}
@push('fab')
<div class="absolute bottom-16 right-4 z-50">
    <button @click="$dispatch('open-create-modal')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold w-14 h-14 rounded-full flex items-center justify-center shadow-lg">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
    </button>
</div>
@endpush


@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        // Mendefinisikan komponen Alpine untuk form
        Alpine.data('formFields', (isEdit = false) => ({
            isEdit: isEdit,
            // Variabel lokal untuk form
            nama_lengkap: '',
            username: '',
            peran: 'anggota',
            status: 'Aktif',
            tanggal_lahir: '',
            no_hp: '',
            alamat: '',
            
            // Fungsi untuk MENGISI form ini dengan data
            // Ini akan dipicu oleh $watch
            fillData(userData) {
                if (this.isEdit && userData) {
                    this.nama_lengkap = userData.nama_lengkap || '';
                    this.username = userData.username || '';
                    this.peran = userData.peran || 'anggota';
                    this.status = userData.status || 'Aktif';
                    // PERBAIKAN BUG TANGGAL:
                    // Ambil 10 karakter pertama (YYYY-MM-DD)
                    this.tanggal_lahir = userData.tanggal_lahir ? userData.tanggal_lahir.substring(0, 10) : '';
                    this.no_hp = userData.no_hp || '';
                    this.alamat = userData.alamat || '';
                }
            },
            
            // Fungsi untuk MERESET form "Tambah Akun"
            resetCreateForm() {
                if (!this.isEdit) {
                    this.nama_lengkap = '';
                    this.username = '';
                    this.peran = 'anggota';
                    this.status = 'Aktif';
                    this.tanggal_lahir = '';
                    this.no_hp = '';
                    this.alamat = '';
                }
            }
        }));

        // DAFTARKAN EVENT LISTENER GLOBAL
        // Ini akan dipicu oleh 'effect' di bawah
        
        // 1. Saat 'editUser' global berubah, panggil 'fillData' di form edit
        window.addEventListener('fill-edit-form', (event) => {
            const editForm = document.querySelector('[x-data^="formFields(true"]');
            if (editForm && editForm._x_dataStack) {
                editForm._x_dataStack[0].fillData(event.detail);
            }
        });
        
        // 2. Saat modal create ditutup, panggil 'resetCreateForm'
        window.addEventListener('reset-create-form', () => {
             const createForm = document.querySelector('[x-data^="formFields(false"]');
             if (createForm && createForm._x_dataStack) {
                createForm._x_dataStack[0].resetCreateForm();
             }
        });
    });

    // SCRIPT INI AKAN MEMANTAU PERUBAHAN DI 'x-data' UTAMA
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof Alpine !== 'undefined') {
            Alpine.effect(() => {
                const mainEl = document.querySelector('[x-data]');
                if (!mainEl || !mainEl._x_dataStack) return;
                
                const mainData = mainEl._x_dataStack[0];
                if (!mainData) return;

                // 1. Amati 'editUser'. Jika berubah, kirim event
                const editUser = mainData.editUser;
                if (editUser && editUser.id_pengguna) {
                    window.dispatchEvent(new CustomEvent('fill-edit-form', { detail: editUser }));
                }
                
                // 2. Amati 'openCreateModal'. Jika ditutup, kirim event
                const createModalOpen = mainData.openCreateModal;
                if (createModalOpen === false) {
                    window.dispatchEvent(new CustomEvent('reset-create-form'));
                }
            });
        }
    });
</script>
@endpush