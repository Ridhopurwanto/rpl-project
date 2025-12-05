<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Patroli;
use App\Models\PatroliClaim;
use App\Models\Shift;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PatroliController extends Controller
{
    /**
     * Jadwal Patroli berdasarkan Shift
     * jenis_shift: 1 = Pagi, 2 = Malam
     */
    private function getJadwalPatroli()
    {
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

    /**
     * Get nama shift untuk display
     */
    private function getNamaShift($jenisShift)
    {
        return $jenisShift == 1 ? 'Pagi' : 'Malam';
    }

    /**
     * Cek apakah waktu sekarang valid untuk patroli tertentu
     * @param string $jenisPatroli - Nama patroli (Patroli 1, Patroli 2, dst)
     * @param int $jenisShift - 1 = Pagi, 2 = Malam
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
     * Get status patroli (available, locked, completed)
     * @param int $jenisShift - 1 = Pagi, 2 = Malam
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

        // Semua patroli selalu available (claim system yang mengontrol)
        return 'available';
    }

    /**
     * Cek status shift user berdasarkan presensi (SAMA SEPERTI DI HALAMAN PRESENSI)
     */
    private function checkShiftStatus($userId, $tanggal)
    {
        // Ambil data shift hari ini dari tabel SHIFT (sama seperti PresensiController)
        $todayShiftData = Shift::with('shiftRule')
            ->where('id_pengguna', $userId)
            ->whereDate('tanggal', $tanggal)
            ->first();

        if (!$todayShiftData || !$todayShiftData->shiftRule) {
            // Tidak ada jadwal shift
            return [
                'is_off' => true,
                'nama_shift' => 'OFF',
                'status' => 'TIDAK ADA JADWAL'
            ];
        }

        $shiftRule = $todayShiftData->shiftRule;

        // Cek jenis_shift di shift_rule
        if ($shiftRule->jenis_shift === 'Off') {
            // Shift OFF/LIBUR
            return [
                'is_off' => true,
                'nama_shift' => 'OFF',
                'status' => 'LIBUR'
            ];
        }

        // Shift AKTIF (Pagi, Malam, atau Non Shift)
        $namaShift = strtoupper($shiftRule->jenis_shift);

        return [
            'is_off' => false,
            'nama_shift' => $namaShift,
            'status' => 'AKTIF'
        ];
    }

    /*** Menampilkan daftar patroli (Halaman Index)*/
    public function index(Request $request)
    {
        $user = Auth::user();
        $tanggalTerpilih = $request->input('tanggal')
            ? Carbon::parse($request->input('tanggal'))
            : Carbon::today();

        // Ambil semua checkpoint yang sudah ada
        $allPatrols = Patroli::whereDate('tanggal', $tanggalTerpilih)
            ->orderBy('jenis_patroli', 'asc')
            ->get();

        $patrolGroups = $allPatrols->groupBy('jenis_patroli');

        $allClaims = PatroliClaim::where('tanggal', $tanggalTerpilih)
            ->with('pengguna')
            ->get();

        $displayData = collect();

        foreach ($allClaims as $claim) {
            $jenisPatroli = $claim->jenis_patroli;
            $progressCount = Patroli::whereDate('tanggal', $tanggalTerpilih)
                ->where('jenis_patroli', $jenisPatroli)
                ->distinct('wilayah')
                ->count('wilayah');
            $checkpoints = $patrolGroups->get($jenisPatroli, collect());

            $displayData->push([
                'jenis_patroli' => $jenisPatroli,
                'nama_petugas' => $claim->pengguna->nama_lengkap ?? 'Unknown',
                'progress' => $progressCount,
                'checkpoints' => $checkpoints,
                'has_checkpoints' => $checkpoints->isNotEmpty(),
                'is_completed' => $progressCount >= 17
            ]);
        }

        $displayData = $displayData->sortBy(function ($item) {
            preg_match('/\d+/', $item['jenis_patroli'], $matches);
            return (int) ($matches[0] ?? 0);
        });

        // ===== PERBAIKAN: CEK SHIFT DARI checkShiftStatus() =====
        $isToday = $tanggalTerpilih->isToday();
        $isShiftOff = false;
        $namaShift = null;
        $statusShift = null;

        if ($isToday) {
            // Gunakan method checkShiftStatus() yang sudah benar
            $shiftStatus = $this->checkShiftStatus($user->id_pengguna, $tanggalTerpilih);

            $isShiftOff = $shiftStatus['is_off'];
            $namaShift = $shiftStatus['nama_shift'];
            $statusShift = $shiftStatus['status'];
        }

        return view('anggota.patroli-index', [
            'displayData' => $displayData,
            'tanggalTerpilih' => $tanggalTerpilih,
            'isShiftOff' => $isShiftOff,
            'namaShift' => $namaShift,
            'statusShift' => $statusShift,
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

        $jenisShift = $user->jenis_shift ?? 1;
        $namaShift = $this->getNamaShift($jenisShift);

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

        // ===== VALIDASI SHIFT STATUS =====
        $shiftStatus = $this->checkShiftStatus($user->id_pengguna, $tanggal);

        if ($shiftStatus['is_off']) {
            return redirect()->back()
                ->with('error', 'Anda tidak dapat melakukan patroli karena sedang OFF/LIBUR');
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
     * ===== METHOD: CEK STATUS AREA (UNTUK LIVE UPDATE) =====
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
    public function claimPatroli(Request $request)
    {
        $request->validate([
            'jenis_patroli' => 'required|string',
        ]);

        $user = Auth::user();
        $tanggal = Carbon::today();
        $jenisPatroli = $request->jenis_patroli;

        // ===== VALIDASI SHIFT STATUS =====
        $shiftStatus = $this->checkShiftStatus($user->id_pengguna, $tanggal);

        if ($shiftStatus['is_off']) {
            return redirect()->back()
                ->with('error', 'Anda tidak dapat claim patroli karena sedang OFF/LIBUR');
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

        return redirect()->back()->with('success', 'Berhasil claim patroli!');
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
}