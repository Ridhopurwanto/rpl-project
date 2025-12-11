{{-- Pagination Desktop --}}
<div class="hidden md:flex justify-between items-center px-6 py-4 border-t border-gray-200">
    <div class="text-sm text-gray-600">
        Showing {{ $kendaraanMaster->firstItem() ?? 0 }} to {{ $kendaraanMaster->lastItem() ?? 0 }} of {{ $kendaraanMaster->total() }} entries
    </div>
    <div class="flex items-center gap-1">
        @if ($kendaraanMaster->onFirstPage())
            <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Previous</span>
        @else
            <button onclick="loadMasterPage({{ $kendaraanMaster->currentPage() - 1 }})" class="px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Previous</button>
        @endif
        @foreach(range(1, $kendaraanMaster->lastPage()) as $page)
            @if($page == $kendaraanMaster->currentPage())
                <span class="px-3 py-1 text-white bg-[#1e3a5f] rounded font-bold">{{ $page }}</span>
            @else
                <button onclick="loadMasterPage({{ $page }})" class="px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">{{ $page }}</button>
            @endif
        @endforeach
        @if ($kendaraanMaster->hasMorePages())
            <button onclick="loadMasterPage({{ $kendaraanMaster->currentPage() + 1 }})" class="px-3 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Next</button>
        @else
            <span class="px-3 py-1 text-gray-400 bg-gray-100 rounded cursor-not-allowed">Next</span>
        @endif
    </div>
</div>

{{-- Pagination Mobile --}}
<div class="md:hidden px-3 pb-3">
    <div class="text-sm text-gray-600 text-center mb-3">
        Showing {{ $kendaraanMaster->firstItem() ?? 0 }} to {{ $kendaraanMaster->lastItem() ?? 0 }} of {{ $kendaraanMaster->total() }} entries
    </div>
    <div class="flex justify-center items-center gap-1">
        @if ($kendaraanMaster->onFirstPage())
            <span class="px-2 py-1 text-xs text-gray-400 bg-gray-100 rounded cursor-not-allowed">Prev</span>
        @else
            <button onclick="loadMasterPage({{ $kendaraanMaster->currentPage() - 1 }})" class="px-2 py-1 text-xs text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Prev</button>
        @endif
        <span class="px-2 py-1 text-xs text-white bg-[#1e3a5f] rounded font-bold">{{ $kendaraanMaster->currentPage() }}</span>
        <span class="text-xs text-gray-500">of {{ $kendaraanMaster->lastPage() }}</span>
        @if ($kendaraanMaster->hasMorePages())
            <button onclick="loadMasterPage({{ $kendaraanMaster->currentPage() + 1 }})" class="px-2 py-1 text-xs text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Next</button>
        @else
            <span class="px-2 py-1 text-xs text-gray-400 bg-gray-100 rounded cursor-not-allowed">Next</span>
        @endif
    </div>
</div>