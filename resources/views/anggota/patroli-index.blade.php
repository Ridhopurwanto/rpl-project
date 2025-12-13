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

        {{-- Filter Tanggal --}}
        <div class="bg-white px-6 py-5 rounded-xl shadow-sm mt-4 mb-6 border border-gray-200">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-700 uppercase tracking-wide">RIWAYAT PATROLI</h3>
                    <p class="text-xs text-gray-500 mt-1">Pilih tanggal untuk melihat detail catatan patroli.</p>
                </div>
                <form action="{{ route('anggota.patroli.index') }}" method="GET" class="w-full md:w-auto">
                    <div class="w-full md:w-64">
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ $tanggalTerpilih->format('Y-m-d') }}" max="{{ date('Y-m-d') }}" onchange="this.form.submit()"
                            class="block w-full h-[42px] px-4 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg shadow-sm cursor-pointer">
                    </div>
                </form>
            </div>
        </div>

        {{-- LIST PATROLI --}}
        <div class="mt-4 mb-32 space-y-3">
            @if ($displayData->isEmpty())
                <div class="bg-white rounded-xl shadow-md p-8 text-center">
                    <p class="text-gray-500 font-semibold">Tidak ada jadwal patroli aktif atau data tidak ditemukan.</p>
                </div>
            @else
                @foreach($displayData as $item)
                    
                    {{-- CARD 1: EXPIRED (TERLEWAT) --}}
                    @if($item['is_expired'])
                        <div class="bg-white rounded-xl shadow-md overflow-hidden border-2 border-red-300">
                            <div class="bg-gradient-to-r from-red-500 to-red-600 px-4 py-2.5 flex justify-between items-center">
                                <div>
                                    <p class="text-xs text-red-100 font-semibold uppercase">Jenis Patroli</p>
                                    <p class="text-white font-bold text-base">{{ $item['jenis_patroli'] }}</p>
                                </div>
                                <span class="bg-red-700 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg uppercase flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293z" clip-rule="evenodd"></path></svg>
                                    Terlewat
                                </span>
                            </div>
                            <div class="p-4 bg-red-50">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-red-200 rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-bold text-red-800">Patroli Belum Dilaksanakan</p>
                                        <p class="text-xs text-red-600">Batas waktu: {{ $item['waktu_batas'] }} WIB</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    {{-- CARD 2: PROSES / SELESAI / AVAILABLE --}}
                    @else
                        <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                            {{-- Header --}}
                            <div class="bg-gradient-to-b from-[#243a5e] via-[#2a4a6f] to-[#365c82] px-4 py-2.5 flex justify-between items-center">
                                <div>
                                    <p class="text-xs text-blue-200 font-semibold uppercase">Jenis Patroli</p>
                                    <p class="text-white font-bold text-base">{{ $item['jenis_patroli'] }}</p>
                                </div>
                                @if($item['is_completed'])
                                    <span class="bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg uppercase flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                        Selesai
                                    </span>
                                @elseif($item['id_claim'])
                                    <span class="bg-yellow-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg uppercase">Proses</span>
                                @else
                                    <span class="bg-gray-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg uppercase">Belum Diambil</span>
                                @endif
                            </div>

                            {{-- Body --}}
                            <div class="p-4 space-y-3">
                                {{-- Petugas info --}}
                                <div class="flex items-center gap-3 pb-3 border-b border-gray-100">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-[10px] text-gray-500 font-semibold uppercase">Petugas</p>
                                        <p class="text-gray-800 font-bold text-base">{{ $item['nama_petugas'] }}</p>
                                    </div>
                                </div>

                                {{-- Progress --}}
                                <div>
                                    <div class="flex justify-between items-center mb-2">
                                        <p class="text-xs text-gray-600 font-semibold">Progress Checkpoint</p>
                                        <span class="text-xs font-bold {{ $item['is_completed'] ? 'text-green-600' : 'text-orange-600' }}">
                                            {{ $item['progress'] }} / 17
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                                        <div class="h-full rounded-full transition-all {{ $item['is_completed'] ? 'bg-green-500' : 'bg-yellow-500' }}"
                                            style="width: {{ ($item['progress'] / 17) * 100 }}%">
                                        </div>
                                    </div>
                                </div>

                                {{-- Action Button --}}
                                @if($item['has_checkpoints'])
                                    <button @click.prevent="showModal = true; modalGroup = {{ $item['checkpoints']->values() }}; selectedCheckpointIndex = 0;"
                                        class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold py-2.5 px-4 rounded-lg shadow-md transition-all transform hover:scale-[1.02] flex items-center justify-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        <span class="text-sm">LIHAT DETAIL</span>
                                    </button>
                                @else
                                    <div class="w-full bg-gray-100 text-gray-500 font-semibold py-2.5 px-4 rounded-lg flex items-center justify-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span class="text-sm">Belum ada foto</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                @endforeach
            @endif
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

        {{-- Modal Detail & Modal Off (Copy dari file lama Anda) --}}
        <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4" style="display: none;">
             {{-- ... Isi Modal Detail ... --}}
             <div @click.outside="showModal = false" class="bg-white rounded-lg shadow-xl w-full max-w-xs">
                <div class="flex justify-between items-center p-4 border-b">
                    <h3 class="text-lg font-bold text-gray-800" x-text="modalTitle"></h3>
                    <button @click="showModal = false" class="text-gray-500 hover:text-gray-800"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
                <div class="p-4 space-y-4">
                    <img :src="modalPhoto" class="w-full h-auto rounded-lg bg-gray-200">
                    <div>
                        <label class="text-xs font-semibold text-gray-500">Wilayah</label>
                        <select x-model.number="selectedCheckpointIndex" class="w-full bg-slate-200 rounded-lg p-2"><template x-for="(checkpoint, index) in modalGroup" :key="index"><option :value="index" x-text="checkpoint.wilayah"></option></template></select>
                    </div>
                    <div><label class="text-xs font-semibold text-gray-500">Waktu</label><div class="w-full bg-slate-800 text-white rounded-lg p-2 text-center" x-text="modalWaktu"></div></div>
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