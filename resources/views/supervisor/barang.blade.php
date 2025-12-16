@extends('layouts.app')

{{-- Terapkan layout full-width --}}
@section('mobile_width', 'max-w-full')
@section('desktop_width', 'lg:max-w-full')

@section('header-left')
    <a class="flex items-center border-2 border-[#1a2847] text-[#1a2847] text-sm font-bold px-4 py-2 rounded-full">
        BARANG
    </a>
@endsection

@section('content')
{{-- Style untuk animasi timer notifikasi --}}
    <style>
        @keyframes countdown {
            from { stroke-dashoffset: 0; }
            to { stroke-dashoffset: 100; }
        }
        .timer-circle {
            fill: none;
            stroke-width: 3;
            stroke-linecap: round;
            stroke-dasharray: 100;
            stroke-dashoffset: 0;
            transform: rotate(-90deg);
            transform-origin: center;
        }
        /* Animasi berjalan 5 detik sesuai timeout javascript */
        .animate-timer {
            animation: countdown 5s linear forwards;
        }
    </style>

{{-- Wrapper Alpine.js untuk Modal Foto --}}
<div class="w-full mx-auto"
     x-data="{ 
        showPhotoModal: false, 
        photos: [],
        currentPhotoIndex: 0,
        touchStartX: 0,
        touchEndX: 0,
        showTemuan: true,
        showTitipan: true,
        showSuccessNotif: {{ session('success') ? 'true' : 'false' }},
        showErrorNotif: {{ session('error') ? 'true' : 'false' }}
     }">
    
    {{-- Floating Notification Success --}}
    <div x-show="showSuccessNotif" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-x-full"
            x-transition:enter-end="opacity-100 transform translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-x-0"
            x-transition:leave-end="opacity-0 transform translate-x-full"
            x-init="if(showSuccessNotif) setTimeout(() => showSuccessNotif = false, 5000)"
            class="fixed top-4 right-4 z-50 bg-green-500 text-white pl-6 pr-2 py-1 rounded-lg shadow-2xl flex items-center gap-3 min-w-[300px] max-w-md"
            style="display: none;">
        <div class="flex-shrink-0">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div class="flex-1">
            <p class="font-semibold text-sm">{{ session('success') }}</p>
        </div>
        {{-- Tombol Close dengan Timer Circle --}}
        <button @click="showSuccessNotif = false" class="relative flex-shrink-0 text-white hover:text-green-100 transition-colors w-10 h-10 flex items-center justify-center">
            {{-- SVG Timer Circle --}}
            <svg class="absolute inset-0 w-full h-full p-1" viewBox="0 0 36 36">
                    <path class="text-green-700/40 timer-circle" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" style="stroke-dasharray: 100; stroke-dashoffset: 0;"></path>
                    <path class="text-white timer-circle animate-timer" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" stroke="currentColor"></path>
            </svg>
            {{-- Icon X --}}
            <svg class="w-4 h-4 relative z-10" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
        </button>
    </div>

    {{-- Floating Notification Error --}}
    <div x-show="showErrorNotif" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-x-full"
            x-transition:enter-end="opacity-100 transform translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-x-0"
            x-transition:leave-end="opacity-0 transform translate-x-full"
            x-init="if(showErrorNotif) setTimeout(() => showErrorNotif = false, 5000)"
            class="fixed top-4 right-4 z-50 bg-red-500 text-white pl-6 pr-2 py-1 rounded-lg shadow-2xl flex items-center gap-3 min-w-[300px] max-w-md"
            style="display: none;">
        <div class="flex-shrink-0">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div class="flex-1">
            <p class="font-semibold text-sm">{{ session('error') }}</p>
        </div>
        {{-- Tombol Close dengan Timer Circle --}}
        <button @click="showErrorNotif = false" class="relative flex-shrink-0 text-white hover:text-red-100 transition-colors w-10 h-10 flex items-center justify-center">
            {{-- SVG Timer Circle --}}
            <svg class="absolute inset-0 w-full h-full p-1" viewBox="0 0 36 36">
                    <path class="text-red-800/40 timer-circle" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" style="stroke-dasharray: 100; stroke-dashoffset: 0;"></path>
                    <path class="text-white timer-circle animate-timer" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" stroke="currentColor"></path>
            </svg>
            <svg class="w-4 h-4 relative z-10" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
        </button>
    </div>
    
    <h2 class="text-2xl font-bold text-slate-800 mb-4">Laporan Barang</h2>

    <div id="barang-results">
        @include('supervisor.partials.barang-list')
    </div>

    {{-- Modal Tampil Foto dengan SLIDER (Sama persis dengan Anggota) --}}
    <div x-show="showPhotoModal" style="display: none;"
        class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center p-4 z-[60]" x-transition
        @keydown.window.escape="showPhotoModal = false"
        @keydown.window.arrow-right="if(showPhotoModal && currentPhotoIndex < photos.length - 1) currentPhotoIndex++"
        @keydown.window.arrow-left="if(showPhotoModal && currentPhotoIndex > 0) currentPhotoIndex--"
        @touchstart="touchStartX = $event.changedTouches[0].screenX" 
        @touchend="
            touchEndX = $event.changedTouches[0].screenX;
            if (touchStartX - touchEndX > 50 && currentPhotoIndex < photos.length - 1) currentPhotoIndex++;
            if (touchEndX - touchStartX > 50 && currentPhotoIndex > 0) currentPhotoIndex--;
        "
        @mousedown="touchStartX = $event.screenX"
        @mouseup="
            touchEndX = $event.screenX;
            if (touchStartX - touchEndX > 50 && currentPhotoIndex < photos.length - 1) currentPhotoIndex++;
            if (touchEndX - touchStartX > 50 && currentPhotoIndex > 0) currentPhotoIndex--;
        ">

        <div @click.outside="showPhotoModal = false" class="relative max-w-4xl w-full">

            {{-- Header Modal --}}
            <div class="flex justify-between items-center mb-4">
                <div class="text-white">
                    <p class="text-sm text-gray-300">Foto <span x-text="currentPhotoIndex + 1"></span> dari <span
                            x-text="photos.length"></span></p>
                    <p class="text-xs text-gray-400 mt-1"
                        x-text="currentPhotoIndex === 0 ? 'Foto Barang' : 'Foto Penerima'"></p>
                </div>
                <button @click="showPhotoModal = false"
                    class="text-white hover:text-gray-300 text-2xl font-bold bg-gray-800 hover:bg-gray-700 rounded-full w-10 h-10 flex items-center justify-center transition-colors">
                    ×
                </button>
            </div>

            {{-- Image Container --}}
            <div class="relative flex justify-center bg-transparent">
                <img :src="photos[currentPhotoIndex]"
                    class="w-auto h-auto max-h-[70vh] object-contain rounded-lg border-2 border-gray-700">

                {{-- Previous Button --}}
                <button x-show="currentPhotoIndex > 0" @click="currentPhotoIndex--"
                    class="absolute left-4 top-1/2 -translate-y-1/2 bg-black bg-opacity-50 hover:bg-opacity-75 text-white rounded-full w-12 h-12 flex items-center justify-center transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                        </path>
                    </svg>
                </button>

                {{-- Next Button --}}
                <button x-show="currentPhotoIndex < photos.length - 1" @click="currentPhotoIndex++"
                    class="absolute right-4 top-1/2 -translate-y-1/2 bg-black bg-opacity-50 hover:bg-opacity-75 text-white rounded-full w-12 h-12 flex items-center justify-center transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>

            {{-- Indicator Dots --}}
            <div class="flex justify-center gap-2 mt-4" x-show="photos.length > 1">
                <template x-for="(photo, index) in photos" :key="index">
                    <button @click="currentPhotoIndex = index"
                        :class="currentPhotoIndex === index ? 'bg-white w-8' : 'bg-gray-500 w-2'"
                        class="h-2 rounded-full transition-all"></button>
                </template>
            </div>

            {{-- Hint --}}
            <div class="text-center mt-4 text-gray-400 text-xs" x-show="photos.length > 1">
                Swipe atau gunakan tombol panah untuk navigasi
            </div>
        </div>
    </div>

    {{-- Loading Indicator --}}
    <div id="loading-indicator" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-30">
        <div class="bg-white p-4 rounded-lg shadow-xl flex items-center gap-3">
            <div class="animate-spin rounded-full h-8 w-8 border-4 border-[#1e3a5f] border-t-transparent"></div>
            <span class="font-bold text-[#1e3a5f]">Memuat Data...</span>
        </div>
    </div>


</div>

{{-- SCRIPT AJAX SEARCH & PAGINATION --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const resultsContainer = document.getElementById('barang-results');

    // Debounce function
    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    function fetchResults(url) {
        toggleLoading(true);
        if (!url) {
            // Collect params from inputs inside the results container (which are re-rendered)
            const searchInputTemuan = document.getElementById('searchInputTemuan');
            const searchInputTitipan = document.getElementById('searchInputTitipan');
            const dateInputTemuan = document.getElementById('tanggal_temuan');
            const dateInputTitipan = document.getElementById('tanggal_titipan');
            const perPageTemuan = document.getElementById('per_page_temuan');
            const perPageTitipan = document.getElementById('per_page_titipan');

            const params = new URLSearchParams(window.location.search);
            
            if (searchInputTemuan && searchInputTemuan.value) params.set('search_temuan', searchInputTemuan.value); else params.delete('search_temuan');
            if (searchInputTitipan && searchInputTitipan.value) params.set('search_titipan', searchInputTitipan.value); else params.delete('search_titipan');
            if (dateInputTemuan && dateInputTemuan.value) params.set('tanggal_temuan', dateInputTemuan.value);
            if (dateInputTitipan && dateInputTitipan.value) params.set('tanggal_titipan', dateInputTitipan.value);
            if (perPageTemuan && perPageTemuan.value) params.set('per_page_temuan', perPageTemuan.value);
            if (perPageTitipan && perPageTitipan.value) params.set('per_page_titipan', perPageTitipan.value);

            url = `${window.location.pathname}?${params.toString()}`;
            
            // Update Browser URL
            window.history.pushState({}, '', url);
        }

        // Fetch Data
        fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                resultsContainer.innerHTML = html;
                // Re-attach listeners after content update
                attachListeners();
                
                // Important: Re-initialize Alpine.js for the new content if needed
                // But since x-data is on the parent div, removing x-data from children 
                // means we rely on parent scope. Alpine typically handles DOM updates if inside an x-data component.
                // However, since we replace HTML via innerHTML, existing Alpine bindings might be lost 
                // unless we are careful. 
                // Because we replaced the entire content, we might need to be careful about x-show/x-collapse.
                // Since the parent component holds the state (showTemuan, showTitipan), and the partial 
                // contains x-show="showTemuan" relying on parent.
                // Alpine usually needs to 'discover' new DOM nodes.
                
                // Note: Livewire is better for this, but with vanilla JS + Alpine + AJAX replacement:
                // We might need to manually trigger Alpine to scan the new DOM.
            })
            .catch(error => console.error('Error:', error))
            .finally(() => {
                toggleLoading(false);
            });
    }

    function toggleLoading(show) {
        const loader = document.getElementById('loading-indicator');
        if (loader) loader.style.display = show ? 'flex' : 'none';
    }

    function attachListeners() {
        const debouncedFetch = debounce(() => fetchResults(), 500);

        const searchInputTemuan = document.getElementById('searchInputTemuan');
        if (searchInputTemuan) searchInputTemuan.addEventListener('input', debouncedFetch);
        
        const searchInputTitipan = document.getElementById('searchInputTitipan');
        if (searchInputTitipan) searchInputTitipan.addEventListener('input', debouncedFetch);

        const dateInputTemuan = document.getElementById('tanggal_temuan');
        if (dateInputTemuan) dateInputTemuan.addEventListener('change', () => fetchResults());

        const dateInputTitipan = document.getElementById('tanggal_titipan');
        if (dateInputTitipan) dateInputTitipan.addEventListener('change', () => fetchResults());

        const perPageTemuan = document.getElementById('per_page_temuan');
        if (perPageTemuan) perPageTemuan.addEventListener('change', () => fetchResults());

        const perPageTitipan = document.getElementById('per_page_titipan');
        if (perPageTitipan) perPageTitipan.addEventListener('change', () => fetchResults());
    }

    // Attach initial listeners
    attachListeners();

    // Delegate Pagination Clicks
    resultsContainer.addEventListener('click', function(e) {
        if (e.target.closest('.pagination-link')) {
            e.preventDefault();
            const link = e.target.closest('.pagination-link');
            fetchResults(link.href);
            // Also update URL to match the pagination link
            window.history.pushState({}, '', link.href);
        }
    });

    // Handle Back/Forward Browser Buttons
    window.addEventListener('popstate', function() {
        fetchResults(window.location.href);
    });
});
</script>
@endsection
