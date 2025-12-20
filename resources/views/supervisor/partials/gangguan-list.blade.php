{{-- TABEL (Desktop) --}}
<div class="hidden md:block overflow-x-auto">
    <table class="w-full min-w-max">
        <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500">
            <tr>
                <th class="py-3 px-4 text-center">No</th>
                <th class="py-3 px-4 text-center">Tanggal</th>
                <th class="py-3 px-4 text-center">Lokasi</th>
                <th class="py-3 px-4 text-center">Kategori</th>
                <th class="py-3 px-4 text-center">Deskripsi</th>
                <th class="py-3 px-4 text-center">Foto</th>

            </tr>
        </thead>
        <tbody class="text-sm divide-y divide-gray-200">
            @forelse($riwayatGangguan as $index => $gangguan)
                <tr>
                    <td class="py-2 px-4 text-center">{{ $riwayatGangguan->firstItem() + $index }}.</td>
                    <td class="py-2 px-4 text-center">{{ $gangguan->waktu_lapor->format('d/m/Y H:i') }}</td>
                    <td class="py-2 px-4 text-center">{{ $gangguan->lokasi }}</td>
                    <td class="py-2 px-4 text-center">{{ $gangguan->kategori }}</td>
                    <td class="py-2 px-4 text-center">{{ $gangguan->deskripsi }}</td>
                    <td class="py-2 px-4 text-center">
                        @if($gangguan->foto)
                            <button
                                @click="showPhotoModal = true; photoUrl = '{{ asset('storage/' . $gangguan->foto) }}'"
                                class="text-blue-500 hover:underline">Buka</button>
                        @else
                            -
                        @endif
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="6"
                        class="py-4 px-4 text-center text-gray-500">
                        Tidak ada data gangguan kamtibmas pada bulan ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- CARD LAYOUT (Mobile) --}}
<div class="md:hidden space-y-2 p-3">
    @forelse($riwayatGangguan as $gangguan)
        <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
            <div class="flex gap-3 p-3">
                {{-- Foto di Sebelah Kiri --}}
                @if($gangguan->foto)
                    <div class="flex-shrink-0">
                        <button
                            @click="showPhotoModal = true; photoUrl = '{{ asset('storage/' . $gangguan->foto) }}'"
                            class="block w-20 h-20 rounded-lg overflow-hidden border-2 border-gray-200 hover:border-blue-500 transition">
                            <img src="{{ asset('storage/' . $gangguan->foto) }}" 
                                    alt="Foto" 
                                    class="w-full h-full object-cover">
                        </button>
                    </div>
                @endif

                {{-- Info di Tengah --}}
                <div class="flex-1 min-w-0">
                    {{-- Lokasi & Kategori Badge --}}
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex-1 min-w-0 pr-2">
                            <h4 class="font-bold text-gray-800 text-sm">{{ $gangguan->lokasi }}</h4>
                            <p class="text-gray-600 text-xs">{{ $gangguan->waktu_lapor->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="inline-block text-[10px] font-bold px-2 py-1 rounded-full bg-red-500 text-white text-center leading-tight max-w-[80px] break-words">
                                {{ $gangguan->kategori }}
                            </span>
                        </div>
                    </div>

                    {{-- Deskripsi --}}
                    <div class="mb-2">
                        <p class="text-gray-700 text-xs">{{ $gangguan->deskripsi }}</p>
                    </div>

                    {{-- Tombol Aksi --}}

                </div>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-lg shadow-sm p-6 text-center">
            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <p class="text-gray-500 text-sm font-semibold">Tidak ada data gangguan kamtibmas pada bulan ini.</p>
        </div>
    @endforelse
</div>

{{-- Pagination Desktop --}}
@if($riwayatGangguan->total() > 0)
    <div class="hidden md:flex justify-between items-center px-6 py-4 border-t border-gray-200">
        <div class="text-sm text-gray-600">Showing {{ $riwayatGangguan->firstItem() ?? 0 }} to
            {{ $riwayatGangguan->lastItem() ?? 0 }} of {{ $riwayatGangguan->total() }} entries</div>
        <div class="flex gap-1">
            @if($riwayatGangguan->onFirstPage())
                <span
                    class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Previous</span>
            @else
                <a href="{{ $riwayatGangguan->appends(request()->query())->previousPageUrl() }}"
                    class="pagination-link px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Previous</a>
            @endif
            @foreach($riwayatGangguan->getUrlRange(1, $riwayatGangguan->lastPage()) as $page => $url)
                @if($page == $riwayatGangguan->currentPage())
                    <span class="px-3 py-1 text-white bg-[#1e3a5f] rounded font-bold">{{ $page }}</span>
                @else
                    <a href="{{ $riwayatGangguan->appends(request()->query())->url($page) }}"
                        class="pagination-link px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">{{ $page }}</a>
                @endif
            @endforeach
            @if($riwayatGangguan->hasMorePages())
                <a href="{{ $riwayatGangguan->appends(request()->query())->nextPageUrl() }}"
                    class="pagination-link px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Next</a>
            @else
                <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Next</span>
            @endif
        </div>
    </div>
@endif

{{-- Pagination Mobile --}}
@if($riwayatGangguan->total() > 0)
    <div class="md:hidden flex justify-between items-center px-3 py-4 border-t border-gray-200">
        <div class="text-xs text-gray-600">
            {{ $riwayatGangguan->firstItem() ?? 0 }}-{{ $riwayatGangguan->lastItem() ?? 0 }} of
            {{ $riwayatGangguan->total() }}</div>
        <div class="flex gap-1">
            @if($riwayatGangguan->onFirstPage())
                <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Prev</span>
            @else
                <a href="{{ $riwayatGangguan->appends(request()->query())->previousPageUrl() }}"
                    class="pagination-link px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Prev</a>
            @endif
            @foreach($riwayatGangguan->getUrlRange(1, $riwayatGangguan->lastPage()) as $page => $url)
                @if($page == $riwayatGangguan->currentPage())
                    <span class="px-3 py-1 text-white bg-[#1e3a5f] rounded font-bold">{{ $page }}</span>
                @else
                    <a href="{{ $riwayatGangguan->appends(request()->query())->url($page) }}"
                        class="pagination-link px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">{{ $page }}</a>
                @endif
            @endforeach
            @if($riwayatGangguan->hasMorePages())
                <a href="{{ $riwayatGangguan->appends(request()->query())->nextPageUrl() }}"
                    class="pagination-link px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Next</a>
            @else
                <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Next</span>
            @endif
        </div>
    </div>
@endif
