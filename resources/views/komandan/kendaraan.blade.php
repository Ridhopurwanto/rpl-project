@extends('layouts.app')

@section('header-left')
    <a class="bg-slate-800 text-white text-sm font-semibold px-6 py-2 rounded-full shadow-md hover:bg-slate-700 transition">
        LAPORAN KENDARAAN
    </a>
@endsection

@section('content')
<div class="w-full mx-auto"
     x-data="{ 
        showEditModal: false, editAction: '', editPlat: '', editPemilik: '', editTipe: '',
        showDeleteModal: false, deleteAction: '',
        showPromoteModal: false, promoteAction: ''
     }">
    
    <h2 class="text-2xl font-bold text-slate-800 mb-4">Laporan Kendaraan</h2>

    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            {{ session('error') }}
        </div>
    @endif

    {{-- Form Filter & Search --}}
    <form id="filterForm" action="{{ route('komandan.kendaraan') }}" method="GET">
        <div class="bg-white p-4 rounded-lg shadow-md mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                
                {{-- Filter Tanggal --}}
                <div class="sm:col-span-1">
                    <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">TANGGAL:</label>
                    <input type="date" id="tanggal" name="tanggal" 
                           onchange="document.getElementById('filterForm').submit()"
                           class="w-full bg-[#2a4a6f] text-white px-4 py-2 rounded-lg shadow border-none focus:outline-none focus:ring-2 focus:ring-blue-400" 
                           style="color-scheme: dark;"
                           value="{{ $tanggalTerpilih }}">
                </div>

                {{-- Filter Tipe --}}
                <div class="sm:col-span-1">
                    <label for="tipe" class="block text-sm font-medium text-gray-700 mb-1">TIPE:</label>
                    <select id="tipe" name="tipe" 
                            onchange="document.getElementById('filterForm').submit()"
                            class="w-full bg-[#2a4a6f] text-white px-4 py-2 rounded-lg shadow border-none focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">Semua Tipe</option>
                        <option value="Roda 2" {{ $tipeTerpilih == 'Roda 2' ? 'selected' : '' }}>Roda 2</option>
                        <option value="Roda 4" {{ $tipeTerpilih == 'Roda 4' ? 'selected' : '' }}>Roda 4</option>
                    </select>
                </div>

                {{-- ▼▼▼ LIVE SEARCH INPUT (KHUSUS RIWAYAT) ▼▼▼ --}}
                <div class="sm:col-span-2">
                    <label for="searchInput" class="block text-sm font-medium text-gray-700 mb-1">CARI:</label>
                    <input type="text" id="searchInput" name="search" 
                           class="w-full bg-[#2a4a6f] text-white px-4 py-2 rounded-lg shadow border-none focus:outline-none focus:ring-2 focus:ring-blue-400 placeholder-gray-300" 
                           value="{{ $search ?? '' }}" 
                           placeholder="Ketik Nopol atau Nama...">
                </div>

            </div>
        </div>
    </form>

    {{-- ▼▼▼ ID Container Khusus Tabel Riwayat ▼▼▼ --}}
    <div id="riwayat-container" class="transition-opacity duration-200">
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="bg-gray-100 p-3 border-b border-gray-200">
                <h3 class="font-bold text-gray-800">RIWAYAT KELUAR/MASUK</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-max">
                    <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500">
                        <tr>
                            <th class="py-3 px-4 text-left w-16">No</th>
                            <th class="py-3 px-4 text-left w-32">Nopol</th>
                            <th class="py-3 px-4 text-left">Pemilik</th>
                            {{-- ▼▼▼ TAMBAHKAN KOLOM TIPE ▼▼▼ --}}
                            <th class="py-3 px-4 text-left w-24">Tipe</th>
                            {{-- ▲▲▲ --}}
                            <th class="py-3 px-4 text-left w-28">Masuk</th>
                            <th class="py-3 px-4 text-left w-28">Keluar</th>
                            <th class="py-3 px-4 text-left w-40">Ket.</th>
                            @if(Auth::user()->peran == 'komandan')
                                <th class="py-3 px-4 text-center w-28">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-200">
                        @forelse($riwayat as $index => $log)
                        <tr>
                            <td class="py-2 px-4">{{ $index + 1 }}.</td>
                            <td class="py-2 px-4 font-medium">{{ $log->nopol ?? 'N/A' }}</td>
                            <td class="py-2 px-4">{{ $log->pemilik ?? 'N/A' }}</td>
                            
                            {{-- ▼▼▼ TAMPILKAN DATA TIPE ▼▼▼ --}}
                            <td class="py-2 px-4">
                                <span class="text-xs font-semibold px-2 py-1 rounded-full 
                                    {{ $log->tipe == 'Roda 4' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                    {{ $log->tipe ?? '-' }}
                                </span>
                            </td>
                            {{-- ▲▲▲ --}}
                            
                            <td class="py-2 px-4 text-gray-700">
                                @if($log->waktu_masuk && $log->waktu_masuk->format('Y-m-d') == $tanggalTerpilih)
                                    {{ $log->waktu_masuk->format('H:i:s') }}
                                @else <span class="text-gray-400">-</span> @endif
                            </td>
                            <td class="py-2 px-4 text-gray-700">
                                @if($log->waktu_keluar && $log->waktu_keluar->format('Y-m-d') == $tanggalTerpilih)
                                    {{ $log->waktu_keluar->format('H:i:s') }}
                                @else <span class="text-gray-400">-</span> @endif
                            </td>
                            <td class="py-2 px-4">
                                @if(Auth::user()->peran == 'komandan')
                                    <form action="{{ route('komandan.kendaraan.log.updateKeterangan', $log->id_log) }}" method="POST">
                                        @csrf @method('PUT')
                                        <select name="keterangan" onchange="this.form.submit()" class="border-gray-300 rounded-lg shadow-sm text-xs py-1 focus:border-blue-500 focus:ring-blue-500">
                                            <option value="tidak menginap" {{ $log->keterangan == 'tidak menginap' ? 'selected' : '' }}>Tidak Menginap</option>
                                            <option value="menginap" {{ $log->keterangan == 'Menginap' ? 'selected' : '' }}>Menginap</option>
                                        </select>
                                    </form>
                                @else {{ $log->keterangan }} @endif
                            </td>
                            @if(Auth::user()->peran == 'komandan')
                                <td class="py-2 px-4 text-center">
                                    @if($log->kendaraan)
                                        <span class="text-green-500" title="Terdaftar"><svg class="w-6 h-6 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg></span>
                                    @else
                                        <button @click.prevent="showPromoteModal = true; promoteAction = '{{ route('komandan.kendaraan.log.promote', $log->id_log) }}'" class="text-blue-500 hover:text-blue-700"><svg class="w-6 h-6 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"></path></svg></button>
                                    @endif
                                </td>
                            @endif
                        </tr>
                        @empty
                        <tr><td colspan="8" class="py-4 px-4 text-center text-gray-500">Data tidak ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ▲▲▲ End Riwayat Container ▲▲▲ --}}

    {{-- Tabel 2: KENDARAAN MASTER (Statik / Tidak ikut search ini) --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="bg-gray-100 p-3 border-b border-gray-200">
            <h3 class="font-bold text-gray-800">KENDARAAN YANG TERDAFTAR</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-max">
                <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500">
                    <tr>
                        <th class="py-3 px-4 text-left w-16">No</th>
                        <th class="py-3 px-4 text-left w-40">Nopol</th>
                        <th class="py-3 px-4 text-left">Pemilik</th>
                        <th class="py-3 px-4 text-left w-32">Tipe</th>
                        @if(Auth::user()->peran == 'komandan')
                            <th class="py-3 px-4 text-center w-28">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse($kendaraanMaster as $index => $kendaraan)
                    <tr>
                        <td class="py-2 px-4">{{ $index + 1 }}.</td>
                        <td class="py-2 px-4 font-medium">{{ $kendaraan->nomor_plat }}</td>
                        <td class="py-2 px-4">{{ $kendaraan->pemilik }}</td>
                        <td class="py-2 px-4">{{ $kendaraan->tipe }}</td>
                        @if(Auth::user()->peran == 'komandan')
                            <td class="py-2 px-4 text-center">
                                <div class="flex justify-center space-x-3">
                                    <button @click="showEditModal = true; editAction = '{{ route('komandan.kendaraan.master.update', $kendaraan->id_kendaraan) }}'; editPlat = '{{ $kendaraan->nomor_plat }}'; editPemilik = '{{ $kendaraan->pemilik }}'; editTipe = '{{ $kendaraan->tipe }}';" class="text-blue-500 hover:text-blue-700">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828zM5 12V7a2 2 0 012-2h2.586l-4 4H5zM3 15a2 2 0 00-2 2v2h16v-2a2 2 0 00-2-2H3z"></path></svg>
                                    </button>
                                    <button @click.prevent="showDeleteModal = true; deleteAction = '{{ route('komandan.kendaraan.master.destroy', $kendaraan->id_kendaraan) }}'" class="text-red-500 hover:text-red-700">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                    </button>
                                </div>
                            </td>
                        @endif
                    </tr>
                    @empty
                    <tr><td colspan="5" class="py-4 px-4 text-center text-gray-500">Tidak ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL EDIT, HAPUS, PROMOTE (Paste kode modal kamu yang sebelumnya di sini) --}}
    {{-- ... (Kode Modal Disimpan disini) ... --}}

    {{-- Modal Edit Kendaraan --}}
    <div x-show="showEditModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
        @click.away="showEditModal = false"
        style="display: none;">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-4 relative" @click.stop>
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-xl font-bold text-gray-800">EDIT DATA KENDARAAN</h3>
            <button @click="showEditModal = false" class="text-gray-500 hover:text-gray-800 text-3xl">&times;</button>
        </div>
        <form :action="editAction" method="POST" class="mt-4">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label for="edit_nomor_plat" class="block text-sm font-medium text-gray-700 mb-1">Nomor Plat:</label>
                    <input type="text" id="edit_nomor_plat" name="nomor_plat" x-model="editPlat" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label for="edit_pemilik" class="block text-sm font-medium text-gray-700 mb-1">Pemilik:</label>
                    <input type="text" id="edit_pemilik" name="pemilik" x-model="editPemilik" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label for="edit_tipe" class="block text-sm font-medium text-gray-700 mb-1">Tipe:</label>
                    <select id="edit_tipe" name="tipe" x-model="editTipe" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="Roda 2">Roda 2</option>
                        <option value="Roda 4">Roda 4</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-green-500 text-white font-bold py-2 px-4 rounded-lg shadow hover:bg-green-600 transition">SUBMIT</button>
            </div>
        </form>
    </div>
    </div>

    {{-- Modal Hapus --}}
    <div x-show="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4" @click.away="showDeleteModal = false" style="display: none;">
    <div class="bg-white rounded-lg shadow-xl max-w-sm w-full p-6 relative" @click.stop>
        <h3 class="text-lg font-bold text-gray-900 mb-4">Konfirmasi Hapus</h3>
        <p class="text-gray-600 mb-6">Apakah Anda yakin ingin menghapus data kendaraan ini? Tindakan ini tidak dapat dibatalkan.</p>
        <form :action="deleteAction" method="POST" class="flex justify-end space-x-4">
            @csrf
            @method('DELETE')
            <button type="button" @click="showDeleteModal = false" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300">Batal</button>
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">Ya, Hapus</button>
        </form>
    </div>
    </div>

    {{-- Modal Promote --}}
    <div x-show="showPromoteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4" @click.away="showPromoteModal = false" style="display: none;">
    <div class="bg-white rounded-lg shadow-xl max-w-sm w-full p-6 relative" @click.stop>
        <h3 class="text-lg font-bold text-gray-900 mb-4">Konfirmasi Pendaftaran</h3>
        <p class="text-gray-600 mb-6">Tambahkan kendaraan ini ke Daftar Master?</p>
        <form :action="promoteAction" method="POST" class="flex justify-end space-x-4">
            @csrf
            <button type="button" @click="showPromoteModal = false" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300">Batal</button>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">OK</button>
        </form>
    </div>
    </div>

</div>

{{-- SCRIPT AJAX LIVE SEARCH --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const riwayatContainer = document.getElementById('riwayat-container');
        const form = document.getElementById('filterForm');
        let timeout = null;

        if(searchInput && riwayatContainer && form) {
            searchInput.addEventListener('input', function() {
                // Hapus timeout lama (debounce)
                clearTimeout(timeout);
                
                // Efek loading
                riwayatContainer.style.opacity = '0.5';

                // Tunggu 500ms setelah selesai ketik
                timeout = setTimeout(() => {
                    fetchData();
                }, 500);
            });

            function fetchData() {
                const formData = new FormData(form);
                const searchParams = new URLSearchParams(formData);

                fetch(`${form.action}?${searchParams.toString()}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    // Hanya ganti isi dari #riwayat-container
                    const newContent = doc.getElementById('riwayat-container');

                    if (newContent) {
                        riwayatContainer.innerHTML = newContent.innerHTML;
                    }
                    
                    riwayatContainer.style.opacity = '1';
                    
                    // Update URL agar kalau di-refresh filternya tetap ada
                    window.history.pushState(null, '', `?${searchParams.toString()}`);
                })
                .catch(error => {
                    console.error('Error:', error);
                    riwayatContainer.style.opacity = '1';
                });
            }
        }
    });
</script>
@endsection