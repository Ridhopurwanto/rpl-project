{{-- TABEL (Desktop) --}}
<div class="hidden md:block overflow-x-auto">
    <table class="w-full min-w-max table-fixed">
        <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500">
            <tr>
                <th class="py-3 px-4 text-center w-[8%]">No</th>
                <th class="py-3 px-4 text-center w-[25%]">Nopol</th>
                <th class="py-3 px-4 text-center w-[37%]">Pemilik</th>
                <th class="py-3 px-4 text-center w-[15%]">Tipe</th>
                <th class="py-3 px-4 text-center w-[15%]">Aksi</th>
            </tr>
        </thead>
        <tbody class="text-sm divide-y divide-gray-200">
            @forelse($kendaraanMaster as $index => $kendaraan)
            <tr>
                <td class="py-2 px-4">{{ $index + 1 }}.</td>
                <td class="py-2 px-4 font-medium">{{ $kendaraan->nomor_plat }}</td>
                <td class="py-2 px-4">{{ $kendaraan->pemilik }}</td>
                <td class="py-2 px-4 text-center">
                    <span class="text-xs font-semibold px-2 py-1 rounded-full 
                        {{ $kendaraan->tipe == 'Roda 4' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                        {{ $kendaraan->tipe }}
                    </span>
                </td>
                <td class="py-2 px-4 text-center">
                    <div class="flex justify-center space-x-3">
                        <button @click="$dispatch('edit-master', { id: {{ $kendaraan->id_kendaraan }}, plat: '{{ $kendaraan->nomor_plat }}', pemilik: '{{ $kendaraan->pemilik }}', tipe: '{{ $kendaraan->tipe }}' })" class="text-blue-500 hover:text-blue-700">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828zM5 12V7a2 2 0 012-2h2.586l-4 4H5zM3 15a2 2 0 00-2 2v2h16v-2a2 2 0 00-2-2H3z"></path></svg>
                        </button>
                        <button @click="$dispatch('delete-master', { id: {{ $kendaraan->id_kendaraan }} })" class="text-red-500 hover:text-red-700">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="py-4 px-4 text-center text-gray-500">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- CARD LAYOUT (Mobile) --}}
<div class="md:hidden space-y-3 p-3">
    @forelse($kendaraanMaster as $index => $kendaraan)
        <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
            <div class="bg-gradient-to-r from-[#2a4a6f] to-[#4a6a8f] px-4 py-2.5 flex justify-between items-center">
                <div>
                    <p class="text-xs text-blue-200 font-semibold uppercase">{{ $kendaraan->nomor_plat }}</p>
                    <p class="text-white font-bold text-base">{{ $kendaraan->pemilik }}</p>
                </div>
                <span class="text-xs font-bold px-2.5 py-1 rounded-full 
                    {{ $kendaraan->tipe == 'Roda 4' ? 'bg-blue-500 text-white' : 'bg-green-500 text-white' }}">
                    {{ $kendaraan->tipe }}
                </span>
            </div>

            <div class="p-4">
                <div class="flex gap-2">
                    <button @click="$dispatch('edit-master', { id: {{ $kendaraan->id_kendaraan }}, plat: '{{ $kendaraan->nomor_plat }}', pemilik: '{{ $kendaraan->pemilik }}', tipe: '{{ $kendaraan->tipe }}' })" 
                            class="flex-1 bg-blue-500 text-white font-bold py-2 rounded-lg hover:bg-blue-600 transition flex items-center justify-center gap-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828zM5 12V7a2 2 0 012-2h2.586l-4 4H5zM3 15a2 2 0 00-2 2v2h16v-2a2 2 0 00-2-2H3z"></path></svg>
                        <span class="text-xs">Edit</span>
                    </button>
                    <button @click="$dispatch('delete-master', { id: {{ $kendaraan->id_kendaraan }} })" 
                            class="flex-1 bg-red-500 text-white font-bold py-2 rounded-lg hover:bg-red-600 transition flex items-center justify-center gap-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                        <span class="text-xs">Hapus</span>
                    </button>
                </div>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-xl shadow-md p-8 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <p class="text-gray-500 font-semibold">Tidak ada data.</p>
        </div>
    @endforelse
</div>