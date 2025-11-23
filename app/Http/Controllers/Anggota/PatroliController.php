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
     * Menampilkan daftar patroli (Halaman Index)
     */
    public function index(Request $request)
    {
        $tanggalTerpilih = $request->input('tanggal') 
                            ? Carbon::parse($request->input('tanggal')) 
                            : Carbon::today();
        
        // Ambil semua patroli yang sudah lengkap 17 area
        $allPatrols = Patroli::whereDate('tanggal', $tanggalTerpilih)
                        ->orderBy('jenis_patroli', 'asc')
                        ->get();
        
        // Filter hanya yang sudah lengkap 17 area
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

        // 1. Opsi dropdown
        $opsiJenisPatroli = [
            'Patroli 1', 'Patroli 2', 'Patroli 3', 
            'Patroli 4', 'Patroli 5', 'Patroli 6'
        ];

        // 2. Cek patroli mana saja yang SUDAH LENGKAP 17 AREA
        $patroliYangSudahSubmit = [];
        foreach ($opsiJenisPatroli as $opsi) {
            $jumlah = Patroli::where('id_pengguna', $user->id_pengguna)
                            ->whereDate('tanggal', $tanggal)
                            ->where('jenis_patroli', $opsi)
                            ->count();
            
            if ($jumlah >= 17) {
                $patroliYangSudahSubmit[] = $opsi;
            }
        }

        // 3. Tentukan patroli yang dipilih
        $jenisPatroliTerpilih = $request->input('jenis_patroli');
        
        if (!$jenisPatroliTerpilih) {
            // Cari patroli pertama yang belum lengkap
            foreach ($opsiJenisPatroli as $opsi) {
                if (!in_array($opsi, $patroliYangSudahSubmit)) {
                    $jenisPatroliTerpilih = $opsi;
                    break;
                }
            }
            
            if (!$jenisPatroliTerpilih) {
                $jenisPatroliTerpilih = 'Patroli 1';
            }
        }

        // 4. Cek apakah patroli yang dipilih sudah submit
        $sudahSubmit = in_array($jenisPatroliTerpilih, $patroliYangSudahSubmit);

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
                                    return strtoupper($value); // Pastikan Uppercase
                                })
                                ->toArray();

        return view('anggota.patroli-create-session', [
            'semuaArea' => $semuaArea,
            'opsiJenisPatroli' => $opsiJenisPatroli,
            'jenisPatroliTerpilih' => $jenisPatroliTerpilih,
            'completedCheckpoints' => $completedCheckpoints,
            'totalCompleted' => count($completedCheckpoints),
            'sudahSubmit' => $sudahSubmit,
            'patroliYangSudahSubmit' => $patroliYangSudahSubmit
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

        $wilayahUpper = strtoupper($request->wilayah);

        // Cek duplikat
        $sudahAda = Patroli::where('id_pengguna', Auth::id())
                        ->whereDate('tanggal', Carbon::today())
                        ->where('jenis_patroli', $request->jenis_patroli)
                        ->where('wilayah', $wilayahUpper)
                        ->exists();

        if ($sudahAda) {
            return response()->json(['status' => 'error', 'message' => 'Area ' . $wilayahUpper . ' sudah difoto!'], 400);
        }

        try {
            $imageData = $request->foto_base64;
            // Bersihkan prefix base64 jika ada
            if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
                $imageData = substr($imageData, strpos($imageData, ',') + 1);
                $imageData = base64_decode($imageData);
            } else {
                $imageData = base64_decode($imageData);
            }

            $fileName = 'patroli/' . Auth::id() . '_' . Str::uuid() . '.jpg';
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

        // Validasi harus tepat 17 area
        if ($jumlahCheckpoint != 17) {
            return redirect()->back()->with('error', 'Semua 17 area belum selesai.');
        }

        // Redirect ke index (waktu sudah tercatat saat foto diambil)
        return redirect()->route('anggota.patroli.index')
                         ->with('success', 'Sesi ' . $jenisPatroli . ' berhasil disubmit!');
    }
}