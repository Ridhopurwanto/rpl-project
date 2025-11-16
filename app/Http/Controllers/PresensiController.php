<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Presensi; // Panggil Model Presensi
use App\Models\Shift;    // Panggil Model Shift (PENTING)
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class PresensiController extends Controller
{
    /**
     * Menampilkan halaman laporan presensi (untuk Komandan dan BAU).
     *
     * INI FUNGSI YANG DIPERBARUI SECARA TOTAL
     */
    public function index(Request $request)
    {
        // Ambil filter tanggal, default: hari ini.
        $tanggalFilter = $request->input('tanggal', now()->format('Y-m-d'));
        
        // Ambil filter shift, default: 'semua'.
        $shiftFilter = $request->input('shift', 'semua');

        // Query dasar: Gabungkan Presensi dengan Shift
        $query = Presensi::join('shift', 'presensi.id_shift', '=', 'shift.id_shift')
                         ->whereDate('presensi.tanggal', $tanggalFilter)
                         ->select('presensi.*', 'shift.jenis_shift'); // Pilih kolom

        // Terapkan filter shift jika bukan 'semua'
        if ($shiftFilter !== 'semua') {
            $query->where('shift.jenis_shift', $shiftFilter);
        }

        // Clone query dasar untuk memisahkan Masuk dan Pulang
        // Clone PENTING agar filter tidak tumpang tindih
        $queryMasuk = clone $query;
        $queryPulang = clone $query;

        // Ambil data PRESENSI MASUK
        $dataMasuk = $queryMasuk->where('presensi.jenis_presensi', 'Masuk')
                               ->orderBy('presensi.waktu', 'asc')
                               ->get();

        // Ambil data PRESENSI PULANG
        $dataPulang = $queryPulang->where('presensi.jenis_presensi', 'Pulang')
                                 ->orderBy('presensi.waktu', 'asc')
                                 ->get();

        // Kirim data ke view
        return view('komandan.presensi', [
            'dataMasuk' => $dataMasuk,
            'dataPulang' => $dataPulang,
            'tanggalTerpilih' => $tanggalFilter,
            'shiftTerpilih' => $shiftFilter, // Ganti nama variabel agar sesuai view
        ]);
    }

    /**
     * Menghapus data presensi (HANYA UNTUK KOMANDAN).
     *
     * DIPERBARUI: 'foto_masuk' -> 'foto'
     */
    public function destroy($id_presensi)
    {
        if (Auth::user()->peran !== 'komandan') {
            return redirect()->route('komandan.presensi')->with('error', 'Anda tidak memiliki hak akses.');
        }

        try {
            $presensi = Presensi::findOrFail($id_presensi);
            
            // Hapus foto dari storage (disesuaikan ke 1 kolom 'foto')
            if ($presensi->foto) {
                Storage::disk('public')->delete($presensi->foto);
            }

            $presensi->delete();
            
            return redirect()->back()->with('success', 'Data presensi berhasil dihapus.');
        
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data.');
        }
    }

    /**
     * Fungsi edit() tidak diperlukan lagi karena kita pakai MODAL.
     * Hapus fungsi 'edit()' yang lama.
     */
    // public function edit(...) { ... }

    /**
     * Mengupdate data presensi (HANYA UNTUK KOMANDAN).
     *
     * DIPERBARUI: Disesuaikan dengan modal dan database baru.
     */
    public function update(Request $request, $id_presensi)
    {
        if (Auth::user()->peran !== 'komandan') {
            return redirect()->route('komandan.presensi')->with('error', 'Anda tidak memiliki hak akses.');
        }

        // Validasi input (disesuaikan dengan field modal)
        $request->validate([
            'waktu' => 'required|date',
            'status' => 'required|in:tepat waktu,terlambat,terlalu cepat,izin',
            'jenis_presensi' => 'required|in:Masuk,Pulang',
        ]);

        try {
            $presensi = Presensi::findOrFail($id_presensi);
            
            $presensi->update([
                'waktu' => $request->waktu,
                'status' => $request->status,
                'jenis_presensi' => $request->jenis_presensi,
                'tanggal' => Carbon::parse($request->waktu)->format('Y-m-d'), // Update tanggal
            ]);

            return redirect()->back()->with('success', 'Data presensi berhasil diperbarui.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }


    // --- FUNGSI UNTUK ANGGOTA ---
    // (DIPERBARUI TOTAL AGAR SESUAI DB BARU)
    
    /**
     * Menampilkan halaman presensi untuk Anggota.
     */
    public function createForAnggota()
    {
        $userId = Auth::id(); 
        $today = now()->format('Y-m-d');

        // 1. Cek jadwal shift hari ini
        $shiftHariIni = Shift::where('id_pengguna', $userId)
                             ->whereDate('tanggal', $today)
                             ->first();

        // 2. Cek apakah sudah presensi Masuk
        $presensiMasuk = Presensi::where('id_pengguna', $userId)
                                 ->whereDate('tanggal', $today)
                                 ->where('jenis_presensi', 'Masuk')
                                 ->first();
        
        // 3. Cek apakah sudah presensi Pulang
        $presensiPulang = Presensi::where('id_pengguna', $userId)
                                  ->whereDate('tanggal', $today)
                                  ->where('jenis_presensi', 'Pulang')
                                  ->first();
        
        return view('anggota.presensi', [
            'shiftHariIni' => $shiftHariIni,
            'presensiMasuk' => $presensiMasuk,
            'presensiPulang' => $presensiPulang,
        ]);
    }

    /**
     * Menyimpan data presensi dari Anggota (Masuk atau Pulang).
     */
    public function storeForAnggota(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $userId = Auth::id();
        $namaLengkap = Auth::user()->nama_lengkap ?? Auth::user()->username; 
        $today = now()->format('Y-m-d');
        
        // Cek shift hari ini
        $shiftHariIni = Shift::where('id_pengguna', $userId)
                             ->whereDate('tanggal', $today)
                             ->first();

        // Validasi 1: Apakah ada jadwal shift?
        if (!$shiftHariIni) {
            return redirect()->back()->with('error', 'Anda tidak memiliki jadwal shift hari ini.');
        }

        // Validasi 2: Apakah shift-nya 'Off'?
        if ($shiftHariIni->jenis_shift == 'Off') {
            return redirect()->back()->with('error', 'Anda sedang libur (Off) hari ini.');
        }

        // Cek data presensi yang sudah ada
        $presensiMasuk = Presensi::where('id_pengguna', $userId)
                                 ->whereDate('tanggal', $today)
                                 ->where('jenis_presensi', 'Masuk')
                                 ->first();
        
        $presensiPulang = Presensi::where('id_pengguna', $userId)
                                  ->whereDate('tanggal', $today)
                                  ->where('jenis_presensi', 'Pulang')
                                  ->first();

        // Simpan foto
        $path = $request->file('foto')->store('presensi', 'public');

        try {
            if (!$presensiMasuk) {
                // --- LOGIKA ABSEN MASUK ---
                
                // Tentukan batas waktu (Contoh: Pagi 07:00, Malam 19:00)
                $batasMasuk = ($shiftHariIni->jenis_shift == 'Pagi') ? '07:00:00' : '19:00:00';
                $status = (now()->format('H:i:s') > $batasMasuk) ? 'terlambat' : 'tepat waktu';

                Presensi::create([
                    'id_pengguna' => $userId,
                    'id_shift' => $shiftHariIni->id_shift, // <--- PENTING
                    'nama_lengkap' => $namaLengkap,
                    'waktu' => now(),
                    'foto' => $path,
                    'status' => $status,
                    'jenis_presensi' => 'Masuk', // <--- PENTING
                    'tanggal' => $today,
                ]);
                return redirect()->back()->with('success', 'Berhasil melakukan presensi MASUK.');

            } elseif (!$presensiPulang) {
                // --- LOGIKA ABSEN PULANG ---
                
                // Tentukan batas waktu (Contoh: Pagi 19:00, Malam 07:00 besok)
                $batasPulang = ($shiftHariIni->jenis_shift == 'Pagi') ? '19:00:00' : '07:00:00';
                $status = (now()->format('H:i:s') < $batasPulang && $shiftHariIni->jenis_shift == 'Pagi') ? 'terlalu cepat' : 'tepat waktu';

                Presensi::create([
                    'id_pengguna' => $userId,
                    'id_shift' => $shiftHariIni->id_shift, // <--- PENTING
                    'nama_lengkap' => $namaLengkap,
                    'waktu' => now(),
                    'foto' => $path,
                    'status' => $status,
                    'jenis_presensi' => 'Pulang', // <--- PENTING
                    'tanggal' => $today,
                ]);
                return redirect()->back()->with('success', 'Berhasil melakukan presensi PULANG.');
            } else {
                // Sudah Masuk dan Pulang
                Storage::disk('public')->delete($path); // Hapus foto yg terupload krn tidak jadi
                return redirect()->back()->with('error', 'Anda sudah menyelesaikan presensi hari ini.');
            }
        } catch (\Exception $e) {
            Storage::disk('public')->delete($path);
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}