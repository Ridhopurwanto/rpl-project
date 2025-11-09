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
        <script src="//unpkg.com/alpinejs" defer></script>
    </head>
    <body class="font-sans antialiased">
        {{-- 
          PERUBAHAN: 
          1. Menambahkan 'relative' agar kita bisa menempatkan tombol + (FAB)
          2. Latar belakang diatur ke bg-gray-100
        --}}
        <div class="min-h-screen flex flex-col bg-slate-900 text-white">

            <header class="w-full px-4 py-4 shadow-lg bg-slate-800/50">
                <div class="flex justify-between items-center max-w-sm mx-auto">
                    <div>
                        <p class="text-sm text-gray-300">SELAMAT DATANG,</p>
                        <h3 class="text-lg font-semibold">{{ Auth::user()->name ?? 'Pengguna' }}</h3>
                    </div>

                    <div class="flex items-center space-x-3">
                        <a href="#" class="text-gray-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </a>
                        <a href="#" class="p-1 text-gray-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341A6.002 6.002 0 006 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        </a>

                        <div x-data="{ open: false }" class="relative">
                            
                            <button @click="open = !open" class="p-1 text-gray-500 rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </button>

                            <div x-show="open"
                                @click.outside="open = false"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute right-0 z-50 mt-2 w-48 origin-top-right rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5"
                                style="display: none;"
                            >
                                <div class="py-1" role="menu" aria-orientation="vertical">
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                        Info Profil
                                    </a>

                                    {{-- Ini adalah form POST yang tersembunyi, tapi terlihat seperti link --}}
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <a href="{{ route('logout') }}"
                                        onclick="event.preventDefault(); this.closest('form').submit();"
                                        class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100" 
                                        role="menuitem">
                                            Logout
                                        </a>
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

            <footer class="mt-auto w-full text-center py-3 text-white bg-slate-900 rounded-t-2xl">
                Siap v 1.0.0
            </footer>

            {{-- 
              PERUBAHAN: 
              Menambahkan @stack untuk menampung tombol FAB (+)
            --}}
            @stack('fab')
        </div>
    </body>
</html>