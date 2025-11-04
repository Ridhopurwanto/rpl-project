<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Halaman About - {{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @if (file_exists(public_path('build/manifest.json')))
        {{-- (Logika manifest.json kamu ada di sini) --}}
    @elseif (file_exists(public_path('hot')))
        @vite('resources/js/app.js')
    @else
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    
    <style>
        /*! Tailwindcss v4.0.7 ... */
        @layer theme {
            :root {
                /* (Sisa CSS Tailwind kamu ada di sini) */
            }
        }
    </style>
</head>

<body class="bg-[#f0f0f0] dark:bg-[#1b1b1b] text-[#1b1b1b] dark:text-[#f0f0f0]">
    <div class="flex items-center justify-center min-h-screen">
        <div class="max-w-md w-full p-8 bg-white dark:bg-gray-800 rounded-lg shadow-xl text-center">
            
            <h1 class="text-3xl font-bold">
                Halaman About
            </h1>
            
            <p class="mt-4 text-gray-600 dark:text-gray-300">
                Ini adalah file view baru kamu yang bernama <code>about.blade.php</code>.
            </p>
            
            <div class="mt-6">
                <a href="/" class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Kembali ke Home
                </a>
            </div>

        </div>
    </div>
</body>
</html>