<?php

namespace App\Http\Controllers\Komandan;

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
        
        
        $tanggalPagi = $request->filled('tanggal_pagi') 
            ? $request->input('tanggal_pagi') 
            : now()->format('Y-m-d');
            
        $tanggalMalam = $request->filled('tanggal_malam') 
            ? $request->input('tanggal_malam') 
            : now()->format('Y-m-d');
        
        
        $jenisPatroliOptions = collect([
            'Semua',
            'Patroli 1', 'Patroli 2', 'Patroli 3', 
            'Patroli 4', 'Patroli 5', 'Patroli 6'
        ]);
        
        
        
        
        $jenisPatroliTerpilihPagi = $request->input('jenis_patroli_pagi', 'Semua');
        $perPagePagi = $request->input('per_page_pagi', 10);

        $queryPagi = Patroli::query()
            ->with(['claim.rule']) 
            ->whereHas('claim', function ($q) use ($tanggalPagi) {
                $q->whereDate('tanggal', $tanggalPagi); 
                $q->whereHas('rule', function ($qRule) {
                    $qRule->where('jenis_shift', 'Pagi'); 
                });
            })
            ->orderBy('waktu_exact', 'asc');

        
        if ($jenisPatroliTerpilihPagi !== 'Semua') {
            $queryPagi->whereHas('claim.rule', function ($q) use ($jenisPatroliTerpilihPagi) {
                $q->where('jenis_patroli', $jenisPatroliTerpilihPagi);
            });
        }

        $dataPatroliPagi = $queryPagi->paginate($perPagePagi, ['*'], 'page_pagi');


        
        
        
        $jenisPatroliTerpilihMalam = $request->input('jenis_patroli_malam', 'Semua');
        $perPageMalam = $request->input('per_page_malam', 10);

        $queryMalam = Patroli::query()
            ->with(['claim.rule']) 
            ->whereHas('claim', function ($q) use ($tanggalMalam) {
                $q->whereDate('tanggal', $tanggalMalam); 
                $q->whereHas('rule', function ($qRule) {
                    $qRule->where('jenis_shift', 'Malam'); 
                });
            })
            ->orderBy('waktu_exact', 'asc');    

        
        if ($jenisPatroliTerpilihMalam !== 'Semua') {
            $queryMalam->whereHas('claim.rule', function ($q) use ($jenisPatroliTerpilihMalam) {
                $q->where('jenis_patroli', $jenisPatroliTerpilihMalam);
            });
        }

        $dataPatroliMalam = $queryMalam->paginate($perPageMalam, ['*'], 'page_malam');


        
        
        
        
        
        $patroliRules = PatroliRule::all()->groupBy('jenis_shift');
        
        if ($request->ajax()) {
            return response()->json([
                'html_pagi' => view('komandan.partials.patroli-list', [
                    'data' => $dataPatroliPagi,
                    'shift' => 'pagi'
                ])->render(),
                'html_malam' => view('komandan.partials.patroli-list', [
                    'data' => $dataPatroliMalam,
                    'shift' => 'malam'
                ])->render(),
            ]);
        }

        return view('komandan.patroli', [
            
            'dataPatroliPagi' => $dataPatroliPagi,
            'dataPatroliMalam' => $dataPatroliMalam,
            
            
            'tanggalPagi' => $tanggalPagi,
            'tanggalMalam' => $tanggalMalam,
            'jenisPatroliTerpilihPagi' => $jenisPatroliTerpilihPagi,
            'jenisPatroliTerpilihMalam' => $jenisPatroliTerpilihMalam,
            
            
            'jenisPatroliOptions' => $jenisPatroliOptions,
            'patroliRules' => $patroliRules,
            'perPagePagi' => $perPagePagi,
            'perPageMalam' => $perPageMalam,
        ]);
    }

     
    public function update(Request $request, $id)
    {
        $request->validate([
            'wilayah' => 'required|string|max:255',
        ]);

        try {
            $patroli = Patroli::findOrFail($id);
            $patroli->wilayah = $request->wilayah;
            $patroli->save();

            return back()->with('success', 'Data patroli berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

     
    public function destroy(Request $request, $id)
    {
        try {
            $patroli = Patroli::findOrFail($id);
            
            
            if ($patroli->foto) {
                Storage::delete('public/' . $patroli->foto);
            }
            
            $patroli->delete();

            
            $params = $request->only([
                'tanggal_pagi', 
                'tanggal_malam', 
                'per_page_pagi', 
                'per_page_malam', 
                'jenis_patroli_pagi', 
                'jenis_patroli_malam'
            ]);

            
            return redirect()->route('komandan.patroli', $params)
                             ->with('success', 'Data patroli berhasil dihapus.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

     
    public function updateRules(Request $request)
    {
        try {
            
            if ($request->has('shift_pagi')) {
                foreach ($request->shift_pagi as $jenisPatroli => $waktu) {
                    PatroliRule::updateOrCreate(
                        [
                            'jenis_shift' => 'Pagi',
                            'jenis_patroli' => $jenisPatroli
                        ],
                        [
                            'jam_mulai' => $waktu['jam_mulai'],
                            'jam_selesai' => $waktu['jam_selesai']
                        ]
                    );
                }
            }

            
            if ($request->has('shift_malam')) {
                foreach ($request->shift_malam as $jenisPatroli => $waktu) {
                    PatroliRule::updateOrCreate(
                        [
                            'jenis_shift' => 'Malam',
                            'jenis_patroli' => $jenisPatroli
                        ],
                        [
                            'jam_mulai' => $waktu['jam_mulai'],
                            'jam_selesai' => $waktu['jam_selesai']
                        ]
                    );
                }
            }

            return back()->with('success', 'Pengaturan jam patroli berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui pengaturan: ' . $e->getMessage());
        }
    }
}
