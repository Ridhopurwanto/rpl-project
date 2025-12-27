<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PatroliRule;
use Illuminate\Support\Facades\DB;

class PatroliRuleController extends Controller
{
     
    public function updateRules(Request $request)
    {
        $request->validate([
            'shift_pagi.*.jam_mulai' => 'required|date_format:H:i',
            'shift_pagi.*.jam_selesai' => 'required|date_format:H:i',
            'shift_malam.*.jam_mulai' => 'required|date_format:H:i',
            'shift_malam.*.jam_selesai' => 'required|date_format:H:i',
        ]);

        try {
            DB::beginTransaction();

            
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
}
