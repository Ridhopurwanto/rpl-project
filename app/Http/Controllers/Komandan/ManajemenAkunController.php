<?php

namespace App\Http\Controllers\Komandan;

use App\Http\Controllers\Controller;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ManajemenAkunController extends Controller
{
     
    public function index()
    {
        
        $users = User::orderBy('nama_lengkap')->get();
        
        return view('komandan.akun.index', compact('users'));
    }

     
    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email'        => 'required|email|max:255|unique:pengguna,email',
            
            'username'     => 'required|string|max:255|unique:pengguna,username', 
            'password'     => ['required', 'confirmed', Password::min(8)],
            'peran'        => ['required', Rule::in(['anggota', 'komandan', 'supervisor'])],
            'status'       => ['required', Rule::in(['Aktif', 'Tidak Aktif'])],
            'tanggal_lahir'=> 'nullable|date',
            'no_hp'        => 'nullable|string|max:20',
            'alamat'       => 'nullable|string',
            'foto_profil'  => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except(['password', 'foto_profil', 'password_confirmation']);
        $data['nama_lengkap'] = strtoupper($request->nama_lengkap);

        
        $data['password'] = Hash::make($request->password);

        
        if ($request->hasFile('foto_profil')) {
            $path = $request->file('foto_profil')->store('akun', 'public');
            $data['foto_profil'] = $path;
        }

        User::create($data);

        return redirect()->route('komandan.akun.index')->with('success', 'Akun baru berhasil ditambahkan.');
    }

     
    public function update(Request $request, $id_pengguna)
    {
        $user = User::findOrFail($id_pengguna);

        $rules = [
            'nama_lengkap' => 'required|string|max:255',
            'email'        => ['required', 'email', 'max:255', Rule::unique('pengguna')->ignore($user->id_pengguna, 'id_pengguna')],
            'password'     => ['nullable', 'confirmed', Password::min(8)],
            'jenis_jadwal' => 'nullable|in:shift,non_shift',
            'status'       => ['required', Rule::in(['Aktif', 'Tidak Aktif'])],
            'tanggal_lahir'=> 'nullable|date',
            'no_hp'        => 'nullable|string|max:20',
            'alamat'       => 'nullable|string',
            'foto_profil'  => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        if ($request->filled('username')) {
            $rules['username'] = ['required', 'string', 'max:255', Rule::unique('pengguna')->ignore($user->id_pengguna, 'id_pengguna')];
        }

        $request->validate($rules);

        $data = $request->except(['password', 'foto_profil', 'password_confirmation', 'peran']);
        if (!$request->filled('username')) {
            unset($data['username']);
        }

        if ($request->has('nama_lengkap')) {
            $data['nama_lengkap'] = strtoupper($request->nama_lengkap);
        }

        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        
        if ($request->hasFile('foto_profil')) {
            
            if ($user->foto_profil) {
                if (file_exists(public_path('uploads/profil/' . $user->foto_profil))) {
                    @unlink(public_path('uploads/profil/' . $user->foto_profil));
                }
                if (Storage::disk('public')->exists($user->foto_profil)) {
                    Storage::disk('public')->delete($user->foto_profil);
                }
            }
            
            $path = $request->file('foto_profil')->store('akun', 'public');
            $data['foto_profil'] = $path;
        }

        $user->update($data);

        return redirect()->route('komandan.akun.index')->with('success', 'Data akun berhasil diperbarui.');
    }

     
    public function destroy($id_pengguna)
    {
        $user = User::findOrFail($id_pengguna);

        if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
            Storage::disk('public')->delete($user->foto_profil);
        }

        $user->delete();

        return redirect()->route('komandan.akun.index')->with('success', 'Akun berhasil dihapus.');
    }
}