@extends('layouts.app')

{{-- Tombol KEMBALI --}}
@section('header-left')
    <a class="inline-block border-2 border-[#1a2847] text-[#1a2847] text-sm font-bold px-4 py-1 rounded-full mb-4">
        PRESENSI
    </a>
@endsection

@section('content')
<div class="w-full mx-auto"
     x-data="{ 
         showPhotoModal: false, 
         photoUrl: '', 
         showEditModal: false, 
         editAction: '',
         editWaktu: '',
         editStatus: '',
         editJenisPresensi: '',
         showDeleteModal: false,
         deleteAction: '',
         showRuleModal: false
     }">
    
    <h2 class="text-2xl font-bold text-slate-800 mb-4">Laporan Presensi Anggota</h2>

    {{-- Tampilkan Notifikasi --}}
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
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Oops! Terjadi kesalahan:</strong>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Filter --}}
    <form action="{{ route('komandan.presensi') }}" method="GET">
        <div class="bg-white p-4 rounded-lg shadow-md mb-6">
            <div class="flex flex-col sm:flex-row sm:items-end sm:space-x-4 space-y-4 sm:space-y-0">
                <div class="flex-1">
                    <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">TANGGAL:</label>
                    <input type="date" id="tanggal" name="tanggal" 
                           onchange="this.form.submit()"
                           class="w-full bg-[#2a4a6f] text-white px-4 py-2 rounded-lg shadow border-none focus:outline-none focus:ring-2 focus:ring-blue-400" 
                           style="color-scheme: dark;"
                           value="{{ $tanggalTerpilih }}">
                </div>
                <div class="flex-1">
                    <label for="shift" class="block text-sm font-medium text-gray-700 mb-1">JENIS SHIFT:</label>
                    <select id="shift" name="shift" 
                            onchange="this.form.submit()"
                            class="w-full bg-[#2a4a6f] text-white px-4 py-2 rounded-lg shadow border-none focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="semua" @if($shiftTerpilih == 'semua') selected @endif>Semua Shift</option>
                        <option value="Pagi" @if($shiftTerpilih == 'Pagi') selected @endif>Shift Pagi</option>
                        <option value="Malam" @if($shiftTerpilih == 'Malam') selected @endif>Shift Malam</option>
                        <option value="Non Shift" @if($shiftTerpilih == 'Non Shift') selected @endif>Non Shift</option>
                        <option value="Off" @if($shiftTerpilih == 'Off') selected @endif>Off</option>
                    </select>
                </div>
            </div>
        </div>
    </form>

    {{-- DAFTAR PRESENSI MASUK --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="bg-gray-100 p-3 border-b border-gray-200">
            <h3 class="font-bold text-gray-800">DAFTAR PRESENSI MASUK</h3>
        </div>
        
        {{-- TABEL (Desktop) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full min-w-max">
                <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500">
                    <tr>
                        <th class="py-3 px-4 text-left w-16">No</th>
                        <th class="py-3 px-4 text-center w-25">Nama</th>
                        <th class="py-3 px-4 text-left w-25">Waktu</th>
                        <th class="py-3 px-4 text-center w-25">Foto</th>
                        <th class="py-3 px-4 text-center w-25">Status</th>
                        @if(Auth::user()->peran == 'komandan')
                            <th class="py-3 px-4 text-center w-28">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse($dataMasuk as $index => $presensi)
                    <tr>
                        <td class="py-2 px-4">{{ $index + 1 }}.</td>
                        <td class="py-2 px-4 font-medium">{{ $presensi->nama_lengkap }}</td>
                        <td class="py-2 px-4">{{ $presensi->waktu->format('H:i:s') }}</td>
                        <td class="py-2 px-4 text-center">
                            <button @click="showPhotoModal = true; photoUrl = '{{ asset('storage/' . $presensi->foto) }}'" class="text-blue-500 hover:underline">
                                Buka
                            </button>
                        </td>
                        <td class="py-2 px-4">
                            @if($presensi->status == 'tepat waktu')
                                <span class="text-green-600 font-semibold">Tepat Waktu</span>
                            @elseif($presensi->status == 'terlambat')
                                <span class="text-red-500 font-semibold">Terlambat</span>
                            @else
                                <span class="text-yellow-500 font-semibold">{{ ucfirst($presensi->status) }}</span>
                            @endif
                        </td>
                        @if(Auth::user()->peran == 'komandan')
                            <td class="py-2 px-4">
                                <div class="flex justify-center space-x-3">
                                    <button @click="
                                        showEditModal = true; 
                                        editAction = '{{ route('komandan.presensi.update', $presensi->id_presensi) }}';
                                        editWaktu = '{{ $presensi->waktu->format('Y-m-d\TH:i') }}';
                                        editStatus = '{{ $presensi->status }}';
                                        editJenisPresensi = '{{ $presensi->jenis_presensi }}';
                                    " class="text-blue-500 hover:text-blue-700" title="Edit">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828zM5 12V7a2 2 0 012-2h2.586l-4 4H5zM3 15a2 2 0 00-2 2v2h16v-2a2 2 0 00-2-2H3z"></path></svg>
                                    </button>
                                    <button @click.prevent="showDeleteModal = true; deleteAction = '{{ route('komandan.presensi.destroy', $presensi->id_presensi) }}'" class="text-red-500 hover:text-red-700" title="Hapus">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                    </button>
                                </div>
                            </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ Auth::user()->peran == 'komandan' ? '6' : '5' }}" class="py-4 px-4 text-center text-gray-500">
                            Tidak ada data presensi masuk pada tanggal ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- CARD LAYOUT (Mobile) --}}
        <div class="md:hidden space-y-3 p-3">
            @forelse($dataMasuk as $index => $presensi)
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                    {{-- Header --}}
                    <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] px-4 py-2.5 flex justify-between items-center">
                        <div>
                            <p class="text-xs text-blue-200 font-semibold uppercase">Nama Anggota</p>
                            <p class="text-white font-bold text-base">{{ $presensi->nama_lengkap }}</p>
                        </div>
                        @if($presensi->status == 'tepat waktu')
                            <span class="bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg uppercase">Tepat Waktu</span>
                        @elseif($presensi->status == 'terlambat')
                            <span class="bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg uppercase">Telat</span>
                        @else
                            <span class="bg-yellow-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg uppercase">{{ ucfirst($presensi->status) }}</span>
                        @endif
                    </div>

                    {{-- Body --}}
                    <div class="p-4 space-y-3">
                        <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <p class="text-[10px] text-gray-500 font-semibold uppercase">Waktu</p>
                                    <p class="text-gray-800 font-bold text-base">{{ $presensi->waktu->format('H:i:s') }}</p>
                                </div>
                            </div>
                            <button @click="showPhotoModal = true; photoUrl = '{{ asset('storage/' . $presensi->foto) }}'" class="text-blue-500 hover:text-blue-700 font-semibold text-sm underline">
                                Lihat Foto
                            </button>
                        </div>

                        @if(Auth::user()->peran == 'komandan')
                            <div class="flex gap-2 pt-2">
                                <button @click="
                                    showEditModal = true; 
                                    editAction = '{{ route('komandan.presensi.update', $presensi->id_presensi) }}';
                                    editWaktu = '{{ $presensi->waktu->format('Y-m-d\TH:i') }}';
                                    editStatus = '{{ $presensi->status }}';
                                    editJenisPresensi = '{{ $presensi->jenis_presensi }}';
                                " class="flex-1 bg-blue-500 text-white font-bold py-2 rounded-lg hover:bg-blue-600 transition flex items-center justify-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828zM5 12V7a2 2 0 012-2h2.586l-4 4H5zM3 15a2 2 0 00-2 2v2h16v-2a2 2 0 00-2-2H3z"></path></svg>
                                    <span class="text-xs">Edit</span>
                                </button>
                                <button @click.prevent="showDeleteModal = true; deleteAction = '{{ route('komandan.presensi.destroy', $presensi->id_presensi) }}'" class="flex-1 bg-red-500 text-white font-bold py-2 rounded-lg hover:bg-red-600 transition flex items-center justify-center gap-1">
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
                    <p class="text-gray-500 font-semibold">Tidak ada data presensi masuk pada tanggal ini.</p>
                </div>
            @endforelse
        </div>
    </div>
    
    {{-- DAFTAR PRESENSI PULANG --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="bg-gray-100 p-3 border-b border-gray-200">
            <h3 class="font-bold text-gray-800">DAFTAR PRESENSI PULANG</h3>
        </div>
        
        {{-- TABEL (Desktop) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full min-w-max">
                <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500">
                    <tr>
                        <th class="py-3 px-4 text-left w-16">No</th>
                        <th class="py-3 px-4 text-center w-25">Nama</th>
                        <th class="py-3 px-4 text-left w-25">Waktu</th>
                        <th class="py-3 px-4 text-center w-25">Foto</th>
                        <th class="py-3 px-4 text-center w-25">Status</th>
                        @if(Auth::user()->peran == 'komandan')
                            <th class="py-3 px-4 text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse($dataPulang as $index => $presensi)
                    <tr>
                        <td class="py-2 px-4">{{ $index + 1 }}.</td>
                        <td class="py-2 px-4 font-medium">{{ $presensi->nama_lengkap }}</td>
                        <td class="py-2 px-4">{{ $presensi->waktu->format('H:i:s') }}</td>
                        <td class="py-2 px-4 text-center">
                            <button @click="showPhotoModal = true; photoUrl = '{{ asset('storage/' . $presensi->foto) }}'" class="text-blue-500 hover:underline">
                                Buka
                            </button>
                        </td>
                        <td class="py-2 px-4">
                            @if($presensi->status == 'tepat waktu')
                                <span class="text-green-600 font-semibold">Tepat Waktu</span>
                            @elseif($presensi->status == 'terlambat')
                                <span class="text-red-500 font-semibold">Terlambat</span>
                            @else
                                <span class="text-yellow-500 font-semibold">{{ ucfirst($presensi->status) }}</span>
                            @endif
                        </td>
                        @if(Auth::user()->peran == 'komandan')
                            <td class="py-2 px-4">
                                <div class="flex justify-center space-x-3">
                                    <button @click="
                                        showEditModal = true; 
                                        editAction = '{{ route('komandan.presensi.update', $presensi->id_presensi) }}';
                                        editWaktu = '{{ $presensi->waktu->format('Y-m-d\TH:i') }}';
                                        editStatus = '{{ $presensi->status }}';
                                        editJenisPresensi = '{{ $presensi->jenis_presensi }}';
                                    " class="text-blue-500 hover:text-blue-700" title="Edit">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828zM5 12V7a2 2 0 012-2h2.586l-4 4H5zM3 15a2 2 0 00-2 2v2h16v-2a2 2 0 00-2-2H3z"></path></svg>
                                    </button>
                                    <button @click.prevent="showDeleteModal = true; deleteAction = '{{ route('komandan.presensi.destroy', $presensi->id_presensi) }}'" class="text-red-500 hover:text-red-700" title="Hapus">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                    </button>
                                </div>
                            </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ Auth::user()->peran == 'komandan' ? '6' : '5' }}" class="py-4 px-4 text-center text-gray-500">
                            Tidak ada data presensi pulang pada tanggal ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- CARD LAYOUT (Mobile) --}}
        <div class="md:hidden space-y-3 p-3">
            @forelse($dataPulang as $index => $presensi)
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                    <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] px-4 py-2.5 flex justify-between items-center">
                        <div>
                            <p class="text-xs text-blue-200 font-semibold uppercase">Nama Anggota</p>
                            <p class="text-white font-bold text-base">{{ $presensi->nama_lengkap }}</p>
                        </div>
                        @if($presensi->status == 'tepat waktu')
                            <span class="bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg uppercase">Tepat Waktu</span>
                        @elseif($presensi->status == 'terlambat')
                            <span class="bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg uppercase">Telat</span>
                        @else
                            <span class="bg-yellow-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg uppercase">{{ ucfirst($presensi->status) }}</span>
                        @endif
                    </div>

                    <div class="p-4 space-y-3">
                        <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <p class="text-[10px] text-gray-500 font-semibold uppercase">Waktu</p>
                                    <p class="text-gray-800 font-bold text-base">{{ $presensi->waktu->format('H:i:s') }}</p>
                                </div>
                            </div>
                            <button @click="showPhotoModal = true; photoUrl = '{{ asset('storage/' . $presensi->foto) }}'" class="text-blue-500 hover:text-blue-700 font-semibold text-sm underline">
                                Lihat Foto
                            </button>
                        </div>

                        @if(Auth::user()->peran == 'komandan')
                            <div class="flex gap-2 pt-2">
                                <button @click="
                                    showEditModal = true; 
                                    editAction = '{{ route('komandan.presensi.update', $presensi->id_presensi) }}';
                                    editWaktu = '{{ $presensi->waktu->format('Y-m-d\TH:i') }}';
                                    editStatus = '{{ $presensi->status }}';
                                    editJenisPresensi = '{{ $presensi->jenis_presensi }}';
                                " class="flex-1 bg-blue-500 text-white font-bold py-2 rounded-lg hover:bg-blue-600 transition flex items-center justify-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828zM5 12V7a2 2 0 012-2h2.586l-4 4H5zM3 15a2 2 0 00-2 2v2h16v-2a2 2 0 00-2-2H3z"></path></svg>
                                    <span class="text-xs">Edit</span>
                                </button>
                                <button @click.prevent="showDeleteModal = true; deleteAction = '{{ route('komandan.presensi.destroy', $presensi->id_presensi) }}'" class="flex-1 bg-red-500 text-white font-bold py-2 rounded-lg hover:bg-red-600 transition flex items-center justify-center gap-1">
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
                    <p class="text-gray-500 font-semibold">Tidak ada data presensi pulang pada tanggal ini.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- TOMBOL PENGATURAN SHIFT RULE --}}
    <div class="flex justify-end mb-8">
        <button @click="showRuleModal = true" 
                class="flex items-center gap-2 bg-slate-700 text-white px-5 py-2.5 rounded-lg shadow hover:bg-slate-800 transition transform active:scale-95">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            <span>PENGATURAN SHIFT RULE</span>
        </button>
    </div>

    {{-- MODAL PENGATURAN RULE --}}
    <div x-show="showRuleModal"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
         @click.away="showRuleModal = false"
         style="display: none;">
        
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden relative" @click.stop>
            <div class="bg-slate-800 p-4 flex justify-between items-center">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                    PENGATURAN JAM SHIFT
                </h3>
                <button @click="showRuleModal = false" class="text-gray-300 hover:text-white text-2xl font-bold">&times;</button>
            </div>

            <form action="{{ route('komandan.presensi.updateRules') }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

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
                <div class="bg-slate-50 p-4 rounded-lg border border-slate-200 mb-6">
                    <h4 class="font-bold text-slate-700 mb-3 text-center text-sm uppercase">Pengaturan Umum</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase">Toleransi Keterlambatan</label>
                            <div class="flex items-center">
                                <input type="number" name="toleransi" required min="0"
                                    value="{{ $globalToleransi }}"
                                    class="w-full border-gray-300 rounded-l focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <span class="bg-gray-200 border border-l-0 border-gray-300 rounded-r px-2 py-2 text-gray-600 text-xs font-bold">Menit</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase">Presensi Dibuka</label>
                            <div class="flex items-center">
                                <input type="number" name="dibuka" required min="0"
                                    value="{{ $globalDibuka }}"
                                    class="w-full border-gray-300 rounded-l focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <span class="bg-gray-200 border border-l-0 border-gray-300 rounded-r px-2 py-2 text-gray-600 text-xs font-bold">Menit</span>
                            </div>
                        </div>

                        <div class="flex flex-col justify-center">
                            <label class="block text-[10px] font-bold text-gray-500 mb-2 uppercase text-center">Wajib Lokasi (GPS)</label>
                            <label class="relative inline-flex items-center cursor-pointer mx-auto">
                                <input type="hidden" name="is_geotag_enabled" value="0">
                                <input type="checkbox" name="is_geotag_enabled" value="1" class="sr-only peer"
                                    @if($rules->firstWhere('jenis_shift', 'Pagi')->is_geotag_enabled) checked @endif>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                <span class="ml-3 text-xs font-bold text-gray-700 peer-checked:text-blue-600">AKTIF</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" @click="showRuleModal = false" class="px-5 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-bold transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold shadow-md transition transform hover:scale-105">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL FOTO --}}
    <div x-show="showPhotoModal" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
         @click.away="showPhotoModal = false"
         style="display: none;">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full p-4 relative" @click.stop>
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 class="text-xl font-bold text-gray-800">FOTO PRESENSI</h3>
                <button @click="showPhotoModal = false" class="text-gray-500 hover:text-gray-800 text-3xl">&times;</button>
            </div>
            <div class="mt-4">
                <img :src="photoUrl" alt="Foto Presensi" class="w-full h-auto rounded">
            </div>
        </div>
    </div>

    {{-- MODAL EDIT --}}
    <div x-show="showEditModal"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
         @click.away="showEditModal = false"
         style="display: none;">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-4 relative" @click.stop>
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 class="text-xl font-bold text-gray-800">EDIT PRESENSI</h3>
                <button @click="showEditModal = false" class="text-gray-500 hover:text-gray-800 text-3xl">&times;</button>
            </div>
            <form :action="editAction" method="POST" class="mt-4">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label for="waktu" class="block text-sm font-medium text-gray-700 mb-1">Waktu:</label>
                        <input type="datetime-local" id="waktu" name="waktu" x-model="editWaktu"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status:</label>
                        <select id="status" name="status" x-model="editStatus"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="tepat waktu">Tepat Waktu</option>
                            <option value="terlambat">Terlambat</option>
                            <option value="terlalu cepat">Terlalu Cepat</option>
                            <option value="izin">Izin</option>
                        </select>
                    </div>
                    <div>
                        <label for="jenis_presensi" class="block text-sm font-medium text-gray-700 mb-1">Jenis Presensi:</label>
                        <select id="jenis_presensi" name="jenis_presensi" x-model="editJenisPresensi"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="Masuk">Masuk</option>
                            <option value="Pulang">Pulang</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full bg-green-500 text-white font-bold py-2 px-4 rounded-lg shadow hover:bg-green-600 transition">
                        SUBMIT
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL HAPUS --}}
    <div x-show="showDeleteModal"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
         @click.away="showDeleteModal = false"
         style="display: none;">
        <div class="bg-white rounded-lg shadow-xl max-w-sm w-full p-6 relative" @click.stop>
            <h3 class="text-lg font-bold text-gray-900 mb-4">Konfirmasi Hapus</h3>
            <p class="text-gray-600 mb-6">
                Apakah Anda yakin ingin menghapus data presensi ini? Tindakan ini tidak dapat dibatalkan.
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
@endsection
