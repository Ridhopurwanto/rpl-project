<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

// ↓↓↓ TAMBAHKAN BARIS DI BAWAH INI ↓↓↓
Route::get('/about', function () {
    return view('about'); // Ini akan memanggil file 'resources/views/about.blade.php'
});