<?php

namespace App\Http\Controllers\Komandan;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\PatroliRule;
use Illuminate\Support\Facades\DB;

class PatroliRuleController extends Controller
{
    /**
     * Update atau create patroli rules
     */
    public function updateRules(Request $request)
    {
        $request->validate([
            'shift_pagi.*.jam_mulai' => 'required|date_format:H:i',
            'shift_pagi.*.jam_selesai' => 'required|date_format:H:i',
            'shift_malam.*.jam_mulai' => 'required|date_format:H:i',
            'shift_malam.*.jam_selesai' => 'required|date_format:H:i',
        ]);

        try {
            // --- VALIDASI GAP & OVERLAP ---
            // Kita harus pastikan jam patroli bersambung tanpa jeda.
            
            if ($request->has('shift_pagi')) {
                $errorPagi = $this->validateContiguous($request->shift_pagi, 'Pagi');
                if ($errorPagi) return back()->with('error', $errorPagi);
            }

            if ($request->has('shift_malam')) {
                $errorMalam = $this->validateContiguous($request->shift_malam, 'Malam');
                if ($errorMalam) return back()->with('error', $errorMalam);
            }

            DB::beginTransaction();

            // Update Shift Pagi
            if ($request->has('shift_pagi')) {
                foreach ($request->shift_pagi as $jenisPatroli => $data) {
                    PatroliRule::updateOrCreate(
                        [
                            'jenis_shift' => 'Pagi',
                            'jenis_patroli' => $jenisPatroli,
                        ],
                        [
                            'jam_mulai' => $data['jam_mulai'],
                            'jam_selesai' => $data['jam_selesai'],
                        ]
                    );
                }
            }

            // Update Shift Malam
            if ($request->has('shift_malam')) {
                foreach ($request->shift_malam as $jenisPatroli => $data) {
                    PatroliRule::updateOrCreate(
                        [
                            'jenis_shift' => 'Malam',
                            'jenis_patroli' => $jenisPatroli,
                        ],
                        [
                            'jam_mulai' => $data['jam_mulai'],
                            'jam_selesai' => $data['jam_selesai'],
                        ]
                    );
                }
            }

            DB::commit();
            return back()->with('success', 'Pengaturan jam patroli berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan pengaturan: ' . $e->getMessage());
        }
    }

    /**
     * Memvalidasi apakah jam patroli urut dan bersambung.
     */
    private function validateContiguous($rules, $shiftName)
    {
        // Urutkan berdasarkan kunci (Patroli 1, Patroli 2, dst)
        // Kita asumsikan kuncinya string 'Patroli X'
        uksort($rules, 'strnatcmp');

        $keys = array_keys($rules);
        $count = count($keys);

        for ($i = 0; $i < $count - 1; $i++) {
            $currentPatrol = $keys[$i];
            $nextPatrol = $keys[$i+1];

            $currentData = $rules[$currentPatrol];
            $nextData = $rules[$nextPatrol];

            // Cek apakah Jam Selesai sekarang == Jam Mulai berikutnya
            // Kita bandingkan string jam (HH:mm)
            if ($currentData['jam_selesai'] !== $nextData['jam_mulai']) {
                return "Validasi Gagal ($shiftName): Jam Selesai {$currentPatrol} ({$currentData['jam_selesai']}) tidak sama dengan Jam Mulai {$nextPatrol} ({$nextData['jam_mulai']}). Tidak boleh ada jeda.";
            }
        }

        return null; // Valid
    }
}
