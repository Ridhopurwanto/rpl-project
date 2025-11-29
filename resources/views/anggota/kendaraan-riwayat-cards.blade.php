@forelse($riwayat_kendaraan as $log)
    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
        <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] px-4 py-2.5 flex justify-between items-center">
            <div>
                <p class="text-xs text-blue-200 font-semibold uppercase">Nomor Kendaraan</p>
                <p class="text-white font-bold text-base uppercase">{{ $log->nopol }}</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase @if($log->keterangan == 'Menginap') bg-red-500 text-white @else bg-blue-100 text-blue-700 @endif">
                {{ $log->keterangan }}
            </span>
        </div>

        <div class="p-4 space-y-3">
            <div class="flex items-center gap-3 pb-3 border-b border-gray-100">
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-[10px] text-gray-500 font-semibold uppercase">Pemilik</p>
                    <p class="text-gray-800 font-bold text-base">{{ $log->pemilik }}</p>
                </div>
                <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
                    {{ $log->tipe }}
                </span>
            </div>

            <div class="grid grid-cols-2 gap-3 text-sm">
                <div>
                    <p class="text-gray-500 text-xs uppercase mb-1">Lama Parkir</p>
                    <p class="text-gray-800 font-semibold">{{ $log->waktu_masuk->diffForHumans($log->waktu_keluar, true) }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs uppercase mb-1">Waktu Keluar</p>
                    <p class="text-gray-800 font-semibold">{{ $log->waktu_keluar->format('H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="bg-white rounded-xl shadow-md p-8 text-center">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <p class="text-gray-500 font-semibold">Tidak ada riwayat kendaraan pada tanggal ini.</p>
    </div>
@endforelse
