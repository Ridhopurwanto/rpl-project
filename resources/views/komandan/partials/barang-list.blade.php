    {{-- Tabel Barang Temuan --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] p-3 border-b border-[#2a4a6f] cursor-pointer hover:bg-[#2a4a6f] transition" @click="showTemuan = !showTemuan">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <h3 class="font-bold text-white">BARANG TEMUAN</h3>
                </div>
                <svg class="w-5 h-5 text-white transition-transform" :class="{ 'rotate-180': !showTemuan }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <div class="w-[calc(50%-0.5rem)] md:flex-1">
                    <label for="status_temuan" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Status</label>
                    <select name="status_temuan" id="status_temuan" class="h-[42px] pl-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm appearance-none cursor-pointer" style="background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27currentColor%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3e%3cpolyline points=%276 9 12 15 18 9%27%3e%3c/polyline%3e%3c/svg%3e'); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 1.25em 1.25em;">
                        <option value="" {{ ($statusTemuan ?? '') == '' ? 'selected' : '' }}>Semua Status</option>
                        <option value="belum selesai" {{ ($statusTemuan ?? '') == 'belum selesai' ? 'selected' : '' }}>Belum Selesai</option>
                        <option value="selesai" {{ ($statusTemuan ?? '') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>
                <div class="w-[calc(50%-0.5rem)] md:flex-1">
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
        <div class="md:hidden space-y-2 p-3">
            @forelse($barangTemuan as $barang)
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
                                        <p class="text-gray-700 font-semibold text-xs">{{ $barang->nama_pelapor }}</p>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <p class="text-gray-700 font-semibold text-xs">{{ $barang->lokasi_penemuan }}</p>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-600">{{ $barang->catatan }}</p>
                            </div>
                        </div>
                        <div class="pt-3 border-t border-gray-100">
                            <div class="grid grid-cols-2 gap-4 text-xs text-gray-600">
                                <div>
                                    <span class="font-semibold">Masuk:</span>
                                    <span>{{ $barang->waktu_lapor->format('d/m/Y H:i') }}</span>
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
                    <p class="text-gray-500 text-sm font-semibold">Tidak ada data barang temuan.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination Desktop --}}
        <div class="hidden md:flex justify-between items-center px-6 py-4 border-t border-gray-200">
            <div class="text-sm text-gray-600">Showing {{ $barangTemuan->firstItem() ?? 0 }} to {{ $barangTemuan->lastItem() ?? 0 }} of {{ $barangTemuan->total() }} entries</div>
            <div class="flex gap-1">
                @if($barangTemuan->onFirstPage())
                    <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Previous</span>
                @else
                    <a href="{{ $barangTemuan->appends(request()->except('page_temuan'))->previousPageUrl() }}" class="pagination-link px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Previous</a>
                @endif
                @foreach($barangTemuan->getUrlRange(1, $barangTemuan->lastPage()) as $page => $url)
                    @if($page == $barangTemuan->currentPage())
                        <span class="px-3 py-1 text-white bg-[#1e3a5f] rounded font-bold">{{ $page }}</span>
                    @else
                        <a href="{{ $barangTemuan->appends(request()->except('page_temuan'))->url($page) }}" class="pagination-link px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">{{ $page }}</a>
                    @endif
                @endforeach
                @if($barangTemuan->hasMorePages())
                    <a href="{{ $barangTemuan->appends(request()->except('page_temuan'))->nextPageUrl() }}" class="pagination-link px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Next</a>
                @else
                    <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Next</span>
                @endif
            </div>
        </div>

        {{-- Pagination Mobile --}}
        <div class="md:hidden flex justify-between items-center px-3 py-4 border-t border-gray-200">
            <div class="text-xs text-gray-600">{{ $barangTemuan->firstItem() ?? 0 }}-{{ $barangTemuan->lastItem() ?? 0 }} of {{ $barangTemuan->total() }}</div>
            <div class="flex gap-1">
                @if($barangTemuan->onFirstPage())
                    <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Prev</span>
                @else
                    <a href="{{ $barangTemuan->appends(request()->except('page_temuan'))->previousPageUrl() }}" class="pagination-link px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Prev</a>
                @endif
                @foreach($barangTemuan->getUrlRange(1, $barangTemuan->lastPage()) as $page => $url)
                    @if($page == $barangTemuan->currentPage())
                        <span class="px-3 py-1 text-white bg-[#1e3a5f] rounded font-bold">{{ $page }}</span>
                    @else
                        <a href="{{ $barangTemuan->appends(request()->except('page_temuan'))->url($page) }}" class="pagination-link px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">{{ $page }}</a>
                    @endif
                @endforeach
                @if($barangTemuan->hasMorePages())
                    <a href="{{ $barangTemuan->appends(request()->except('page_temuan'))->nextPageUrl() }}" class="pagination-link px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Next</a>
                @else
                    <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Next</span>
                @endif
            </div>
        </div>
        </div>
    </div>

    {{-- Tabel Barang Titipan --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] p-3 border-b border-[#2a4a6f] cursor-pointer hover:bg-[#2a4a6f] transition" @click="showTitipan = !showTitipan">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <h3 class="font-bold text-white">BARANG TITIPAN</h3>
                </div>
                <svg class="w-5 h-5 text-white transition-transform" :class="{ 'rotate-180': !showTitipan }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <div class="w-[calc(50%-0.5rem)] md:flex-1">
                    <label for="status_titipan" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Status</label>
                    <select name="status_titipan" id="status_titipan" class="h-[42px] pl-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm appearance-none cursor-pointer" style="background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27currentColor%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3e%3cpolyline points=%276 9 12 15 18 9%27%3e%3c/polyline%3e%3c/svg%3e'); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 1.25em 1.25em;">
                        <option value="" {{ ($statusTitipan ?? '') == '' ? 'selected' : '' }}>Semua Status</option>
                        <option value="belum selesai" {{ ($statusTitipan ?? '') == 'belum selesai' ? 'selected' : '' }}>Belum Selesai</option>
                        <option value="selesai" {{ ($statusTitipan ?? '') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>
                <div class="w-[calc(50%-0.5rem)] md:flex-1">
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
        </div>
    </div>
