<?php

namespace App\Http\Controllers\Bau;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patroli; 
use App\Models\PatroliRule;

class PatroliController extends Controller
{
    public function index(Request $request)
    {
        $tanggalTerpilih = $request->input('tanggal', now()->format('Y-m-d'));
        $perPage = $request->input('per_page', 10);
        
        $jenisPatroliOptions = collect([
            'Patroli 1',
            'Patroli 2',
            'Patroli 3',
            'Patroli 4',
            'Patroli 5',
            'Patroli 6'
        ]);

        $jenisPatroliTerpilih = $request->input('jenis_patroli');

        if (empty($jenisPatroliTerpilih)) {
            $jenisPatroliTerpilih = $jenisPatroliOptions->first();
        }

        $query = Patroli::query();
        $query->whereDate('tanggal', $tanggalTerpilih);
        
        if ($jenisPatroliTerpilih) {
             $query->where('jenis_patroli', $jenisPatroliTerpilih);
        }

        $dataPatroli = $query->orderBy('waktu_exact', 'asc')->paginate($perPage);

        return view('bau.patroli', [
            'dataPatroli' => $dataPatroli,
            'tanggalTerpilih' => $tanggalTerpilih,
            'jenisPatroliTerpilih' => $jenisPatroliTerpilih,
            'jenisPatroliOptions' => $jenisPatroliOptions,
            'perPage' => $perPage,
        ]);
    }
}
