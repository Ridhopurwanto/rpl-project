@extends('layouts.app') {{-- Menggunakan layout utama --}}

{{-- Tombol Kiri Header diganti jadi 'KEMBALI' --}}
@section('header-left')
    {{-- Link kembali dinamis, tergantung role user --}}
    @php $peran = Auth::user()->peran; @endphp
    <a href="{{ route($peran . '.dashboard') }}" class="bg-slate-800 text-white text-sm font-semibold px-6 py-2 rounded-full shadow-md hover:bg-slate-700 transition">
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
    <form action="{{ route('laporan.presensi') }}" method="GET">
        <div class="bg-white p-4 rounded-lg shadow-md mb-6">
            <div class="flex flex-col sm:flex-row sm:items-end sm:space-x-4 space-y-4 sm:space-y-0">
                <div class="flex-1">
                    <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">TANGGAL:</label>
                    <input type="date" id="tanggal" name="tanggal" 
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                           value="{{ $tanggalTerpilih }}">
                </div>
                <button type="submit" class="w-full sm:w-auto bg-blue-600 text-white px-5 py-2 rounded-lg shadow hover:bg-blue-700 transition">
                    Tampilkan
                </button>
            </div>
        </div>
    </form>

    {{-- Tabel Daftar Presensi Masuk --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="bg-gray-100 p-3 border-b border-gray-200">
            <h3 class="font-bold text-gray-800">DAFTAR PRESENSI DATANG</h3>
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
                        <td class="py-2 px-4 text-center">
                            {{-- INI KUNCINYA: Tampilkan tombol Hapus HANYA untuk Komandan --}}
                            @if(Auth::user()->peran == 'komandan')
                                <form action="{{ route('laporan.presensi.destroy', $presensi->id_presensi) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700" title="Hapus">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                    </button>
                                </form>
                            @else
                                {{-- Untuk BAU, tampilkan strip --}}
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
                    @endForetlse
                </tbody>
            </table>
        </div>
    </div>
    
    {{-- TODO: Buat tabel kedua untuk 'DAFTAR PRESENSI PULANG' (data $dataPulang) --}}
    {{-- ... (copy-paste tabel di atas dan ganti $dataMasuk -> $dataPulang) ... --}}

</div>
@endsection