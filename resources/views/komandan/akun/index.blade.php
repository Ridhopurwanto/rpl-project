@extends('layouts.app')

@section('header-left')
    <a href="{{ route('komandan.akun.index') }}" class="bg-slate-800 text-white text-sm font-semibold px-6 py-2 rounded-full shadow-md hover:bg-slate-700 transition">
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
    resetForm() {
        $dispatch('reset-create-data')
    }
}"
@open-create-modal.window="openCreateModal = true" 
>

    {{-- Konten Halaman --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Notifikasi --}}
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
            </div>
        @endif

        {{-- Daftar Kartu Akun --}}
        <div class="space-y-3">
            @foreach ($users as $user) 
                    <div class="{{ $user->status == 'Tidak Aktif' ? 'bg-[#567ba5]' : 'bg-[#2a4a6f]' }} rounded-lg shadow-md p-4 flex items-center justify-between transition-colors duration-300">                    
                    <div class="flex items-center space-x-3">
                        <div class="bg-gray-600 rounded-full p-2 flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                        </div>
                        <div>
                            <h5 class="text-base font-semibold text-white uppercase">{{ $user->peran }}</h5>
                            <p class="text-sm text-gray-300">{{ $user->nama_lengkap }}</p>
                        </div>
                    </div>

                    <div class="flex flex-shrink-0 space-x-1.5">
                        <a href="{{ route('komandan.akun.shift', $user->id_pengguna) }}" class="px-2 py-1 bg-blue-500 rounded-md shadow-sm" title="Shift">
                            <span class="text-white text-xs font-semibold">Shift</span>
                        </a>
                        
                        {{-- Tombol INFO --}}
                        <button @click="openInfoModal = true; infoUser = {{ json_encode($user) }};" 
                                class="px-2 py-1 bg-green-500 rounded-md shadow-sm" title="Info">
                            <span class="text-white text-xs font-semibold">Info</span>
                        </button>
                        
                        {{-- 
                           Tombol EDIT (PERBAIKAN)
                           Selain membuka modal, kita kirim event 'set-edit-data' 
                           dengan data user agar form partial menangkapnya.
                        --}}
                        <button @click="
                                    openEditModal = true; 
                                    editUser = {{ json_encode($user) }}; 
                                    editFormAction = '{{ route('komandan.akun.update', $user->id_pengguna) }}';
                                    $dispatch('set-edit-data', {{ json_encode($user) }});
                                " 
                                class="px-2 py-1 bg-yellow-500 rounded-md shadow-sm" title="Edit">
                            <span class="text-white text-xs font-semibold">Edit</span>
                        </button>
                        
                        {{-- Tombol HAPUS --}}
                        <button @click="openHapusModal = true; hapusUserName = '{{ $user->nama_lengkap }}'; hapusFormAction = '{{ route('komandan.akun.destroy', $user->id_pengguna) }}';" 
                                class="px-2 py-1 bg-red-600 rounded-md shadow-sm" title="Delete">
                            <span class="text-white text-xs font-semibold">Delete</span>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- 
      =================================
      KUMPULAN MODAL
      =================================
    --}}

    {{-- MODAL TAMBAH (RESET FORM SAAT DIBUKA) --}}
    <div x-show="openCreateModal" x-init="$watch('openCreateModal', value => { if(value) $dispatch('reset-create-data') })"
         class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-gray-900 bg-opacity-50" style="display: none;">
        <div class="relative w-full max-w-md p-4 mx-4 bg-white rounded-lg shadow-xl" @click.away="openCreateModal = false">
            <form action="{{ route('komandan.akun.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="flex justify-between items-center pb-3 mb-3 border-b">
                    <h5 class="text-xl font-semibold text-gray-800 uppercase">Tambah Akun</h5>
                    <button type="button" @click="openCreateModal = false" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
                </div>
                <div class="modal-body max-h-[70vh] overflow-y-auto p-2">
                    {{-- Include Partial untuk Tambah (isEdit = false) --}}
                    @include('komandan.akun.partials.form-fields', ['isEdit' => false])
                </div>
                <div class="modal-footer pt-3 mt-3 border-t">
                    <button type="submit" class="w-full px-4 py-2.5 text-white bg-[#2a4a6f] rounded-lg hover:bg-opacity-90">BUAT AKUN</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL INFO (TETAP SAMA) --}}
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
                    {{-- Detail Fields (Readonly) --}}
                    <div class="flex items-center"><label class="w-1/3 text-sm font-medium text-gray-700">NAMA</label><div class="w-2/3 bg-[#2a4a6f] text-white text-sm font-semibold rounded-md shadow-sm px-4 py-2.5" x-text="infoUser.nama_lengkap || '-'"></div></div>
                    <div class="flex items-center"><label class="w-1/3 text-sm font-medium text-gray-700">TANGGAL LAHIR</label><div class="w-2/3 bg-[#2a4a6f] text-white text-sm font-semibold rounded-md shadow-sm px-4 py-2.5" x-text="infoUser.tanggal_lahir ? infoUser.tanggal_lahir.substring(0, 10) : '-'"></div></div>
                    <div class="flex items-center"><label class="w-1/3 text-sm font-medium text-gray-700">NO. HP</label><div class="w-2/3 bg-[#2a4a6f] text-white text-sm font-semibold rounded-md shadow-sm px-4 py-2.5" x-text="infoUser.no_hp || '-'"></div></div>
                    <div class="flex items-start"><label class="w-1/3 text-sm font-medium text-gray-700 pt-2.5">ALAMAT</label><div class="w-2/3 bg-[#2a4a6f] text-white text-sm font-semibold rounded-md shadow-sm px-4 py-2.5" x-text="infoUser.alamat || '-'"></div></div>
                    <div class="flex items-center"><label class="w-1/3 text-sm font-medium text-gray-700">STATUS</label><div class="w-2/3 bg-[#2a4a6f] text-white text-sm font-semibold rounded-md shadow-sm px-4 py-2.5" x-text="infoUser.status || '-'"></div></div>
                </div>
            </div>
            <div class="modal-footer flex justify-end pt-3 mt-3 border-t">
                <button type="button" @click="openInfoModal = false" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">Tutup</button>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT (DENGAN EVENT LISTENER) --}}
    <div x-show="openEditModal" class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-gray-900 bg-opacity-50" style="display: none;">
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
                        <img :src="editUser.foto_profil ? `/storage/${editUser.foto_profil}` : defaultFoto" alt="Foto Profil" 
                             class="w-32 h-32 mx-auto rounded-full object-cover border-4 border-white shadow-md">
                    </div>
                    {{-- Include Partial untuk Edit (isEdit = true) --}}
                    @include('komandan.akun.partials.form-fields', ['isEdit' => true])
                </div>
                <div class="modal-footer pt-3 mt-3 border-t">
                    <button type="submit" class="w-full px-4 py-2.5 text-white bg-[#2a4a6f] rounded-lg hover:bg-opacity-90">APPLY</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL HAPUS (TETAP SAMA) --}}
    <div x-show="openHapusModal" class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-gray-900 bg-opacity-50" style="display: none;">
        <div class="relative w-full max-w-lg p-6 mx-4 bg-white rounded-lg shadow-xl" @click.away="openHapusModal = false">
            <form :action="hapusFormAction" method="POST">
                @csrf @method('DELETE')
                <div class="modal-header flex items-center pb-3 border-b border-red-200">
                    <span class="p-2 bg-red-100 rounded-full text-red-600 mr-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
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

</div>

{{-- Tombol FAB --}}
@push('fab')
<div class="absolute bottom-16 right-4 z-50">
    <button @click="$dispatch('open-create-modal')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold w-14 h-14 rounded-full flex items-center justify-center shadow-lg">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
    </button>
</div>
@endpush

@endsection