@forelse($riwayat_kendaraan as $log)
    <div class="bg-white rounded-lg shadow-md overflow-hidden border-2 border-gray-300 relative">
        {{-- Badge Tipe di Pojok Kanan Atas --}}
        <div class="absolute top-2 right-2 z-10">
            <span class="inline-block @if($log->tipe == 'Roda 4') bg-blue-500 @else bg-yellow-500 @endif text-white text-[10px] font-bold px-2 py-1 rounded-full shadow-md">{{ $log->tipe }}</span>
        </div>
        
        <div class="p-3">
            <div class="w-full">
                {{-- Nomor Plat --}}
                <h4 class="font-bold text-gray-800 text-sm mb-1 uppercase">{{ $log->nopol }}</h4>
                
                {{-- Status Badge --}}
                <div class="mb-2">
                    <span class="inline-block @if($log->keterangan == 'Menginap') bg-red-500 text-white @else bg-blue-500 text-white @endif text-[10px] font-bold px-2 py-1 rounded-full">{{ $log->keterangan }}</span>
                </div>

                {{-- Info Pemilik & Waktu --}}
                <div class="flex items-center gap-3 mb-2">
                    <div class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-blue-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                        </svg>
                        <p class="text-gray-700 font-semibold text-xs">{{ $log->pemilik }}</p>
                    </div>
                    
                    <div class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <p class="text-gray-700 font-semibold text-xs">{{ $log->waktu_keluar->format('H:i') }}</p>
                    </div>
                </div>

                {{-- Lama Parkir --}}
                <div class="text-xs text-gray-500">
                    Lama parkir: <span class="font-semibold text-gray-700">{{ $log->waktu_masuk->diffForHumans($log->waktu_keluar, true) }}</span>
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="bg-white rounded-xl shadow-md p-8 text-center border-2 border-gray-300">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <p class="text-gray-500 font-semibold">Tidak ada riwayat kendaraan pada tanggal ini.</p>
    </div>
@endforelse
