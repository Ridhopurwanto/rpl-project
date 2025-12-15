    {{-- Tabel Barang Temuan --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="bg-gray-100 p-3 border-b border-gray-200 cursor-pointer hover:bg-gray-200 transition" @click="showTemuan = !showTemuan">
            <div class="flex justify-between items-center">
                <h3 class="font-bold text-gray-800">BARANG TEMUAN</h3>
                <svg class="w-5 h-5 text-gray-600 transition-transform" :class="{ 'rotate-180': !showTemuan }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
        </div>

        <div x-show="showTemuan" x-collapse>
        {{-- Form Filter Temuan --}}
        <form action="{{ route('komandan.barang') }}" method="GET" class="p-4 border-b border-gray-200" onsubmit="return false;">
            <div class="flex flex-wrap gap-4">
                <div class="w-[calc(50%-0.5rem)] md:w-auto">
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Show</label>
                    <div class="flex items-center gap-2">
                        <select name="per_page_temuan" id="per_page_temuan" class="h-[42px] pl-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm appearance-none cursor-pointer" style="background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27currentColor%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3e%3cpolyline points=%276 9 12 15 18 9%27%3e%3c/polyline%3e%3c/svg%3e'); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 1.25em 1.25em;">
                            <option value="5" {{ $perPageTemuan == 5 ? 'selected' : '' }}>5</option>
                            <option value="10" {{ $perPageTemuan == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ $perPageTemuan == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ $perPageTemuan == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ $perPageTemuan == 100 ? 'selected' : '' }}>100</option>
                        </select>
                        <span class="text-sm text-gray-600">rows</span>
                    </div>
                </div>
                <div class="w-[calc(50%-0.5rem)] md:flex-1">
                    <label for="tanggal_temuan" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Tanggal</label>
                    <div class="cursor-pointer" @click="$refs.dateInputTemuan.showPicker()">
                        <input type="date" id="tanggal_temuan" name="tanggal_temuan" x-ref="dateInputTemuan" class="block w-full h-[42px] px-4 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer" value="{{ $tanggalTemuan }}">
                    </div>
                </div>
                <div class="w-full md:flex-1">
                    <label for="searchInputTemuan" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Cari Barang</label>
                    <input type="text" id="searchInputTemuan" name="search_temuan" class="block w-full h-[42px] px-4 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm placeholder-gray-400" value="{{ $searchTemuan }}" placeholder="Ketik untuk mencari...">
                </div>
            </div>
        </form>
        
        {{-- TABEL (Desktop) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full min-w-max">
                <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500">
                    <tr>
                        <th class="py-3 px-4 text-center">No</th>
                        <th class="py-3 px-4 text-center">Foto</th>
                        <th class="py-3 px-4 text-center">Nama Barang</th>
                        <th class="py-3 px-4 text-center">Pelapor</th>
                        <th class="py-3 px-4 text-center">Lokasi Temuan</th>
                        <th class="py-3 px-4 text-center">Catatan</th>
                        <th class="py-3 px-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse($barangTemuan as $index => $barang)
                        <tr>
                            <td class="py-2 px-4">{{ $barangTemuan->firstItem() + $index }}.</td>
                            <td class="py-2 px-4 text-center">
                                @if($barang->foto)
                                    <button @click="showPhotoModal = true; photos = ['{{ asset('storage/' . $barang->foto) }}'@if($barang->foto_penerima), '{{ asset('storage/' . $barang->foto_penerima) }}'@endif]; currentPhotoIndex = 0" class="text-blue-500 hover:underline">Buka</button>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="py-2 px-4 font-medium">{{ $barang->nama_barang }}</td>
                            <td class="py-2 px-4">{{ $barang->nama_pelapor }}</td>
                            <td class="py-2 px-4">{{ $barang->lokasi_penemuan }}</td>
                            <td class="py-2 px-4">{{ $barang->catatan }}</td>
                            <td class="py-2 px-4 text-center">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $barang->status == 'belum selesai' ? 'bg-red-200 text-yellow-800' : 'bg-green-200 text-green-800' }}">{{ $barang->status }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-4 px-4 text-center text-gray-500">Tidak ada data barang temuan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- CARD LAYOUT (Mobile) --}}
        <div class="md:hidden space-y-3 p-3">
            @forelse($barangTemuan as $barang)
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                    <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] px-4 py-2.5 flex justify-between items-center">
                        <div class="flex-1">
                            <p class="text-xs text-blue-200 font-semibold uppercase">Barang Temuan</p>
                            <p class="text-white font-bold text-base">{{ $barang->nama_barang }}</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold shadow-lg {{ $barang->status == 'belum selesai' ? 'bg-yellow-500 text-white' : 'bg-green-500 text-white' }}">{{ $barang->status }}</span>
                    </div>
                    <div class="p-4 space-y-3">
                        <div class="grid grid-cols-2 gap-3 pb-2 border-b border-gray-100">
                            <div>
                                <p class="text-[10px] text-gray-500 font-semibold uppercase">Pelapor</p>
                                <p class="text-gray-800 font-bold text-sm">{{ $barang->nama_pelapor }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-500 font-semibold uppercase">Lokasi Temuan</p>
                                <p class="text-gray-800 font-bold text-sm">{{ $barang->lokasi_penemuan }}</p>
                            </div>
                        </div>
                        <div class="pt-2">
                            <p class="text-[10px] text-gray-500 font-semibold uppercase mb-1">Catatan</p>
                            <p class="text-gray-800 text-sm leading-relaxed">{{ $barang->catatan }}</p>
                        </div>
                        @if($barang->foto)
                            <div class="pt-2">
                                <button @click="showPhotoModal = true; photos = ['{{ asset('storage/' . $barang->foto) }}'@if($barang->foto_penerima), '{{ asset('storage/' . $barang->foto_penerima) }}'@endif]; currentPhotoIndex = 0" class="w-full bg-blue-500 text-white font-bold py-2 rounded-lg hover:bg-blue-600 transition flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span class="text-xs">Lihat Foto</span>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl shadow-md p-8 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                    <p class="text-gray-500 font-semibold">Tidak ada data barang temuan.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination Desktop --}}
        <div class="hidden md:flex justify-between items-center px-6 py-4 border-t border-gray-200">
            <div class="text-sm text-gray-600">Showing {{ $barangTemuan->firstItem() ?? 0 }} to {{ $barangTemuan->lastItem() ?? 0 }} of {{ $barangTemuan->total() }} entries</div>
            <div class="flex gap-1">
                @if($barangTemuan->onFirstPage())
                    <span class="px-3 py-2 text-sm text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">Previous</span>
                @else
                    <a href="{{ $barangTemuan->appends(request()->except('page_temuan'))->previousPageUrl() }}" class="pagination-link px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Previous</a>
                @endif
                @foreach($barangTemuan->getUrlRange(1, $barangTemuan->lastPage()) as $page => $url)
                    @if($page == $barangTemuan->currentPage())
                        <span class="px-3 py-2 text-sm text-white bg-[#1e3a5f] rounded-lg">{{ $page }}</span>
                    @else
                        <a href="{{ $barangTemuan->appends(request()->except('page_temuan'))->url($page) }}" class="pagination-link px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">{{ $page }}</a>
                    @endif
                @endforeach
                @if($barangTemuan->hasMorePages())
                    <a href="{{ $barangTemuan->appends(request()->except('page_temuan'))->nextPageUrl() }}" class="pagination-link px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Next</a>
                @else
                    <span class="px-3 py-2 text-sm text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">Next</span>
                @endif
            </div>
        </div>

        {{-- Pagination Mobile --}}
        <div class="md:hidden flex justify-between items-center px-3 py-4 border-t border-gray-200">
            <div class="text-xs text-gray-600">{{ $barangTemuan->firstItem() ?? 0 }}-{{ $barangTemuan->lastItem() ?? 0 }} of {{ $barangTemuan->total() }}</div>
            <div class="flex gap-1">
                @if($barangTemuan->onFirstPage())
                    <span class="px-2 py-1 text-xs text-gray-400 bg-gray-100 rounded cursor-not-allowed">Prev</span>
                @else
                    <a href="{{ $barangTemuan->appends(request()->except('page_temuan'))->previousPageUrl() }}" class="pagination-link px-2 py-1 text-xs text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Prev</a>
                @endif
                @foreach($barangTemuan->getUrlRange(1, $barangTemuan->lastPage()) as $page => $url)
                    @if($page == $barangTemuan->currentPage())
                        <span class="px-2 py-1 text-xs text-white bg-[#1e3a5f] rounded">{{ $page }}</span>
                    @else
                        <a href="{{ $barangTemuan->appends(request()->except('page_temuan'))->url($page) }}" class="pagination-link px-2 py-1 text-xs text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">{{ $page }}</a>
                    @endif
                @endforeach
                @if($barangTemuan->hasMorePages())
                    <a href="{{ $barangTemuan->appends(request()->except('page_temuan'))->nextPageUrl() }}" class="pagination-link px-2 py-1 text-xs text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Next</a>
                @else
                    <span class="px-2 py-1 text-xs text-gray-400 bg-gray-100 rounded cursor-not-allowed">Next</span>
                @endif
            </div>
        </div>
        </div>
    </div>

    {{-- Tabel Barang Titipan --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="bg-gray-100 p-3 border-b border-gray-200 cursor-pointer hover:bg-gray-200 transition" @click="showTitipan = !showTitipan">
            <div class="flex justify-between items-center">
                <h3 class="font-bold text-gray-800">BARANG TITIPAN</h3>
                <svg class="w-5 h-5 text-gray-600 transition-transform" :class="{ 'rotate-180': !showTitipan }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
        </div>

        <div x-show="showTitipan" x-collapse>
        {{-- Form Filter Titipan --}}
        <form action="{{ route('komandan.barang') }}" method="GET" class="p-4 border-b border-gray-200" onsubmit="return false;">
            <div class="flex flex-wrap gap-4">
                <div class="w-[calc(50%-0.5rem)] md:w-auto">
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Show</label>
                    <div class="flex items-center gap-2">
                        <select name="per_page_titipan" id="per_page_titipan" class="h-[42px] pl-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm appearance-none cursor-pointer" style="background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27currentColor%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3e%3cpolyline points=%276 9 12 15 18 9%27%3e%3c/polyline%3e%3c/svg%3e'); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 1.25em 1.25em;">
                            <option value="5" {{ $perPageTitipan == 5 ? 'selected' : '' }}>5</option>
                            <option value="10" {{ $perPageTitipan == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ $perPageTitipan == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ $perPageTitipan == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ $perPageTitipan == 100 ? 'selected' : '' }}>100</option>
                        </select>
                        <span class="text-sm text-gray-600">rows</span>
                    </div>
                </div>
                <div class="w-[calc(50%-0.5rem)] md:flex-1">
                    <label for="tanggal_titipan" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Tanggal</label>
                    <div class="cursor-pointer" @click="$refs.dateInputTitipan.showPicker()">
                        <input type="date" id="tanggal_titipan" name="tanggal_titipan" x-ref="dateInputTitipan" class="block w-full h-[42px] px-4 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer" value="{{ $tanggalTitipan }}">
                    </div>
                </div>
                <div class="w-full md:flex-1">
                    <label for="searchInputTitipan" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Cari Barang</label>
                    <input type="text" id="searchInputTitipan" name="search_titipan" class="block w-full h-[42px] px-4 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm placeholder-gray-400" value="{{ $searchTitipan }}" placeholder="Ketik untuk mencari...">
                </div>
            </div>
        </form>

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
        <div class="md:hidden space-y-3 p-3">
            @forelse($barangTitipan as $barang)
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                    <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] px-4 py-2.5 flex justify-between items-center">
                        <div class="flex-1">
                            <p class="text-xs text-blue-200 font-semibold uppercase">Barang Titipan</p>
                            <p class="text-white font-bold text-base">{{ $barang->nama_barang }}</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold shadow-lg {{ $barang->status == 'belum selesai' ? 'bg-yellow-500 text-white' : 'bg-green-500 text-white' }}">{{ $barang->status }}</span>
                    </div>
                    <div class="p-4 space-y-3">
                        <div class="grid grid-cols-2 gap-3 pb-2 border-b border-gray-100">
                            <div>
                                <p class="text-[10px] text-gray-500 font-semibold uppercase">Penitip</p>
                                <p class="text-gray-800 font-bold text-sm">{{ $barang->nama_penitip }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-500 font-semibold uppercase">Penerima</p>
                                <p class="text-gray-800 font-bold text-sm">{{ $barang->tujuan }}</p>
                            </div>
                        </div>
                        <div class="pt-2">
                            <p class="text-[10px] text-gray-500 font-semibold uppercase mb-1">Catatan</p>
                            <p class="text-gray-800 text-sm leading-relaxed">{{ $barang->catatan }}</p>
                        </div>
                        @if($barang->foto)
                            <div class="pt-2">
                                <button @click="showPhotoModal = true; photos = ['{{ asset('storage/' . $barang->foto) }}'@if($barang->foto_penerima), '{{ asset('storage/' . $barang->foto_penerima) }}'@endif]; currentPhotoIndex = 0" class="w-full bg-blue-500 text-white font-bold py-2 rounded-lg hover:bg-blue-600 transition flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span class="text-xs">Lihat Foto</span>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl shadow-md p-8 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                    <p class="text-gray-500 font-semibold">Tidak ada data barang titipan.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination Desktop --}}
        <div class="hidden md:flex justify-between items-center px-6 py-4 border-t border-gray-200">
            <div class="text-sm text-gray-600">Showing {{ $barangTitipan->firstItem() ?? 0 }} to {{ $barangTitipan->lastItem() ?? 0 }} of {{ $barangTitipan->total() }} entries</div>
            <div class="flex gap-1">
                @if($barangTitipan->onFirstPage())
                    <span class="px-3 py-2 text-sm text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">Previous</span>
                @else
                    <a href="{{ $barangTitipan->appends(request()->except('page_titipan'))->previousPageUrl() }}" class="pagination-link px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Previous</a>
                @endif
                @foreach($barangTitipan->getUrlRange(1, $barangTitipan->lastPage()) as $page => $url)
                    @if($page == $barangTitipan->currentPage())
                        <span class="px-3 py-2 text-sm text-white bg-[#1e3a5f] rounded-lg">{{ $page }}</span>
                    @else
                        <a href="{{ $barangTitipan->appends(request()->except('page_titipan'))->url($page) }}" class="pagination-link px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">{{ $page }}</a>
                    @endif
                @endforeach
                @if($barangTitipan->hasMorePages())
                    <a href="{{ $barangTitipan->appends(request()->except('page_titipan'))->nextPageUrl() }}" class="pagination-link px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Next</a>
                @else
                    <span class="px-3 py-2 text-sm text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">Next</span>
                @endif
            </div>
        </div>

        {{-- Pagination Mobile --}}
        <div class="md:hidden flex justify-between items-center px-3 py-4 border-t border-gray-200">
            <div class="text-xs text-gray-600">{{ $barangTitipan->firstItem() ?? 0 }}-{{ $barangTitipan->lastItem() ?? 0 }} of {{ $barangTitipan->total() }}</div>
            <div class="flex gap-1">
                @if($barangTitipan->onFirstPage())
                    <span class="px-2 py-1 text-xs text-gray-400 bg-gray-100 rounded cursor-not-allowed">Prev</span>
                @else
                    <a href="{{ $barangTitipan->appends(request()->except('page_titipan'))->previousPageUrl() }}" class="pagination-link px-2 py-1 text-xs text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Prev</a>
                @endif
                @foreach($barangTitipan->getUrlRange(1, $barangTitipan->lastPage()) as $page => $url)
                    @if($page == $barangTitipan->currentPage())
                        <span class="px-2 py-1 text-xs text-white bg-[#1e3a5f] rounded">{{ $page }}</span>
                    @else
                        <a href="{{ $barangTitipan->appends(request()->except('page_titipan'))->url($page) }}" class="pagination-link px-2 py-1 text-xs text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">{{ $page }}</a>
                    @endif
                @endforeach
                @if($barangTitipan->hasMorePages())
                    <a href="{{ $barangTitipan->appends(request()->except('page_titipan'))->nextPageUrl() }}" class="pagination-link px-2 py-1 text-xs text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Next</a>
                @else
                    <span class="px-2 py-1 text-xs text-gray-400 bg-gray-100 rounded cursor-not-allowed">Next</span>
                @endif
            </div>
        </div>
        </div>
    </div>
