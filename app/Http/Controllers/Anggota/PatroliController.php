<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Patroli;
use App\Models\PatroliClaim;
use App\Models\PatroliRule;
use App\Models\Shift;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PatroliController extends Controller
{
    /**
     * Jadwal Patroli dari Database dengan fallback default
     */
    private function getJadwalPatroli()
    {
        $rules = PatroliRule::all();
        $jadwal = [1 => [], 2 => []];

        foreach ($rules as $rule) {
            $jenisShiftKey = $rule->jenis_shift === 'Pagi' ? 1 : 2;
            $jadwal[$jenisShiftKey][$rule->jenis_patroli] = [
                Carbon::parse($rule->jam_mulai)->format('H:i'),
                Carbon::parse($rule->jam_selesai)->format('H:i')
            ];
        }

        if (empty($jadwal[1]) && empty($jadwal[2])) {
            return [
                1 => [
                    'Patroli 1' => ['07:30', '08:30'], 'Patroli 2' => ['08:30', '10:30'],
                    'Patroli 3' => ['11:30', '12:30'], 'Patroli 4' => ['13:40', '15:30'],
                    'Patroli 5' => ['15:30', '17:30'], 'Patroli 6' => ['17:30', '18:40'],
                ],
                2 => [
                    'Patroli 1' => ['19:30', '20:20'], 'Patroli 2' => ['21:30', '22:30'],
                    'Patroli 3' => ['23:30', '00:30'], 'Patroli 4' => ['01:30', '02:30'],
                    'Patroli 5' => ['03:30', '04:30'], 'Patroli 6' => ['05:30', '06:30'],
                ]
            ];
        }

        return $jadwal;
    }

    private function getNamaShift($jenisShift)
    {
        return $jenisShift == 1 ? 'Pagi' : 'Malam';
    }

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

        if ($selesai->lt($mulai)) {
            $selesai->addDay();
            if ($waktuSekarang->lt($mulai)) {
                $waktuSekarang = $waktuSekarang->copy()->addDay();
            }
        }

        return $waktuSekarang->between($mulai, $selesai);
    }

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

        // Cek apakah shift sudah dimulai
        if ($jenisShift == 1) {
            $shiftMulai = Carbon::parse('07:00');
            $shiftSelesai = Carbon::parse('19:00');

            if ($waktuSekarang->lt($shiftMulai)) return false;
            if ($waktuSekarang->gte($shiftSelesai)) return true;
        } else {
            $shiftSudahDimulai = $waktuSekarang->hour >= 19 || $waktuSekarang->hour < 7;
            if (!$shiftSudahDimulai) return false;
            if ($waktuSekarang->hour >= 7 && $waktuSekarang->hour < 19) return true;
        }

        // Cek per patroli
        $mulai = Carbon::parse($jamMulai);
        $selesai = Carbon::parse($jamSelesai);

        if ($jenisShift == 2) {
            if ($selesai->lt($mulai)) {
                $selesai->addDay();
            }

            if ($waktuSekarang->hour < 7) {
                if ($mulai->hour < 7) {
                    return $waktuSekarang->gt($selesai);
                } else {
                    return true;
                }
            } else if ($waktuSekarang->hour >= 19) {
                if ($mulai->hour < 7) {
                    return false;
                } else {
                    return $waktuSekarang->gt($selesai);
                }
            }
        } else {
            return $waktuSekarang->gt($selesai);
        }

        return false;
    }

    /**
     * Get status patroli (available, locked, completed, expired)
     */
    private function getStatusPatroli($jenisPatroli, $jenisShift, $idPengguna, $tanggal, $idShift = null)
    {
        $query = Patroli::whereDate('tanggal', $tanggal)
            ->where('jenis_patroli', $jenisPatroli);

        if ($idShift) {
            $query->where('id_shift', $idShift);
        }

        $completed = $query->distinct('wilayah')->count('wilayah');

        if ($completed >= 17) {
            return 'completed';
        }

        if ($this->isPatroliTerlewat($jenisPatroli, $jenisShift)) {
            return 'expired';
        }

        return 'available';
    }

    /**
     * Cek status shift user berdasarkan presensi
     */
    private function checkShiftStatus($idPengguna, $tanggal)
    {
        $shift = Shift::where('id_pengguna', $idPengguna)
            ->whereDate('tanggal', $tanggal)
            ->with('shiftRule')
            ->first();

        $defaultOff = [
            'is_off' => true,
            'nama_shift' => 'OFF',
            'jenis_shift_id' => null,
            'id_shift' => null,
        ];

        if (!$shift || !$shift->shiftRule) {
            return $defaultOff;
        }

        $shiftRule = $shift->shiftRule;
        $jenisShift = $shiftRule->jenis_shift;

        if (in_array($jenisShift, ['Non Shift', 'Off'])) {
            return [
                'is_off' => true,
                'nama_shift' => $jenisShift,
                'jenis_shift_id' => $shiftRule->idshift_rule,
                'id_shift' => $shift->id_shift,
            ];
        }

        return [
            'is_off' => false,
            'nama_shift' => strtoupper($jenisShift),
            'jenis_shift_id' => $shiftRule->idshift_rule,
            'id_shift' => $shift->id_shift,
        ];
    }

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
            ->with(['pengguna', 'shift.shiftRule']) // ✅ Eager load shift info
            ->get();

        $displayData = collect();

        // 1. Patroli yang sudah di-claim
        foreach ($allClaims as $claim) {
            $jenisPatroli = $claim->jenis_patroli;
            $idShiftClaim = $claim->id_shift; // ✅ Ambil id_shift dari claim

            // ✅ PERBAIKAN: Filter progress per shift
            $progressCount = Patroli::whereDate('tanggal', $tanggalTerpilih)
                ->where('jenis_patroli', $jenisPatroli)
                ->where('id_shift', $idShiftClaim) // ✅ Filter per shift
                ->distinct('wilayah')
                ->count('wilayah');

            $checkpoints = $patrolGroups->get($jenisPatroli, collect());

            // ✅ Ambil nama shift dari relasi
            $namaShift = 'N/A';
            if ($claim->shift && $claim->shift->shiftRule) {
                $namaShift = $claim->shift->shiftRule->jenis_shift;
            }

            $displayData->push([
                'jenis_patroli' => $jenisPatroli,
                'nama_petugas' => $claim->pengguna->nama_lengkap ?? 'Unknown',
                'progress' => $progressCount,
                'checkpoints' => $checkpoints,
                'has_checkpoints' => $checkpoints->isNotEmpty(),
                'is_completed' => $progressCount >= 17,
                'id_shift' => $idShiftClaim, // ✅ Dari claim
                'nama_shift' => $namaShift, // ✅ Untuk tampilan card
                'is_expired' => false,
                'expired_time' => null,
            ]);
        }

        $isToday = $tanggalTerpilih->isToday();
        $isShiftOff = false;
        $namaShift = null;
        $jenisShift = null;
        $isWaktuShiftAktif = false;
        $shiftStatus = null; // ✅ Tambahkan variabel

        if ($isToday) {
            $shiftStatus = $this->checkShiftStatus($user->id_pengguna, $tanggalTerpilih);
            $isShiftOff = $shiftStatus['is_off'];
            $namaShift = $shiftStatus['nama_shift'];

            if (!$isShiftOff) {
                $jenisShift = $shiftStatus['nama_shift'] === 'PAGI' ? 1 : 2;
                $isWaktuShiftAktif = $this->isWaktuShiftAktif($jenisShift);
            }
        }

        // 2. Patroli terlewat tapi belum di-claim
        if ($isToday && !$isShiftOff && isset($jenisShift) && $shiftStatus) {
            $jadwalPatroli = $this->getJadwalPatroli()[$jenisShift];
            $claimedPatrolis = $displayData->pluck('jenis_patroli')->toArray();
            $idShiftUser = $shiftStatus['id_shift']; // ✅ Ambil id_shift user

            foreach ($jadwalPatroli as $namaPatroli => $waktu) {
                if (in_array($namaPatroli, $claimedPatrolis)) {
                    continue;
                }

                if ($this->isPatroliTerlewat($namaPatroli, $jenisShift)) {
                    $displayData->push([
                        'jenis_patroli' => $namaPatroli,
                        'nama_petugas' => null,
                        'progress' => 0,
                        'checkpoints' => collect(),
                        'has_checkpoints' => false,
                        'is_completed' => false,
                        'id_shift' => $idShiftUser, // ✅ PERBAIKAN: pakai id_shift user
                        'nama_shift' => $namaShift, // ✅ Untuk card
                        'is_expired' => true,
                        'expired_time' => $waktu[1],
                    ]);
                }
            }
        }

        // 3. Sort berdasarkan prioritas
        $displayData = $displayData->sortBy(function ($item) {
            preg_match('/\d+/', $item['jenis_patroli'], $matches);
            $nomorPatroli = (int) ($matches[0] ?? 0);

            if (!$item['is_expired'] && !$item['is_completed']) {
                return '1_' . str_pad($nomorPatroli, 2, '0', STR_PAD_LEFT);
            } elseif ($item['is_expired']) {
                return '2_' . str_pad($nomorPatroli, 2, '0', STR_PAD_LEFT);
            } else {
                return '3_' . str_pad($nomorPatroli, 2, '0', STR_PAD_LEFT);
            }
        })->values();

        return view('anggota.patroli-index', [
            'displayData' => $displayData,
            'tanggalTerpilih' => $tanggalTerpilih,
            'isShiftOff' => $isShiftOff,
            'namaShift' => $namaShift,
            'statusShift' => $isShiftOff ? 'OFF' : 'AKTIF', // ✅ Set di sini
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

        $shiftStatus = $this->checkShiftStatus($user->id_pengguna, $tanggal);

        if ($shiftStatus['is_off']) {
            return redirect()->route('anggota.patroli.index')
                ->with('error', 'Anda tidak dapat melakukan patroli karena sedang OFF/LIBUR');
        }

        $jenisShift = $shiftStatus['nama_shift'] === 'PAGI' ? 1 : 2;
        $namaShift = $shiftStatus['nama_shift'];
        $idShift = $shiftStatus['id_shift'];

        $opsiJenisPatroli = [
            'Patroli 1', 'Patroli 2', 'Patroli 3',
            'Patroli 4', 'Patroli 5', 'Patroli 6'
        ];

        $statusPatroli = [];
        foreach ($opsiJenisPatroli as $opsi) {
            $statusPatroli[$opsi] = $this->getStatusPatroli(
                $opsi,
                $jenisShift,
                $user->id_pengguna,
                $tanggal,
                $idShift
            );
        }

        $jenisPatroliTerpilih = $request->input('jenis_patroli');
        $jadwalPatroli = $this->getJadwalPatroli()[$jenisShift];

        // Auto-select patroli jika tidak ada parameter
        if (!$jenisPatroliTerpilih) {
            $now = Carbon::now();
            $jenisPatroliTerpilih = 'Patroli 1';

            foreach ($jadwalPatroli as $namaPatroli => [$jamMulai, $jamSelesai]) {
                if ($statusPatroli[$namaPatroli] === 'expired') continue;

                $start = Carbon::parse($jamMulai);
                $end = Carbon::parse($jamSelesai);
                $nowCompare = $now->copy();

                if ($end->lt($start)) {
                    $end->addDay();
                    if ($nowCompare->lt($start)) $nowCompare->addDay();
                }

                if ($nowCompare->between($start, $end)) {
                    $jenisPatroliTerpilih = $namaPatroli;
                    break;
                }

                if ($nowCompare->lt($start)) {
                    $jenisPatroliTerpilih = $namaPatroli;
                    break;
                }
            }
        }

        // Data patroli terlewat untuk card
        $patroliTerlewat = [];
        foreach ($jadwalPatroli as $namaPatroli => $waktu) {
            if ($namaPatroli === $jenisPatroliTerpilih) continue;
            if ($statusPatroli[$namaPatroli] === 'completed') continue;

            if ($this->isPatroliTerlewat($namaPatroli, $jenisShift)) {
                $patroliTerlewat[] = [
                    'jenis_patroli' => $namaPatroli,
                    'jam_selesai' => $waktu[1]
                ];
            }
        }

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
                'jadwalPatroli' => $jadwalPatroli,
                'isClaimed' => false,
                'isOwner' => false,
                'claimedBy' => null,
                'patroliExpired' => true,
                'expiredMessage' => $jenisPatroliTerpilih . ' sudah terlewat. Silakan melakukan laporan ke Komandan.',
                'patroliTerlewat' => $patroliTerlewat,
            ]);
        }

        $semuaArea = [
            'AREA POS 2', 'LOBBY VVIP', 'LOBBY AUDIT', 'KOLAM IKAN VVIP', 'AREA BAU',
            'AREA KANTIN', 'AREA BAAK', 'AKSES LORONG GD 3', 'AKSES LORONG GD 2',
            'AREA POS 3', 'AKSES BESI GD 2', 'AKSES KACA GD 2', 'AKSES SELATAN AUDIT',
            'AKSES RUANG LETKOR', 'AKSES PARKIR BASEMENT', 'AKSES LIFT GD 2', 'AREA POS 1'
        ];

        $completedCheckpoints = Patroli::whereDate('tanggal', $tanggal)
            ->where('jenis_patroli', $jenisPatroliTerpilih)
            ->where('id_shift', $idShift)
            ->pluck('wilayah')
            ->map(fn($value) => strtoupper($value))
            ->unique()
            ->values()
            ->toArray();

        $claimStatus = $this->getClaimStatus($jenisPatroliTerpilih, $tanggal, $user->id_pengguna, $idShift);

        $canClaimNow = true;
        $now = Carbon::now();
        if (isset($jadwalPatroli[$jenisPatroliTerpilih])) {
            $jamMulai = Carbon::parse($jadwalPatroli[$jenisPatroliTerpilih][0]);
            if ($now->lt($jamMulai)) $canClaimNow = false;
        }

        return view('anggota.patroli-create-session', [
            'semuaArea' => $semuaArea,
            'opsiJenisPatroli' => $opsiJenisPatroli,
            'jenisPatroliTerpilih' => $jenisPatroliTerpilih,
            'completedCheckpoints' => $completedCheckpoints,
            'totalCompleted' => count($completedCheckpoints),
            'statusPatroli' => $statusPatroli,
            'jenisShift' => $jenisShift,
            'namaShift' => $namaShift,
            'jadwalPatroli' => $jadwalPatroli,
            'isClaimed' => $claimStatus['is_claimed'],
            'isOwner' => $claimStatus['is_owner'],
            'claimedBy' => $claimStatus['claimed_by'] ?? null,
            'patroliTerlewat' => $patroliTerlewat,
            'patroliExpired' => false,
            'canClaimNow' => $canClaimNow,
        ]);
    }

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

    public function storeCheckpoint(Request $request)
    {
        $request->validate([
            'foto_base64' => 'required|string',
            'jenis_patroli' => 'required|string',
            'wilayah' => 'required|string',
        ]);

        $user = Auth::user();
        $wilayahUpper = strtoupper($request->wilayah);
        $tanggalHariIni = Carbon::today();

        $shiftStatus = $this->checkShiftStatus($user->id_pengguna, $tanggalHariIni);

        if ($shiftStatus['is_off']) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak dapat melakukan patroli karena sedang OFF/LIBUR!'
            ], 403);
        }

        $jenisShift = $shiftStatus['jenis_shift_id'];
        $idShift = $shiftStatus['id_shift'];

        if ($this->isPatroliTerlewat($request->jenis_patroli, $jenisShift)) {
            return response()->json([
                'status' => 'error',
                'message' => $request->jenis_patroli . ' sudah terlewat dan tidak dapat diakses lagi!'
            ], 403);
        }

        $claim = PatroliClaim::where('tanggal', $tanggalHariIni)
            ->where('jenis_patroli', $request->jenis_patroli)
            ->where('id_shift', $idShift)
            ->first();

        if (!$claim) {
            return response()->json(['status' => 'error', 'message' => 'Patroli ini belum di-claim oleh siapapun!'], 403);
        }

        if ($claim->id_pengguna != $user->id_pengguna) {
            $namaPetugas = $claim->pengguna->nama_lengkap ?? 'Anggota lain';
            return response()->json(['status' => 'error', 'message' => "Patroli ini sedang dilakukan oleh {$namaPetugas}!"], 403);
        }

        $sudahAda = Patroli::whereDate('tanggal', $tanggalHariIni)
            ->where('jenis_patroli', $request->jenis_patroli)
            ->where('wilayah', $wilayahUpper)
            ->where('id_shift', $idShift)
            ->exists();

        if ($sudahAda) {
            return response()->json(['status' => 'error', 'message' => 'Area ' . $wilayahUpper . ' sudah difoto!'], 400);
        }

        try {
            $imageData = $request->foto_base64;
            if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
                $imageData = substr($imageData, strpos($imageData, ',') + 1);
            }
            $imageData = base64_decode($imageData);

            $fileName = 'patroli/' . $user->id_pengguna . '_' . Str::uuid() . '.jpg';
            Storage::disk('public')->put($fileName, $imageData);

            Patroli::create([
                'tanggal' => $tanggalHariIni,
                'waktu_exact' => now(),
                'jenis_patroli' => $request->jenis_patroli,
                'wilayah' => $wilayahUpper,
                'foto' => $fileName,
                'id_pengguna' => $user->id_pengguna,
                'id_shift' => $idShift,
            ]);

            return response()->json(['status' => 'success', 'message' => 'Checkpoint disimpan']);

        } catch (\Exception $e) {
            Log::error('Error storeCheckpoint: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Gagal menyimpan checkpoint.'], 500);
        }
    }

    public function submitSession(Request $request)
    {
        $request->validate(['jenis_patroli' => 'required|string']);

        $user = Auth::user();
        $tanggal = Carbon::today();
        $jenisPatroli = $request->jenis_patroli;
        $jenisShift = $user->jenis_shift ?? 1;

        $shiftStatus = $this->checkShiftStatus($user->id_pengguna, $tanggal);

        if ($shiftStatus['is_off']) {
            return redirect()->back()->with('error', 'Anda tidak dapat melakukan patroli karena sedang OFF/LIBUR');
        }

        if ($this->isPatroliTerlewat($jenisPatroli, $jenisShift)) {
            return redirect()->back()->with('error', $jenisPatroli . ' sudah terlewat dan tidak dapat disubmit.');
        }

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

    public function checkArea(Request $request)
    {
        $jenisPatroli = $request->query('jenis_patroli');
        $wilayah = $request->query('wilayah');
        $tanggal = Carbon::today();

        $user = Auth::user();
        $shiftStatus = $this->checkShiftStatus($user->id_pengguna, $tanggal);
        $idShift = $shiftStatus['id_shift'];

        $patroli = Patroli::whereDate('tanggal', $tanggal)
            ->where('jenis_patroli', $jenisPatroli)
            ->where('wilayah', strtoupper($wilayah))
            ->where('id_shift', $idShift)
            ->first();

        if ($patroli) {
            return response()->json([
                'sudah_ada' => true,
                'nama_petugas' => $patroli->nama_lengkap ?? 'Anggota lain',
                'waktu' => Carbon::parse($patroli->waktu_exact)->format('H:i:s')
            ]);
        }

        return response()->json(['sudah_ada' => false]);
    }

    public function claimPatroli(Request $request)
    {
        $request->validate(['jenis_patroli' => 'required|string']);

        $user = Auth::user();
        $tanggal = Carbon::today();
        $jenisPatroli = $request->jenis_patroli;

        $shiftStatus = $this->checkShiftStatus($user->id_pengguna, $tanggal);

        if ($shiftStatus['is_off']) {
            return redirect()->back()->with('error', 'Anda tidak dapat claim patroli karena sedang OFF/LIBUR');
        }

        $jenisShift = $shiftStatus['jenis_shift_id'];
        $idShift = $shiftStatus['id_shift'];

        if (!$idShift) {
            return redirect()->back()->with('error', 'Data shift Anda tidak valid. Silakan hubungi Komandan.');
        }

        if ($this->isPatroliTerlewat($jenisPatroli, $jenisShift)) {
            return redirect()->back()->with('error', $jenisPatroli . ' sudah terlewat.');
        }

        $existingClaim = PatroliClaim::where('tanggal', $tanggal)
            ->where('jenis_patroli', $jenisPatroli)
            ->where('id_shift', $idShift)
            ->first();

        if ($existingClaim) {
            if ($existingClaim->id_pengguna == $user->id_pengguna) {
                return redirect()->back()->with('info', 'Anda sudah claim patroli ini.');
            } else {
                $namaPetugas = $existingClaim->pengguna->nama_lengkap ?? 'Anggota lain';
                return redirect()->back()->with('error', "Patroli ini sudah di-claim oleh {$namaPetugas}");
            }
        }

        PatroliClaim::create([
            'id_pengguna' => $user->id_pengguna,
            'tanggal' => $tanggal,
            'jenis_patroli' => $jenisPatroli,
            'id_shift' => $idShift,
            'claimed_at' => now()
        ]);

        return redirect()
            ->route('anggota.patroli.createSession', ['jenis_patroli' => $jenisPatroli])
            ->with('success', $jenisPatroli . ' berhasil di-claim!');
    }

    private function getClaimStatus($jenisPatroli, $tanggal, $userId, $idShift)
    {
        $claim = PatroliClaim::where('tanggal', $tanggal)
            ->where('jenis_patroli', $jenisPatroli)
            ->where('id_shift', $idShift)
            ->first();

        if (!$claim) {
            return ['is_claimed' => false, 'is_owner' => false, 'claimed_by' => null];
        }

        return [
            'is_claimed' => true,
            'is_owner' => $claim->id_pengguna == $userId,
            'claimed_by' => $claim->pengguna->nama_lengkap ?? 'Anggota lain',
            'claimed_at' => $claim->claimed_at
        ];
    }

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
            if ($now->hour >= 19) return true;
            if ($now->hour < 7) return true;
            return false;
        }
    }
}