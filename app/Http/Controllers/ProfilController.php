<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ProfilController extends Controller
{
    /**
     * Tampilkan halaman info profil
     */
    public function index()
    {
        $pengguna = Auth::user();
        return view('profil.index', compact('pengguna'));
    }

    /**
     * Tampilkan halaman edit profil
     */
    public function edit()
    {
        $pengguna = Auth::user();
        return view('profil.edit', compact('pengguna'));
    }

    /**
     * Update data profil
     */
    public function update(Request $request)
    {
        $pengguna = Auth::user();
        
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:pengguna,email,' . $pengguna->id_pengguna . ',id_pengguna',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->only(['nama_lengkap', 'email', 'no_hp', 'alamat', 'tanggal_lahir']);

        // Handle upload foto profil
        if ($request->hasFile('foto_profil')) {
            $file = $request->file('foto_profil');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/profil'), $filename);
            $data['foto_profil'] = $filename;
            
            // Hapus foto lama jika ada
            if ($pengguna->foto_profil && file_exists(public_path('uploads/profil/' . $pengguna->foto_profil))) {
                unlink(public_path('uploads/profil/' . $pengguna->foto_profil));
            }
        }

        User::where('id_pengguna', $pengguna->id_pengguna)->update($data);

        return redirect()->route('profil.index')->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'password_lama' => 'required',
            'password_baru' => 'required|min:8|confirmed',
        ]);

        $pengguna = Auth::user();

        // Cek password lama
        if (!Hash::check($request->password_lama, $pengguna->password)) {
            return back()->withErrors(['password_lama' => 'Password lama tidak sesuai']);
        }

        // Update password baru
        User::where('id_pengguna', $pengguna->id_pengguna)->update([
            'password' => Hash::make($request->password_baru)
        ]);

        return redirect()->route('profil.index')->with('success', 'Password berhasil diubah!');
    }
}
