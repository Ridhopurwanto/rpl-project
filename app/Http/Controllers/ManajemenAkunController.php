<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ManajemenAkunController extends Controller
{
    /**
     * Menampilkan daftar akun pengguna.
     */
    public function index()
    {
        // Mengambil semua user diurutkan berdasarkan nama
        $users = User::orderBy('nama_lengkap')->get();
        
        return view('komandan.akun.index', compact('users'));
    }

    /**
     * Menyimpan data akun baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email'        => 'required|email|max:255|unique:pengguna,email',
            // Pastikan tabel di DB bernama 'users' atau sesuaikan 'unique:users,username'
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

        // Hash password
        $data['password'] = Hash::make($request->password);

        // Upload Foto
        if ($request->hasFile('foto_profil')) {
            $path = $request->file('foto_profil')->store('akun', 'public');
            $data['foto_profil'] = $path;
        }

        User::create($data);

        return redirect()->route('komandan.akun.index')->with('success', 'Akun baru berhasil ditambahkan.');
    }

    /**
     * Memperbarui data akun.
     */
    public function update(Request $request, $id_pengguna)
    {
        $user = User::findOrFail($id_pengguna);

        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            // Validasi unique mengecualikan ID pengguna saat ini
            'email'        => ['required', 'email', 'max:255', Rule::unique('pengguna')->ignore($user->id_pengguna, 'id_pengguna')],
            'username'     => ['required', 'string', 'max:255', Rule::unique('pengguna')->ignore($user->id_pengguna, 'id_pengguna')],
            'password'     => ['nullable', 'confirmed', Password::min(8)],
            'jenis_jadwal' => 'nullable|in:shift,non_shift',
            'status'       => ['required', Rule::in(['Aktif', 'Tidak Aktif'])],
            'tanggal_lahir'=> 'nullable|date',
            'no_hp'        => 'nullable|string|max:20',
            'alamat'       => 'nullable|string',
            'foto_profil'  => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except(['password', 'foto_profil', 'password_confirmation', 'peran']);
        if ($request->has('nama_lengkap')) {
            $data['nama_lengkap'] = strtoupper($request->nama_lengkap);
        }

        // Update password hanya jika diisi
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Update Foto
        if ($request->hasFile('foto_profil')) {
            // Hapus foto lama (Legacy & Storage)
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

    /**
     * Menghapus akun.
     */
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