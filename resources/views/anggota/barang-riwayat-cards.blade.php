@forelse($riwayat_barang as $barang)
    <div class="bg-white rounded-lg shadow-md overflow-hidden border-2 border-gray-300 relative">
        {{-- Badge Status di Pojok Kanan Atas --}}
        <div class="absolute top-3 right-3 z-10">
            <span class="inline-block bg-green-500 text-white text-[10px] font-bold px-2 py-1 rounded-full shadow-md">SELESAI</span>
        </div>
        
        <div class="p-4">
            <div class="w-full">
                {{-- Nama Barang sebagai judul utama --}}
                <h4 class="font-bold text-gray-800 text-sm mb-3 pr-20">{{ $barang->nama_barang }}</h4>

                {{-- Info Foto & Detail --}}
                <div class="flex gap-4 mb-2">
                    {{-- Foto di Kiri --}}
                    <div class="flex-shrink-0">
                        @if($barang->foto)
                            <div @click="showPhotoModal = true; photoUrl = '{{ Storage::url($barang->foto) }}'" 
                                 class="w-16 h-16 rounded-lg overflow-hidden border-2 border-gray-200 cursor-pointer hover:border-blue-400 transition-colors relative">
                                <img src="{{ Storage::url($barang->foto) }}" 
                                     alt="Foto Barang" 
                                     class="w-full h-full object-cover">
                                @if($barang->foto_penerima)
                                    <div class="absolute bottom-1 right-1 bg-blue-600 text-white text-[8px] font-bold px-1 py-0.5 rounded">
                                        2
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="w-16 h-16 rounded-lg bg-gray-100 flex items-center justify-center border-2 border-gray-200">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif
                    </div>
                    
                    {{-- Info di Kanan --}}
                    <div class="flex-1">
                        <div class="flex items-center gap-1 mb-1">
                            <svg class="w-3.5 h-3.5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <p class="text-gray-700 font-semibold text-xs">
                                @if($barang instanceof \App\Models\BarangTitipan)
                                    {{ $barang->nama_penitip }}
                                @else
                                    {{ $barang->nama_pelapor }}
                                @endif
                            </p>
                        </div>
                        
                        <div class="flex items-center gap-1 mb-1">
                            <svg class="w-3.5 h-3.5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-gray-700 font-semibold text-xs">
                                @if($barang instanceof \App\Models\BarangTitipan)
                                    {{ $barang->waktu_titip->format('d/m/Y H:i') }}
                                @else
                                    {{ $barang->waktu_lapor->format('d/m/Y H:i') }}
                                @endif
                            </p>
                        </div>
                        
                        <div class="text-xs text-gray-500">
                            <span class="font-semibold">Penerima:</span> {{ Str::limit($barang->nama_penerima, 30) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="bg-white rounded-xl shadow-md p-8 text-center border-2 border-gray-300">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
        </div>
        <p class="text-gray-500 font-semibold">Tidak ada riwayat.</p>
    </div>
@endforelse