<?php


namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\GangguanKamtibmas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; 
use Carbon\Carbon;
use Illuminate\Support\Str; 

class GangguanKamtibmasController extends Controller
{
     
    public function index(Request $request)
    {
        
        $bulan_terpilih = $request->input('bulan', date('Y-m'));
        $kategori_terpilih = $request->input('kategori', 'semua');

        if (empty($bulan_terpilih)) {
            $bulan_terpilih = Carbon::today()->format('Y-m');
        }

        
        $carbonDate = Carbon::createFromFormat('Y-m', $bulan_terpilih);

        
        $kategoris = \DB::select("SHOW COLUMNS FROM gangguan_kamtibmas WHERE Field = 'kategori'")[0]->Type;
        preg_match('/^enum\((.*)\)$/', $kategoris, $matches);
        $kategori_list = array_map(function($value) {
            return trim($value, "'");
        }, explode(',', $matches[1]));

        
        $query = GangguanKamtibmas::query()
                    ->whereYear('waktu_lapor', $carbonDate->year)
                    ->whereMonth('waktu_lapor', $carbonDate->month);

        
        if ($kategori_terpilih !== 'semua') {
            $query->where('kategori', $kategori_terpilih);
        }

        
        $laporan_gangguan = $query->orderBy('waktu_lapor', 'desc')->get();

        
        return view('anggota.gangguan-index', [
            'laporan_gangguan' => $laporan_gangguan,
            'bulan_terpilih' => $bulan_terpilih,
            'kategori_terpilih' => $kategori_terpilih,
            'kategori_list' => $kategori_list,
        ]);
    }

     
    public function create()
    {
        return view('anggota.gangguan-create');
    }

     
    public function store(Request $request)
    {
        
        $request->validate([
            
            'foto_base64' => 'required|string', 
            
            'tanggal_lapor' => 'required|date',
            'waktu_lapor_time' => 'required|date_format:H:i',
            'kategori' => 'required|string',
            'lokasi' => 'required|string|max:255',
            'deskripsi' => 'nullable|string'
        ]);

        
        $pathFoto = null;
        if ($request->foto_base64) {
            
            list($type, $data) = explode(';', $request->foto_base64);
            list(, $data) = explode(',', $data);
            $imageData = base64_decode($data);
            
            
            $filename = 'foto_gangguan/' . Str::random(20) . '.jpg';
            
            
            Storage::disk('public')->put($filename, $imageData);
            $pathFoto = $filename;
        }

        
        $waktu_lapor_gabungan = Carbon::parse($request->tanggal_lapor . ' ' . $request->waktu_lapor_time);

        
        GangguanKamtibmas::create([
            'id_pengguna' => Auth::id(),
            'waktu_lapor' => $waktu_lapor_gabungan,
            'lokasi' => $request->lokasi,
            'kategori' => $request->kategori,
            'deskripsi' => $request->deskripsi,
            'foto' => $pathFoto,
        ]);

        
        return redirect()->route('anggota.gangguan.index')
                         ->with('success', 'Laporan gangguan berhasil ditambahkan.');
    }
}