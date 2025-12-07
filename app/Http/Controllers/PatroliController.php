<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patroli; 
use App\Models\PatroliRule;  // ← TAMBAHKAN INI
use Illuminate\Support\Facades\Storage;

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

        // ===== TAMBAHKAN INI: AMBIL DATA PATROLI RULES ===== 
        $patroliRules = PatroliRule::all()->groupBy('jenis_shift');

        return view('komandan.patroli', [
            'dataPatroli' => $dataPatroli,
            'tanggalTerpilih' => $tanggalTerpilih,
            'jenisPatroliTerpilih' => $jenisPatroliTerpilih,
            'jenisPatroliOptions' => $jenisPatroliOptions,
            'patroliRules' => $patroliRules,  // ← TAMBAHKAN INI
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
    public function destroy($id)
    {
        try {
            $patroli = Patroli::findOrFail($id);
            
            if ($patroli->foto) {
                Storage::delete('public/' . $patroli->foto);
            }
            
            $patroli->delete();

            return back()->with('success', 'Data patroli berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
