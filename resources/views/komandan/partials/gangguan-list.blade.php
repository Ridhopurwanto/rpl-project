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
                @if(Auth::user()->peran == 'komandan')
                    <th class="py-3 px-4 text-center">Aksi</th>
                @endif
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
                    @if(Auth::user()->peran == 'komandan')
                        <td class="py-2 px-4">
                            <div class="flex justify-center space-x-3">
                                <button @click="
                                    showEditModal = true; 
                                    editAction = '{{ route('komandan.gangguan.update', $gangguan->id_gangguan) }}';
                                    editWaktu = '{{ $gangguan->waktu_lapor->format('Y-m-d\TH:i') }}';
                                    editLokasi = '{{ $gangguan->lokasi }}';
                                    editKategori = '{{ $gangguan->kategori }}';
                                    editDeskripsi = '{{ $gangguan->deskripsi }}';
                                " class="text-blue-500 hover:text-blue-700" title="Edit">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828zM5 12V7a2 2 0 012-2h2.586l-4 4H5zM3 15a2 2 0 00-2 2v2h16v-2a2 2 0 00-2-2H3z">
                                        </path>
                                    </svg>
                                </button>
                                <button @click.prevent="
                                    showDeleteModal = true; 
                                    deleteAction = '{{ route('komandan.gangguan.destroy', $gangguan->id_gangguan) }}'
                                " class="text-red-500 hover:text-red-700" title="Hapus">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ Auth::user()->peran == 'komandan' ? '7' : '6' }}"
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
                    @if(Auth::user()->peran == 'komandan')
                        <div class="flex gap-1.5">
                            <button @click="
                                    showEditModal = true; 
                                    editAction = '{{ route('komandan.gangguan.update', $gangguan->id_gangguan) }}';
                                    editWaktu = '{{ $gangguan->waktu_lapor->format('Y-m-d\TH:i') }}';
                                    editLokasi = '{{ $gangguan->lokasi }}';
                                    editKategori = '{{ $gangguan->kategori }}';
                                    editDeskripsi = '{{ $gangguan->deskripsi }}';
                                "
                                class="flex-1 bg-blue-500 text-white font-bold py-1.5 rounded text-xs hover:bg-blue-600 transition flex items-center justify-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828zM5 12V7a2 2 0 012-2h2.586l-4 4H5zM3 15a2 2 0 00-2 2v2h16v-2a2 2 0 00-2-2H3z"></path>
                                </svg>
                                Edit
                            </button>
                            <button
                                @click.prevent="showDeleteModal = true; deleteAction = '{{ route('komandan.gangguan.destroy', $gangguan->id_gangguan) }}'"
                                class="flex-1 bg-red-500 text-white font-bold py-1.5 rounded text-xs hover:bg-red-600 transition flex items-center justify-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                Hapus
                            </button>
                        </div>
                    @endif
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
                    class="px-3 py-2 text-sm text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">Previous</span>
            @else
                <a href="{{ $riwayatGangguan->appends(request()->query())->previousPageUrl() }}"
                    class="pagination-link px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Previous</a>
            @endif
            @foreach($riwayatGangguan->getUrlRange(1, $riwayatGangguan->lastPage()) as $page => $url)
                @if($page == $riwayatGangguan->currentPage())
                    <span class="px-3 py-2 text-sm text-white bg-[#1e3a5f] rounded-lg">{{ $page }}</span>
                @else
                    <a href="{{ $riwayatGangguan->appends(request()->query())->url($page) }}"
                        class="pagination-link px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">{{ $page }}</a>
                @endif
            @endforeach
            @if($riwayatGangguan->hasMorePages())
                <a href="{{ $riwayatGangguan->appends(request()->query())->nextPageUrl() }}"
                    class="pagination-link px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Next</a>
            @else
                <span class="px-3 py-2 text-sm text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">Next</span>
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
                <span class="px-2 py-1 text-xs text-gray-400 bg-gray-100 rounded cursor-not-allowed">Prev</span>
            @else
                <a href="{{ $riwayatGangguan->appends(request()->query())->previousPageUrl() }}"
                    class="pagination-link px-2 py-1 text-xs text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Prev</a>
            @endif
            @foreach($riwayatGangguan->getUrlRange(1, $riwayatGangguan->lastPage()) as $page => $url)
                @if($page == $riwayatGangguan->currentPage())
                    <span class="px-2 py-1 text-xs text-white bg-[#1e3a5f] rounded">{{ $page }}</span>
                @else
                    <a href="{{ $riwayatGangguan->appends(request()->query())->url($page) }}"
                        class="pagination-link px-2 py-1 text-xs text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">{{ $page }}</a>
                @endif
            @endforeach
            @if($riwayatGangguan->hasMorePages())
                <a href="{{ $riwayatGangguan->appends(request()->query())->nextPageUrl() }}"
                    class="pagination-link px-2 py-1 text-xs text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Next</a>
            @else
                <span class="px-2 py-1 text-xs text-gray-400 bg-gray-100 rounded cursor-not-allowed">Next</span>
            @endif
        </div>
    </div>
@endif
