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

        // Fungsi untuk mengisi form (Dipanggil saat tombol Edit diklik)
        fillForm(data) {
            this.nama_lengkap = data.nama_lengkap || '';
            this.username = data.username || '';
            this.peran = data.peran || 'anggota';
            
            // LOGIKA PERBAIKAN: 
            // Jika data.jenis_jadwal kosong (NULL), kita set default berdasarkan peran
            if (data.jenis_jadwal) {
                this.jenis_jadwal = data.jenis_jadwal;
            } else {
                // Jika null, Komandan -> non_shift, Anggota -> shift
                this.jenis_jadwal = (this.peran === 'komandan') ? 'non_shift' : 'shift';
            }

            this.status = data.status || 'Aktif';
            this.tanggal_lahir = data.tanggal_lahir ? data.tanggal_lahir.substring(0, 10) : '';
            this.no_hp = data.no_hp || '';
            this.alamat = data.alamat || '';
        },

        // Fungsi reset (Dipanggil saat tombol Tambah diklik)
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

    {{-- Username --}}
    <div x-show="!{{ $isEdit ? 'true' : 'false' }}">
        <label for="username" class="flex items-center text-sm font-medium text-gray-700 mb-1">
            <svg class="w-4 h-4 mr-2 text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"></path><path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd"></path></svg>
            USERNAME
        </label>
        <input type="text" id="username" name="username" x-model="username" 
               class="w-full bg-[#2a4a6f] text-white border-none rounded-md shadow-sm px-4 py-2.5 placeholder-gray-400 focus:ring-blue-500"
               placeholder="Username" {{ !$isEdit ? 'required' : '' }}>
    </div>
    
    {{-- Password --}}
    <div x-show="!{{ $isEdit ? 'true' : 'false' }} || (peran === 'komandan' || peran === 'anggota' || peran === 'bau')">
        <label for="password" class="flex items-center text-sm font-medium text-gray-700 mb-1">
            <svg class="w-4 h-4 mr-2 text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"></path><path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd"></path></svg>
            PASSWORD
            @if($isEdit) <span class="text-xs text-gray-500 ml-2 font-normal italic">(Isi jika ingin mengubah)</span> @endif
        </label>
        <input type="password" id="password" name="password" 
               class="w-full bg-[#2a4a6f] text-white border-none rounded-md shadow-sm px-4 py-2.5 placeholder-gray-400 focus:ring-blue-500"
               placeholder="*******" 
               {{ !$isEdit ? 'required' : '' }}>
    </div>

    <div x-show="!{{ $isEdit ? 'true' : 'false' }} || (peran === 'komandan' || peran === 'anggota' || peran === 'bau')">
        <label for="password_confirmation" class="flex items-center text-sm font-medium text-gray-700 mb-1">
            <svg class="w-4 h-4 mr-2 text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"></path><path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd"></path></svg>
            KONFIRMASI PASSWORD
        </label>
        <input type="password" id="password_confirmation" name="password_confirmation"
               class="w-full bg-[#2a4a6f] text-white border-none rounded-md shadow-sm px-4 py-2.5 placeholder-gray-400 focus:ring-blue-500"
               placeholder="Ulangi Password" 
               {{ !$isEdit ? 'required' : '' }}>
    </div>

    {{-- Nama --}}
    <div>
        <label for="nama_lengkap" class="flex items-center text-sm font-medium text-gray-700 mb-1">
            <svg class="w-4 h-4 mr-2 text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"></path><path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd"></path></svg>
            NAMA
        </label>
        <input type="text" id="nama_lengkap" name="nama_lengkap" x-model="nama_lengkap" required
               class="w-full bg-[#2a4a6f] text-white border-none rounded-md shadow-sm px-4 py-2.5 placeholder-gray-400 focus:ring-blue-500"
               placeholder="Nama">
    </div>

    {{-- ======================================================= --}}
    {{-- JENIS JADWAL (EDITABLE untuk Anggota & Komandan)       --}}
    {{-- Perbaikan: Muncul jika peran anggota ATAU komandan      --}}
    {{-- ======================================================= --}}
    <div x-show="peran === 'anggota' || peran === 'komandan'">
        <label for="jenis_jadwal" class="flex items-center text-sm font-medium text-gray-700 mb-1">
            <svg class="w-4 h-4 mr-2 text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"></path><path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd"></path></svg>
            JENIS JADWAL
        </label>
        <select id="jenis_jadwal" name="jenis_jadwal" x-model="jenis_jadwal" required
                class="w-full bg-[#2a4a6f] text-white border-none rounded-md shadow-sm px-4 py-2.5 focus:ring-blue-500">
            <option value="shift">Shift</option>
            <option value="non_shift">Non-Shift</option>
        </select>
    </div>

    {{-- Tanggal Lahir --}}
    <div>
        <label for="tanggal_lahir" class="flex items-center text-sm font-medium text-gray-700 mb-1">
            <svg class="w-4 h-4 mr-2 text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"></path><path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd"></path></svg>
            TANGGAL LAHIR
        </label>
        <input type="date" id="tanggal_lahir" name="tanggal_lahir" x-model="tanggal_lahir"
               class="w-full bg-[#2a4a6f] text-white border-none rounded-md shadow-sm px-4 py-2.5 focus:ring-blue-500"
               style="color-scheme: dark;">
    </div>
    
    {{-- No. HP --}}
    <div>
        <label for="no_hp" class="flex items-center text-sm font-medium text-gray-700 mb-1">
            <svg class="w-4 h-4 mr-2 text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"></path><path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd"></path></svg>
            NO. HP
        </label>
        <input type="text" id="no_hp" name="no_hp" x-model="no_hp"
               class="w-full bg-[#2a4a6f] text-white border-none rounded-md shadow-sm px-4 py-2.5 placeholder-gray-400 focus:ring-blue-500"
               placeholder="087732145567">
    </div>

    {{-- Alamat --}}
    <div>
        <label for="alamat" class="flex items-center text-sm font-medium text-gray-700 mb-1">
            <svg class="w-4 h-4 mr-2 text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"></path><path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd"></path></svg>
            ALAMAT
        </label>
        <textarea id="alamat" name="alamat" rows="3" x-model="alamat"
                  class="w-full bg-[#2a4a6f] text-white border-none rounded-md shadow-sm px-4 py-2.5 placeholder-gray-400 focus:ring-blue-500"
                  placeholder="Jl. Haji Yahya No. 6..."></textarea>
    </div>
    
    {{-- Peran (Role) - Hanya Tampil saat Create --}}
    <div x-show="!{{ $isEdit ? 'true' : 'false' }}">
        <label for="peran" class="flex items-center text-sm font-medium text-gray-700 mb-1">
            <svg class="w-4 h-4 mr-2 text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
            PERAN (ROLE)
        </label>
        
        <div class="relative">
            <input type="text" value="Anggota" disabled
                   class="w-full bg-gray-300 text-gray-600 border-none rounded-md shadow-sm px-4 py-2.5 font-bold cursor-not-allowed focus:ring-0">
            <input type="hidden" name="peran" value="anggota" x-model="peran">
        </div>
        <p class="text-xs text-gray-500 mt-1 italic">*Hanya dapat menambahkan akun Anggota.</p>
    </div>

    {{-- Status Akun - Hanya Tampil saat Edit --}}
    <div x-show="{{ $isEdit ? 'true' : 'false' }}">
        <label for="status" class="flex items-center text-sm font-medium text-gray-700 mb-1">
            STATUS
        </label>
        <select id="status" name="status" x-model="status" required
                class="w-full bg-[#2a4a6f] text-white border-none rounded-md shadow-sm px-4 py-2.5 focus:ring-blue-500">
            <option value="Aktif">Aktif</option>
            <option value="Tidak Aktif">Tidak Aktif</option>
        </select>
    </div>
    
    {{-- Foto Profil --}}
    <div>
        <label for="foto_profil" class="block text-sm font-medium text-gray-700 mb-1">FOTO PROFIL</label>
        <input type="file" id="foto_profil" name="foto_profil"
               class="w-full text-sm text-gray-500
                      file:mr-4 file:py-2 file:px-4
                      file:rounded-full file:border-0
                      file:text-sm file:font-semibold
                      file:bg-blue-50 file:text-blue-700
                      hover:file:bg-blue-100">
        @if($isEdit)
        <p class="mt-1 text-xs text-gray-500">Kosongkan jika tidak ingin mengubah foto.</p>
        @endif
    </div>
</div>