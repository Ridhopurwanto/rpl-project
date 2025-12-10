<?php
use App\Models\User;
use App\Models\Presensi;
use Carbon\Carbon;

echo "User Count: " . User::count() . PHP_EOL;

$start = Carbon::create(2024, 10, 1);
echo "Start Date class: " . get_class($start) . PHP_EOL;
$start->addDay();
echo "Date after addDay: " . $start->toDateString() . PHP_EOL;

if ($start->toDateString() == '2024-10-01') {
    echo "CARBON IS IMMUTABLE! Seeder loop was infinite or broken." . PHP_EOL;
} else {
    echo "Carbon is Mutable." . PHP_EOL;
}

try {
    $p = Presensi::create([
        'id_pengguna' => User::first()->id_pengguna,
         'id_shift' => 1,
        'nama_lengkap' => 'Debug User',
        'waktu' => now(),
        'foto' => 'debug.jpg',
        'status' => 'tepat waktu', // Valid Enum
        'jenis_presensi' => 'Masuk',
        'tanggal' => now(),
        'latitude' => 0,
        'longitude' => 0
    ]);
    echo "Created Presensi ID: " . $p->id_presensi . PHP_EOL;
} catch (\Exception $e) {
    echo "Create failed: " . $e->getMessage() . PHP_EOL;
}
