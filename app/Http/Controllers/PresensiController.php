<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Presensi; // Panggil Model
use Illuminate\Support\Facades\Auth; // Untuk cek user login
use Illuminate\Support\Facades\Storage; // Untuk kelola file
use Illuminate\Support\Carbon; // Untuk kelola waktu

class PresensiController extends Controller
{
    /**
     * Menampilkan halaman laporan presensi (untuk Komandan dan BAU).
     */
    public function index(Request $request)
    {
        // Ambil tanggal dari filter. Default: hari ini.
        $tanggalFilter = $request->input('tanggal', now()->format('Y-m-d'));

        // Ambil data PRESENSI MASUK berdasarkan filter tanggal
        $dataMasuk = Presensi::whereDate('tanggal', $tanggalFilter)
                            ->whereNotNull('waktu_masuk')
                            ->orderBy('waktu_masuk', 'asc')
                            ->get();

        // Ambil data PRESENSI PULANG berdasarkan filter tanggal
        $dataPulang = Presensi::whereDate('tanggal', $tanggalFilter)
                            ->whereNotNull('waktu_pulang')
                            ->orderBy('waktu_pulang', 'asc')
                            ->get();

        // --- ▼▼▼ INI PERUBAHANNYA ▼▼▼ ---
        // Kita panggil view 'komandan.presensi' sesuai lokasi file kamu
        return view('komandan.presensi', [ 

            'dataMasuk' => $dataMasuk,
            'dataPulang' => $dataPulang,
            'tanggalTerpilih' => $tanggalFilter,
        ]);
    }

    /**
     * Menghapus data presensi (HANYA UNTUK KOMANDAN).
     */
    public function destroy($id_presensi)
    {
        // Cek keamanan: Hanya 'komandan' yang boleh menghapus
        if (Auth::user()->peran !== 'komandan') {
            return redirect()->route('komandan.presensi')->with('error', 'Anda tidak memiliki hak akses.');
        }

        try {
            $presensi = Presensi::findOrFail($id_presensi);
            
            // Hapus foto dari storage (opsional tapi disarankan)
            if ($presensi->foto_masuk) {
                Storage::disk('public')->delete($presensi->foto_masuk);
            }
            if ($presensi->foto_pulang) {
                Storage::disk('public')->delete($presensi->foto_pulang);
            }

            // Hapus data dari database
            $presensi->delete();
            
            return redirect()->back()->with('success', 'Data presensi berhasil dihapus.');
        
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data.');
        }
    }

    public function edit($id_presensi)
    {
        if (Auth::user()->peran !== 'komandan') {
            return redirect()->route('komandan.presensi')->with('error', 'Anda tidak memiliki hak akses.');
        }

        try {
            $presensi = Presensi::findOrFail($id_presensi);
            // Kirim data presensi ke view 'komandan.presensi_edit'
            return view('komandan.presensi_edit', ['presensi' => $presensi]);
        } catch (\Exception $e) {
            return redirect()->route('komandan.presensi')->with('error', 'Data presensi tidak ditemukan.');
        }
    }

    public function update(Request $request, $id_presensi)
    {
        if (Auth::user()->peran !== 'komandan') {
            return redirect()->route('komandan.presensi')->with('error', 'Anda tidak memiliki hak akses.');
        }

        // Validasi input
        $request->validate([
            'waktu_masuk' => 'required|date',
            'waktu_pulang' => 'nullable|date|after_or_equal:waktu_masuk',
            'status' => 'required|in:tepat waktu,terlambat,terlalu cepat,izin',
        ]);

        try {
            $presensi = Presensi::findOrFail($id_presensi);
            
            $presensi->update([
                'waktu_masuk' => $request->waktu_masuk,
                'waktu_pulang' => $request->waktu_pulang,
                'status' => $request->status,
                'tanggal' => Carbon::parse($request->waktu_masuk)->format('Y-m-d'), // Update tanggal
            ]);

            return redirect()->route('komandan.presensi')->with('success', 'Data presensi berhasil diperbarui.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }


    // --- FUNGSI UNTUK ANGGOTA ---
    // (Kode createForAnggota dan storeForAnggota tetap sama)
    
    public function createForAnggota()
    {
        $userId = Auth::id(); 
        $presensiHariIni = Presensi::where('id_pengguna', $userId)
                                    ->whereDate('tanggal', now())
                                    ->first();
        
        return view('anggota.presensi', [ // Ini akan memanggil 'anggota/presensi.blade.php'
            'presensiHariIni' => $presensiHariIni
        ]);
    }

    public function storeForAnggota(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $userId = Auth::id();
        $namaLengkap = Auth::user()->nama_lengkap ?? Auth::user()->name; 
        $today = now()->format('Y-m-d');
        $path = $request->file('foto')->store('presensi', 'public');

        $presensiHariIni = Presensi::where('id_pengguna', $userId)
                                    ->whereDate('tanggal', $today)
                                    ->first();

        try {
            if (!$presensiHariIni) {
                // --- LOGIKA ABSEN MASUK ---
                $status = (now()->format('H:i:s') > '07:00:00') ? 'terlambat' : 'tepat waktu';
                Presensi::create([
                    'id_pengguna' => $userId,
                    'nama_lengkap' => $namaLengkap,
                    'waktu_masuk' => now(),
                    'foto_masuk' => $path,
                    'status' => $status,
                    'tanggal' => $today,
                ]);
                return redirect()->back()->with('success', 'Berhasil melakukan presensi masuk.');

            } elseif (!$presensiHariIni->waktu_pulang) {
                // --- LOGIKA ABSEN PULANG ---
                $presensiHariIni->update([
                    'waktu_pulang' => now(),
                    'foto_pulang' => $path,
                ]);
                return redirect()->back()->with('success', 'Berhasil melakukan presensi pulang.');
            } else {
                return redirect()->back()->with('error', 'Anda sudah menyelesaikan presensi hari ini.');
            }
        } catch (\Exception $e) {
            Storage::disk('public')->delete($path);
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}