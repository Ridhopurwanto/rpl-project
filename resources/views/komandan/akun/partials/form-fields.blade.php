{{-- 
  File: resources/views/komandan/akun/partials/form-fields.blade.php
  Versi BARU dengan styling Tailwind CSS sesuai Mockup UI
--}}
@php
    $isEdit = $isEdit ?? false;
@endphp

{{-- 
  Kita gunakan Alpine.js x-data untuk data binding
  agar form "Edit" bisa terisi otomatis.
--}}
<div x-data="formFields({{ $isEdit ? 'true, editUser' : 'false' }})" class="space-y-4">

    {{-- Username --}}
    <div x-show="!isEdit">
        <label for="username" class="flex items-center text-sm font-medium text-gray-700 mb-1">
            <svg class="w-4 h-4 mr-2 text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"></path><path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd"></path></svg>
            USERNAME
        </label>
        <input type="text" id="username" name="username" x-model="username" 
               class="w-full bg-[#2a4a6f] text-white border-none rounded-md shadow-sm px-4 py-2.5 placeholder-gray-400 focus:ring-blue-500"
               placeholder="M. YUSUF" {{ !$isEdit ? 'required' : '' }}>
    </div>
    
    {{-- Password --}}
    <div x-show="!isEdit">
        <label for="password" class="flex items-center text-sm font-medium text-gray-700 mb-1">
            <svg class="w-4 h-4 mr-2 text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"></path><path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd"></path></svg>
            PASSWORD
        </label>
        <input type="password" id="password" name="password" 
               class="w-full bg-[#2a4a6f] text-white border-none rounded-md shadow-sm px-4 py-2.5 placeholder-gray-400 focus:ring-blue-500"
               placeholder="STIS2025" {{ !$isEdit ? 'required' : '' }}>
    </div>

    {{-- Konfirmasi Password --}}
    <div x-show="!isEdit">
        <label for="password_confirmation" class="flex items-center text-sm font-medium text-gray-700 mb-1">
            <svg class="w-4 h-4 mr-2 text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"></path><path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd"></path></svg>
            KONFIRMASI PASSWORD
        </label>
        <input type="password" id="password_confirmation" name="password_confirmation"
               class="w-full bg-[#2a4a6f] text-white border-none rounded-md shadow-sm px-4 py-2.5 placeholder-gray-400 focus:ring-blue-500"
               placeholder="Ulangi Password" {{ !$isEdit ? 'required' : '' }}>
    </div>

    {{-- Nama --}}
    <div>
        <label for="nama_lengkap" class="flex items-center text-sm font-medium text-gray-700 mb-1">
            <svg class="w-4 h-4 mr-2 text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"></path><path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd"></path></svg>
            NAMA
        </label>
        <input type="text" id="nama_lengkap" name="nama_lengkap" x-model="nama_lengkap" required
               class="w-full bg-[#2a4a6f] text-white border-none rounded-md shadow-sm px-4 py-2.5 placeholder-gray-400 focus:ring-blue-500"
               placeholder="M. YUSUF">
    </div>

    {{-- Tanggal Lahir --}}
    <div>
        <label for="tanggal_lahir" class="flex items-center text-sm font-medium text-gray-700 mb-1">
            <svg class="w-4 h-4 mr-2 text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"></path><path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd"></path></svg>
            TANGGAL LAHIR
        </label>
        <input type="date" id="tanggal_lahir" name="tanggal_lahir" x-model="tanggal_lahir"
               class="w-full bg-[#2a4a6f] text-white border-none rounded-md shadow-sm px-4 py-2.5 focus:ring-blue-500"
               style="color-scheme: dark;">
    </div>
    
    {{-- No. HP --}}
    <div>
        <label for="no_hp" class="flex items-center text-sm font-medium text-gray-700 mb-1">
            <svg class="w-4 h-4 mr-2 text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"></path><path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd"></path></svg>
            NO. HP
        </label>
        <input type="text" id="no_hp" name="no_hp" x-model="no_hp"
               class="w-full bg-[#2a4a6f] text-white border-none rounded-md shadow-sm px-4 py-2.5 placeholder-gray-400 focus:ring-blue-500"
               placeholder="087732145567">
    </div>

    {{-- Alamat --}}
    <div>
        <label for="alamat" class="flex items-center text-sm font-medium text-gray-700 mb-1">
            <svg class="w-4 h-4 mr-2 text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"></path><path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd"></path></svg>
            ALAMAT
        </label>
        <textarea id="alamat" name="alamat" rows="2" x-model="alamat"
                  class="w-full bg-[#2a4a6f] text-white border-none rounded-md shadow-sm px-4 py-2.5 placeholder-gray-400 focus:ring-blue-500"
                  placeholder="Jl. Haji Yahya No. 6..."></textarea>
    </div>
    
    {{-- 
      FUNGSI PENTING:
      Mockup "Tambah Akun" tidak punya 'Peran' & 'Status'.
      Mockup "Edit Akun" punya 'Status' tapi tidak punya 'Peran'.
      Controller kita BUTUH 'Peran' dan 'Status' saat create.
      Jadi, kita tambahkan di sini dan sembunyikan/tampilkan sesuai 'isEdit'.
    --}}
    
    {{-- Peran (Role) --}}
    <div x-show="!isEdit">
        <label for="peran" class="flex items-center text-sm font-medium text-gray-700 mb-1">
            <svg class="w-4 h-4 mr-2 text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 2a1 1 0 00-1 1v1a1 1 0 002 0V3a1 1 0 00-1-1zM4 4a1 1 0 001 1v1a1 1 0 002 0V5a1 1 0 00-1-1H4zM15 3a1 1 0 011-1v-1a1 1 0 11-2 0v1a1 1 0 011 1zM4 15a1 1 0 011-1v-1a1 1 0 11-2 0v1a1 1 0 011 1zM10 17a1 1 0 011-1v-1a1 1 0 11-2 0v1a1 1 0 011 1zM15 15a1 1 0 011-1v-1a1 1 0 11-2 0v1a1 1 0 011 1zM8 8a1 1 0 011-1V6a1 1 0 01-2 0v1a1 1 0 011 1zM11 8a1 1 0 00-1-1V6a1 1 0 002 0v1a1 1 0 00-1 1z" clip-rule="evenodd"></path></svg>
            PERAN (ROLE)
        </label>
        <select id="peran" name="peran" x-model="peran" required
                class="w-full bg-[#2a4a6f] text-white border-none rounded-md shadow-sm px-4 py-2.5 focus:ring-blue-500">
            <option value="anggota">Anggota</option>
            <option value="komandan">Komandan</option>
            <option value="bau">BAU</option>
        </select>
    </div>

    {{-- Status Akun --}}
    <div>
        <label for="status" class_="{{ $isEdit ? '' : 'flex items-center text-sm font-medium text-gray-700 mb-1' }}">
            <svg x-show="!isEdit" class="w-4 h-4 mr-2 text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 2a1 1 0 00-1 1v1a1 1 0 002 0V3a1 1 0 00-1-1zM4 4a1 1 0 001 1v1a1 1 0 002 0V5a1 1 0 00-1-1H4zM15 3a1 1 0 011-1v-1a1 1 0 11-2 0v1a1 1 0 011 1zM4 15a1 1 0 011-1v-1a1 1 0 11-2 0v1a1 1 0 011 1zM10 17a1 1 0 011-1v-1a1 1 0 11-2 0v1a1 1 0 011 1zM15 15a1 1 0 011-1v-1a1 1 0 11-2 0v1a1 1 0 011 1zM8 8a1 1 0 011-1V6a1 1 0 01-2 0v1a1 1 0 011 1zM11 8a1 1 0 00-1-1V6a1 1 0 002 0v1a1 1 0 00-1 1z" clip-rule="evenodd"></path></svg>
            <span x-show="!isEdit">STATUS</span>
            <span x-show="isEdit" class="flex items-center text-sm font-medium text-gray-700 mb-1">STATUS</span>
        </label>
        <select id="status" name="status" x-model="status" required
                class="w-full bg-[#2a4a6f] text-white border-none rounded-md shadow-sm px-4 py-2.5 focus:ring-blue-500">
            <option value="Aktif">Aktif</option>
            <option value="Tidak Aktif">Tidak Aktif</option>
        </select>
    </div>

    {{-- Foto Profil --}}
    <div>
        <label for="foto_profil" class="flex items-center text-sm font-medium text-gray-700 mb-1">
             <svg class="w-4 h-4 mr-2 text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4V5h12v10zM8.5 8.5a1 1 0 11-2 0 1 1 0 012 0zM10 11a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" clip-rule="evenodd"></path></svg>
            FOTO PROFIL
        </label>
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