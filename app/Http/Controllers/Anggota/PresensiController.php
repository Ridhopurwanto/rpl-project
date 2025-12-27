<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Presensi;
use App\Models\Shift;
use App\Models\ShiftRule;

class PresensiController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        
        $startDate = $request->input('start_date', Carbon::today()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());

        
        $startCarbon = Carbon::parse($startDate);
        $bulan = $startCarbon->month;
        $tahun = $startCarbon->year;
        
        $shiftsDariDB = Shift::with('shiftRule')
                            ->where('id_pengguna', $user->id_pengguna)
                            ->whereMonth('tanggal', $bulan)
                            ->whereYear('tanggal', $tahun)
                            ->get();

        $shiftMap = $shiftsDariDB->keyBy(function ($shift) {
            return Carbon::parse($shift->tanggal)->format('Y-m-d');
        });

        
        $calStart = $startCarbon->copy()->startOfMonth();
        $calEnd = $startCarbon->copy()->endOfMonth();
        $period = CarbonPeriod::create($calStart, $calEnd);
        $dataKalender = [];

        for ($i = 0; $i < $calStart->dayOfWeek; $i++) {
            $dataKalender[] = ['tanggal' => null, 'jenis_shift' => null];
        }

        foreach ($period as $date) {
            $tglStr = $date->format('Y-m-d');
            $shiftData = $shiftMap->get($tglStr);
            $namaJenis = $shiftData && $shiftData->shiftRule ? strtolower($shiftData->shiftRule->jenis_shift) : null;
            
            $dataKalender[] = [
                'tanggal' => $date->format('d'),
                'jenis_shift' => $namaJenis,
            ];
        }

        
        $riwayatPresensi = Presensi::where('id_pengguna', $user->id_pengguna)
                            ->whereDate('waktu', '>=', $startDate)
                            ->whereDate('waktu', '<=', $endDate)
                            ->orderBy('waktu', 'desc')
                            ->get();

        $now = Carbon::now();

        $todayShiftData = Shift::with('shiftRule')
                            ->where('id_pengguna', $user->id_pengguna)
                            ->whereDate('tanggal', Carbon::today())
                            ->first();

        
        
        $presensiLog = Presensi::where('id_pengguna', $user->id_pengguna)
                            ->whereDate('waktu', Carbon::today()) 
                            ->get();

        
        $sudahMasuk = $presensiLog->where('jenis_presensi', 'Masuk')->first();   
        $sudahPulang = $presensiLog->where('jenis_presensi', 'Pulang')->first();  

        
        
        $jadwalAbsen = [
            'nama_shift'     => 'TIDAK ADA JADWAL',
            'info_terdekat'  => '-',
            'canpresensi'   => false,
            'pesan_error'    => 'Tidak ada jadwal shift hari ini.',
            'disable_masuk'  => true, 
            'disable_pulang' => true,
            'default_jenis'  => 'masuk',
        ];

        if ($todayShiftData && $todayShiftData->shiftRule) {
            $rule = $todayShiftData->shiftRule;
            $jadwalAbsen['nama_shift'] = strtoupper($rule->jenis_shift);
            $menitDibuka = $rule->dibuka ?? 0;

            if ($rule->jam_masuk && $rule->jam_keluar) {
                $jamMasuk = Carbon::createFromTimeString($rule->jam_masuk);
                $jamKeluar = Carbon::createFromTimeString($rule->jam_keluar);

                if ($jamKeluar->lt($jamMasuk)) {
                    $jamKeluar->addDay();
                }

                $waktuBukaMasuk = $jamMasuk->copy()->subMinutes($menitDibuka);
                $waktuBukaPulang = $jamKeluar->copy();

                if( $jadwalAbsen['nama_shift'] == 'OFF'){
                    $jadwalAbsen['info_terdekat'] = '-';
                    $jadwalAbsen['canpresensi'] = false;
                    $jadwalAbsen['pesan_error'] = 'Tidak sedang bekerja';
                    $jadwalAbsen['disable_masuk'] = true;
                    $jadwalAbsen['disable_pulang'] = true;
                }
                elseif (!$sudahMasuk) {
                    $jadwalAbsen['info_terdekat'] = 'MASUK DIBUKA: ' . $waktuBukaMasuk->format('H:i');
                    $jadwalAbsen['disable_masuk'] = false;
                    $jadwalAbsen['disable_pulang'] = true;
                    $jadwalAbsen['default_jenis'] = 'masuk';

                    if ($now->gte($waktuBukaMasuk)) {
                        $jadwalAbsen['canpresensi'] = true;
                        $jadwalAbsen['pesan_error'] = '';
                    } else {
                        $jadwalAbsen['canpresensi'] = false;
                        $jadwalAbsen['pesan_error'] = 'Presensi Masuk belum dibuka. Tunggu ' . $waktuBukaMasuk->format('H:i');
                    }
                } elseif (!$sudahPulang) {
                    $jadwalAbsen['info_terdekat'] = 'PULANG PADA PUKUL: ' . $waktuBukaPulang->format('H:i');
                    $jadwalAbsen['disable_masuk'] = true;
                    $jadwalAbsen['disable_pulang'] = false;
                    $jadwalAbsen['default_jenis'] = 'pulang';
                    $jadwalAbsen['canpresensi'] = true;
                    $jadwalAbsen['pesan_error'] = '';
                } else {
                    $jadwalAbsen['info_terdekat'] = 'PRESENSI SELESAI';
                    $jadwalAbsen['canpresensi'] = false;
                    $jadwalAbsen['pesan_error'] = 'Anda sudah menyelesaikan presensi hari ini.';
                    $jadwalAbsen['disable_masuk'] = true;
                    $jadwalAbsen['disable_pulang'] = true;
                }
            }
        }

        $shiftHariIni = $todayShiftData && $todayShiftData->shiftRule ? strtoupper($todayShiftData->shiftRule->jenis_shift) : 'TIDAK ADA JADWAL';

        
        $wajibGeotag = true; 
        if ($todayShiftData && $todayShiftData->shiftRule) {
            $wajibGeotag = (bool) $todayShiftData->shiftRule->is_geotag_enabled;
        }

        return view('anggota.presensi', [
            'namaBulan' => $startCarbon->format('F Y'),
            'dataKalender' => $dataKalender,
            'riwayatPresensi' => $riwayatPresensi,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'shiftHariIni' => $shiftHariIni,
            'jadwalAbsen' => $jadwalAbsen,
            'wajibGeotag' => $wajibGeotag,
        ]);
    }

    public function store(Request $request)
    {        
        $user = Auth::user();
        $now = Carbon::now();
        $status = 'tepat waktu';
        $shiftHariIni = Shift::with('shiftRule')
                        ->where('id_pengguna', $user->id_pengguna)
                        ->whereDate('tanggal', $now)
                        ->first();

        $isGeotagEnabled = true; 
        if ($shiftHariIni && $shiftHariIni->shiftRule) {
            $isGeotagEnabled = $shiftHariIni->shiftRule->is_geotag_enabled;
        }

        $rules = [
            'foto_base64' => 'required|string',
            'jenis_presensi' => 'required|in:masuk,pulang',
            
            'latitude' => 'nullable', 
            'longitude' => 'nullable',
        ];

        
        if ($isGeotagEnabled) {
            $rules['latitude'] = 'required|numeric';
            $rules['longitude'] = 'required|numeric';
        }

        $request->validate($rules);

        try {
            
            $distance = 0; 
            if ($isGeotagEnabled) {
                $campusLat = -6.2315465;
                $campusLng = 106.8666516;
                $maxDistance = 500;
                
                $distance = $this->calculateDistance(
                    $request->latitude,
                    $request->longitude,
                    $campusLat,
                    $campusLng
                );
                
                if ($distance > $maxDistance) {
                    return redirect()->back()->with('error', 
                        'Lokasi Anda ' . round($distance) . 'm dari titik presensi. Maksimal ' . $maxDistance . 'm.');
                }
            }
                  
            
            $cekDuplikat = Presensi::where('id_pengguna', $user->id_pengguna)
                            ->whereDate('waktu', $now) 
                            ->where('jenis_presensi', $request->jenis_presensi)
                            ->exists();
            
            if ($cekDuplikat) {
                return redirect()->back()->with('error', 'Anda sudah presensi ' . $request->jenis_presensi . ' hari ini.');
            }

            
            $imageData = $request->foto_base64;
            if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
                $imageData = substr($imageData, strpos($imageData, ',') + 1);
                $imageData = base64_decode($imageData);
            } else {
                $imageData = base64_decode($imageData);
            }
            $fileName = 'presensi/' . $request->jenis_presensi . '_' . $user->id_pengguna . '_' . time() . '.jpg';
            Storage::disk('public')->put($fileName, $imageData);

            
            $status = 'tepat waktu';
            $shiftHariIni = Shift::with('shiftRule')
                            ->where('id_pengguna', $user->id_pengguna)
                            ->whereDate('tanggal', $now)
                            ->first();

            if ($request->jenis_presensi == 'masuk' && $shiftHariIni && $shiftHariIni->shiftRule) {
                $rule = $shiftHariIni->shiftRule;
                
                if ($rule->jam_masuk) {
                    $jamMasuk = Carbon::createFromTimeString($rule->jam_masuk);
                    $batasTerlambat = $jamMasuk->copy()->addMinutes($rule->toleransi);
                    $waktuSekarang = Carbon::createFromTimeString($now->format('H:i:s'));
                    
                    if ($waktuSekarang->gt($batasTerlambat)) {
                        $status = 'terlambat';
                    }
                }
            } elseif ($request->jenis_presensi == 'pulang' && $shiftHariIni && $shiftHariIni->shiftRule) {
                $rule = $shiftHariIni->shiftRule;
                
                if ($rule->jam_keluar) {
                    $jamMasuk = Carbon::createFromTimeString($rule->jam_masuk);
                    $jamKeluar = Carbon::createFromTimeString($rule->jam_keluar);
                    
                    if ($jamKeluar->lt($jamMasuk)) {
                        $jamKeluar->addDay();
                    }
                                
                    $batasAwalPulang = $jamKeluar->copy()->subMinutes($rule->toleransi);
                    $batasAkhirPulang = $jamKeluar->copy()->addMinutes($rule->toleransi);
                    $waktuSekarang = Carbon::createFromTimeString($now->format('H:i:s'));
                    
                    if ($waktuSekarang->lt($batasAwalPulang)) {
                        $status = 'terlalu cepat';
                    }
                    elseif($waktuSekarang->gt($batasAkhirPulang)){
                        $status = 'terlambat';
                    }
                }                
            }


            
            Presensi::create([
                'id_pengguna'    => $user->id_pengguna,
                'id_shift'       => $shiftHariIni ? $shiftHariIni->id_shift : null,
                'nama_lengkap'   => $user->nama_lengkap,
                'waktu'          => $now,
                'tanggal'        => $now->toDateString(), 
                'foto'           => $fileName,
                'jenis_presensi' => $request->jenis_presensi,
                'status'         => $status,
                'latitude'       => $isGeotagEnabled ? $request->latitude : null,
                'longitude'      => $isGeotagEnabled ? $request->longitude : null,
            ]);

            return redirect()->route('anggota.presensi.index')
                             ->with('success', 'Presensi ' . $request->jenis_presensi . ' berhasil! Status: ' . $status);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

     
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; 
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        
        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);
             
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        
        $distance = $earthRadius * $c;
        
        return $distance;
    }
}