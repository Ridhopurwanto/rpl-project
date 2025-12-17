{{-- TABEL (Desktop) --}}
<div class="hidden md:block overflow-x-auto">
    <table class="w-full min-w-max table-fixed">
        <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-500">
            <tr>
                <th class="py-3 px-4 text-center w-[6%]">No</th>
                <th class="py-3 px-4 text-center w-[14%]">Nopol</th>
                <th class="py-3 px-4 text-center w-[20%]">Pemilik</th>
                <th class="py-3 px-4 text-center w-[10%]">Tipe</th>
                <th class="py-3 px-4 text-center w-[11%]">Masuk</th>
                <th class="py-3 px-4 text-center w-[11%]">Keluar</th>
                <th class="py-3 px-4 text-center w-[16%]">Ket.</th>

            </tr>
        </thead>
        <tbody class="text-sm divide-y divide-gray-200">
            @forelse($riwayat as $index => $log)
            <tr>
                <td class="py-2 px-4">{{ $index + 1 }}.</td>
                <td class="py-2 px-4 font-medium">{{ $log->nopol ?? 'N/A' }}</td>
                <td class="py-2 px-4">{{ $log->pemilik ?? 'N/A' }}</td>
                <td class="py-2 px-4 text-center">
                    <span class="text-xs font-semibold px-2 py-1 rounded-full 
                        {{ $log->tipe == 'Roda 4' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                        {{ $log->tipe ?? '-' }}
                    </span>
                </td>
                <td class="py-2 px-4 text-gray-700">
                    @if($log->waktu_masuk && $log->waktu_masuk->format('Y-m-d') == $tanggalFilter)
                        {{ $log->waktu_masuk->format('H:i:s') }}
                    @else <span class="text-gray-400">-</span> @endif
                </td>
                <td class="py-2 px-4 text-gray-700">
                    @if($log->waktu_keluar && $log->waktu_keluar->format('Y-m-d') == $tanggalFilter)
                        {{ $log->waktu_keluar->format('H:i:s') }}
                    @else <span class="text-gray-400">-</span> @endif
                </td>
                <td class="py-2 px-4">
                    <span class="text-xs font-semibold px-2 py-1 rounded-full 
                        {{ $log->keterangan == 'Menginap' ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $log->keterangan ?? 'Tidak Menginap' }}
                    </span>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="py-4 px-4 text-center text-gray-500">Data tidak ditemukan.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- CARD LAYOUT (Mobile) --}}
        <div class="md:hidden space-y-2 p-3">
            @forelse($riwayat as $index => $log)
                <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200 master-card" data-tipe="{{ $log->tipe }}" data-searchtext="{{ strtolower(($log->nopol ?? '') . ' ' . ($log->pemilik ?? '')) }}">
                    <div class="flex gap-3 p-3">
                        {{-- Info Kendaraan --}}
                        <div class="flex-1 min-w-0">
                            {{-- Nopol & Tipe Badge --}}
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h4 class="font-bold text-gray-800 text-sm">{{ $log->nopol ?? 'N/A' }}</h4>
                                    <p class="text-gray-600 text-xs">{{ $log->pemilik ?? 'N/A' }}</p>
                                </div>
                                <span class="text-xs font-bold px-2 py-1 rounded-full 
                                    {{ $log->tipe == 'Roda 4' ? 'bg-blue-500 text-white' : 'bg-green-500 text-white' }}">
                                    {{ $log->tipe ?? '-' }}
                                </span>
                            </div>

                            {{-- Waktu Masuk/Keluar --}}
                            <div class="grid grid-cols-2 gap-2 mb-2 text-xs">
                                <div>
                                    <p class="text-gray-500 font-semibold">Masuk:</p>
                                    <p class="text-gray-800 font-bold">
                                        @if($log->waktu_masuk && $log->waktu_masuk->format('Y-m-d') == $tanggalFilter)
                                            {{ $log->waktu_masuk->format('H:i:s') }}
                                        @else <span class="text-gray-400">-</span> @endif
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-500 font-semibold">Keluar:</p>
                                    <p class="text-gray-800 font-bold">
                                        @if($log->waktu_keluar && $log->waktu_keluar->format('Y-m-d') == $tanggalFilter)
                                            {{ $log->waktu_keluar->format('H:i:s') }}
                                        @else <span class="text-gray-400">-</span> @endif
                                    </p>
                                </div>
                            </div>

                            {{-- Keterangan --}}
                            <div class="mb-2">
                                <span class="text-xs font-semibold px-2 py-1 rounded-full 
                                    {{ $log->keterangan == 'Menginap' ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $log->keterangan ?? 'Tidak Menginap' }}
                                </span>
                            </div>

                            {{-- Status --}}
                            @if($log->kendaraan)
                                <div class="flex items-center justify-center gap-1 bg-green-50 text-green-700 font-bold py-1.5 rounded text-xs border border-green-300">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                    Terdaftar
                                </div>
                            @else
                                <div class="flex items-center justify-center gap-1 bg-gray-50 text-gray-600 font-bold py-1.5 rounded text-xs border border-gray-300">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                                    Belum Terdaftar
                                </div>
                            @endif
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
                    <p class="text-gray-500 text-sm font-semibold">Data tidak ditemukan.</p>
                </div>
            @endforelse
        </div>