<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-t8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SIAP') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <script src="//unpkg.com/alpinejs" defer></script>
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gray-100">
        {{-- 
            Seluruh halaman akan dikontrol oleh file yang
            meng-extend layout ini. 
            Latar belakang utama kita set ke abu-abu terang.
        --}}
        @yield('content')
    </body>
</html>