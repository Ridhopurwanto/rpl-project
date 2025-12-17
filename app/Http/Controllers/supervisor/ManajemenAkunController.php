<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ManajemenAkunController extends Controller
{
    /**
     * Menampilkan daftar akun pengguna.
     */
    public function index()
    {
        // Mengambil semua user diurutkan berdasarkan nama
        $users = User::orderBy('nama_lengkap')->get();
        
        return view('supervisor.akun.index', compact('users'));
    }


}