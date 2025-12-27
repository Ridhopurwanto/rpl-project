<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KendaraanSeeder extends Seeder
{
     
    public function run(): void
    {
        
        
        
        

        $data = [
            ['nomor_plat' => 'B 1087 TQH', 'pemilik' => 'DIREKTUR', 'tipe' => 'Roda 4'],
            ['nomor_plat' => 'B 1397 TQO', 'pemilik' => 'WADIR 1', 'tipe' => 'Roda 4'],
            ['nomor_plat' => 'B 1386 TQO', 'pemilik' => 'WADIR 2', 'tipe' => 'Roda 4'],
            ['nomor_plat' => 'B 1399 TQO', 'pemilik' => 'WADIR 3', 'tipe' => 'Roda 4'],
            ['nomor_plat' => 'B 1603 TQN', 'pemilik' => 'KABAG BAAU', 'tipe' => 'Roda 4'],
            ['nomor_plat' => 'B 1207 TQN', 'pemilik' => 'KABAG BAAK', 'tipe' => 'Roda 4'],
            ['nomor_plat' => 'B 1430 LQ', 'pemilik' => 'IBNU', 'tipe' => 'Roda 4'],
            ['nomor_plat' => 'B 1210 TQN', 'pemilik' => 'ASKA', 'tipe' => 'Roda 4'],
            ['nomor_plat' => 'B 7358 TPA', 'pemilik' => 'OPS', 'tipe' => 'Roda 4'],
            ['nomor_plat' => 'B 7360 TPA', 'pemilik' => 'OPS', 'tipe' => 'Roda 4'],
            ['nomor_plat' => 'B 1051 PQH', 'pemilik' => 'BAMBANG', 'tipe' => 'Roda 4'],
            ['nomor_plat' => 'F 1228 ABU', 'pemilik' => 'NASRUDIN', 'tipe' => 'Roda 4'],
            ['nomor_plat' => 'F 1224 ABH', 'pemilik' => 'SHILFI', 'tipe' => 'Roda 4'],
            ['nomor_plat' => 'B 1457 TKU', 'pemilik' => 'TIMBANG', 'tipe' => 'Roda 4'],
            ['nomor_plat' => 'B 1334 KFN', 'pemilik' => 'MAYA', 'tipe' => 'Roda 4'],
            ['nomor_plat' => 'B 2394 KBG', 'pemilik' => 'RINI SILFI', 'tipe' => 'Roda 4'],
            ['nomor_plat' => 'B 2351 TKR', 'pemilik' => 'NURSETO', 'tipe' => 'Roda 4'],
            ['nomor_plat' => 'B 1210 PRB', 'pemilik' => 'MADE', 'tipe' => 'Roda 4'],
            ['nomor_plat' => 'B 1316 KRR', 'pemilik' => 'KANTIN', 'tipe' => 'Roda 4'],
            ['nomor_plat' => 'B 1099 SMN', 'pemilik' => 'DWI BAGUS', 'tipe' => 'Roda 4'],
            ['nomor_plat' => 'B 2029 UI', 'pemilik' => 'SRI KOPERASI', 'tipe' => 'Roda 4'],
            ['nomor_plat' => 'B 2930 TOM', 'pemilik' => 'SOFYAN', 'tipe' => 'Roda 4'],
            ['nomor_plat' => 'B 2225 TGV', 'pemilik' => 'BONNY', 'tipe' => 'Roda 4'],
            ['nomor_plat' => 'F 1403 JI', 'pemilik' => 'BAMBANG', 'tipe' => 'Roda 4'],
            ['nomor_plat' => 'B 2930 TYF', 'pemilik' => 'ASKA', 'tipe' => 'Roda 4'],
            ['nomor_plat' => 'B 8521 XB', 'pemilik' => 'TRIA. M', 'tipe' => 'Roda 4'],
            ['nomor_plat' => 'K 1422 KN', 'pemilik' => 'CLAUDIA', 'tipe' => 'Roda 4'],
            ['nomor_plat' => 'B 1986 NB', 'pemilik' => 'FITRI. K', 'tipe' => 'Roda 4'],
            ['nomor_plat' => 'B 2912 SKH', 'pemilik' => 'RANI NURAINI', 'tipe' => 'Roda 4'],
            ['nomor_plat' => 'BE 1676 UX', 'pemilik' => 'ARIFIN', 'tipe' => 'Roda 4'],
            ['nomor_plat' => 'B 2735 FVJ', 'pemilik' => 'FAISAL', 'tipe' => 'Roda 4'],
            ['nomor_plat' => 'T 1185 TM', 'pemilik' => 'KADIR', 'tipe' => 'Roda 4'],
            ['nomor_plat' => 'B 2415 KKY', 'pemilik' => 'LUCI', 'tipe' => 'Roda 4'],
            ['nomor_plat' => 'B 1381 EKV', 'pemilik' => 'DOKI', 'tipe' => 'Roda 4'],
            ['nomor_plat' => 'B 1106 EMB', 'pemilik' => 'DOKI', 'tipe' => 'Roda 4'],
            ['nomor_plat' => 'B 2391 KYD', 'pemilik' => 'MAYA', 'tipe' => 'Roda 4'],
            ['nomor_plat' => 'B 4663 TXY', 'pemilik' => 'WAHYUDIN', 'tipe' => 'Roda 2'],
            ['nomor_plat' => 'B 3688 SEA', 'pemilik' => 'LAMBANG', 'tipe' => 'Roda 2'],
            ['nomor_plat' => 'F 5887 FGK', 'pemilik' => 'HERI (CS)', 'tipe' => 'Roda 2'],
            ['nomor_plat' => 'B 4679 TMB', 'pemilik' => 'LIA (KANTIN)', 'tipe' => 'Roda 2'],
            ['nomor_plat' => 'B 5058 KID', 'pemilik' => 'ARIS (KANTIN)', 'tipe' => 'Roda 2'],
            ['nomor_plat' => 'B 5597 KDM', 'pemilik' => 'ANDRI (CS)', 'tipe' => 'Roda 2'],
            ['nomor_plat' => 'B 4773 SBR', 'pemilik' => 'SUKIM', 'tipe' => 'Roda 2'],
            ['nomor_plat' => 'B 6993 WJI', 'pemilik' => 'BONNY', 'tipe' => 'Roda 2'],
            ['nomor_plat' => 'B 4046 TYG', 'pemilik' => 'ROBERT', 'tipe' => 'Roda 2'],
            ['nomor_plat' => 'B 4042 SRR', 'pemilik' => 'SAMSI (CS)', 'tipe' => 'Roda 2'],
            ['nomor_plat' => 'B 3818 TYZ', 'pemilik' => 'SUGIARTO', 'tipe' => 'Roda 2'],
            ['nomor_plat' => 'B 6403 UKT', 'pemilik' => 'JAYADI (CS)', 'tipe' => 'Roda 2'],
            ['nomor_plat' => 'B 4204 SLY', 'pemilik' => 'PRIYONO', 'tipe' => 'Roda 2'],
            ['nomor_plat' => 'B 6727 BMG', 'pemilik' => 'PRIYONO', 'tipe' => 'Roda 2'],
            ['nomor_plat' => 'R 3771 CW', 'pemilik' => 'MADIN', 'tipe' => 'Roda 2'],
            ['nomor_plat' => 'H 3712 BBG', 'pemilik' => 'NOVITA', 'tipe' => 'Roda 2'],
            ['nomor_plat' => 'B 3851 EDC', 'pemilik' => 'NURUL', 'tipe' => 'Roda 2'],
            ['nomor_plat' => 'B 3209 PDA', 'pemilik' => 'BETRAND', 'tipe' => 'Roda 2'],
            ['nomor_plat' => 'B 5223 FZU', 'pemilik' => 'JUNIARTI', 'tipe' => 'Roda 2'],
            ['nomor_plat' => 'B 3381 TPO', 'pemilik' => 'FITRI. K', 'tipe' => 'Roda 2'],
            ['nomor_plat' => 'B 6054 WUU', 'pemilik' => 'RIKKY', 'tipe' => 'Roda 2'],
            ['nomor_plat' => 'B 4786 TCV', 'pemilik' => 'ARIE', 'tipe' => 'Roda 2'],
            ['nomor_plat' => 'B 5832 TWI', 'pemilik' => 'IBNU', 'tipe' => 'Roda 2'],
            ['nomor_plat' => 'G 6916 JF', 'pemilik' => 'FITRI CL', 'tipe' => 'Roda 2'],
            ['nomor_plat' => 'B 4563 TRS', 'pemilik' => 'GAMA', 'tipe' => 'Roda 2'],
            ['nomor_plat' => 'B 3134 TSU', 'pemilik' => 'IBU (KANTIN) YAYIK', 'tipe' => 'Roda 2'],
            ['nomor_plat' => 'AA 2272 IM', 'pemilik' => 'CAHYO', 'tipe' => 'Roda 2'],
            ['nomor_plat' => 'B 5763 TNK', 'pemilik' => 'NOVA ARIANI', 'tipe' => 'Roda 2'],
            ['nomor_plat' => 'B 5121 TSS', 'pemilik' => 'ALBERT (CS)', 'tipe' => 'Roda 2'],
            ['nomor_plat' => 'B 5437 KJO', 'pemilik' => 'CLAUDIA', 'tipe' => 'Roda 2'],
            ['nomor_plat' => 'B 5940 TEW', 'pemilik' => 'FARAH SINTA', 'tipe' => 'Roda 2'],
            ['nomor_plat' => 'AB 6898 WA', 'pemilik' => 'TENTY', 'tipe' => 'Roda 2'],
            ['nomor_plat' => 'B 6056 TWO', 'pemilik' => 'SALMAN (CS)', 'tipe' => 'Roda 2'],
            ['nomor_plat' => 'B 4046 SFM', 'pemilik' => 'FARID', 'tipe' => 'Roda 2'],
            ['nomor_plat' => 'B 3759 PMN', 'pemilik' => 'RIVA\'I (CS)', 'tipe' => 'Roda 2'],
            ['nomor_plat' => 'B 4969 SHW', 'pemilik' => 'YUNITA (CS)', 'tipe' => 'Roda 2'],
            ['nomor_plat' => 'B 4944 JJ', 'pemilik' => 'LAMBANG', 'tipe' => 'Roda 2'],
            ['nomor_plat' => 'B 6188 KMK', 'pemilik' => 'AGUNG', 'tipe' => 'Roda 2'],
            ['nomor_plat' => 'F 5032 RF', 'pemilik' => 'DWI BAGUS', 'tipe' => 'Roda 2'],
            ['nomor_plat' => 'B 5622 KAW', 'pemilik' => 'ANDRI (CS)', 'tipe' => 'Roda 2'],
            ['nomor_plat' => 'B 3590 TRC', 'pemilik' => 'SOFYAN', 'tipe' => 'Roda 2'],
            ['nomor_plat' => 'B 5751 TBB', 'pemilik' => 'BUDIANDRA', 'tipe' => 'Roda 2'],
            ['nomor_plat' => 'B 4088 SFA', 'pemilik' => 'DOSEN LUAR', 'tipe' => 'Roda 2'],
            ['nomor_plat' => 'B 3522 KCZ', 'pemilik' => 'DOSEN LUAR', 'tipe' => 'Roda 2'],
            ['nomor_plat' => 'B 3332 TOG', 'pemilik' => 'DOSEN LUAR', 'tipe' => 'Roda 2'],
            ['nomor_plat' => 'BK 6360 ALB', 'pemilik' => 'DOSEN LUAR', 'tipe' => 'Roda 2'],
        ];

        
        $now = now();
        $dataWithTime = array_map(function ($item) use ($now) {
            return array_merge($item, [
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }, $data);

        DB::table('kendaraan')->insert($dataWithTime);
    }
}