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

    public function index()
    {
        // DIUBAH: Menggunakan model User dan variabel $users
        $users = User::orderBy('nama_lengkap')->get();
        
        // Tampilkan view dan kirim data pengguna
        return view('komandan.akun.index', compact('users'));
    }

    /**
     * Menyimpan data akun baru (dari modal "Tambah Akun").
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:pengguna,username',
            'password' => ['required', 'confirmed', Password::min(8)],
            'peran' => ['required', Rule::in(['anggota', 'komandan', 'bau'])],
            'status' => ['required', Rule::in(['Aktif', 'Tidak Aktif'])],
            'tanggal_lahir' => 'nullable|date',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except(['password', 'foto_profil', 'password_confirmation']);

        // Hash password
        $data['password'] = Hash::make($request->password);

        // Handle upload foto profil
        if ($request->hasFile('foto_profil')) {
            $path = $request->file('foto_profil')->store('akun', 'public');
            $data['foto_profil'] = $path;
        }

        User::create($data); // DIUBAH: Menggunakan model User

        return redirect()->route('komandan.akun.index')->with('success', 'Akun baru berhasil ditambahkan.');
    }

    /**
     * Memperbarui data akun (dari modal "Edit Akun").
     */
    public function update(Request $request, $id_pengguna)
    {
        $user = User::findOrFail($id_pengguna); // DIUBAH: Menggunakan model User

        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('pengguna')->ignore($user->id_pengguna, 'id_pengguna')],
            'password' => ['nullable', 'confirmed', Password::min(8)], // Password opsional
            'peran' => ['required', Rule::in(['anggota', 'komandan', 'bau'])],
            'status' => ['required', Rule::in(['Aktif', 'Tidak Aktif'])],
            'tanggal_lahir' => 'nullable|date',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except(['password', 'foto_profil', 'password_confirmation']);

        // Cek jika password diisi, hash password baru
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Handle upload foto profil baru
        if ($request->hasFile('foto_profil')) {
            // Hapus foto lama jika ada
            if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
                Storage::disk('public')->delete($user->foto_profil);
            }
            // Simpan foto baru
            $path = $request->file('foto_profil')->store('akun', 'public');
            $data['foto_profil'] = $path;
        }

        $user->update($data);

        return redirect()->route('komandan.akun.index')->with('success', 'Data akun berhasil diperbarui.');
    }

    /**
     * Menghapus data akun (sesuai diagram aktivitas).
     */
    public function destroy($id_pengguna)
    {
        $user = User::findOrFail($id_pengguna); // DIUBAH: Menggunakan model User

        // Hapus foto profil dari storage
        if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
            Storage::disk('public')->delete($user->foto_profil);
        }

        $user->delete();

        return redirect()->route('komandan.akun.index')->with('success', 'Akun berhasil dihapus.');
    }
}