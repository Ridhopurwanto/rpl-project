<?php

namespace App\Http\Controllers\Bau;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Shift;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ShiftController extends Controller
{
    public function index($id_pengguna, Request $request)
    {
        $user = User::findOrFail($id_pengguna);
        
        $bulanParam = $request->query('bulan', Carbon::now()->format('Y-m'));
        $bulanTahun = Carbon::createFromFormat('Y-m', $bulanParam)->startOfMonth();
        
        $prevMonth = $bulanTahun->copy()->subMonth()->format('Y-m');
        $nextMonth = $bulanTahun->copy()->addMonth()->format('Y-m');
        
        $startDate = $bulanTahun->copy()->startOfMonth();
        $endDate = $bulanTahun->copy()->endOfMonth();
        
        $shifts = Shift::with('shiftRule')
            ->where('id_pengguna', $id_pengguna)
            ->whereBetween('tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->keyBy('tanggal');
        
        $kalender = [];
        $firstDayOfWeek = $startDate->copy()->dayOfWeek;
        
        for ($i = 0; $i < $firstDayOfWeek; $i++) {
            $kalender[] = null;
        }
        
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $dateStr = $currentDate->format('Y-m-d');
            $shift = $shifts->get($dateStr);
            
            $kalender[] = [
                'tanggal' => $currentDate->day,
                'full_date' => $dateStr,
                'jenis_shift' => $shift && $shift->shiftRule ? $shift->shiftRule->jenis_shift : 'Off'
            ];
            
            $currentDate->addDay();
        }
        
        return view('bau.akun.shift', compact('user', 'bulanTahun', 'kalender', 'prevMonth', 'nextMonth'));
    }
}
