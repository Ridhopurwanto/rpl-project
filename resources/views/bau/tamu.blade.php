@extends('layouts.app')

@section('mobile_width', 'max-w-full')
@section('desktop_width', 'lg:max-w-full')

@section('header-left')
    <a class="flex items-center border-2 border-[#1a2847] text-[#1a2847] text-sm font-bold px-4 py-2 rounded-full">
        TAMU
    </a>
@endsection

@section('content')
<div class="w-full mx-auto">
    
    <h2 class="text-2xl font-bold text-slate-800 mb-4">Laporan Kunjungan Tamu</h2>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="bg-gray-100 p-3 border-b border-gray-200">
            <h3 class="font-bold text-gray-800">RIWAYAT KUNJUNGAN</h3>
        </div>

        <form action="{{ route('bau.tamu.index') }}" method="GET" class="p-4 border-b border-gray-200" x-data="{}">
            <div class="flex flex-wrap gap-4">
                <div class="w-[calc(50%-0.5rem)] md:w-auto">
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Show</label>
                    <div class="flex items-center gap-2">
                        <select name="per_page" onchange="this.form.submit()" class="h-[42px] pl-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm appearance-none cursor-pointer" style="background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27currentColor%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3e%3cpolyline points=%276 9 12 15 18 9%27%3e%3c/polyline%3e%3c/svg%3e'); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 1.25em 1.25em;">
                            <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5</option>
                            <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                        </select>
                        <span class="text-sm text-gray-600">rows</span>
                    </div>
                </div>

                <div class="w-[calc(50%-0.5rem)] md:flex-1">
                    <label for="start_date" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Dari Tanggal</label>
                    <input type="date" id="start_date" name="start_date" onchange="this.form.submit()" class="block w-full h-[42px] px-4 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer" value="{{ $startDate }}">
                </div>

                <div class="w-full md:flex-1">
                    <label for="end_date" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Sampai Tanggal</label>
                    <input type="date" id="end_date" name="end_date" onchange="this.form.submit()" class="block w-full h-[42px] px-4 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer" value="{{ $endDate }}">
                </div>
            </div>
        </form>
        
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full min-w-max table-fixed">
                <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500">
                    <tr>
                        <th class="py-3 px-4 text-center w-[8%]">No</th>
                        <th class="py-3 px-4 text-center w-[26%]">Nama</th>
                        <th class="py-3 px-4 text-center w-[24%]">Instansi</th>
                        <th class="py-3 px-4 text-center w-[18%]">Waktu Kunjungan</th>
                        <th class="py-3 px-4 text-center w-[24%]">Tujuan</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse($riwayatTamu as $index => $tamu)
                    <tr>
                        <td class="py-2 px-4">{{ $riwayatTamu->firstItem() + $index }}.</td>
                        <td class="py-2 px-4 font-medium">{{ $tamu->nama_tamu }}</td>
                        <td class="py-2 px-4">{{ $tamu->instansi }}</td>
                        <td class="py-2 px-4 text-gray-700 whitespace-nowrap">
                            {{ $tamu->waktu_datang->format('d/m/Y H:i') }}
                        </td>
                        <td class="py-2 px-4">{{ $tamu->tujuan }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-4 px-4 text-center text-gray-500">
                            Tidak ada data kunjungan tamu pada tanggal ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($riwayatTamu->total() > 0)
        <div class="hidden md:flex justify-between items-center px-6 py-4 border-t border-gray-200">
            <div class="text-sm text-gray-600">
                Showing {{ $riwayatTamu->firstItem() ?? 0 }} to {{ $riwayatTamu->lastItem() ?? 0 }} of {{ $riwayatTamu->total() }} entries
            </div>
            <div class="flex gap-1">
                @if($riwayatTamu->onFirstPage())
                    <span class="px-3 py-2 text-sm text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">Previous</span>
                @else
                    <a href="{{ $riwayatTamu->appends(request()->query())->previousPageUrl() }}" class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Previous</a>
                @endif

                @foreach($riwayatTamu->getUrlRange(1, $riwayatTamu->lastPage()) as $page => $url)
                    @if($page == $riwayatTamu->currentPage())
                        <span class="px-3 py-2 text-sm text-white bg-[#1e3a5f] rounded-lg">{{ $page }}</span>
                    @else
                        <a href="{{ $riwayatTamu->appends(request()->query())->url($page) }}" class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">{{ $page }}</a>
                    @endif
                @endforeach

                @if($riwayatTamu->hasMorePages())
                    <a href="{{ $riwayatTamu->appends(request()->query())->nextPageUrl() }}" class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Next</a>
                @else
                    <span class="px-3 py-2 text-sm text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">Next</span>
                @endif
            </div>
        </div>
        @endif

        <div class="md:hidden space-y-3 p-3">
            @forelse($riwayatTamu as $index => $tamu)
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                    <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] px-4 py-2.5 flex justify-between items-center">
                        <div class="flex-1">
                            <p class="text-xs text-blue-200 font-semibold uppercase">Tamu</p>
                            <p class="text-white font-bold text-base">{{ $tamu->nama_tamu }}</p>
                        </div>
                        <span class="bg-blue-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg">
                            {{ $tamu->waktu_datang->format('d/m/Y') }}
                        </span>
                    </div>

                    <div class="p-4 space-y-3">
                        <div class="flex items-center gap-2 pb-2 border-b border-gray-100">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <div class="flex-1">
                                <p class="text-[10px] text-gray-500 font-semibold uppercase">Instansi</p>
                                <p class="text-gray-800 font-bold text-sm">{{ $tamu->instansi }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <p class="text-[10px] text-gray-500 font-semibold uppercase mb-1">Waktu</p>
                                <p class="text-gray-800 font-bold text-xs">{{ $tamu->waktu_datang->format('H:i') }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-500 font-semibold uppercase mb-1">Tujuan</p>
                                <p class="text-gray-800 font-bold text-xs">{{ $tamu->tujuan }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl shadow-md p-8 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-500 font-semibold">Tidak ada data kunjungan tamu pada tanggal ini.</p>
                </div>
            @endforelse
        </div>

        <div class="md:hidden flex justify-between items-center px-3 py-4 border-t border-gray-200">
            <div class="text-xs text-gray-600">
                {{ $riwayatTamu->firstItem() ?? 0 }}-{{ $riwayatTamu->lastItem() ?? 0 }} of {{ $riwayatTamu->total() }}
            </div>
            <div class="flex gap-1">
                @if($riwayatTamu->onFirstPage())
                    <span class="px-2 py-1 text-xs text-gray-400 bg-gray-100 rounded cursor-not-allowed">Prev</span>
                @else
                    <a href="{{ $riwayatTamu->appends(request()->query())->previousPageUrl() }}" class="px-2 py-1 text-xs text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Prev</a>
                @endif

                @foreach($riwayatTamu->getUrlRange(1, $riwayatTamu->lastPage()) as $page => $url)
                    @if($page == $riwayatTamu->currentPage())
                        <span class="px-2 py-1 text-xs text-white bg-[#1e3a5f] rounded">{{ $page }}</span>
                    @else
                        <a href="{{ $riwayatTamu->appends(request()->query())->url($page) }}" class="px-2 py-1 text-xs text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">{{ $page }}</a>
                    @endif
                @endforeach

                @if($riwayatTamu->hasMorePages())
                    <a href="{{ $riwayatTamu->appends(request()->query())->nextPageUrl() }}" class="px-2 py-1 text-xs text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Next</a>
                @else
                    <span class="px-2 py-1 text-xs text-gray-400 bg-gray-100 rounded cursor-not-allowed">Next</span>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection
