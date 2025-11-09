@extends('layouts.app')

{{-- 1. Ganti Tombol Header --}}
@section('header-left')
    <a href="{{ route('anggota.patroli.index') }}" class="bg-blue-600 text-white text-sm font-semibold px-6 py-2 rounded-full shadow-md">
        PATROLI
    </a>
@endsection

{{-- 2. Isi Konten Halaman --}}
@section('content')
{{-- 
  PERUBAHAN Alpine.js state:
  - modalGroup: Akan menampung SEMUA checkpoint (foto/wilayah) untuk satu sesi patroli.
  - selectedCheckpointIndex: Melacak checkpoint mana yang sedang dilihat (0, 1, 2, ...).
  - Getter (get...): Properti bantu untuk menampilkan data di modal.
--}}
<div x-data="{ 
        showModal: false, 
        modalGroup: [], 
        selectedCheckpointIndex: 0,
        
        // Properti bantu untuk mengambil data dari checkpoint yang dipilih
        get currentCheckpoint() { return this.modalGroup[this.selectedCheckpointIndex] || null; },
        get modalTitle() { return this.currentCheckpoint ? 'DETAIL ' + this.currentCheckpoint.jenis_patroli : 'DETAIL'; },
        get modalPhoto() { return this.currentCheckpoint ? '{{ asset('storage') }}/' + this.currentCheckpoint.foto : ''; },
        get modalWaktu() { 
            if (!this.currentCheckpoint) return '';
            // Format waktu dari 'waktu_exact'
            return new Date(this.currentCheckpoint.waktu_exact).toLocaleTimeString('id-ID', { 
                hour: '2-digit', minute: '2-digit', second: '2-digit' 
            });
        }
    }">

    <div class="mt-4 p-4 bg-white rounded-lg shadow">
        <h3 class="text-xs font-semibold text-gray-500 uppercase mb-2">DAFTAR PATROLI :</h3>
        <div class="flex items-center justify-between">
            <label for="filter-tanggal" class="text-sm font-semibold text-gray-700">TANGGAL :</label>
            <button id="filter-tanggal" class="flex-grow-0 flex items-center justify-between bg-slate-800 text-white text-sm font-semibold px-4 py-2 rounded-full shadow-md w-auto">
                <span>{{ $tanggalTerpilih->format('d/m/Y') }}</span>
                <svg class="w-4 h-4 ml-2" ...></svg>
            </button>
        </div>
    </div>

    <div class="mt-4 mb-20 bg-white rounded-lg shadow">
        <div class="overflow-x-auto rounded-lg">
            <table class="w-full text-sm">
                <thead class="bg-slate-800 text-white">
                    <tr class_="text-left">
                        <th class="p-3 font-semibold">NO</th>
                        <th class="p-3 font-semibold">FOTO</th>
                        <th class="p-3 font-semibold">WAKTU</th>
                        <th class="p-3 font-semibold">NAMA</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    {{-- 
                      PERUBAHAN: Loop $patrolGroups dari controller
                      $jenisPatroli akan berisi 'Patroli 1', 'Patroli 2', dst.
                      $checkpoints adalah COLLECTION berisi semua data untuk grup tsb.
                    --}}
                    @forelse($patrolGroups as $jenisPatroli => $checkpoints)
                    <tr class="border-b">
                        <td class="p-3 text-center">{{ $loop->iteration }}</td>
                        <td class="p-3 text-center">
                            <a href="#" 
                               @click.prevent="
                                   showModal = true;
                                   // Kirim SEMUA data checkpoint ke 'modalGroup'
                                   // .values() penting untuk mengubahnya jadi array
                                   modalGroup = {{ $checkpoints->values() }}; 
                                   selectedCheckpointIndex = 0; // Tampilkan foto pertama
                               "
                               class="text-blue-600 underline font-semibold">
                                Buka
                            </a>
                        </td>
                        <td class="p-3 font-medium">{{ $jenisPatroli }}</td>
                        {{-- Ambil nama dari data checkpoint pertama --}}
                        <td class="p-3 font-medium">{{ $checkpoints->first()->nama_lengkap }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-4 text-center text-gray-500">
                            Belum ada data patroli untuk tanggal ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div 
        x-show="showModal" 
        @keydown.escape.window="showModal = false"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
        style="display: none;"
    >
        <div 
            @click.outside="showModal = false"
            class="bg-white rounded-lg shadow-xl w-full max-w-xs"
        >
            <div class="flex justify-between items-center p-4 border-b">
                {{-- Judul diisi oleh getter 'modalTitle' --}}
                <h3 class="text-lg font-bold text-gray-800" x-text="modalTitle"></h3>
                <button @click="showModal = false" class="text-gray-500 hover:text-gray-800">
                    <svg class="w-6 h-6" ...>X</svg>
                </button>
            </div>
            <div class="p-4 space-y-4">
                {{-- Foto diisi oleh getter 'modalPhoto' --}}
                <img :src="modalPhoto" alt="Foto Patroli" class="w-full h-auto rounded-lg bg-gray-200 shadow-inner">
                
                <div>
                    <label class="text-xs font-semibold text-gray-500">Wilayah Patroli</label>
                    {{-- 
                      'x-model.number' diikat ke 'selectedCheckpointIndex'
                      Saat dropdown berubah, index berubah, lalu foto & waktu ikut berubah.
                    --}}
                    <select x-model.number="selectedCheckpointIndex" class="w-full bg-slate-200 text-slate-800 rounded-lg p-2 font-semibold border-0 focus:ring-2 focus:ring-blue-500">
                        {{-- Loop 'modalGroup' (data checkpoint) --}}
                        <template x-for="(checkpoint, index) in modalGroup" :key="index">
                            <option :value="index" x-text="checkpoint.wilayah"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-500">Waktu</label>
                    {{-- Waktu diisi oleh getter 'modalWaktu' --}}
                    <div class="w-full bg-slate-800 text-white rounded-lg p-2 font-semibold text-center" x-text="modalWaktu">
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

{{-- 3. Tambahkan Tombol FAB (+) (Tetap sama) --}}
@push('fab')
    <a href="{{ route('anggota.patroli.createSession') }}" 
       class="fixed z-50 bottom-6 right-6 md:right-[calc((100vw-768px)/2+24px)] p-4 bg-blue-700 rounded-full text-white shadow-lg hover:bg-blue-800 transition">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
    </a>
@endpush