@extends('layouts.app')

@section('header-left')
    <a class="flex items-center border-2 border-[#1a2847] text-[#1a2847] text-sm font-bold px-4 py-2 rounded-full">
        PATROLI
    </a>
@endsection

@section('content')
<div class="w-full mx-auto" 
     x-data="{ 
         showPhotoModal: false, 
         photoUrl: '', 
         showDeleteModal: false,
         deleteAction: '',
         showRulesModal: false
     }">
    
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold text-slate-800">Laporan Patroli Anggota</h2>
        <button @click="showRulesModal = true" 
                class="bg-[#2a4a6f] text-white p-2.5 rounded-lg shadow-md hover:bg-[#1e3a5f] transition" 
                title="Pengaturan Jam Patroli">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
        </button>
    </div>

    {{-- Tampilkan Notifikasi Sukses/Error --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span>{{ session('error') }}</span>
        </div>
    @endif

{{-- SHIFT PAGI - Daftar Patroli --}}
<div id="pagi-container" class="bg-white rounded-lg shadow-md overflow-hidden mb-6" x-data="{ showPagi: true }">
    <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] p-3 border-b border-[#2a4a6f] cursor-pointer hover:bg-[#2a4a6f] transition" @click="showPagi = !showPagi">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-300" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                </svg>
                <h3 class="font-bold text-white">SHIFT PAGI</h3>
            </div>
            <svg class="w-5 h-5 text-white transition-transform" :class="{ 'rotate-180': !showPagi }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </div>
    </div>

    <div x-show="showPagi" x-collapse>
        
        {{-- Form Filter Shift Pagi --}}
        <div class="px-6 py-5 border-b border-gray-200">
            <div class="flex flex-wrap gap-4">
                
                {{-- Show Entries --}}
                <div class="w-[calc(50%-0.5rem)] md:w-auto">
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Show</label>
                    <div class="flex items-center gap-2">
                        <div class="relative">
                            <select id="perPagePagi" name="per_page_pagi" class="filter-input block h-[42px] pl-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer appearance-none">
                                <option value="5" {{ $perPagePagi == 5 ? 'selected' : '' }}>5</option>
                                <option value="10" {{ $perPagePagi == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ $perPagePagi == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ $perPagePagi == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ $perPagePagi == 100 ? 'selected' : '' }}>100</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                        <span class="text-sm text-gray-600 whitespace-nowrap">rows</span>
                    </div>
                </div>
                
                {{-- Filter Tanggal --}}
                <div class="w-[calc(50%-0.5rem)] md:flex-1">
                    <label for="tanggal_pagi" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                        Tanggal
                    </label>
                    <div class="cursor-pointer" @click="$refs.dateInputPagi.showPicker()">
                        <input type="date" id="tanggal_pagi" name="tanggal" x-ref="dateInputPagi"
                            class="filter-input block w-full h-[42px] px-4 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer"
                            value="{{ $tanggalTerpilih }}">
                    </div>
                </div>

                {{-- Filter Jenis Patroli --}}
                <div class="w-[calc(50%-0.5rem)] md:flex-1">
                    <label for="jenis_patroli_pagi" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                        Jenis Patroli
                    </label>
                    <div class="relative">
                        <select id="jenis_patroli_pagi" name="jenis_patroli_pagi" 
                                class="filter-input block w-full h-[42px] px-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer appearance-none">
                            @forelse($jenisPatroliOptions as $opsi)
                                <option value="{{ $opsi }}" {{ $jenisPatroliTerpilihPagi == $opsi ? 'selected' : '' }}>
                                    {{ $opsi }}
                                </option>
                            @empty
                                <option value="" disabled selected>Tidak ada data jenis patroli</option>
                            @endforelse
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="patroli-pagi-wrapper">
             @include('komandan.partials.patroli-list', ['data' => $dataPatroliPagi, 'shift' => 'pagi'])
        </div>
        
    </div>
</div>

{{-- SHIFT MALAM - Daftar Patroli --}}
<div id="malam-container" class="bg-white rounded-lg shadow-md overflow-hidden mb-6" x-data="{ showMalam: true }">
    <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] p-3 border-b border-[#2a4a6f] cursor-pointer hover:bg-[#2a4a6f] transition" @click="showMalam = !showMalam">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-300" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                </svg>
                <h3 class="font-bold text-white">SHIFT MALAM</h3>
            </div>
            <svg class="w-5 h-5 text-white transition-transform" :class="{ 'rotate-180': !showMalam }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </div>
    </div>

    <div x-show="showMalam" x-collapse>
        
        {{-- Form Filter Shift Malam --}}
        <div class="px-6 py-5 border-b border-gray-200">
            <div class="flex flex-wrap gap-4">
                
                {{-- Show Entries --}}
                <div class="w-[calc(50%-0.5rem)] md:w-auto">
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Show</label>
                    <div class="flex items-center gap-2">
                        <div class="relative">
                            <select id="perPageMalam" name="per_page_malam" class="filter-input block h-[42px] pl-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer appearance-none">
                                <option value="5" {{ $perPageMalam == 5 ? 'selected' : '' }}>5</option>
                                <option value="10" {{ $perPageMalam == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ $perPageMalam == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ $perPageMalam == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ $perPageMalam == 100 ? 'selected' : '' }}>100</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                        <span class="text-sm text-gray-600 whitespace-nowrap">rows</span>
                    </div>
                </div>
                
                {{-- Filter Tanggal --}}
                <div class="w-[calc(50%-0.5rem)] md:flex-1">
                    <label for="tanggal_malam" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                        Tanggal
                    </label>
                    <div class="cursor-pointer" @click="$refs.dateInputMalam.showPicker()">
                        <input type="date" id="tanggal_malam" name="tanggal" x-ref="dateInputMalam"
                            class="filter-input block w-full h-[42px] px-4 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer"
                            value="{{ $tanggalTerpilih }}">
                    </div>
                </div>

                {{-- Filter Jenis Patroli --}}
                <div class="w-[calc(50%-0.5rem)] md:flex-1">
                    <label for="jenis_patroli_malam" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                        Jenis Patroli
                    </label>
                    <div class="relative">
                        <select id="jenis_patroli_malam" name="jenis_patroli_malam" 
                                class="filter-input block w-full h-[42px] px-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer appearance-none">
                            @forelse($jenisPatroliOptions as $opsi)
                                <option value="{{ $opsi }}" {{ $jenisPatroliTerpilihMalam == $opsi ? 'selected' : '' }}>
                                    {{ $opsi }}
                                </option>
                            @empty
                                <option value="" disabled selected>Tidak ada data jenis patroli</option>
                            @endforelse
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="patroli-malam-wrapper">
             @include('komandan.partials.patroli-list', ['data' => $dataPatroliMalam, 'shift' => 'malam'])
        </div>
        
    </div>
</div>



    {{-- MODAL FOTO --}}
    <div x-show="showPhotoModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
        @click.away="showPhotoModal = false" style="display: none;">
        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full relative overflow-hidden" @click.stop>
            {{-- Header Biru --}}
            <div class="bg-[#1e3a5f] py-4 px-6 border-b border-[#1e3a5f] flex justify-between items-center">
                <h3 class="text-lg font-bold text-white flex items-center tracking-wide">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                    FOTO PATROLI
                </h3>
                <button @click="showPhotoModal = false" class="text-white/70 hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
            <div class="p-4">
                <img :src="photoUrl" alt="Foto Patroli" class="w-full h-auto rounded">
            </div>
        </div>
    </div>


    {{-- Modal Hapus --}}
    <div x-show="showDeleteModal"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
         @click.away="showDeleteModal = false"
         style="display: none;">
        <div class="bg-white rounded-lg shadow-xl max-w-sm w-full p-6 relative" @click.stop>
            <h3 class="text-lg font-bold text-gray-900 mb-4">Konfirmasi Hapus</h3>
            <p class="text-gray-600 mb-6">
                Apakah Anda yakin ingin menghapus data patroli ini? Tindakan ini tidak dapat dibatalkan.
            </p>
            <form :action="deleteAction" method="POST" class="flex justify-end space-x-4">
                @csrf
                @method('DELETE')
                <button type="button" @click="showDeleteModal = false"
                    class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300">
                    Batal
                </button>
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                    Ya, Hapus
                </button>
            </form>
        </div>
    </div>

    {{-- Modal Pengaturan Jam Patroli --}}
    <div x-show="showRulesModal"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
         @click.away="showRulesModal = false"
         style="display: none;">
        <div class="bg-white rounded-2xl shadow-2xl max-w-5xl w-full max-h-[90vh] flex flex-col" @click.stop>
            
            {{-- Header Modal --}}
            <div class="bg-[#1e3a5f] px-6 py-4 rounded-t-2xl flex items-center justify-between flex-shrink-0 border-b border-[#1e3a5f]">
                <h3 class="text-lg font-bold text-white flex items-center tracking-wide">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                        </path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                        </path>
                    </svg>
                    PENGATURAN JAM PATROLI
                </h3>
                <button @click="showRulesModal = false" class="text-white/70 hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            {{-- Body Modal --}}
            <div class="overflow-y-auto flex-1">
                <form action="{{ route('komandan.patroli.updateRules') }}" method="POST" class="p-6">
                @csrf
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    {{-- SHIFT PAGI --}}
                    <div class="bg-amber-50 rounded-xl p-5 border-2 border-amber-200">
                        <h4 class="text-lg font-bold text-amber-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                            </svg>
                            SHIFT PAGI
                        </h4>
                        
                        @foreach(['Patroli 1', 'Patroli 2', 'Patroli 3', 'Patroli 4', 'Patroli 5', 'Patroli 6'] as $patroli)
                            @php
                                $rule = isset($patroliRules['Pagi']) ? $patroliRules['Pagi']->firstWhere('jenis_patroli', $patroli) : null;
                            @endphp
                            <div class="mb-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">{{ $patroli }}</label>
                                <div class="flex items-center gap-2">
                                    <input type="time" 
                                           name="shift_pagi[{{ $patroli }}][jam_mulai]" 
                                           value="{{ $rule ? \Carbon\Carbon::parse($rule->jam_mulai)->format('H:i') : '07:00' }}"
                                           class="flex-1 border-gray-300 rounded-lg shadow-sm text-sm">
                                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                    <input type="time" 
                                           name="shift_pagi[{{ $patroli }}][jam_selesai]" 
                                           value="{{ $rule ? \Carbon\Carbon::parse($rule->jam_selesai)->format('H:i') : '19:00' }}"
                                           class="flex-1 border-gray-300 rounded-lg shadow-sm text-sm">
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- SHIFT MALAM --}}
                    <div class="bg-blue-50 rounded-xl p-5 border-2 border-blue-200">
                        <h4 class="text-lg font-bold text-blue-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                            </svg>
                            SHIFT MALAM
                        </h4>
                        
                        @foreach(['Patroli 1', 'Patroli 2', 'Patroli 3', 'Patroli 4', 'Patroli 5', 'Patroli 6'] as $patroli)
                            @php
                                $rule = isset($patroliRules['Malam']) ? $patroliRules['Malam']->firstWhere('jenis_patroli', $patroli) : null;
                            @endphp
                            <div class="mb-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">{{ $patroli }}</label>
                                <div class="flex items-center gap-2">
                                    <input type="time" 
                                           name="shift_malam[{{ $patroli }}][jam_mulai]" 
                                           value="{{ $rule ? \Carbon\Carbon::parse($rule->jam_mulai)->format('H:i') : '19:00' }}"
                                           class="flex-1 border-gray-300 rounded-lg shadow-sm text-sm">
                                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                    <input type="time" 
                                           name="shift_malam[{{ $patroli }}][jam_selesai]" 
                                           value="{{ $rule ? \Carbon\Carbon::parse($rule->jam_selesai)->format('H:i') : '07:00' }}"
                                           class="flex-1 border-gray-300 rounded-lg shadow-sm text-sm">
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- NON SHIFT - Placeholder --}}
                    <div class="bg-gray-50 rounded-xl p-5 border-2 border-gray-200">
                        <h4 class="text-lg font-bold text-gray-700 mb-4">NON SHIFT</h4>
                        <p class="text-sm text-gray-500 italic">Pengaturan non-shift dapat ditambahkan jika diperlukan.</p>
                    </div>

                </div>

                    {{-- Footer/Action Buttons --}}
                    <div class="mt-6 p-4 border-t bg-gray-50 flex justify-end gap-3">
                        <button type="button" @click="showRulesModal = false" 
                                class="bg-gray-200 text-gray-800 px-6 py-2.5 rounded-lg font-semibold hover:bg-gray-300 transition">
                            Batal
                        </button>
                        <button type="submit" 
                                class="bg-[#1e3a5f] text-white px-6 py-2.5 rounded-xl font-bold hover:bg-[#2a4a6f] shadow-lg transition transform hover:-translate-y-0.5 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            SIMPAN PERUBAHAN
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    {{-- Loading Indicator --}}
    <div id="loading-indicator" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-30">
        <div class="bg-white p-4 rounded-lg shadow-xl flex items-center gap-3">
            <div class="animate-spin rounded-full h-8 w-8 border-4 border-[#1e3a5f] border-t-transparent"></div>
            <span class="font-bold text-[#1e3a5f]">Memuat Data...</span>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterInputs = document.querySelectorAll('.filter-input');
        
        // Sync Dates
        const datePagi = document.getElementById('tanggal_pagi');
        const dateMalam = document.getElementById('tanggal_malam');
        
        if(datePagi && dateMalam) {
            datePagi.addEventListener('change', () => dateMalam.value = datePagi.value);
            dateMalam.addEventListener('change', () => datePagi.value = dateMalam.value);
        }

        // Handle Filter Changes
        filterInputs.forEach(input => {
            input.addEventListener('change', function() {
                fetchData();
            });
        });

        // Handle Pagination
        document.addEventListener('click', function(e) {
            if (e.target.closest('.pagination-link')) {
                e.preventDefault();
                const url = e.target.closest('.pagination-link').href;
                fetchData(url);
            }
        });

        function fetchData(url = null) {
            toggleLoading(true);
            
            // Build URL if not provided (filter change)
            if (!url) {
                const params = new URLSearchParams();
                
                // Get Date (Pagi/Malam synced so just take one)
                params.append('tanggal', document.getElementById('tanggal_pagi').value);
                
                params.append('per_page_pagi', document.getElementById('perPagePagi').value);
                params.append('jenis_patroli_pagi', document.getElementById('jenis_patroli_pagi').value);
                
                params.append('per_page_malam', document.getElementById('perPageMalam').value);
                params.append('jenis_patroli_malam', document.getElementById('jenis_patroli_malam').value);
                
                url = "{{ route('komandan.patroli') }}?" + params.toString();
            }

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('patroli-pagi-wrapper').innerHTML = data.html_pagi;
                document.getElementById('patroli-malam-wrapper').innerHTML = data.html_malam;
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memuat data.');
            })
            .finally(() => {
                toggleLoading(false);
            });
        }
        
        function toggleLoading(show) {
            const loader = document.getElementById('loading-indicator');
            if (loader) loader.style.display = show ? 'flex' : 'none';
        }
    });
</script>
@endsection
