@forelse($riwayat_barang as $barang)
    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
        {{-- Header Card --}}
        <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] px-4 py-2.5 flex justify-between items-center">
            <div>
                <p class="text-xs text-blue-200 font-semibold uppercase">Nama Barang</p>
                <p class="text-white font-bold text-base">{{ $barang->nama_barang }}</p>
            </div>
            <span class="bg-gray-200 text-gray-700 text-xs font-bold px-3 py-1 rounded-full">
                SELESAI
            </span>
        </div>

        {{-- Body Card --}}
        <div class="p-4 flex gap-4">
            {{-- Foto Barang --}}
            <div class="flex-shrink-0">
                @if($barang->foto)
                    <div @click="photoModalOpen = true; photos = ['{{ Storage::url($barang->foto) }}'@if($barang->foto_penerima), '{{ Storage::url($barang->foto_penerima) }}'@endif]; currentPhotoIndex = 0" 
                         class="w-24 h-24 rounded-lg overflow-hidden border-2 border-gray-200 cursor-pointer hover:border-blue-400 transition-colors relative">
                        <img src="{{ Storage::url($barang->foto) }}" alt="Foto Barang" class="w-full h-full object-cover">
                        @if($barang->foto_penerima)
                            <div class="absolute bottom-1 right-1 bg-blue-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded">
                                1
                            </div>
                        @endif
                    </div>
                @else
                    <div class="w-24 h-24 rounded-lg bg-gray-100 flex items-center justify-center border-2 border-gray-200">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                @endif
            </div>

            {{-- Info Barang --}}
            <div class="flex-1 flex flex-col justify-center space-y-2">
                <div>
                    <p class="text-xs text-gray-500 font-semibold uppercase">
                        @if($barang instanceof \App\Models\BarangTitipan)
                            Penitip
                        @else
                            Pelapor
                        @endif
                    </p>
                    <p class="text-gray-800 font-semibold">
                        @if($barang instanceof \App\Models\BarangTitipan)
                            {{ $barang->nama_penitip }}
                        @else
                            {{ $barang->nama_pelapor }}
                        @endif
                    </p>
                </div>

                <div>
                    <p class="text-xs text-gray-500 font-semibold uppercase">Penerima</p>
                    <p class="text-gray-800 font-semibold">{{ $barang->nama_penerima }}</p>
                </div>

                <div>
                    <p class="text-xs text-gray-500 font-semibold uppercase">
                        @if($barang instanceof \App\Models\BarangTitipan)
                            Tujuan
                        @else
                            Lokasi
                        @endif
                    </p>
                    <p class="text-gray-800 font-semibold text-sm">
                        @if($barang instanceof \App\Models\BarangTitipan)
                            {{ $barang->tujuan }}
                        @else
                            {{ $barang->lokasi_penemuan }}
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="bg-white rounded-xl shadow-md p-8 text-center">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
        </div>
        <p class="text-gray-500 font-semibold">Tidak ada riwayat.</p>
    </div>
@endforelse