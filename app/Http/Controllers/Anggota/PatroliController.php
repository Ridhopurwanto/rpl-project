<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
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
     * DIPERBAIKI: Menggunakan Logic PatroliRule & Cross-Day Shift Malam
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $tanggalTerpilih = $request->input('tanggal')
            ? Carbon::parse($request->input('tanggal'))
            : Carbon::today();

        // 1. Cek Shift User pada Tanggal Terpilih
        $shiftStatus = $this->checkShiftStatus($user->id_pengguna, $tanggalTerpilih);
        $isShiftOff = $shiftStatus['is_off'];
        $namaShift = $shiftStatus['nama_shift']; // "PAGI" atau "MALAM"
        $statusShift = $isShiftOff ? 'OFF' : 'AKTIF';
        
        $displayData = collect();

        // Jika OFF, kita mungkin tidak menampilkan apa-apa atau hanya history jika ada
        // Tapi jika AKTIF, kita load Rules dari DB
        if (!$isShiftOff) {
            $rules = PatroliRule::where('jenis_shift', ucfirst(strtolower($namaShift)))->get();
            
            // Ambil semua claim pada tanggal tersebut untuk user/shift ini
            // Note: Kita ambil semua claim di tanggal itu utk efisiensi query
            $claims = PatroliClaim::whereDate('tanggal', $tanggalTerpilih)
                ->with(['pengguna', 'rule', 'patrolis'])
                ->get();

            $now = Carbon::now();

            foreach ($rules as $rule) {
                // --- LOGIC WAKTU (SAMA SEPERTI CREATE SESSION) ---
                $jamMulai = Carbon::parse($tanggalTerpilih->format('Y-m-d') . ' ' . $rule->jam_mulai);
                $jamSelesai = Carbon::parse($tanggalTerpilih->format('Y-m-d') . ' ' . $rule->jam_selesai);

                if ($namaShift === 'MALAM') {
                    // Logic Cross Day
                    if ($jamMulai->hour < 12) {
                        $jamMulai->addDay();
                        $jamSelesai->addDay();
                    } elseif ($jamSelesai->lt($jamMulai)) {
                        $jamSelesai->addDay();
                    }
                } else {
                    // Pagi (Jaga-jaga)
                    if ($jamSelesai->lt($jamMulai)) $jamSelesai->addDay();
                }

                // Cek Claim
                // $claimData = $claims->where('id_patroli_rule', $rule->id_patroli_rule)->first();

                // if ($claimData) {
                //     // KASUS 1: SUDAH DI-CLAIM (TAMPILKAN)
                //     $checkpoints = $claimData->patrolis;
                //     $progressCount = $checkpoints->unique('wilayah')->count();
                    
                //     $displayData->push([
                //         'jenis_patroli' => $rule->jenis_patroli,
                //         'id_claim' => $claimData->id_claim,
                //         'nama_petugas' => $claimData->pengguna->nama_lengkap ?? 'Unknown',
                //         'progress' => $progressCount,
                //         'checkpoints' => $checkpoints,
                //         'has_checkpoints' => $checkpoints->isNotEmpty(),
                //         'is_completed' => $progressCount >= 17,
                //         'is_expired' => false,
                //         'status_label' => ($progressCount >= 17) ? 'Selesai' : 'Proses',
                //         'waktu_batas' => $jamSelesai->format('H:i')
                //     ]);
                // } else {
                //     // KASUS 2: BELUM DI-CLAIM
                //     $isExpired = $now->gt($jamSelesai);

                //     // LOGIC FILTER BARU: 
                //     // Hanya masukkan ke list jika SUDAH TERLEWAT (Expired).
                //     // Jika masih available (belum lewat waktu), SKIP/JANGAN TAMPILKAN.
                //     if ($isExpired) {
                //         $displayData->push([
                //             'jenis_patroli' => $rule->jenis_patroli,
                //             'id_claim' => null,
                //             'nama_petugas' => '-',
                //             'progress' => 0,
                //             'checkpoints' => collect(),
                //             'has_checkpoints' => false,
                //             'is_completed' => false,
                //             'is_expired' => true,
                //             'status_label' => 'Terlewat',
                //             'waktu_batas' => $jamSelesai->format('H:i')
                //         ]);
                //     }
                // }

                // Cek apakah Rule ini sudah di-claim?
                $claimData = $claims->where('id_patroli_rule', $rule->id_patroli_rule)->first();

                if ($claimData) {
                    // --- KASUS 1: SUDAH DI-CLAIM (PROSES / SELESAI) ---
                    $checkpoints = $claimData->patrolis;
                    $progressCount = $checkpoints->unique('wilayah')->count();
                    
                    $displayData->push([
                        'jenis_patroli' => $rule->jenis_patroli,
                        'id_claim' => $claimData->id_claim,
                        'nama_petugas' => $claimData->pengguna->nama_lengkap ?? 'Unknown',
                        'progress' => $progressCount,
                        'checkpoints' => $checkpoints,
                        'has_checkpoints' => $checkpoints->isNotEmpty(),
                        'is_completed' => $progressCount >= 17,
                        'is_expired' => false,
                        'status_label' => ($progressCount >= 17) ? 'Selesai' : 'Proses',
                        'waktu_batas' => $jamSelesai->format('H:i')
                    ]);
                } else {
                    // --- KASUS 2: BELUM DI-CLAIM (CEK EXPIRED) ---
                    // Expired jika waktu sekarang > jam selesai
                    $isExpired = $now->gt($jamSelesai);
                    
                    // Kita hanya menampilkan card jika statusnya "Terlewat" atau "Sedang Aktif"
                    // Untuk Index, biasanya kita ingin melihat report.
                    // Jika belum lewat waktu (Future), opsional mau ditampilkan atau tidak.
                    
                    // Logic tampilan: Tampilkan jika Expired. 
                    // Jika Active (Available), tampilkan sebagai "Belum Diambil".
                    
                    $statusLabel = $isExpired ? 'Terlewat' : 'Belum Dilaksanakan';

                    $displayData->push([
                        'jenis_patroli' => $rule->jenis_patroli,
                        'id_claim' => null,
                        'nama_petugas' => '-',
                        'progress' => 0,
                        'checkpoints' => collect(),
                        'has_checkpoints' => false,
                        'is_completed' => false,
                        'is_expired' => $isExpired,
                        'status_label' => $statusLabel,
                        'waktu_batas' => $jamSelesai->format('H:i')
                    ]);
                }
            }
        }

        // Sort data: Expired/Completed di bawah, Active/Proses di atas (Opsional, sesuaikan selera)
        // Disini saya urutkan berdasarkan urutan Patroli (Patroli 1, 2, 3...)
        $displayData = $displayData->sortBy(function ($item) {
            preg_match('/\d+/', $item['jenis_patroli'], $matches);
            return (int) ($matches[0] ?? 0);
        })->values();

        return view('anggota.patroli-index', [
            'displayData' => $displayData,
            'tanggalTerpilih' => $tanggalTerpilih,
            'isShiftOff' => $isShiftOff,
            'namaShift' => $namaShift,
            'statusShift' => $statusShift,
            'isWaktuShiftAktif' => !$isShiftOff // Simplifikasi logic view
        ]);
    }

    private function getEnumWilayah()
    {
        try {
            // Query untuk melihat tipe kolom 'wilayah' di tabel 'patroli'
            // Outputnya string seperti: "enum('AREA 1','AREA 2','AREA 3')"
            $instance = new Patroli();
            $table = $instance->getTable();
            $column = 'wilayah';
            
            $result = DB::select("SHOW COLUMNS FROM {$table} LIKE '{$column}'");
            
            if (empty($result)) {
                return [];
            }

            $type = $result[0]->Type;
            
            // Gunakan Regex untuk mengambil teks di dalam kurung enum(...)
            preg_match('/^enum\((.*)\)$/', $type, $matches);
            
            $enum = [];
            
            if (isset($matches[1])) {
                foreach(explode(',', $matches[1]) as $value){
                    // Trim tanda kutip tunggal ('AREA 1' -> AREA 1)
                    $enum[] = trim($value, "'");
                }
            }
            
            return $enum;
        } catch (\Exception $e) {
            Log::error("Gagal mengambil ENUM wilayah: " . $e->getMessage());
            return []; // Fallback empty array agar tidak crash fatal
        }
    }
    /**
     * Halaman Grid 17 Area (Create Session)
     */
    public function createSession(Request $request)
    {
        $user = Auth::user();
        $tanggal = Carbon::today(); // Tanggal Shift dimulai

        // 1. Cek Shift
        $shiftStatus = $this->checkShiftStatus($user->id_pengguna, $tanggal);
        if ($shiftStatus['is_off']) {
             return redirect()->route('anggota.patroli.index')->with('error', 'Sedang OFF');
        }

        $jenisShiftStr = $shiftStatus['nama_shift']; // "PAGI" atau "MALAM"
        
        // 2. Ambil Rules
        $rules = PatroliRule::where('jenis_shift', ucfirst(strtolower($jenisShiftStr)))->get();
        if ($rules->isEmpty()) {
            return redirect()->route('anggota.patroli.index')->with('error', 'Jadwal belum diatur.');
        }

        $opsiJenisPatroli = $rules->pluck('jenis_patroli')->toArray();
        $semuaArea = $this->getEnumWilayah(); // Pastikan method ini ada di controller
        if (empty($semuaArea)) $semuaArea = [];

        // --- [LOGIC UTAMA: TIME TRAVEL CHECK] ---
        $statusPatroli = [];
        $jadwalPatroli = [];
        $now = Carbon::now();
        $suggestedPatroli = null;

        foreach($rules as $r) {
             // Buat timestamp absolut hari ini dulu
             $jamMulai = Carbon::parse($tanggal->format('Y-m-d') . ' ' . $r->jam_mulai);
             $jamSelesai = Carbon::parse($tanggal->format('Y-m-d') . ' ' . $r->jam_selesai);

             // LOGIC CROSS-DAY SHIFT MALAM
             if ($jenisShiftStr === 'MALAM') {
                 // Jika jam mulai < 12 siang (misal 01:30), itu milik BESOK
                 if ($jamMulai->hour < 12) {
                     $jamMulai->addDay();
                     $jamSelesai->addDay();
                 }
                 // Jika mulai malam (23:30) tapi selesai pagi (00:30), selesai tambah hari
                 elseif ($jamSelesai->lt($jamMulai)) {
                     $jamSelesai->addDay();
                 }
             } else {
                 // Shift Pagi (Jaga-jaga lintas hari)
                 if ($jamSelesai->lt($jamMulai)) $jamSelesai->addDay();
             }

             // Simpan string jam untuk View
             $jadwalPatroli[$r->jenis_patroli] = [
                $jamMulai->format('H:i'), 
                $jamSelesai->format('H:i')
             ];

             // TENTUKAN STATUS (Pending / Active / Expired)
             if ($now->lt($jamMulai)) {
                 $statusPatroli[$r->jenis_patroli] = 'pending'; // Belum waktunya
             } elseif ($now->gt($jamSelesai)) {
                 $statusPatroli[$r->jenis_patroli] = 'expired'; // Sudah lewat
             } else {
                 $statusPatroli[$r->jenis_patroli] = 'active'; // Sedang jalan
                 if (!$suggestedPatroli) $suggestedPatroli = $r->jenis_patroli;
             }
        }

        // 3. Select Patroli
        $jenisPatroliTerpilih = $request->input('jenis_patroli') ?? ($suggestedPatroli ?? $rules->first()->jenis_patroli);
        $selectedRule = $rules->where('jenis_patroli', $jenisPatroliTerpilih)->first();
        
        if (!$selectedRule) return redirect()->route('anggota.patroli.index')->with('error', 'Jadwal invalid');

        // 4. Cek Claim & Checkpoint
        $claim = PatroliClaim::where('tanggal', $tanggal)
            ->where('id_patroli_rule', $selectedRule->id_patroli_rule)
            ->with('pengguna')
            ->first();

        $idClaim = $claim ? $claim->id_claim : null;
        $isClaimed = $claim ? true : false;
        $isOwner = $claim && $claim->id_pengguna == $user->id_pengguna;
        $claimedBy = $claim ? ($claim->pengguna->nama_lengkap ?? 'Anggota lain') : null;
        
        $completedCheckpoints = [];
        if ($idClaim) {
            $completedCheckpoints = Patroli::where('id_claim', $idClaim)
                ->pluck('wilayah')->map(fn($v)=>strtoupper($v))->unique()->values()->toArray();
        }
        
        // Cek status spesifik patroli terpilih untuk variable view lama
        $statusCurrent = $statusPatroli[$jenisPatroliTerpilih];

        return view('anggota.patroli-create-session', [
            'semuaArea' => $semuaArea,
            'opsiJenisPatroli' => $opsiJenisPatroli,
            'jenisPatroliTerpilih' => $jenisPatroliTerpilih,
            'completedCheckpoints' => $completedCheckpoints,
            'totalCompleted' => count($completedCheckpoints),
            'statusPatroli' => $statusPatroli, // Array status lengkap
            'jenisShift' => $shiftStatus['nama_shift'],
            'namaShift' => $shiftStatus['nama_shift'],
            'jadwalPatroli' => $jadwalPatroli,
            'isClaimed' => $isClaimed,
            'isOwner' => $isOwner,
            'claimedBy' => $claimedBy,
            
            // Mapping variable lama ke logic baru
            'patroliTerlewat' => [], 
            'patroliExpired' => ($statusCurrent === 'expired' && !$isOwner), 
            'patroliPending' => ($statusCurrent === 'pending'), // Variable baru utk view
            
            'canClaimNow' => ($statusCurrent === 'active' || $isOwner),
            'idClaim' => $idClaim,      
            'idPatroliRule' => $selectedRule->id_patroli_rule 
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
            'id_claim' => 'required|integer|exists:patroli_claims,id_claim', // Validasi FK
            'wilayah' => 'required|string',
        ]);

        $user = Auth::user();
        
        // 1. Definisikan variabel yang sebelumnya hilang
        $wilayahUpper = strtoupper($request->wilayah); 

        // 2. Verifikasi kepemilikan Claim
        $claim = PatroliClaim::find($request->id_claim);
        
        if ($claim->id_pengguna != $user->id_pengguna) {
             return response()->json(['status' => 'error', 'message' => 'Anda bukan pemilik sesi patroli ini!'], 403);
        }

        // 3. Cek Duplikasi (menggunakan id_claim)
        $sudahAda = Patroli::where('id_claim', $request->id_claim)
            ->where('wilayah', $wilayahUpper)
            ->exists();

        if ($sudahAda) {
            return response()->json(['status' => 'error', 'message' => 'Area ini sudah difoto!'], 400);
        }

        try {
            // Proses Decode Gambar
            $imageData = $request->foto_base64;
            if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
                $imageData = substr($imageData, strpos($imageData, ',') + 1);
            }
            $imageData = base64_decode($imageData);

            // Simpan File
            $fileName = 'patroli/' . $user->id_pengguna . '_' . Str::uuid() . '.jpg';
            Storage::disk('public')->put($fileName, $imageData);

            // 4. SIMPAN KE DATABASE (SESUAI STRUKTUR BARU)
            Patroli::create([
                'id_claim' => $request->id_claim, // KUNCI UTAMA (Foreign Key)
                'wilayah' => $wilayahUpper,       // Gunakan variabel yang sudah didefinisikan
                'foto' => $fileName,
                'tanggal' => Carbon::today(),     // Gunakan Carbon::today() pengganti $tanggalHariIni
                'waktu_exact' => now(),
                
                // HAPUS KOLOM INI (Karena sudah ada di tabel patroli_claims):
                // 'jenis_patroli' => ...,
                // 'id_pengguna' => ...,
                // 'id_shift' => ...,
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
        // 1. Ambil input dari request (View mengirimkan id_claim & wilayah)
        $idClaim = $request->query('id_claim');
        $wilayah = $request->query('wilayah');

        // Jika id_claim tidak ada, kembalikan false
        if (!$idClaim) {
            return response()->json(['sudah_ada' => false]);
        }

        // 2. Query menggunakan ID Claim (Foreign Key)
        // Kita mencari apakah di sesi claim ini, wilayah tersebut sudah ada datanya
        $patroli = Patroli::where('id_claim', $idClaim)
            ->where('wilayah', strtoupper($wilayah))
            ->with('claim.pengguna') // Eager load untuk mengambil nama petugas dari tabel pengguna
            ->first();

        // 3. Jika ditemukan, kirim respon JSON
        if ($patroli) {
            // Ambil nama dari relasi: Patroli -> PatroliClaim -> Pengguna
            // Karena tabel 'patroli' tidak lagi menyimpan nama_lengkap, kita ambil dari relasi
            $namaPetugas = $patroli->claim->pengguna->nama_lengkap ?? 'Petugas';

            return response()->json([
                'sudah_ada' => true,
                'nama_petugas' => $namaPetugas,
                'waktu' => \Carbon\Carbon::parse($patroli->waktu_exact)->format('H:i:s')
            ]);
        }

        // 4. Jika tidak ditemukan
        return response()->json(['sudah_ada' => false]);
    }

    public function claimPatroli(Request $request)
    {
        $request->validate(['jenis_patroli' => 'required|string']);

        $user = Auth::user();
        $tanggal = Carbon::today();
        
        $shiftStatus = $this->checkShiftStatus($user->id_pengguna, $tanggal);
        $jenisShiftStr = $shiftStatus['nama_shift']; // Pagi/Malam

        // Cari Rule yang sesuai
        $rule = PatroliRule::where('jenis_shift', ucfirst(strtolower($jenisShiftStr)))
                    ->where('jenis_patroli', $request->jenis_patroli)
                    ->first();

        if (!$rule) {
            return back()->with('error', 'Jadwal patroli tidak valid untuk shift ini.');
        }

        // Cek apakah sudah di-claim (cek by ID Rule)
        $existingClaim = PatroliClaim::where('tanggal', $tanggal)
            ->where('id_patroli_rule', $rule->id_patroli_rule)
            ->first();

        if ($existingClaim) {
            return back()->with('error', 'Patroli sudah diambil orang lain.');
        }

        // Create Claim dengan Foreign Key Rule
        PatroliClaim::create([
            'id_pengguna' => $user->id_pengguna,
            'id_shift' => $shiftStatus['id_shift'], // Tetap simpan shift user sebagai audit
            'id_patroli_rule' => $rule->id_patroli_rule, // FK BARU
            'tanggal' => $tanggal,
            'claimed_at' => now()
        ]);

        return redirect()->route('anggota.patroli.createSession', ['jenis_patroli' => $request->jenis_patroli]);
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