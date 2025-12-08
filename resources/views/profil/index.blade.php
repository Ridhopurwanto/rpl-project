@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto pb-10">
    
    {{-- Header Navigasi Kecil --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            Profil Pengguna
        </h1>
        <a href="{{ url()->previous() }}" class="text-sm font-medium text-gray-500 hover:text-[#2a4a6f] transition-colors flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Kembali
        </a>
    </div>

    {{-- Alert Pesan Sukses --}}
    @if(session('success'))
        <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- KARTU UTAMA --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        
        {{-- Banner Background --}}
        <div class="h-32 bg-[#2a4a6f]"></div>

        <div class="px-6 pb-6">
            <div class="relative flex flex-col sm:flex-row -mt-12 mb-6 gap-5">
                
                {{-- Foto Profil --}}
                <div class="relative flex-shrink-0 mx-auto sm:mx-0">
                    <div class="w-32 h-32 rounded-xl bg-white p-1 shadow-md ring-1 ring-gray-100">
                        @php
                            // LOGIKA BARU: Cek foto di dua lokasi agar kompatibel dengan data lama & baru
                            $fotoUrl = null;
                            if ($pengguna->foto_profil) {
                                // 1. Cek di folder Storage (Standar Laravel / Referensi Komandan)
                                if (file_exists(public_path('storage/' . $pengguna->foto_profil))) {
                                    $fotoUrl = asset('storage/' . $pengguna->foto_profil);
                                } 
                                // 2. Cek di folder Uploads Custom (Jika ada upload manual lewat controller baru)
                                elseif (file_exists(public_path('uploads/profil/' . $pengguna->foto_profil))) {
                                    $fotoUrl = asset('uploads/profil/' . $pengguna->foto_profil);
                                }
                            }
                        @endphp

                        @if($fotoUrl)
                            <img src="{{ $fotoUrl }}" class="w-full h-full object-cover rounded-lg bg-gray-100" alt="Foto Profil">
                        @else
                            {{-- Fallback jika tidak ada foto --}}
                            <div class="w-full h-full bg-gray-100 rounded-lg flex items-center justify-center text-[#2a4a6f] text-4xl font-bold">
                                {{ strtoupper(substr($pengguna->nama_lengkap ?? $pengguna->username, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Identitas --}}
                <div class="flex-1 text-center sm:text-left sm:pt-14">
                    <h2 class="text-2xl font-bold text-gray-900">{{ $pengguna->nama_lengkap }}</h2>
                    <p class="text-gray-500 font-medium mb-3">{{ '@' . $pengguna->username }}</p>
                    
                    <div class="flex flex-wrap gap-2 justify-center sm:justify-start">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium bg-blue-50 text-[#2a4a6f] border border-blue-100 capitalize">
                            {{ $pengguna->peran }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium {{ $pengguna->status == 'Aktif' ? 'bg-green-50 text-green-700 border border-green-100' : 'bg-red-50 text-red-700 border border-red-100' }}">
                            {{ ucfirst($pengguna->status) }}
                        </span>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="w-full sm:w-auto mt-2 sm:mt-0 sm:pt-14 flex justify-center sm:justify-end">
                    <a href="{{ route('profil.edit') }}" class="inline-flex items-center px-4 py-2 bg-[#2a4a6f] border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-800 transition shadow-sm h-fit">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                        Edit Profil
                    </a>
                </div>
            </div>

            <div class="border-t border-gray-100 my-6"></div>

            {{-- Grid Informasi --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                {{-- KOLOM KIRI: Kontak --}}
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                        Informasi Kontak
                    </h3>
                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-100 space-y-4">
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Alamat Email</p>
                            <p class="text-gray-900 font-medium mt-1">{{ $pengguna->email }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">No. WhatsApp / HP</p>
                            <p class="text-gray-900 font-medium mt-1">{{ $pengguna->no_hp ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Alamat Domisili</p>
                            <p class="text-gray-900 font-medium mt-1 leading-relaxed">{{ $pengguna->alamat ?? 'Belum diisi' }}</p>
                        </div>
                    </div>
                </div>

                {{-- KOLOM KANAN: Data Personal --}}
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        Informasi Personal
                    </h3>
                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-100 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Tanggal Lahir</p>
                                <p class="text-gray-900 font-medium mt-1">
                                    @if($pengguna->tanggal_lahir)
                                        {{ \Carbon\Carbon::parse($pengguna->tanggal_lahir)->translatedFormat('d F Y') }}
                                    @else
                                        -
                                    @endif
                                </p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Bergabung</p>
                                <p class="text-gray-900 font-medium mt-1">
                                    {{ $pengguna->created_at->translatedFormat('d M Y') }}
                                </p>
                            </div>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Terakhir Update</p>
                            <p class="text-sm text-gray-600 mt-1">
                                Data profil terakhir diperbarui pada {{ $pengguna->updated_at->diffForHumans() }}.
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection