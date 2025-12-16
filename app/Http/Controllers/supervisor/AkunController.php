<?php

namespace App\Http\Controllers\Bau;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AkunController extends Controller
{
    /**
     * Menampilkan daftar akun pengguna (Read Only).
     */
    public function index()
    {
        // Mengambil semua user diurutkan berdasarkan nama
        $users = User::orderBy('nama_lengkap')->get();
        
        return view('bau.akun.index', compact('users'));
    }
}
