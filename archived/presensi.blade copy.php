@extends('layouts.app')

{{-- Tombol KEMBALI --}}
@section('header-left')
    <a class="flex items-center border-2 border-[#1a2847] text-[#1a2847] text-sm font-bold px-4 py-2 rounded-full">
        PRESENSI
    </a>
@endsection

@section('content')
    <div class="w-full mx-auto" x-data="presensiData()">

        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-slate-800">Laporan Presensi Anggota</h2>
            <button @click="showRuleModal = true"
                class="bg-[#2a4a6f] text-white p-2.5 rounded-lg shadow-md hover:bg-[#1e3a5f] transition"
                title="Pengaturan Shift Rule">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                    </path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </button>
        </div>

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
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 10000)"
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
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 10000)"
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

        {{-- Form Filter --}}
        <form action="{{ route('komandan.presensi') }}" method="GET" x-data="{}">
            <div class="bg-white px-6 py-5 rounded-xl shadow-sm mb-6 border border-gray-200">

                <div class="flex flex-wrap gap-4">

                    {{-- Show Entries --}}
                    <div class="w-[calc(50%-0.5rem)] md:w-auto">
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Show</label>
                        <div class="flex items-center gap-2">
                            <div class="relative">
                                <select name="per_page" onchange="this.form.submit()"
                                    class="block h-[42px] pl-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer appearance-none">
                                    <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5</option>
                                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                            <span class="text-sm text-gray-600 whitespace-nowrap">rows</span>
                        </div>
                    </div>

                    {{-- Filter Tanggal --}}
                    <div class="w-[calc(50%-0.5rem)] md:flex-1">
                        <label for="tanggal"
                            class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                            Tanggal
                        </label>
                        <div class="cursor-pointer" @click="$refs.dateInput.showPicker()">
                            <input type="date" id="tanggal" name="tanggal" x-ref="dateInput" onchange="this.form.submit()"
                                class="block w-full h-[42px] px-4 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer"
                                value="{{ $tanggalTerpilih }}">
                        </div>
                    </div>

                    {{-- Filter Jenis Shift --}}
                    <div class="w-[calc(50%-0.5rem)] md:flex-1">
                        <label for="shift" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                            Jenis Shift
                        </label>
                        <div class="relative">
                            <select id="shift" name="shift" onchange="this.form.submit()"
                                class="block w-full h-[42px] px-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer appearance-none">
                                <option value="semua" @if($shiftTerpilih == 'semua') selected @endif>Semua Shift</option>
                                <option value="Pagi" @if($shiftTerpilih == 'Pagi') selected @endif>Shift Pagi</option>
                                <option value="Malam" @if($shiftTerpilih == 'Malam') selected @endif>Shift Malam</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        {{-- DAFTAR PRESENSI MASUK --}}
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
                        <th class="py-3 px-4 text-center {{ $shiftTerpilih == 'semua' ? 'w-[5%]' : 'w-[8%]' }}">No
                        </th>
                        <th class="py-3 px-4 text-center {{ $shiftTerpilih == 'semua' ? 'w-[25%]' : 'w-[28%]' }}">
                            Nama</th>
                        @if($shiftTerpilih == 'semua')
                            <th class="py-3 px-4 text-center w-[15%]">Jenis Shift</th>
                        @endif
                        <th class="py-3 px-4 text-center {{ $shiftTerpilih == 'semua' ? 'w-[15%]' : 'w-[18%]' }}">
                            Waktu</th>
                        <th class="py-3 px-4 text-center {{ $shiftTerpilih == 'semua' ? 'w-[10%]' : 'w-[13%]' }}">
                            Foto</th>
                        <th class="py-3 px-4 text-center {{ $shiftTerpilih == 'semua' ? 'w-[15%]' : 'w-[18%]' }}">
                            Status</th>
                        <th class="py-3 px-4 text-center w-[15%]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse($dataMasuk as $index => $presensi)
                        <tr>
                            <td class="py-2 px-4 text-center">{{ $index + 1 }}.</td>
                            <td class="py-2 px-4 font-medium">{{ $presensi->nama_lengkap }}</td>
                            @if($shiftTerpilih == 'semua')
                                <td class="py-2 px-4 text-center">
                                    @if($presensi->jenis_shift == 1)
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
                            <td colspan="7" class="py-4 px-4 text-center text-gray-500">
                                Tidak ada data presensi masuk pada tanggal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- CARD LAYOUT (Mobile) - MASUK --}}
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
                                @if($presensi->jenis_shift == 1)
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
                                    @if($presensi->jenis_shift == 1)
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
                    Showing {{ $dataMasuk->firstItem() ?? 0 }} to {{ $dataMasuk->lastItem() ?? 0 }} of
                    {{ $dataMasuk->total() }} entries
                </div>
                <div class="flex items-center gap-1">
                    @if ($dataMasuk->onFirstPage())
                        <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Previous</span>
                    @else
                        <a href="{{ $dataMasuk->appends(request()->query())->previousPageUrl() }}"
                            class="px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Previous</a>
                    @endif
                    @foreach(range(1, $dataMasuk->lastPage()) as $page)
                        @if($page == $dataMasuk->currentPage())
                            <span class="px-3 py-1 text-white bg-[#1e3a5f] rounded font-bold">{{ $page }}</span>
                        @else
                            <a href="{{ $dataMasuk->appends(request()->query())->url($page) }}"
                                class="px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">{{ $page }}</a>
                        @endif
                    @endforeach
                    @if ($dataMasuk->hasMorePages())
                        <a href="{{ $dataMasuk->appends(request()->query())->nextPageUrl() }}"
                            class="px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Next</a>
                    @else
                        <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Next</span>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>


        {{-- DAFTAR PRESENSI PULANG --}}
        {{-- DAFTAR PRESENSI PULANG --}}
<div class="bg-white rounded-lg shadow-md overflow-hidden mb-6" x-data="{ showPulang: true }">
    <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] p-3 border-b border-[#2a4a6f] cursor-pointer hover:bg-[#2a4a6f] transition"
        @click="showPulang = !showPulang">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                <h3 class="font-bold text-white">DAFTAR PRESENSI PULANG</h3>
            </div>
            <svg class="w-5 h-5 text-white transition-transform" :class="{ 'rotate-180': !showPulang }"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </div>
    </div>

    <div x-show="showPulang" x-collapse>

        {{-- TABEL (Desktop) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full min-w-max table-fixed">
                <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500">
                    <tr>
                        <th class="py-3 px-4 text-center {{ $shiftTerpilih == 'semua' ? 'w-[5%]' : 'w-[8%]' }}">No
                        </th>
                        <th class="py-3 px-4 text-center {{ $shiftTerpilih == 'semua' ? 'w-[25%]' : 'w-[28%]' }}">
                            Nama</th>
                        @if($shiftTerpilih == 'semua')
                            <th class="py-3 px-4 text-center w-[15%]">Jenis Shift</th>
                        @endif
                        <th class="py-3 px-4 text-center {{ $shiftTerpilih == 'semua' ? 'w-[15%]' : 'w-[18%]' }}">
                            Waktu</th>
                        <th class="py-3 px-4 text-center {{ $shiftTerpilih == 'semua' ? 'w-[10%]' : 'w-[13%]' }}">
                            Foto</th>
                        <th class="py-3 px-4 text-center {{ $shiftTerpilih == 'semua' ? 'w-[15%]' : 'w-[18%]' }}">
                            Status</th>
                        <th class="py-3 px-4 text-center w-[15%]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse($dataPulang as $index => $presensi)
                        <tr>
                            <td class="py-2 px-4 text-center">{{ $index + 1 }}.</td>
                            <td class="py-2 px-4 font-medium">{{ $presensi->nama_lengkap }}</td>
                            @if($shiftTerpilih == 'semua')
                                <td class="py-2 px-4 text-center">
                                    @if($presensi->jenis_shift == 1)
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
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828zM5 12V7a2 2 0 012-2h2.586l-4 4H5zM3 15a2 2 0 00-2 2v2h16v-2a2 2 0 00-2-2H3z">
                                            </path>
                                        </svg>
                                    </button>
                                    <button
                                        @click.prevent="showDeleteModal = true; deleteAction = '{{ route('komandan.presensi.destroy', $presensi->id_presensi) }}'"
                                        class="text-red-500 hover:text-red-700" title="Hapus">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-4 px-4 text-center text-gray-500">
                                Tidak ada data presensi pulang pada tanggal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- CARD LAYOUT (Mobile) - PULANG --}}
<div class="md:hidden space-y-2 p-3">
    @forelse($dataPulang as $index => $presensi)
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
                                @if($presensi->jenis_shift == 1)
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
                                    @if($presensi->jenis_shift == 1)
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
            <p class="text-gray-500 text-sm font-semibold">Tidak ada data presensi pulang pada tanggal ini.</p>
        </div>
    @endforelse
</div>



        {{-- Pagination Pulang --}}
        @if($dataPulang->total() > 0)
            <div class="flex justify-between items-center px-6 py-4 border-t border-gray-200">
                <div class="text-sm text-gray-600">
                    Showing {{ $dataPulang->firstItem() ?? 0 }} to {{ $dataPulang->lastItem() ?? 0 }} of
                    {{ $dataPulang->total() }} entries
                </div>
                <div class="flex items-center gap-1">
                    @if ($dataPulang->onFirstPage())
                        <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Previous</span>
                    @else
                        <a href="{{ $dataPulang->appends(request()->query())->previousPageUrl() }}"
                            class="px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Previous</a>
                    @endif
                    @foreach(range(1, $dataPulang->lastPage()) as $page)
                        @if($page == $dataPulang->currentPage())
                            <span class="px-3 py-1 text-white bg-[#1e3a5f] rounded font-bold">{{ $page }}</span>
                        @else
                            <a href="{{ $dataPulang->appends(request()->query())->url($page) }}"
                                class="px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">{{ $page }}</a>
                        @endif
                    @endforeach
                    @if ($dataPulang->hasMorePages())
                        <a href="{{ $dataPulang->appends(request()->query())->nextPageUrl() }}"
                            class="px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Next</a>
                    @else
                        <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Next</span>
                    @endif
                </div>
            </div>
        @endif
        </div>
    </div>

    
    {{-- MODAL PENGATURAN RULE --}}
    <div x-show="showRuleModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
        @click.away="showRuleModal = false"
        style="display: none;">
        
        {{-- Container Modal dengan max-height dan overflow --}}
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden relative" @click.stop>
            
            {{-- Header Modal (Fixed) --}}
            <div class="bg-[#1e3a5f] py-4 px-6 border-b border-[#1e3a5f] flex justify-between items-center flex-shrink-0">
                <h3 class="text-lg font-bold text-white flex items-center tracking-wide">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                    </svg>
                    PENGATURAN JAM SHIFT
                </h3>
                <button @click="showRuleModal = false" class="text-white/70 hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Form dengan Scrollable Body --}}
            <form action="{{ route('komandan.presensi.updateRules') }}" method="POST" class="flex flex-col flex-1 overflow-hidden">
                @csrf
                @method('PUT')
                
                {{-- Body Modal (Scrollable) --}}
                <div class="overflow-y-auto flex-1 p-6">
                    
                    {{-- Grid Shift --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        
                        {{-- SHIFT PAGI --}}
                        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                            <h4 class="font-bold text-yellow-800 mb-3 border-b border-yellow-200 pb-2 text-center">SHIFT PAGI</h4>
                            <div class="space-y-3">
                                <div>
                                    <label class="text-xs font-bold text-gray-600 block mb-1">JAM MASUK</label>
                                    <input type="time" name="masuk_pagi" required 
                                        value="{{ $rules->firstWhere('jenis_shift', 'Pagi')->jam_masuk ?? '07:00' }}"
                                        class="w-full border-gray-300 rounded focus:ring-yellow-500 focus:border-yellow-500 text-sm">
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-gray-600 block mb-1">JAM PULANG</label>
                                    <input type="time" name="keluar_pagi" required 
                                        value="{{ $rules->firstWhere('jenis_shift', 'Pagi')->jam_keluar ?? '19:00' }}"
                                        class="w-full border-gray-300 rounded focus:ring-yellow-500 focus:border-yellow-500 text-sm">
                                </div>
                            </div>
                        </div>

                        {{-- SHIFT MALAM --}}
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                            <h4 class="font-bold text-blue-800 mb-3 border-b border-blue-200 pb-2 text-center">SHIFT MALAM</h4>
                            <div class="space-y-3">
                                <div>
                                    <label class="text-xs font-bold text-gray-600 block mb-1">JAM MASUK</label>
                                    <input type="time" name="masuk_malam" required 
                                        value="{{ $rules->firstWhere('jenis_shift', 'Malam')->jam_masuk ?? '19:00' }}"
                                        class="w-full border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500 text-sm">
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-gray-600 block mb-1">JAM PULANG</label>
                                    <input type="time" name="keluar_malam" required 
                                        value="{{ $rules->firstWhere('jenis_shift', 'Malam')->jam_keluar ?? '07:00' }}"
                                        class="w-full border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500 text-sm">
                                </div>
                            </div>
                        </div>

                        {{-- NON SHIFT --}}
                        <div class="bg-gray-100 p-4 rounded-lg border border-gray-300">
                            <h4 class="font-bold text-gray-700 mb-3 border-b border-gray-300 pb-2 text-center">NON SHIFT</h4>
                            <div class="space-y-3">
                                <div>
                                    <label class="text-xs font-bold text-gray-600 block mb-1">JAM MASUK</label>
                                    <input type="time" name="masuk_non" required 
                                        value="{{ $rules->firstWhere('jenis_shift', 'Non Shift')->jam_masuk ?? '07:00' }}"
                                        class="w-full border-gray-300 rounded focus:ring-gray-500 focus:border-gray-500 text-sm">
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-gray-600 block mb-1">JAM PULANG</label>
                                    <input type="time" name="keluar_non" required 
                                        value="{{ $rules->firstWhere('jenis_shift', 'Non Shift')->jam_keluar ?? '17:00' }}"
                                        class="w-full border-gray-300 rounded focus:ring-gray-500 focus:border-gray-500 text-sm">
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- PENGATURAN GLOBAL --}}
                    <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                        <h4 class="font-bold text-slate-700 mb-3 text-center text-sm uppercase">Pengaturan Umum</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            
                            {{-- Toleransi --}}
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase">Toleransi Keterlambatan</label>
                                <div class="flex items-center gap-2">
                                    <input type="number" name="toleransi" required min="0" value="{{ $globalToleransi }}"
                                        class="w-full border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500 text-sm h-[30px]">
                                    <div class="bg-gray-200 border border-gray-300 rounded px-3 h-[30px] flex items-center text-gray-600 text-xs font-bold">
                                        Menit
                                    </div>
                                </div>
                            </div>

                            {{-- Presensi Dibuka --}}
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase">Presensi Dibuka</label>
                                <div class="flex items-center gap-2">
                                    <input type="number" name="dibuka" required min="0" value="{{ $globalDibuka }}"
                                        class="w-full border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500 text-sm h-[30px]">
                                    <div class="bg-gray-200 border border-gray-300 rounded px-3 h-[30px] flex items-center text-gray-600 text-xs font-bold">
                                        Menit
                                    </div>
                                </div>
                            </div>

                            {{-- Toggle GPS --}}
                            <div class="flex flex-col justify-center">
                                <label class="block text-[10px] font-bold text-gray-500 mb-2 uppercase text-center">Wajib Lokasi (GPS)</label>
                                <label class="relative inline-flex items-center cursor-pointer mx-auto">
                                    <input type="hidden" name="isgeotagenabled" value="0">
                                    <input type="checkbox" name="isgeotagenabled" value="1" class="sr-only peer" 
                                        @if($rules->firstWhere('jenis_shift', 'Pagi')->is_geotag_enabled) checked @endif>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    <span class="ml-3 text-xs font-bold text-gray-700 peer-checked:text-blue-600">AKTIF</span>
                                </label>
                            </div>

                        </div>
                    </div>

                </div>

                {{-- Footer (Fixed) --}}
                <div class="flex justify-end gap-3 pt-4 border-t bg-gray-50 p-4 flex-shrink-0">
                    <button type="button" @click="showRuleModal = false" 
                            class="px-6 py-2.5 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 font-semibold transition">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-6 py-2.5 bg-[#1e3a5f] text-white rounded-xl hover:bg-[#2a4a6f] font-bold shadow-lg transition transform hover:-translate-y-0.5">
                        SIMPAN PERUBAHAN
                    </button>
                </div>

            </form>
        </div>
    </div>


    {{-- MODAL EDIT --}}
    <div x-show="showEditModal"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
         @click.away="showEditModal = false"
         style="display: none;">
        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full relative overflow-hidden" @click.stop>
            {{-- Header Biru --}}
            <div class="bg-[#1e3a5f] py-4 px-6 border-b border-[#1e3a5f] flex justify-between items-center">
                <h3 class="text-lg font-bold text-white flex items-center tracking-wide">
                    EDIT PRESENSI
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
                        
                        {{-- GROUP: Informasi Presensi --}}
                        <div class="p-4 bg-blue-50/50 rounded-xl border border-blue-100">                            
                            <div class="space-y-4">
                                {{-- Waktu --}}
                                <div>
                                    <label for="waktu" class="block text-xs font-bold text-[#1e3a5f] uppercase tracking-wide mb-1">Waktu <span class="text-red-500">*</span></label>
                                    <div class="relative cursor-pointer" @click="$refs.waktuInput.showPicker()">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-[#1e3a5f]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <input type="datetime-local" id="waktu" name="waktu" x-ref="waktuInput"
                                               x-model="editWaktu" 
                                               :min="editMin" 
                                               :max="editMax"
                                               @input="calculateStatus()" required
                                               class="pl-10 w-full bg-white border border-gray-300 text-gray-800 text-sm font-medium rounded-lg shadow-sm focus:ring-[#1e3a5f] focus:border-[#1e3a5f] block p-2.5 cursor-pointer">
                                    </div>
                                </div>
                                
                                {{-- Status --}}
                                <div>
                                    <label for="status" class="block text-xs font-bold text-[#1e3a5f] uppercase tracking-wide mb-1">Status <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-[#1e3a5f]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <select id="status" name="status" x-model="editStatus" required
                                                class="pl-10 pr-10 w-full bg-white border border-gray-300 text-[#1e3a5f] text-sm font-bold rounded-lg shadow-sm focus:ring-[#1e3a5f] focus:border-[#1e3a5f] block p-2.5 cursor-pointer appearance-none">
                                            <option value="tepat waktu">Tepat Waktu</option>
                                            <option value="terlambat">Terlambat</option>
                                            <option value="terlalu cepat">Terlalu Cepat</option>
                                            <option value="izin">Izin</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                {{-- Jenis Presensi --}}
                                <div>
                                    <label for="jenis_presensi" class="block text-xs font-bold text-[#1e3a5f] uppercase tracking-wide mb-1">Jenis Presensi <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-[#1e3a5f]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                        </div>
                                        <select id="jenis_presensi" name="jenis_presensi" x-model="editJenisPresensi" @change="calculateStatus()" required
                                                class="pl-10 pr-10 w-full bg-white border border-gray-300 text-[#1e3a5f] text-sm font-bold rounded-lg shadow-sm focus:ring-[#1e3a5f] focus:border-[#1e3a5f] block p-2.5 cursor-pointer appearance-none">
                                            <option value="Masuk">Masuk</option>
                                            <option value="Pulang">Pulang</option>
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
                    </div>
                </div>

            {{-- Container Modal dengan max-height dan overflow --}}
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden relative"
                @click.stop>

                {{-- Header Modal (Fixed) --}}
                <div
                    class="bg-[#1e3a5f] py-4 px-6 border-b border-[#1e3a5f] flex justify-between items-center flex-shrink-0">
                    <h3 class="text-lg font-bold text-white flex items-center tracking-wide">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4">
                            </path>
                        </svg>
                        PENGATURAN JAM SHIFT
                    </h3>
                    <button @click="showRuleModal = false" class="text-white/70 hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>

                {{-- Form dengan Scrollable Body --}}
                <form action="{{ route('komandan.presensi.updateRules') }}" method="POST"
                    class="flex flex-col flex-1 overflow-hidden">
                    @csrf
                    @method('PUT')

                    {{-- Body Modal (Scrollable) --}}
                    <div class="overflow-y-auto flex-1 p-6">

                        {{-- Grid Shift --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

                            {{-- SHIFT PAGI --}}
                            <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                                <h4 class="font-bold text-yellow-800 mb-3 border-b border-yellow-200 pb-2 text-center">SHIFT
                                    PAGI</h4>
                                <div class="space-y-3">
                                    <div>
                                        <label class="text-xs font-bold text-gray-600 block mb-1">JAM MASUK</label>
                                        <input type="time" name="masuk_pagi" required
                                            value="{{ $rules->firstWhere('jenis_shift', 'Pagi')->jam_masuk ?? '07:00' }}"
                                            class="w-full border-gray-300 rounded focus:ring-yellow-500 focus:border-yellow-500 text-sm">
                                    </div>
                                    <div>
                                        <label class="text-xs font-bold text-gray-600 block mb-1">JAM PULANG</label>
                                        <input type="time" name="keluar_pagi" required
                                            value="{{ $rules->firstWhere('jenis_shift', 'Pagi')->jam_keluar ?? '19:00' }}"
                                            class="w-full border-gray-300 rounded focus:ring-yellow-500 focus:border-yellow-500 text-sm">
                                    </div>
                                </div>
                            </div>

                            {{-- SHIFT MALAM --}}
                            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                                <h4 class="font-bold text-blue-800 mb-3 border-b border-blue-200 pb-2 text-center">SHIFT
                                    MALAM</h4>
                                <div class="space-y-3">
                                    <div>
                                        <label class="text-xs font-bold text-gray-600 block mb-1">JAM MASUK</label>
                                        <input type="time" name="masuk_malam" required
                                            value="{{ $rules->firstWhere('jenis_shift', 'Malam')->jam_masuk ?? '19:00' }}"
                                            class="w-full border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500 text-sm">
                                    </div>
                                    <div>
                                        <label class="text-xs font-bold text-gray-600 block mb-1">JAM PULANG</label>
                                        <input type="time" name="keluar_malam" required
                                            value="{{ $rules->firstWhere('jenis_shift', 'Malam')->jam_keluar ?? '07:00' }}"
                                            class="w-full border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500 text-sm">
                                    </div>
                                </div>
                            </div>

                            {{-- NON SHIFT --}}
                            <div class="bg-gray-100 p-4 rounded-lg border border-gray-300">
                                <h4 class="font-bold text-gray-700 mb-3 border-b border-gray-300 pb-2 text-center">NON SHIFT
                                </h4>
                                <div class="space-y-3">
                                    <div>
                                        <label class="text-xs font-bold text-gray-600 block mb-1">JAM MASUK</label>
                                        <input type="time" name="masuk_non" required
                                            value="{{ $rules->firstWhere('jenis_shift', 'Non Shift')->jam_masuk ?? '07:00' }}"
                                            class="w-full border-gray-300 rounded focus:ring-gray-500 focus:border-gray-500 text-sm">
                                    </div>
                                    <div>
                                        <label class="text-xs font-bold text-gray-600 block mb-1">JAM PULANG</label>
                                        <input type="time" name="keluar_non" required
                                            value="{{ $rules->firstWhere('jenis_shift', 'Non Shift')->jam_keluar ?? '17:00' }}"
                                            class="w-full border-gray-300 rounded focus:ring-gray-500 focus:border-gray-500 text-sm">
                                    </div>
                                </div>
                            </div>

                        </div>

                        {{-- PENGATURAN GLOBAL --}}
                        <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                            <h4 class="font-bold text-slate-700 mb-3 text-center text-sm uppercase">Pengaturan Umum</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                                {{-- Toleransi --}}
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase">Toleransi
                                        Keterlambatan</label>
                                    <div class="flex items-center gap-2">
                                        <input type="number" name="toleransi" required min="0"
                                            value="{{ $globalToleransi }}"
                                            class="w-full border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500 text-sm h-[30px]">
                                        <div
                                            class="bg-gray-200 border border-gray-300 rounded px-3 h-[30px] flex items-center text-gray-600 text-xs font-bold">
                                            Menit
                                        </div>
                                    </div>
                                </div>

                                {{-- Presensi Dibuka --}}
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase">Presensi
                                        Dibuka</label>
                                    <div class="flex items-center gap-2">
                                        <input type="number" name="dibuka" required min="0" value="{{ $globalDibuka }}"
                                            class="w-full border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500 text-sm h-[30px]">
                                        <div
                                            class="bg-gray-200 border border-gray-300 rounded px-3 h-[30px] flex items-center text-gray-600 text-xs font-bold">
                                            Menit
                                        </div>
                                    </div>
                                </div>

                                {{-- Toggle GPS --}}
                                <div class="flex flex-col justify-center">
                                    <label
                                        class="block text-[10px] font-bold text-gray-500 mb-2 uppercase text-center">Wajib
                                        Lokasi (GPS)</label>
                                    <label class="relative inline-flex items-center cursor-pointer mx-auto">
                                        <input type="hidden" name="isgeotagenabled" value="0">
                                        <input type="checkbox" name="isgeotagenabled" value="1" class="sr-only peer"
                                            @if($rules->firstWhere('jenis_shift', 'Pagi')->isgeotagenabled) checked @endif>
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                                        </div>
                                        <span
                                            class="ml-3 text-xs font-bold text-gray-700 peer-checked:text-blue-600">AKTIF</span>
                                    </label>
                                </div>

                            </div>
                        </div>

                    </div>

                    {{-- Footer (Fixed) --}}
                    <div class="flex justify-end gap-3 pt-4 border-t bg-gray-50 p-4 flex-shrink-0">
                        <button type="button" @click="showRuleModal = false"
                            class="px-6 py-2.5 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 font-semibold transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-6 py-2.5 bg-[#1e3a5f] text-white rounded-xl hover:bg-[#2a4a6f] font-bold shadow-lg transition transform hover:-translate-y-0.5">
                            SIMPAN PERUBAHAN
                        </button>
                    </div>

                </form>
            </div>
        </div>
        
        {{-- MODAL FOTO --}}
        <div x-show="showPhotoModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
            @click.away="showPhotoModal = false" style="display: none;">
            <div class="bg-white rounded-xl shadow-xl max-w-lg w-full relative overflow-hidden" @click.stop>
                {{-- Header Biru --}}
                <div class="bg-[#1e3a5f] py-4 px-6 border-b border-[#1e3a5f] flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white flex items-center tracking-wide">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        FOTO PRESENSI
                    </h3>
                    <button @click="showPhotoModal = false" class="text-white/70 hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>
                <div class="p-4">
                    <img :src="photoUrl" alt="Foto Presensi" class="w-full h-auto rounded">
                </div>
            </div>
        </div>

        {{-- MODAL EDIT --}}
        <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
            @click.away="showEditModal = false" style="display: none;">
            <div class="bg-white rounded-xl shadow-xl max-w-lg w-full relative overflow-hidden" @click.stop>
                {{-- Header Biru --}}
                <div class="bg-[#1e3a5f] py-4 px-6 border-b border-[#1e3a5f] flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white flex items-center tracking-wide">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                            </path>
                        </svg>
                        EDIT PRESENSI
                    </h3>
                    <button @click="showEditModal = false" class="text-white/70 hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>

                <form :action="editAction" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body max-h-[70vh] overflow-y-auto p-6">
                        <div class="space-y-5">

                            {{-- GROUP: Informasi Presensi --}}
                            <div class="p-4 bg-blue-50/50 rounded-xl border border-blue-100">
                                <div class="space-y-4">
                                    {{-- Waktu --}}
                                    <div>
                                        <label for="waktu"
                                            class="block text-xs font-bold text-[#1e3a5f] uppercase tracking-wide mb-1">Waktu
                                            <span class="text-red-500">*</span></label>
                                        <div class="relative">
                                            <div
                                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <svg class="h-5 w-5 text-[#1e3a5f]" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                            <input type="datetime-local" id="waktu" name="waktu" x-model="editWaktu"
                                                :min="editMin" :max="editMax" @input="calculateStatus()" required
                                                class="pl-10 w-full bg-white border border-gray-300 text-gray-800 text-sm font-medium rounded-lg shadow-sm focus:ring-[#1e3a5f] focus:border-[#1e3a5f] block p-2.5">
                                        </div>
                                    </div>

                                    {{-- Status --}}
                                    <div>
                                        <label for="status"
                                            class="block text-xs font-bold text-[#1e3a5f] uppercase tracking-wide mb-1">Status
                                            <span class="text-red-500">*</span></label>
                                        <div class="relative">
                                            <div
                                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <svg class="h-5 w-5 text-[#1e3a5f]" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                            <select id="status" name="status" x-model="editStatus" required
                                                class="pl-10 w-full bg-white border border-gray-300 text-[#1e3a5f] text-sm font-bold rounded-lg shadow-sm focus:ring-[#1e3a5f] focus:border-[#1e3a5f] block p-2.5 cursor-pointer">
                                                <option value="tepat waktu">Tepat Waktu</option>
                                                <option value="terlambat">Terlambat</option>
                                                <option value="terlalu cepat">Terlalu Cepat</option>
                                                <option value="izin">Izin</option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Jenis Presensi --}}
                                    <div>
                                        <label for="jenis_presensi"
                                            class="block text-xs font-bold text-[#1e3a5f] uppercase tracking-wide mb-1">Jenis
                                            Presensi <span class="text-red-500">*</span></label>
                                        <div class="relative">
                                            <div
                                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <svg class="h-5 w-5 text-[#1e3a5f]" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                                    </path>
                                                </svg>
                                            </div>
                                            <select id="jenis_presensi" name="jenis_presensi" x-model="editJenisPresensi"
                                                @change="calculateStatus()" required
                                                class="pl-10 w-full bg-white border border-gray-300 text-[#1e3a5f] text-sm font-bold rounded-lg shadow-sm focus:ring-[#1e3a5f] focus:border-[#1e3a5f] block p-2.5 cursor-pointer">
                                                <option value="Masuk">Masuk</option>
                                                <option value="Pulang">Pulang</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer p-4 border-t bg-gray-50">
                        <button type="submit"
                            class="w-full px-4 py-3 text-white font-bold bg-[#1e3a5f] rounded-xl hover:bg-[#2a4a6f] shadow-lg transition transform hover:-translate-y-0.5">
                            SIMPAN PERUBAHAN
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL HAPUS --}}
        <div x-show="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
            @click.away="showDeleteModal = false" style="display: none;">
            <div class="bg-white rounded-lg shadow-xl max-w-sm w-full p-6 relative" @click.stop>
                <h3 class="text-lg font-bold text-gray-900 mb-4">Konfirmasi Hapus</h3>
                <p class="text-gray-600 mb-6">
                    Apakah Anda yakin ingin menghapus data presensi ini? Tindakan ini tidak dapat dibatalkan.
                </p>
                <form :action="deleteAction" method="POST" class="flex justify-end space-x-4">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="showDeleteModal = false"
                        class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300">
                        Batal
                    </button>
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>

    </div>

    <<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('presensiData', () => ({
            showPhotoModal: false, 
            photoUrl: '', 
            showEditModal: false, 
            editAction: '',
            editWaktu: '',
            editMin: '', // Constraint start
            editMax: '', // Constraint end
            editStatus: '',
            editJenisPresensi: '',
            editShiftId: '',
            showDeleteModal: false,
            deleteAction: '',
            showRuleModal: false,
            rules: @json($rules),

            calculateStatus() {
                if (!this.editWaktu || !this.editShiftId || !this.editJenisPresensi) return;
                
                // Mapping ID ke Nama
                let namaShift = '';
                if (this.editShiftId == 1) namaShift = 'Pagi';
                else if (this.editShiftId == 2) namaShift = 'Malam';
                else return; 
                
                let rule = this.rules.find(r => r.jenis_shift === namaShift);
                if (!rule) return;

                // Parse Waktu (YYYY-MM-DDTHH:mm)
                let timeString = this.editWaktu.split('T')[1]; 
                if(!timeString) return;
                
                if(timeString.length > 5) timeString = timeString.substring(0, 5);
                let timeValues = timeString.split(':');
                let editTimeVal = parseInt(timeValues[0]) * 60 + parseInt(timeValues[1]);

                if (this.editJenisPresensi === 'Masuk') {
                    // Logic Masuk
                    let jamMasuk = rule.jam_masuk.substring(0, 5);
                    let masukValues = jamMasuk.split(':');
                    let ruleMasukVal = parseInt(masukValues[0]) * 60 + parseInt(masukValues[1]);
                    
                    if (editTimeVal > ruleMasukVal) {
                        this.editStatus = 'terlambat';
                    } else if (editTimeVal < ruleMasukVal) {
                        this.editStatus = 'terlalu cepat';
                    } else {
                        this.editStatus = 'tepat waktu';
                    }

                } else if (this.editJenisPresensi === 'Pulang') {
                    // Logic Pulang
                    let jamKeluar = rule.jam_keluar.substring(0, 5);
                    let keluarValues = jamKeluar.split(':');
                    let ruleKeluarVal = parseInt(keluarValues[0]) * 60 + parseInt(keluarValues[1]);
                    
                    let isMalam = (namaShift === 'Malam');
                    
                    if (isMalam) {
                         if (editTimeVal < ruleKeluarVal) {
                            this.editStatus = 'terlalu cepat'; 
                        } else {
                            this.editStatus = 'tepat waktu';
                        }
                    } else {
                        // Shift Pagi Pulang jam 19:00.
                        if (editTimeVal < ruleKeluarVal) {
                             this.editStatus = 'terlalu cepat';
                        } else {
                            this.editStatus = 'tepat waktu';
                        }
                    }
                }
            }
        }))
    })
</script>
@endsection