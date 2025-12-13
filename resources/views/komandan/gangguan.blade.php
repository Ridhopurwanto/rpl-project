@extends('layouts.app')

{{-- Terapkan layout full-width --}}
@section('mobile_width', 'max-w-full')
@section('desktop_width', 'lg:max-w-full')

@section('header-left')
    <a class="flex items-center border-2 border-[#1a2847] text-[#1a2847] text-sm font-bold px-4 py-2 rounded-full">
        GANGGUAN<br class="sm:hidden"> KAMTIBMAS
    </a>
@endsection

@section('content')
    <div class="w-full mx-auto" x-data="{ 
            showPhotoModal: false, 
            photoUrl: '', 
            showEditModal: false, 
            editAction: '',
            editWaktu: '',
            editLokasi: '',
            editKategori: '',
            editDeskripsi: '',
            maxDate: '{{ now()->format('Y-m-d\T23:59') }}',
            showDeleteModal: false,
            deleteAction: '' 
         }">

        <h2 class="text-2xl font-bold text-slate-800 mb-4">Laporan Gangguan Kamtibmas</h2>

        {{-- Tampilkan Notifikasi Sukses/Error (Floating Toast) --}}
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-full"
                x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-full"
                class="fixed top-4 right-4 z-50 max-w-sm w-full bg-white rounded-lg shadow-lg border-l-4 border-green-500 p-4 flex items-start space-x-3">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex-1 pt-0.5">
                    <p class="text-sm font-medium text-gray-900">{{ session('success') }}</p>
                </div>
                <button @click="show = false" class="flex-shrink-0 ml-4 text-gray-400 hover:text-gray-500 focus:outline-none">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-full"
                x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-full"
                class="fixed top-4 right-4 z-50 max-w-sm w-full bg-white rounded-lg shadow-lg border-l-4 border-red-500 p-4 flex items-start space-x-3">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex-1 pt-0.5">
                    <p class="text-sm font-medium text-gray-900">{{ session('error') }}</p>
                </div>
                <button @click="show = false" class="flex-shrink-0 ml-4 text-gray-400 hover:text-gray-500 focus:outline-none">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        @endif

        @if ($errors->any())
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-full"
                x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-full"
                class="fixed top-4 right-4 z-50 max-w-sm w-full bg-white rounded-lg shadow-lg border-l-4 border-red-500 p-4 flex items-start space-x-3">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="flex-1 pt-0.5">
                    <p class="text-sm font-bold text-gray-900 mb-1">Oops! Terjadi kesalahan:</p>
                    <ul class="text-sm text-gray-600 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button @click="show = false" class="flex-shrink-0 ml-4 text-gray-400 hover:text-gray-500 focus:outline-none">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        @endif

        {{-- Tabel Riwayat Gangguan --}}
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] p-3 border-b border-[#2a4a6f]">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <h3 class="font-bold text-white">RIWAYAT GANGGUAN</h3>
                </div>
            </div>

            {{-- Form Filter --}}
            <form id="filterForm" action="{{ route('komandan.gangguan') }}" method="GET"
                class="p-4 border-b border-gray-200" x-data="{}">
                <div class="flex flex-wrap gap-4">
                    {{-- Show Entries --}}
                    <div class="w-[calc(50%-0.5rem)] md:w-auto">
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Show</label>
                        <div class="flex items-center gap-2">
                            <select name="per_page" onchange="this.form.submit()"
                                class="h-[42px] pl-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm appearance-none cursor-pointer"
                                style="background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27currentColor%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3e%3cpolyline points=%276 9 12 15 18 9%27%3e%3c/polyline%3e%3c/svg%3e'); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 1.25em 1.25em;">
                                <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5</option>
                                <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                            </select>
                            <span class="text-sm text-gray-600">rows</span>
                        </div>
                    </div>

                    {{-- Filter Bulan --}}
                    {{-- Filter Bulan (Custom Picker) --}}
                    <div class="w-[calc(50%-0.5rem)] md:flex-1" x-data="{
                        showPicker: false,
                        month: parseInt('{{ \Carbon\Carbon::parse($bulanTerpilih)->format('m') }}'),
                        year: parseInt('{{ \Carbon\Carbon::parse($bulanTerpilih)->format('Y') }}'),
                        months: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                        shortMonths: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'],

                        get displayValue() {
                            return this.months[this.month - 1] + ' ' + this.year;
                        },

                        selectMonth(m) {
                            this.month = m;
                            this.submitForm();
                        },

                        changeYear(delta) {
                            this.year += delta;
                        },

                        submitForm() {
                            let m = this.month.toString().padStart(2, '0');
                            this.$refs.hiddenBulan.value = this.year + '-' + m;
                            document.getElementById('filterForm').submit();
                        }
                    }" @click.away="showPicker = false">
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Bulan</label>
                        <div class="relative">
                            {{-- Hidden Input --}}
                            <input type="hidden" name="bulan" x-ref="hiddenBulan" value="{{ $bulanTerpilih }}">

                            {{-- Trigger Button (Looks like Input) --}}
                            <div @click="showPicker = !showPicker"
                                class="block w-full h-[42px] px-4 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg hover:border-[#1e3a5f] cursor-pointer flex items-center justify-between">
                                <span x-text="displayValue"></span>
                                <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>

                            {{-- Dropdown Picker --}}
                            <div x-show="showPicker" style="display: none;"
                                class="absolute z-50 top-full left-0 mt-1 w-64 bg-white rounded-lg shadow-xl border border-gray-200 p-4">

                                {{-- Year Navigator --}}
                                <div class="flex justify-between items-center mb-4">
                                    <button type="button" @click.stop="changeYear(-1)"
                                        class="p-1 hover:bg-gray-100 rounded-full">
                                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </button>
                                    <span class="font-bold text-gray-800" x-text="year"></span>
                                    <button type="button" @click.stop="changeYear(1)"
                                        class="p-1 hover:bg-gray-100 rounded-full">
                                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                </div>

                                {{-- Months Grid --}}
                                <div class="grid grid-cols-4 gap-2">
                                    <template x-for="(mName, index) in shortMonths">
                                        <button type="button" @click.stop="selectMonth(index + 1)"
                                            :class="{'bg-[#1e3a5f] text-white': month === (index + 1), 'hover:bg-blue-50 text-gray-700': month !== (index + 1)}"
                                            class="text-xs font-medium py-2 rounded-md transition-colors" x-text="mName">
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Filter Kategori --}}
                    <div class="w-[calc(50%-0.5rem)] md:flex-1">
                        <label for="kategori"
                            class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Kategori</label>
                        <div class="relative">
                            <select id="kategori" name="kategori" onchange="this.form.submit()"
                                class="block w-full h-[42px] px-4 pr-10 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3a5f] focus:border-[#1e3a5f] shadow-sm cursor-pointer appearance-none">
                                <option value="semua">Semua Kategori</option>
                                @foreach($kategoriOptions as $kategori)
                                    <option value="{{ $kategori }}" {{ $kategoriTerpilih == $kategori ? 'selected' : '' }}>
                                        {{ $kategori }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            {{-- TABEL (Desktop) --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full min-w-max">
                    <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500">
                        <tr>
                            <th class="py-3 px-4 text-center">No</th>
                            <th class="py-3 px-4 text-center">Tanggal</th>
                            <th class="py-3 px-4 text-center">Lokasi</th>
                            <th class="py-3 px-4 text-center">Kategori</th>
                            <th class="py-3 px-4 text-center">Deskripsi</th>
                            <th class="py-3 px-4 text-center">Foto</th>
                            @if(Auth::user()->peran == 'komandan')
                                <th class="py-3 px-4 text-center">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-200">
                        @forelse($riwayatGangguan as $index => $gangguan)
                            <tr>
                                <td class="py-2 px-4 text-center">{{ $index + 1 }}.</td>
                                <td class="py-2 px-4 text-center">{{ $gangguan->waktu_lapor->format('d/m/Y H:i') }}</td>
                                <td class="py-2 px-4 text-center">{{ $gangguan->lokasi }}</td>
                                <td class="py-2 px-4 text-center">{{ $gangguan->kategori }}</td>
                                <td class="py-2 px-4 text-center">{{ $gangguan->deskripsi }}</td>
                                <td class="py-2 px-4 text-center">
                                    @if($gangguan->foto)
                                        <button
                                            @click="showPhotoModal = true; photoUrl = '{{ asset('storage/' . $gangguan->foto) }}'"
                                            class="text-blue-500 hover:underline">Buka</button>
                                    @else
                                        -
                                    @endif
                                </td>
                                @if(Auth::user()->peran == 'komandan')
                                    <td class="py-2 px-4">
                                        <div class="flex justify-center space-x-3">
                                            <button @click="
                                                showEditModal = true; 
                                                editAction = '{{ route('komandan.gangguan.update', $gangguan->id_gangguan) }}';
                                                editWaktu = '{{ $gangguan->waktu_lapor->format('Y-m-d\TH:i') }}';
                                                editLokasi = '{{ $gangguan->lokasi }}';
                                                editKategori = '{{ $gangguan->kategori }}';
                                                editDeskripsi = '{{ $gangguan->deskripsi }}';
                                            " class="text-blue-500 hover:text-blue-700" title="Edit">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828zM5 12V7a2 2 0 012-2h2.586l-4 4H5zM3 15a2 2 0 00-2 2v2h16v-2a2 2 0 00-2-2H3z">
                                                    </path>
                                                </svg>
                                            </button>
                                            <button @click.prevent="
                                                showDeleteModal = true; 
                                                deleteAction = '{{ route('komandan.gangguan.destroy', $gangguan->id_gangguan) }}'
                                            " class="text-red-500 hover:text-red-700" title="Hapus">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ Auth::user()->peran == 'komandan' ? '7' : '6' }}"
                                    class="py-4 px-4 text-center text-gray-500">
                                    Tidak ada data gangguan kamtibmas pada bulan ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- CARD LAYOUT (Mobile) --}}
            <div class="md:hidden space-y-2 p-3">
                @forelse($riwayatGangguan as $gangguan)
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
                        <div class="flex gap-3 p-3">
                            {{-- Foto di Sebelah Kiri --}}
                            @if($gangguan->foto)
                                <div class="flex-shrink-0">
                                    <button
                                        @click="showPhotoModal = true; photoUrl = '{{ asset('storage/' . $gangguan->foto) }}'"
                                        class="block w-20 h-20 rounded-lg overflow-hidden border-2 border-gray-200 hover:border-blue-500 transition">
                                        <img src="{{ asset('storage/' . $gangguan->foto) }}" 
                                             alt="Foto" 
                                             class="w-full h-full object-cover">
                                    </button>
                                </div>
                            @endif

                            {{-- Info di Tengah --}}
                            <div class="flex-1 min-w-0">
                                {{-- Lokasi & Kategori Badge --}}
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex-1 min-w-0 pr-2">
                                        <h4 class="font-bold text-gray-800 text-sm">{{ $gangguan->lokasi }}</h4>
                                        <p class="text-gray-600 text-xs">{{ $gangguan->waktu_lapor->format('d/m/Y H:i') }}</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="inline-block text-[10px] font-bold px-2 py-1 rounded-full bg-red-500 text-white text-center leading-tight max-w-[80px] break-words">
                                            {{ $gangguan->kategori }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Deskripsi --}}
                                <div class="mb-2">
                                    <p class="text-gray-700 text-xs">{{ $gangguan->deskripsi }}</p>
                                </div>

                                {{-- Tombol Aksi --}}
                                @if(Auth::user()->peran == 'komandan')
                                    <div class="flex gap-1.5">
                                        <button @click="
                                                showEditModal = true; 
                                                editAction = '{{ route('komandan.gangguan.update', $gangguan->id_gangguan) }}';
                                                editWaktu = '{{ $gangguan->waktu_lapor->format('Y-m-d\TH:i') }}';
                                                editLokasi = '{{ $gangguan->lokasi }}';
                                                editKategori = '{{ $gangguan->kategori }}';
                                                editDeskripsi = '{{ $gangguan->deskripsi }}';
                                            "
                                            class="flex-1 bg-blue-500 text-white font-bold py-1.5 rounded text-xs hover:bg-blue-600 transition flex items-center justify-center gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828zM5 12V7a2 2 0 012-2h2.586l-4 4H5zM3 15a2 2 0 00-2 2v2h16v-2a2 2 0 00-2-2H3z"></path>
                                            </svg>
                                            Edit
                                        </button>
                                        <button
                                            @click.prevent="showDeleteModal = true; deleteAction = '{{ route('komandan.gangguan.destroy', $gangguan->id_gangguan) }}'"
                                            class="flex-1 bg-red-500 text-white font-bold py-1.5 rounded text-xs hover:bg-red-600 transition flex items-center justify-center gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            Hapus
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-500 text-sm font-semibold">Tidak ada data gangguan kamtibmas pada bulan ini.</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination Desktop --}}
            @if($riwayatGangguan->total() > 0)
                <div class="hidden md:flex justify-between items-center px-6 py-4 border-t border-gray-200">
                    <div class="text-sm text-gray-600">Showing {{ $riwayatGangguan->firstItem() ?? 0 }} to
                        {{ $riwayatGangguan->lastItem() ?? 0 }} of {{ $riwayatGangguan->total() }} entries</div>
                    <div class="flex gap-1">
                        @if($riwayatGangguan->onFirstPage())
                            <span
                                class="px-3 py-2 text-sm text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">Previous</span>
                        @else
                            <a href="{{ $riwayatGangguan->appends(request()->query())->previousPageUrl() }}"
                                class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Previous</a>
                        @endif
                        @foreach($riwayatGangguan->getUrlRange(1, $riwayatGangguan->lastPage()) as $page => $url)
                            @if($page == $riwayatGangguan->currentPage())
                                <span class="px-3 py-2 text-sm text-white bg-[#1e3a5f] rounded-lg">{{ $page }}</span>
                            @else
                                <a href="{{ $riwayatGangguan->appends(request()->query())->url($page) }}"
                                    class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">{{ $page }}</a>
                            @endif
                        @endforeach
                        @if($riwayatGangguan->hasMorePages())
                            <a href="{{ $riwayatGangguan->appends(request()->query())->nextPageUrl() }}"
                                class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Next</a>
                        @else
                            <span class="px-3 py-2 text-sm text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">Next</span>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Pagination Mobile --}}
            @if($riwayatGangguan->total() > 0)
                <div class="md:hidden flex justify-between items-center px-3 py-4 border-t border-gray-200">
                    <div class="text-xs text-gray-600">
                        {{ $riwayatGangguan->firstItem() ?? 0 }}-{{ $riwayatGangguan->lastItem() ?? 0 }} of
                        {{ $riwayatGangguan->total() }}</div>
                    <div class="flex gap-1">
                        @if($riwayatGangguan->onFirstPage())
                            <span class="px-2 py-1 text-xs text-gray-400 bg-gray-100 rounded cursor-not-allowed">Prev</span>
                        @else
                            <a href="{{ $riwayatGangguan->appends(request()->query())->previousPageUrl() }}"
                                class="px-2 py-1 text-xs text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Prev</a>
                        @endif
                        @foreach($riwayatGangguan->getUrlRange(1, $riwayatGangguan->lastPage()) as $page => $url)
                            @if($page == $riwayatGangguan->currentPage())
                                <span class="px-2 py-1 text-xs text-white bg-[#1e3a5f] rounded">{{ $page }}</span>
                            @else
                                <a href="{{ $riwayatGangguan->appends(request()->query())->url($page) }}"
                                    class="px-2 py-1 text-xs text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">{{ $page }}</a>
                            @endif
                        @endforeach
                        @if($riwayatGangguan->hasMorePages())
                            <a href="{{ $riwayatGangguan->appends(request()->query())->nextPageUrl() }}"
                                class="px-2 py-1 text-xs text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Next</a>
                        @else
                            <span class="px-2 py-1 text-xs text-gray-400 bg-gray-100 rounded cursor-not-allowed">Next</span>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- Modal Tampil Foto (Zoom) --}}
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
                        FOTO GANGGUAN
                    </h3>
                    <button @click="showPhotoModal = false" class="text-white/70 hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>
                <div class="p-4">
                    <img :src="photoUrl" alt="Foto Gangguan" class="w-full h-auto rounded">
                </div>
            </div>
        </div>

        {{-- Modal Edit Gangguan --}}
        <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
            @click.away="showEditModal = false" style="display: none;">
            <div class="bg-white rounded-xl shadow-xl max-w-lg w-full relative overflow-hidden" @click.stop>
                {{-- Header Biru --}}
                <div class="bg-[#1e3a5f] py-4 px-6 border-b border-[#1e3a5f] flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white flex items-center tracking-wide">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                            </path>
                        </svg>
                        EDIT GANGGUAN KAMTIBMAS
                    </h3>
                    <button @click="showEditModal = false" class="text-white/70 hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>

                <form :action="editAction" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body max-h-[70vh] overflow-y-auto p-6">
                        <div class="space-y-5">

                            {{-- GROUP: Informasi Laporan --}}
                            <div class="p-4 bg-blue-50/50 rounded-xl border border-blue-100">

                                <div class="space-y-4">
                                    {{-- Waktu Lapor --}}
                                    <div>
                                        <label for="waktu_lapor"
                                            class="block text-xs font-bold text-[#1e3a5f] uppercase tracking-wide mb-1">Waktu
                                            Lapor <span class="text-red-500">*</span></label>
                                        <div class="relative">
                                            <div
                                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <svg class="h-5 w-5 text-[#1e3a5f]" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                            <input type="datetime-local" id="waktu_lapor" name="waktu_lapor"
                                                x-model="editWaktu" required :max="maxDate"
                                                class="pl-10 w-full bg-white border border-gray-300 text-gray-800 text-sm font-medium rounded-lg shadow-sm focus:ring-[#1e3a5f] focus:border-[#1e3a5f] block p-2.5">
                                        </div>
                                    </div>

                                    {{-- Lokasi --}}
                                    <div>
                                        <label for="lokasi"
                                            class="block text-xs font-bold text-[#1e3a5f] uppercase tracking-wide mb-1">Lokasi
                                            <span class="text-red-500">*</span></label>
                                        <div class="relative">
                                            <div
                                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <svg class="h-5 w-5 text-[#1e3a5f]" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                                    </path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                            </div>
                                            <input type="text" id="lokasi" name="lokasi" x-model="editLokasi" required
                                                placeholder="Contoh: Jl. Sudirman"
                                                class="pl-10 w-full bg-white border border-gray-300 text-gray-800 text-sm font-medium rounded-lg shadow-sm focus:ring-[#1e3a5f] focus:border-[#1e3a5f] block p-2.5">
                                        </div>
                                    </div>

                                    {{-- Kategori --}}
                                    <div>
                                        <label for="kategori_edit"
                                            class="block text-xs font-bold text-[#1e3a5f] uppercase tracking-wide mb-1">Kategori
                                            <span class="text-red-500">*</span></label>
                                        <div class="relative">
                                            <div
                                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <svg class="h-5 w-5 text-[#1e3a5f]" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                                    </path>
                                                </svg>
                                            </div>
                                            <select id="kategori_edit" name="kategori" x-model="editKategori" required
                                                class="pl-10 w-full bg-white border border-gray-300 text-[#1e3a5f] text-sm font-bold rounded-lg shadow-sm focus:ring-[#1e3a5f] focus:border-[#1e3a5f] block p-2.5 cursor-pointer">
                                                @foreach($kategoriOptions as $kategori)
                                                    <option value="{{ $kategori }}">{{ $kategori }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Deskripsi --}}
                                    <div>
                                        <label for="deskripsi"
                                            class="block text-xs font-bold text-[#1e3a5f] uppercase tracking-wide mb-1">Deskripsi
                                            <span class="text-red-500">*</span></label>
                                        <div class="relative">
                                            <div class="absolute top-3 left-0 pl-3 flex items-start pointer-events-none">
                                                <svg class="h-5 w-5 text-[#1e3a5f]" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 6h16M4 12h16M4 18h7"></path>
                                                </svg>
                                            </div>
                                            <textarea id="deskripsi" name="deskripsi" x-model="editDeskripsi" rows="3"
                                                required placeholder="Keterangan singkat..."
                                                class="pl-10 w-full bg-white border border-gray-300 text-gray-800 text-sm font-medium rounded-lg shadow-sm focus:ring-[#1e3a5f] focus:border-[#1e3a5f] block p-2.5"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer p-4 border-t bg-gray-50">
                        <button type="submit"
                            class="w-full px-4 py-3 text-white font-bold bg-[#1e3a5f] rounded-xl hover:bg-[#2a4a6f] shadow-lg transition transform hover:-translate-y-0.5">
                            SIMPAN PERUBAHAN
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Hapus --}}
        <div x-show="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
            @click.away="showDeleteModal = false" style="display: none;">
            <div class="bg-white rounded-lg shadow-xl max-w-sm w-full p-6 relative" @click.stop>
                <h3 class="text-lg font-bold text-gray-900 mb-4">Konfirmasi Hapus</h3>
                <p class="text-gray-600 mb-6">
                    Apakah Anda yakin ingin menghapus laporan gangguan ini? Tindakan ini tidak dapat dibatalkan.
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

    </div>
@endsection