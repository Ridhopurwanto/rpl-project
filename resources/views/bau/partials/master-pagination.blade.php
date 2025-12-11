@if($kendaraanMaster->total() > 0)
<div class="flex justify-between items-center px-6 py-4 border-t border-gray-200">
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
@endif