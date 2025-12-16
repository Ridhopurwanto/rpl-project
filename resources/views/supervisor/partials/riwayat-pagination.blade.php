{{-- Pagination Desktop --}}
@if($riwayat->total() > 0)
    <div class="hidden md:flex justify-between items-center px-6 py-4 border-t border-gray-200">
        <div class="text-sm text-gray-600">
            Showing {{ $riwayat->firstItem() ?? 0 }} to {{ $riwayat->lastItem() ?? 0 }} of {{ $riwayat->total() }} entries
        </div>
        <div class="flex items-center gap-1">
            @if ($riwayat->onFirstPage())
                <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Previous</span>
            @else
                <button onclick="loadRiwayatPage({{ $riwayat->currentPage() - 1 }})" class="px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Previous</button>
            @endif
            
            @php
                $current = $riwayat->currentPage();
                $last = $riwayat->lastPage();
                $start = max(1, $current - 2);
                $end = min($last, $current + 2);
            @endphp
            
            @if($start > 1)
                <button onclick="loadRiwayatPage(1)" class="px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">1</button>
                @if($start > 2)
                    <span class="px-3 py-1 text-gray-500">...</span>
                @endif
            @endif
            
            @for($page = $start; $page <= $end; $page++)
                @if($page == $current)
                    <span class="px-3 py-1 text-white bg-[#1e3a5f] rounded font-bold">{{ $page }}</span>
                @else
                    <button onclick="loadRiwayatPage({{ $page }})" class="px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">{{ $page }}</button>
                @endif
            @endfor
            
            @if($end < $last)
                @if($end < $last - 1)
                    <span class="px-3 py-1 text-gray-500">...</span>
                @endif
                <button onclick="loadRiwayatPage({{ $last }})" class="px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">{{ $last }}</button>
            @endif
            
            @if ($riwayat->hasMorePages())
                <button onclick="loadRiwayatPage({{ $riwayat->currentPage() + 1 }})" class="px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Next</button>
            @else
                <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Next</span>
            @endif
        </div>
    </div>

    {{-- Pagination Mobile --}}
    <div class="md:hidden flex justify-between items-center px-3 py-4 border-t border-gray-200">
        <div class="text-xs text-gray-600">
            {{ $riwayat->firstItem() ?? 0 }}-{{ $riwayat->lastItem() ?? 0 }} of {{ $riwayat->total() }}
        </div>
        <div class="flex gap-1">
            @if ($riwayat->onFirstPage())
                <span class="px-2 py-1 text-xs text-gray-400 bg-gray-100 rounded cursor-not-allowed">Prev</span>
            @else
                <button onclick="loadRiwayatPage({{ $riwayat->currentPage() - 1 }})" class="px-2 py-1 text-xs text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Prev</button>
            @endif
            
            @if($start > 1)
                <button onclick="loadRiwayatPage(1)" class="px-2 py-1 text-xs text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">1</button>
                @if($start > 2)
                    <span class="px-2 py-1 text-xs text-gray-500">...</span>
                @endif
            @endif
            
            @for($page = $start; $page <= $end; $page++)
                @if($page == $current)
                    <span class="px-2 py-1 text-xs text-white bg-[#1e3a5f] rounded">{{ $page }}</span>
                @else
                    <button onclick="loadRiwayatPage({{ $page }})" class="px-2 py-1 text-xs text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">{{ $page }}</button>
                @endif
            @endfor
            
            @if($end < $last)
                @if($end < $last - 1)
                    <span class="px-2 py-1 text-xs text-gray-500">...</span>
                @endif
                <button onclick="loadRiwayatPage({{ $last }})" class="px-2 py-1 text-xs text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">{{ $last }}</button>
            @endif
            
            @if ($riwayat->hasMorePages())
                <button onclick="loadRiwayatPage({{ $riwayat->currentPage() + 1 }})" class="px-2 py-1 text-xs text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Next</button>
            @else
                <span class="px-2 py-1 text-xs text-gray-400 bg-gray-100 rounded cursor-not-allowed">Next</span>
            @endif
        </div>
    </div>
@endif