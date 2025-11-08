@extends('layouts.guest')

@section('content')
{{-- 
    Wrapper untuk menengahkan card di layar.
    Kita berikan background abu-abu terang (bg-gray-100) dari layout.
--}}
<div class="min-h-screen flex items-center justify-center px-4 py-12">
    
    {{-- 
        INI ADALAH CARD UTAMA (SATU BUAH)
        - w-full max-w-sm -> Responsif
        - rounded-lg -> Memberi sudut bulat pada keseluruhan card
        - shadow-xl -> Bayangan yang jelas
        - overflow-hidden -> SANGAT PENTING: Ini akan "memotong" bagian 
                            biru di atas agar pas dengan sudut bulat card.
    --}}
    <div class="w-full max-w-sm mx-auto shadow-xl rounded-lg overflow-hidden">

        <div class="bg-slate-900 pt-12 pb-8 text-white text-center">
            
            <div class="flex justify-center">
                <img src="{{ asset('images/logo-siap.png') }}" alt="Logo SIAP" class="w-24 h-24">
            </div>
            
            <h1 class="text-white text-5xl font-bold mt-4">SIAP</h1>
            <p class="text-gray-300 text-sm mt-1 tracking-wide">
                Sistem Informasi Administrasi dan Pelaporan
            </p>
        </div>

        <div class="bg-white px-6 py-8">
            
            <h2 class="text-center text-3xl font-extrabold text-gray-900 mb-6">
                LOGIN
            </h2>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div>
                    {{-- 
                      PERUBAHAN: Label kini 'text-center'
                    --}}
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1 text-center">
                        USERNAME
                    </label>
                    <input id="username" type="text" name="username" 
                           value="{{ old('username', 'M. SONY') }}" required autofocus
                           placeholder="Username Anda"
                           class="w-full bg-gray-200 border-0 rounded-full text-gray-900 placeholder-gray-500 py-3 px-5 focus:ring-2 focus:ring-blue-500 shadow-sm">
                    
                    @error('username')
                        <span class="block text-red-500 text-sm mt-2 text-center">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mt-4" x-data="{ showPassword: false }">
                    {{-- 
                      PERUBAHAN: Label kini 'text-center'
                    --}}
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1 text-center">
                        Password
                    </label>
                    
                    <div class="relative">
                        <input id="password" name="password" required autocomplete="current-password"
                               placeholder="••••••••"
                               class="w-full bg-gray-200 border-0 rounded-full text-gray-900 placeholder-gray-500 py-3 px-5 focus:ring-2 focus:ring-blue-500 shadow-sm"
                               :type="showPassword ? 'text' : 'password'">
                        
                        <button 
                            type="button" 
                            @click="showPassword = !showPassword" 
                            class="absolute inset-y-0 right-0 px-4 flex items-center text-gray-600">
                            
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

                <div class="mt-8">
                    <button type="submit" 
                            class="w-full flex items-center justify-center text-base py-3 px-4 border border-transparent rounded-full shadow-lg font-medium text-white bg-slate-800 hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500">
                        MASUK
                    </button>
                </div>
            </form>
        </div>

    </div> </div>
@endsection