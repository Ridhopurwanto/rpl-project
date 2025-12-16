@extends('layouts.app')

@section('header-left')
    <a class="flex items-center border-2 border-[#1a2847] text-[#1a2847] text-sm font-bold px-4 py-2 rounded-full">
        UNDUH
    </a>
@endsection

@section('content')
<div class="w-full mx-auto mb-20 px-3 sm:px-4"
     x-data="{ 
         reportType: 'harian',
         dateFrom: '{{ now()->format('Y-m-d') }}',
         dateTo: '{{ now()->format('Y-m-d') }}',
         
         selectedChecks: [], 
         
         isBarangActive: false, 
         barangChecks: [],

         downloadQueue: [],
         baseUrlSingle: '{{ url('/supervisor/laporan/download-single') }}', 

         toggleBarang() {
             if (this.isBarangActive) {
                 this.barangChecks = ['barang_temu', 'barang_titip'];
             } else {
                 this.barangChecks = [];
             }
         },

         updateParent() {
             if (this.barangChecks.length === 2) {
                 this.isBarangActive = true;
             } else {
                 this.isBarangActive = false;
             }
         },

         addToQueue() {
             const hasBarang = this.barangChecks.length > 0;
             
             if (this.selectedChecks.length === 0 && !hasBarang) {
                 alert('Pilih minimal satu laporan dulu!');
                 return;
             }

             this.selectedChecks.forEach(checkId => {
                 this.pushItemToQueue(checkId);
             });

             this.barangChecks.forEach(barangId => {
                 this.pushItemToQueue(barangId);
             });

             this.selectedChecks = [];
             this.isBarangActive = false;
             this.barangChecks = [];
         },

         pushItemToQueue(value) {
             this.downloadQueue.push({
                 id: Date.now() + Math.random(),
                 value: value, 
                 label: this.formatLabel(value),
                 dateStart: this.dateFrom,
                 dateEnd: this.dateTo,
                 periodeDisplay: this.formatDate(this.dateFrom) + ' - ' + this.formatDate(this.dateTo),
                 tipe: this.reportType
             });
         },

         downloadSingle(url) {
             window.location.href = url;
         },

         formatLabel(str) {
             const map = {
                 'barang_temu': 'Laporan Barang Temuan',
                 'barang_titip': 'Laporan Barang Titipan',
                 'laporan_presensi': 'Laporan Presensi',
                 'laporan_patroli': 'Laporan Patroli',
                 'gangguan_kamtibmas': 'Laporan Gangguan Kamtibmas',
                 'shift_anggota': 'Laporan Shift Anggota',
                 'laporan_anggota': 'Tabel Anggota',
                 'laporan_kendaraan_terdaftar': 'Tabel Kendaraan Terdaftar'
             };
             if (map[str]) return map[str];
             return str.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
         },

         formatDate(dateStr) {
            if(!dateStr) return '';
            let parts = dateStr.split('-');
            return parts[2] + '/' + parts[1] + '/' + parts[0];
         }
     }"
>
    <h2 class="text-xl sm:text-2xl font-bold text-slate-800 mb-4">Unduh Laporan Gabungan</h2>

    <form action="{{ route('supervisor.laporan.download') }}" method="POST">
        @csrf
        <input type="hidden" name="download_queue" :value="JSON.stringify(downloadQueue)">

        {{-- FILTER SECTION - DIPERBAIKI UNTUK MOBILE --}}
        <div class="bg-white px-4 sm:px-6 py-4 sm:py-5 rounded-xl shadow-sm mb-6 border border-gray-200">
            
            <div class="grid grid-cols-1 gap-4 sm:gap-6">
                
                {{-- 1. JENIS LAPORAN --}}
                <div class="w-full">
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                        Jenis Laporan
                    </label>
                    <div class="relative">
                        <select x-model="reportType" 
                                class="block w-full h-[42px] px-3 sm:px-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer appearance-none">
                            <option value="harian">Laporan Harian</option>
                            <option value="bulanan">Laporan Bulanan</option>
                            <option value="administrasi">Data Administrasi</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- 2. DATE RANGE PICKER - DIPERBAIKI --}}
                <div class="w-full">
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">
                        Periode Tanggal
                    </label>
                    <div class="flex flex-col sm:flex-row items-stretch gap-2 sm:gap-3">
                        <div class="flex-1 min-w-0">
                            <input type="date" x-model="dateFrom" x-ref="dateStart"
                                   @click="$el.showPicker()"
                                   class="block w-full h-[42px] px-3 sm:px-4 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer">
                        </div>
                        <span class="text-gray-500 text-sm font-medium self-center text-center sm:text-left">s/d</span>
                        <div class="flex-1 min-w-0">
                            <input type="date" x-model="dateTo" x-ref="dateEnd"
                                   @click="$el.showPicker()"
                                   class="block w-full h-[42px] px-3 sm:px-4 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer">
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- PREVIEW CHECKBOXES --}}
        <div class="bg-white p-4 rounded-lg shadow-md mb-6">
            <h3 class="font-bold text-gray-800 mb-3 border-b pb-2 text-sm sm:text-base">PREVIEW (PILIH LAPORAN)</h3>
            
            <div x-show="reportType === 'harian'" class="space-y-3">
                
                <label class="flex items-center cursor-pointer hover:bg-slate-50 p-2 rounded">
                    <input type="checkbox" value="laporan_presensi" x-model="selectedChecks" class="rounded text-[#1a2847] w-5 h-5 flex-shrink-0 focus:ring-[#1a2847] accent-[#365c82]">
                    <span class="ml-3 text-gray-700 text-sm sm:text-base">Laporan Presensi</span>
                </label>
                
                <label class="flex items-center cursor-pointer hover:bg-slate-50 p-2 rounded">
                    <input type="checkbox" value="laporan_patroli" x-model="selectedChecks" class="rounded text-[#1a2847] w-5 h-5 flex-shrink-0 focus:ring-[#1a2847] accent-[#365c82]">
                    <span class="ml-3 text-gray-700 text-sm sm:text-base">Laporan Patroli</span>
                </label>
                
                {{-- FITUR PENGELOLAAN BARANG --}}
                <div class="group">
                    <label class="flex items-center cursor-pointer hover:bg-slate-50 p-2 rounded transition">
                        <input type="checkbox" x-model="isBarangActive" @change="toggleBarang()" 
                               class="rounded text-[#1a2847] w-5 h-5 flex-shrink-0 border-gray-300 focus:ring-[#1a2847] accent-[#365c82]">
                        
                        <span class="ml-3 text-gray-700 text-sm sm:text-base flex-1">Laporan Pengelolaan Barang</span>
                        
                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0 transform transition-transform duration-200" 
                             :class="isBarangActive || barangChecks.length > 0 ? 'rotate-180' : ''"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </label>

                    <div x-show="isBarangActive || barangChecks.length > 0" 
                         x-transition.origin.top.duration.300ms
                         class="ml-6 sm:ml-7 mt-1 space-y-1 border-l-2 border-gray-200 pl-3">
                        
                        <label class="flex items-center cursor-pointer hover:bg-slate-50 p-2 rounded">
                            <input type="checkbox" value="barang_temu" x-model="barangChecks" @change="updateParent()" 
                                   class="rounded text-[#1a2847] w-4 h-4 flex-shrink-0 border-gray-300 focus:ring-[#1a2847] accent-[#365c82]">
                            <span class="ml-2 text-gray-600 text-xs sm:text-sm">Barang Temuan</span>
                        </label>
                        
                        <label class="flex items-center cursor-pointer hover:bg-slate-50 p-2 rounded">
                            <input type="checkbox" value="barang_titip" x-model="barangChecks" @change="updateParent()" 
                                   class="rounded text-[#1a2847] w-4 h-4 flex-shrink-0 border-gray-300 focus:ring-[#1a2847] accent-[#365c82]">
                            <span class="ml-2 text-gray-600 text-xs sm:text-sm">Barang Titipan</span>
                        </label>
                    </div>
                </div>
                
                <label class="flex items-center cursor-pointer hover:bg-slate-50 p-2 rounded">
                    <input type="checkbox" value="kendaraan" x-model="selectedChecks" class="rounded text-[#1a2847] w-5 h-5 flex-shrink-0 focus:ring-[#1a2847] accent-[#365c82]">
                    <span class="ml-3 text-gray-700 text-sm sm:text-base">Laporan Kendaraan</span>
                </label>
                
                <label class="flex items-center cursor-pointer hover:bg-slate-50 p-2 rounded">
                    <input type="checkbox" value="tamu" x-model="selectedChecks" class="rounded text-[#1a2847] w-5 h-5 flex-shrink-0 focus:ring-[#1a2847] accent-[#365c82]">
                    <span class="ml-3 text-gray-700 text-sm sm:text-base">Laporan Tamu</span>
                </label>
            </div>

            <div x-show="reportType === 'bulanan'" class="space-y-3" style="display: none;">
                <label class="flex items-center cursor-pointer hover:bg-slate-50 p-2 rounded">
                    <input type="checkbox" value="gangguan_kamtibmas" x-model="selectedChecks" class="rounded text-[#1a2847] w-5 h-5 flex-shrink-0 focus:ring-[#1a2847] accent-[#365c82]">
                    <span class="ml-3 text-gray-700 text-sm sm:text-base">Laporan Gangguan Kamtibmas</span>
                </label>
                <label class="flex items-center cursor-pointer hover:bg-slate-50 p-2 rounded">
                    <input type="checkbox" value="shift_anggota" x-model="selectedChecks" class="rounded text-[#1a2847] w-5 h-5 flex-shrink-0 focus:ring-[#1a2847] accent-[#365c82]">
                    <span class="ml-3 text-gray-700 text-sm sm:text-base">Laporan Shift Anggota</span>
                </label>
            </div>

            <div x-show="reportType === 'administrasi'" class="space-y-3" style="display: none;">
                <label class="flex items-center cursor-pointer hover:bg-slate-50 p-2 rounded">
                    <input type="checkbox" value="laporan_anggota" x-model="selectedChecks" class="rounded text-[#1a2847] w-5 h-5 flex-shrink-0 focus:ring-[#1a2847] accent-[#365c82]">
                    <span class="ml-3 text-gray-700 text-sm sm:text-base">Tabel Anggota</span>
                </label>
                <label class="flex items-center cursor-pointer hover:bg-slate-50 p-2 rounded">
                    <input type="checkbox" value="laporan_kendaraan_terdaftar" x-model="selectedChecks" class="rounded text-[#1a2847] w-5 h-5 flex-shrink-0 focus:ring-[#1a2847] accent-[#365c82]">
                    <span class="ml-3 text-gray-700 text-sm sm:text-base">Tabel Kendaraan Terdaftar</span>
                </label>
            </div>

            <div class="flex justify-end mt-4 pt-2 border-t">
                <button type="button" @click="addToQueue()" 
                        class="bg-[#1e4275] text-white px-5 sm:px-6 py-2 rounded-lg shadow font-semibold text-sm hover:bg-blue-900 transition transform active:scale-95">
                    Tambah
                </button>
            </div>
        </div>

        {{-- TABEL ANTRIAN - DIPERBAIKI UNTUK MOBILE --}}
        <div class="bg-white p-3 sm:p-4 rounded-lg shadow-md mb-6" x-show="downloadQueue.length > 0">
            <h3 class="font-bold text-[#1e4275] text-sm mb-3">Laporan yang diunduh</h3>
            <div class="overflow-x-auto -mx-3 sm:mx-0">
                <div class="inline-block min-w-full align-middle px-3 sm:px-0">
                    <div class="overflow-hidden border border-gray-200 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200 text-sm text-left">
                            <thead class="bg-[#1e4275] text-white">
                                <tr>
                                    <th class="px-2 sm:px-4 py-2 text-center w-8 sm:w-10 text-xs sm:text-sm">No.</th>
                                    <th class="px-2 sm:px-4 py-2 text-xs sm:text-sm">Jenis Laporan</th>
                                    <th class="px-2 sm:px-4 py-2 text-center w-20 sm:w-32 text-xs sm:text-sm">Unduh</th> 
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <template x-for="(item, index) in downloadQueue" :key="item.id">
                                    <tr>
                                        <td class="px-2 sm:px-4 py-3 text-center text-gray-600 text-xs sm:text-sm" x-text="index + 1 + '.'"></td>
                                        <td class="px-2 sm:px-4 py-3 text-gray-800">
                                            <div class="font-bold text-slate-700 text-xs sm:text-sm" x-text="item.label"></div>
                                            <div class="text-[10px] sm:text-xs text-gray-500 mt-0.5" x-text="item.periodeDisplay"></div>
                                            <button type="button" @click="downloadQueue.splice(index, 1)" 
                                                    class="text-[10px] sm:text-xs text-red-400 hover:text-red-600 underline mt-1">
                                                Hapus
                                            </button>
                                        </td>
                                        <td class="px-2 sm:px-4 py-3 text-center align-middle">
                                            <div class="flex justify-center gap-1 sm:gap-2">
                                                <button type="button"
                                                   @click.prevent="downloadSingle(baseUrlSingle + '?type=' + item.value + '&format=excel&start=' + item.dateStart + '&end=' + item.dateEnd)"
                                                   class="bg-green-100 text-green-700 p-1.5 sm:p-2 rounded hover:bg-green-200 transition border border-green-200 cursor-pointer" 
                                                   title="Download Excel">
                                                   <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="sm:w-5 sm:h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2z"/><path d="M3 7l9 6 9-6"/></svg>
                                                </button>
                                                <button type="button"
                                                   @click.prevent="downloadSingle(baseUrlSingle + '?type=' + item.value + '&format=pdf&start=' + item.dateStart + '&end=' + item.dateEnd)"
                                                   class="bg-red-100 text-red-700 p-1.5 sm:p-2 rounded hover:bg-red-200 transition border border-red-200 cursor-pointer" 
                                                   title="Download PDF">
                                                   <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="sm:w-5 sm:h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 space-y-3">
            <button type="submit" name="format" value="excel" :disabled="downloadQueue.length === 0" 
                    class="w-full bg-green-600 text-white font-bold py-3 px-4 rounded-lg shadow hover:bg-green-700 transition disabled:opacity-50 disabled:cursor-not-allowed text-sm sm:text-base">
                UNDUH LAPORAN GABUNGAN (EXCEL)
            </button>
            <button type="submit" name="format" value="pdf" :disabled="downloadQueue.length === 0" 
                    class="w-full bg-red-600 text-white font-bold py-3 px-4 rounded-lg shadow hover:bg-red-700 transition disabled:opacity-50 disabled:cursor-not-allowed text-sm sm:text-base">
                UNDUH LAPORAN GABUNGAN (PDF)
            </button>
        </div>
    </form>
</div>
@endsection
