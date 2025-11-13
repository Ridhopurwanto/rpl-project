@extends('layouts.app')

@section('content')
<div class="w-full min-h-screen bg-slate-100 p-4 pb-32">

    {{-- Filter Tanggal --}}
    <div class="flex justify-between items-center my-3 px-2">
        <label for="tanggal" class="text-lg font-bold text-slate-700 uppercase">RIWAYAT :</label>
        
        <form action="{{ route('anggota.tamu.index') }}" method="GET">
            <input 
                type="date" 
                id="tanggal"
                name="tanggal"
                value="{{ $tanggal_terpilih }}"
                onchange="this.form.submit()" {{-- Auto submit saat tanggal ganti --}}
                class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow border-none focus:outline-none focus:ring-2 focus:ring-blue-400"
            >
        </form>
    </div>

    {{-- Kontainer tabel riwayat --}}
    <div class="bg-white rounded-lg shadow-md p-4 mt-2 overflow-x-auto">
        <table class="w-full min-w-[600px] text-sm text-left">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th class="py-3 px-4">No</th>
                    <th class="py-3 px-4">Nama</th>
                    <th class="py-3 px-4">Instansi</th>
                    <th class="py-3 px-4">Waktu</th>
                    <th class="py-3 px-4">Tujuan</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($riwayat_tamu as $tamu)
                <tr class="bg-white">
                    <td class="py-3 px-4 font-medium">{{ $loop->iteration }}.</td>
                    <td class="py-3 px-4 font-medium">{{ $tamu->nama_tamu }}</td>
                    <td class="py-3 px-4">{{ $tamu->instansi }}</td>
                    <td class="py-3 px-4">{{ $tamu->waktu_datang->format('H:i:s') }}</td>
                    <td class="py-3 px-4">{{ $tamu->tujuan }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-4 px-4 text-center text-gray-500">
                        Tidak ada riwayat tamu pada tanggal ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Tombol Aksi Tambah (FAB) --}}
    <a href="{{ route('anggota.tamu.create') }}" class="fixed bottom-24 right-4 bg-blue-800 text-white rounded-full w-16 h-16 flex items-center justify-center shadow-lg transform hover:scale-110 transition-transform">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
    </a>

</div>
@endsection