<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Patroli; // <-- Import model Patroli
use Illuminate\Support\Facades\Auth; // <-- Import Auth
use Illuminate\Support\Facades\Storage; // Untuk simpan foto
use Illuminate\Support\Str; // Untuk nama file

class PatroliController extends Controller
{
    /**
     * Menampilkan daftar patroli (Gambar 1)
     */
    public function index(Request $request)
    {
        $tanggalTerpilih = $request->input('tanggal') 
                            ? Carbon::parse($request->input('tanggal')) 
                            : Carbon::today();
        
        // Kueri DIUBAH: Hanya ambil yang 'waktu_patroli' TIDAK NULL
        $allPatrols = Patroli::where('id_pengguna', Auth::id())
                        ->whereDate('tanggal', $tanggalTerpilih)
                        ->whereNotNull('waktu_patroli') // <-- HANYA TAMPILKAN YANG SUDAH SELESAI
                        ->orderBy('waktu_patroli', 'asc')
                        ->get();
        
        $patrolGroups = $allPatrols->groupBy('jenis_patroli');
                        
        return view('anggota.patroli-index', [
            'patrolGroups' => $patrolGroups,
            'tanggalTerpilih' => $tanggalTerpilih
        ]);
    }

    public function createSession(Request $request)
    {
        $user = Auth::user();
        $tanggal = Carbon::today();

        // 1. Tentukan Jenis Patroli yang sedang dipilih
        // Ambil dari URL query (?jenis_patroli=...) atau default ke 'Patroli 1'
        $jenisPatroliTerpilih = $request->input('jenis_patroli', 'Patroli 1');

        // 2. Daftar 17 Area (Hardcoded sesuai desain Anda)
        $semuaArea = [
            'AREA POS-2', 'LOBBY VVIP', 'LOBBY AUDIT', 'KOLAM IKAN VVIP', 
            'AREA BAU', 'AREA KANTIN', 'AREA BAAK', 'AKSES LORONG GD-3',
            'AKSES LORONG GD-2', 'AREA POS-3', 'AKSES BESI GD-2', 'AKSES KACA GD-2',
            'AKSES SELATAN AUDIT', 'AKSES RUANG LETKOR', 'AKSES PARKIR BASEMENT',
            'AKSES LIFT GD-2', 'AREA POS-1'
        ];

        // 3. Opsi untuk dropdown
        $opsiJenisPatroli = [
            'Patroli 1', 'Patroli 2', 'Patroli 3', 
            'Patroli 4', 'Patroli 5', 'Patroli 6'
        ];

        // 4. Ambil data patroli (checkpoint) yang SUDAH SELESAI
        //    untuk tanggal ini dan jenis patroli ini
        $completedCheckpoints = Patroli::where('id_pengguna', $user->id_pengguna)
                                ->whereDate('tanggal', $tanggal)
                                ->where('jenis_patroli', $jenisPatroliTerpilih)
                                ->whereNull('waktu_patroli') // <-- HANYA HITUNG YANG 'IN PROGRESS'
                                ->pluck('wilayah')
                                ->toArray();

        return view('anggota.patroli-create-session', [
            'semuaArea' => $semuaArea,
            'opsiJenisPatroli' => $opsiJenisPatroli,
            'jenisPatroliTerpilih' => $jenisPatroliTerpilih,
            'completedCheckpoints' => $completedCheckpoints, // Array wilayah yg selesai
            'totalCompleted' => count($completedCheckpoints) // Jumlah yg selesai
        ]);
    }

    /**
     * Halaman 2: Menampilkan Halaman Kamera (Gambar 2)
     */
    public function createCheckpoint(Request $request)
    {
        // Ambil data dari URL
        $jenisPatroli = $request->query('jenis_patroli');
        $wilayah = $request->query('wilayah');

        if (!$jenisPatroli || !$wilayah) {
            abort(400, 'Jenis patroli dan wilayah diperlukan.');
        }

        // Kirim data ke view (untuk ditampilkan di header & hidden input)
        return view('anggota.patroli-create-checkpoint', [
            'jenisPatroli' => $jenisPatroli,
            'wilayah' => $wilayah
        ]);
    }

    /**
     * Aksi: Menyimpan 1 foto checkpoint (Gambar 3)
     */
    public function storeCheckpoint(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'foto_base64' => 'required|string',
            'jenis_patroli' => 'required|string',
            'wilayah' => 'required|string',
        ]);

        // 2. Decode & Simpan Foto (Sama seperti Presensi)
        $imageData = $request->foto_base64;
        @list($type, $imageData) = explode(';', $imageData);
        @list(, $imageData) = explode(',', $imageData);
        $fileData = base64_decode($imageData);
        $fileName = 'patroli/' . Auth::id() . '_' . Str::uuid() . '.jpg';
        Storage::disk('public')->put($fileName, $fileData);

        // 3. Simpan data checkpoint ke Database
        // Model Event 'boot()' di Model Patroli akan auto-fill 'id_pengguna' & 'nama_lengkap'
        Patroli::create([
            'tanggal' => Carbon::today(),
            'waktu_exact' => now(), // Waktu foto diambil
            'waktu_patroli' => now(), // Jika ini juga diisi dengan waktu yang sama?
            'jenis_patroli' => $request->jenis_patroli,
            'wilayah' => $request->wilayah,
            'foto' => $fileName,
        ]);

        // 4. Redirect KEMBALI ke halaman Grid (dengan jenis patroli yang sama)
        return redirect()->route('anggota.patroli.createSession', [
            'jenis_patroli' => $request->jenis_patroli
        ])->with('success', 'Checkpoint ' . $request->wilayah . ' disimpan!');
    }

    /**
     * Aksi: Men-submit Sesi Patroli (17 area)
     * Mengisi 'waktu_patroli' untuk semua checkpoint.
     */
    public function submitSession(Request $request)
    {
        $request->validate([
            'jenis_patroli' => 'required|string',
        ]);

        $user = Auth::user();
        $tanggal = Carbon::today();
        $jenisPatroli = $request->jenis_patroli;
        $waktuSubmit = now(); // Ini adalah 'waktu_patroli' keseluruhan

        // 1. Cari semua checkpoint "In Progress" (yang waktu_patroli-nya NULL)
        //    untuk sesi ini.
        $checkpointsToSubmit = Patroli::where('id_pengguna', $user->id_pengguna)
                                    ->whereDate('tanggal', $tanggal)
                                    ->where('jenis_patroli', $jenisPatroli)
                                    ->whereNull('waktu_patroli'); // <-- Hanya yang masih 'In Progress'

        // 2. Cek apakah jumlahnya 17 (atau sesuai kebutuhan)
        if ($checkpointsToSubmit->count() < 17) {
            // Seharusnya tidak terjadi jika tombolnya nonaktif, tapi ini pengaman
            return redirect()->back()->with('error', 'Semua 17 area belum selesai.');
        }

        // 3. Update semua 17 rekaman tersebut
        $checkpointsToSubmit->update([
            'waktu_patroli' => $waktuSubmit
        ]);

        // 4. Redirect ke halaman index (daftar utama)
        return redirect()->route('anggota.patroli.index')
                         ->with('success', 'Sesi ' . $jenisPatroli . ' berhasil disubmit!');
    }
}