@extends('layouts.guest')

@section('content')
{{-- Wrapper untuk menengahkan card di layar --}}
<div class="min-h-screen flex items-center justify-center px-4 py-12 bg-gray-100">

    {{-- CARD UTAMA --}}
    <div class="w-full max-w-sm mx-auto shadow-2xl rounded-3xl overflow-hidden bg-white">

        {{-- Header dengan background gradient biru gelap dan logo --}}
        {{-- Hapus pt-8 agar elemen di dalamnya bisa menempel ke atas --}}
        <div class="bg-gradient-to-b from-[#1a2847] via-[#243a5e] to-[#2a4a6f] pt-0 pb-12 text-white text-center relative">
            
            {{-- 
              Elemen putih 'shield'
              - Dibuat 'absolute' agar bisa menempel 'top-0'
              - 'left-1/2 -translate-x-1/2' untuk center horizontal
              - 'rounded-t-[3rem]' untuk bagian runcing di atas
              - 'rounded-b-full' untuk bagian bulat di bawah
            --}}
            <div class="relative w-40 h-40 bg-white rounded-t-[3rem] rounded-b-full flex items-center justify-center shadow-xl absolute top-0 left-1/2 -translate-x-1/2">
                <img src="{{ asset('images/logo-siap.png') }}" alt="Logo SIAP" class="w-20 h-20 z-10">
            </div>
            
            {{-- 
              Beri margin-top yang cukup (mt-44) pada Judul 'SIAP' 
              agar tidak tertimpa oleh elemen logo yang 'absolute'.
              (h-40 logo + sedikit jarak)
            --}}
            <h1 class="text-white text-5xl font-bold mt-5 tracking-wide">SIAP</h1>
            <p class="text-gray-300 text-xs mt-2 tracking-wider px-4">
                Sistem Informasi Administrasi dan Pelaporan
            </p>
        </div>

        {{-- Bagian Form Login dengan rounded top --}}
        <div class="bg-white px-8 py-10 -mt-6 rounded-t-[2.5rem] relative z-10">
            
            <h2 class="text-center text-4xl font-black text-gray-900 mb-8 tracking-wide">
                LOGIN
            </h2>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-5">
                    {{-- Label username --}}
                    <label for="username" class="block text-sm font-semibold text-gray-800 mb-2 text-center tracking-wide">
                        USERNAME
                    </label>
                    <input id="username" type="text" name="username" 
                           value="{{ old('username') }}" required autofocus
                           placeholder="Username Anda"
                           class="w-full bg-gray-200 border-0 rounded-full text-gray-900 font-medium placeholder-gray-400 py-4 px-6 focus:ring-2 focus:ring-blue-500 focus:bg-gray-100 shadow-md transition-all">
                    
                    @error('username')
                        <span class="block text-red-500 text-sm mt-2 text-center">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-6" x-data="{ showPassword: false }">
                    {{-- Label password --}}
                    <label for="password" class="block text-sm font-semibold text-gray-800 mb-2 text-center tracking-wide">
                        Password
                    </label>
                    
                    <div class="relative">
                        <input id="password" name="password" required autocomplete="current-password"
                               placeholder="••••••••"
                               class="w-full bg-gray-200 border-0 rounded-full text-gray-900 font-medium placeholder-gray-400 py-4 px-6 pr-14 focus:ring-2 focus:ring-blue-500 focus:bg-gray-100 shadow-md transition-all"
                               :type="showPassword ? 'text' : 'password'">
                        
                        {{-- Icon toggle password --}}
                        <button 
                            type="button" 
                            @click="showPassword = !showPassword" 
                            class="absolute inset-y-0 right-0 px-5 flex items-center text-gray-600 hover:text-gray-800">
                            <svg x-show="!showPassword" class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243l-4.243-4.243" />
                            </svg>
                            <svg x-show="showPassword" class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="display: none;">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <span class="block text-red-500 text-sm mt-2 text-center">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mt-8 flex justify-center">
                    <button type="submit" 
                            class="w-48 text-lg py-4 px-8 border border-transparent rounded-full shadow-xl font-bold text-white bg-[#1a2847] hover:bg-[#2a3a5f] focus:outline-none focus:ring-4 focus:ring-blue-300 transition-all transform hover:scale-105 tracking-wider">
                        MASUK
                    </button>
                </div>
            </form>
        </div>

    </div> 
</div>
@endsection

