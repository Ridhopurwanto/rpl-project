@extends('layouts.app')

{{-- Terapkan layout full-width jika ada --}}
@section('mobile_width', 'max-w-full')
@section('desktop_width', 'lg:max-w-full')

@section('header-left')
    <a class="flex items-center border-2 border-[#1a2847] text-[#1a2847] text-sm font-bold px-4 py-2 rounded-full">
        TAMU
    </a>
@endsection

@section('content')
<div class="w-full mx-auto"
     x-data="{ 
        showEditModal: false, 
        editAction: '',
        editNama: '',
        editInstansi: '',
        editTujuan: '',
        editWaktuDatang: '',
        maxDate: '{{ now()->format('Y-m-d\T23:59') }}',
        showDeleteModal: false,
        deleteAction: '' 
     }">




    
    <h2 class="text-2xl font-bold text-slate-800 mb-4">Laporan Kunjungan Tamu</h2>

    {{-- Toast Notification --}}
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-full"
                x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-full"
                class="fixed top-4 right-4 z-50 max-w-sm w-full bg-white rounded-lg shadow-lg border-l-4 border-green-500 p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-gray-900">{{ session('success') }}</p>
                    </div>
                    <button @click="show = false" class="ml-4 flex-shrink-0 text-gray-400 hover:text-gray-500">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-full"
                x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-full"
                class="fixed top-4 right-4 z-50 max-w-sm w-full bg-white rounded-lg shadow-lg border-l-4 border-red-500 p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-gray-900">{{ session('error') }}</p>
                    </div>
                    <button @click="show = false" class="ml-4 flex-shrink-0 text-gray-400 hover:text-gray-500">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-full"
                x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-full"
                class="fixed top-4 right-4 z-50 max-w-sm w-full bg-white rounded-lg shadow-lg border-l-4 border-red-500 p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <ul class="text-sm text-gray-900 space-y-1">@foreach ($errors->all() as $error) <li>{{ $error }}</li>
                        @endforeach</ul>
                    </div>
                    <button @click="show = false" class="ml-4 flex-shrink-0 text-gray-400 hover:text-gray-500">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        @endif

    {{-- Tabel Riwayat Tamu --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] p-3 border-b border-[#2a4a6f]">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <h3 class="font-bold text-white">RIWAYAT KUNJUNGAN</h3>
                </div>
        </div>

        {{-- Form Filter --}}
            <form id="filterFormTamu" action="{{ route('komandan.tamu') }}" method="GET" class="p-4 border-b border-gray-200">
                <div class="flex flex-wrap gap-4">
                    {{-- Show Dropdown --}}
                    <div class="w-[calc(50%-0.5rem)] md:w-auto">
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Show</label>
                        <div class="flex items-center gap-2">
                            <select name="per_page" id="per_page"
                                class="h-[42px] pl-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm appearance-none cursor-pointer"
                                style="background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27currentColor%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3e%3cpolyline points=%276 9 12 15 18 9%27%3e%3c/polyline%3e%3c/svg%3e'); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 1.25em 1.25em;">
                                <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5</option>
                                <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                            </select>
                            <span class="text-sm text-gray-600">rows</span>
                        </div>
                    </div>

                    {{-- Filter Dari Tanggal --}}
                    <div class="w-[calc(50%-0.5rem)] md:flex-1">
                        <label for="start_date"
                            class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Dari
                            Tanggal</label>
                        <div class="cursor-pointer" @click="$refs.dateStart.showPicker()">
                            <input type="date" id="start_date" name="start_date" x-ref="dateStart"
                                class="block w-full h-[42px] px-4 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer"
                                value="{{ $startDate }}">
                        </div>
                    </div>

                    {{-- Filter Sampai Tanggal --}}
                    <div class="w-[calc(50%-0.5rem)] md:flex-1">
                        <label for="end_date"
                            class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Sampai
                            Tanggal</label>
                        <div class="cursor-pointer" @click="$refs.dateEnd.showPicker()">
                            <input type="date" id="end_date" name="end_date" x-ref="dateEnd"
                                class="block w-full h-[42px] px-4 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer"
                                value="{{ $endDate }}">
                        </div>
                    </div>

                    {{-- Filter Pencarian (Search) --}}
                    <div class="w-full md:flex-1">
                        <label for="cari"
                            class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Cari</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" id="cari" name="cari" value="{{ request('cari') }}"
                                class="block w-full h-[42px] pl-10 pr-4 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm"
                                placeholder="Nama atau Instansi...">
                        </div>
                    </div>
                </div>
            </form>
        
        <div id="tamu-results">
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
                    <tr id="no-search-result" style="display: none;">
                        <td colspan="{{ Auth::user()->peran == 'komandan' ? '6' : '5' }}" class="py-4 px-4 text-center text-gray-500">
                            Data tidak ditemukan.
                        </td>
                    </tr>
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
                <div class="bg-white rounded-lg shadow-sm p-6 text-center" id="no-search-result-card" style="display: none;">
                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-500 text-sm font-semibold">Data tidak ditemukan.</p>
                </div>
        </div>

        {{-- Pagination --}}
        <div class="hidden md:flex justify-between items-center px-6 py-4 border-t border-gray-200">
            <div class="text-sm text-gray-600">
                Showing {{ $riwayatTamu->firstItem() ?? 0 }} to {{ $riwayatTamu->lastItem() ?? 0 }} of {{ $riwayatTamu->total() }} entries
            </div>
            <div class="flex gap-1">
                @if($riwayatTamu->onFirstPage())
                    <span class="px-3 py-2 text-sm text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">Previous</span>
                @else
                    <a href="{{ $riwayatTamu->appends(['per_page' => request('per_page'), 'start_date' => request('start_date'), 'end_date' => request('end_date')])->previousPageUrl() }}" class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Previous</a>
                @endif

                @php
                    $current = $riwayatTamu->currentPage();
                    $last = $riwayatTamu->lastPage();
                    $start = max(1, $current - 2);
                    $end = min($last, $current + 2);
                @endphp
                
                @if($start > 1)
                    <a href="{{ $riwayatTamu->appends(['per_page' => request('per_page'), 'start_date' => request('start_date'), 'end_date' => request('end_date')])->url(1) }}" class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">1</a>
                    @if($start > 2)
                        <span class="px-3 py-2 text-sm text-gray-500">...</span>
                    @endif
                @endif
                
                @for($page = $start; $page <= $end; $page++)
                    @if($page == $current)
                        <span class="px-3 py-2 text-sm text-white bg-[#1e3a5f] rounded-lg">{{ $page }}</span>
                    @else
                        <a href="{{ $riwayatTamu->appends(['per_page' => request('per_page'), 'start_date' => request('start_date'), 'end_date' => request('end_date')])->url($page) }}" class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">{{ $page }}</a>
                    @endif
                @endfor
                
                @if($end < $last)
                    @if($end < $last - 1)
                        <span class="px-3 py-2 text-sm text-gray-500">...</span>
                    @endif
                    <a href="{{ $riwayatTamu->appends(['per_page' => request('per_page'), 'start_date' => request('start_date'), 'end_date' => request('end_date')])->url($last) }}" class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">{{ $last }}</a>
                @endif

                @if($riwayatTamu->hasMorePages())
                    <a href="{{ $riwayatTamu->appends(['per_page' => request('per_page'), 'start_date' => request('start_date'), 'end_date' => request('end_date')])->nextPageUrl() }}" class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Next</a>
                @else
                    <span class="px-3 py-2 text-sm text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">Next</span>
                @endif
            </div>
        </div>

        <div class="md:hidden flex justify-between items-center px-3 py-4 border-t border-gray-200">
            <div class="text-xs text-gray-600">
                {{ $riwayatTamu->firstItem() ?? 0 }}-{{ $riwayatTamu->lastItem() ?? 0 }} of {{ $riwayatTamu->total() }}
            </div>
            <div class="flex gap-1">
                @if($riwayatTamu->onFirstPage())
                    <span class="px-2 py-1 text-xs text-gray-400 bg-gray-100 rounded cursor-not-allowed">Prev</span>
                @else
                    <a href="{{ $riwayatTamu->appends(['per_page' => request('per_page'), 'start_date' => request('start_date'), 'end_date' => request('end_date')])->previousPageUrl() }}" class="px-2 py-1 text-xs text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Prev</a>
                @endif

                @if($start > 1)
                    <a href="{{ $riwayatTamu->appends(['per_page' => request('per_page'), 'start_date' => request('start_date'), 'end_date' => request('end_date')])->url(1) }}" class="px-2 py-1 text-xs text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">1</a>
                    @if($start > 2)
                        <span class="px-2 py-1 text-xs text-gray-500">...</span>
                    @endif
                @endif
                
                @for($page = $start; $page <= $end; $page++)
                    @if($page == $current)
                        <span class="px-2 py-1 text-xs text-white bg-[#1e3a5f] rounded">{{ $page }}</span>
                    @else
                        <a href="{{ $riwayatTamu->appends(['per_page' => request('per_page'), 'start_date' => request('start_date'), 'end_date' => request('end_date')])->url($page) }}" class="px-2 py-1 text-xs text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">{{ $page }}</a>
                    @endif
                @endfor
                
                @if($end < $last)
                    @if($end < $last - 1)
                        <span class="px-2 py-1 text-xs text-gray-500">...</span>
                    @endif
                    <a href="{{ $riwayatTamu->appends(['per_page' => request('per_page'), 'start_date' => request('start_date'), 'end_date' => request('end_date')])->url($last) }}" class="px-2 py-1 text-xs text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">{{ $last }}</a>
                @endif

                @if($riwayatTamu->hasMorePages())
                    <a href="{{ $riwayatTamu->appends(['per_page' => request('per_page'), 'start_date' => request('start_date'), 'end_date' => request('end_date')])->nextPageUrl() }}" class="px-2 py-1 text-xs text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Next</a>
                @else
                    <span class="px-2 py-1 text-xs text-gray-400 bg-gray-100 rounded cursor-not-allowed">Next</span>
                @endif
            </div>
        </div>
        </div>
    </div>

    {{-- Modal Edit Tamu --}}
    <div x-show="showEditModal"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
         @click.away="showEditModal = false"
         style="display: none;">
        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full relative overflow-hidden" @click.stop>
            {{-- Header Biru --}}
            <div class="bg-[#1e3a5f] py-4 px-6 border-b border-[#1e3a5f] flex justify-between items-center">
                <h3 class="text-lg font-bold text-white flex items-center tracking-wide">
                    EDIT DATA TAMU
                </h3>
                <button @click="showEditModal = false" class="text-white/70 hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <form :action="editAction" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body max-h-[70vh] overflow-y-auto p-6">
                    <div class="space-y-5">
                        
                        {{-- GROUP: Informasi Tamu --}}
                        <div class="p-4 bg-blue-50/50 rounded-xl border border-blue-100">

                            
                            <div class="space-y-4">
                                {{-- Nama Tamu --}}
                                <div>
                                    <label for="nama_tamu" class="block text-xs font-bold text-[#1e3a5f] uppercase tracking-wide mb-1">Nama Tamu <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-[#1e3a5f]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        </div>
                                        <input type="text" id="nama_tamu" name="nama_tamu" x-model="editNama" required placeholder="Nama lengkap tamu"
                                               class="pl-10 w-full bg-white border border-gray-300 text-gray-800 text-sm font-medium rounded-lg shadow-sm focus:ring-[#1e3a5f] focus:border-[#1e3a5f] block p-2.5">
                                    </div>
                                </div>
                                
                                {{-- Instansi --}}
                                <div>
                                    <label for="instansi" class="block text-xs font-bold text-[#1e3a5f] uppercase tracking-wide mb-1">Instansi <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-[#1e3a5f]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        </div>
                                        <input type="text" id="instansi" name="instansi" x-model="editInstansi" required placeholder="Nama instansi/perusahaan"
                                               class="pl-10 w-full bg-white border border-gray-300 text-gray-800 text-sm font-medium rounded-lg shadow-sm focus:ring-[#1e3a5f] focus:border-[#1e3a5f] block p-2.5">
                                    </div>
                                </div>

                                {{-- Tujuan --}}
                                <div>
                                    <label for="tujuan" class="block text-xs font-bold text-[#1e3a5f] uppercase tracking-wide mb-1">Tujuan <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-[#1e3a5f]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                        </div>
                                        <input type="text" id="tujuan" name="tujuan" x-model="editTujuan" required placeholder="Tujuan kunjungan"
                                               class="pl-10 w-full bg-white border border-gray-300 text-gray-800 text-sm font-medium rounded-lg shadow-sm focus:ring-[#1e3a5f] focus:border-[#1e3a5f] block p-2.5">
                                    </div>
                                </div>
                                
                                {{-- Waktu Kunjungan --}}
                                <div>
                                    <label for="waktu_datang" class="block text-xs font-bold text-[#1e3a5f] uppercase tracking-wide mb-1">Waktu Kunjungan <span class="text-red-500">*</span></label>
                                    <div class="relative cursor-pointer" @click="$refs.waktuDatangInput.showPicker()">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-[#1e3a5f]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <input type="datetime-local" id="waktu_datang" name="waktu_datang" x-ref="waktuDatangInput" x-model="editWaktuDatang" :max="maxDate" required
                                               class="pl-10 w-full bg-white border border-gray-300 text-gray-800 text-sm font-medium rounded-lg shadow-sm focus:ring-[#1e3a5f] focus:border-[#1e3a5f] block p-2.5 cursor-pointer">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer p-4 border-t bg-gray-50">
                    <button type="submit" class="w-full px-4 py-3 text-white font-bold bg-[#1e3a5f] rounded-xl hover:bg-[#2a4a6f] shadow-lg transition transform hover:-translate-y-0.5">
                        SIMPAN PERUBAHAN
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
                Apakah Anda yakin ingin menghapus data tamu ini? Tindakan ini tidak dapat dibatalkan.
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

</div>

{{-- SCRIPT LIVE SEARCH --}}
<script>
document.getElementById('cari').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('.tamu-row');
    const cards = document.querySelectorAll('.tamu-card');
    
    let visibleCount = 0;
    
    // Filter desktop table rows
    rows.forEach(row => {
        const nama = row.dataset.nama;
        const instansi = row.dataset.instansi;
        const isVisible = nama.includes(searchTerm) || instansi.includes(searchTerm);
        row.style.display = isVisible ? '' : 'none';
        if (isVisible) visibleCount++;
    });
    
    // Filter mobile cards
    cards.forEach(card => {
        const nama = card.dataset.nama;
        const instansi = card.dataset.instansi;
        const isVisible = nama.includes(searchTerm) || instansi.includes(searchTerm);
        card.style.display = isVisible ? '' : 'none';
    });
    
    // Show/hide no data message
    const noSearchRow = document.getElementById('no-search-result');
    const noSearchCard = document.getElementById('no-search-result-card');
    if (noSearchRow) noSearchRow.style.display = visibleCount === 0 && searchTerm ? '' : 'none';
    if (noSearchCard) noSearchCard.style.display = visibleCount === 0 && searchTerm ? '' : 'none';
});
</script>
@endsection
