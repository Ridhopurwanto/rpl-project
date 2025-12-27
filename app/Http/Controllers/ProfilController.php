<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User; 

class ProfilController extends Controller
{
     
    public function index()
    {
        $pengguna = Auth::user();
        return view('profil.index', compact('pengguna'));
    }

     
    public function edit()
    {
        $pengguna = Auth::user();
        return view('profil.edit', compact('pengguna'));
    }

     
    public function update(Request $request)
    {
        $pengguna = Auth::user(); 
        
        
        $request->validate([
            'nama_lengkap'  => 'required|string|max:255',
            'no_hp'         => 'nullable|string|max:20',
            'alamat'        => 'nullable|string|max:500',
            'tanggal_lahir' => 'nullable|date',
            'foto_profil'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'foto_profil.image'     => 'File harus berupa gambar.',
            'foto_profil.max'       => 'Ukuran foto maksimal 2MB.',
        ]);

        
        $dataToUpdate = [
            'nama_lengkap'  => $request->nama_lengkap,
            'no_hp'         => $request->no_hp,
            'alamat'        => $request->alamat,
            'tanggal_lahir' => $request->tanggal_lahir,
        ];

        
        if ($request->hasFile('foto_profil')) {
            
            
            if ($pengguna->foto_profil && file_exists(public_path('uploads/profil/' . $pengguna->foto_profil))) {
                @unlink(public_path('uploads/profil/' . $pengguna->foto_profil));
            }
            
            elseif ($pengguna->foto_profil && Storage::disk('public')->exists($pengguna->foto_profil)) {
                Storage::disk('public')->delete($pengguna->foto_profil);
            }

            
            
            $path = $request->file('foto_profil')->store('akun', 'public');

            
            $dataToUpdate['foto_profil'] = $path;
        }

        
        
        
        User::where('id_pengguna', $pengguna->id_pengguna)->update($dataToUpdate);

        return redirect()->route('profil.index')->with('success', 'Profil berhasil diperbarui!');
    }

     
    public function updatePassword(Request $request)
    {
        
        $request->validateWithBag('password_errors', [
            'password_lama' => 'required',
            'password_baru' => 'required|min:8|confirmed',
        ], [
            'password_lama.required' => 'Password lama harus diisi.',
            'password_baru.required' => 'Password baru harus diisi.', 
            'password_baru.min'      => 'Password baru minimal 8 karakter.',
            'password_baru.confirmed'=> 'Konfirmasi password tidak cocok.',
        ]);

        $pengguna = Auth::user();

        
        if (!Hash::check($request->password_lama, $pengguna->password)) {
            
            return back()
                ->withErrors(['password_lama' => 'Password lama yang Anda masukkan salah.'], 'password_errors')
                ->withInput();
        }

        
        
        $pengguna->forceFill([
            'password' => Hash::make($request->password_baru)
        ])->save();

        return back()->with('success', 'Password berhasil diubah!');
    }
}