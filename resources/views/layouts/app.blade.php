<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SIAP') }} - Dashboard</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
        
        <script src="//unpkg.com/alpinejs" defer></script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex flex-col max-w-sm mx-auto bg-gray-100 shadow-lg lg:max-w-none lg:mx-0 lg:shadow-none relative">
            
            <header class="w-full px-4 pt-4 pb-2">
                <div class="flex justify-between items-center">
                    @php
                            $homeRoute = 'login'; // Default jika terjadi kesalahan

                            if (Auth::check()) {
                                // --- INI YANG DIUBAH ---
                                // 1. Ambil peran DARI SESSION
                                // 2. Jika di session tidak ada, baru ambil dari database (user->peran)
                                $currentRole = session('current_role', Auth::user()->peran);
                                // --- SELESAI PERUBAHAN ---

                                if ($currentRole == 'komandan') {
                                    $homeRoute = 'komandan.dashboard';
                                } elseif ($currentRole == 'anggota') {
                                    $homeRoute = 'anggota.dashboard';
                                } elseif ($currentRole == 'bau') {
                                    $homeRoute = 'bau.dashboard';
                                }
                            }
                    @endphp
                    @section('header-left')
                        {{-- 
                            PERBAIKAN 1: Tombol HOME
                            href-nya sekarang dinamis berdasarkan peran
                        --}}
                        <a href="{{ route($homeRoute) }}" class="bg-[#2a4a6f] text-white text-sm font-semibold px-6 py-2 rounded-full shadow-md">
                            HOME
                        </a>
                    @show
                
                    

                    {{-- 
                        Blok ikon di sebelah kanan
                    --}}
                    <div class="flex items-center space-x-2">
                        
                        {{-- 
                            PERBAIKAN 2: Tombol LOGO
                            href-nya sekarang dinamis berdasarkan peran
                        --}}
                        <a href="{{ route($homeRoute) }}" class="bg-white p-2 rounded-full shadow">
                            <img src="{{ asset('images/logo-siap.png') }}" alt="Logo" class="w-6 h-6">
                        </a>
                        
                        {{-- 2. Lonceng Notifikasi --}}
                        <div x-data="{ notifOpen: false }" class="relative">
                            {{-- Tombol Lonceng --}}
                            <button @click="notifOpen = !notifOpen" class="bg-white p-2 rounded-full shadow text-gray-500 focus:outline-none relative" title="Notifikasi">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341A6.002 6.002 0 006 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                
                                {{-- Badge Merah Jumlah Notifikasi --}}
                                @if(Auth::user()->unreadNotifications->count() > 0)
                                    <span class="absolute top-0 right-0 block h-4 w-4 transform -translate-y-1/4 translate-x-1/4 rounded-full bg-red-500 text-white text-[10px] flex items-center justify-center font-bold">
                                        {{ Auth::user()->unreadNotifications->count() }}
                                    </span>
                                @endif
                            </button>

                            {{-- Dropdown Menu Notifikasi --}}
                            <div x-show="notifOpen" 
                                @click.away="notifOpen = false" 
                                x-transition
                                class="absolute top-full right-0 mt-2 w-80 bg-white rounded-lg shadow-xl z-50 overflow-hidden"
                                style="display: none;">
                                
                                {{-- Header Dropdown --}}
                                <div class="px-4 py-2 bg-gray-50 border-b border-gray-200 font-semibold text-gray-700 flex justify-between items-center">
                                    <span>Notifikasi</span>
                                    <span class="text-xs bg-gray-200 px-2 py-1 rounded-full">{{ Auth::user()->unreadNotifications->count() }} Baru</span>
                                </div>

                                {{-- List Notifikasi --}}
                                <div class="max-h-64 overflow-y-auto">
                                    @forelse(Auth::user()->notifications as $notification)
                                        <a href="{{ route('markAsRead', $notification->id) }}" 
                                        class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100 {{ $notification->read_at ? '' : 'bg-blue-50' }}">
                                            <div class="flex items-start">
                                                <div class="flex-1">
                                                    <p class="text-sm font-bold text-gray-800 flex justify-between">
                                                        {{ $notification->data['title'] ?? 'Pemberitahuan' }}
                                                        @if(is_null($notification->read_at))
                                                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-1"></span>
                                                        @endif
                                                    </p>
                                                    <p class="text-xs text-gray-600 mt-1">
                                                        {{ Str::limit($notification->data['message'] ?? '-', 50) }}
                                                    </p>
                                                    <p class="text-xs text-gray-400 mt-1 flex items-center">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        {{ $notification->created_at->diffForHumans() }}
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                    @empty
                                        <div class="px-4 py-6 text-center text-gray-500 text-sm">
                                            Tidak ada notifikasi saat ini.
                                        </div>
                                    @endforelse
                                </div>

                                {{-- Footer Dropdown --}}
                                <a href="#" class="block bg-gray-50 text-center px-4 py-2 text-sm text-blue-600 hover:text-blue-800 font-medium hover:bg-gray-100">
                                    Lihat Semua
                                </a>
                            </div>
                        </div>
                        
                        {{-- 3. Dropdown Profil (Menggunakan AlpineJS) --}}
                        {{-- Ganti 'a' tag dengan 'div' + AlpineJS --}}
                        <div x-data="{ open: false }" class="relative">
                            
                            {{-- Tombol Ikon Profil (Toggle) --}}
                            <button @click="open = !open" class="bg-white p-2 rounded-full shadow text-gray-500 focus:outline-none" title="Profil Pengguna">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </button>
            
                            {{-- Menu Dropdown --}}
                            <div x-show="open" 
                                 @click.away="open = false" 
                                 x-transition
                                 class="absolute top-full right-0 mt-2 w-60 bg-white rounded-lg shadow-xl p-4 z-50 text-gray-800"
                                 style="display: none;">
                                
                                {{-- Info Pengguna --}}
                                <div class="flex items-center space-x-3 mb-4 pb-3 border-b border-gray-200">
                                    <div class="w-14 h-14 bg-gray-200 rounded-lg flex items-center justify-center text-gray-400">
                                        {{-- Placeholder Ikon Foto --}}
                                         <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4V5h12v10zm-9.414-2.586a2 2 0 112.828 2.828L8.414 13H12v-1H6.586l1-1zM10 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg>
                                    </div>
                                    <div>
                                        {{-- Mengambil nama_lengkap atau username --}}
                                        <p class="font-bold text-gray-900">{{ Auth::user()->nama_lengkap ?? Auth::user()->username }}</p>
                                        <p class="text-sm text-gray-500 capitalize">{{ Auth::user()->peran }}</p>
                                    </div>
                                </div>
                
                                {{-- Tombol Aksi (Sesuai Permintaan) --}}
                                <div class="space-y-2">
                                    {{-- 1. INFO PROFIL --}}
                                    <a href="{{ route('profil.index') }}" class="block w-full text-center px-4 py-2 text-sm text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                                        Info Profil
                                    </a>

                                    
                                    {{-- 2. PILIH ROLE --}}
                                    {{-- Tampilkan tombol ini HANYA jika rolenya komandan --}}
                                    @if(Auth::user()->peran == 'komandan')
                                        <a href="{{ route('komandan.pilih-role') }}" class="block w-full text-center px-4 py-2 text-sm text-white bg-gray-600 rounded-lg hover:bg-gray-700">
                                            Pilih Peran
                                        </a>
                                    @endif
                
                                    {{-- 3. KELUAR --}}
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-center px-4 py-2 text-sm text-white bg-red-600 rounded-lg hover:bg-red-700">
                                            Keluar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </header>

            <main class="flex-grow px-4 py-2">
                @yield('content')
            </main>

            <footer class="mt-auto w-full text-center py-3 text-white bg-[#2a4a6f] rounded-t-2xl">
                Siap v 1.0.0
            </footer>
            
            @stack('fab')
        </div>
        @stack('scripts')
    </body>
</html>