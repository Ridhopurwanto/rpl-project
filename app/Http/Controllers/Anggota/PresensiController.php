<?php
namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Presensi;

class PresensiController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // 1. Tentukan Tanggal Terpilih
        $tanggalTerpilih = $request->input('tanggal') 
                            ? Carbon::parse($request->input('tanggal')) 
                            : Carbon::today();

        // 2. Ambil Data Shift (Untuk Kalender)
        // Asumsi: Tabel Shift masih menggunakan kolom 'tanggal' atau sejenisnya
        $bulan = $tanggalTerpilih->month;
        $tahun = $tanggalTerpilih->year;
        
        $shiftsDariDB = $user->shifts()
                            ->whereMonth('tanggal', $bulan)
                            ->whereYear('tanggal', $tahun)
                            ->get();

        $shiftMap = $shiftsDariDB->keyBy(function ($shift) {
            return Carbon::parse($shift->tanggal)->format('Y-m-d');
        });

        // 3. Generate Kalender (Logika Visual)
        $tanggalAwal = $tanggalTerpilih->copy()->startOfMonth();
        $tanggalAkhir = $tanggalTerpilih->copy()->endOfMonth();
        $period = CarbonPeriod::create($tanggalAwal, $tanggalAkhir);
        $dataKalender = [];

        for ($i = 0; $i < $tanggalAwal->dayOfWeek; $i++) {
            $dataKalender[] = ['tanggal' => null, 'jenis_shift' => null];
        }

        foreach ($period as $date) {
            $tgl = $date->format('Y-m-d');
            $shiftData = $shiftMap->get($tgl);
            $dataKalender[] = [
                'tanggal' => $date->format('d'),
                'jenis_shift' => $shiftData ? strtolower($shiftData->jenis_shift) : null,
            ];
        }

        // 4. AMBIL RIWAYAT PRESENSI (LOGIKA BARU: Filter by 'waktu')
        // Kita ambil semua data presensi user pada tanggal yang dipilih
        $logsHariIni = Presensi::where('id_pengguna', $user->id_pengguna)
                        ->whereDate('waktu', $tanggalTerpilih) // <--- UBAH KE 'waktu'
                        ->get();

        $presensiMasuk = $logsHariIni->where('jenis_presensi', 'masuk')->first();
        $presensiPulang = $logsHariIni->where('jenis_presensi', 'pulang')->first();

        // Bentuk objek display untuk View
        $riwayatHariIni = null;
        if ($presensiMasuk || $presensiPulang) {
            $riwayatHariIni = (object) [
                // Format jam dari kolom 'waktu' (datetime)
                'waktu_masuk' => $presensiMasuk ? $presensiMasuk->waktu->format('H:i') : '-',
                'foto_masuk'  => $presensiMasuk ? $presensiMasuk->foto : null,
                
                'waktu_pulang'=> $presensiPulang ? $presensiPulang->waktu->format('H:i') : null,
                'foto_pulang' => $presensiPulang ? $presensiPulang->foto : null,
                
                // Ambil status dari salah satu
                'status'      => $presensiMasuk ? $presensiMasuk->status : ($presensiPulang ? $presensiPulang->status : '-'),
            ];
        }

        $shiftHariIniObj = $shiftMap->get($tanggalTerpilih->format('Y-m-d'));
        $shiftHariIni = $shiftHariIniObj ? strtolower($shiftHariIniObj->jenis_shift) : null;

        return view('anggota.presensi', [
            'namaBulan' => $tanggalTerpilih->format('F Y'),
            'dataKalender' => $dataKalender,
            'riwayatHariIni' => $riwayatHariIni,
            'tanggalTerpilih' => $tanggalTerpilih,
            'shiftHariIni' => $shiftHariIni,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'foto_base64' => 'required|string',
            'jenis_presensi' => 'required|in:masuk,pulang',
        ]);

        try {
            $user = Auth::user();
            $now = Carbon::now();

            // 1. Cek Duplikasi (Berdasarkan Waktu Hari Ini)
            $cekDuplikat = Presensi::where('id_pengguna', $user->id_pengguna)
                            ->whereDate('waktu', $now) 
                            ->where('jenis_presensi', $request->jenis_presensi)
                            ->exists();

            if ($cekDuplikat) {
                return redirect()->back()->with('error', 'Anda sudah melakukan presensi ' . $request->jenis_presensi . ' hari ini.');
            }

            // 2. Proses Foto Base64
            $imageData = $request->foto_base64;
            if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
                $imageData = substr($imageData, strpos($imageData, ',') + 1);
                $imageData = base64_decode($imageData);
            } else {
                $imageData = base64_decode($imageData);
            }

            // Nama file: TIPE_IDUSER_TIMESTAMP.jpg
            $fileName = 'presensi/' . $request->jenis_presensi . '_' . $user->id_pengguna . '_' . time() . '.jpg';
            Storage::disk('public')->put($fileName, $imageData);

            // 3. Cek Shift (Opsional)
            // Jika tabel shift masih pakai tanggal, gunakan ini. Jika tidak, hapus/sesuaikan.
            $shiftHariIni = $user->shifts()->whereDate('tanggal', $now)->first();
            
            // 4. Susun Data untuk Disimpan
            $dataToSave = [
                'id_pengguna'    => $user->id_pengguna,
                'waktu'          => $now,
                'foto'           => $fileName,
                'jenis_presensi' => $request->jenis_presensi,
                'status'         => 'Hadir',
            ];

            // Tambahkan id_shift HANYA jika shift ditemukan (menghindari error constraint)
            if ($shiftHariIni) {
                $dataToSave['id_shift'] = $shiftHariIni->id_shift;
            }

            // Tambahkan nama_lengkap manual (jika boot model tidak jalan atau kolom wajib)
            // Pastikan kolom 'nama_lengkap' BENAR-BENAR ADA di tabel presensi Anda
            $dataToSave['nama_lengkap'] = $user->nama_lengkap ?? $user->nama;

            // 5. Eksekusi Simpan
            Presensi::create($dataToSave);

            return redirect()->route('anggota.presensi.index')
                             ->with('success', 'Presensi ' . $request->jenis_presensi . ' berhasil!');

        } catch (\Exception $e) {
            // Tampilkan pesan error asli untuk debugging (misal: Column not found)
            return redirect()->back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }
}

// class PresensiController extends Controller
// {
//     public function create()
//     {
//         // Cukup tampilkan view baru.
//         // Logika akan ditangani oleh Alpine.js di frontend.
//         return view('anggota.presensi-create');
//     }
//     /**
//      * Menampilkan halaman presensi (kalender dan riwayat).
//      */
//     public function index(Request $request)
//     {
//         // Dapatkan pengguna yang sedang login
//         $user = Auth::user();

//         // =======================================================
//         // 1. TENTUKAN TANGGAL TERPILIH (SUMBER KEBENARAN)
//         // =======================================================
//         // Gunakan input 'tanggal', jika tidak ada, gunakan hari ini.
//         $tanggalTerpilih = $request->input('tanggal') 
//                             ? Carbon::parse($request->input('tanggal')) 
//                             : Carbon::today();

//         // Dapatkan bulan dan tahun DARI tanggal yang dipilih
//         $bulan = $tanggalTerpilih->month;
//         $tahun = $tanggalTerpilih->year;

//         // =======================================================
//         // 2. AMBIL DATA SHIFT (DARI DATABASE)
//         // =======================================================
        
//         // Ambil SEMUA shift untuk bulan & tahun yang dipilih
//         $shiftsDariDB = $user->shifts()
//                             ->whereMonth('tanggal', $bulan)
//                             ->whereYear('tanggal', $tahun)
//                             ->get();

//         // Ubah data shift menjadi "lookup map" ['2025-10-01' => 'pagi']
//         $shiftMap = $shiftsDariDB->keyBy(function ($shift) {
//             return Carbon::parse($shift->tanggal)->format('Y-m-d');
//         })->map(function ($shift) {
//             return strtolower($shift->jenis_shift);; 
//         });


//         // =======================================================
//         // 3. BUAT DATA KALENDER LENGKAP
//         // =======================================================
        
//         // Tentukan hari pertama dan terakhir DARI BULAN YANG DIPILIH
//         $tanggalAwal = $tanggalTerpilih->copy()->startOfMonth();
//         $tanggalAkhir = $tanggalTerpilih->copy()->endOfMonth();

//         // Buat "kalender virtual"
//         $period = CarbonPeriod::create($tanggalAwal, $tanggalAkhir);
//         $dataKalender = [];

//         // Tambahkan padding (hari kosong) di awal kalender
//         $hariKosongDiAwal = $tanggalAwal->dayOfWeek;
//         for ($i = 0; $i < $hariKosongDiAwal; $i++) {
//             $dataKalender[] = ['tanggal' => null, 'jenis_shift' => null];
//         }

//         // Isi kalender dengan data shift
//         foreach ($period as $date) {
//             $tanggalString = $date->format('Y-m-d');
//             $jenisShift = $shiftMap->get($tanggalString); // 'pagi', 'malam', 'off', atau null

//             $dataKalender[] = [
//                 'tanggal' => $date->format('d'),
//                 'jenis_shift' => $jenisShift,
//             ];
//         }

//         // =======================================================
//         // 4. AMBIL DATA RIWAYAT & SHIFT HARI INI
//         // =======================================================
        
//         // Ambil riwayat presensi UNTUK TANGGAL TERPILIH
//         $riwayatHariIni = $user->presensi()
//                             ->whereDate('tanggal', $tanggalTerpilih)
//                             ->first(); //

//         // (BARU) Ambil data shift UNTUK TANGGAL TERPILIH dari map
//         $shiftHariIni = $shiftMap->get($tanggalTerpilih->format('Y-m-d'));


//         // =======================================================
//         // 5. KIRIM SEMUA DATA KE VIEW
//         // =======================================================
//         return view('anggota.presensi', [
//             'namaBulan' => $tanggalTerpilih->format('F Y'), // e.g., "OKTOBER 2025"
//             'dataKalender' => $dataKalender,
//             'riwayatHariIni' => $riwayatHariIni,
//             'tanggalTerpilih' => $tanggalTerpilih, // Kirim objek Carbon tanggal
//             'shiftHariIni' => $shiftHariIni, // Kirim string shift ('pagi', 'off', null)
//         ]);
//     }

//     /**
//      * Menyimpan data (Check-in atau Check-out).
//      * Ini akan dipanggil oleh tombol '+'
//      */
//     /**
//      * Menyimpan data (Check-in atau Check-out).
//      * Dipanggil dari halaman presensi-create.
//      */
//     public function store(Request $request)
//     {
//         // Validasi, pastikan kita menerima data gambar
//         $request->validate([
//             'foto_base64' => 'required|string',
//         ]);

//         // 1. Ambil data gambar Base64 dari input
//         $imageData = $request->foto_base64;

//         // 2. Decode data Base64
//         // Formatnya adalah 'data:image/jpeg;base64,xxxxxx...'
//         // Kita perlu memisahkan 'xxxxxx'
//         @list($type, $imageData) = explode(';', $imageData);
//         @list(, $imageData) = explode(',', $imageData);
        
//         // 3. Konversi data teks menjadi file biner
//         $fileData = base64_decode($imageData);

//         // 4. Buat nama file unik
//         $fileName = 'presensi/' . Auth::id() . '_' . Str::uuid() . '.jpg';

//         // 5. Simpan file ke storage
//         // Pastikan Anda sudah menjalankan 'php artisan storage:link'
//         Storage::disk('public')->put($fileName, $fileData);

//         // 6. Simpan path file ke database (CONTOH)
//         // Logika ini HANYA untuk check-in. Anda perlu logika
//         // untuk mendeteksi apakah ini check-in atau check-out.
        
//         $presensiHariIni = Auth::user()->presensi()
//                             ->whereDate('tanggal', Carbon::today())
//                             ->first();

//         if ($presensiHariIni) {
//             // Jika sudah ada data, ini CHECK-OUT
//             $presensiHariIni->update([
//                 'waktu_pulang' => now(),
//                 'foto_pulang' => $fileName,
//                 // 'lokasi_pulang' => $request->lokasi,
//             ]);
//         } else {
//             // Jika belum ada, ini CHECK-IN
//             Auth::user()->presensi()->create([
//                 'tanggal' => Carbon::today(),
//                 'waktu_masuk' => now(),
//                 'foto_masuk' => $fileName,
//                 'status' => 'Tepat Waktu', // (Contoh)
//                 // 'lokasi_masuk' => $request->lokasi,
//             ]);
//         }
        
//         // 7. Redirect kembali ke halaman daftar presensi
//         return redirect()->route('anggota.presensi.index')
//                          ->with('success', 'Presensi berhasil dicatat!');
//     }
// }