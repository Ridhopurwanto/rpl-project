@extends('layouts.app')

@section('header-left')
    <a class="bg-slate-800 text-white text-sm font-semibold px-6 py-2 rounded-full shadow-md hover:bg-slate-700 transition">
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
        editWaktuMasuk: '',
        editWaktuPulang: '',
        editStatus: '',
        showDeleteModal: false,
        deleteAction: '' 
     }">
    
    <h2 class="text-2xl font-bold text-slate-800 mb-4">Laporan Presensi Anggota</h2>

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


    {{-- Form Filter (Sama) --}}
    <form action="{{ route('komandan.presensi') }}" method="GET">
        <div class="bg-white p-4 rounded-lg shadow-md mb-6">
            <div class="flex flex-col sm:flex-row sm:items-end sm:space-x-4 space-y-4 sm:space-y-0">
                <div class="flex-1">
                    <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">TANGGAL:</label>
                    <input type="date" id="tanggal" name="tanggal" 
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                           value="{{ $tanggalTerpilih }}">
                </div>
                <div class="flex-1">
            <label for="shift" class="block text-sm font-medium text-gray-700 mb-1">JENIS SHIFT:</label>
            <select id="shift" name="shift" 
                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="semua">Semua Shift</option>
                <option value="pagi">Shift Pagi</option>
                <option value="malam">Shift Malam</option>
            </select>
            </div>
                <button type="submit" class="w-full sm:w-auto bg-blue-600 text-white px-5 py-2 rounded-lg shadow hover:bg-blue-700 transition">
                    Tampilkan
                </button>
            </div>
            
        </div>

        
    </form>

    {{-- Tabel 1: DAFTAR PRESENSI MASUK --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="bg-gray-100 p-3 border-b border-gray-200">
            <h3 class="font-bold text-gray-800">DAFTAR PRESENSI MASUK</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-max">
                <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500">
                    <tr>
                        <th class="py-3 px-4 text-left">No</th>
                        <th class="py-3 px-4 text-left">Nama</th>
                        <th class="py-3 px-4 text-left">Waktu</th>
                        <th class="py-3 px-4 text-center">Foto</th>
                        <th class="py-3 px-4 text-left">Status</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse($dataMasuk as $index => $presensi)
                    <tr>
                        <td class="py-2 px-4">{{ $index + 1 }}.</td>
                        <td class="py-2 px-4 font-medium">{{ $presensi->nama_lengkap }}</td>
                        <td class="py-2 px-4">{{ $presensi->waktu_masuk->format('H:i:s') }}</td>
                        <td class="py-2 px-4 text-center">
                            {{-- ▼▼▼ PERBAIKAN TOMBOL BUKA (FOTO) ▼▼▼ --}}
                            <button @click="showPhotoModal = true; photoUrl = '{{ asset('storage/' . $presensi->foto_masuk) }}'" class="text-blue-500 hover:underline">
                                Buka
                            </button>
                        </td>
                        <td class="py-2 px-4">
                            @if($presensi->status == 'tepat waktu')
                                <span class="text-green-600 font-semibold">Tepat Waktu</span>
                            @elseif($presensi->status == 'terlambat')
                                <span class="text-red-500 font-semibold">Terlambat</span>
                            @elseif($presensi->status == 'izin')
                                <span class="text-orange-500 font-semibold">Izin</span>
                            @else
                                <span class="text-yellow-500 font-semibold">{{ ucfirst($presensi->status) }}</span>
                            @endif
                        </td>
                        <td class="py-2 px-4">
                            @if(Auth::user()->peran == 'komandan')
                                <div class="flex justify-center space-x-3">
                                    {{-- ▼▼▼ PERBAIKAN TOMBOL EDIT ▼▼▼ --}}
                                    <button @click="
                                        showEditModal = true; 
                                        editAction = '{{ route('komandan.presensi.update', $presensi->id_presensi) }}';
                                        editWaktuMasuk = '{{ $presensi->waktu_masuk->format('Y-m-d\TH:i') }}';
                                        editWaktuPulang = '{{ $presensi->waktu_pulang ? $presensi->waktu_pulang->format('Y-m-d\TH:i') : '' }}';
                                        editStatus = '{{ $presensi->status }}';
                                    " class="text-blue-500 hover:text-blue-700" title="Edit">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828zM5 12V7a2 2 0 012-2h2.586l-4 4H5zM3 15a2 2 0 00-2 2v2h16v-2a2 2 0 00-2-2H3z"></path></svg>
                                    </button>
                                    
                                    {{-- ▼▼▼ PERBAIKAN TOMBOL HAPUS ▼▼▼ --}}
                                    <button @click.prevent="showDeleteModal = true; deleteAction = '{{ route('komandan.presensi.destroy', $presensi->id_presensi) }}'" class="text-red-500 hover:text-red-700" title="Hapus">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                    </button>
                                </div>
                            @else
                                -
                            @endif
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
    </div>
    
    {{-- Tabel 2: DAFTAR PRESENSI PULANG --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="bg-gray-100 p-3 border-b border-gray-200">
            <h3 class="font-bold text-gray-800">DAFTAR PRESENSI PULANG</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-max">
                <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500">
                    <tr>
                        <th class="py-3 px-4 text-left">No</th>
                        <th class="py-3 px-4 text-left">Nama</th>
                        <th class="py-3 px-4 text-left">Waktu</th>
                        <th class="py-3 px-4 text-center">Foto</th>
                        <th class="py-3 px-4 text-left">Status</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse($dataPulang as $index => $presensi)
                    <tr>
                        <td class="py-2 px-4">{{ $index + 1 }}.</td>
                        <td class="py-2 px-4 font-medium">{{ $presensi->nama_lengkap }}</td>
                        <td class="py-2 px-4">{{ $presensi->waktu_pulang->format('H:i:s') }}</td>
                        <td class="py-2 px-4 text-center">
                            {{-- ▼▼▼ PERBAIKAN TOMBOL BUKA (FOTO) ▼▼▼ --}}
                            <button @click="showPhotoModal = true; photoUrl = '{{ asset('storage/' . $presensi->foto_pulang) }}'" class="text-blue-500 hover:underline">
                                Buka
                            </button>
                        </td>
                                                <td class="py-2 px-4">
                            @if($presensi->status == 'tepat waktu')
                                <span class="text-green-600 font-semibold">Tepat Waktu</span>
                            @elseif($presensi->status == 'terlambat')
                                <span class="text-red-500 font-semibold">Terlambat</span>
                            @elseif($presensi->status == 'izin')
                                <span class="text-orange-500 font-semibold">Izin</span>
                            @else
                                <span class="text-yellow-500 font-semibold">{{ ucfirst($presensi->status) }}</span>
                            @endif
                        </td>
                        <td class="py-2 px-4">
                            @if(Auth::user()->peran == 'komandan')
                                <div class="flex justify-center space-x-3">
                                    <button @click="
                                        showEditModal = true; 
                                        editAction = '{{ route('komandan.presensi.update', $presensi->id_presensi) }}';
                                        editWaktuMasuk = '{{ $presensi->waktu_masuk->format('Y-m-d\TH:i') }}';
                                        editWaktuPulang = '{{ $presensi->waktu_pulang ? $presensi->waktu_pulang->format('Y-m-d\TH:i') : '' }}';
                                        editStatus = '{{ $presensi->status }}';
                                    " class="text-blue-500 hover:text-blue-700" title="Edit">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828zM5 12V7a2 2 0 012-2h2.586l-4 4H5zM3 15a2 2 0 00-2 2v2h16v-2a2 2 0 00-2-2H3z"></path></svg>
                                    </button>
                                    
                                    <button @click.prevent="showDeleteModal = true; deleteAction = '{{ route('komandan.presensi.destroy', $presensi->id_presensi) }}'" class="text-red-500 hover:text-red-700" title="Hapus">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                    </button>
                                </div>
                            @else
                                -
                            @endif
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
    </div>


    {{-- === ▼▼▼ MODAL POP-UP (Foto, Edit, Hapus) - SEPERTI DI PATROLI ▼▼▼ === --}}

    {{-- 1. Modal Tampil Foto --}}
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

    {{-- 2. Modal Edit Presensi --}}
    <div x-show="showEditModal"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
         @click.away="showEditModal = false"
         style="display: none;">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-4 relative" @click.stop>
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 class="text-xl font-bold text-gray-800">EDIT PRESENSI</h3>
                <button @click="showEditModal = false" class="text-gray-500 hover:text-gray-800 text-3xl">&times;</button>
            </div>
            {{-- Form Edit --}}
            <form :action="editAction" method="POST" class="mt-4">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    
                    <div>
                        <label for="waktu_masuk" class="block text-sm font-medium text-gray-700 mb-1">Waktu Masuk:</label>
                        <input type="datetime-local" id="waktu_masuk" name="waktu_masuk" x-model="editWaktuMasuk"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label for="waktu_pulang" class="block text-sm font-medium text-gray-700 mb-1">Waktu Pulang:</label>
                        <input type="datetime-local" id="waktu_pulang" name="waktu_pulang" x-model="editWaktuPulang"
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

                    <button type="submit" class="w-full bg-green-500 text-white font-bold py-2 px-4 rounded-lg shadow hover:bg-green-600 transition">
                        SUBMIT
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 3. Modal Hapus --}}
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
    
    {{-- === ▲▲▲ BATAS AKHIR MODAL POP-UP ▲▲▲ === --}}

</div>
@endsection