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

    {{-- Card Layout Patroli --}}
    <div class="mt-4 mb-32 space-y-3">
        @if ($patrolGroups->isEmpty())
            {{-- Empty State --}}
            <div class="bg-white rounded-xl shadow-md p-8 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <p class="text-gray-500 font-semibold">Belum ada data patroli untuk tanggal ini.</p>
            </div>
        @else
            @foreach($patrolGroups as $jenisPatroli => $checkpoints)
                @php
                    $jumlahSelesai = $checkpoints->count();
                    $isSelesai = $jumlahSelesai >= 17;
                @endphp
                
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                    
                    {{-- Header: Jenis Patroli & Status --}}
                    <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] px-4 py-2.5 flex justify-between items-center">
                        <div>
                            <p class="text-xs text-blue-200 font-semibold uppercase">Jenis Patroli</p>
                            <p class="text-white font-bold text-base">{{ $jenisPatroli }}</p>
                        </div>
                        
                        {{-- Badge Status --}}
                        @if($isSelesai)
                            <span class="bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg uppercase flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Selesai
                            </span>
                        @else
                            <span class="bg-yellow-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg uppercase">
                                Proses
                            </span>
                        @endif
                    </div>

                    {{-- Body: Info + Tombol --}}
                    <div class="p-4 space-y-3">
                        
                        {{-- Nama Petugas --}}
                        <div class="flex items-center gap-3 pb-3 border-b border-gray-100">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-[10px] text-gray-500 font-semibold uppercase">Petugas</p>
                                <p class="text-gray-800 font-bold text-base">{{ $checkpoints->first()->nama_lengkap }}</p>
                            </div>
                        </div>

                        {{-- Progress Bar --}}
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <p class="text-xs text-gray-600 font-semibold">Progress Checkpoint</p>
                                <span class="text-xs font-bold {{ $isSelesai ? 'text-green-600' : 'text-orange-600' }}">
                                    {{ $jumlahSelesai }} / 17
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                                <div class="h-full rounded-full transition-all {{ $isSelesai ? 'bg-green-500' : 'bg-yellow-500' }}" 
                                     style="width: {{ ($jumlahSelesai / 17) * 100 }}%">
                                </div>
                            </div>
                        </div>

                        {{-- Tombol Detail --}}
                        <button 
                            @click.prevent="
                                showModal = true;
                                modalGroup = {{ $checkpoints->values() }}; 
                                selectedCheckpointIndex = 0;
                            "
                            class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold py-2.5 px-4 rounded-lg shadow-md transition-all transform hover:scale-[1.02] flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <span class="text-sm">LIHAT DETAIL</span>
                        </button>

                    </div>
                </div>
            @endforeach
        @endif
    </div>

    {{-- Tombol FAB Tambah Patroli --}}
    @if($tanggalTerpilih->isToday())
        @php
            $user = Auth::user();
            $jenisShift = $user->jenis_shift ?? 1;
            
            // Jadwal Patroli
            $jadwalPatroli = [
                1 => [ // Shift Pagi
                    'Patroli 1' => ['07:30', '08:30'],
                    'Patroli 2' => ['09:30', '10:30'],
                    'Patroli 3' => ['11:30', '12:30'],
                    'Patroli 4' => ['13:40', '14:30'],
                    'Patroli 5' => ['15:30', '16:30'],
                    'Patroli 6' => ['17:30', '18:40'],
                ],
                2 => [ // Shift Malam
                    'Patroli 1' => ['19:30', '20:20'],
                    'Patroli 2' => ['21:30', '22:30'],
                    'Patroli 3' => ['23:30', '00:30'],
                    'Patroli 4' => ['01:30', '02:30'],
                    'Patroli 5' => ['03:30', '04:30'],
                    'Patroli 6' => ['05:30', '06:30'],
                ]
            ];
            
            // Cek apakah ada patroli yang available
            $adaPatroliAvailable = false;
            $semuaSelesai = true;
            
            foreach(['Patroli 1', 'Patroli 2', 'Patroli 3', 'Patroli 4', 'Patroli 5', 'Patroli 6'] as $patroli) {
                // Cek apakah sudah selesai 17 area
                $jumlah = App\Models\Patroli::where('id_pengguna', $user->id_pengguna)
                                ->whereDate('tanggal', \Carbon\Carbon::today())
                                ->where('jenis_patroli', $patroli)
                                ->count();
                
                if ($jumlah < 17) {
                    $semuaSelesai = false;
                    
                    // Cek apakah waktu sekarang valid
                    $waktuSekarang = \Carbon\Carbon::now();
                    [$jamMulai, $jamSelesai] = $jadwalPatroli[$jenisShift][$patroli];
                    
                    $mulai = \Carbon\Carbon::parse($jamMulai);
                    $selesai = \Carbon\Carbon::parse($jamSelesai);
                    
                    // Handle midnight crossing
                    if ($selesai->lt($mulai)) {
                        $selesai->addDay();
                        if ($waktuSekarang->lt($mulai)) {
                            $waktuSekarang = $waktuSekarang->copy()->addDay();
                        }
                    }
                    
                    if ($waktuSekarang->between($mulai, $selesai)) {
                        $adaPatroliAvailable = true;
                        break;
                    }
                }
            }
            
            $fabDisabled = !$adaPatroliAvailable;
        @endphp
        
        @if($fabDisabled)
            {{-- FAB Terkunci --}}
            <div class="fixed bottom-24 right-4 z-40">
                <div class="bg-gray-400 text-white rounded-full w-16 h-16 flex items-center justify-center shadow-lg cursor-not-allowed relative group">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    
                    {{-- Tooltip --}}
                    <div class="absolute bottom-20 right-0 bg-gray-800 text-white text-xs rounded-lg px-3 py-2 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap shadow-lg">
                        @if($semuaSelesai)
                            Semua patroli sudah selesai
                        @else
                            Belum ada patroli yang aktif
                        @endif
                        <div class="absolute top-full right-4 w-0 h-0 border-l-4 border-l-transparent border-r-4 border-r-transparent border-t-4 border-t-gray-800"></div>
                    </div>
                </div>
            </div>
        @else
            {{-- FAB Normal (Aktif) --}}
            <a href="{{ route('anggota.patroli.createSession') }}" 
            :class="showModal ? 'pointer-events-none opacity-50' : ''"
            x-bind:tabindex="showModal ? -1 : 0"
            x-bind:aria-disabled="showModal ? 'true' : 'false'"
            class="fixed bottom-24 right-4 bg-[#2a4a6f] text-white rounded-full w-16 h-16 flex items-center justify-center shadow-lg transform hover:scale-110 transition-transform z-40 cursor-pointer group relative">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                
                {{-- Tooltip --}}
                <div class="absolute bottom-20 right-0 bg-green-600 text-white text-xs rounded-lg px-3 py-2 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap shadow-lg">
                    Tambah Patroli
                    <div class="absolute top-full right-4 w-0 h-0 border-l-4 border-l-transparent border-r-4 border-r-transparent border-t-4 border-t-green-600"></div>
                </div>
            </a>
        @endif
    @endif


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