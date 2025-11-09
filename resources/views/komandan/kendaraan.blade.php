@extends('layouts.app')

@section('header-left')
    <a class="bg-slate-800 text-white text-sm font-semibold px-6 py-2 rounded-full shadow-md hover:bg-slate-700 transition">
        KENDARAAN
    </a>
@endsection

@section('content')
{{-- 
    PERBAIKAN: 
    Atribut 'x-data' telah DIHAPUS DARI SINI untuk menghindari konflik
    dengan menu navigasi Anda di layouts.app
--}}
<div class="w-full mx-auto">
    
    {{-- Tampilkan Notifikasi Sukses/Error (Wajib ada) --}}
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

    {{-- 
    ============================================
    TABEL 1: MASTER DATA KENDARAAN
    ============================================
    --}}
    <h2 class="text-2xl font-bold text-slate-800 mb-4">Master Data Kendaraan</h2>
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
        <div class="overflow-x-auto">
            <table class="w-full min-w-max">
                <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500">
                    <tr>
                        <th class="py-3 px-4 text-left">No</th>
                        <th class="py-3 px-4 text-left">Nama Kendaraan</th>
                        <th class="py-3 px-4 text-left">Nomor Plat</th>
                        <th class="py-3 px-4 text-left">Status</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    {{-- Loop dari $kendaraans di controller --}}
                    @forelse($kendaraans as $index => $kendaraan)
                    <tr>
                        <td class="py-2 px-4">{{ $index + 1 }}.</td>
                        <td class="py-2 px-4 font-medium">{{ $kendaraan->nama_kendaraan }}</td>
                        <td class="py-2 px-4">{{ $kendaraan->nomor_plat }}</td>
                        <td class="py-2 px-4">{{ $kendaraan->status }}</td>
                        <td class="py-2 px-4">
                            @if(Auth::user()->peran == 'komandan')
                                <div class="flex justify-center space-x-3">
                                    
                                    {{-- PERBAIKAN: Tombol Edit: $dispatch untuk 'event' --}}
                                    <button @click="$dispatch('open-edit-modal', {
                                        action: '{{ route('komandan.kendaraan.master.update', $kendaraan->id_kendaraan) }}',
                                        nama: '{{ $kendaraan->nama_kendaraan }}',
                                        plat: '{{ $kendaraan->nomor_plat }}',
                                        status: '{{ $kendaraan->status }}'
                                    })" class="text-blue-500 hover:text-blue-700" title="Edit">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828zM5 12V7a2 2 0 012-2h2.586l-4 4H5zM3 15a2 2 0 00-2 2v2h16v-2a2 2 0 00-2-2H3z"></path></svg>
                                    </button>
                                    
                                    {{-- PERBAIKAN: Tombol Hapus: $dispatch untuk 'event' --}}
                                    <button @click.prevent="$dispatch('open-delete-modal', {
                                        action: '{{ route('komandan.kendaraan.master.destroy', $kendaraan->id_kendaraan) }}'
                                    })" class="text-red-500 hover:text-red-700" title="Hapus">
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
                        <td colspan="5" class="py-4 px-4 text-center text-gray-500">
                            Tidak ada data master kendaraan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    {{-- 
    ============================================
    TABEL 2: LOG PENGECEKAN KENDARAAN
    ============================================
    --}}
    <h2 class="text-2xl font-bold text-slate-800 mb-4">Log Pengecekan Kendaraan</h2>
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full min-w-max">
                <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500">
                    <tr>
                        <th class="py-3 px-4 text-left">Waktu Cek</th>
                        <th class="py-3 px-4 text-left">Anggota</th>
                        <th class="py-3 px-4 text-left">Kendaraan</th>
                        <th class="py-3 px-4 text-left">Nomor Plat</th>
                        <th class="py-3 px-4 text-left">Kondisi</th>
                        <th class="py-3 px-4 text-left">Keterangan</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    {{-- Loop dari $logs di controller --}}
                    @forelse($logs as $log)
                    <tr>
                        <td class="py-2 px-4">{{ $log->waktu_pengecekan->format('d/m/Y H:i') }}</td>
                        <td class="py-2 px-4">{{ $log->pengguna->nama_lengkap ?? 'N/A' }}</td>
                        <td class="py-2 px-4">{{ $log->kendaraan->nama_kendaraan ?? 'N/A' }}</td>
                        <td class="py-2 px-4">{{ $log->kendaraan->nomor_plat ?? 'N/A' }}</td>
                        <td class="py-2 px-4">{{ $log->kondisi }}</td>
                        <td class="py-2 px-4">{{ $log->keterangan ?? '-' }}</td>
                        <td class="py-2 px-4">
                            @if(Auth::user()->peran == 'komandan')
                                <div class="flex justify-center">
                                    {{-- PERBAIKAN: Tombol Edit Keterangan: $dispatch --}}
                                     <button @click="$dispatch('open-keterangan-modal', {
                                        action: '{{ route('komandan.kendaraan.log.updateKeterangan', $log->id_log) }}',
                                        text: '{{ $log->keterangan }}'
                                     })" class="text-blue-500 hover:text-blue-700" title="Edit Keterangan">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828zM5 12V7a2 2 0 012-2h2.586l-4 4H5zM3 15a2 2 0 00-2 2v2h16v-2a2 2 0 00-2-2H3z"></path></svg>
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
                            Tidak ada data log.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


    {{-- === ▼▼▼ SEMUA MODAL POP-UP (DENGAN x-data MANDIRI) ▼▼▼ === --}}

    {{-- 1. Modal Edit Master Kendaraan --}}
    <div 
         {{-- PERBAIKAN: x-data dipindah ke sini --}}
         x-data="{ 
            show: false, 
            action: '',
            nama: '',
            plat: '',
            status: ''
         }"
         {{-- Mendengarkan 'event' open-edit-modal --}}
         @open-edit-modal.window="
            show = true;
            action = $event.detail.action;
            nama = $event.detail.nama;
            plat = $event.detail.plat;
            status = $event.detail.status;
         "
         x-show="show"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
         @click.away="show = false"
         style="display: none;">
        
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-4 relative" @click.stop>
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 class="text-xl font-bold text-gray-800">EDIT KENDARAAN</h3>
                <button @click="show = false" class="text-gray-500 hover:text-gray-800 text-3xl">&times;</button>
            </div>
            
            <form :action="action" method="POST" class="mt-4">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    {{-- Field ini sesuai dengan validasi di updateMaster --}}
                    <div>
                        <label for="edit_nama_kendaraan" class="block text-sm font-medium text-gray-700 mb-1">Nama Kendaraan:</label>
                        <input type="text" id="edit_nama_kendaraan" name="nama_kendaraan" x-model="nama"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label for="edit_nomor_plat" class="block text-sm font-medium text-gray-700 mb-1">Nomor Plat:</label>
                        <input type="text" id="edit_nomor_plat" name="nomor_plat" x-model="plat"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label for="edit_status" class="block text-sm font-medium text-gray-700 mb-1">Status:</label>
                        <select id="edit_status" name="status" x-model="status"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            <option value="Tersedia">Tersedia</option>
                            <option value="Digunakan">Digunakan</option>
                            <option value="Perbaikan">Perbaikan</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full bg-green-500 text-white font-bold py-2 px-4 rounded-lg shadow hover:bg-green-600 transition">
                        SIMPAN PERUBAHAN
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 2. Modal Hapus Master Kendaraan --}}
    <div 
         {{-- PERBAIKAN: x-data dipindah ke sini --}}
         x-data="{ show: false, action: '' }"
         {{-- Mendengarkan 'event' open-delete-modal --}}
         @open-delete-modal.window="show = true; action = $event.detail.action;"
         x-show="show"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
         @click.away="show = false"
         style="display: none;">
        
        <div class="bg-white rounded-lg shadow-xl max-w-sm w-full p-6 relative" @click.stop>
            <h3 class="text-lg font-bold text-gray-900 mb-4">Konfirmasi Hapus</h3>
            <p class="text-gray-600 mb-6">
                Apakah Anda yakin ingin menghapus data kendaraan ini? Tindakan ini tidak dapat dibatalkan.
            </p>
            <form :action="action" method="POST" class="flex justify-end space-x-4">
                @csrf
                @method('DELETE')
                <button type="button" @click="show = false" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300">
                    Batal
                </button>
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                    Ya, Hapus
                </button>
            </form>
        </div>
    </div>
    
    {{-- 3. Modal Edit Keterangan Log --}}
    <div 
         {{-- PERBAIKAN: x-data dipindah ke sini --}}
         x-data="{ show: false, action: '', text: '' }"
         {{-- Mendengarkan 'event' open-keterangan-modal --}}
         @open-keterangan-modal.window="
            show = true;
            action = $event.detail.action;
            text = $event.detail.text;
         "
         x-show="show"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
         @click.away="show = false"
         style="display: none;">
        
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-4 relative" @click.stop>
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 class="text-xl font-bold text-gray-800">EDIT KTERANGAN LOG</h3>
                <button @click="show = false" class="text-gray-500 hover:text-gray-800 text-3xl">&times;</button>
            </div>
            
            <form :action="action" method="POST" class="mt-4">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    {{-- Field ini sesuai dengan validasi di updateKeterangan --}}
                    <div>
                        <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan:</label>
                        <textarea id="keterangan" name="keterangan" x-model="text" rows="4"
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-green-500 text-white font-bold py-2 px-4 rounded-lg shadow hover:bg-green-600 transition">
                        SIMPAN KETERANGAN
                    </button>
                </div>
            </form>
        </div>
    </div>
    
</div>
@endsection