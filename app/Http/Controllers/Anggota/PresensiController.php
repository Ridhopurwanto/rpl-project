<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Storage; // Untuk menyimpan file
use Illuminate\Support\Str; // Untuk membuat nama file

class PresensiController extends Controller
{
    public function create()
    {
        // Cukup tampilkan view baru.
        // Logika akan ditangani oleh Alpine.js di frontend.
        return view('anggota.presensi-create');
    }
    /**
     * Menampilkan halaman presensi (kalender dan riwayat).
     */
    public function index(Request $request)
    {
        // Dapatkan pengguna yang sedang login
        $user = Auth::user();

        // Tentukan bulan dan tahun yang akan dilihat.
        // Jika ada input 'month', gunakan itu. Jika tidak, gunakan bulan ini.
        $tanggalSekarang = Carbon::parse($request->input('month', 'now'));
        
        $bulan = $tanggalSekarang->month;
        $tahun = $tanggalSekarang->year;

        // =======================================================
        // 1. AMBIL DATA SHIFT (DARI DATABASE)
        // =======================================================
        
        // Ambil SEMUA shift untuk bulan & tahun yang dipilih
        $shiftsDariDB = $user->shifts()
                            ->whereMonth('tanggal', $bulan)
                            ->whereYear('tanggal', $tahun)
                            ->get();

        // Ubah data shift menjadi "lookup map" agar mudah dicari
        // Hasilnya akan seperti: ['2025-10-01' => 'off', '2025-10-02' => 'pagi']
        $shiftMap = $shiftsDariDB->keyBy(function ($shift) {
            return Carbon::parse($shift->tanggal)->format('Y-m-d');
        })->map(function ($shift) {
            // Asumsi Anda punya kolom 'jenis_shift' ('pagi', 'malam', 'off')
            return strtolower($shift->jenis_shift);; 
        });


        // =======================================================
        // 2. BUAT DATA KALENDER LENGKAP
        // =======================================================
        
        // Tentukan hari pertama dan terakhir dari bulan yang dipilih
        $tanggalAwal = $tanggalSekarang->copy()->startOfMonth();
        $tanggalAkhir = $tanggalSekarang->copy()->endOfMonth();

        // Buat "kalender virtual" dari tanggal awal sampai akhir
        $period = CarbonPeriod::create($tanggalAwal, $tanggalAkhir);

        $dataKalender = [];

        // Tambahkan padding (hari kosong) di awal kalender
        // 'dayOfWeek' (0=Minggu, 1=Senin, ... 6=Sabtu)
        $hariKosongDiAwal = $tanggalAwal->dayOfWeek;
        for ($i = 0; $i < $hariKosongDiAwal; $i++) {
            $dataKalender[] = [
                'tanggal' => null,
                'jenis_shift' => null,
            ];
        }

        // Isi kalender dengan data shift
        foreach ($period as $date) {
            $tanggalString = $date->format('Y-m-d');
            
            // Cek di "lookup map" apakah ada shift untuk tanggal ini
            $jenisShift = $shiftMap->get($tanggalString); // Akan 'null' jika tidak ada

            $dataKalender[] = [
                'tanggal' => $date->format('d'), // Hanya angkanya (1, 2, 3...)
                'jenis_shift' => $jenisShift,    // 'pagi', 'malam', 'off', atau null
            ];
        }

        // =======================================================
        // 3. AMBIL DATA TABEL RIWAYAT (Tetap sama)
        // =======================================================
        $tanggalRiwayat = $request->input('tanggal') ? Carbon::parse($request->input('tanggal')) : Carbon::today();
        
        $riwayatHariIni = $user->presensi()
                            ->whereDate('tanggal', $tanggalRiwayat)
                            ->first();

        // =======================================================
        // 4. KIRIM SEMUA DATA KE VIEW
        // =======================================================
        return view('anggota.presensi', [
            'namaBulan' => $tanggalSekarang->format('F Y'), // e.g., "OKTOBER 2025"
            'dataKalender' => $dataKalender,     // DATA KALENDER BARU (LENGKAP)
            'riwayatHariIni' => $riwayatHariIni, // Data untuk tabel
            'tanggalTerpilih' => $tanggalRiwayat, // Untuk filter
        ]);
    }

    /**
     * Menyimpan data (Check-in atau Check-out).
     * Ini akan dipanggil oleh tombol '+'
     */
    /**
     * Menyimpan data (Check-in atau Check-out).
     * Dipanggil dari halaman presensi-create.
     */
    public function store(Request $request)
    {
        // Validasi, pastikan kita menerima data gambar
        $request->validate([
            'foto_base64' => 'required|string',
        ]);

        // 1. Ambil data gambar Base64 dari input
        $imageData = $request->foto_base64;

        // 2. Decode data Base64
        // Formatnya adalah 'data:image/jpeg;base64,xxxxxx...'
        // Kita perlu memisahkan 'xxxxxx'
        @list($type, $imageData) = explode(';', $imageData);
        @list(, $imageData) = explode(',', $imageData);
        
        // 3. Konversi data teks menjadi file biner
        $fileData = base64_decode($imageData);

        // 4. Buat nama file unik
        $fileName = 'presensi/' . Auth::id() . '_' . Str::uuid() . '.jpg';

        // 5. Simpan file ke storage
        // Pastikan Anda sudah menjalankan 'php artisan storage:link'
        Storage::disk('public')->put($fileName, $fileData);

        // 6. Simpan path file ke database (CONTOH)
        // Logika ini HANYA untuk check-in. Anda perlu logika
        // untuk mendeteksi apakah ini check-in atau check-out.
        
        $presensiHariIni = Auth::user()->presensi()
                            ->whereDate('tanggal', Carbon::today())
                            ->first();

        if ($presensiHariIni) {
            // Jika sudah ada data, ini CHECK-OUT
            $presensiHariIni->update([
                'waktu_pulang' => now(),
                'foto_pulang' => $fileName,
                // 'lokasi_pulang' => $request->lokasi,
            ]);
        } else {
            // Jika belum ada, ini CHECK-IN
            Auth::user()->presensi()->create([
                'tanggal' => Carbon::today(),
                'waktu_masuk' => now(),
                'foto_masuk' => $fileName,
                'status' => 'Tepat Waktu', // (Contoh)
                // 'lokasi_masuk' => $request->lokasi,
            ]);
        }
        
        // 7. Redirect kembali ke halaman daftar presensi
        return redirect()->route('anggota.presensi.index')
                         ->with('success', 'Presensi berhasil dicatat!');
    }
}