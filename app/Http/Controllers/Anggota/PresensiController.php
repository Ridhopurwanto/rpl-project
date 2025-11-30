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
use App\Models\Shift;
use App\Models\ShiftRule;

class PresensiController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // --- 1. FILTER TANGGAL (RANGE) ---
        $startDate = $request->input('start_date', Carbon::today()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());

        // --- 2. AMBIL DATA SHIFT (Untuk Kalender) ---
        // Kita ambil shift bulan dari startDate saja untuk kalender visual
        $startCarbon = Carbon::parse($startDate);
        $bulan = $startCarbon->month;
        $tahun = $startCarbon->year;
        
        $shiftsDariDB = Shift::with('shiftRule')
                            ->where('id_pengguna', $user->id_pengguna)
                            ->whereMonth('tanggal', $bulan)
                            ->whereYear('tanggal', $tahun)
                            ->get();

        $shiftMap = $shiftsDariDB->keyBy(function ($shift) {
            return Carbon::parse($shift->tanggal)->format('Y-m-d');
        });

        // --- 3. GENERATE KALENDER (Visual Bulan Ini) ---
        $calStart = $startCarbon->copy()->startOfMonth();
        $calEnd = $startCarbon->copy()->endOfMonth();
        $period = CarbonPeriod::create($calStart, $calEnd);
        $dataKalender = [];

        for ($i = 0; $i < $calStart->dayOfWeek; $i++) {
            $dataKalender[] = ['tanggal' => null, 'jenis_shift' => null];
        }

        foreach ($period as $date) {
            $tglStr = $date->format('Y-m-d');
            $shiftData = $shiftMap->get($tglStr);
            $namaJenis = $shiftData && $shiftData->shiftRule ? strtolower($shiftData->shiftRule->jenis_shift) : null;
            
            $dataKalender[] = [
                'tanggal' => $date->format('d'),
                'jenis_shift' => $namaJenis,
            ];
        }

        // --- 4. AMBIL RIWAYAT PRESENSI (Sesuai Rentang Tanggal) ---
        // Kita ambil semua log presensi dalam rentang tanggal yang dipilih
        $riwayatPresensi = Presensi::where('id_pengguna', $user->id_pengguna)
                            ->whereDate('waktu', '>=', $startDate)
                            ->whereDate('waktu', '<=', $endDate)
                            ->orderBy('waktu', 'desc') // Urutkan dari terbaru
                            ->get();

        $now = Carbon::now();
        $now = Carbon::now()->setTime(18, 00, 00);

        $todayShiftData = Shift::with('shiftRule')
                            ->where('id_pengguna', $user->id_pengguna)
                            ->whereDate('tanggal', Carbon::today())
                            ->first();

        // Cek Status Presensi Hari Ini (Sudah Masuk / Pulang?)
        // Kita cari record presensi hari ini (untuk shift pagi) atau kemarin (untuk shift malam yg belum kelar)
        // Sederhananya: Cek record hari ini dulu
        $presensiLog = Presensi::where('id_pengguna', $user->id_pengguna)
                            ->whereDate('waktu', Carbon::today()) 
                            ->get();

        $sudahMasuk = $presensiLog->where('jenis_presensi', 'Masuk')->first();
        $sudahPulang = $presensiLog->where('jenis_presensi', 'Pulang')->first();
        
        // Default Data Jadwal
        $jadwalAbsen = [
            'nama_shift'     => 'TIDAK ADA JADWAL',
            'info_terdekat'  => '-',
            'can_presensi'   => false,
            'pesan_error'    => 'Tidak ada jadwal shift hari ini.',
            // Flag untuk mematikan radio button
            'disable_masuk'  => true, 
            'disable_pulang' => true,
            'default_jenis'  => 'masuk', // Default pilihan radio
        ];

        if ($todayShiftData && $todayShiftData->shiftRule) {
            $rule = $todayShiftData->shiftRule;
            $jadwalAbsen['nama_shift'] = strtoupper($rule->jenis_shift);
            $menitDibuka = $rule->dibuka ?? 0;

            if ($rule->jam_masuk && $rule->jam_keluar) {
                // Buat Objek Waktu
                $jamMasuk = Carbon::createFromTimeString($rule->jam_masuk);
                $jamKeluar = Carbon::createFromTimeString($rule->jam_keluar);

                // --- LOGIKA LINTAS HARI (SHIFT MALAM) ---
                // Jika jam keluar lebih kecil dari jam masuk (misal 07:00 < 19:00), 
                // berarti jam keluar adalah BESOKNYA.
                if ($jamKeluar->lt($jamMasuk)) {
                    $jamKeluar->addDay(); // Tambah 1 hari
                }

                // Hitung Waktu Buka (Buffer)
                $waktuBukaMasuk = $jamMasuk->copy()->subMinutes($menitDibuka);
                $waktuBukaPulang = $jamKeluar->copy();

                // --- LOGIKA TOMBOL ---
                if( $jadwalAbsen['nama_shift'] == 'OFF'){
                    $jadwalAbsen['info_terdekat'] = '-';
                    $jadwalAbsen['can_presensi'] = false;
                    $jadwalAbsen['pesan_error'] = 'Tidak sedang bekerja';
                    $jadwalAbsen['disable_masuk'] = true;
                    $jadwalAbsen['disable_pulang'] = true;
                }
                elseif (!$sudahMasuk) {
                    // KASUS 1: BELUM MASUK
                    $jadwalAbsen['info_terdekat'] = 'MASUK DIBUKA: ' . $waktuBukaMasuk->format('H:i');
                    $jadwalAbsen['disable_masuk'] = false;  // Boleh pilih Masuk
                    $jadwalAbsen['disable_pulang'] = true;  // Gaboleh pilih Pulang
                    $jadwalAbsen['default_jenis'] = 'masuk';

                    if ($now->gte($waktuBukaMasuk)) {
                        $jadwalAbsen['can_presensi'] = true;
                        $jadwalAbsen['pesan_error'] = '';
                    } else {
                        $jadwalAbsen['can_presensi'] = false;
                        $jadwalAbsen['pesan_error'] = 'Presensi Masuk belum dibuka. Tunggu ' . $waktuBukaMasuk->format('H:i');
                    }

                } elseif (!$sudahPulang) {
                    // KASUS 2: SUDAH MASUK, BELUM PULANG
                    $jadwalAbsen['info_terdekat'] = 'PULANG PADA PUKUL: ' . $waktuBukaPulang->format('H:i');
                    $jadwalAbsen['disable_masuk'] = true;   // Gaboleh pilih Masuk lagi
                    $jadwalAbsen['disable_pulang'] = false; // Boleh pilih Pulang
                    $jadwalAbsen['default_jenis'] = 'pulang'; // Default ganti ke Pulang
                    $jadwalAbsen['can_presensi'] = true;
                    $jadwalAbsen['pesan_error'] = '';
                } else {
                    // KASUS 3: SELESAI
                    $jadwalAbsen['info_terdekat'] = 'PRESENSI SELESAI';
                    $jadwalAbsen['can_presensi'] = false;
                    $jadwalAbsen['pesan_error'] = 'Anda sudah menyelesaikan presensi hari ini.';
                    $jadwalAbsen['disable_masuk'] = true;
                    $jadwalAbsen['disable_pulang'] = true;
                }
            }
        }

        $shiftHariIni = $todayShiftData && $todayShiftData->shiftRule ? strtoupper($todayShiftData->shiftRule->jenis_shift) : 'TIDAK ADA JADWAL';

        return view('anggota.presensi', [
            'namaBulan' => $startCarbon->format('F Y'),
            'dataKalender' => $dataKalender,
            'riwayatPresensi' => $riwayatPresensi, // Kirim Collection
            'startDate' => $startDate,
            'endDate' => $endDate,
            'shiftHariIni' => $shiftHariIni,
            'jadwalAbsen' => $jadwalAbsen,
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
            $tanggal = Carbon::now()->format('Y-m-d');

            // 1. Cek Duplikasi
            $cekDuplikat = Presensi::where('id_pengguna', $user->id_pengguna)
                            ->whereDate('waktu', $now) 
                            ->where('jenis_presensi', $request->jenis_presensi)
                            ->exists();

            
            if ($cekDuplikat) {
                return redirect()->back()->with('error', 'Anda sudah presensi ' . $request->jenis_presensi . ' hari ini.');
            }

            // 2. Simpan Foto
            $imageData = $request->foto_base64;
            if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
                $imageData = substr($imageData, strpos($imageData, ',') + 1);
                $imageData = base64_decode($imageData);
            } else {
                $imageData = base64_decode($imageData);
            }
            $fileName = 'presensi/' . $request->jenis_presensi . '_' . $user->id_pengguna . '_' . time() . '.jpg';
            Storage::disk('public')->put($fileName, $imageData);

            // 3. LOGIKA HITUNG KETERLAMBATAN (BARU)
            $status = 'tepat waktu'; // Default
            $shiftHariIni = Shift::with('shiftRule')
                            ->where('id_pengguna', $user->id_pengguna)
                            ->whereDate('tanggal', $now)
                            ->first();

            // Hanya hitung status jika ini presensi MASUK dan shift ditemukan
            if ($request->jenis_presensi == 'masuk' && $shiftHariIni && $shiftHariIni->shiftRule) {
                $rule = $shiftHariIni->shiftRule;
                
                // Jika jenis shift 'Off', mungkin statusnya beda (misal: Lembur)
                // Tapi asumsi di sini kita cek Pagi/Malam dll.
                if ($rule->jam_masuk) {
                    $jamMasuk = Carbon::createFromTimeString($rule->jam_masuk);
                    // Tambah toleransi (menit)
                    $batasTerlambat = $jamMasuk->copy()->addMinutes($rule->toleransi);
                    
                    // Bandingkan waktu sekarang dengan batas toleransi
                    // Kita hanya ambil jam & menitnya untuk perbandingan yang adil
                    $waktuSekarang = Carbon::createFromTimeString($now->format('H:i:s'));
                    
                    if ($waktuSekarang->gt($batasTerlambat)) {
                        $status = 'terlambat';
                    }
                }
            } elseif ($request->jenis_presensi == 'pulang') {
                $rule = $shiftHariIni->shiftRule;
                
                // Jika jenis shift 'Off', mungkin statusnya beda (misal: Lembur)
                // Tapi asumsi di sini kita cek Pagi/Malam dll.
                if ($rule->jam_keluar) {
                    $jamMasuk = Carbon::createFromTimeString($rule->jam_masuk);
                    $jamKeluar = Carbon::createFromTimeString($rule->jam_keluar);
                    
                    if ($jamKeluar->lt($jamMasuk)) {
                        $jamKeluar->addDay(); // Tambah 1 hari
                    }
                                
                    // Tambah toleransi (menit)
                    $batasAwalPulang = $jamKeluar->copy()->subMinutes($rule->toleransi);
                    $batasAkhirPulang = $jamKeluar->copy()->addMinutes($rule->toleransi);

                    // Bandingkan waktu sekarang dengan batas toleransi
                    // Kita hanya ambil jam & menitnya untuk perbandingan yang adil
                    $waktuSekarang = Carbon::createFromTimeString($now);
                    
                    if ($waktuSekarang->lt($batasAwalPulang)) {
                        $status = 'terlalu cepat';
                    }
                    elseif($waktuSekarang->gt($batasAkhirPulang)){
                        $status = 'terlambat';
                    }
                }                
            }

            // 4. Simpan Data
            Presensi::create([
                'id_pengguna'    => $user->id_pengguna,
                'id_shift'       => $shiftHariIni ? $shiftHariIni->id_shift : null,
                'nama_lengkap'   => $user->nama_lengkap,
                'waktu'          => $now,
                'tanggal'        => $tanggal,
                'foto'           => $fileName,
                'jenis_presensi' => $request->jenis_presensi,
                'status'         => $status, // Hasil perhitungan otomatis
            ]);

            return redirect()->route('anggota.presensi.index')
                             ->with('success', 'Presensi ' . $request->jenis_presensi . ' berhasil! Status: ' . $status);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
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



// $user = Auth::user();
//         $tanggalTerpilih = $request->input('tanggal') 
//                             ? Carbon::parse($request->input('tanggal')) 
//                             : Carbon::today();

//         // 1. AMBIL DATA SHIFT & RULE (Eager Loading)
//         $bulan = $tanggalTerpilih->month;
//         $tahun = $tanggalTerpilih->year;
        
//         // Ambil shift user beserta aturan shift-nya (jenis, jam, dll)
//         $shiftsDariDB = Shift::with('shiftRule') // Eager load relasi shiftRule
//                             ->where('id_pengguna', $user->id_pengguna)
//                             ->whereMonth('tanggal', $bulan)
//                             ->whereYear('tanggal', $tahun)
//                             ->get();

//         $shiftMap = $shiftsDariDB->keyBy(function ($shift) {
//             return Carbon::parse($shift->tanggal)->format('Y-m-d');
//         });

//         // 2. GENERATE KALENDER
//         $tanggalAwal = $tanggalTerpilih->copy()->startOfMonth();
//         $tanggalAkhir = $tanggalTerpilih->copy()->endOfMonth();
//         $period = CarbonPeriod::create($tanggalAwal, $tanggalAkhir);
//         $dataKalender = [];

//         // Padding hari kosong di awal
//         for ($i = 0; $i < $tanggalAwal->dayOfWeek; $i++) {
//             $dataKalender[] = ['tanggal' => null, 'jenis_shift' => null];
//         }

//         foreach ($period as $date) {
//             $tglStr = $date->format('Y-m-d');
//             $shiftData = $shiftMap->get($tglStr);
            
//             // Ambil nama jenis shift dari relasi shiftRule (jika ada)
//             // Contoh: 'Pagi', 'Malam', 'Off'
//             $namaJenisShift = $shiftData && $shiftData->shiftRule 
//                                 ? strtolower($shiftData->shiftRule->jenis_shift) 
//                                 : null;

//             $dataKalender[] = [
//                 'tanggal' => $date->format('d'),
//                 'jenis_shift' => $namaJenisShift,
//             ];
//         }

//         // 3. RIWAYAT PRESENSI HARI INI
//         $logsHariIni = Presensi::where('id_pengguna', $user->id_pengguna)
//                         ->whereDate('waktu', $tanggalTerpilih)
//                         ->get();

//         $presensiMasuk = $logsHariIni->where('jenis_presensi', 'masuk')->first();
//         $presensiPulang = $logsHariIni->where('jenis_presensi', 'pulang')->first();

//         $riwayatHariIni = null;
//         if ($presensiMasuk || $presensiPulang) {
//             $riwayatHariIni = (object) [
//                 'waktu_masuk' => $presensiMasuk ? $presensiMasuk->waktu->format('H:i') : '-',
//                 'foto_masuk'  => $presensiMasuk ? $presensiMasuk->foto : null,
//                 'waktu_pulang'=> $presensiPulang ? $presensiPulang->waktu->format('H:i') : null,
//                 'foto_pulang' => $presensiPulang ? $presensiPulang->foto : null,
//                 'status'      => $presensiMasuk ? $presensiMasuk->status : '-',
//             ];
//         }

//         // Ambil info shift untuk header "JENIS SHIFT :"
//         $shiftHariIniData = $shiftMap->get($tanggalTerpilih->format('Y-m-d'));
//         $shiftHariIni = $shiftHariIniData && $shiftHariIniData->shiftRule 
//                             ? strtolower($shiftHariIniData->shiftRule->jenis_shift) 
//                             : null;

//         return view('anggota.presensi', [
//             'namaBulan' => $tanggalTerpilih->format('F Y'),
//             'dataKalender' => $dataKalender,
//             'riwayatHariIni' => $riwayatHariIni,
//             'tanggalTerpilih' => $tanggalTerpilih,
//             'shiftHariIni' => $shiftHariIni,
//         ]);