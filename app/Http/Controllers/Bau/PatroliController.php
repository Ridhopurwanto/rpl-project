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
        // 1. Ambil Tanggal (Default Hari Ini)
        $tanggalTerpilih = $request->input('tanggal', now()->format('Y-m-d'));
        
        // 2. Definisikan Opsi Jenis Patroli
        $jenisPatroliOptions = collect([
            'Patroli 1',
            'Patroli 2',
            'Patroli 3',
            'Patroli 4',
            'Patroli 5',
            'Patroli 6'
        ]);

        // 3. Logika Filter Jenis Patroli
        $jenisPatroliTerpilih = $request->input('jenis_patroli');

        if (empty($jenisPatroliTerpilih)) {
            $jenisPatroliTerpilih = $jenisPatroliOptions->first();
        }

        // 4. Mulai Query
        $query = Patroli::query();

        // Filter Tanggal
        $query->whereDate('tanggal', $tanggalTerpilih);
        
        // Filter Jenis
        if ($jenisPatroliTerpilih) {
             $query->where('jenis_patroli', $jenisPatroliTerpilih);
        }

        // Ambil data
        $dataPatroli = $query->orderBy('waktu_exact', 'asc')->get();

        // Ambil data Patroli Rules
        $patroliRules = PatroliRule::all()->groupBy('jenis_shift');

        return view('bau.patroli', [
            'dataPatroli' => $dataPatroli,
            'tanggalTerpilih' => $tanggalTerpilih,
            'jenisPatroliTerpilih' => $jenisPatroliTerpilih,
            'jenisPatroliOptions' => $jenisPatroliOptions,
            'patroliRules' => $patroliRules,
        ]);
    }
}
