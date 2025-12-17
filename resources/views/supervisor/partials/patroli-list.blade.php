{{-- TABEL (Desktop & Tablet) --}}
<div class="hidden md:block overflow-x-auto">
    <table class="w-full min-w-max table-fixed">
        <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500">
            <tr>
                <th class="py-3 px-4 text-center w-[5%]">No</th>
                <th class="py-3 px-4 text-center w-[25%]">Nama</th>
                <th class="py-3 px-4 text-center w-[20%]">Jenis Patroli</th>
                <th class="py-3 px-4 text-center w-[15%]">Waktu</th>
                <th class="py-3 px-4 text-center w-[20%]">Wilayah</th>
                <th class="py-3 px-4 text-center w-[5%]">Foto</th>

            </tr>
        </thead>
        <tbody class="text-sm divide-y divide-gray-200">
            @forelse($data as $index => $item)
            <tr>
                <td class="py-2 px-4 text-center">{{ $data->firstItem() + $index }}.</td>
                <td class="py-2 px-4 font-medium text-center">{{ $item->nama_lengkap }}</td>
                <td class="py-2 px-4 text-center">{{ $item->jenis_patroli }}</td>
                <td class="py-2 px-4 text-center">{{ $item->waktu_exact->format('H:i:s') }}</td>
                <td class="py-2 px-4 text-center">{{ $item->wilayah }}</td>
                <td class="py-2 px-4 text-center">
                    <button @click="showPhotoModal = true; photoUrl = '{{ asset('storage/' . $item->foto) }}'" class="text-blue-500 hover:underline">
                        Buka
                    </button>
                </td>

            </tr>
            @empty
            <tr>
                <td colspan="6" class="py-4 px-4 text-center text-gray-500">
                    Tidak ada data patroli shift {{ $shift }} pada tanggal ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>


{{-- CARD LAYOUT (Mobile) --}}
<div class="md:hidden space-y-2 p-3">
    @forelse($data as $index => $item)
        <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200 relative">
            <div class="flex gap-3 p-3">
                {{-- Foto di Sebelah Kiri --}}
                <div class="flex-shrink-0">
                    <button
                        @click="showPhotoModal = true; photoUrl = '{{ asset('storage/' . $item->foto) }}'"
                        class="block w-20 h-20 rounded-lg overflow-hidden border-2 border-gray-200 hover:border-blue-500 transition">
                        <img src="{{ asset('storage/' . $item->foto) }}" 
                                alt="Foto" 
                                class="w-full h-full object-cover">
                    </button>
                </div>

                {{-- Info di Sebelah Kanan --}}
                <div class="flex-1 min-w-0">
                    {{-- Jenis Patroli Badge --}}
                    <div class="mb-1">
                        <span class="inline-block {{ $shift == 'pagi' ? 'bg-amber-500' : 'bg-blue-500' }} text-white text-[10px] font-bold px-2 py-1 rounded-full">{{ $item->jenis_patroli }}</span>
                    </div>
                    
                    {{-- Nama --}}
                    <h4 class="font-bold text-gray-800 text-sm mb-2">{{ $item->nama_lengkap }}</h4>

                    {{-- Info Wilayah & Waktu (Sejajar) --}}
                    <div class="flex items-center gap-3 mb-2">
                        {{-- Wilayah --}}
                        <div class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 {{ $shift == 'pagi' ? 'text-amber-600' : 'text-blue-600' }} flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <p class="text-gray-700 font-semibold text-xs">{{ $item->wilayah }}</p>
                        </div>
                        
                        {{-- Waktu --}}
                        <div class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 {{ $shift == 'pagi' ? 'text-amber-600' : 'text-blue-600' }} flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-gray-700 font-semibold text-xs">{{ $item->waktu_exact->format('H:i:s') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-lg shadow-sm p-6 text-center">
            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <p class="text-gray-500 text-sm font-semibold">Tidak ada data patroli shift {{ $shift }} pada tanggal ini.</p>
        </div>
    @endforelse
</div>

{{-- Pagination --}}
@if($data->total() > 0)
    <div class="flex justify-between items-center px-6 py-4 border-t border-gray-200">
        <div class="text-sm text-gray-600">
            Showing {{ $data->firstItem() ?? 0 }} to {{ $data->lastItem() ?? 0 }} of {{ $data->total() }} entries
        </div>
        <div class="flex items-center gap-1">
            @if ($data->onFirstPage())
                <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Previous</span>
            @else
                <a href="{{ $data->appends(request()->query())->previousPageUrl() }}" class="pagination-link px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Previous</a>
            @endif
            @foreach(range(1, $data->lastPage()) as $page)
                @if($page == $data->currentPage())
                    <span class="px-3 py-1 text-white bg-[#1e3a5f] rounded font-bold">{{ $page }}</span>
                @else
                    <a href="{{ $data->appends(request()->query())->url($page) }}" class="pagination-link px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">{{ $page }}</a>
                @endif
            @endforeach
            @if ($data->hasMorePages())
                <a href="{{ $data->appends(request()->query())->nextPageUrl() }}" class="pagination-link px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Next</a>
            @else
                <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Next</span>
            @endif
        </div>
    </div>
@endif
