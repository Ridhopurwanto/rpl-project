<div class="bg-white rounded-lg shadow-md overflow-hidden mb-6" x-data="{ showMasuk: true }">
    <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] p-3 border-b border-[#2a4a6f] cursor-pointer hover:bg-[#2a4a6f] transition"
        @click="showMasuk = !showMasuk">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                </svg>
                <h3 class="font-bold text-white">DAFTAR PRESENSI MASUK</h3>
            </div>
            <svg class="w-5 h-5 text-white transition-transform" :class="{ 'rotate-180': !showMasuk }"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </div>
    </div>

        <div x-show="showMasuk" x-collapse>
        
        {{-- TABEL (Desktop) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full min-w-max table-fixed">
                <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500">
                    <tr>
                        <th class="py-3 px-4 text-center {{ $shiftTerpilih == 'semua' ? 'w-[5%]' : 'w-[8%]' }}">No</th>
                        <th class="py-3 px-4 text-center {{ $shiftTerpilih == 'semua' ? 'w-[25%]' : 'w-[28%]' }}">Nama</th>
                        @if($shiftTerpilih == 'semua')
                            <th class="py-3 px-4 text-center w-[15%]">Jenis Shift</th>
                        @endif
                        <th class="py-3 px-4 text-center {{ $shiftTerpilih == 'semua' ? 'w-[15%]' : 'w-[18%]' }}">Waktu</th>
                        <th class="py-3 px-4 text-center {{ $shiftTerpilih == 'semua' ? 'w-[10%]' : 'w-[13%]' }}">Foto</th>
                        <th class="py-3 px-4 text-center {{ $shiftTerpilih == 'semua' ? 'w-[15%]' : 'w-[18%]' }}">Status</th>
                        <th class="py-3 px-4 text-center w-[15%]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse($dataMasuk as $index => $presensi)
                    <tr>
                        <td class="py-2 px-4">{{ $dataMasuk->firstItem() + $index }}.</td>
                        <td class="py-2 px-4 font-medium">{{ $presensi->nama_lengkap }}</td>
                        @if($shiftTerpilih == 'semua')
                            <td class="py-2 px-4 text-center">
                                @if($presensi->jenis_shift == 1 || $presensi->jenis_shift == 4)
                                    Shift Pagi
                                @elseif($presensi->jenis_shift == 2)
                                    Shift Malam
                                @else
                                    {{ $presensi->jenis_shift }}
                                @endif
                            </td>
                        @endif
                        <td class="py-2 px-4 text-center">{{ $presensi->waktu->format('H:i:s') }}</td>
                        <td class="py-2 px-4 text-center">
                            <button @click="showPhotoModal = true; photoUrl = '{{ asset('storage/' . $presensi->foto) }}'" class="text-blue-500 hover:underline">
                                Buka
                            </button>
                        </td>
                        <td class="py-2 px-4 text-center">
                            @if($presensi->status == 'tepat waktu')
                                <span class="text-green-600 font-semibold">Tepat Waktu</span>
                            @elseif($presensi->status == 'terlambat')
                                <span class="text-red-500 font-semibold">Terlambat</span>
                            @else
                                <span class="text-yellow-500 font-semibold">{{ ucfirst($presensi->status) }}</span>
                            @endif
                        </td>

                            <td class="py-2 px-4">
                                <div class="flex justify-center space-x-3">
                                    <button @click="
                                        showEditModal = true; 
                                        editAction = '{{ route('komandan.presensi.update', $presensi->id_presensi) }}';
                                        editWaktu = '{{ $presensi->waktu->format('Y-m-d\TH:i') }}';
                                        editMin = '{{ $presensi->waktu->format('Y-m-d') }}T00:00';
                                        editMax = '{{ $presensi->waktu->format('Y-m-d') }}T23:59';
                                        editStatus = '{{ $presensi->status }}';
                                        editJenisPresensi = '{{ $presensi->jenis_presensi }}';
                                        editShiftId = '{{ $presensi->jenis_shift }}';
                                    " class="text-blue-500 hover:text-blue-700" title="Edit">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828zM5 12V7a2 2 0 012-2h2.586l-4 4H5zM3 15a2 2 0 00-2 2v2h16v-2a2 2 0 00-2-2H3z"></path></svg>
                                    </button>
                                    <button @click.prevent="showDeleteModal = true; deleteAction = '{{ route('komandan.presensi.destroy', $presensi->id_presensi) }}'" class="text-red-500 hover:text-red-700" title="Hapus">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                    </button>
                                </div>
                            </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-4 px-4 text-center text-gray-500">
                            Tidak ada data presensi masuk pada tanggal ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- CARD LAYOUT (Mobile) --}}
        <div class="md:hidden space-y-2 p-3">
            @forelse($dataMasuk as $index => $presensi)
                <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
                    <div class="flex gap-3 p-3">
                        {{-- Foto di Sebelah Kiri --}}
                        <div class="flex-shrink-0">
                            <button
                                @click="showPhotoModal = true; photoUrl = '{{ asset('storage/' . $presensi->foto) }}'"
                                class="block w-20 h-20 rounded-lg overflow-hidden border-2 border-gray-200 hover:border-blue-500 transition">
                                <img src="{{ asset('storage/' . $presensi->foto) }}" 
                                    alt="Foto" 
                                    class="w-full h-full object-cover">
                            </button>
                        </div>

                        {{-- Info di Sebelah Kanan --}}
                        <div class="flex-1 min-w-0">
                            {{-- Nama --}}
                            <h4 class="font-bold text-gray-800 text-sm mb-1">{{ $presensi->nama_lengkap }}</h4>
                            
                            {{-- Status Badge --}}
                            <div class="mb-2">
                                @if($presensi->status == 'tepat waktu')
                                    <span class="inline-block bg-green-500 text-white text-[10px] font-bold px-2 py-1 rounded-full">TEPAT WAKTU</span>
                                @elseif($presensi->status == 'terlambat')
                                    <span class="inline-block bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded-full">TERLAMBAT</span>
                                @else
                                    <span class="inline-block bg-yellow-500 text-white text-[10px] font-bold px-2 py-1 rounded-full">{{ strtoupper($presensi->status) }}</span>
                                @endif
                            </div>

                            {{-- Info Shift & Waktu (Sejajar) --}}
                            <div class="flex items-center gap-3 mb-2">
                                {{-- Shift dengan icon dinamis --}}
                                @if($shiftTerpilih == 'semua')
                                    <div class="flex items-center gap-1">
                                        @if($presensi->jenis_shift == 1 || $presensi->jenis_shift == 4)
                                            {{-- Icon Matahari untuk Shift Pagi --}}
                                            <svg class="w-3.5 h-3.5 text-amber-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                                            </svg>
                                        @elseif($presensi->jenis_shift == 2)
                                            {{-- Icon Bulan untuk Shift Malam --}}
                                            <svg class="w-3.5 h-3.5 text-blue-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                                            </svg>
                                        @else
                                            {{-- Default icon jika shift lain --}}
                                            <svg class="w-3.5 h-3.5 text-gray-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                                            </svg>
                                        @endif
                                        <p class="text-gray-700 font-semibold text-xs">
                                            @if($presensi->jenis_shift == 1 || $presensi->jenis_shift == 4)
                                                Shift Pagi
                                            @elseif($presensi->jenis_shift == 2)
                                                Shift Malam
                                            @else
                                                {{ $presensi->jenis_shift }}
                                            @endif
                                        </p>
                                    </div>
                                @endif
                                
                                {{-- Waktu --}}
                                <div class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="text-gray-700 font-semibold text-xs">{{ $presensi->waktu->format('H:i:s') }}</p>
                                </div>
                            </div>

                            {{-- Tombol Aksi --}}
                            <div class="flex gap-1.5">
                                <button @click="
                                        showEditModal = true; 
                                            editAction = '{{ route('komandan.presensi.update', $presensi->id_presensi) }}';
                                            editWaktu = '{{ $presensi->waktu->format('Y-m-d\TH:i') }}';
                                            editMin = '{{ $presensi->waktu->format('Y-m-d') }}T00:00';
                                            editMax = '{{ $presensi->waktu->format('Y-m-d') }}T23:59';
                                            editStatus = '{{ $presensi->status }}';
                                            editJenisPresensi = '{{ $presensi->jenis_presensi }}';
                                            editShiftId = '{{ $presensi->jenis_shift }}';
                                    "
                                    class="flex-1 bg-blue-500 text-white font-bold py-1.5 rounded text-xs hover:bg-blue-600 transition flex items-center justify-center gap-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828zM5 12V7a2 2 0 012-2h2.586l-4 4H5zM3 15a2 2 0 00-2 2v2h16v-2a2 2 0 00-2-2H3z"></path>
                                    </svg>
                                    Edit
                                </button>
                                <button
                                    @click.prevent="showDeleteModal = true; deleteAction = '{{ route('komandan.presensi.destroy', $presensi->id_presensi) }}'"
                                    class="flex-1 bg-red-500 text-white font-bold py-1.5 rounded text-xs hover:bg-red-600 transition flex items-center justify-center gap-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    Hapus
                                </button>
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
                    <p class="text-gray-500 text-sm font-semibold">Tidak ada data presensi masuk pada tanggal ini.</p>
                </div>
            @endforelse
        </div>
        
        {{-- Pagination Masuk --}}
        @if($dataMasuk->total() > 0)
            <div class="flex justify-between items-center px-6 py-4 border-t border-gray-200">
                <div class="text-sm text-gray-600">
                    Showing {{ $dataMasuk->firstItem() ?? 0 }} to {{ $dataMasuk->lastItem() ?? 0 }} of {{ $dataMasuk->total() }} entries
                </div>
                <div class="flex items-center gap-1">
                    {{-- Previous Page Link --}}
                    @if ($dataMasuk->onFirstPage())
                        <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Previous</span>
                    @else
                        <a href="{{ $dataMasuk->appends(request()->query())->previousPageUrl() }}" 
                           class="pagination-link px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50"
                           data-target="masuk">Previous</a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($dataMasuk->links()->elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span class="px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded">{{ $element }}</span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $dataMasuk->currentPage())
                                    <span class="px-3 py-1 text-white bg-[#1e3a5f] rounded font-bold">{{ $page }}</span>
                                @else
                                    <a href="{{ $dataMasuk->appends(request()->query())->url($page) }}" 
                                       class="pagination-link px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50"
                                       data-target="masuk">{{ $page }}</a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($dataMasuk->hasMorePages())
                        <a href="{{ $dataMasuk->appends(request()->query())->nextPageUrl() }}" 
                           class="pagination-link px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50"
                           data-target="masuk">Next</a>
                    @else
                        <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Next</span>
                    @endif
                </div>
            </div>
        @endif
        </div>
</div>
