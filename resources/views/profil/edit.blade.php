@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto pb-10">
    
    {{-- Header Navigasi --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Edit Profil</h1>
            <p class="text-sm text-gray-500">Perbarui informasi data diri dan keamanan akun Anda.</p>
        </div>
        <a href="{{ route('profil.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#2a4a6f] focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
            Batal
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- BAGIAN 1: FORM EDIT DATA DIRI (Lebih Lebar) --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Informasi Pribadi</h3>
                </div>
                
                <form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                    @csrf
                    @method('PATCH') {{-- Gunakan PATCH untuk update data --}}

                    {{-- Upload Foto --}}
                    <div>
                        {{-- Upload Foto dengan Instant Preview --}}
                        <div x-data="{
                                photoName: null,
                                photoPreview: null,
                                updatePhotoPreview() {
                                    const file = this.$refs.photo.files[0];
                                    if (! file) return;
                                    
                                    this.photoName = file.name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        this.photoPreview = e.target.result;
                                    };
                                    reader.readAsDataURL(file);
                                }
                            }">
                            
                            <label class="block text-sm font-medium text-gray-700 mb-2">Foto Profil</label>
                            
                            <div class="flex items-center space-x-5">
                                
                                {{-- Container Foto --}}
                                <div class="flex-shrink-0">
                                    
                                    {{-- 1. TAMPILAN JIKA ADA PREVIEW BARU (User baru pilih file) --}}
                                    <div x-show="photoPreview" style="display: none;">
                                        <span class="block h-16 w-16 rounded-full ring-2 ring-offset-2 ring-[#2a4a6f]"
                                            :style="'background-size: cover; background-repeat: no-repeat; background-position: center center; background-image: url(\'' + photoPreview + '\');'">
                                        </span>
                                    </div>

                                    {{-- 2. TAMPILAN FOTO LAMA (Default dari Database) --}}
                                    <div x-show="!photoPreview">
                                        @php
                                            // Logika PHP cek foto lama
                                            $fotoUrl = null;
                                            if ($pengguna->foto_profil) {
                                                if (file_exists(public_path('storage/' . $pengguna->foto_profil))) {
                                                    $fotoUrl = asset('storage/' . $pengguna->foto_profil);
                                                } elseif (file_exists(public_path('uploads/profil/' . $pengguna->foto_profil))) {
                                                    $fotoUrl = asset('uploads/profil/' . $pengguna->foto_profil);
                                                }
                                            }
                                        @endphp

                                        @if($fotoUrl)
                                            <img class="h-16 w-16 object-cover rounded-full border border-gray-200" src="{{ $fotoUrl }}" alt="Foto Saat Ini">
                                        @else
                                            {{-- Fallback Inisial --}}
                                            <div class="h-16 w-16 rounded-full bg-[#2a4a6f] flex items-center justify-center text-white text-xl font-bold">
                                                {{ strtoupper(substr($pengguna->nama_lengkap ?? $pengguna->username, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Input File --}}
                                <div class="flex-1">
                                    <input type="file" 
                                        name="foto_profil" 
                                        x-ref="photo"
                                        @change="updatePhotoPreview()"
                                        class="block w-full text-sm text-gray-500 
                                                file:mr-4 file:py-2 file:px-4 
                                                file:rounded-full file:border-0 
                                                file:text-sm file:font-semibold 
                                                file:bg-blue-50 file:text-[#2a4a6f] 
                                                hover:file:bg-blue-100 
                                                transition cursor-pointer">
                                    
                                    <p class="mt-1 text-xs text-gray-500">JPG, PNG atau JPEG (Maks. 2MB).</p>
                                    
                                    @error('foto_profil')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Nama Lengkap --}}
                        <div class="col-span-2">
                            <label for="nama_lengkap" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" id="nama_lengkap" value="{{ old('nama_lengkap', $pengguna->nama_lengkap) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#2a4a6f] focus:ring-[#2a4a6f] sm:text-sm">
                            @error('nama_lengkap') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Email (Readonly saran saya, biar ga sembarangan ganti email login) --}}
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $pengguna->email) }}" class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-50 text-gray-500 shadow-sm focus:border-[#2a4a6f] focus:ring-[#2a4a6f] sm:text-sm cursor-not-allowed" readonly title="Hubungi admin untuk ganti email">
                        </div>

                        {{-- No HP --}}
                        <div>
                            <label for="no_hp" class="block text-sm font-medium text-gray-700">No. WhatsApp / HP</label>
                            <input type="text" name="no_hp" id="no_hp" value="{{ old('no_hp', $pengguna->no_hp) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#2a4a6f] focus:ring-[#2a4a6f] sm:text-sm">
                            @error('no_hp') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Tanggal Lahir --}}
                        <div>
                            <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" id="tanggal_lahir" value="{{ old('tanggal_lahir', $pengguna->tanggal_lahir) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#2a4a6f] focus:ring-[#2a4a6f] sm:text-sm">
                            @error('tanggal_lahir') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Alamat --}}
                        <div class="col-span-2">
                            <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat Domisili</label>
                            <textarea name="alamat" id="alamat" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#2a4a6f] focus:ring-[#2a4a6f] sm:text-sm">{{ old('alamat', $pengguna->alamat) }}</textarea>
                            @error('alamat') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-gray-100">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-[#2a4a6f] hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2a4a6f] transition-colors">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- BAGIAN 2: FORM GANTI PASSWORD --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden top-6">
                
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Keamanan</h3>
                    <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                </div>

                <form action="{{ route('profil.update-password') }}" method="POST" class="p-6 space-y-3">
                    @csrf
                    @method('PATCH')

                    {{-- Alert Error Global untuk Form Password --}}
                    @if($errors->password_errors->any())
                        <div class="bg-red-50 border-l-4 border-red-500 p-3 mb-4 rounded-r-md">
                            <p class="text-xs text-red-700 font-bold">Gagal Mengubah Password</p>
                        </div>
                    @endif

                    {{-- 1. Password Lama --}}
                    <div x-data="{ show: false }" class="group relative">
                       <label for="password_lama" class="block text-sm font-medium text-gray-700">Password Lama</label>
                        <div class="relative mt-2">
                            <input :type="show ? 'text' : 'password'" 
                                   name="password_lama" 
                                   id="password_lama" 
                                   class="block w-full border-t-0 border-x-0 border-b-2 border-gray-200 bg-transparent py-2 px-0 text-gray-900 placeholder-gray-300 focus:border-gray-900 focus:ring-0 focus:outline-none shadow-none rounded-none sm:text-sm transition-colors duration-300 ease-in-out" 
                                   placeholder="Masukkan password lama">
                            
                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-1 text-gray-400 hover:text-gray-900 focus:outline-none transition-colors cursor-pointer">
                                <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                        {{-- PERBAIKAN: Tambahkan argumen kedua 'password_errors' --}}
                        @error('password_lama', 'password_errors') 
                            <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> 
                        @enderror
                    </div>

                    {{-- 2. Password Baru --}}
                    <div x-data="{ show: false }" class="group relative">
                        <label for="password_baru" class="block text-sm font-medium text-gray-700">Password Baru</label>
                        <div class="relative mt-2">
                            <input :type="show ? 'text' : 'password'" 
                                   name="password_baru" 
                                   id="password_baru" 
                                   class="block w-full border-t-0 border-x-0 border-b-2 border-gray-200 bg-transparent py-2 px-0 text-gray-900 placeholder-gray-300 focus:border-gray-900 focus:ring-0 focus:outline-none shadow-none rounded-none sm:text-sm transition-colors duration-300 ease-in-out" 
                                   placeholder="Minimal 8 karakter">

                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-1 text-gray-400 hover:text-gray-900 focus:outline-none transition-colors cursor-pointer">
                                <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                        {{-- PERBAIKAN: Tambahkan argumen kedua 'password_errors' --}}
                        @error('password_baru', 'password_errors') 
                            <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> 
                        @enderror
                    </div>

                    {{-- 3. Konfirmasi Password Baru --}}
                    <div x-data="{ show: false }" class="group relative">
                        <label for="password_baru_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                        <div class="relative mt-2">
                            <input :type="show ? 'text' : 'password'" 
                                   name="password_baru_confirmation" 
                                   id="password_baru_confirmation" 
                                   class="block w-full border-t-0 border-x-0 border-b-2 border-gray-200 bg-transparent py-2 px-0 text-gray-900 placeholder-gray-300 focus:border-gray-900 focus:ring-0 focus:outline-none shadow-none rounded-none sm:text-sm transition-colors duration-300 ease-in-out" 
                                   placeholder="Ulangi password baru">

                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-1 text-gray-400 hover:text-gray-900 focus:outline-none transition-colors cursor-pointer">
                                <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full inline-flex justify-center py-3 px-4 border border-transparent shadow-lg text-sm font-bold rounded-lg text-white bg-gray-800 hover:bg-gray-900 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-blue-200 transition-all duration-300 transform">
                            Ganti Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection