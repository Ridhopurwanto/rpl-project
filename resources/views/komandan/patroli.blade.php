@extends('layouts.app')

@section('header-left')
    <a class="bg-slate-800 text-white text-sm font-semibold px-6 py-2 rounded-full shadow-md hover:bg-slate-700 transition">
        PATROLI
    </a>
@endsection

@section('content')
<div class="w-full mx-auto" 
     x-data="{ 
         showPhotoModal: false, 
         photoUrl: '', 
         showEditModal: false, 
         editAction: '', 
         editWilayah: '',
         showDeleteModal: false,
         deleteAction: '' 
     }">
    
    <h2 class="text-2xl font-bold text-slate-800 mb-4">Laporan Patroli Anggota</h2>

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

    {{-- Form Filter --}}
    <form action="{{ route('komandan.patroli') }}" method="GET">
        <div class="bg-white p-4 rounded-lg shadow-md mb-6">
            <div class="flex flex-col sm:flex-row sm:items-end sm:space-x-4 space-y-4 sm:space-y-0">
                
                {{-- Filter Tanggal --}}
                <div class="flex-1">
                    <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">TANGGAL:</label>
                    <input type="date" id="tanggal" name="tanggal" 
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                           value="{{ $tanggalTerpilih }}">
                </div>

                {{-- Filter Jenis Patroli --}}
                <div class="flex-1">
                    <label for="jenis_patroli" class="block text-sm font-medium text-gray-700 mb-1">JENIS PATROLI:</label>
                    <select id="jenis_patroli" name="jenis_patroli" 
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        
                        {{-- Opsi "Semua" DIHAPUS sesuai permintaan --}}
                        
                        @forelse($jenisPatroliOptions as $opsi)
                            <option value="{{ $opsi }}" {{ $jenisPatroliTerpilih == $opsi ? 'selected' : '' }}>
                                {{-- Menampilkan nama patroli apa adanya dari DB --}}
                                {{ $opsi }}
                            </option>
                        @empty
                            <option value="" disabled selected>Tidak ada data jenis patroli</option>
                        @endforelse
                        
                    </select>
                </div>

                <button type="submit" class="w-full sm:w-auto bg-blue-600 text-white px-5 py-2 rounded-lg shadow hover:bg-blue-700 transition">
                    Tampilkan
                </button>
            </div>
        </div>
    </form>

    {{-- Tabel Daftar Patroli --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="bg-gray-100 p-3 border-b border-gray-200">
            <h3 class="font-bold text-gray-800">DAFTAR PATROLI</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full min-w-max">
                <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500">
                    <tr>
                        <th class="py-3 px-4 text-left w-16">No</th>
                        <th class="py-3 px-4 text-left w-32">Waktu</th>
                        <th class="py-3 px-4 text-left w-32">Jenis</th>
                        <th class="py-3 px-4 text-left">Wilayah</th>
                        <th class="py-3 px-4 text-left w-48">Nama</th>
                        <th class="py-3 px-4 text-center w-24">Detail</th>
                        @if(Auth::user()->peran == 'komandan')
                            <th class="py-3 px-4 text-center w-28">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse($dataPatroli as $index => $item)
                    <tr>
                        <td class="py-2 px-4">{{ $index + 1 }}.</td>
                        <td class="py-2 px-4">{{ $item->waktu_exact->format('H:i:s') }}</td>
                        <td class="py-2 px-4">
                            <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                                {{ $item->jenis_patroli }}
                            </span>
                        </td>
                        <td class="py-2 px-4 font-medium">{{ $item->wilayah }}</td>
                        <td class="py-2 px-4">{{ $item->nama_lengkap }}</td>
                        <td class="py-2 px-4 text-center">
                            <button @click="showPhotoModal = true; photoUrl = '{{ asset('storage/' . $item->foto) }}'" class="text-blue-500 hover:underline">
                                Buka
                            </button>
                        </td>
                        @if(Auth::user()->peran == 'komandan')
                            <td class="py-2 px-4">
                                <div class="flex justify-center space-x-3">
                                    <button @click="showEditModal = true; editAction = '{{ route('komandan.patroli.update', $item->id_patroli) }}'; editWilayah = '{{ $item->wilayah }}'" class="text-blue-500 hover:text-blue-700" title="Edit">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828zM5 12V7a2 2 0 012-2h2.586l-4 4H5zM3 15a2 2 0 00-2 2v2h16v-2a2 2 0 00-2-2H3z"></path></svg>
                                    </button>
                                    <button @click.prevent="showDeleteModal = true; deleteAction = '{{ route('komandan.patroli.destroy', $item->id_patroli) }}'" class="text-red-500 hover:text-red-700" title="Hapus">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                    </button>
                                </div>
                            </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ Auth::user()->peran == 'komandan' ? '6' : '5' }}" class="py-4 px-4 text-center text-gray-500">
                            Tidak ada data patroli pada tanggal ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Foto --}}
    <div x-show="showPhotoModal" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
         @click.away="showPhotoModal = false"
         style="display: none;">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full p-4 relative" @click.stop>
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 class="text-xl font-bold text-gray-800">PHOTO</h3>
                <button @click="showPhotoModal = false" class="text-gray-500 hover:text-gray-800 text-3xl">&times;</button>
            </div>
            <div class="mt-4">
                <img :src="photoUrl" alt="Foto Patroli" class="w-full h-auto rounded">
            </div>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div x-show="showEditModal"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
         @click.away="showEditModal = false"
         style="display: none;">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-4 relative" @click.stop>
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 class="text-xl font-bold text-gray-800">EDIT PATROLI</h3>
                <button @click="showEditModal = false" class="text-gray-500 hover:text-gray-800 text-3xl">&times;</button>
            </div>
            <form :action="editAction" method="POST" class="mt-4">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div class="w-full h-40 bg-gray-200 rounded-lg flex items-center justify-center text-gray-400">
                        <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4V5h12v10zm-9.414-2.586a2 2 0 112.828 2.828L8.414 13H12v-1H6.586l1-1zM10 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg>
                    </div>

                    <div>
                        <label for="wilayah" class="block text-sm font-medium text-gray-700 mb-1">WILAYAH:</label>
                        <select id="wilayah" name="wilayah" x-model="editWilayah"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="Area Gedung A">Area Gedung A</option>
                            <option value="Area Parkir Belakang">Area Parkir Belakang</option>
                            <option value="Area Pos-2">Area Pos-2</option>
                            <option value="Lobby VVIP">Lobby VVIP</option>
                            <option value="Area BAU">Area BAU</option>
                            <option value="Area Kantin">Area Kantin</option>
                            <option value="Area BAAM">Area BAAM</option>
                            <option value="Akses Lorong GD-3">Akses Lorong GD-3</option>
                            <option value="Akses Lorong GD-2">Akses Lorong GD-2</option>
                            <option value="Area Pos-3">Area Pos-3</option>
                            <option value="Akses Besi GD-2">Akses Besi GD-2</option>
                            <option value="Akses Kaca GD-2">Akses Kaca GD-2</option>
                            <option value="Akses Selatan Audit">Akses Selatan Audit</option>
                            <option value="Akses Ruang Lektor">Akses Ruang Lektor</option>
                            <option value="Akses Parkir Basement">Akses Parkir Basement</option>
                            <option value="Akses Lift GD-2">Akses Lift GD-2</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full bg-green-500 text-white font-bold py-2 px-4 rounded-lg shadow hover:bg-green-600 transition">
                        SUBMIT
                    </button>
                </div>
            </form>
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
                <button type="button" @click="showDeleteModal = false" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300">
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