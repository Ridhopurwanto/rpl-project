<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User; // Pastikan Model User diimport

class ProfilController extends Controller
{
    /**
     * Tampilkan halaman info profil (Read Only)
     */
    public function index()
    {
        $pengguna = Auth::user();
        return view('profil.index', compact('pengguna'));
    }

    /**
     * Tampilkan halaman edit profil (Form)
     */
    public function edit()
    {
        $pengguna = Auth::user();
        return view('profil.edit', compact('pengguna'));
    }

    /**
     * Proses Update Data Diri (Foto, Nama, HP, Alamat)
     */
    public function update(Request $request)
    {
        $pengguna = Auth::user(); // Ambil user yang sedang login
        
        // 1. Validasi Input
        $request->validate([
            'nama_lengkap'  => 'required|string|max:255',
            'no_hp'         => 'nullable|string|max:20',
            'alamat'        => 'nullable|string|max:500',
            'tanggal_lahir' => 'nullable|date',
            'foto_profil'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'foto_profil.image'     => 'File harus berupa gambar.',
            'foto_profil.max'       => 'Ukuran foto maksimal 2MB.',
        ]);

        // 2. Siapkan data untuk diupdate
        $dataToUpdate = [
            'nama_lengkap'  => $request->nama_lengkap,
            'no_hp'         => $request->no_hp,
            'alamat'        => $request->alamat,
            'tanggal_lahir' => $request->tanggal_lahir,
        ];

        // 3. Cek apakah ada upload foto baru
        if ($request->hasFile('foto_profil')) {
            
            // A. Hapus foto lama (Cek Legacy Path: public/uploads/profil)
            if ($pengguna->foto_profil && file_exists(public_path('uploads/profil/' . $pengguna->foto_profil))) {
                @unlink(public_path('uploads/profil/' . $pengguna->foto_profil));
            }
            // B. Hapus foto lama (Cek Storage Path: storage/app/public/...)
            elseif ($pengguna->foto_profil && Storage::disk('public')->exists($pengguna->foto_profil)) {
                Storage::disk('public')->delete($pengguna->foto_profil);
            }

            // Simpan foto baru menggunakan Storage (Standard ManajemenAkunController)
            // Path otomatis: storage/app/public/akun/filename.ext
            $path = $request->file('foto_profil')->store('akun', 'public');

            // Masukkan path relative ke array update (Contoh: akun/xyz.jpg)
            $dataToUpdate['foto_profil'] = $path;
        }

        // 4. Update Database
        // Pastikan pakai ID yang benar. Kalau di tabelmu primary key-nya 'id', ganti 'id_pengguna' jadi 'id'
        // Asumsi di sini primary key kamu adalah 'id_pengguna' sesuai kode awalmu.
        User::where('id_pengguna', $pengguna->id_pengguna)->update($dataToUpdate);

        return redirect()->route('profil.index')->with('success', 'Profil berhasil diperbarui!');
    }

    /** 
     * Proses Update Password
    */
    public function updatePassword(Request $request)
    {
        // 1. Validasi Input (Masuk ke bag 'password_errors')
        $request->validateWithBag('password_errors', [
            'password_lama' => 'required',
            'password_baru' => 'required|min:8|confirmed',
        ], [
            'password_lama.required' => 'Password lama harus diisi.',
            'password_baru.required' => 'Password baru harus diisi.', // Tambahan
            'password_baru.min'      => 'Password baru minimal 8 karakter.',
            'password_baru.confirmed'=> 'Konfirmasi password tidak cocok.',
        ]);

        $pengguna = Auth::user();

        // 2. Cek apakah password lama benar
        if (!Hash::check($request->password_lama, $pengguna->password)) {
            // PERBAIKAN: Tambahkan argumen kedua 'password_errors' agar masuk ke tas yang benar
            return back()
                ->withErrors(['password_lama' => 'Password lama yang Anda masukkan salah.'], 'password_errors')
                ->withInput();
        }

        // 3. Update Password Baru
        // Gunakan object user langsung agar lebih aman & clean
        $pengguna->forceFill([
            'password' => Hash::make($request->password_baru)
        ])->save();

        return back()->with('success', 'Password berhasil diubah!');
    }
}