<?php

namespace App\Http\Controllers;

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
        // Ambil Tanggal (Default Hari Ini)
        $tanggalTerpilih = $request->input('tanggal', now()->format('Y-m-d'));
        
        // Definisikan Opsi Jenis Patroli
        $jenisPatroliOptions = collect([
            'Semua',
            'Patroli 1',
            'Patroli 2',
            'Patroli 3',
            'Patroli 4',
            'Patroli 5',
            'Patroli 6'
        ]);

        // === SHIFT PAGI (jenis_shift = 1) ===
        $jenisPatroliTerpilihPagi = $request->input('jenis_patroli_pagi', 'Semua');
        $perPagePagi = $request->input('per_page_pagi', 10);

        // Query untuk Shift Pagi - LEFT JOIN ke shift dan pengguna
        $queryPagi = Patroli::query()
            ->leftJoin('pengguna', 'patroli.id_pengguna', '=', 'pengguna.id_pengguna')
            ->leftJoin('shift', 'patroli.id_shift', '=', 'shift.id_shift')
            ->select('patroli.*', 'pengguna.nama_lengkap', 'shift.jenis_shift')
            ->whereDate('patroli.tanggal', $tanggalTerpilih)
            ->where('shift.jenis_shift', 1) // 1 = Pagi
            ->orderBy('patroli.waktu_exact', 'asc');

<<<<<<< HEAD
        // Filter jenis patroli jika bukan "Semua"
        if ($jenisPatroliTerpilihPagi !== 'Semua') {
            $queryPagi->where('patroli.jenis_patroli', $jenisPatroliTerpilihPagi);
=======
        // 4. Mulai Query
        // $query = Patroli::query();
        $query = Patroli::query()->with(['claim.rule']);

        // Filter Tanggal
        $query->whereDate('tanggal', $tanggalTerpilih);
        
        // Filter Jenis
        if ($jenisPatroliTerpilih) {
             $query->where('jenis_patroli', $jenisPatroliTerpilih);
>>>>>>> 380e17a61840f0fb2dfa91e6e28519ad7231dd33
        }

        $dataPatroliPagi = $queryPagi->paginate($perPagePagi, ['*'], 'page_pagi');

        // === SHIFT MALAM (jenis_shift = 2) ===
        $jenisPatroliTerpilihMalam = $request->input('jenis_patroli_malam', 'Semua');
        $perPageMalam = $request->input('per_page_malam', 10);

        // Query untuk Shift Malam - LEFT JOIN ke shift dan pengguna
        $queryMalam = Patroli::query()
            ->leftJoin('pengguna', 'patroli.id_pengguna', '=', 'pengguna.id_pengguna')
            ->leftJoin('shift', 'patroli.id_shift', '=', 'shift.id_shift')
            ->select('patroli.*', 'pengguna.nama_lengkap', 'shift.jenis_shift')
            ->whereDate('patroli.tanggal', $tanggalTerpilih)
            ->where('shift.jenis_shift', 2) // 2 = Malam
            ->orderBy('patroli.waktu_exact', 'asc');

        // Filter jenis patroli jika bukan "Semua"
        if ($jenisPatroliTerpilihMalam !== 'Semua') {
            $queryMalam->where('patroli.jenis_patroli', $jenisPatroliTerpilihMalam);
        }

        $dataPatroliMalam = $queryMalam->paginate($perPageMalam, ['*'], 'page_malam');

        // Ambil data Patroli Rules
        $patroliRules = PatroliRule::all()->groupBy('jenis_shift');

        return view('komandan.patroli', [
            'dataPatroliPagi' => $dataPatroliPagi,
            'dataPatroliMalam' => $dataPatroliMalam,
            'tanggalTerpilih' => $tanggalTerpilih,
            'jenisPatroliTerpilihPagi' => $jenisPatroliTerpilihPagi,
            'jenisPatroliTerpilihMalam' => $jenisPatroliTerpilihMalam,
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
    public function destroy($id)
    {
        try {
            $patroli = Patroli::findOrFail($id);
            
            // Hapus foto dari storage jika ada
            if ($patroli->foto) {
                Storage::delete('public/' . $patroli->foto);
            }
            
            $patroli->delete();

            return back()->with('success', 'Data patroli berhasil dihapus.');
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
