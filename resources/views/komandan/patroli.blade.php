@extends('layouts.app')

@section('header-left')
    <a class="flex items-center border-2 border-[#1a2847] text-[#1a2847] text-sm font-bold px-4 py-2 rounded-full">
        PATROLI
    </a>
@endsection

@section('content')
<div class="w-full mx-auto" 
     x-data="{ 
         showPhotoModal: false, 
         photoUrl: '', 
         showEditModal: false, 
         editAction: '', 
         editWilayah: '',
         showDeleteModal: false,
         deleteAction: '',
         showRulesModal: false
     }">
    
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold text-slate-800">Laporan Patroli Anggota</h2>
        <button @click="showRulesModal = true" 
                class="bg-[#2a4a6f] text-white p-2.5 rounded-lg shadow-md hover:bg-[#1e3a5f] transition" 
                title="Pengaturan Jam Patroli">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
        </button>
    </div>

    {{-- Tampilkan Notifikasi Sukses/Error --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Daftar Patroli --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="bg-gray-100 p-3 border-b border-gray-200">
            <h3 class="font-bold text-gray-800">DAFTAR PATROLI</h3>
        </div>
        
        {{-- Form Filter --}}
        <form action="{{ route('komandan.patroli') }}" method="GET" x-data="{}">
            <div class="px-6 py-5 border-b border-gray-200">
                <div class="flex flex-wrap gap-4">
                    
                    {{-- Show Entries --}}
                    <div class="w-[calc(50%-0.5rem)] md:w-auto">
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Show</label>
                        <div class="flex items-center gap-2">
                            <div class="relative">
                                <select name="per_page" onchange="this.form.submit()" class="block h-[42px] pl-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer appearance-none">
                                    <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5</option>
                                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                            <span class="text-sm text-gray-600 whitespace-nowrap">rows</span>
                        </div>
                    </div>
                    
                    {{-- Filter Tanggal --}}
                    <div class="w-[calc(50%-0.5rem)] md:flex-1">
                        <label for="tanggal" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                            Tanggal
                        </label>
                        <div class="cursor-pointer" @click="$refs.dateInput.showPicker()">
                            <input type="date" id="tanggal" name="tanggal" x-ref="dateInput"
                                   onchange="this.form.submit()"
                                   class="block w-full h-[42px] px-4 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer"
                                   value="{{ $tanggalTerpilih }}">
                        </div>
                    </div>

                    {{-- Filter Jenis Patroli --}}
                    <div class="w-[calc(50%-0.5rem)] md:flex-1">
                        <label for="jenis_patroli" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                            Jenis Patroli
                        </label>
                        <div class="relative">
                            <select id="jenis_patroli" name="jenis_patroli" 
                                    onchange="this.form.submit()"
                                    class="block w-full h-[42px] px-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer appearance-none">
                                @forelse($jenisPatroliOptions as $opsi)
                                    <option value="{{ $opsi }}" {{ $jenisPatroliTerpilih == $opsi ? 'selected' : '' }}>
                                        {{ $opsi }}
                                    </option>
                                @empty
                                    <option value="" disabled selected>Tidak ada data jenis patroli</option>
                                @endforelse
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        
        {{-- TABEL (Desktop) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full min-w-max table-fixed">
                <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500">
                    <tr>
                        <th class="py-3 px-4 text-center w-[6%]">No</th>
                        <th class="py-3 px-4 text-center w-[12%]">Waktu</th>
                        <th class="py-3 px-4 text-center w-[13%]">Jenis</th>
                        <th class="py-3 px-4 text-center w-[25%]">Wilayah</th>
                        <th class="py-3 px-4 text-center w-[20%]">Nama</th>
                        <th class="py-3 px-4 text-center w-[10%]">Detail</th>
                        @if(Auth::user()->peran == 'komandan')
                            <th class="py-3 px-4 text-center w-[14%]">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse($dataPatroli as $index => $item)
                    <tr>
                        <td class="py-2 px-4">{{ $index + 1 }}.</td>
                        <td class="py-2 px-4">{{ $item->waktu_exact->format('H:i:s') }}</td>
                        <td class="py-2 px-4 text-center">
                            <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                                {{ $item->jenis_patroli }}
                            </span>
                        </td>
                        <td class="py-2 px-4 font-medium">{{ $item->wilayah }}</td>
                        <td class="py-2 px-4">{{ $item->nama_lengkap }}</td>
                        <td class="py-2 px-4 text-center">
                            <button @click="showPhotoModal = true; photoUrl = '{{ asset('storage/' . $item->foto) }}'" class="text-blue-500 hover:underline">
                                Buka
                            </button>
                        </td>
                        @if(Auth::user()->peran == 'komandan')
                            <td class="py-2 px-4">
                                <div class="flex justify-center space-x-3">
                                    <button @click="showEditModal = true; editAction = '{{ route('komandan.patroli.update', $item->id_patroli) }}'; editWilayah = '{{ $item->wilayah }}'" class="text-blue-500 hover:text-blue-700" title="Edit">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828zM5 12V7a2 2 0 012-2h2.586l-4 4H5zM3 15a2 2 0 00-2 2v2h16v-2a2 2 0 00-2-2H3z"></path></svg>
                                    </button>
                                    <button @click.prevent="showDeleteModal = true; deleteAction = '{{ route('komandan.patroli.destroy', $item->id_patroli) }}'" class="text-red-500 hover:text-red-700" title="Hapus">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                    </button>
                                </div>
                            </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-4 px-4 text-center text-gray-500">
                            Tidak ada data patroli pada tanggal ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- CARD LAYOUT (Mobile) --}}
        <div class="md:hidden space-y-3 p-3">
            @forelse($dataPatroli as $index => $item)
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                    
                    {{-- Header: Jenis Patroli & Nama --}}
                    <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] px-4 py-2.5 flex justify-between items-center">
                        <div>
                            <p class="text-xs text-blue-200 font-semibold uppercase">{{ $item->jenis_patroli }}</p>
                            <p class="text-white font-bold text-base">{{ $item->nama_lengkap }}</p>
                        </div>
                        <span class="bg-blue-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg">
                            {{ $item->waktu_exact->format('H:i') }}
                        </span>
                    </div>

                    {{-- Body: Info Detail --}}
                    <div class="p-4 space-y-3">
                        
                        {{-- Wilayah --}}
                        <div class="flex items-center gap-2 pb-2 border-b border-gray-100">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <div class="flex-1">
                                <p class="text-[10px] text-gray-500 font-semibold uppercase">Wilayah</p>
                                <p class="text-gray-800 font-bold text-sm">{{ $item->wilayah }}</p>
                            </div>
                            
                            {{-- Tombol Foto --}}
                            <button @click="showPhotoModal = true; photoUrl = '{{ asset('storage/' . $item->foto) }}'" 
                                    class="text-blue-500 hover:text-blue-700 font-semibold text-sm underline">
                                Lihat Foto
                            </button>
                        </div>

                        {{-- Tombol Aksi (Jika Komandan) --}}
                        @if(Auth::user()->peran == 'komandan')
                            <div class="flex gap-2 pt-2">
                                <button @click="showEditModal = true; editAction = '{{ route('komandan.patroli.update', $item->id_patroli) }}'; editWilayah = '{{ $item->wilayah }}'" 
                                        class="flex-1 bg-blue-500 text-white font-bold py-2 rounded-lg hover:bg-blue-600 transition flex items-center justify-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828zM5 12V7a2 2 0 012-2h2.586l-4 4H5zM3 15a2 2 0 00-2 2v2h16v-2a2 2 0 00-2-2H3z"></path></svg>
                                    <span class="text-xs">Edit</span>
                                </button>
                                <button @click.prevent="showDeleteModal = true; deleteAction = '{{ route('komandan.patroli.destroy', $item->id_patroli) }}'" 
                                        class="flex-1 bg-red-500 text-white font-bold py-2 rounded-lg hover:bg-red-600 transition flex items-center justify-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                    <span class="text-xs">Hapus</span>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl shadow-md p-8 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-500 font-semibold">Tidak ada data patroli pada tanggal ini.</p>
                </div>
            @endforelse
        </div>
        
        {{-- Pagination --}}
        @if($dataPatroli->total() > 0)
            <div class="flex justify-between items-center px-6 py-4 border-t border-gray-200">
                <div class="text-sm text-gray-600">
                    Showing {{ $dataPatroli->firstItem() ?? 0 }} to {{ $dataPatroli->lastItem() ?? 0 }} of {{ $dataPatroli->total() }} entries
                </div>
                <div class="flex items-center gap-1">
                    @if ($dataPatroli->onFirstPage())
                        <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Previous</span>
                    @else
                        <a href="{{ $dataPatroli->appends(request()->query())->previousPageUrl() }}" class="px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Previous</a>
                    @endif
                    @foreach(range(1, $dataPatroli->lastPage()) as $page)
                        @if($page == $dataPatroli->currentPage())
                            <span class="px-3 py-1 text-white bg-[#1e3a5f] rounded font-bold">{{ $page }}</span>
                        @else
                            <a href="{{ $dataPatroli->appends(request()->query())->url($page) }}" class="px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">{{ $page }}</a>
                        @endif
                    @endforeach
                    @if ($dataPatroli->hasMorePages())
                        <a href="{{ $dataPatroli->appends(request()->query())->nextPageUrl() }}" class="px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Next</a>
                    @else
                        <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Next</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    {{-- Modal Foto --}}
    <div x-show="showPhotoModal" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
         @click.away="showPhotoModal = false"
         style="display: none;">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full p-4 relative" @click.stop>
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 class="text-xl font-bold text-gray-800">PHOTO</h3>
                <button @click="showPhotoModal = false" class="text-gray-500 hover:text-gray-800 text-3xl">&times;</button>
            </div>
            <div class="mt-4">
                <img :src="photoUrl" alt="Foto Patroli" class="w-full h-auto rounded">
            </div>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div x-show="showEditModal"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
         @click.away="showEditModal = false"
         style="display: none;">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-4 relative" @click.stop>
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 class="text-xl font-bold text-gray-800">EDIT PATROLI</h3>
                <button @click="showEditModal = false" class="text-gray-500 hover:text-gray-800 text-3xl">&times;</button>
            </div>
            <form :action="editAction" method="POST" class="mt-4">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div class="w-full h-40 bg-gray-200 rounded-lg flex items-center justify-center text-gray-400">
                        <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4V5h12v10zm-9.414-2.586a2 2 0 112.828 2.828L8.414 13H12v-1H6.586l1-1zM10 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg>
                    </div>

                    <div>
                        <label for="wilayah" class="block text-sm font-medium text-gray-700 mb-1">WILAYAH:</label>
                        <select id="wilayah" name="wilayah" x-model="editWilayah"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="Area Gedung A">Area Gedung A</option>
                            <option value="Area Parkir Belakang">Area Parkir Belakang</option>
                            <option value="Area Pos-2">Area Pos-2</option>
                            <option value="Lobby VVIP">Lobby VVIP</option>
                            <option value="Area BAU">Area BAU</option>
                            <option value="Area Kantin">Area Kantin</option>
                            <option value="Area BAAM">Area BAAM</option>
                            <option value="Akses Lorong GD-3">Akses Lorong GD-3</option>
                            <option value="Akses Lorong GD-2">Akses Lorong GD-2</option>
                            <option value="Area Pos-3">Area Pos-3</option>
                            <option value="Akses Besi GD-2">Akses Besi GD-2</option>
                            <option value="Akses Kaca GD-2">Akses Kaca GD-2</option>
                            <option value="Akses Selatan Audit">Akses Selatan Audit</option>
                            <option value="Akses Ruang Lektor">Akses Ruang Lektor</option>
                            <option value="Akses Parkir Basement">Akses Parkir Basement</option>
                            <option value="Akses Lift GD-2">Akses Lift GD-2</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full bg-green-500 text-white font-bold py-2 px-4 rounded-lg shadow hover:bg-green-600 transition">
                        SUBMIT
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Hapus --}}
    <div x-show="showDeleteModal"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
         @click.away="showDeleteModal = false"
         style="display: none;">
        <div class="bg-white rounded-lg shadow-xl max-w-sm w-full p-6 relative" @click.stop>
            <h3 class="text-lg font-bold text-gray-900 mb-4">Konfirmasi Hapus</h3>
            <p class="text-gray-600 mb-6">
                Apakah Anda yakin ingin menghapus data patroli ini? Tindakan ini tidak dapat dibatalkan.
            </p>
            <form :action="deleteAction" method="POST" class="flex justify-end space-x-4">
                @csrf
                @method('DELETE')
                <button type="button" @click="showDeleteModal = false" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300">
                    Batal
                </button>
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                    Ya, Hapus
                </button>
            </form>
        </div>
    </div>

    {{-- Modal Pengaturan Jam Patroli --}}
    <div x-show="showRulesModal"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
         @click.away="showRulesModal = false"
         style="display: none;">
        <div class="bg-white rounded-2xl shadow-2xl max-w-5xl w-full max-h-[90vh] flex flex-col" @click.stop>
            
            {{-- Header Modal --}}
            <div class="bg-[#1e3a5f] px-6 py-4 rounded-t-2xl flex items-center justify-between flex-shrink-0 border-b border-[#1e3a5f]">
                <h3 class="text-lg font-bold text-white flex items-center tracking-wide">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    PENGATURAN JAM PATROLI
                </h3>
                <button @click="showRulesModal = false" class="text-white/70 hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            {{-- Body Modal --}}
            <div class="overflow-y-auto flex-1">
                <form action="{{ route('komandan.patroli.updateRules') }}" method="POST" class="p-6">
                @csrf
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    {{-- SHIFT PAGI --}}
                    <div class="bg-amber-50 rounded-xl p-5 border-2 border-amber-200">
                        <h4 class="text-lg font-bold text-amber-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                            </svg>
                            SHIFT PAGI
                        </h4>
                        
                        @foreach(['Patroli 1', 'Patroli 2', 'Patroli 3', 'Patroli 4', 'Patroli 5', 'Patroli 6'] as $patroli)
                            @php
                                $rule = isset($patroliRules['Pagi']) ? $patroliRules['Pagi']->firstWhere('jenis_patroli', $patroli) : null;
                            @endphp
                            <div class="mb-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">{{ $patroli }}</label>
                                <div class="flex items-center gap-2">
                                    <input type="time" 
                                           name="shift_pagi[{{ $patroli }}][jam_mulai]" 
                                           value="{{ $rule ? \Carbon\Carbon::parse($rule->jam_mulai)->format('H:i') : '07:00' }}"
                                           class="flex-1 border-gray-300 rounded-lg shadow-sm text-sm">
                                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                    <input type="time" 
                                           name="shift_pagi[{{ $patroli }}][jam_selesai]" 
                                           value="{{ $rule ? \Carbon\Carbon::parse($rule->jam_selesai)->format('H:i') : '19:00' }}"
                                           class="flex-1 border-gray-300 rounded-lg shadow-sm text-sm">
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- SHIFT MALAM --}}
                    <div class="bg-blue-50 rounded-xl p-5 border-2 border-blue-200">
                        <h4 class="text-lg font-bold text-blue-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                            </svg>
                            SHIFT MALAM
                        </h4>
                        
                        @foreach(['Patroli 1', 'Patroli 2', 'Patroli 3', 'Patroli 4', 'Patroli 5', 'Patroli 6'] as $patroli)
                            @php
                                $rule = isset($patroliRules['Malam']) ? $patroliRules['Malam']->firstWhere('jenis_patroli', $patroli) : null;
                            @endphp
                            <div class="mb-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">{{ $patroli }}</label>
                                <div class="flex items-center gap-2">
                                    <input type="time" 
                                           name="shift_malam[{{ $patroli }}][jam_mulai]" 
                                           value="{{ $rule ? \Carbon\Carbon::parse($rule->jam_mulai)->format('H:i') : '19:00' }}"
                                           class="flex-1 border-gray-300 rounded-lg shadow-sm text-sm">
                                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                    <input type="time" 
                                           name="shift_malam[{{ $patroli }}][jam_selesai]" 
                                           value="{{ $rule ? \Carbon\Carbon::parse($rule->jam_selesai)->format('H:i') : '07:00' }}"
                                           class="flex-1 border-gray-300 rounded-lg shadow-sm text-sm">
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- NON SHIFT - Placeholder --}}
                    <div class="bg-gray-50 rounded-xl p-5 border-2 border-gray-200">
                        <h4 class="text-lg font-bold text-gray-700 mb-4">NON SHIFT</h4>
                        <p class="text-sm text-gray-500 italic">Pengaturan non-shift dapat ditambahkan jika diperlukan.</p>
                    </div>

                </div>

                    {{-- Footer/Action Buttons --}}
                    <div class="mt-6 p-4 border-t bg-gray-50 flex justify-end gap-3">
                        <button type="button" @click="showRulesModal = false" 
                                class="bg-gray-200 text-gray-800 px-6 py-2.5 rounded-lg font-semibold hover:bg-gray-300 transition">
                            Batal
                        </button>
                        <button type="submit" 
                                class="bg-[#1e3a5f] text-white px-6 py-2.5 rounded-xl font-bold hover:bg-[#2a4a6f] shadow-lg transition transform hover:-translate-y-0.5 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            SIMPAN PERUBAHAN
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

</div>
@endsection
