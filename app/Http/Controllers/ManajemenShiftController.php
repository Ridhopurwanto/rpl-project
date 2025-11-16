<?php

namespace App\Http\Controllers;

use App\Models\User;  
use App\Models\Shift; 
use Illuminate\Http\Request;

class ManajemenShiftController extends Controller
{

    public function index($id_pengguna)
    {
        $user = User::findOrFail($id_pengguna); // DIUBAH: Menggunakan model User

        // AMBIL DATA SHIFT: Menggunakan relasi 'shifts()' dari model User Anda
        $shifts = $user->shifts()->get();
        
        // Anda bisa juga mengambil data shift untuk bulan tertentu,
        // tapi untuk saat ini kita ambil semua
        // $shifts = $user->shifts()
        //               ->whereYear('tanggal', date('Y'))
        //               ->whereMonth('tanggal', date('m'))
        //               ->get();

        return view('komandan.akun.shift', compact('user', 'shifts'));
    }

}