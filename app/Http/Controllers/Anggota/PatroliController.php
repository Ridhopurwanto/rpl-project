<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Patroli;
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
                'Patroli 2' => ['09:30', '10:30'],
                'Patroli 3' => ['11:30', '12:30'],
                'Patroli 4' => ['13:40', '14:30'],
                'Patroli 5' => ['15:30', '17:30'],
                'Patroli 6' => ['17:30', '18:40'],
            ],
            2 => [ // Shift Malam
                'Patroli 1' => ['19:30', '20:20'],
                'Patroli 2' => ['21:30', '22:30'],
                'Patroli 3' => ['23:30', '00:30'], // 24:30 = 00:30 (hari berikutnya)
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
        // Cek apakah sudah complete (17 area)
        $jumlah = Patroli::where('id_pengguna', $userId)
                        ->whereDate('tanggal', $tanggal)
                        ->where('jenis_patroli', $jenisPatroli)
                        ->count();
        
        if ($jumlah >= 17) {
            return 'completed';
        }

        // Cek apakah waktu valid
        if ($this->isWaktuPatroliValid($jenisPatroli, $jenisShift)) {
            return 'available';
        }

        return 'locked';
    }

    /**
     * Menampilkan daftar patroli (Halaman Index)
     */
    public function index(Request $request)
    {
        $tanggalTerpilih = $request->input('tanggal') 
                            ? Carbon::parse($request->input('tanggal')) 
                            : Carbon::today();
        
        $allPatrols = Patroli::whereDate('tanggal', $tanggalTerpilih)
                        ->orderBy('jenis_patroli', 'asc')
                        ->get();
        
        $patrolGroups = $allPatrols->groupBy('jenis_patroli');
                        
        return view('anggota.patroli-index', [
            'patrolGroups' => $patrolGroups,
            'tanggalTerpilih' => $tanggalTerpilih
        ]);
    }

    /**
     * Halaman Grid 17 Area (Create Session)
     */
    public function createSession(Request $request)
    {
        $user = Auth::user();
        $tanggal = Carbon::today();
        $jenisShift = $user->jenis_shift ?? 1; // Default shift pagi jika belum diset
        $namaShift = $this->getNamaShift($jenisShift);

        // 1. Opsi dropdown
        $opsiJenisPatroli = [
            'Patroli 1', 'Patroli 2', 'Patroli 3', 
            'Patroli 4', 'Patroli 5', 'Patroli 6'
        ];

        // 2. Build status untuk setiap patroli
        $statusPatroli = [];
        foreach ($opsiJenisPatroli as $opsi) {
            $statusPatroli[$opsi] = $this->getStatusPatroli($opsi, $jenisShift, $user->id_pengguna, $tanggal);
        }

        // 3. Tentukan patroli yang dipilih
        $jenisPatroliTerpilih = $request->input('jenis_patroli');
        
        if (!$jenisPatroliTerpilih) {
            // Cari patroli pertama yang available
            foreach ($opsiJenisPatroli as $opsi) {
                if ($statusPatroli[$opsi] === 'available') {
                    $jenisPatroliTerpilih = $opsi;
                    break;
                }
            }
            
            // Jika tidak ada yang available, pilih yang pertama
            if (!$jenisPatroliTerpilih) {
                $jenisPatroliTerpilih = 'Patroli 1';
            }
        }

        // 4. Validasi apakah patroli terpilih bisa diakses
        $currentStatus = $statusPatroli[$jenisPatroliTerpilih];
        
        if ($currentStatus === 'locked') {
            $jadwal = $this->getJadwalPatroli();
            [$jamMulai, $jamSelesai] = $jadwal[$jenisShift][$jenisPatroliTerpilih];
            
            return redirect()->route('anggota.patroli.index')
                           ->with('error', "$jenisPatroliTerpilih hanya bisa dilakukan pada pukul $jamMulai - $jamSelesai WIB");
        }

        // 5. Daftar 17 Area
        $semuaArea = [
            'AREA POS 2', 'LOBBY VVIP', 'LOBBY AUDIT', 'KOLAM IKAN VVIP', 
            'AREA BAU', 'AREA KANTIN', 'AREA BAAK', 'AKSES LORONG GD 3',
            'AKSES LORONG GD 2', 'AREA POS 3', 'AKSES BESI GD 2', 'AKSES KACA GD 2',
            'AKSES SELATAN AUDIT', 'AKSES RUANG LETKOR', 'AKSES PARKIR BASEMENT',
            'AKSES LIFT GD 2', 'AREA POS 1'
        ];

        // 6. Ambil checkpoint yang sudah selesai
        $completedCheckpoints = Patroli::where('id_pengguna', $user->id_pengguna)
                                ->whereDate('tanggal', $tanggal)
                                ->where('jenis_patroli', $jenisPatroliTerpilih)
                                ->pluck('wilayah')
                                ->map(function($value) {
                                    return strtoupper($value);
                                })
                                ->toArray();

        return view('anggota.patroli-create-session', [
            'semuaArea' => $semuaArea,
            'opsiJenisPatroli' => $opsiJenisPatroli,
            'jenisPatroliTerpilih' => $jenisPatroliTerpilih,
            'completedCheckpoints' => $completedCheckpoints,
            'totalCompleted' => count($completedCheckpoints),
            'statusPatroli' => $statusPatroli,
            'jenisShift' => $jenisShift,
            'namaShift' => $namaShift,
            'jadwalPatroli' => $this->getJadwalPatroli()[$jenisShift]
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
        $jenisShift = $user->jenis_shift ?? 1; // Default shift pagi
        $wilayahUpper = strtoupper($request->wilayah);

        // VALIDASI WAKTU
        if (!$this->isWaktuPatroliValid($request->jenis_patroli, $jenisShift)) {
            $jadwal = $this->getJadwalPatroli();
            [$jamMulai, $jamSelesai] = $jadwal[$jenisShift][$request->jenis_patroli];
            
            return response()->json([
                'status' => 'error', 
                'message' => "{$request->jenis_patroli} hanya bisa dilakukan pada pukul $jamMulai - $jamSelesai WIB"
            ], 403);
        }

        // Cek duplikat
        $sudahAda = Patroli::where('id_pengguna', $user->id_pengguna)
                        ->whereDate('tanggal', Carbon::today())
                        ->where('jenis_patroli', $request->jenis_patroli)
                        ->where('wilayah', $wilayahUpper)
                        ->exists();

        if ($sudahAda) {
            return response()->json(['status' => 'error', 'message' => 'Area ' . $wilayahUpper . ' sudah difoto!'], 400);
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
                'tanggal' => Carbon::today(),
                'waktu_exact' => now(), 
                'jenis_patroli' => $request->jenis_patroli,
                'wilayah' => $wilayahUpper,
                'foto' => $fileName,
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

        // Cek jumlah checkpoint
        $jumlahCheckpoint = Patroli::where('id_pengguna', $user->id_pengguna)
                                ->whereDate('tanggal', $tanggal)
                                ->where('jenis_patroli', $jenisPatroli)
                                ->count();

        if ($jumlahCheckpoint != 17) {
            return redirect()->back()->with('error', 'Semua 17 area belum selesai.');
        }

        return redirect()->route('anggota.patroli.index')
                         ->with('success', 'Sesi ' . $jenisPatroli . ' berhasil disubmit!');
    }
}