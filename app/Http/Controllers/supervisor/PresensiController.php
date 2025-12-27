<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Presensi; 
use App\Models\Shift;    
use App\Models\ShiftRule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class PresensiController extends Controller
{
     
    public function index(Request $request)
    {
        
        $tanggalFilter = $request->input('tanggal', now()->format('Y-m-d'));
        
        
        $shiftFilter = $request->input('shift', 'semua');
        
        $perPage = $request->input('per_page', 10);

        
        $query = Presensi::join('shift', 'presensi.id_shift', '=', 'shift.id_shift')
                         ->whereDate('presensi.tanggal', $tanggalFilter)
                         ->select('presensi.*', 'shift.jenis_shift'); 

        $shiftId = ShiftRule::where('jenis_shift', $shiftFilter)
                                  ->first();

        
        if ($shiftFilter !== 'semua') {
            $query->where('shift.jenis_shift', $shiftId->idshift_rule);
        }

        
        
        $queryMasuk = clone $query;
        $queryPulang = clone $query;

        
        $dataMasuk = $queryMasuk->where('presensi.jenis_presensi', 'Masuk')
                               ->orderBy('presensi.waktu', 'asc')
                               ->paginate($perPage, ['*'], 'page_masuk');

        
        $dataPulang = $queryPulang->where('presensi.jenis_presensi', 'Pulang')
                                 ->orderBy('presensi.waktu', 'asc')
                                 ->paginate($perPage, ['*'], 'page_pulang');



        
        if ($request->ajax()) {
            return response()->json([
                'html_masuk' => view('supervisor.partials.presensi-masuk', [
                    'dataMasuk' => $dataMasuk,
                    'shiftTerpilih' => $shiftFilter,
                ])->render(),
                'html_pulang' => view('supervisor.partials.presensi-pulang', [
                    'dataPulang' => $dataPulang,
                    'shiftTerpilih' => $shiftFilter,
                ])->render(),
            ]);
        }

        return view('supervisor.presensi', [
            'dataMasuk' => $dataMasuk,
            'dataPulang' => $dataPulang,
            'tanggalTerpilih' => $tanggalFilter,
            'shiftTerpilih' => $shiftFilter,
            'perPage' => $perPage,
        ]);
    }



     
    




    
    
    
     
    public function createForAnggota()
    {
        $userId = Auth::id(); 
        $today = now()->format('Y-m-d');

        
        $shiftHariIni = Shift::where('id_pengguna', $userId)
                             ->whereDate('tanggal', $today)
                             ->first();

        
        $presensiMasuk = Presensi::where('id_pengguna', $userId)
                                 ->whereDate('tanggal', $today)
                                 ->where('jenis_presensi', 'Masuk')
                                 ->first();
        
        
        $presensiPulang = Presensi::where('id_pengguna', $userId)
                                  ->whereDate('tanggal', $today)
                                  ->where('jenis_presensi', 'Pulang')
                                  ->first();
        
        return view('anggota.presensi', [
            'shiftHariIni' => $shiftHariIni,
            'presensiMasuk' => $presensiMasuk,
            'presensiPulang' => $presensiPulang,
        ]);
    }

     
    public function storeForAnggota(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $userId = Auth::id();
        $namaLengkap = Auth::user()->nama_lengkap ?? Auth::user()->username; 
        $today = now()->format('Y-m-d');
        
        
        $shiftHariIni = Shift::where('id_pengguna', $userId)
                             ->whereDate('tanggal', $today)
                             ->first();

        
        if (!$shiftHariIni) {
            return redirect()->back()->with('error', 'Anda tidak memiliki jadwal shift hari ini.');
        }

        
        if ($shiftHariIni->jenis_shift == 'Off') {
            return redirect()->back()->with('error', 'Anda sedang libur (Off) hari ini.');
        }

        
        $presensiMasuk = Presensi::where('id_pengguna', $userId)
                                 ->whereDate('tanggal', $today)
                                 ->where('jenis_presensi', 'Masuk')
                                 ->first();
        
        $presensiPulang = Presensi::where('id_pengguna', $userId)
                                  ->whereDate('tanggal', $today)
                                  ->where('jenis_presensi', 'Pulang')
                                  ->first();

        
        $path = $request->file('foto')->store('presensi', 'public');

        try {
            if (!$presensiMasuk) {
                
                
                
                $batasMasuk = ($shiftHariIni->jenis_shift == 'Pagi') ? '07:00:00' : '19:00:00';
                $status = (now()->format('H:i:s') > $batasMasuk) ? 'terlambat' : 'tepat waktu';

                Presensi::create([
                    'id_pengguna' => $userId,
                    'id_shift' => $shiftHariIni->id_shift, 
                    'nama_lengkap' => $namaLengkap,
                    'waktu' => now(),
                    'foto' => $path,
                    'status' => $status,
                    'jenis_presensi' => 'Masuk', 
                    'tanggal' => $today,
                ]);
                return redirect()->back()->with('success', 'Berhasil melakukan presensi MASUK.');

            } elseif (!$presensiPulang) {
                
                
                
                $batasPulang = ($shiftHariIni->jenis_shift == 'Pagi') ? '19:00:00' : '07:00:00';
                $status = (now()->format('H:i:s') < $batasPulang && $shiftHariIni->jenis_shift == 'Pagi') ? 'terlalu cepat' : 'tepat waktu';

                Presensi::create([
                    'id_pengguna' => $userId,
                    'id_shift' => $shiftHariIni->id_shift, 
                    'nama_lengkap' => $namaLengkap,
                    'waktu' => now(),
                    'foto' => $path,
                    'status' => $status,
                    'jenis_presensi' => 'Pulang', 
                    'tanggal' => $today,
                ]);
                return redirect()->back()->with('success', 'Berhasil melakukan presensi PULANG.');
            } else {
                
                Storage::disk('public')->delete($path); 
                return redirect()->back()->with('error', 'Anda sudah menyelesaikan presensi hari ini.');
            }
        } catch (\Exception $e) {
            Storage::disk('public')->delete($path);
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


}