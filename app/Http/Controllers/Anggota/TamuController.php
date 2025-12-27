<?php


namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Tamu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TamuController extends Controller
{
     
    public function index(Request $request)
    {
        
        $defaultStartDate = Carbon::now()->subWeek()->toDateString();
        $defaultEndDate = Carbon::now()->toDateString();

        
        $startDate = $request->input('start_date', $defaultStartDate);
        $endDate = $request->input('end_date', $defaultEndDate);
        $perPage = $request->input('per_page', 5);
        $search = $request->input('search');

        
        $riwayat_tamu = Tamu::whereDate('waktu_datang', '>=', $startDate)
                            ->whereDate('waktu_datang', '<=', $endDate)
                            ->orderBy('waktu_datang', 'desc')
                            ->get();

        
        return view('anggota.tamu-index', [
            'riwayat_tamu' => $riwayat_tamu,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

     
    public function create()
    {
        return view('anggota.tamu-create');
    }

     
    public function store(Request $request)
    {
        
        $request->validate([
            'nama_tamu' => 'required|string|max:255',
            'instansi' => 'required|string|max:255',
            'tanggal_kunjungan' => 'required|date', 
            'jam_kunjungan' => 'required|date_format:H:i', 
            'tujuan' => 'required|string|max:255',
        ]);

        
        $waktu_datang_gabungan = Carbon::parse($request->tanggal_kunjungan . ' ' . $request->jam_kunjungan);

        
        Tamu::create([
            'nama_tamu' => $request->nama_tamu,
            'instansi' => $request->instansi,
            'tujuan' => $request->tujuan,
            'waktu_datang' => $waktu_datang_gabungan, 
            'no_identitas' => $request->no_identitas,
            'id_pengguna' => Auth::id(), 
        ]);

        
        return redirect()->route('anggota.tamu.index')
                         ->with('success', 'Data tamu berhasil ditambahkan.');
    }
}