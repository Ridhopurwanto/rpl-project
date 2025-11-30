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
                        <label class="block text-sm font-medium text-gray-700 mb-2">Foto Profil</label>
                        <div class="flex items-center space-x-5">
                            <div class="flex-shrink-0">
                                @php
                                    // LOGIKA BARU (Sama seperti index): Cek foto di dua lokasi
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
                                    <div class="h-16 w-16 rounded-full bg-[#2a4a6f] flex items-center justify-center text-white text-xl font-bold">
                                        {{ strtoupper(substr($pengguna->nama_lengkap ?? $pengguna->username, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <input type="file" name="foto_profil" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-[#2a4a6f] hover:file:bg-blue-100 transition cursor-pointer">
                                <p class="mt-1 text-xs text-gray-500">JPG, PNG atau JPEG (Maks. 2MB).</p>
                                @error('foto_profil')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
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

        {{-- BAGIAN 2: FORM GANTI PASSWORD (Sebelah Kanan) --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden sticky top-6">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Keamanan</h3>
                    <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                </div>

                <form action="{{ route('profil.update-password') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    @method('PATCH')

                    {{-- Pesan Error Khusus Password --}}
                    @if($errors->hasBag('password_errors'))
                        <div class="bg-red-50 border-l-4 border-red-500 p-3 mb-4">
                            <p class="text-xs text-red-700 font-bold">Gagal Mengubah Password</p>
                        </div>
                    @endif

                    <div>
                        <label for="password_lama" class="block text-sm font-medium text-gray-700">Password Lama</label>
                        <input type="password" name="password_lama" id="password_lama" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#2a4a6f] focus:ring-[#2a4a6f] sm:text-sm" placeholder="••••••••">
                        @error('password_lama') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <hr class="border-gray-100 border-dashed">

                    <div>
                        <label for="password_baru" class="block text-sm font-medium text-gray-700">Password Baru</label>
                        <input type="password" name="password_baru" id="password_baru" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#2a4a6f] focus:ring-[#2a4a6f] sm:text-sm" placeholder="Min. 8 karakter">
                        @error('password_baru') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password_baru_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                        <input type="password" name="password_baru_confirmation" id="password_baru_confirmation" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#2a4a6f] focus:ring-[#2a4a6f] sm:text-sm" placeholder="Ulangi password baru">
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-colors">
                            Ganti Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection