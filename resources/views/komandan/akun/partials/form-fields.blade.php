@php
    $isEdit = $isEdit ?? false;
@endphp

<div x-data="{
        // Definisi variabel form
        nama_lengkap: '',
        username: '',
        peran: 'anggota',
        jenis_jadwal: 'shift', 
        status: 'Aktif',
        tanggal_lahir: '',
        no_hp: '',
        alamat: '',

        // Fungsi untuk mengisi form (Edit)
        fillForm(data) {
            this.nama_lengkap = data.nama_lengkap || '';
            this.username = data.username || '';
            this.peran = data.peran || 'anggota';
            
            if (data.jenis_jadwal) {
                this.jenis_jadwal = data.jenis_jadwal;
            } else {
                this.jenis_jadwal = (this.peran === 'komandan') ? 'non_shift' : 'shift';
            }

            this.status = data.status || 'Aktif';
            this.tanggal_lahir = data.tanggal_lahir ? data.tanggal_lahir.substring(0, 10) : '';
            this.no_hp = data.no_hp || '';
            this.alamat = data.alamat || '';
        },

        // Fungsi reset (Create)
        resetForm() {
            this.nama_lengkap = '';
            this.username = '';
            this.peran = 'anggota';
            this.jenis_jadwal = 'shift';
            this.status = 'Aktif';
            this.tanggal_lahir = '';
            this.no_hp = '';
            this.alamat = '';
        }
    }"
    {{-- Event Listener --}}
    @if($isEdit)
        @set-edit-data.window="fillForm($event.detail)"
    @else
        @reset-create-data.window="resetForm()"
    @endif
    class="space-y-4">

    {{-- GROUP 1: Informasi Login (Grid 2 Kolom) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        
        {{-- Username (Icon Abu-abu) --}}
        <div x-show="!{{ $isEdit ? 'true' : 'false' }}">
            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Username</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                </div>
                <input type="text" name="username" x-model="username" placeholder="Masukkan username" {{ !$isEdit ? 'required' : '' }}
                    class="pl-10 w-full bg-gray-50 border border-gray-100 text-gray-800 text-sm font-semibold rounded-xl focus:ring-[#1e3a5f] focus:border-[#1e3a5f] block p-2.5 transition-colors duration-200">
            </div>
        </div>

        {{-- Peran (Role) - Readonly (Icon Biru Tua) --}}
        <div x-show="!{{ $isEdit ? 'true' : 'false' }}">
            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Peran</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-[#1e3a5f]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                </div>
                <input type="text" value="Anggota" disabled
                    class="pl-10 w-full bg-gray-50 text-gray-500 border border-gray-100 text-sm font-bold rounded-xl cursor-not-allowed block p-2.5">
                <input type="hidden" name="peran" value="anggota" x-model="peran">
            </div>
        </div>
    </div>

    {{-- Password Section (Icon Ungu) --}}
    <div x-show="!{{ $isEdit ? 'true' : 'false' }} || (peran === 'komandan' || peran === 'anggota' || peran === 'bau')" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        
        {{-- Password --}}
        <div>
            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">
                Password @if($isEdit) <span class="text-[9px] text-purple-500 normal-case ml-1">(Isi jika ubah)</span> @endif
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                </div>
                <input type="password" name="password" placeholder="*******" {{ !$isEdit ? 'required' : '' }}
                    class="pl-10 w-full bg-gray-50 border border-gray-100 text-gray-800 text-sm font-semibold rounded-xl focus:ring-purple-500 focus:border-purple-500 block p-2.5 transition-colors duration-200">
            </div>
        </div>

        {{-- Konfirmasi Password --}}
        <div>
            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Ulangi Password</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <input type="password" name="password_confirmation" placeholder="Konfirmasi Password" {{ !$isEdit ? 'required' : '' }}
                    class="pl-10 w-full bg-gray-50 border border-gray-100 text-gray-800 text-sm font-semibold rounded-xl focus:ring-purple-500 focus:border-purple-500 block p-2.5 transition-colors duration-200">
            </div>
        </div>
    </div>

    <hr class="border-gray-100 my-2 border-dashed">

    {{-- GROUP 2: Data Diri --}}
    
    {{-- Nama Lengkap (Icon Biru Tua) --}}
    <div>
        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Nama Lengkap</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-[#1e3a5f]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" /></svg>
            </div>
            <input type="text" name="nama_lengkap" x-model="nama_lengkap" required placeholder="Nama Lengkap"
                class="pl-10 w-full bg-gray-50 border border-gray-100 text-gray-800 text-sm font-bold rounded-xl focus:ring-[#1e3a5f] focus:border-[#1e3a5f] block p-2.5 transition-colors duration-200">
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- No HP (Icon Hijau) --}}
        <div>
            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">No. Handphone</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                </div>
                <input type="text" name="no_hp" x-model="no_hp" placeholder="08..."
                    class="pl-10 w-full bg-gray-50 border border-gray-100 text-gray-800 text-sm font-semibold rounded-xl focus:ring-green-500 focus:border-green-500 block p-2.5 transition-colors duration-200">
            </div>
        </div>

        {{-- Tanggal Lahir (Icon Merah) --}}
        <div>
            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Tanggal Lahir</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                </div>
                <input type="date" name="tanggal_lahir" x-model="tanggal_lahir"
                    class="pl-10 w-full bg-gray-50 border border-gray-100 text-gray-800 text-sm font-semibold rounded-xl focus:ring-red-500 focus:border-red-500 block p-2.5 transition-colors duration-200">
            </div>
        </div>
    </div>

    {{-- Alamat (Icon Orange) --}}
    <div>
        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Alamat Domisili</label>
        <div class="relative">
            <div class="absolute top-3 left-0 pl-3 flex items-start pointer-events-none">
                <svg class="h-5 w-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </div>
            <textarea name="alamat" rows="2" x-model="alamat" placeholder="Jalan..."
                class="pl-10 w-full bg-gray-50 border border-gray-100 text-gray-800 text-sm font-medium rounded-xl focus:ring-orange-500 focus:border-orange-500 block p-2.5 transition-colors duration-200"></textarea>
        </div>
    </div>

    {{-- GROUP 3: Pengaturan Akun (Grid) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        
        {{-- Jenis Jadwal (Icon Biru Langit) --}}
        <div x-show="peran === 'anggota' || peran === 'komandan'">
            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Jenis Jadwal</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <select name="jenis_jadwal" x-model="jenis_jadwal" required
                    class="pl-10 w-full bg-gray-50 border border-gray-100 text-[#1e3a5f] text-sm font-bold rounded-xl focus:ring-blue-500 focus:border-blue-500 block p-2.5 cursor-pointer">
                    <option value="shift">Shift</option>
                    <option value="non_shift">Non-Shift</option>
                </select>
            </div>
        </div>

        {{-- Status Akun (Icon Hijau/Merah) --}}
        <div x-show="{{ $isEdit ? 'true' : 'false' }}">
            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Status Akun</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" /></svg>
                </div>
                <select name="status" x-model="status" required
                    class="pl-10 w-full bg-gray-50 border border-gray-100 text-gray-800 text-sm font-bold rounded-xl focus:ring-[#1e3a5f] focus:border-[#1e3a5f] block p-2.5 cursor-pointer">
                    <option value="Aktif">Aktif</option>
                    <option value="Tidak Aktif">Tidak Aktif</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Foto Profil --}}
    <div>
        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Foto Profil</label>
        <div class="flex items-center justify-center w-full">
            <label for="foto_profil" class="flex flex-col items-center justify-center w-full h-24 border-2 border-gray-200 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                    <svg class="w-6 h-6 mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                    <p class="mb-1 text-xs text-gray-500"><span class="font-semibold text-[#1e3a5f]">Klik upload</span> / drag file</p>
                </div>
                <input id="foto_profil" name="foto_profil" type="file" class="hidden" />
            </label>
        </div>
    </div>

</div>