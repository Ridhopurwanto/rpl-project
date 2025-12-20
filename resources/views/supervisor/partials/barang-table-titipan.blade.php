{{-- TABEL (Desktop) --}}
<div class="hidden md:block overflow-x-auto">
    <table class="w-full min-w-max">
        <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500">
            <tr>
                <th class="py-3 px-4 text-center">No</th>
                <th class="py-3 px-4 text-center">Foto</th>
                <th class="py-3 px-4 text-center">Nama Barang</th>
                <th class="py-3 px-4 text-center">Penitip</th>
                <th class="py-3 px-4 text-center">Penerima</th>
                <th class="py-3 px-4 text-center">Catatan</th>
                <th class="py-3 px-4 text-center">Status</th>
            </tr>
        </thead>
        <tbody class="text-sm divide-y divide-gray-200">
            @forelse($barangTitipan as $index => $barang)
                <tr>
                    <td class="py-2 px-4">{{ $barangTitipan->firstItem() + $index }}.</td>
                    <td class="py-2 px-4 text-center">
                        @if($barang->foto)
                            <button @click="showPhotoModal = true; photos = ['{{ asset('storage/' . $barang->foto) }}'@if($barang->foto_penerima), '{{ asset('storage/' . $barang->foto_penerima) }}'@endif]; currentPhotoIndex = 0" class="text-blue-500 hover:underline">Buka</button>
                        @else
                            -
                        @endif
                    </td>
                    <td class="py-2 px-4 font-medium">{{ $barang->nama_barang }}</td>
                    <td class="py-2 px-4">{{ $barang->nama_penitip }}</td>
                    <td class="py-2 px-4">{{ $barang->tujuan }}</td>
                    <td class="py-2 px-4">{{ $barang->catatan }}</td>
                    <td class="py-2 px-4 text-center">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $barang->status == 'belum selesai' ? 'bg-red-200 text-yellow-800' : 'bg-green-200 text-green-800' }}">{{ $barang->status }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="py-4 px-4 text-center text-gray-500">Tidak ada data barang titipan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- CARD LAYOUT (Mobile) --}}
<div class="md:hidden space-y-2 p-3">
    @forelse($barangTitipan as $barang)
        <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200 relative">
            <span class="absolute top-2 right-2 {{ $barang->status == 'belum selesai' ? 'bg-yellow-500' : 'bg-green-500' }} text-white text-[10px] font-bold px-2 py-1 rounded-full z-10">{{ strtoupper($barang->status) }}</span>
            <div class="p-3">
                <div class="flex gap-3 mb-3">
                    <div class="flex-shrink-0">
                        @if($barang->foto)
                            <button @click="showPhotoModal = true; photos = ['{{ asset('storage/' . $barang->foto) }}'@if($barang->foto_penerima), '{{ asset('storage/' . $barang->foto_penerima) }}'@endif]; currentPhotoIndex = 0" class="block w-20 h-20 rounded-lg overflow-hidden border-2 border-gray-200 hover:border-blue-500 transition">
                                <img src="{{ asset('storage/' . $barang->foto) }}" alt="Foto Barang" class="w-full h-full object-cover">
                            </button>
                        @else
                            <div class="w-20 h-20 rounded-lg border-2 border-gray-200 bg-gray-100 flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-bold text-gray-800 text-sm mb-2">{{ $barang->nama_barang }}</h4>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <p class="text-gray-700 font-semibold text-xs">{{ $barang->nama_penitip }}</p>
                            </div>
                            <div class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <p class="text-gray-700 font-semibold text-xs">{{ $barang->tujuan }}</p>
                            </div>
                        </div>
                        <p class="text-xs text-gray-600">{{ $barang->catatan }}</p>
                    </div>
                </div>
                <div class="pt-3 border-t border-gray-100">
                    <div class="grid grid-cols-2 gap-4 text-xs text-gray-600">
                        <div>
                            <span class="font-semibold">Masuk:</span>
                            <span>{{ $barang->waktu_titip->format('d/m/Y H:i') }}</span>
                        </div>
                        @if($barang->waktu_selesai)
                            <div>
                                <span class="font-semibold">Selesai:</span>
                                <span>{{ $barang->waktu_selesai->format('d/m/Y H:i') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-lg shadow-sm p-6 text-center">
            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            </div>
            <p class="text-gray-500 text-sm font-semibold">Tidak ada data barang titipan.</p>
        </div>
    @endforelse
</div>

{{-- Pagination Desktop --}}
<div class="hidden md:flex justify-between items-center px-6 py-4 border-t border-gray-200">
    <div class="text-sm text-gray-600">Showing {{ $barangTitipan->firstItem() ?? 0 }} to {{ $barangTitipan->lastItem() ?? 0 }} of {{ $barangTitipan->total() }} entries</div>
    <div class="flex gap-1">
        @if($barangTitipan->onFirstPage())
            <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Previous</span>
        @else
            <a href="{{ $barangTitipan->appends(request()->except('page_titipan'))->previousPageUrl() }}" class="pagination-link px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Previous</a>
        @endif
        @foreach($barangTitipan->getUrlRange(1, $barangTitipan->lastPage()) as $page => $url)
            @if($page == $barangTitipan->currentPage())
                <span class="px-3 py-1 text-white bg-[#1e3a5f] rounded font-bold">{{ $page }}</span>
            @else
                <a href="{{ $barangTitipan->appends(request()->except('page_titipan'))->url($page) }}" class="pagination-link px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">{{ $page }}</a>
            @endif
        @endforeach
        @if($barangTitipan->hasMorePages())
            <a href="{{ $barangTitipan->appends(request()->except('page_titipan'))->nextPageUrl() }}" class="pagination-link px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Next</a>
        @else
            <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Next</span>
        @endif
    </div>
</div>

{{-- Pagination Mobile --}}
<div class="md:hidden flex justify-between items-center px-3 py-4 border-t border-gray-200">
    <div class="text-xs text-gray-600">{{ $barangTitipan->firstItem() ?? 0 }}-{{ $barangTitipan->lastItem() ?? 0 }} of {{ $barangTitipan->total() }}</div>
    <div class="flex gap-1">
        @if($barangTitipan->onFirstPage())
            <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Prev</span>
        @else
            <a href="{{ $barangTitipan->appends(request()->except('page_titipan'))->previousPageUrl() }}" class="pagination-link px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Prev</a>
        @endif
        @foreach($barangTitipan->getUrlRange(1, $barangTitipan->lastPage()) as $page => $url)
            @if($page == $barangTitipan->currentPage())
                <span class="px-3 py-1 text-white bg-[#1e3a5f] rounded font-bold">{{ $page }}</span>
            @else
                <a href="{{ $barangTitipan->appends(request()->except('page_titipan'))->url($page) }}" class="pagination-link px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">{{ $page }}</a>
            @endif
        @endforeach
        @if($barangTitipan->hasMorePages())
            <a href="{{ $barangTitipan->appends(request()->except('page_titipan'))->nextPageUrl() }}" class="pagination-link px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Next</a>
        @else
            <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Next</span>
        @endif
    </div>
</div>
