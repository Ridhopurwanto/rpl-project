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
    class="space-y-5">

    {{-- GROUP 1: Informasi Login --}}
    <div class="p-4 bg-blue-50/50 rounded-xl border border-blue-100">
        <h3 class="text-sm font-bold text-[#1e3a5f] mb-3 flex items-center">
            {{-- Icon Header --}}
            <svg class="w-4 h-4 mr-2 text-[#1e3a5f]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 19l-1 1-1-1-1 1-1-1-1 1-1-1-5.636-5.636A6 6 0 1115 7z"></path></svg>
            AKUN PENGGUNA
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Username --}}
            <div x-show="!{{ $isEdit ? 'true' : 'false' }}">
                <label class="block text-xs font-bold text-[#1e3a5f] uppercase tracking-wide mb-1">Username</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        {{-- Icon Uniform Color --}}
                        <svg class="h-5 w-5 text-[#1e3a5f]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    </div>
                    <input type="text" name="username" x-model="username" placeholder="Masukkan username" {{ !$isEdit ? 'required' : '' }}
                        class="pl-10 w-full bg-white border border-gray-300 text-gray-800 text-sm font-medium rounded-lg shadow-sm focus:ring-[#1e3a5f] focus:border-[#1e3a5f] block p-2.5">
                </div>
            </div>

            {{-- Peran (Role) - Readonly --}}
            <div x-show="!{{ $isEdit ? 'true' : 'false' }}">
                <label class="block text-xs font-bold text-[#1e3a5f] uppercase tracking-wide mb-1">Peran</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        {{-- Icon Uniform Color --}}
                        <svg class="h-5 w-5 text-[#1e3a5f]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                    </div>
                    <input type="text" value="Anggota" disabled
                        class="pl-10 w-full bg-gray-100 text-gray-500 border border-gray-200 text-sm font-bold rounded-lg cursor-not-allowed block p-2.5 shadow-inner">
                    <input type="hidden" name="peran" value="anggota" x-model="peran">
                </div>
            </div>
        </div>

        {{-- Password Section --}}
        <div x-show="!{{ $isEdit ? 'true' : 'false' }} || (peran === 'komandan' || peran === 'anggota' || peran === 'bau')" class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            {{-- Password --}}
            <div>
                <label class="block text-xs font-bold text-[#1e3a5f] uppercase tracking-wide mb-1">
                    Password @if($isEdit) <span class="text-[10px] text-[#1e3a5f] normal-case ml-1 font-normal italic opacity-70">(Isi jika ingin mengubah)</span> @endif
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        {{-- Icon Uniform Color --}}
                        <svg class="h-5 w-5 text-[#1e3a5f]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                    </div>
                    <input type="password" name="password" placeholder="*******" {{ !$isEdit ? 'required' : '' }}
                        class="pl-10 w-full bg-white border border-gray-300 text-gray-800 text-sm font-medium rounded-lg shadow-sm focus:ring-[#1e3a5f] focus:border-[#1e3a5f] block p-2.5">
                </div>
            </div>

            {{-- Konfirmasi Password --}}
            <div>
                <label class="block text-xs font-bold text-[#1e3a5f] uppercase tracking-wide mb-1">Ulangi Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        {{-- Icon Uniform Color --}}
                        <svg class="h-5 w-5 text-[#1e3a5f]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <input type="password" name="password_confirmation" placeholder="Konfirmasi Password" {{ !$isEdit ? 'required' : '' }}
                        class="pl-10 w-full bg-white border border-gray-300 text-gray-800 text-sm font-medium rounded-lg shadow-sm focus:ring-[#1e3a5f] focus:border-[#1e3a5f] block p-2.5">
                </div>
            </div>
        </div>
    </div>

    {{-- GROUP 2: Data Diri --}}
    <div class="p-4 bg-white rounded-xl border border-gray-200 shadow-sm">
        <h3 class="text-sm font-bold text-[#1e3a5f] mb-3 flex items-center">
             {{-- Icon Header --}}
            <svg class="w-4 h-4 mr-2 text-[#1e3a5f]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" /></svg>
            DATA PRIBADI
        </h3>

        {{-- Nama Lengkap --}}
        <div class="mb-4">
            <label class="block text-xs font-bold text-[#1e3a5f] uppercase tracking-wide mb-1">Nama Lengkap</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    {{-- Icon Uniform Color --}}
                    <svg class="h-5 w-5 text-[#1e3a5f]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                </div>
                <input type="text" name="nama_lengkap" x-model="nama_lengkap" required placeholder="Nama Lengkap sesuai KTP"
                    class="pl-10 w-full bg-white border border-gray-300 text-gray-800 text-sm font-bold rounded-lg shadow-sm focus:ring-[#1e3a5f] focus:border-[#1e3a5f] block p-2.5">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            {{-- No HP --}}
            <div>
                <label class="block text-xs font-bold text-[#1e3a5f] uppercase tracking-wide mb-1">No. Handphone</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        {{-- Icon Uniform Color --}}
                        <svg class="h-5 w-5 text-[#1e3a5f]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                    </div>
                    <input type="text" name="no_hp" x-model="no_hp" placeholder="08..."
                        class="pl-10 w-full bg-white border border-gray-300 text-gray-800 text-sm font-medium rounded-lg shadow-sm focus:ring-[#1e3a5f] focus:border-[#1e3a5f] block p-2.5">
                </div>
            </div>

            {{-- Tanggal Lahir --}}
            <div>
                <label class="block text-xs font-bold text-[#1e3a5f] uppercase tracking-wide mb-1">Tanggal Lahir</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        {{-- Icon Uniform Color --}}
                        <svg class="h-5 w-5 text-[#1e3a5f]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    </div>
                    <input type="date" name="tanggal_lahir" x-model="tanggal_lahir"
                        class="pl-10 w-full bg-white border border-gray-300 text-gray-800 text-sm font-medium rounded-lg shadow-sm focus:ring-[#1e3a5f] focus:border-[#1e3a5f] block p-2.5">
                </div>
            </div>
        </div>

        {{-- Alamat --}}
        <div>
            <label class="block text-xs font-bold text-[#1e3a5f] uppercase tracking-wide mb-1">Alamat Domisili</label>
            <div class="relative">
                <div class="absolute top-3 left-0 pl-3 flex items-start pointer-events-none">
                    {{-- Icon Uniform Color --}}
                    <svg class="h-5 w-5 text-[#1e3a5f]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <textarea name="alamat" rows="2" x-model="alamat" placeholder="Nama Jalan, RT/RW, Kelurahan..."
                    class="pl-10 w-full bg-white border border-gray-300 text-gray-800 text-sm font-medium rounded-lg shadow-sm focus:ring-[#1e3a5f] focus:border-[#1e3a5f] block p-2.5"></textarea>
            </div>
        </div>
    </div>

    {{-- GROUP 3: Pengaturan Lainnya --}}
    <div class="p-4 bg-white rounded-xl border border-gray-200 shadow-sm">
        <h3 class="text-sm font-bold text-[#1e3a5f] mb-3 flex items-center">
             {{-- Icon Header --}}
            <svg class="w-4 h-4 mr-2 text-[#1e3a5f]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            PENGATURAN STATUS
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            
            {{-- Jenis Jadwal --}}
            <div x-show="peran === 'anggota' || peran === 'komandan'">
                <label class="block text-xs font-bold text-[#1e3a5f] uppercase tracking-wide mb-1">Jenis Jadwal</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        {{-- Icon Uniform Color --}}
                        <svg class="h-5 w-5 text-[#1e3a5f]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <select name="jenis_jadwal" x-model="jenis_jadwal" required
                        class="pl-10 w-full bg-white border border-gray-300 text-[#1e3a5f] text-sm font-bold rounded-lg shadow-sm focus:ring-[#1e3a5f] focus:border-[#1e3a5f] block p-2.5 cursor-pointer">
                        <option value="shift">Shift</option>
                        <option value="non_shift">Non-Shift</option>
                    </select>
                </div>
            </div>

            {{-- Status Akun --}}
            <div x-show="{{ $isEdit ? 'true' : 'false' }}">
                <label class="block text-xs font-bold text-[#1e3a5f] uppercase tracking-wide mb-1">Status Akun</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        {{-- Icon Uniform Color --}}
                        <svg class="h-5 w-5 text-[#1e3a5f]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" /></svg>
                    </div>
                    <select name="status" x-model="status" required
                        class="pl-10 w-full bg-white border border-gray-300 text-gray-800 text-sm font-bold rounded-lg shadow-sm focus:ring-[#1e3a5f] focus:border-[#1e3a5f] block p-2.5 cursor-pointer">
                        <option value="Aktif">Aktif</option>
                        <option value="Tidak Aktif">Tidak Aktif</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Foto Profil --}}
        <div class="mt-4">
            <label class="block text-xs font-bold text-[#1e3a5f] uppercase tracking-wide mb-2">Foto Profil</label>
            <div class="flex items-center justify-center w-full">
                <label for="foto_profil" class="flex flex-col items-center justify-center w-full h-24 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-white hover:border-[#1e3a5f] transition-all duration-300 group">
                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                        {{-- Icon Uniform Color (Default & Hover state handled by group-hover) --}}
                        <svg class="w-6 h-6 mb-2 text-[#1e3a5f] group-hover:text-[#1e3a5f] transition-colors opacity-60 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        <p class="mb-1 text-xs text-gray-500 group-hover:text-gray-700"><span class="font-semibold text-[#1e3a5f]">Klik upload</span> / drag file</p>
                    </div>
                    <input id="foto_profil" name="foto_profil" type="file" class="hidden" />
                </label>
            </div>
        </div>
    </div>

</div>