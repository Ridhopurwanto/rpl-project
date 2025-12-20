<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ManajemenAkunController extends Controller
{
    /**
     * Menampilkan daftar akun pengguna.
     */
    public function index()
    {
        // Mengambil semua user diurutkan berdasarkan nama
        $users = User::orderBy('nama_lengkap')->get();
        
        return view('supervisor.akun.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('supervisor.akun.create', ['isEdit' => false]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email'        => 'required|email|unique:pengguna,email',
            'username'     => 'required|string|max:255|unique:pengguna,username',
            'password'     => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::min(8)],
            'peran'        => ['required', \Illuminate\Validation\Rule::in(['anggota', 'komandan', 'supervisor'])],
            'jenis_jadwal' => 'nullable|in:shift,non_shift',
            'status'       => 'required|in:Aktif,Tidak Aktif',
            'tanggal_lahir'=> 'nullable|date',
            'no_hp'        => 'nullable|string|max:20',
            'foto_profil'  => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except(['password', 'foto_profil', 'password_confirmation']);
        $data['password'] = \Illuminate\Support\Facades\Hash::make($request->password);

        // Upload Foto
        if ($request->hasFile('foto_profil')) {
            $path = $request->file('foto_profil')->store('akun', 'public');
            $data['foto_profil'] = $path;
        }

        User::create($data);

        return redirect()->route('supervisor.akun.index')->with('success', 'Akun berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id_pengguna)
    {
        $user = User::findOrFail($id_pengguna);
        return view('supervisor.akun.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id_pengguna)
    {
        $user = User::findOrFail($id_pengguna);

        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email'        => ['required', 'email', 'max:255', \Illuminate\Validation\Rule::unique('pengguna')->ignore($user->id_pengguna, 'id_pengguna')],
            'username'     => ['required', 'string', 'max:255', \Illuminate\Validation\Rule::unique('pengguna')->ignore($user->id_pengguna, 'id_pengguna')],
            'password'     => ['nullable', 'confirmed', \Illuminate\Validation\Rules\Password::min(8)],
            'jenis_jadwal' => 'nullable|in:shift,non_shift',
            'status'       => ['required', \Illuminate\Validation\Rule::in(['Aktif', 'Tidak Aktif'])],
            'tanggal_lahir'=> 'nullable|date',
            'no_hp'        => 'nullable|string|max:20',
            'foto_profil'  => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Peran tidak diupdate sesuai request user
        $data = $request->except(['password', 'foto_profil', 'password_confirmation', 'peran']);

        // Update password hanya jika diisi
        if ($request->filled('password')) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        // Update Foto
        if ($request->hasFile('foto_profil')) {
            // Hapus foto lama (Legacy & Storage)
            if ($user->foto_profil) {
                if (file_exists(public_path('uploads/profil/' . $user->foto_profil))) {
                    @unlink(public_path('uploads/profil/' . $user->foto_profil));
                }
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($user->foto_profil)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($user->foto_profil);
                }
            }
            
            $path = $request->file('foto_profil')->store('akun', 'public');
            $data['foto_profil'] = $path;
        }

        $user->update($data);

        return redirect()->route('supervisor.akun.index')->with('success', 'Akun berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id_pengguna)
    {
        $user = User::findOrFail($id_pengguna);

        if ($user->foto_profil && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->foto_profil)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->foto_profil);
        }

        $user->delete();

        return redirect()->route('supervisor.akun.index')->with('success', 'Akun berhasil dihapus.');
    }
}