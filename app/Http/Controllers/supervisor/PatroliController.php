<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patroli; 
use App\Models\PatroliRule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PatroliController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil Tanggal (Default Hari Ini)
        $tanggalTerpilih = $request->input('tanggal', now()->format('Y-m-d'));
        $tanggalTerpilihPagi = $request->input('tanggal_pagi', $tanggalTerpilih);
        $tanggalTerpilihMalam = $request->input('tanggal_malam', $tanggalTerpilih);
        
        // 2. Definisikan Opsi Jenis Patroli (Untuk Dropdown Filter)
        $jenisPatroliOptions = collect([
            'Semua',
            'Patroli 1', 'Patroli 2', 'Patroli 3', 
            'Patroli 4', 'Patroli 5', 'Patroli 6'
        ]);
        
        // ---------------------------------------------------------
        // QUERY SHIFT PAGI (jenis_shift = 'Pagi')
        // ---------------------------------------------------------
        $jenisPatroliTerpilihPagi = $request->input('jenis_patroli_pagi', 'Semua');
        $perPagePagi = $request->input('per_page_pagi', 10);

        $queryPagi = Patroli::query()
            ->with(['claim.rule']) // Eager load relasi
            ->whereDate('tanggal', $tanggalTerpilihPagi)
            ->whereHas('claim.rule', function ($q) {
                // Filter hanya yang shift Pagi
                $q->where('jenis_shift', 'Pagi'); 
            })
            ->orderBy('waktu_exact', 'asc');

        // Filter Jenis Patroli Pagi (jika user memilih spesifik)
        if ($jenisPatroliTerpilihPagi !== 'Semua') {
            $queryPagi->whereHas('claim.rule', function ($q) use ($jenisPatroliTerpilihPagi) {
                $q->where('jenis_patroli', $jenisPatroliTerpilihPagi);
            });
        }

        $dataPatroliPagi = $queryPagi->paginate($perPagePagi, ['*'], 'page_pagi');


        // ---------------------------------------------------------
        // QUERY SHIFT MALAM (jenis_shift = 'Malam')
        // ---------------------------------------------------------
        $jenisPatroliTerpilihMalam = $request->input('jenis_patroli_malam', 'Semua');
        $perPageMalam = $request->input('per_page_malam', 10);

        $queryMalam = Patroli::query()
            ->with(['claim.rule']) // Eager load relasi
            ->whereDate('tanggal', $tanggalTerpilihMalam)
            ->whereHas('claim.rule', function ($q) {
                // Filter hanya yang shift Malam
                $q->where('jenis_shift', 'Malam'); 
            })
            ->orderBy('waktu_exact', 'asc');

        // Filter Jenis Patroli Malam (jika user memilih spesifik)
        if ($jenisPatroliTerpilihMalam !== 'Semua') {
            $queryMalam->whereHas('claim.rule', function ($q) use ($jenisPatroliTerpilihMalam) {
                $q->where('jenis_patroli', $jenisPatroliTerpilihMalam);
            });
        }

        $dataPatroliMalam = $queryMalam->paginate($perPageMalam, ['*'], 'page_malam');



        
        if ($request->ajax()) {
            return response()->json([
                'html_pagi' => view('supervisor.partials.patroli-list', [
                    'data' => $dataPatroliPagi,
                    'shift' => 'pagi'
                ])->render(),
                'html_malam' => view('supervisor.partials.patroli-list', [
                    'data' => $dataPatroliMalam,
                    'shift' => 'malam'
                ])->render(),
            ]);
        }

        return view('supervisor.patroli', [
            // Data Utama
            'dataPatroliPagi' => $dataPatroliPagi,
            'dataPatroliMalam' => $dataPatroliMalam,
            
            // Filter State
            'tanggalTerpilih' => $tanggalTerpilih,
            'tanggalTerpilihPagi' => $tanggalTerpilihPagi,
            'tanggalTerpilihMalam' => $tanggalTerpilihMalam,
            'jenisPatroliTerpilihPagi' => $jenisPatroliTerpilihPagi,
            'jenisPatroliTerpilihMalam' => $jenisPatroliTerpilihMalam,
            
            // Options & Config
            'jenisPatroliOptions' => $jenisPatroliOptions,
            'perPagePagi' => $perPagePagi,
            'perPageMalam' => $perPageMalam,
        ]);
    }






}
