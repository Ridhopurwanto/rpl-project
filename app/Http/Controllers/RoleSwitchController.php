<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class RoleSwitchController extends Controller
{
     
    public function setRole(Request $request)
    {
        
        $request->validate([
            'role' => 'required|string|in:komandan,anggota,bau',
        ]);

        $user = Auth::user();
        $desiredRole = $request->input('role');

        
        if ($user->peran != 'komandan' && $desiredRole == 'anggota') {
            abort(403, 'Aksi tidak diizinkan.');
        }

        
        
        Session::put('current_role', $desiredRole);

        
        if ($desiredRole == 'komandan') {
            return redirect()->route('komandan.dashboard');
        } elseif ($desiredRole == 'anggota') {
            return redirect()->route('anggota.dashboard');
        }

        return redirect('/'); 
    }
}