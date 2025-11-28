@extends('layouts.app')

@section('header-left')
    <a href="{{ url()->previous() }}" class="bg-slate-800 text-white text-sm font-semibold px-6 py-2 rounded-full shadow-md hover:bg-slate-700 transition">
        UNDUH
    </a>
@endsection

@section('content')
<div class="w-full mx-auto mb-20"
     x-data="{ 
         reportType: 'harian',
         dateFrom: '{{ now()->format('Y-m-d') }}',
         dateTo: '{{ now()->format('Y-m-d') }}',
         
         // TAMBAHAN: Variable khusus untuk filter bulan
         monthFilter: '{{ now()->format('Y-m') }}', 
         
         selectedChecks: [], 
         
         // --- LOGIKA BARANG ---
         isBarangActive: false, 
         barangChecks: [], 
         // ---------------------

         downloadQueue: [],
         baseUrlSingle: '{{ url('/komandan/laporan/download-single') }}', 

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

             // Masukkan Checkbox Biasa
             this.selectedChecks.forEach(checkId => {
                 this.pushItemToQueue(checkId);
             });

             // Masukkan Pilihan Barang
             this.barangChecks.forEach(barangId => {
                 this.pushItemToQueue(barangId);
             });

             // Reset Pilihan
             this.selectedChecks = [];
             this.isBarangActive = false;
             this.barangChecks = [];
         },

         // LOGIKA BARU: Menentukan tanggal start/end berdasarkan tipe laporan
         getDateRange() {
             if (this.reportType === 'harian') {
                 return {
                     start: this.dateFrom,
                     end: this.dateTo
                 };
             } else {
                 // Jika Bulanan: Ambil tanggal 1 s/d tanggal terakhir bulan itu
                 const [year, month] = this.monthFilter.split('-');
                 // Hack JS: day 0 di bulan berikutnya = hari terakhir bulan sebelumnya
                 const lastDay = new Date(year, month, 0).getDate(); 
                 
                 return {
                     start: `${this.monthFilter}-01`,
                     end: `${year}-${month}-${lastDay}`
                 };
             }
         },

         pushItemToQueue(value) {
             // Panggil fungsi helper range tanggal
             const range = this.getDateRange(); 

             this.downloadQueue.push({
                 id: Date.now() + Math.random(),
                 value: value, 
                 label: this.formatLabel(value),
                 
                 // Gunakan hasil perhitungan range
                 dateStart: range.start,
                 dateEnd: range.end,
                 periodeDisplay: this.formatDate(range.start) + ' - ' + this.formatDate(range.end),
                 
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
                 'shift_anggota': 'Laporan Shift Anggota'
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
    <h2 class="text-2xl font-bold text-slate-800 mb-4">Unduh Laporan Gabungan</h2>

    <form action="{{ route('komandan.laporan.download') }}" method="POST">
        @csrf
        <input type="hidden" name="download_queue" :value="JSON.stringify(downloadQueue)">

        {{-- FILTER SECTION --}}
        <div class="bg-white p-4 rounded-lg shadow-md mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                
                {{-- 1. PILIH JENIS --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">JENIS LAPORAN:</label>
                    <select x-model="reportType" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="harian">Laporan Harian</option>
                        <option value="bulanan">Laporan Bulanan</option>
                    </select>
                </div>

                {{-- 2. FILTER TANGGAL (LOGIKA IF/ELSE UI) --}}
                
                {{-- TAMPILAN JIKA HARIAN (Date Range) --}}
                <template x-if="reportType === 'harian'">
                    <div class="contents">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">DARI TANGGAL:</label>
                            <input type="date" x-model="dateFrom" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">SAMPAI TANGGAL:</label>
                            <input type="date" x-model="dateTo" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                </template>

                {{-- TAMPILAN JIKA BULANAN (Month Picker) --}}
                <template x-if="reportType === 'bulanan'">
                    <div class="sm:col-span-2"> {{-- Ambil 2 kolom agar lebar --}}
                        <label class="block text-sm font-medium text-gray-700 mb-1">PILIH BULAN:</label>
                        {{-- input type="month" hanya memilih Bulan & Tahun --}}
                        <input type="month" x-model="monthFilter" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </template>

            </div>
        </div>

        {{-- PREVIEW CHECKBOXES --}}
        <div class="bg-white p-4 rounded-lg shadow-md mb-6">
            <h3 class="font-bold text-gray-800 mb-3 border-b pb-2">PREVIEW (PILIH LAPORAN)</h3>
            
            {{-- Form Harian --}}
            <div x-show="reportType === 'harian'" class="space-y-3">
                <label class="flex items-center cursor-pointer hover:bg-slate-50 p-1 rounded">
                    <input type="checkbox" value="laporan_presensi" x-model="selectedChecks" class="rounded text-blue-600 w-5 h-5">
                    <span class="ml-2 text-gray-700">Laporan Presensi</span>
                </label>
                <label class="flex items-center cursor-pointer hover:bg-slate-50 p-1 rounded">
                    <input type="checkbox" value="laporan_patroli" x-model="selectedChecks" class="rounded text-blue-600 w-5 h-5">
                    <span class="ml-2 text-gray-700">Laporan Patroli</span>
                </label>
                
                {{-- FITUR PENGELOLAAN BARANG --}}
                <div class="border rounded-lg p-3 bg-slate-50">
                    <label class="flex items-center cursor-pointer mb-2">
                        <input type="checkbox" x-model="isBarangActive" @change="toggleBarang()" class="rounded text-blue-600 w-5 h-5">
                        <span class="ml-2 font-semibold text-gray-800">Laporan Pengelolaan Barang</span>
                    </label>
                    <div x-show="isBarangActive || barangChecks.length > 0" x-transition class="ml-7 space-y-2 border-l-2 border-blue-200 pl-3">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" value="barang_temu" x-model="barangChecks" @change="updateParent()" class="rounded text-blue-600 w-4 h-4">
                            <span class="ml-2 text-gray-600 text-sm">Barang Temuan</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" value="barang_titip" x-model="barangChecks" @change="updateParent()" class="rounded text-blue-600 w-4 h-4">
                            <span class="ml-2 text-gray-600 text-sm">Barang Titipan</span>
                        </label>
                    </div>
                </div>

                <label class="flex items-center cursor-pointer hover:bg-slate-50 p-1 rounded">
                    <input type="checkbox" value="kendaraan" x-model="selectedChecks" class="rounded text-blue-600 w-5 h-5">
                    <span class="ml-2 text-gray-700">Laporan Kendaraan</span>
                </label>
                <label class="flex items-center cursor-pointer hover:bg-slate-50 p-1 rounded">
                    <input type="checkbox" value="tamu" x-model="selectedChecks" class="rounded text-blue-600 w-5 h-5">
                    <span class="ml-2 text-gray-700">Laporan Tamu</span>
                </label>
            </div>

            {{-- Form Bulanan --}}
            <div x-show="reportType === 'bulanan'" class="space-y-3" style="display: none;">
                <label class="flex items-center cursor-pointer hover:bg-slate-50 p-1 rounded">
                    <input type="checkbox" value="gangguan_kamtibmas" x-model="selectedChecks" class="rounded text-blue-600 w-5 h-5">
                    <span class="ml-2 text-gray-700">Laporan Gangguan Kamtibmas</span>
                </label>
                <label class="flex items-center cursor-pointer hover:bg-slate-50 p-1 rounded">
                    <input type="checkbox" value="shift_anggota" x-model="selectedChecks" class="rounded text-blue-600 w-5 h-5">
                    <span class="ml-2 text-gray-700">Laporan Shift Anggota</span>
                </label>
            </div>

            <div class="flex justify-end mt-4 pt-2 border-t">
                <button type="button" @click="addToQueue()" 
                        class="bg-[#1e4275] text-white px-6 py-2 rounded-lg shadow font-semibold text-sm hover:bg-blue-900 transition transform active:scale-95">
                    Tambah
                </button>
            </div>
        </div>

        {{-- TABEL ANTRIAN (SAMA SEPERTI SEBELUMNYA) --}}
        <div class="bg-white p-4 rounded-lg shadow-md mb-6" x-show="downloadQueue.length > 0">
            <h3 class="font-bold text-[#1e4275] text-sm mb-3">Laporan yang diunduh</h3>
            <div class="overflow-hidden border border-gray-200 rounded-lg">
                <table class="w-full text-sm text-left">
                    <thead class="bg-[#1e4275] text-white">
                        <tr>
                            <th class="px-4 py-2 text-center w-10">No.</th>
                            <th class="px-4 py-2">Jenis Laporan</th>
                            <th class="px-4 py-2 text-center w-32">Download Satuan</th> 
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <template x-for="(item, index) in downloadQueue" :key="item.id">
                            <tr>
                                <td class="px-4 py-3 text-center text-gray-600" x-text="index + 1 + '.'"></td>
                                <td class="px-4 py-3 text-gray-800">
                                    <div class="font-bold text-slate-700" x-text="item.label"></div>
                                    <div class="text-xs text-gray-500" x-text="item.periodeDisplay"></div>
                                    <button type="button" @click="downloadQueue.splice(index, 1)" class="text-xs text-red-400 hover:text-red-600 underline mt-1">
                                        Hapus dari daftar
                                    </button>
                                </td>
                                <td class="px-4 py-3 text-center align-middle">
                                    <div class="flex justify-center gap-2">
                                        <button type="button"
                                           @click.prevent="downloadSingle(baseUrlSingle + '?type=' + item.value + '&format=excel&start=' + item.dateStart + '&end=' + item.dateEnd)"
                                           class="bg-green-100 text-green-700 p-2 rounded hover:bg-green-200 transition border border-green-200 cursor-pointer" title="Download Excel">
                                           <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2z"/><path d="M3 7l9 6 9-6"/><path d="M14.5 10h.5a2 2 0 0 1 0 4H12v-4h2.5"/></svg>
                                        </button>
                                        <button type="button"
                                           @click.prevent="downloadSingle(baseUrlSingle + '?type=' + item.value + '&format=pdf&start=' + item.dateStart + '&end=' + item.dateEnd)"
                                           class="bg-red-100 text-red-700 p-2 rounded hover:bg-red-200 transition border border-red-200 cursor-pointer" title="Download PDF">
                                           <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><path d="M10 13H8v5h2c1.2 0 2-.8 2-2v-1c0-1.2-.8-2-2-2z"/><line x1="12" y1="13" x2="12" y2="18"/><path d="M16 13h2c.6 0 1 .4 1 1v2c0 .6-.4 1-1 1h-2v-4z"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            <button type="submit" name="format" value="excel" :disabled="downloadQueue.length === 0" class="w-full bg-green-600 text-white font-bold py-3 px-4 rounded-lg shadow hover:bg-green-700 transition mb-4 disabled:opacity-50 disabled:cursor-not-allowed">
                UNDUH LAPORAN GABUNGAN (EXCEL)
            </button>
            <button type="submit" name="format" value="pdf" :disabled="downloadQueue.length === 0" class="w-full bg-red-600 text-white font-bold py-3 px-4 rounded-lg shadow hover:bg-red-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                UNDUH LAPORAN GABUNGAN (PDF)
            </button>
        </div>
    </form>
</div>
@endsection