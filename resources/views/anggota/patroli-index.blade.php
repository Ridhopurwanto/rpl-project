@extends('layouts.app')

@section('header-left')
    <a href="{{ route('anggota.patroli.index') }}" class="bg-[#2a4a6f] text-white text-sm font-semibold px-6 py-2 rounded-full shadow-md">
        PATROLI
    </a>
@endsection

@section('content')
<div x-data="{ 
        showModal: false, 
        modalGroup: [], 
        selectedCheckpointIndex: 0,
        
        get currentCheckpoint() { return this.modalGroup[this.selectedCheckpointIndex] || null; },
        get modalTitle() { return this.currentCheckpoint ? 'DETAIL ' + this.currentCheckpoint.jenis_patroli : 'DETAIL'; },
        get modalPhoto() { return this.currentCheckpoint ? '{{ asset('storage') }}/' + this.currentCheckpoint.foto : ''; },
        get modalWaktu() { 
            if (!this.currentCheckpoint) return '';
            return new Date(this.currentCheckpoint.waktu_exact).toLocaleTimeString('id-ID', { 
                hour: '2-digit', minute: '2-digit', second: '2-digit' 
            });
        }
    }">

    {{-- Filter Tanggal & Header --}}
    <div class="bg-white rounded-lg shadow-md p-5 mt-4 mb-6">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            
            {{-- Bagian Kiri: Judul & Deskripsi --}}
            <div>
                <h3 class="text-lg font-bold text-slate-700 uppercase tracking-wide">RIWAYAT PATROLI</h3>
                <p class="text-xs text-gray-500 mt-1">Pilih tanggal untuk melihat detail catatan patroli.</p>
            </div>

            {{-- Bagian Kanan: Form Filter --}}
            <form action="{{ route('anggota.patroli.index') }}" method="GET" class="flex items-center gap-3 self-end md:self-auto">
                <label for="filter-tanggal" class="text-sm font-bold text-slate-600 uppercase hidden sm:block">
                    TANGGAL :
                </label>
                
                <div class="relative">
                    <input 
                        type="date" 
                        id="filter-tanggal"
                        name="tanggal" 
                        value="{{ $tanggalTerpilih->format('Y-m-d') }}"
                        onchange="this.form.submit()" 
                        class="bg-[#2a4a6f] text-white text-sm font-bold px-5 py-2.5 rounded-lg shadow-md hover:bg-[#1e3a5a] focus:outline-none focus:ring-2 focus:ring-blue-400 transition-all cursor-pointer"
                        style="color-scheme: dark;"
                    >
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Patroli --}}
    <div class="mt-4 mb-32 bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-sm table-fixed">
            <thead class="bg-[#2a4a6f] text-white">
                <tr>
                    <th class="w-16 p-3 font-semibold text-center">NO</th>
                    <th class="p-3 font-semibold text-center">JENIS PATROLI</th>
                    <th class="p-3 font-semibold text-center">NAMA</th>
                    <th class="p-3 font-semibold text-center">DETAIL</th>
                    <th class="p-3 font-semibold text-center">STATUS</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
            @if ($patrolGroups->isEmpty())
                <tr>
                    <td colspan="5" class="p-6 text-center text-gray-500"> {{-- Colspan disesuaikan jadi 5 --}}
                        Belum ada data patroli untuk tanggal ini.
                    </td>
                </tr>
            @else
                @foreach($patrolGroups as $jenisPatroli => $checkpoints)
                    @php
                        $jumlahSelesai = $checkpoints->count();
                        $isSelesai = $jumlahSelesai >= 17;
                    @endphp
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3 text-center align-middle">{{ $loop->iteration }}</td>
                        <td class="p-3 text-center align-middle font-medium">{{ $jenisPatroli }}</td>
                        <td class="p-3 text-center align-middle font-medium">{{ $checkpoints->first()->nama_lengkap }}</td>
                        <td class="p-3 text-center align-middle">
                            <a href="#" 
                            @click.prevent="
                                showModal = true;
                                modalGroup = {{ $checkpoints->values() }}; 
                                selectedCheckpointIndex = 0;
                            "
                            class="text-blue-500 hover:underline font-semibold text-xs">
                                Buka
                            </a>
                        </td>
                        
                        {{-- KOLOM STATUS BARU --}}
                        <td class="p-3 text-center align-middle">
                            <div class="flex flex-col items-center justify-center gap-1">
                                {{-- Teks Jumlah --}}
                                <span class="text-xs font-bold {{ $isSelesai ? 'text-green-600' : 'text-orange-500' }}">
                                    {{ $jumlahSelesai }} / 17
                                </span>
                                
                                {{-- Badge Status --}}
                                @if($isSelesai)
                                    <span class="bg-green-100 text-green-800 text-[10px] font-bold px-2 py-0.5 rounded-full border border-green-200">
                                        SELESAI
                                    </span>
                                @else
                                    <span class="bg-yellow-100 text-yellow-800 text-[10px] font-bold px-2 py-0.5 rounded-full border border-yellow-200 whitespace-nowrap">
                                        BELUM SELESAI
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
        </table>
    </div>

    {{-- Modal Detail Patroli --}}
    <div 
        x-show="showModal" 
        @keydown.escape.window="showModal = false"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
        style="display: none;">
        <div 
            @click.outside="showModal = false"
            class="bg-white rounded-lg shadow-xl w-full max-w-xs">
            <div class="flex justify-between items-center p-4 border-b">
                <h3 class="text-lg font-bold text-gray-800" x-text="modalTitle"></h3>
                <button @click="showModal = false" class="text-gray-500 hover:text-gray-800">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-4 space-y-4">
                <img :src="modalPhoto" alt="Foto Patroli" class="w-full h-auto rounded-lg bg-gray-200 shadow-inner">
                
                <div>
                    <label class="text-xs font-semibold text-gray-500">Wilayah Patroli</label>
                    <select x-model.number="selectedCheckpointIndex" class="w-full bg-slate-200 text-slate-800 rounded-lg p-2 font-semibold border-0 focus:ring-2 focus:ring-blue-500">
                        <template x-for="(checkpoint, index) in modalGroup" :key="index">
                            <option :value="index" x-text="checkpoint.wilayah"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-500">Waktu</label>
                    <div class="w-full bg-slate-800 text-white rounded-lg p-2 font-semibold text-center" x-text="modalWaktu"></div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('fab')
    <a href="{{ route('anggota.patroli.createSession') }}" 
       class="fixed z-50 bottom-28 right-6 md:right-[calc((100vw-768px)/2+24px)] bg-[#2a4a6f] p-4 rounded-full text-white shadow-lg transform hover:scale-110 transition-transform">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path>
        </svg>
    </a>
@endpush