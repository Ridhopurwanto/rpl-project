<div x-data="{ 
    notifOpen: false, 
    unreadCount: {{ Auth::user()->unreadNotifications->count() }},
    decrementCount() {
        if (this.unreadCount > 0) this.unreadCount--;
    }
}" 
@decrement-count="decrementCount()"
class="relative">
    {{-- Tombol Lonceng --}}
    <button @click="notifOpen = !notifOpen" class="bg-white p-2 rounded-full shadow text-gray-500 focus:outline-none relative" title="Notifikasi">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341A6.002 6.002 0 006 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>

        {{-- Badge Merah Jumlah Notifikasi --}}
        <span x-show="unreadCount > 0" x-text="unreadCount" class="absolute top-0 right-0 block h-4 w-4 transform -translate-y-1/4 translate-x-1/4 rounded-full bg-red-500 text-white text-[10px] flex items-center justify-center font-bold"></span>
    </button>

    {{-- Dropdown Menu Notifikasi --}}
    <div x-show="notifOpen"
        @click.away="notifOpen = false"
        x-transition
        class="absolute top-full right-0 mt-2 w-80 bg-white rounded-lg shadow-xl z-50 overflow-hidden"
        style="display: none;">

        {{-- Header Dropdown --}}
        <div class="px-4 py-2 bg-gray-50 border-b border-gray-200 font-semibold text-gray-700 flex justify-between items-center">
            <span>Notifikasi</span>
            <span class="text-xs bg-gray-200 px-2 py-1 rounded-full"><span x-text="unreadCount"></span> Baru</span>
        </div>

        {{-- List Notifikasi --}}
        <div class="max-h-64 overflow-y-auto">
            @forelse(Auth::user()->notifications as $notification)
            {{-- Kita perlu menyimpan pesan asli ke variabel lokal $fullMessage --}}
            @php
                $fullMessage = $notification->data['message'] ?? '';
                $limit = 60; // Batasan karakter
                $isTruncated = strlen($fullMessage) > $limit;
                $markAsReadUrl = route('markAsRead',$notification->id);
                $isRead = !is_null($notification->read_at);
            @endphp

            {{-- BARU: Tambahkan x-data untuk mengontrol tampilan --}}
            <div x-data="{ 
                    isExpanded: false, 
                    isRead: {{ $isRead ? 'true' : 'false' }},
                    markAsRead() {
                        if (!this.isRead) {
                            this.isRead = true;
                            fetch('{{ $markAsReadUrl }}'); // Kirim request di background
                            $dispatch('decrement-count'); // Panggil event parent
                        }
                    }
                }"
                class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100 transition-colors duration-200"
                :class="{ 'bg-blue-50': !isRead, 'bg-white': isRead }">

                <div class="flex items-start">
                    <div class="flex-1">
                        <p class="text-sm font-bold text-gray-800 flex justify-between">
                            {{ $notification->data['title'] ?? 'Pemberitahuan' }}
                            <span x-show="!isRead" class="w-2 h-2 bg-blue-500 rounded-full mt-1"></span>
                        </p>
                        
                        {{-- BARU: Konten Pesan Notifikasi --}}
                        <p class="text-xs text-gray-600 mt-1">
                            {{-- Tampilkan pesan penuh jika isExpanded TRUE, atau pesan terpotong jika FALSE --}}
                            <span x-show="!isExpanded">
                                {{ Str::limit($fullMessage, $limit) }}
                            </span>
                            <span x-show="isExpanded">
                                {{ $fullMessage }}
                            </span>
                        </p>
                        
                        {{-- BARU: Link "Lihat Selengkapnya..." di pojok kanan --}}
                        @if ($isTruncated)
                            <div class="mt-1 flex justify-end">
                                <button type="button" @click.prevent="isExpanded = !isExpanded; markAsRead()" 
                                        class="text-xs font-semibold text-blue-600 hover:text-blue-800">
                                    <span x-show="!isExpanded">Lihat Selengkapnya...</span>
                                    <span x-show="isExpanded">Tutup</span>
                                </button>
                            </div>
                        @endif
                        
                        {{-- Tetap gunakan link markAsRead untuk waktu notifikasi (agar notif ditandai saat diklik) --}}
                        <a href="{{ $markAsReadUrl }}" @click.prevent="markAsRead(); window.location.href='{{ $markAsReadUrl }}'" class="text-xs text-gray-400 mt-1 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ $notification->created_at->diffForHumans() }}
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="px-4 py-6 text-center text-gray-500 text-sm">
                Tidak ada notifikasi saat ini.
            </div>
            @endforelse
        </div>
    </div>
</div>