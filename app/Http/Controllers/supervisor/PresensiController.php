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

        // --- TAMBAHAN BARU: Ambil Data Shift Rule ---
        // Kita ambil data rule untuk Pagi, Malam, dan Non Shift agar bisa ditampilkan di modal
        $rules = ShiftRule::whereIn('jenis_shift', ['Pagi', 'Malam', 'Non Shift'])->get();
        
        // Ambil nilai toleransi & dibuka dari salah satu (misal Pagi) karena nilainya seragam
        $globalRule = $rules->firstWhere('jenis_shift', 'Pagi');

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
            'shiftTerpilih' => $shiftFilter, // Ganti nama variabel agar sesuai view
            'rules' => $rules,
            'globalToleransi' => $globalRule ? $globalRule->toleransi : 0,
            'globalDibuka' => $globalRule ? $globalRule->dibuka : 0,
            'perPage' => $perPage,
        ]);
    }

    /**
     * Menghapus data presensi (HANYA UNTUK SUPERVISOR).
     *
     * DIPERBARUI: 'foto_masuk' -> 'foto'
     */
    public function destroy($id_presensi)
    {
        if (Auth::user()->peran !== 'supervisor') {
            return redirect()->route('supervisor.presensi.index')->with('error', 'Anda tidak memiliki hak akses.');
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
        if (Auth::user()->peran !== 'supervisor') {
            return redirect()->route('supervisor.presensi.index')->with('error', 'Anda tidak memiliki hak akses.');
        }

        // Validasi input (disesuaikan dengan field modal)
        $request->validate([
            'waktu' => 'required|date',
            'status' => 'required|in:tepat waktu,terlambat,terlalu cepat,izin',
            'jenis_presensi' => 'required|in:Masuk,Pulang',
        ]);

        try {
            $presensi = Presensi::findOrFail($id_presensi);
            
            // Cek apakah status berubah
            $oldStatus = $presensi->status;
            
            $presensi->update([
                'waktu' => $request->waktu,
                'status' => $request->status,
                'jenis_presensi' => $request->jenis_presensi,
                'tanggal' => Carbon::parse($request->waktu)->format('Y-m-d'), // Update tanggal
            ]);

            // Kirim Notifikasi jika status berubah
            if ($oldStatus != $request->status) {
                $user = \App\Models\User::find($presensi->id_pengguna);
                if ($user) {
                    // Format waktu presensi agar lebih enak dibaca (Hari, Tanggal Jam)
                    $waktuFormatted = \Carbon\Carbon::parse($presensi->waktu)->translatedFormat('l, d F Y H:i');
                    $pesan = "Status presensi Anda pada {$waktuFormatted} telah diubah oleh Komandan dari '{$oldStatus}' menjadi '{$request->status}'.";
                    
                    try {
                        $user->notify(new \App\Notifications\PerubahanStatusPresensiNotification($pesan, $presensi));
                    } catch (\Exception $e) {
                         // Log error notification but don't stop process
                         \Illuminate\Support\Facades\Log::error('Gagal kirim notifikasi status presensi: ' . $e->getMessage());
                    }
                }
            }

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

    public function updateRules(Request $request)
    {
        if (Auth::user()->peran !== 'supervisor') {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'masuk_pagi' => 'required',
            'keluar_pagi' => 'required',
            'masuk_malam' => 'required',
            'keluar_malam' => 'required',
            'masuk_non' => 'required',
            'keluar_non' => 'required',
            'toleransi' => 'required|numeric|min:0',
            'dibuka' => 'required|numeric|min:0',
        ]);

        $geoStatus = $request->input('isgeotagenabled', 0);

        // --- VALIDASI TAMBAHAN ---
        // Jam Pulang Shift Pagi harus sama persis dengan Jam Masuk Shift Malam (Sambung)
        // Kita ambil 5 karakter pertama (HH:mm) untuk mengantisipasi perbedaan detik (misal 19:00 vs 19:00:00)
        $pagiKeluar = substr($request->keluar_pagi, 0, 5);
        $malamMasuk = substr($request->masuk_malam, 0, 5);

        if ($pagiKeluar != $malamMasuk) {
            return redirect()->back()->with('error', 'Jam Pulang Shift Pagi harus bersambung dengan Jam Masuk Shift Malam (Tidak boleh ada jeda).');
        }

        try {
            // 1. Update Shift Pagi
            ShiftRule::where('jenis_shift', 'Pagi')->update([
                'jam_masuk' => $request->masuk_pagi,
                'jam_keluar' => $request->keluar_pagi,
                'toleransi' => $request->toleransi,
                'dibuka' => $request->dibuka,
                'is_geotag_enabled' => $geoStatus,
            ]);

            // 2. Update Shift Malam
            ShiftRule::where('jenis_shift', 'Malam')->update([
                'jam_masuk' => $request->masuk_malam,
                'jam_keluar' => $request->keluar_malam,
                'toleransi' => $request->toleransi,
                'dibuka' => $request->dibuka,
                'is_geotag_enabled' => $geoStatus,
            ]);

            // 3. Update Non Shift
            ShiftRule::where('jenis_shift', 'Non Shift')->update([
                'jam_masuk' => $request->masuk_non,
                'jam_keluar' => $request->keluar_non,
                'toleransi' => $request->toleransi,
                'dibuka' => $request->dibuka,
                'is_geotag_enabled' => $geoStatus,
            ]);
            

            // Catatan: Shift 'Off' tidak diupdate karena toleransi/dibuka null (sesuai gambar)

            return redirect()->back()->with('success', 'Pengaturan Shift Rule berhasil diperbarui.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal update rule: ' . $e->getMessage());
        }
    }
}