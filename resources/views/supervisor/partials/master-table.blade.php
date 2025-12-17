{{-- TABEL (Desktop) --}}
<div class="hidden md:block overflow-x-auto">
    <table class="w-full min-w-max table-fixed">
        <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500">
            <tr>
                <th class="py-3 px-4 text-center w-[8%]">No</th>
                <th class="py-3 px-4 text-center w-[25%]">Nopol</th>
                <th class="py-3 px-4 text-center w-[37%]">Pemilik</th>
                <th class="py-3 px-4 text-center w-[15%]">Tipe</th>

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

{{-- CARD LAYOUT (Mobile) --}}
        <div class="md:hidden space-y-2 p-3">
            @forelse($kendaraanMaster as $index => $kendaraan)
                <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200 master-card" data-tipe="{{ $kendaraan->tipe }}" data-searchtext="{{ strtolower($kendaraan->nomor_plat . ' ' . $kendaraan->pemilik) }}">
                    <div class="flex gap-3 p-3">
                        {{-- Info Kendaraan --}}
                        <div class="flex-1 min-w-0">
                            {{-- Nopol & Tipe Badge --}}
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h4 class="font-bold text-gray-800 text-sm">{{ $kendaraan->nomor_plat }}</h4>
                                    <p class="text-gray-600 text-xs">{{ $kendaraan->pemilik }}</p>
                                </div>
                                <span class="text-xs font-bold px-2 py-1 rounded-full 
                                    {{ $kendaraan->tipe == 'Roda 4' ? 'bg-blue-500 text-white' : 'bg-green-500 text-white' }}">
                                    {{ $kendaraan->tipe }}
                                </span>
                            </div>


                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-500 text-sm font-semibold">Tidak ada data.</p>
                </div>
            @endforelse
        </div>