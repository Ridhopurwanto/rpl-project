{{-- Pagination Desktop --}}
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
        @foreach(range(1, $riwayat->lastPage()) as $page)
            @if($page == $riwayat->currentPage())
                <span class="px-3 py-1 text-white bg-[#1e3a5f] rounded font-bold">{{ $page }}</span>
            @else
                <button onclick="loadRiwayatPage({{ $page }})" class="px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">{{ $page }}</button>
            @endif
        @endforeach
        @if ($riwayat->hasMorePages())
            <button onclick="loadRiwayatPage({{ $riwayat->currentPage() + 1 }})" class="px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Next</button>
        @else
            <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Next</span>
        @endif
    </div>
</div>

{{-- Pagination Mobile --}}
<div class="md:hidden px-3 pb-3">
    <div class="text-sm text-gray-600 text-center mb-3">
        Showing {{ $riwayat->firstItem() ?? 0 }} to {{ $riwayat->lastItem() ?? 0 }} of {{ $riwayat->total() }} entries
    </div>
    <div class="flex justify-center items-center gap-1">
        @if ($riwayat->onFirstPage())
            <span class="px-2 py-1 text-xs text-gray-400 bg-gray-100 rounded cursor-not-allowed">Prev</span>
        @else
            <button onclick="loadRiwayatPage({{ $riwayat->currentPage() - 1 }})" class="px-2 py-1 text-xs text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Prev</button>
        @endif
        <span class="px-2 py-1 text-xs text-white bg-[#1e3a5f] rounded font-bold">{{ $riwayat->currentPage() }}</span>
        <span class="text-xs text-gray-500">of {{ $riwayat->lastPage() }}</span>
        @if ($riwayat->hasMorePages())
            <button onclick="loadRiwayatPage({{ $riwayat->currentPage() + 1 }})" class="px-2 py-1 text-xs text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Next</button>
        @else
            <span class="px-2 py-1 text-xs text-gray-400 bg-gray-100 rounded cursor-not-allowed">Next</span>
        @endif
    </div>
</div>