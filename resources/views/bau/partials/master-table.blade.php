<div class="hidden md:block overflow-x-auto">
    <table class="w-full min-w-max table-fixed">
        <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500">
            <tr>
                <th class="py-3 px-4 text-center w-[10%]">No</th>
                <th class="py-3 px-4 text-center w-[30%]">Nopol</th>
                <th class="py-3 px-4 text-center w-[40%]">Pemilik</th>
                <th class="py-3 px-4 text-center w-[20%]">Tipe</th>
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
            </tr>
            @empty
            <tr><td colspan="4" class="py-4 px-4 text-center text-gray-500">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

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