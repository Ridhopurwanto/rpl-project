<?php

use Illuminate\Support\Facades\Route;

// Semua route akan render view yang sama (SPA)
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');