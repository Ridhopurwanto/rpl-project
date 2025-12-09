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
    
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold text-slate-800">Laporan Presensi Anggota</h2>
        <button @click="showRuleModal = true" 
                class="bg-[#2a4a6f] text-white p-2.5 rounded-lg shadow-md hover:bg-[#1e3a5f] transition" 
                title="Pengaturan Shift Rule">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
        </button>
    </div>

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
    <form action="{{ route('komandan.presensi') }}" method="GET" x-data="{}">
        <div class="bg-white px-6 py-5 rounded-xl shadow-sm mb-6 border border-gray-200">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- Filter Tanggal --}}
                <div class="w-full">
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

                {{-- Filter Jenis Shift --}}
                <div class="w-full">
                    <label for="shift" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                        Jenis Shift
                    </label>
                    <div class="relative">
                        <select id="shift" name="shift" 
                                onchange="this.form.submit()"
                                class="block w-full h-[42px] px-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer appearance-none">
                            <option value="semua" @if($shiftTerpilih == 'semua') selected @endif>Semua Shift</option>
                            <option value="Pagi" @if($shiftTerpilih == 'Pagi') selected @endif>Shift Pagi</option>
                            <option value="Malam" @if($shiftTerpilih == 'Malam') selected @endif>Shift Malam</option>
                            <option value="Non Shift" @if($shiftTerpilih == 'Non Shift') selected @endif>Non Shift</option>
                            <option value="Off" @if($shiftTerpilih == 'Off') selected @endif>Off</option>
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

    {{-- DAFTAR PRESENSI MASUK --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="bg-gray-100 p-3 border-b border-gray-200">
            <h3 class="font-bold text-gray-800">DAFTAR PRESENSI MASUK</h3>
        </div>
        
        {{-- TABEL (Desktop) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full min-w-max table-fixed">
                <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500">
                    <tr>
                        <th class="py-3 px-4 text-center w-[8%]">No</th>
                        <th class="py-3 px-4 text-center w-[28%]">Nama</th>
                        <th class="py-3 px-4 text-center w-[18%]">Waktu</th>
                        <th class="py-3 px-4 text-center w-[13%]">Foto</th>
                        <th class="py-3 px-4 text-center w-[18%]">Status</th>
                        <th class="py-3 px-4 text-center w-[15%]">Aksi</th>
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
            <table class="w-full min-w-max table-fixed">
                <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500">
                    <tr>
                        <th class="py-3 px-4 text-center w-[8%]">No</th>
                        <th class="py-3 px-4 text-center w-[28%]">Nama</th>
                        <th class="py-3 px-4 text-center w-[18%]">Waktu</th>
                        <th class="py-3 px-4 text-center w-[13%]">Foto</th>
                        <th class="py-3 px-4 text-center w-[18%]">Status</th>
                        <th class="py-3 px-4 text-center w-[15%]">Aksi</th>
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
                        <td class="py-2 px-4 text-center">
                            @if($presensi->status == 'tepat waktu')
                                <span class="text-green-600 font-semibold">Tepat Waktu</span>
                            @elseif($presensi->status == 'terlambat')
                                <span class="text-red-500 font-semibold">Terlambat</span>
                            @else
                                <span class="text-yellow-500 font-semibold">{{ ucfirst($presensi->status) }}</span>
                            @endif
                        </td>
                            </td>
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
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-4 px-4 text-center text-gray-500">
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

    
    {{-- MODAL PENGATURAN RULE --}}
    <div x-show="showRuleModal"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
         @click.away="showRuleModal = false"
         style="display: none;">
        
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden relative" @click.stop>
            <div class="bg-[#1e3a5f] py-4 px-6 border-b border-[#1e3a5f] flex justify-between items-center">
                <h3 class="text-lg font-bold text-white flex items-center tracking-wide">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                    PENGATURAN JAM SHIFT
                </h3>
                <button @click="showRuleModal = false" class="text-white/70 hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
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
                            <div class="flex items-center gap-2">
                                <input type="number" name="toleransi" required min="0"
                                    value="{{ $globalToleransi }}"
                                    class="w-full border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500 text-sm h-[30px]">
                                <div class="bg-gray-200 border border-gray-300 rounded px-3 h-[30px] flex items-center text-gray-600 text-xs font-bold">
                                    Menit
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase">Presensi Dibuka</label>
                            <div class="flex items-center gap-2">
                                <input type="number" name="dibuka" required min="0"
                                    value="{{ $globalDibuka }}"
                                    class="w-full border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500 text-sm h-[30px]">
                                <div class="bg-gray-200 border border-gray-300 rounded px-3 h-[30px] flex items-center text-gray-600 text-xs font-bold">
                                    Menit
                                </div>
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

                <div class="flex justify-end gap-3 pt-4 border-t bg-gray-50 p-4">
                    <button type="button" @click="showRuleModal = false" class="px-6 py-2.5 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 font-semibold transition">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 bg-[#1e3a5f] text-white rounded-xl hover:bg-[#2a4a6f] font-bold shadow-lg transition transform hover:-translate-y-0.5">
                        SIMPAN PERUBAHAN
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
        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full relative overflow-hidden" @click.stop>
            {{-- Header Biru --}}
            <div class="bg-[#1e3a5f] py-4 px-6 border-b border-[#1e3a5f] flex justify-between items-center">
                <h3 class="text-lg font-bold text-white flex items-center tracking-wide">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
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
                            <h4 class="text-sm font-bold text-[#1e3a5f] mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-[#1e3a5f]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                INFORMASI PRESENSI
                            </h4>
                            
                            <div class="space-y-4">
                                {{-- Waktu --}}
                                <div>
                                    <label for="waktu" class="block text-xs font-bold text-[#1e3a5f] uppercase tracking-wide mb-1">Waktu <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-[#1e3a5f]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <input type="datetime-local" id="waktu" name="waktu" x-model="editWaktu" required
                                               class="pl-10 w-full bg-white border border-gray-300 text-gray-800 text-sm font-medium rounded-lg shadow-sm focus:ring-[#1e3a5f] focus:border-[#1e3a5f] block p-2.5">
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
                                    <label for="jenis_presensi" class="block text-xs font-bold text-[#1e3a5f] uppercase tracking-wide mb-1">Jenis Presensi <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-[#1e3a5f]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                        </div>
                                        <select id="jenis_presensi" name="jenis_presensi" x-model="editJenisPresensi" required
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
                    <button type="submit" class="w-full px-4 py-3 text-white font-bold bg-[#1e3a5f] rounded-xl hover:bg-[#2a4a6f] shadow-lg transition transform hover:-translate-y-0.5">
                        SIMPAN PERUBAHAN
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
