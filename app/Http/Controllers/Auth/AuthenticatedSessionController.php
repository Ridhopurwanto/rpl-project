<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
     
    public function create()
    {
        return view('auth.login');
    }

     
    public function store(Request $request)
    {
        
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        
        $credentials = $request->only('username', 'password');

        
        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            
            
            throw ValidationException::withMessages([
                'username' => 'Username/Password Salah', 
            ]);
        }

        
        $user = Auth::user();

        if ($user->status !== 'Aktif') {
            
            
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            
            throw ValidationException::withMessages([
                'username' => 'Akun Anda Tidak Valid',
            ]);
        }
        
        
        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'username' => __('auth.failed'),
            ]);
        }

        
        $request->session()->regenerate();

        
        $user = Auth::user();

        $request->session()->put('current_role', $user->peran);
        
        
        switch (strtolower($user->peran)) { 
            case 'anggota':
                return redirect()->route('anggota.dashboard');
            case 'komandan':
                return redirect()->route('komandan.pilih-role');
            case 'supervisor':
                return redirect()->route('supervisor.dashboard');
            default:
                Auth::logout();
                return redirect('/login')->withErrors(['username' => 'Role tidak valid.']);
        }
    }
    
     
    public function destroy(Request $request)
    {
        $request->session()->forget('current_role');

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/'); 
    }
}
