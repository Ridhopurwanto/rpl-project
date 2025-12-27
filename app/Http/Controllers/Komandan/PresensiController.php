<?php

namespace App\Http\Controllers\Komandan;

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
                               ->paginate($perPage, ['*'], 'page_masuk')
                               ->appends($request->query()); 

        
        $dataPulang = $queryPulang->where('presensi.jenis_presensi', 'Pulang')
                                 ->orderBy('presensi.waktu', 'asc')
                                 ->paginate($perPage, ['*'], 'page_pulang')
                                 ->appends($request->query()); 

        
        
        $rules = ShiftRule::whereIn('jenis_shift', ['Pagi', 'Malam', 'Non Shift'])->get();
        
        
        $globalRule = $rules->firstWhere('jenis_shift', 'Pagi');

        
        if ($request->ajax()) {
            return response()->json([
                'html_masuk' => view('komandan.partials.presensi-masuk', [
                    'dataMasuk' => $dataMasuk,
                    'shiftTerpilih' => $shiftFilter,
                ])->render(),
                'html_pulang' => view('komandan.partials.presensi-pulang', [
                    'dataPulang' => $dataPulang,
                    'shiftTerpilih' => $shiftFilter,
                ])->render(),
            ]);
        }

        return view('komandan.presensi', [
            'dataMasuk' => $dataMasuk,
            'dataPulang' => $dataPulang,
            'tanggalTerpilih' => $tanggalFilter,
            'shiftTerpilih' => $shiftFilter, 
            'rules' => $rules,
            'globalToleransi' => $globalRule ? $globalRule->toleransi : 0,
            'globalDibuka' => $globalRule ? $globalRule->dibuka : 0,
            'perPage' => $perPage,
        ]);
    }

     
    public function destroy($id_presensi)
    {
        if (Auth::user()->peran !== 'komandan') {
            return redirect()->route('komandan.presensi')->with('error', 'Anda tidak memiliki hak akses.');
        }

        try {
            $presensi = Presensi::findOrFail($id_presensi);
            
            
            if ($presensi->foto) {
                Storage::disk('public')->delete($presensi->foto);
            }

            $presensi->delete();
            
            return redirect()->back()->with('success', 'Data presensi berhasil dihapus.');
        
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data.');
        }
    }

     
    

     
    public function update(Request $request, $id_presensi)
    {
        if (Auth::user()->peran !== 'komandan') {
            return redirect()->route('komandan.presensi')->with('error', 'Anda tidak memiliki hak akses.');
        }

        
        $request->validate([
            'waktu' => 'required|date',
            'status' => 'required|in:tepat waktu,terlambat,terlalu cepat,izin',
            'jenis_presensi' => 'required|in:Masuk,Pulang',
        ]);

        try {
            $presensi = Presensi::findOrFail($id_presensi);
            
            
            $oldStatus = $presensi->status;
            
            $presensi->update([
                'waktu' => $request->waktu,
                'status' => $request->status,
                'jenis_presensi' => $request->jenis_presensi,
                'tanggal' => Carbon::parse($request->waktu)->format('Y-m-d'), 
            ]);

            
            if ($oldStatus != $request->status) {
                $user = \App\Models\User::find($presensi->id_pengguna);
                if ($user) {
                    
                    $waktuFormatted = \Carbon\Carbon::parse($presensi->waktu)->translatedFormat('l, d F Y H:i');
                    $pesan = "Status presensi Anda pada {$waktuFormatted} telah diubah oleh Komandan dari '{$oldStatus}' menjadi '{$request->status}'.";
                    
                    try {
                        $user->notify(new \App\Notifications\PerubahanStatusPresensiNotification($pesan, $presensi));
                    } catch (\Exception $e) {
                         
                         \Illuminate\Support\Facades\Log::error('Gagal kirim notifikasi status presensi: ' . $e->getMessage());
                    }
                }
            }

            return redirect()->back()->with('success', 'Data presensi berhasil diperbarui.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
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

    public function updateRules(Request $request)
    {
        if (Auth::user()->peran !== 'komandan') {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'masuk_pagi' => 'required',
            'keluar_pagi' => 'required',
            'masuk_malam' => 'required',
            'keluar_malam' => 'required',
            'masuk_non' => 'required',
            'keluar_non' => 'required',
            'toleransi' => 'required|numeric|min:0',
            'dibuka' => 'required|numeric|min:0',
        ]);

        $geoStatus = $request->input('isgeotagenabled', 0);

        
        
        
        $pagiKeluar = substr($request->keluar_pagi, 0, 5);
        $malamMasuk = substr($request->masuk_malam, 0, 5);

        if ($pagiKeluar != $malamMasuk) {
            return redirect()->back()->with('error', 'Jam Pulang Shift Pagi harus bersambung dengan Jam Masuk Shift Malam (Tidak boleh ada jeda).');
        }

        try {
            
            ShiftRule::where('jenis_shift', 'Pagi')->update([
                'jam_masuk' => $request->masuk_pagi,
                'jam_keluar' => $request->keluar_pagi,
                'toleransi' => $request->toleransi,
                'dibuka' => $request->dibuka,
                'is_geotag_enabled' => $geoStatus,
            ]);

            
            ShiftRule::where('jenis_shift', 'Malam')->update([
                'jam_masuk' => $request->masuk_malam,
                'jam_keluar' => $request->keluar_malam,
                'toleransi' => $request->toleransi,
                'dibuka' => $request->dibuka,
                'is_geotag_enabled' => $geoStatus,
            ]);

            
            ShiftRule::where('jenis_shift', 'Non Shift')->update([
                'jam_masuk' => $request->masuk_non,
                'jam_keluar' => $request->keluar_non,
                'toleransi' => $request->toleransi,
                'dibuka' => $request->dibuka,
                'is_geotag_enabled' => $geoStatus,
            ]);
            

            

            return redirect()->back()->with('success', 'Pengaturan Shift Rule berhasil diperbarui.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal update rule: ' . $e->getMessage());
        }
    }
}