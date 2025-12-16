@extends('layouts.app')

@section('header-left')
    <a class="flex items-center border-2 border-[#1a2847] text-[#1a2847] text-sm font-bold px-4 py-2 rounded-full">
        PATROLI
    </a>
@endsection

@section('content')
    <div x-data="{ 
                showModal: false, 
                modalGroup: [], 
                selectedCheckpointIndex: 0,
                showOffModal: false,
                offModalTitle: '',
                offModalMessage: '',

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

        {{-- INFO SHIFT HARI INI --}}
        @if($tanggalTerpilih->isToday() && $namaShift)
            <div class="mt-4 bg-white p-4 rounded-lg shadow-sm border-l-4 {{ $isShiftOff ? 'border-red-500' : 'border-blue-500' }}">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-bold">Shift Anda Hari Ini</p>
                        <p class="text-lg font-bold text-gray-800 uppercase">{{ $namaShift }}</p>
                    </div>
                    <div class="px-3 py-1 rounded-full text-xs font-bold {{ $isShiftOff ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' }}">
                        {{ $statusShift }}
                    </div>
                </div>
            </div>
        @endif

        {{-- BAGIAN RIWAYAT PATROLI --}}
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6 mt-4" x-data="{ isOpen: true }">
            <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] p-3 border-b border-gray-200 cursor-pointer hover:opacity-90 transition" @click="isOpen = !isOpen">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-white mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="font-bold text-white">RIWAYAT PATROLI</h3>
                    </div>
                    <svg class="w-5 h-5 text-white transition-transform" :class="{ 'rotate-180': !isOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>

            <div x-show="isOpen" x-collapse>
                {{-- Filter Tanggal --}}
                <div class="p-4 border-b border-gray-200">
                    <form action="{{ route('anggota.patroli.index') }}" method="GET">
                        <div class="w-full md:w-64">
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Tanggal</label>
                            <input type="date" name="tanggal" value="{{ $tanggalTerpilih->format('Y-m-d') }}" max="{{ date('Y-m-d') }}" onchange="this.form.submit()"
                                class="block w-full h-[42px] px-4 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer">
                        </div>
                    </form>
                </div>

                {{-- List Patroli --}}
                @if ($displayData->isEmpty())
                    <div class="p-6 text-center">
                        <div class="flex flex-col items-center justify-center py-8">
                            <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-gray-500 font-semibold text-lg mb-2">Tidak Ada Riwayat Patroli</p>
                            <p class="text-gray-400 text-sm">Belum ada aktivitas patroli pada tanggal ini</p>
                        </div>
                    </div>
                @else
                    <div class="p-3 space-y-3">
                @foreach($displayData as $item)
                    
                    {{-- CARD 1: EXPIRED (TERLEWAT) --}}
                    @if($item['is_expired'])
                        <div class="bg-white rounded-lg shadow-md overflow-hidden border-2 border-red-300 relative">
                            {{-- Badge Terlewat di Pojok Kanan Atas --}}
                            <div class="absolute top-2 right-2 z-10">
                                <span class="inline-block bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded-full shadow-md">Terlewat</span>
                            </div>
                            
                            <div class="p-3">
                                <div class="w-full">
                                    {{-- Jenis Patroli --}}
                                    <h4 class="font-bold text-gray-800 text-sm mb-1">{{ $item['jenis_patroli'] }}</h4>
                                    
                                    {{-- Info Batas Waktu --}}
                                    <div class="flex items-center gap-1 mb-2">
                                        <svg class="w-3.5 h-3.5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <p class="text-red-700 font-semibold text-xs">Batas: {{ $item['waktu_batas'] }} WIB</p>
                                    </div>

                                    {{-- Status Message --}}
                                    <div class="text-xs text-red-600 bg-red-50 p-2 rounded">
                                        Patroli belum dilaksanakan dalam batas waktu yang ditentukan
                                    </div>
                                </div>
                            </div>
                        </div>

                    {{-- CARD 2: PROSES / SELESAI / AVAILABLE --}}
                    @else
                        <div class="bg-white rounded-lg shadow-md overflow-hidden border-2 border-gray-300 relative">
                            {{-- Badge Status di Pojok Kanan Atas --}}
                            <div class="absolute top-2 right-2 z-10">
                                @if($item['is_completed'])
                                    <span class="inline-block bg-green-500 text-white text-[10px] font-bold px-2 py-1 rounded-full shadow-md">Selesai</span>
                                @elseif($item['id_claim'])
                                    <span class="inline-block bg-yellow-500 text-white text-[10px] font-bold px-2 py-1 rounded-full shadow-md">Proses</span>
                                @else
                                    <span class="inline-block bg-gray-500 text-white text-[10px] font-bold px-2 py-1 rounded-full shadow-md">Belum Diambil</span>
                                @endif
                            </div>
                            
                            <div class="flex gap-3 p-3">
                                {{-- Foto Area Pertama di Sebelah Kiri --}}
                                <div class="flex-shrink-0">
                                    @if($item['has_checkpoints'] && $item['checkpoints']->first())
                                        <div @click="showModal = true; modalGroup = {{ $item['checkpoints']->values() }}; selectedCheckpointIndex = 0;"
                                             class="w-20 h-20 rounded-lg overflow-hidden border-2 border-gray-200 cursor-pointer hover:border-blue-400 transition-colors">
                                            <img src="{{ asset('storage/' . $item['checkpoints']->first()['foto']) }}" alt="Foto Patroli" class="w-full h-full object-cover">
                                        </div>
                                    @else
                                        <div class="w-20 h-20 rounded-lg bg-gray-100 flex items-center justify-center border-2 border-gray-200">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                {{-- Info di Sebelah Kanan --}}
                                <div class="flex-1 min-w-0 flex flex-col justify-between h-20">
                                    {{-- Jenis Patroli --}}
                                    {{-- Jenis Patroli --}}
                                    <h4 class="font-bold text-gray-800 text-lg leading-tight">{{ $item['jenis_patroli'] }}</h4>
                                    
                                    <div class="mt-auto">
                                        {{-- Info Petugas --}}
                                        <div class="flex items-center gap-1 mb-1">
                                            <svg class="w-3.5 h-3.5 text-blue-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                            </svg>
                                            <p class="text-gray-700 font-semibold text-xs">{{ $item['nama_petugas'] }}</p>
                                        </div>
                                        
                                        {{-- Progress --}}
                                        <div class="flex items-center gap-1 mb-2">
                                            <svg class="w-3.5 h-3.5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <p class="text-gray-700 font-semibold text-xs">Progress {{ $item['progress'] }}/17</p>
                                        </div>

                                        {{-- Progress Bar --}}
                                        <div>
                                            <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                                <div class="h-full rounded-full transition-all {{ $item['is_completed'] ? 'bg-green-500' : 'bg-yellow-500' }}"
                                                    style="width: {{ ($item['progress'] / 17) * 100 }}%">
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                    @endif

                    @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- FAB & MODALS TETAP SAMA (Tidak perlu diubah logic-nya) --}}
        {{-- ... Kode FAB Anda ... --}}
        {{-- Tombol FAB --}}
        @if($tanggalTerpilih->isToday())
            @if(isset($isShiftOff) && $isShiftOff)
                 <div class="fixed bottom-24 right-4 z-40">
                    <button @click="showOffModal = true; offModalTitle = 'Patroli Dibatasi'; offModalMessage = 'Hari ini Anda sedang OFF/Libur.';"
                        class="bg-gray-400 text-white rounded-full w-16 h-16 flex items-center justify-center shadow-lg cursor-not-allowed">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </button>
                 </div>
            @else
                <a href="{{ route('anggota.patroli.createSession') }}" 
                    class="fixed bottom-24 right-4 bg-[#2a4a6f] text-white rounded-full w-16 h-16 flex items-center justify-center shadow-lg transform hover:scale-110 transition-transform z-40 cursor-pointer group">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                </a>
            @endif
        @endif

        {{-- Modal Detail Foto Patroli --}}
        <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4" style="display: none;">
            <div @click.outside="showModal = false" class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden">
                {{-- Header Modal --}}
                <div class="bg-[#1e3a5f] px-6 py-4 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white" x-text="modalTitle"></h3>
                    <button @click="showModal = false" class="text-gray-300 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                {{-- Body Modal --}}
                <div class="p-6 space-y-4">
                    {{-- Foto --}}
                    <div class="w-full">
                        <img :src="modalPhoto" alt="Foto Patroli" class="w-full h-64 object-cover rounded-lg bg-gray-200 border">
                    </div>

                    {{-- Selector Area Patroli --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Area Patroli:</label>
                        <select x-model.number="selectedCheckpointIndex" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <template x-for="(checkpoint, index) in modalGroup" :key="index">
                                <option :value="index" x-text="checkpoint.wilayah"></option>
                            </template>
                        </select>
                    </div>

                    {{-- Info Waktu --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Waktu Patroli:</label>
                        <div class="w-full bg-gray-800 text-white rounded-lg p-3 text-center font-mono" x-text="modalWaktu"></div>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="showOffModal" class="fixed inset-0 z-[60] flex items-center justify-center bg-black bg-opacity-60 p-4" style="display: none;">
             <div @click.outside="showOffModal = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden">
                <div class="bg-red-500 p-6 text-center"><h3 class="text-xl font-bold text-white" x-text="offModalTitle"></h3></div>
                <div class="p-6 text-center"><p class="text-gray-700" x-text="offModalMessage"></p></div>
                <div class="px-6 pb-6"><button @click="showOffModal = false" class="w-full bg-blue-600 text-white font-bold py-3 rounded-xl">Mengerti</button></div>
             </div>
        </div>

    </div>
@endsection