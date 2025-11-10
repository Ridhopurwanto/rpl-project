@extends('layouts.app')

@section('header-left')
    <a href="{{ route('anggota.patroli.index') }}" class="bg-blue-600 text-white text-sm font-semibold px-6 py-2 rounded-full shadow-md">
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

    {{-- Filter Tanggal --}}
    <div class="mt-4 p-4 bg-white rounded-lg shadow">
        <h3 class="text-xs font-semibold text-gray-500 uppercase mb-2">DAFTAR PATROLI :</h3>
        <div class="flex items-center justify-end gap-4">
            <label class="text-sm font-semibold text-gray-700 whitespace-nowrap">TANGGAL :</label>
            <div class="relative">
                <input 
                    type="date" 
                    id="filter-tanggal"
                    value="{{ $tanggalTerpilih->format('Y-m-d') }}"
                    onchange="window.location.href = '{{ route('anggota.patroli.index') }}?tanggal=' + this.value"
                    class="w-48 appearance-none bg-slate-800 text-white text-sm font-semibold px-4 py-2 rounded-full shadow-md focus:ring-2 focus:ring-blue-500 cursor-pointer pr-10"
                    style="color-scheme: dark;">
                <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-white pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
        </div>
    </div>

    {{-- Tabel Patroli --}}
    <div class="mt-4 mb-32 bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-sm table-fixed">
            <thead class="bg-slate-800 text-white">
                <tr>
                    <th class="w-16 p-3 font-semibold text-center">NO</th>
                    <th class="w-24 p-3 font-semibold text-center">FOTO</th>
                    <th class="p-3 font-semibold text-center">WAKTU</th>
                    <th class="p-3 font-semibold text-center">NAMA</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                @forelse($patrolGroups as $jenisPatroli => $checkpoints)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3 text-center align-middle">{{ $loop->iteration }}</td>
                    <td class="p-3 text-center align-middle">
                        <a href="#" 
                           @click.prevent="
                               showModal = true;
                               modalGroup = {{ $checkpoints->values() }}; 
                               selectedCheckpointIndex = 0;
                           "
                           class="inline-block bg-blue-600 text-white text-xs font-semibold px-4 py-1 rounded hover:bg-blue-700">
                            Buka
                        </a>
                    </td>
                    <td class="p-3 text-center align-middle font-medium">{{ $jenisPatroli }}</td>
                    <td class="p-3 text-center align-middle font-medium">{{ $checkpoints->first()->nama_lengkap }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-6 text-center text-gray-500">
                        Belum ada data patroli untuk tanggal ini.
                    </td>
                </tr>
                @endforelse
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
       class="fixed z-50 bottom-28 right-6 md:right-[calc((100vw-768px)/2+24px)] bg-blue-700 p-4 rounded-full text-white shadow-lg hover:bg-blue-800 transition">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path>
        </svg>
    </a>
@endpush