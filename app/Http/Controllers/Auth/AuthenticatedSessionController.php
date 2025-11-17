<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Menampilkan view login.
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Menangani percobaan autentikasi.
     */
    public function store(Request $request)
    {
        // 1. Validasi input (Tetap sama)
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // 2. Siapkan kredensial (Tetap sama)
        $credentials = $request->only('username', 'password');

        // 3. TAHAP 1: Cek Username & Password dulu
        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            
            // Jika gagal di sini, pasti karena Username/Password salah
            throw ValidationException::withMessages([
                'username' => 'Username/Password Salah', 
            ]);
        }

        // 4. TAHAP 2: Cek Status Akun secara manual setelah berhasil login sementara
        $user = Auth::user();

        if ($user->status !== 'Aktif') {
            // Login berhasil secara password, tapi statusnya mati.
            // Kita paksa logout lagi.
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Tampilkan pesan khusus status
            throw ValidationException::withMessages([
                'username' => 'Akun Anda Tidak Valid',
            ]);
        }
        
        // 3. Coba lakukan login (Tetap sama, 'Auth' akan diatur di Model)
        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'username' => __('auth.failed'),
            ]);
        }

        // 5. Regenerasi session (Tetap sama)
        $request->session()->regenerate();

        // 6. LOGIKA REDIRECT BARU (INI YANG BERUBAH)
        $user = Auth::user();

        $request->session()->put('current_role', $user->peran);
        
        // Menggunakan kolom 'peran' dari tabel baru Anda
        switch (strtolower($user->peran)) { // <--- PERUBAHAN DARI 'jabatan'
            case 'anggota':
                return redirect()->route('anggota.dashboard');
            case 'komandan':
                return redirect()->route('komandan.pilih-role');
            case 'bau':
                return redirect()->route('bau.dashboard');
            default:
                Auth::logout();
                return redirect('/login')->withErrors(['username' => 'Role tidak valid.']);
        }
    }
    
    /**
     * Menghancurkan session (logout).
     */
    public function destroy(Request $request)
    {
        $request->session()->forget('current_role');

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/'); // Redirect ke halaman utama
    }
}
