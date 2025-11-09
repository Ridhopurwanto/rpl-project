@extends('layouts.app')

{{-- 1. Header (Tombol Kembali) --}}
@section('header-left')
    <a href="{{ route('anggota.patroli.index') }}" class="p-2">
        <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
    </a>
@endsection

@section('content')
<div class="w-full">
    <form action="{{ route('anggota.patroli.createSession') }}" method="GET">
        <div class="flex items-center justify-between mt-4">
            <label class="text-sm font-semibold text-gray-700">WAKTU PATROLI</label>
            <select name="jenis_patroli" 
                    onchange="this.form.submit()" {{-- Reload halaman saat ganti --}}
                    class="bg-slate-800 text-white text-sm font-semibold px-4 py-2 rounded-full shadow-md border-0 focus:ring-2 focus:ring-blue-500">
                
                @foreach($opsiJenisPatroli as $opsi)
                    <option value="{{ $opsi }}" {{ $opsi == $jenisPatroliTerpilih ? 'selected' : '' }}>
                        {{ $opsi }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    <div class="mt-6">
        <h3 class="text-xs font-semibold text-gray-500 uppercase mb-2">DAFTAR PATROLI :</h3>
        
        <div class="grid grid-cols-3 gap-3">
            {{-- Loop 17 area dari controller --}}
            @foreach($semuaArea as $area)
                @php
                    // Cek apakah area ini ada di array $completedCheckpoints
                    $isCompleted = in_array($area, $completedCheckpoints);
                @endphp

                @if($isCompleted)
                    {{-- Tampilan JIKA SUDAH SELESAI (Abu-abu + Ceklis) --}}
                    <div class="relative bg-slate-800 text-white p-4 rounded-lg shadow text-center font-semibold text-sm">
                        {{ $area }}
                        {{-- Ikon Ceklis Hijau --}}
                        <div class="absolute -top-2 -right-2 w-6 h-6 bg-green-500 rounded-full flex items-center justify-center border-2 border-white">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                    </div>
                @else
                    {{-- Tampilan JIKA BELUM (Link ke Halaman Kamera) --}}
                    <a href="{{ route('anggota.patroli.createCheckpoint', [
                            'jenis_patroli' => $jenisPatroliTerpilih,
                            'wilayah' => $area
                        ]) }}"
                       class="bg-gray-400 text-white p-4 rounded-lg shadow text-center font-semibold text-sm hover:bg-gray-500">
                        {{ $area }}
                    </a>
                @endif
            @endforeach
        </div>
    </div>

    <div class="mt-8 mb-20">
        <form action="{{ route('anggota.patroli.submitSession') }}" method="POST">
            @csrf
            {{-- Kirim data tersembunyi agar controller tahu sesi mana yang disubmit --}}
            <input type="hidden" name="jenis_patroli" value="{{ $jenisPatroliTerpilih }}">
            
            @if($totalCompleted >= 17)
                {{-- Tombol AKTIF (bisa diklik) --}}
                <button type="submit" 
                        class="w-full bg-green-600 text-white p-4 rounded-lg shadow font-bold hover:bg-green-700">
                    SUBMIT PATROLI (SELESAI)
                </button>
            @else
                {{-- Tombol NONAKTIF (hanya indikator) --}}
                <button type="button" 
                        class="w-full bg-gray-400 text-gray-600 p-4 rounded-lg shadow font-bold" 
                        disabled>
                    SUBMIT PATROLI ({{ $totalCompleted }}/17 Selesai)
                </button>
            @endif
        </form>
    </div>
</div>
@endsection