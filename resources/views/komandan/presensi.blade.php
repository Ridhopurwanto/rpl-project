@extends('layouts.app')

{{-- Tombol KEMBALI ke dashboard komandan --}}
@section('header-left')
    <a href="{{ route('komandan.dashboard') }}" class="bg-slate-800 text-white text-sm font-semibold px-6 py-2 rounded-full shadow-md hover:bg-slate-700 transition">
        KEMBALI
    </a>
@endsection

@section('content')
<div class="w-full mx-auto">
    
    <h2 class="text-2xl font-bold text-slate-800 mb-4">Laporan Presensi Anggota</h2>

    {{-- Tampilkan Notifikasi Sukses/Error --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Form Filter Tanggal --}}
    {{-- PERBAIKAN: Dibungkus <form> agar filter berfungsi --}}
    <form action="{{ route('laporan.presensi') }}" method="GET">
        <div class="bg-white p-4 rounded-lg shadow-md mb-6">
            <div class="flex flex-col sm:flex-row sm:items-end sm:space-x-4 space-y-4 sm:space-y-0">
                
                {{-- Filter Tanggal --}}
                <div class="flex-1">
                    <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">TANGGAL:</label>
                    {{-- PERBAIKAN: value diambil dari controller --}}
                    <input type="date" id="tanggal" name="tanggal" 
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                           value="{{ $tanggalTerpilih }}">
                </div>

                {{-- Filter Shift (Belum berfungsi, hanya tampilan) --}}
                <div class="flex-1">
                    <label for="shift" class="block text-sm font-medium text-gray-700 mb-1">JENIS SHIFT:</label>
                    <select id="shift" name="shift" 
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="semua">Semua Shift</option>
                        <option value="pagi">Shift Pagi</option>
                        <option value="siang">Shift Siang</option>
                        <option value="malam">Shift Malam</option>
                    </select>
                </div>

                {{-- Tombol Filter --}}
                <button type="submit" class="w-full sm:w-auto bg-blue-600 text-white px-5 py-2 rounded-lg shadow hover:bg-blue-700 transition">
                    Tampilkan
                </button>
            </div>
        </div>
    </form>

    {{-- Tabel Daftar Presensi Masuk --}}
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
                    {{-- PERBAIKAN: Menggunakan data dinamis $dataMasuk --}}
                    @forelse($dataMasuk as $index => $presensi)
                    <tr>
                        <td class="py-2 px-4">{{ $index + 1 }}.</td>
                        <td class="py-2 px-4 font-medium">{{ $presensi->nama_lengkap }}</td>
                        <td class="py-2 px-4">{{ $presensi->waktu_masuk->format('H:i:s') }}</td>
                        <td class="py-2 px-4 text-center">
                            <a href="{{ asset('storage/' . $presensi->foto_masuk) }}" target="_blank" class="text-blue-500 hover:underline">Buka</a>
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
                        <td class="py-2 px-4">
                            {{-- FITUR BARU: Tombol Edit & Hapus --}}
                            @if(Auth::user()->peran == 'komandan')
                                <div class="flex justify-center space-x-3">
                                    {{-- Tombol Edit --}}
                                    <a href="{{ route('laporan.presensi.edit', $presensi->id_presensi) }}" class="text-blue-500 hover:text-blue-700" title="Edit">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828zM5 12V7a2 2 0 012-2h2.586l-4 4H5zM3 15a2 2 0 00-2 2v2h16v-2a2 2 0 00-2-2H3z"></path></svg>
                                    </a>
                                    {{-- Tombol Hapus --}}
                                    <form action="{{ route('laporan.presensi.destroy', $presensi->id_presensi) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700" title="Hapus">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-4 px-4 text-center text-gray-500">
                            Tidak ada data presensi masuk pada tanggal ini.
                        </td>
                    </tr>
                    {{-- PERBAIKAN: Salah ketik @endForetlse -> @endforelse --}}
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    {{-- === ▼▼▼ TABEL PRESENSI PULANG (TAMBAHAN) ▼▼▼ === --}}
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
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    {{-- Menggunakan data dinamis $dataPulang --}}
                    @forelse($dataPulang as $index => $presensi)
                    <tr>
                        <td class="py-2 px-4">{{ $index + 1 }}.</td>
                        <td class="py-2 px-4 font-medium">{{ $presensi->nama_lengkap }}</td>
                        <td class="py-2 px-4">{{ $presensi->waktu_pulang->format('H:i:s') }}</td>
                        <td class="py-2 px-4 text-center">
                            <a href="{{ asset('storage/' . $presensi->foto_pulang) }}" target="_blank" class="text-blue-500 hover:underline">Buka</a>
                        </td>
                        <td class="py-2 px-4">
                            @if(Auth::user()->peran == 'komandan')
                                <div class="flex justify-center space-x-3">
                                    {{-- Tombol Edit --}}
                                    <a href="{{ route('laporan.presensi.edit', $presensi->id_presensi) }}" class="text-blue-500 hover:text-blue-700" title="Edit">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828zM5 12V7a2 2 0 012-2h2.586l-4 4H5zM3 15a2 2 0 00-2 2v2h16v-2a2 2 0 00-2-2H3z"></path></svg>
                                    </a>
                                    {{-- Tombol Hapus --}}
                                    <form action="{{ route('laporan.presensi.destroy', $presensi->id_presensi) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700" title="Hapus">
                                            <svg class.="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-4 px-4 text-center text-gray-500">
                            Tidak ada data presensi pulang pada tanggal ini.
                        </td>
                    </tr>
                    {{-- PERBAIKAN: Salah ketik @endForetlse -> @endforelse --}}
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{-- === ▲▲▲ BATAS TABEL TAMBAHAN ▲▲▲ === --}}

</div>
@endsection