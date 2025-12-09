<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Patroli;
use App\Models\PatroliClaim;
use App\Models\PatroliRule;  // ← TAMBAHKAN INI
use App\Models\Shift;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PatroliController extends Controller
{
    /**
     * Jadwal Patroli dari DATABASE (bukan hardcode)
     * Ambil dari tabel patroli_rules
     */
    private function getJadwalPatroli()
    {
        // Ambil semua data dari database
        $rules = PatroliRule::all();

        $jadwal = [
            1 => [], // Shift Pagi
            2 => []  // Shift Malam
        ];

        foreach ($rules as $rule) {
            $jenisShiftKey = $rule->jenis_shift === 'Pagi' ? 1 : 2;
            $jadwal[$jenisShiftKey][$rule->jenis_patroli] = [
                Carbon::parse($rule->jam_mulai)->format('H:i'),
                Carbon::parse($rule->jam_selesai)->format('H:i')
            ];
        }

        // Jika belum ada data di database, pakai default
        if (empty($jadwal[1]) && empty($jadwal[2])) {
            return [
                1 => [ // Shift Pagi
                    'Patroli 1' => ['07:30', '08:30'],
                    'Patroli 2' => ['08:30', '10:30'],
                    'Patroli 3' => ['11:30', '12:30'],
                    'Patroli 4' => ['13:40', '15:30'],
                    'Patroli 5' => ['15:30', '17:30'],
                    'Patroli 6' => ['17:30', '18:40'],
                ],
                2 => [ // Shift Malam
                    'Patroli 1' => ['19:30', '20:20'],
                    'Patroli 2' => ['21:30', '22:30'],
                    'Patroli 3' => ['23:30', '00:30'],
                    'Patroli 4' => ['01:30', '02:30'],
                    'Patroli 5' => ['03:30', '04:30'],
                    'Patroli 6' => ['05:30', '06:30'],
                ]
            ];
        }

        return $jadwal;
    }

    /**
     * Get nama shift untuk display
     */
    private function getNamaShift($jenisShift)
    {
        return $jenisShift == 1 ? 'Pagi' : 'Malam';
    }

    /**
     * Cek apakah waktu sekarang valid untuk patroli tertentu
     */
    private function isWaktuPatroliValid($jenisPatroli, $jenisShift)
    {
        $jadwal = $this->getJadwalPatroli();

        if (!isset($jadwal[$jenisShift][$jenisPatroli])) {
            return false;
        }

        $waktuSekarang = Carbon::now();
        [$jamMulai, $jamSelesai] = $jadwal[$jenisShift][$jenisPatroli];

        $mulai = Carbon::parse($jamMulai);
        $selesai = Carbon::parse($jamSelesai);

        // Handle midnight crossing (misal 23:30 - 00:30)
        if ($selesai->lt($mulai)) {
            $selesai->addDay();

            // Jika waktu sekarang < jam mulai, tambahkan 1 hari untuk perbandingan
            if ($waktuSekarang->lt($mulai)) {
                $waktuSekarang = $waktuSekarang->copy()->addDay();
            }
        }

        return $waktuSekarang->between($mulai, $selesai);
    }

    /**
     * Cek apakah patroli sudah terlewat (jam selesai sudah lewat)
     */
    /**
     * Cek apakah patroli sudah terlewat (jam selesai sudah lewat)
     */
    private function isPatroliTerlewat($jenisPatroli, $jenisShift)
    {
        $jadwal = $this->getJadwalPatroli();

        if (!isset($jadwal[$jenisShift][$jenisPatroli])) {
            return true;
        }

        $waktuSekarang = Carbon::now();
        [$jamMulai, $jamSelesai] = $jadwal[$jenisShift][$jenisPatroli];

        // ===== CEK APAKAH SHIFT SUDAH DIMULAI =====
        if ($jenisShift == 1) {
            // SHIFT PAGI: 07:00 - 19:00
            $shiftMulai = Carbon::parse('07:00');
            $shiftSelesai = Carbon::parse('19:00');

            // Jika sekarang belum masuk waktu shift pagi
            if ($waktuSekarang->lt($shiftMulai)) {
                return false; // Shift belum dimulai, patroli belum terlewat
            }

            // Jika sekarang sudah lewat waktu shift pagi
            if ($waktuSekarang->gte($shiftSelesai)) {
                return true; // Shift sudah selesai, semua patroli terlewat
            }

        } else {
            // SHIFT MALAM: 19:00 - 07:00 (lewat tengah malam)
            $shiftMulai = Carbon::parse('19:00');
            $shiftSelesaiNextDay = Carbon::parse('07:00')->addDay();

            // Cek apakah shift malam sudah dimulai
            // Shift malam aktif jika: sekarang >= 19:00 ATAU sekarang < 07:00
            $shiftSudahDimulai = $waktuSekarang->hour >= 19 || $waktuSekarang->hour < 7;

            if (!$shiftSudahDimulai) {
                // Shift malam belum dimulai (masih siang/sore)
                return false; // Semua patroli shift malam belum terlewat
            }

            // Jika shift sudah lewat 07:00 pagi (hari berikutnya setelah shift malam selesai)
            if ($waktuSekarang->hour >= 7 && $waktuSekarang->hour < 19) {
                return true; // Shift malam sudah selesai, semua patroli terlewat
            }
        }

        // ===== CEK PER PATROLI (HANYA JIKA SHIFT SUDAH DIMULAI) =====
        $mulai = Carbon::parse($jamMulai);
        $selesai = Carbon::parse($jamSelesai);

        // Handle midnight crossing untuk patroli shift malam
        if ($jenisShift == 2) {
            // Jika jam selesai < jam mulai, berarti lewat tengah malam
            if ($selesai->lt($mulai)) {
                $selesai->addDay();
            }

            // Jika sekarang di dini hari (00:00 - 06:59)
            // Berarti ini adalah lanjutan dari shift malam yang dimulai kemarin
            if ($waktuSekarang->hour < 7) {
                // Untuk patroli yang jam mulainya juga dini hari (misal 01:30)
                if ($mulai->hour < 7) {
                    // Patroli ini untuk hari ini (dini hari), bukan kemarin
                    // Cek normal
                    return $waktuSekarang->gt($selesai);
                } else {
                    // Patroli ini jam mulainya masih kemarin malam (misal 19:30)
                    // Sudah pasti terlewat karena sekarang sudah dini hari
                    return true;
                }
            } else if ($waktuSekarang->hour >= 19) {
                // Sekarang di malam hari (19:00 - 23:59)
                // Shift baru dimulai

                // Untuk patroli yang jam mulainya dini hari (misal 01:30)
                if ($mulai->hour < 7) {
                    // Patroli ini untuk besok dini hari, belum terlewat
                    return false;
                } else {
                    // Patroli ini untuk malam ini (misal 19:30, 21:30)
                    // Cek normal
                    return $waktuSekarang->gt($selesai);
                }
            }
        } else {
            // Shift pagi - patroli tidak lewat tengah malam
            return $waktuSekarang->gt($selesai);
        }

        return false;
    }


    /**
     * Get status patroli (available, locked, completed, expired)
     */
    private function getStatusPatroli($jenisPatroli, $jenisShift, $userId, $tanggal)
    {
        // Cek apakah sudah complete (17 area) oleh SIAPAPUN
        $jumlah = Patroli::whereDate('tanggal', $tanggal)
            ->where('jenis_patroli', $jenisPatroli)
            ->distinct('wilayah')
            ->count('wilayah');

        if ($jumlah >= 17) {
            return 'completed';
        }

        // Cek apakah patroli sudah terlewat (hanya untuk hari ini)
        if (Carbon::parse($tanggal)->isToday() && $this->isPatroliTerlewat($jenisPatroli, $jenisShift)) {
            return 'expired';
        }

        // Semua patroli selalu available (claim system yang mengontrol)
        return 'available';
    }

    /**
     * Cek status shift user berdasarkan presensi
     */
    private function checkShiftStatus($userId, $tanggal)
    {
        $todayShiftData = Shift::with('shiftRule')
            ->where('id_pengguna', $userId)
            ->whereDate('tanggal', $tanggal)
            ->first();

        if (!$todayShiftData || !$todayShiftData->shiftRule) {
            return [
                'is_off' => true,
                'nama_shift' => 'OFF',
                'status' => 'TIDAK ADA JADWAL',
                'jenis_shift_id' => null
            ];
        }

        $shiftRule = $todayShiftData->shiftRule;

        if ($shiftRule->jenis_shift === 'Off') {
            return [
                'is_off' => true,
                'nama_shift' => 'OFF',
                'status' => 'LIBUR',
                'jenis_shift_id' => null
            ];
        }

        $namaShift = strtoupper($shiftRule->jenis_shift);
        $jenisShiftId = $namaShift === 'PAGI' ? 1 : 2;

        return [
            'is_off' => false,
            'nama_shift' => $namaShift,
            'status' => 'AKTIF',
            'jenis_shift_id' => $jenisShiftId
        ];
    }


    /**
     * Menampilkan daftar patroli (Halaman Index)
     */
    /**
     * Menampilkan daftar patroli (Halaman Index)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $tanggalTerpilih = $request->input('tanggal')
            ? Carbon::parse($request->input('tanggal'))
            : Carbon::today();

        $allPatrols = Patroli::whereDate('tanggal', $tanggalTerpilih)
            ->orderBy('jenis_patroli', 'asc')
            ->get();

        $patrolGroups = $allPatrols->groupBy('jenis_patroli');

        $allClaims = PatroliClaim::where('tanggal', $tanggalTerpilih)
            ->with('pengguna')
            ->get();

        $displayData = collect();

        // ===== 1. TAMBAHKAN PATROLI YANG SUDAH DI-CLAIM =====
        foreach ($allClaims as $claim) {
            $jenisPatroli = $claim->jenis_patroli;
            $progressCount = Patroli::whereDate('tanggal', $tanggalTerpilih)
                ->where('jenis_patroli', $jenisPatroli)
                ->distinct('wilayah')
                ->count('wilayah');
            $checkpoints = $patrolGroups->get($jenisPatroli, collect());

            $idShiftPatroli = $checkpoints->first()->id_shift ?? null;

            $displayData->push([
                'jenis_patroli' => $jenisPatroli,
                'nama_petugas' => $claim->pengguna->nama_lengkap ?? 'Unknown',
                'progress' => $progressCount,
                'checkpoints' => $checkpoints,
                'has_checkpoints' => $checkpoints->isNotEmpty(),
                'is_completed' => $progressCount >= 17,
                'id_shift' => $idShiftPatroli,
                'is_expired' => false, // Sudah di-claim, bukan expired
                'expired_time' => null,
            ]);
        }

        $isToday = $tanggalTerpilih->isToday();
        $isShiftOff = false;
        $namaShift = null;
        $statusShift = null;
        $jenisShift = null;
        $isWaktuShiftAktif = false; // ← TAMBAHKAN INI (SET DEFAULT FALSE)

        if ($isToday) {
            $shiftStatus = $this->checkShiftStatus($user->id_pengguna, $tanggalTerpilih);

            $isShiftOff = $shiftStatus['is_off'];
            $namaShift = $shiftStatus['nama_shift'];
            $statusShift = $shiftStatus['status'];

            if (!$isShiftOff) {
                $jenisShift = $shiftStatus['nama_shift'] === 'PAGI' ? 1 : 2;
                $isWaktuShiftAktif = $this->isWaktuShiftAktif($jenisShift); // ← UPDATE INI
            }
        }

        // ===== 2. CEK PATROLI YANG TERLEWAT TAPI BELUM DI-CLAIM =====
        if ($isToday && !$isShiftOff && isset($jenisShift)) {
            $jadwalPatroli = $this->getJadwalPatroli()[$jenisShift];
            $claimedPatrolis = $displayData->pluck('jenis_patroli')->toArray();

            foreach ($jadwalPatroli as $namaPatroli => $waktu) {
                // Skip jika patroli ini sudah di-claim
                if (in_array($namaPatroli, $claimedPatrolis)) {
                    continue;
                }

                // Cek apakah patroli ini terlewat
                if ($this->isPatroliTerlewat($namaPatroli, $jenisShift)) {
                    // Tambahkan ke displayData sebagai patroli terlewat
                    $displayData->push([
                        'jenis_patroli' => $namaPatroli,
                        'nama_petugas' => null,
                        'progress' => 0,
                        'checkpoints' => collect(),
                        'has_checkpoints' => false,
                        'is_completed' => false,
                        'id_shift' => $jenisShift,
                        'is_expired' => true, // ← PATROLI TERLEWAT
                        'expired_time' => $waktu[1], // Jam selesai
                    ]);
                }
            }
        }

        // ===== 3. SORT BERDASARKAN NOMOR PATROLI =====
        // ===== 3. SORT BERDASARKAN PRIORITAS & NOMOR PATROLI =====
        $displayData = $displayData->sortBy(function ($item) {
            // Ekstrak nomor patroli
            preg_match('/\d+/', $item['jenis_patroli'], $matches);
            $nomorPatroli = (int) ($matches[0] ?? 0);

            // Prioritas tampilan:
            // 1. Patroli sedang PROSES (belum selesai, tidak expired)
            // 2. Patroli TERLEWAT (expired)
            // 3. Patroli SELESAI

            if (!$item['is_expired'] && !$item['is_completed']) {
                // PROSES - prioritas tertinggi (00-05)
                return '1_' . str_pad($nomorPatroli, 2, '0', STR_PAD_LEFT);
            } elseif ($item['is_expired']) {
                // TERLEWAT - prioritas kedua (06-11)
                return '2_' . str_pad($nomorPatroli, 2, '0', STR_PAD_LEFT);
            } else {
                // SELESAI - prioritas terakhir (12-17)
                return '3_' . str_pad($nomorPatroli, 2, '0', STR_PAD_LEFT);
            }
        })->values();

        return view('anggota.patroli-index', [
            'displayData' => $displayData,
            'tanggalTerpilih' => $tanggalTerpilih,
            'isShiftOff' => $isShiftOff,
            'namaShift' => $namaShift,
            'statusShift' => $statusShift,
            'jenisShift' => $jenisShift,
            'isWaktuShiftAktif' => $isWaktuShiftAktif,
        ]);
    }

    /**
     * Halaman Grid 17 Area (Create Session)
     */
    public function createSession(Request $request)
    {
        $user = Auth::user();
        $tanggal = Carbon::today();

        // ===== CEK SHIFT STATUS DULU SEBELUM LANJUT =====
        $shiftStatus = $this->checkShiftStatus($user->id_pengguna, $tanggal);

        // Jika OFF/LIBUR, redirect kembali dengan pesan error
        if ($shiftStatus['is_off']) {
            return redirect()->route('anggota.patroli.index')
                ->with('error', 'Anda tidak dapat melakukan patroli karena sedang OFF/LIBUR');
        }

        // ===== AMBIL JENIS SHIFT DARI DATABASE (BUKAN DARI $user->jenis_shift) =====
        $jenisShift = $shiftStatus['nama_shift'] === 'PAGI' ? 1 : 2;
        $namaShift = $shiftStatus['nama_shift'];

        // 1. Opsi dropdown
        $opsiJenisPatroli = [
            'Patroli 1',
            'Patroli 2',
            'Patroli 3',
            'Patroli 4',
            'Patroli 5',
            'Patroli 6'
        ];

        // 2. Build status untuk setiap patroli
        $statusPatroli = [];
        foreach ($opsiJenisPatroli as $opsi) {
            $statusPatroli[$opsi] = $this->getStatusPatroli($opsi, $jenisShift, $user->id_pengguna, $tanggal);
        }

        // 3. Tentukan patroli yang dipilih
        $jenisPatroliTerpilih = $request->input('jenis_patroli'); // dari dropdown, kalau ada
        $jadwalPatroli = $this->getJadwalPatroli()[$jenisShift];

        if (!$jenisPatroliTerpilih) {
            // Kalau datang dari tombol "+" (tidak bawa query jenis_patroli),
            // pilih patroli yang sesuai jam sekarang.
            $now = Carbon::now();
            $jenisPatroliTerpilih = 'Patroli 1'; // default fallback

            foreach ($jadwalPatroli as $namaPatroli => [$jamMulai, $jamSelesai]) {
                // Skip jika sudah expired
                if ($statusPatroli[$namaPatroli] === 'expired') {
                    continue;
                }

                $start = Carbon::parse($jamMulai);
                $end = Carbon::parse($jamSelesai);
                $nowCompare = $now->copy();

                // Handle jadwal yang melewati tengah malam
                if ($end->lt($start)) {
                    $end->addDay();
                    if ($nowCompare->lt($start)) {
                        $nowCompare->addDay();
                    }
                }

                // Jika sekarang berada di dalam rentang patroli ini → pilih ini
                if ($nowCompare->between($start, $end)) {
                    $jenisPatroliTerpilih = $namaPatroli;
                    break;
                }

                // Jika belum masuk jam mulai patroli ini dan belum ada yang kepilih,
                // jadikan patroli ini sebagai kandidat berikutnya.
                if ($nowCompare->lt($start)) {
                    $jenisPatroliTerpilih = $namaPatroli;
                    break;
                }
            }
        }

        // ===== DATA PATROLI TERLEWAT (untuk ditampilkan di card) =====
        $patroliTerlewat = [];
        $jadwalPatroliAll = $this->getJadwalPatroli()[$jenisShift];

        foreach ($jadwalPatroliAll as $namaPatroli => $waktu) {
            // Skip patroli yang sedang dipilih/aktif
            if ($namaPatroli === $jenisPatroliTerpilih) {
                continue;
            }

            // Skip jika sudah completed
            if ($statusPatroli[$namaPatroli] === 'completed') {
                continue;
            }

            // Jika terlewat, masukkan ke array
            if ($this->isPatroliTerlewat($namaPatroli, $jenisShift)) {
                $patroliTerlewat[] = [
                    'jenis_patroli' => $namaPatroli,
                    'jam_selesai' => $waktu[1]
                ];
            }
        }

        // ===== VALIDASI: CEK APAKAH PATROLI TERPILIH SUDAH EXPIRED =====
        if ($statusPatroli[$jenisPatroliTerpilih] === 'expired') {
            return view('anggota.patroli-create-session', [
                'semuaArea' => [],
                'opsiJenisPatroli' => $opsiJenisPatroli,
                'jenisPatroliTerpilih' => $jenisPatroliTerpilih,
                'completedCheckpoints' => [],
                'totalCompleted' => 0,
                'statusPatroli' => $statusPatroli,
                'jenisShift' => $jenisShift,
                'namaShift' => $namaShift,
                'jadwalPatroli' => $this->getJadwalPatroli()[$jenisShift],
                'isClaimed' => false,
                'isOwner' => false,
                'claimedBy' => null,
                'patroliExpired' => true,
                'expiredMessage' => $jenisPatroliTerpilih . ' sudah terlewat. Silakan melakukan laporan ke Komandan.',
                'patroliTerlewat' => $patroliTerlewat, // Tambahkan data patroli terlewat
            ]);
        }

        // 4. Daftar 17 Area
        $semuaArea = [
            'AREA POS 2',
            'LOBBY VVIP',
            'LOBBY AUDIT',
            'KOLAM IKAN VVIP',
            'AREA BAU',
            'AREA KANTIN',
            'AREA BAAK',
            'AKSES LORONG GD 3',
            'AKSES LORONG GD 2',
            'AREA POS 3',
            'AKSES BESI GD 2',
            'AKSES KACA GD 2',
            'AKSES SELATAN AUDIT',
            'AKSES RUANG LETKOR',
            'AKSES PARKIR BASEMENT',
            'AKSES LIFT GD 2',
            'AREA POS 1'
        ];

        // 5. Ambil checkpoint yang sudah selesai oleh SIAPAPUN (bukan hanya user login)
        $completedCheckpoints = Patroli::whereDate('tanggal', $tanggal)
            ->where('jenis_patroli', $jenisPatroliTerpilih)
            ->pluck('wilayah')
            ->map(function ($value) {
                return strtoupper($value);
            })
            ->unique()
            ->values()
            ->toArray();

        // ===== CEK STATUS CLAIM =====
        $claimStatus = $this->getClaimStatus($jenisPatroliTerpilih, $tanggal, $user->id_pengguna);

        return view('anggota.patroli-create-session', [
            'semuaArea' => $semuaArea,
            'opsiJenisPatroli' => $opsiJenisPatroli,
            'jenisPatroliTerpilih' => $jenisPatroliTerpilih,
            'completedCheckpoints' => $completedCheckpoints,
            'totalCompleted' => count($completedCheckpoints),
            'statusPatroli' => $statusPatroli,
            'jenisShift' => $jenisShift,
            'namaShift' => $namaShift,
            'jadwalPatroli' => $this->getJadwalPatroli()[$jenisShift],
            // Tambahan untuk claim
            'isClaimed' => $claimStatus['is_claimed'],
            'isOwner' => $claimStatus['is_owner'],
            'claimedBy' => $claimStatus['claimed_by'] ?? null,
            'patroliTerlewat' => $patroliTerlewat, // Tambahkan data patroli terlewat
        ]);
    }

    /**
     * Halaman Kamera
     */
    public function createCheckpoint(Request $request)
    {
        $jenisPatroli = $request->query('jenis_patroli');
        $wilayah = $request->query('wilayah');

        if (!$jenisPatroli || !$wilayah) {
            abort(400, 'Jenis patroli dan wilayah diperlukan.');
        }

        return view('anggota.patroli-create-checkpoint', [
            'jenisPatroli' => $jenisPatroli,
            'wilayah' => $wilayah
        ]);
    }

    /**
     * Menyimpan 1 foto checkpoint
     */
    public function storeCheckpoint(Request $request)
    {
        $request->validate([
            'foto_base64' => 'required|string',
            'jenis_patroli' => 'required|string',
            'wilayah' => 'required|string',
        ]);

        $user = Auth::user();
        $jenisShift = $user->jenis_shift ?? 1;
        $wilayahUpper = strtoupper($request->wilayah);
        $tanggalHariIni = Carbon::today();

        // ===== VALIDASI SHIFT STATUS =====
        $shiftStatus = $this->checkShiftStatus($user->id_pengguna, $tanggalHariIni);

        if ($shiftStatus['is_off']) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak dapat melakukan patroli karena sedang OFF/LIBUR!'
            ], 403);
        }

        // ===== VALIDASI PATROLI SUDAH TERLEWAT =====
        if ($this->isPatroliTerlewat($request->jenis_patroli, $jenisShift)) {
            return response()->json([
                'status' => 'error',
                'message' => $request->jenis_patroli . ' sudah terlewat dan tidak dapat diakses lagi!'
            ], 403);
        }

        // ===== VALIDASI CLAIM: HANYA PEMILIK CLAIM YANG BOLEH INPUT =====
        $claim = PatroliClaim::where('tanggal', $tanggalHariIni)
            ->where('jenis_patroli', $request->jenis_patroli)
            ->first();

        // Jika tidak ada claim, reject
        if (!$claim) {
            return response()->json([
                'status' => 'error',
                'message' => 'Patroli ini belum di-claim oleh siapapun!'
            ], 403);
        }

        // Jika ada claim tapi bukan milik user ini, reject
        if ($claim->id_pengguna != $user->id_pengguna) {
            $namaPetugas = $claim->pengguna->nama_lengkap ?? 'Anggota lain';
            return response()->json([
                'status' => 'error',
                'message' => "Patroli ini sedang dilakukan oleh {$namaPetugas}!"
            ], 403);
        }

        // ===== CEK DUPLIKAT SECARA GLOBAL =====
        $sudahAda = Patroli::whereDate('tanggal', $tanggalHariIni)
            ->where('jenis_patroli', $request->jenis_patroli)
            ->where('wilayah', $wilayahUpper)
            ->exists();

        if ($sudahAda) {
            return response()->json([
                'status' => 'error',
                'message' => 'Area ' . $wilayahUpper . ' sudah difoto oleh anggota lain!'
            ], 400);
        }

        try {
            $imageData = $request->foto_base64;
            if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
                $imageData = substr($imageData, strpos($imageData, ',') + 1);
                $imageData = base64_decode($imageData);
            } else {
                $imageData = base64_decode($imageData);
            }

            $fileName = 'patroli/' . $user->id_pengguna . '_' . Str::uuid() . '.jpg';
            Storage::disk('public')->put($fileName, $imageData);

            Patroli::create([
                'tanggal' => $tanggalHariIni,
                'waktu_exact' => now(),
                'jenis_patroli' => $request->jenis_patroli,
                'wilayah' => $wilayahUpper,
                'foto' => $fileName,
                'id_pengguna' => $user->id_pengguna,
            ]);

            return response()->json(['status' => 'success', 'message' => 'Checkpoint disimpan']);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Submit Patroli (Validasi 17 area)
     */
    public function submitSession(Request $request)
    {
        $request->validate([
            'jenis_patroli' => 'required|string',
        ]);

        $user = Auth::user();
        $tanggal = Carbon::today();
        $jenisPatroli = $request->jenis_patroli;
        $jenisShift = $user->jenis_shift ?? 1;

        // ===== VALIDASI SHIFT STATUS =====
        $shiftStatus = $this->checkShiftStatus($user->id_pengguna, $tanggal);

        if ($shiftStatus['is_off']) {
            return redirect()->back()
                ->with('error', 'Anda tidak dapat melakukan patroli karena sedang OFF/LIBUR');
        }

        // ===== VALIDASI PATROLI SUDAH TERLEWAT =====
        if ($this->isPatroliTerlewat($jenisPatroli, $jenisShift)) {
            return redirect()->back()
                ->with('error', $jenisPatroli . ' sudah terlewat dan tidak dapat disubmit.');
        }

        // ===== CEK JUMLAH SECARA GLOBAL =====
        $jumlahCheckpoint = Patroli::whereDate('tanggal', $tanggal)
            ->where('jenis_patroli', $jenisPatroli)
            ->distinct('wilayah')
            ->count('wilayah');

        if ($jumlahCheckpoint != 17) {
            return redirect()->back()->with('error', 'Semua 17 area belum selesai.');
        }

        return redirect()->route('anggota.patroli.index')
            ->with('success', 'Sesi ' . $jenisPatroli . ' berhasil disubmit!');
    }

    /**
     * METHOD: CEK STATUS AREA (UNTUK LIVE UPDATE)
     */
    public function checkArea(Request $request)
    {
        $jenisPatroli = $request->query('jenis_patroli');
        $wilayah = $request->query('wilayah');
        $tanggal = Carbon::today();

        $patroli = Patroli::whereDate('tanggal', $tanggal)
            ->where('jenis_patroli', $jenisPatroli)
            ->where('wilayah', strtoupper($wilayah))
            ->first();

        if ($patroli) {
            return response()->json([
                'sudah_ada' => true,
                'nama_petugas' => $patroli->nama_lengkap ?? 'Anggota lain',
                'waktu' => Carbon::parse($patroli->waktu_exact)->format('H:i:s')
            ]);
        }

        return response()->json([
            'sudah_ada' => false
        ]);
    }

    /**
     * Claim patroli oleh anggota
     */
    /**
     * Claim patroli oleh anggota
     */
    public function claimPatroli(Request $request)
    {
        $request->validate([
            'jenis_patroli' => 'required|string',
        ]);

        $user = Auth::user();
        $tanggal = Carbon::today();
        $jenisPatroli = $request->jenis_patroli;
        $jenisShift = $user->jenis_shift ?? 1;

        // ===== VALIDASI SHIFT STATUS =====
        $shiftStatus = $this->checkShiftStatus($user->id_pengguna, $tanggal);

        if ($shiftStatus['is_off']) {
            return redirect()->back()
                ->with('error', 'Anda tidak dapat claim patroli karena sedang OFF/LIBUR');
        }

        // ===== VALIDASI PATROLI SUDAH TERLEWAT =====
        if ($this->isPatroliTerlewat($jenisPatroli, $jenisShift)) {
            return redirect()->back()
                ->with('error', $jenisPatroli . ' sudah terlewat dan tidak dapat di-claim lagi.');
        }

        // Cek apakah sudah ada yang claim patroli ini hari ini
        $existingClaim = PatroliClaim::where('tanggal', $tanggal)
            ->where('jenis_patroli', $jenisPatroli)
            ->first();

        if ($existingClaim) {
            if ($existingClaim->id_pengguna == $user->id_pengguna) {
                return redirect()->back()->with('info', 'Anda sudah claim patroli ini.');
            } else {
                $namaPetugas = $existingClaim->pengguna->nama_lengkap ?? 'Anggota lain';
                return redirect()->back()->with('error', "Patroli ini sudah di-claim oleh {$namaPetugas}");
            }
        }

        // Belum ada yang claim, buat claim baru
        PatroliClaim::create([
            'id_pengguna' => $user->id_pengguna,
            'tanggal' => $tanggal,
            'jenis_patroli' => $jenisPatroli,
            'claimed_at' => now()
        ]);

        // ← UBAH BAGIAN INI: Tambahkan nama user di flash message
        return redirect()->back()->with('success', $jenisPatroli . ' sedang dilakukan oleh ' . $user->nama_lengkap);
    }


    /**
     * Helper untuk cek status claim patroli
     */
    private function getClaimStatus($jenisPatroli, $tanggal, $userId)
    {
        $claim = PatroliClaim::where('tanggal', $tanggal)
            ->where('jenis_patroli', $jenisPatroli)
            ->first();

        if (!$claim) {
            return [
                'is_claimed' => false,
                'is_owner' => false,
                'claimed_by' => null
            ];
        }

        $isOwner = $claim->id_pengguna == $userId;

        return [
            'is_claimed' => true,
            'is_owner' => $isOwner,
            'claimed_by' => $claim->pengguna->nama_lengkap ?? 'Anggota lain',
            'claimed_at' => $claim->claimed_at
        ];
    }

    /**
     * Cek apakah waktu sekarang sesuai dengan shift user
     */
    private function isWaktuShiftAktif($jenisShift)
    {
        $now = Carbon::now();
        if ($jenisShift == 1) {
            // Shift PAGI: 07:00 - 19:00
            $mulai = Carbon::parse('07:00');
            $selesai = Carbon::parse('19:00');
            return $now->between($mulai, $selesai);
        } else {

            // Shift MALAM: 19:00 - 07:00 (lewat tengah malam)
            $mulai = Carbon::parse('19:00');
            $selesai = Carbon::parse('07:00')->addDay();

            // Jika sekarang sebelum tengah malam (19:00 - 23:59)
            if ($now->hour >= 19) {
                return true;
            }

            // Jika sekarang setelah tengah malam (00:00 - 07:00)
            if ($now->hour < 7) {
                return true;
            }
            return false;
        }
    }
}