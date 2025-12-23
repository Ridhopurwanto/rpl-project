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
        // 1. Ambil Tanggal - terpisah untuk pagi dan malam
        // Gunakan filled() untuk memastikan parameter tidak kosong
        $tanggalPagi = $request->filled('tanggal_pagi') 
            ? $request->input('tanggal_pagi') 
            : now()->format('Y-m-d');
            
        $tanggalMalam = $request->filled('tanggal_malam') 
            ? $request->input('tanggal_malam') 
            : now()->format('Y-m-d');
        
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
            ->whereDate('tanggal', $tanggalPagi)
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
            ->whereDate('tanggal', $tanggalMalam)
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


        // ---------------------------------------------------------
        // DATA PENDUKUNG (Rules untuk Modal Edit Jam)
        // ---------------------------------------------------------
        // Mengambil semua rules dan mengelompokkan berdasarkan shift (Pagi/Malam)
        // Agar mudah ditampilkan di modal setting jam
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
            // Data Utama
            'dataPatroliPagi' => $dataPatroliPagi,
            'dataPatroliMalam' => $dataPatroliMalam,
            
            // Filter State - terpisah untuk pagi dan malam
            'tanggalPagi' => $tanggalPagi,
            'tanggalMalam' => $tanggalMalam,
            'jenisPatroliTerpilihPagi' => $jenisPatroliTerpilihPagi,
            'jenisPatroliTerpilihMalam' => $jenisPatroliTerpilihMalam,
            
            // Options & Config
            'jenisPatroliOptions' => $jenisPatroliOptions,
            'patroliRules' => $patroliRules,
            'perPagePagi' => $perPagePagi,
            'perPageMalam' => $perPageMalam,
        ]);
    }

    /**
     * Mengupdate data wilayah patroli (dari modal edit).
     */
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

    /**
     * Menghapus data patroli.
     */
    public function destroy(Request $request, $id)
    {
        try {
            $patroli = Patroli::findOrFail($id);
            
            // Hapus foto dari storage jika ada
            if ($patroli->foto) {
                Storage::delete('public/' . $patroli->foto);
            }
            
            $patroli->delete();

            // Ambil parameter filter dari request agar tidak reset
            $params = $request->only([
                'tanggal_pagi', 
                'tanggal_malam', 
                'per_page_pagi', 
                'per_page_malam', 
                'jenis_patroli_pagi', 
                'jenis_patroli_malam'
            ]);

            // Redirect ke route index dengan parameter yang tetap terjaga
            return redirect()->route('komandan.patroli', $params)
                             ->with('success', 'Data patroli berhasil dihapus.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Update aturan jam patroli
     */
    public function updateRules(Request $request)
    {
        try {
            // Proses Shift Pagi
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

            // Proses Shift Malam
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
