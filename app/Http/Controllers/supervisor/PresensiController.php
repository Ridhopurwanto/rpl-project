<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Presensi; // Panggil Model Presensi
use App\Models\Shift;    // Panggil Model Shift (PENTING)
use App\Models\ShiftRule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class PresensiController extends Controller
{
    /**
     * Menampilkan halaman laporan presensi (untuk Supervisor).
     *
     * INI FUNGSI YANG DIPERBARUI SECARA TOTAL
     */
    public function index(Request $request)
    {
        // Ambil filter tanggal, default: hari ini.
        $tanggalFilter = $request->input('tanggal', now()->format('Y-m-d'));
        
        // Ambil filter shift, default: 'semua'.
        $shiftFilter = $request->input('shift', 'semua');
        
        $perPage = $request->input('per_page', 10);

        // Query dasar: Gabungkan Presensi dengan Shift
        $query = Presensi::join('shift', 'presensi.id_shift', '=', 'shift.id_shift')
                         ->whereDate('presensi.tanggal', $tanggalFilter)
                         ->select('presensi.*', 'shift.jenis_shift'); // Pilih kolom    

        $shiftId = ShiftRule::where('jenis_shift', $shiftFilter)
                                  ->first();

        // Terapkan filter shift jika bukan 'semua'
        if ($shiftFilter !== 'semua') {
            $query->where('shift.jenis_shift', $shiftId->idshift_rule);
        }

        // Clone query dasar untuk memisahkan Masuk dan Pulang
        // Clone PENTING agar filter tidak tumpang tindih
        $queryMasuk = clone $query;
        $queryPulang = clone $query;

        // Ambil data PRESENSI MASUK dengan pagination
        $dataMasuk = $queryMasuk->where('presensi.jenis_presensi', 'Masuk')
                               ->orderBy('presensi.waktu', 'asc')
                               ->paginate($perPage, ['*'], 'page_masuk');

        // Ambil data PRESENSI PULANG dengan pagination
        $dataPulang = $queryPulang->where('presensi.jenis_presensi', 'Pulang')
                                 ->orderBy('presensi.waktu', 'asc')
                                 ->paginate($perPage, ['*'], 'page_pulang');



        // Kirim data ke view
        if ($request->ajax()) {
            return response()->json([
                'html_masuk' => view('supervisor.partials.presensi-masuk', [
                    'dataMasuk' => $dataMasuk,
                    'shiftTerpilih' => $shiftFilter,
                ])->render(),
                'html_pulang' => view('supervisor.partials.presensi-pulang', [
                    'dataPulang' => $dataPulang,
                    'shiftTerpilih' => $shiftFilter,
                ])->render(),
            ]);
        }

        return view('supervisor.presensi', [
            'dataMasuk' => $dataMasuk,
            'dataPulang' => $dataPulang,
            'tanggalTerpilih' => $tanggalFilter,
            'shiftTerpilih' => $shiftFilter,
            'perPage' => $perPage,
        ]);
    }



    /**
     * Fungsi edit() tidak diperlukan lagi karena kita pakai MODAL.
     * Hapus fungsi 'edit()' yang lama.
     */
    // public function edit(...) { ... }




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