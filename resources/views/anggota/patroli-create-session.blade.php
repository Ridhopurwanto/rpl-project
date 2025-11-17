@extends('layouts.app')

@section('header-left')
    <a href="{{ route('anggota.patroli.index') }}" class="p-2">
        <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
    </a>
@endsection

@section('content')
<div class="w-full">
    {{-- Dropdown Pilih Jenis Patroli --}}
    <form action="{{ route('anggota.patroli.createSession') }}" method="GET">
        <div class="flex items-center justify-between mt-4">
            <label class="text-sm font-semibold text-gray-700">WAKTU PATROLI</label>
            <select name="jenis_patroli" 
                    onchange="this.form.submit()"
                    class="bg-slate-800 text-white text-sm font-semibold px-4 py-2 rounded-full shadow-md border-0 focus:ring-2 focus:ring-blue-500">
                
                @foreach($opsiJenisPatroli as $opsi)
                    <option value="{{ $opsi }}" {{ $opsi == $jenisPatroliTerpilih ? 'selected' : '' }}>
                        {{ $opsi }}
                        @if(in_array($opsi, $patroliYangSudahSubmit))
                            ✓
                        @endif
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    @if($sudahSubmit)
        {{-- Tampilan jika patroli sudah disubmit --}}
        <div class="mt-6">
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="font-bold">{{ $jenisPatroliTerpilih }} sudah disubmit</p>
                        <p class="text-sm">Patroli ini telah diselesaikan hari ini. Pilih patroli lain dari dropdown di atas.</p>
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- Grid 17 Area --}}
        <div class="mt-6">
            <h3 class="text-xs font-semibold text-gray-500 uppercase mb-2">DAFTAR PATROLI :</h3>
            
            <div class="grid grid-cols-3 gap-3">
                @foreach($semuaArea as $area)
                    @php
                        $isCompleted = in_array($area, $completedCheckpoints);
                    @endphp

                    @if($isCompleted)
                        <div class="relative bg-slate-800 text-white p-4 rounded-lg shadow text-center font-semibold text-sm">
                            {{ $area }}
                            <div class="absolute -top-2 -right-2 w-6 h-6 bg-green-500 rounded-full flex items-center justify-center border-2 border-white">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('anggota.patroli.createCheckpoint', [
                                'jenis_patroli' => $jenisPatroliTerpilih,
                                'wilayah' => $area
                            ]) }}"
                           class="bg-gray-400 text-white p-4 rounded-lg shadow text-center font-semibold text-sm hover:bg-gray-500">
                            {{ $area }}
                        </a>
                    @endif
                @endforeach
            </div>
        </div>

        {{-- Tombol Submit Patroli --}}
        <div class="mt-8 mb-20">
            <form id="submitPatroliForm" action="{{ route('anggota.patroli.submitSession') }}" method="POST">
                @csrf
                <input type="hidden" name="jenis_patroli" value="{{ $jenisPatroliTerpilih }}">
                
                @if($totalCompleted >= 17)
                    <button type="submit" 
                            class="w-full bg-green-600 text-white p-4 rounded-lg shadow font-bold hover:bg-green-700">
                        SUBMIT PATROLI (SELESAI)
                    </button>
                @else
                    <button type="button" 
                            class="w-full bg-gray-400 text-gray-600 p-4 rounded-lg shadow font-bold" 
                            disabled>
                        SUBMIT PATROLI ({{ $totalCompleted }}/17 Selesai)
                    </button>
                @endif
            </form>
        </div>
    @endif
</div>

{{-- SweetAlert Script --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('submitPatroliForm');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (response.ok) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Patroli sudah disubmit',
                        icon: 'success',
                        confirmButtonText: 'Ok',
                        confirmButtonColor: '#16a34a',
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '{{ route('anggota.patroli.index') }}';
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat submit patroli',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat submit patroli',
                    icon: 'error',
                    confirmButtonText: 'Ok'
                });
            });
        });
    }
});
</script>
@endsection