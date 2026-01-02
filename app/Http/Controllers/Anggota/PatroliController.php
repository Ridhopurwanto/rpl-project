<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Patroli;
use App\Models\PatroliClaim;
use App\Models\PatroliRule;
use App\Models\Shift;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\ShiftRule;

class PatroliController extends Controller
{
     
    private function getEnumWilayah()
    {
        try {
            $instance = new Patroli();
            $table = $instance->getTable();
            $column = 'wilayah';
            $result = DB::select("SHOW COLUMNS FROM {$table} LIKE '{$column}'");

            if (empty($result)) return [];

            $type = $result[0]->Type;
            preg_match('/^enum\((.*)\)$/', $type, $matches);
            
            $enum = [];
            if (isset($matches[1])) {
                foreach(explode(',', $matches[1]) as $value){
                    $enum[] = trim($value, "'");
                }
            }
            return $enum;
        } catch (\Exception $e) {
            
            return [
                'AREA POS 1', 'LOBBY VVIP', 'LOBBY AUDIT', 'KOLAM IKAN VVIP', 
                'AREA BAU', 'AREA KANTIN', 'AREA BAAK', 'AKSES LORONG GD 3',
                'AKSES LORONG GD 2', 'AREA POS 3', 'AKSES BESI GD 2', 
                'AKSES KACA GD 2', 'AKSES SELATAN AUDIT', 'AKSES RUANG LETKOR', 
                'AKSES PARKIR BASEMENT', 'AKSES LIFT GD 2', 'AREA POS 2'
            ];
        }
    }

     
    private function checkShiftStatus($idPengguna, $tanggal)
    {
        $shift = Shift::where('id_pengguna', $idPengguna)
            ->whereDate('tanggal', $tanggal)
            ->with('shiftRule')
            ->first();

        if (!$shift || !$shift->shiftRule) {
            return ['is_off' => true, 'nama_shift' => 'OFF', 'id_shift' => null];
        }

        $jenisShift = $shift->shiftRule->jenis_shift;

        if (in_array($jenisShift, ['Non Shift', 'Off'])) {
            return ['is_off' => true, 'nama_shift' => $jenisShift, 'id_shift' => $shift->id_shift];
        }

        return ['is_off' => false, 'nama_shift' => strtoupper($jenisShift), 'id_shift' => $shift->id_shift];
    }


    private function getEffectiveLogicalDate($idPengguna)
    {
        $now = Carbon::now();

        $batasPatroliMalam = ShiftRule::where('jenis_shift', 'Malam')->first()->jam_keluar;

        if ($now->hour < $batasPatroliMalam) {
            $yesterday = Carbon::yesterday();
            
            $shiftStatus = $this->checkShiftStatus($idPengguna, $yesterday);
            if (!$shiftStatus['is_off'] && $shiftStatus['nama_shift'] === 'MALAM') {
                return $yesterday;
            }
        }
        
        return Carbon::today();
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        
        $defaultDate = $this->getEffectiveLogicalDate($user->id_pengguna);
        
        $tanggalTerpilih = $request->input('tanggal')
            ? Carbon::parse($request->input('tanggal'))
            : $defaultDate;

        $shiftStatus = $this->checkShiftStatus($user->id_pengguna, $tanggalTerpilih);
        $isShiftOff = $shiftStatus['is_off'];
        $namaShift = $shiftStatus['nama_shift'];
        $statusShift = $isShiftOff ? 'OFF' : 'AKTIF';
        
        $displayData = collect();

        if (!$isShiftOff) {
            
            $rules = PatroliRule::where('jenis_shift', ucfirst(strtolower($namaShift)))->get();
            
            
            $claims = PatroliClaim::whereDate('tanggal', $tanggalTerpilih)
                ->with(['pengguna', 'rule', 'patrolis'])
                ->get();

            $now = Carbon::now();

            foreach ($rules as $rule) {
                
                $jamMulai = Carbon::parse($tanggalTerpilih->format('Y-m-d') . ' ' . $rule->jam_mulai);
                $jamSelesai = Carbon::parse($tanggalTerpilih->format('Y-m-d') . ' ' . $rule->jam_selesai);

                if ($namaShift === 'MALAM') {
                    if ($jamMulai->hour < 12) {
                        $jamMulai->addDay();
                        $jamSelesai->addDay();
                    } elseif ($jamSelesai->lt($jamMulai)) {
                        $jamSelesai->addDay();
                    }
                } else {
                    if ($jamSelesai->lt($jamMulai)) $jamSelesai->addDay();
                }

                
                $claimData = $claims->where('id_patroli_rule', $rule->id_patroli_rule)->first();

                if ($claimData) {
                    
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
                        'waktu_batas' => $jamSelesai->format('H:i')
                    ]);
                } else {
                    
                    $isExpired = $now->gt($jamSelesai);
                    
                    if ($isExpired) {
                        $displayData->push([
                            'jenis_patroli' => $rule->jenis_patroli,
                            'id_claim' => null,
                            'nama_petugas' => '-',
                            'progress' => 0,
                            'checkpoints' => collect(),
                            'has_checkpoints' => false,
                            'is_completed' => false,
                            'is_expired' => true,
                            'waktu_batas' => $jamSelesai->format('H:i')
                        ]);
                    }
                }
            }
        }

        $displayData = $displayData->sortBy('jenis_patroli')->values();

        return view('anggota.patroli-index', [
            'displayData' => $displayData,
            'tanggalTerpilih' => $tanggalTerpilih,
            'isShiftOff' => $isShiftOff,
            'namaShift' => $namaShift,
            'statusShift' => $statusShift,
            'isWaktuShiftAktif' => !$isShiftOff 
        ]);
    }

     
    public function createSession(Request $request)
    {
        $user = Auth::user();
        $tanggal = $this->getEffectiveLogicalDate($user->id_pengguna);

        
        $shiftStatus = $this->checkShiftStatus($user->id_pengguna, $tanggal);
        if ($shiftStatus['is_off']) {
             return redirect()->route('anggota.patroli.index')->with('error', 'Sedang OFF');
        }

        $jenisShiftStr = $shiftStatus['nama_shift']; 
        
        
        $rules = PatroliRule::where('jenis_shift', ucfirst(strtolower($jenisShiftStr)))->get();
        if ($rules->isEmpty()) {
            return redirect()->route('anggota.patroli.index')->with('error', 'Jadwal belum diatur.');
        }

        $opsiJenisPatroli = $rules->pluck('jenis_patroli')->toArray();
        
        
        $semuaArea = $this->getEnumWilayah();

        
        $statusPatroli = [];
        $jadwalPatroli = [];
        $now = Carbon::now();
        $suggestedPatroli = null;

        foreach($rules as $r) {
             $jamMulai = Carbon::parse($tanggal->format('Y-m-d') . ' ' . $r->jam_mulai);
             $jamSelesai = Carbon::parse($tanggal->format('Y-m-d') . ' ' . $r->jam_selesai);

             if ($jenisShiftStr === 'MALAM') {
                 if ($jamMulai->hour < 12) { $jamMulai->addDay(); $jamSelesai->addDay(); }
                 elseif ($jamSelesai->lt($jamMulai)) { $jamSelesai->addDay(); }
             } else {
                 if ($jamSelesai->lt($jamMulai)) $jamSelesai->addDay();
             }

             $jadwalPatroli[$r->jenis_patroli] = [
                $jamMulai->format('H:i'), 
                $jamSelesai->format('H:i')
             ];

             if ($now->lt($jamMulai)) {
                 $statusPatroli[$r->jenis_patroli] = 'pending';
             } elseif ($now->gt($jamSelesai)) {
                 $statusPatroli[$r->jenis_patroli] = 'expired';
             } else {
                 $statusPatroli[$r->jenis_patroli] = 'active';
                 if (!$suggestedPatroli) $suggestedPatroli = $r->jenis_patroli;
             }
        }

        
        $jenisPatroliTerpilih = $request->input('jenis_patroli') ?? ($suggestedPatroli ?? $rules->first()->jenis_patroli);
        $selectedRule = $rules->where('jenis_patroli', $jenisPatroliTerpilih)->first();
        
        if (!$selectedRule) return redirect()->route('anggota.patroli.index')->with('error', 'Jadwal invalid');

        
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

        
        $totalCompleted = count($completedCheckpoints);
        $isCompleted = $totalCompleted >= 17;

        
        $statusCurrent = $statusPatroli[$jenisPatroliTerpilih];
        
        
        if ($isCompleted) {
             $statusPatroli[$jenisPatroliTerpilih] = 'completed';
        }

        return view('anggota.patroli-create-session', [
            'semuaArea' => $semuaArea, 
            'opsiJenisPatroli' => $opsiJenisPatroli,
            'jenisPatroliTerpilih' => $jenisPatroliTerpilih,
            'completedCheckpoints' => $completedCheckpoints,
            'totalCompleted' => $totalCompleted,
            'isCompleted' => $isCompleted, 
            
            'statusPatroli' => $statusPatroli,
            'jenisShift' => $shiftStatus['nama_shift'],
            'namaShift' => $shiftStatus['nama_shift'],
            'jadwalPatroli' => $jadwalPatroli,
            'isClaimed' => $isClaimed,
            'isOwner' => $isOwner,
            'claimedBy' => $claimedBy,
            
            
            'patroliExpired' => ($statusCurrent === 'expired'), 
            'patroliPending' => ($statusCurrent === 'pending'),
            
            'canClaimNow' => ($statusCurrent === 'active' || $isOwner),
            'idClaim' => $idClaim,      
            'idPatroliRule' => $selectedRule->id_patroli_rule 
        ]);
    }

     
    public function claimPatroli(Request $request)
    {
        $request->validate(['jenis_patroli' => 'required|string']);

        $user = Auth::user();
        $tanggal = $this->getEffectiveLogicalDate($user->id_pengguna);
        
        $shiftStatus = $this->checkShiftStatus($user->id_pengguna, $tanggal);
        $jenisShiftStr = ucfirst(strtolower($shiftStatus['nama_shift']));

        
        $rule = PatroliRule::where('jenis_shift', $jenisShiftStr)
                    ->where('jenis_patroli', $request->jenis_patroli)
                    ->first();

        if (!$rule) return back()->with('error', 'Jadwal tidak valid.');

        
        $existingClaim = PatroliClaim::where('tanggal', $tanggal)
            ->where('id_patroli_rule', $rule->id_patroli_rule)
            ->first();

        if ($existingClaim) return back()->with('error', 'Sudah diambil petugas lain.');

        
        PatroliClaim::create([
            'id_pengguna' => $user->id_pengguna,
            'id_shift' => $shiftStatus['id_shift'],
            'id_patroli_rule' => $rule->id_patroli_rule, 
            'tanggal' => $tanggal,
            'claimed_at' => now()
        ]);

        return redirect()->route('anggota.patroli.createSession', ['jenis_patroli' => $request->jenis_patroli]);
    }

     
    public function storeCheckpoint(Request $request)
    {
        $request->validate([
            'foto_base64' => 'required|string',
            'id_claim' => 'required|integer|exists:patroli_claims,id_claim',
            'wilayah' => 'required|string',
        ]);

        $user = Auth::user();
        $wilayahUpper = strtoupper($request->wilayah);

        
        $claim = PatroliClaim::with('rule')->find($request->id_claim);
        
        
        if ($claim->id_pengguna != $user->id_pengguna) {
             return response()->json(['status' => 'error', 'message' => 'Bukan pemilik sesi.'], 403);
        }

        
        $sudahAda = Patroli::where('id_claim', $request->id_claim)
            ->where('wilayah', $wilayahUpper)
            ->exists();

        if ($sudahAda) return response()->json(['status' => 'error', 'message' => 'Sudah difoto.'], 400);

        try {
            
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->foto_base64));
            $fileName = 'patroli/' . $user->id_pengguna . '_' . Str::uuid() . '.jpg';
            Storage::disk('public')->put($fileName, $imageData);

            
            Patroli::create([
                'id_claim' => $request->id_claim, 
                'wilayah' => $wilayahUpper,
                'foto' => $fileName,
                'tanggal' => Carbon::today(),
                'waktu_exact' => now(),
                
                
                'jenis_patroli' => $claim->rule->jenis_patroli, 
                
                
            ]);

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

     
    public function checkArea(Request $request)
    {
        
        $idClaim = $request->query('id_claim');
        $wilayah = $request->query('wilayah');

        if (!$idClaim) return response()->json(['sudah_ada' => false]);

        
        $patroli = Patroli::where('id_claim', $idClaim)
            ->where('wilayah', strtoupper($wilayah))
            ->with('claim.pengguna') 
            ->first();

        if ($patroli) {
            return response()->json([
                'sudah_ada' => true,
                
                'nama_petugas' => $patroli->claim->pengguna->nama_lengkap ?? 'Petugas',
                'waktu' => \Carbon\Carbon::parse($patroli->waktu_exact)->format('H:i:s')
            ]);
        }

        return response()->json(['sudah_ada' => false]);
    }
}