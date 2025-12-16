        {{-- TABEL (Desktop) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full min-w-max table-fixed">
                <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500">
                    <tr>
                        <th class="py-3 px-4 text-center w-[6%]">No</th>
                        <th class="py-3 px-4 text-center w-[22%]">Nama</th>
                        <th class="py-3 px-4 text-center w-[20%]">Instansi</th>
                        <th class="py-3 px-4 text-center w-[16%]">Waktu Kunjungan</th>
                        <th class="py-3 px-4 text-center w-[22%]">Tujuan</th>
                        @if(Auth::user()->peran == 'komandan')
                            <th class="py-3 px-4 text-center w-[14%]">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200" id="tamu-table-body">
                    @forelse($riwayatTamu as $index => $tamu)
                    <tr class="tamu-row" data-nama="{{ strtolower($tamu->nama_tamu) }}" data-instansi="{{ strtolower($tamu->instansi) }}">
                        <td class="py-2 px-4">{{ $riwayatTamu->firstItem() + $index }}.</td>
                        <td class="py-2 px-4 font-medium">{{ $tamu->nama_tamu }}</td>
                        <td class="py-2 px-4">{{ $tamu->instansi }}</td>
                        <td class="py-2 px-4 text-gray-700 whitespace-nowrap">
                            {{ $tamu->waktu_datang->format('d/m/Y H:i') }}
                        </td>
                        <td class="py-2 px-4">{{ $tamu->tujuan }}</td>
                        @if(Auth::user()->peran == 'komandan')
                            <td class="py-2 px-4">
                                <div class="flex justify-center space-x-3">
                                    <button @click="
                                        showEditModal = true; 
                                        editAction = '{{ route('komandan.tamu.update', $tamu->id_tamu) }}';
                                        editNama = '{{ $tamu->nama_tamu }}';
                                        editInstansi = '{{ $tamu->instansi }}';
                                        editTujuan = '{{ $tamu->tujuan }}';
                                        editWaktuDatang = '{{ $tamu->waktu_datang->format('Y-m-d\TH:i') }}';
                                        " class="text-blue-500 hover:text-blue-700" title="Edit">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828zM5 12V7a2 2 0 012-2h2.586l-4 4H5zM3 15a2 2 0 00-2 2v2h16v-2a2 2 0 00-2-2H3z"></path></svg>
                                    </button>
                                    <button @click.prevent="
                                        showDeleteModal = true; 
                                        deleteAction = '{{ route('komandan.tamu.destroy', $tamu->id_tamu) }}'
                                    " class="text-red-500 hover:text-red-700" title="Hapus">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                    </button>
                                </div>
                            </td>
                        @endif
                    </tr>
                    @empty
                    <tr id="no-data-row">
                        <td colspan="{{ Auth::user()->peran == 'komandan' ? '6' : '5' }}" class="py-4 px-4 text-center text-gray-500">
                            Tidak ada data kunjungan tamu pada tanggal ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- CARD LAYOUT (Mobile) --}}
        <div class="md:hidden space-y-2 p-3" id="tamu-cards">
                @forelse($riwayatTamu as $index => $tamu)
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200 tamu-card" data-nama="{{ strtolower($tamu->nama_tamu) }}" data-instansi="{{ strtolower($tamu->instansi) }}">
                        <div class="flex gap-3 p-3">
                            <div class="flex-1 min-w-0">
                                <div class="mb-2">
                                    <h4 class="font-bold text-gray-800 text-sm">{{ $tamu->nama_tamu }}</h4>
                                    <p class="text-gray-600 text-xs">{{ $tamu->instansi }}</p>
                                </div>
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <p class="text-gray-700 font-semibold text-xs">{{ $tamu->waktu_datang->format('H:i') }}</p>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                        <p class="text-gray-700 font-semibold text-xs">{{ $tamu->tujuan }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if(Auth::user()->peran == 'komandan')
                            <div class="flex border-t border-gray-200">
                                <button @click="
                                            showEditModal = true; 
                                            editAction = '{{ route('komandan.tamu.update', $tamu->id_tamu) }}';
                                            editNama = '{{ $tamu->nama_tamu }}';
                                            editInstansi = '{{ $tamu->instansi }}';
                                            editTujuan = '{{ $tamu->tujuan }}';
                                            editWaktuDatang = '{{ $tamu->waktu_datang->format('Y-m-d\TH:i') }}';
                                        " 
                                        class="flex-1 bg-blue-500 text-white py-2 hover:bg-blue-600 transition flex items-center justify-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828zM5 12V7a2 2 0 012-2h2.586l-4 4H5zM3 15a2 2 0 00-2 2v2h16v-2a2 2 0 00-2-2H3z"></path>
                                    </svg>
                                    <span class="text-xs font-bold">Edit</span>
                                </button>
                                <button @click.prevent="
                                            showDeleteModal = true; 
                                            deleteAction = '{{ route('komandan.tamu.destroy', $tamu->id_tamu) }}'
                                        " 
                                        class="flex-1 bg-red-500 text-white py-2 hover:bg-red-600 transition flex items-center justify-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                    </svg>
                                    <span class="text-xs font-bold">Hapus</span>
                                </button>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="bg-white rounded-lg shadow-sm p-6 text-center" id="no-data-card">
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-500 text-sm font-semibold">Tidak ada data kunjungan tamu pada tanggal ini.</p>
                    </div>
                @endforelse
        </div>

        {{-- Pagination --}}
        <div class="hidden md:flex justify-between items-center px-6 py-4 border-t border-gray-200">
            <div class="text-sm text-gray-600">
                Showing {{ $riwayatTamu->firstItem() ?? 0 }} to {{ $riwayatTamu->lastItem() ?? 0 }} of {{ $riwayatTamu->total() }} entries
            </div>
            <div class="flex gap-1">
                @if($riwayatTamu->onFirstPage())
                    <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Previous</span>
                @else
                    <a href="{{ $riwayatTamu->appends(['per_page' => request('per_page'), 'start_date' => request('start_date'), 'end_date' => request('end_date'), 'cari' => request('cari')])->previousPageUrl() }}" class="pagination-link px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Previous</a>
                @endif

                @php
                    $current = $riwayatTamu->currentPage();
                    $last = $riwayatTamu->lastPage();
                    $start = max(1, $current - 2);
                    $end = min($last, $current + 2);
                @endphp
                
                @if($start > 1)
                    <a href="{{ $riwayatTamu->appends(['per_page' => request('per_page'), 'start_date' => request('start_date'), 'end_date' => request('end_date'), 'cari' => request('cari')])->url(1) }}" class="pagination-link px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">1</a>
                    @if($start > 2)
                        <span class="px-3 py-1 text-gray-500">...</span>
                    @endif
                @endif
                
                @for($page = $start; $page <= $end; $page++)
                    @if($page == $current)
                        <span class="px-3 py-1 text-white bg-[#1e3a5f] rounded font-bold">{{ $page }}</span>
                    @else
                        <a href="{{ $riwayatTamu->appends(['per_page' => request('per_page'), 'start_date' => request('start_date'), 'end_date' => request('end_date'), 'cari' => request('cari')])->url($page) }}" class="pagination-link px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">{{ $page }}</a>
                    @endif
                @endfor
                
                @if($end < $last)
                    @if($end < $last - 1)
                        <span class="px-3 py-1 text-gray-500">...</span>
                    @endif
                    <a href="{{ $riwayatTamu->appends(['per_page' => request('per_page'), 'start_date' => request('start_date'), 'end_date' => request('end_date'), 'cari' => request('cari')])->url($last) }}" class="pagination-link px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">{{ $last }}</a>
                @endif

                @if($riwayatTamu->hasMorePages())
                    <a href="{{ $riwayatTamu->appends(['per_page' => request('per_page'), 'start_date' => request('start_date'), 'end_date' => request('end_date'), 'cari' => request('cari')])->nextPageUrl() }}" class="pagination-link px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Next</a>
                @else
                    <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Next</span>
                @endif
            </div>
        </div>

        <div class="md:hidden flex justify-between items-center px-3 py-4 border-t border-gray-200">
            <div class="text-xs text-gray-600">
                {{ $riwayatTamu->firstItem() ?? 0 }}-{{ $riwayatTamu->lastItem() ?? 0 }} of {{ $riwayatTamu->total() }}
            </div>
            <div class="flex gap-1">
                @if($riwayatTamu->onFirstPage())
                    <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Prev</span>
                @else
                    <a href="{{ $riwayatTamu->appends(['per_page' => request('per_page'), 'start_date' => request('start_date'), 'end_date' => request('end_date'), 'cari' => request('cari')])->previousPageUrl() }}" class="pagination-link px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Prev</a>
                @endif

                @if($start > 1)
                    <a href="{{ $riwayatTamu->appends(['per_page' => request('per_page'), 'start_date' => request('start_date'), 'end_date' => request('end_date'), 'cari' => request('cari')])->url(1) }}" class="pagination-link px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">1</a>
                    @if($start > 2)
                        <span class="px-3 py-1 text-gray-500">...</span>
                    @endif
                @endif
                
                @for($page = $start; $page <= $end; $page++)
                    @if($page == $current)
                        <span class="px-3 py-1 text-white bg-[#1e3a5f] rounded font-bold">{{ $page }}</span>
                    @else
                        <a href="{{ $riwayatTamu->appends(['per_page' => request('per_page'), 'start_date' => request('start_date'), 'end_date' => request('end_date'), 'cari' => request('cari')])->url($page) }}" class="pagination-link px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">{{ $page }}</a>
                    @endif
                @endfor
                
                @if($end < $last)
                    @if($end < $last - 1)
                        <span class="px-3 py-1 text-gray-500">...</span>
                    @endif
                    <a href="{{ $riwayatTamu->appends(['per_page' => request('per_page'), 'start_date' => request('start_date'), 'end_date' => request('end_date'), 'cari' => request('cari')])->url($last) }}" class="pagination-link px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">{{ $last }}</a>
                @endif

                @if($riwayatTamu->hasMorePages())
                    <a href="{{ $riwayatTamu->appends(['per_page' => request('per_page'), 'start_date' => request('start_date'), 'end_date' => request('end_date'), 'cari' => request('cari')])->nextPageUrl() }}" class="pagination-link px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Next</a>
                @else
                    <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Next</span>
                @endif
            </div>
        </div>
