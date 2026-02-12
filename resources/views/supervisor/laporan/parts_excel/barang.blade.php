@if(isset($data['temu']) && count($data['temu']) > 0)
    <h3>BARANG TEMUAN</h3>
    <table border="1" style="border-collapse: collapse; width: 100%; border: 1px solid #000000;">
        <thead>
            <tr>
                <th style="{{ $thStyle }}">NO</th>
                <th style="{{ $thStyle }}">FOTO</th>
                <th style="{{ $thStyle }}">TANGGAL</th>
                <th style="{{ $thStyle }}">NAMA BARANG</th>
                <th style="{{ $thStyle }}">PELAPOR / PEMILIK</th>
                <th style="{{ $thStyle }}">LOKASI</th>
                <th style="{{ $thStyle }}">STATUS</th>
                <th style="{{ $thStyle }}">CATATAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['temu'] as $index => $item)
                <tr>
                    <td style="{{ $tdCenterStyle }}">{{ $index + 1 }}</td>
                    <td style="{{ $tdCenterStyle }}; text-align: left; vertical-align: top; padding-top: 5px; padding-left: 5px;">
                        @if(isset($item->foto) && $item->foto)
                            <strong>Barang :</strong><br>
                            {{-- Space for Image 1 + Gap --}}
                            <br><br><br><br>
                        @endif

                        @if(isset($item->foto_penerima) && $item->foto_penerima && strtolower($item->status ?? '') === 'selesai')
                            <strong>Penerima :</strong><br>
                             {{-- Space for Image 2 --}}
                             <br><br>
                        @endif
                    </td>
                    <td style="{{ $tdStyle }}">
                        <strong>Lapor :</strong><br>
                        {{ \Carbon\Carbon::parse($item->waktu_lapor ?? $item->created_at)->format('d/m/Y H:i') }}
                        <br><br>
                        <strong>Ambil :</strong><br>
                        {{ $item->waktu_selesai ? \Carbon\Carbon::parse($item->waktu_selesai)->format('d/m/Y H:i') : '-' }}
                    </td>
                    <td style="{{ $tdStyle }}">{{ $item->nama_barang ?? '-' }}</td>
                    <td style="{{ $tdStyle }} font-weight: normal;">
                        <strong>Pelapor :</strong><br>
                        {{ $item->nama_pelapor ?? '-' }}<br><br>
                        <strong>Pemilik :</strong><br>
                        {{ $item->nama_penerima ?? '-' }}
                    </td>
                    <td style="{{ $tdStyle }}">{{ $item->lokasi_penemuan ?? '-' }}</td>
                    <td style="{{ $tdCenterStyle }}">{{ ucfirst($item->status ?? '-') }}</td>
                    <td style="{{ $tdStyle }}">{{ $item->catatan ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <br>
@endif

@if(isset($data['titip']) && count($data['titip']) > 0)
    <h3>BARANG TITIPAN</h3>
    <table border="1" style="border-collapse: collapse; width: 100%; border: 1px solid #000000;">
        <thead>
            <tr>
                <th style="{{ $thStyle }}">NO</th>
                <th style="{{ $thStyle }}">FOTO</th>
                <th style="{{ $thStyle }}">TANGGAL</th>
                <th style="{{ $thStyle }}">NAMA BARANG</th>
                <th style="{{ $thStyle }}" colspan="2">PENITIP / PENERIMA</th>
                <th style="{{ $thStyle }}">STATUS</th>
                <th style="{{ $thStyle }}">CATATAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['titip'] as $index => $item)
                <tr>
                    <td style="{{ $tdCenterStyle }}">{{ $index + 1 }}</td>
                    <td style="{{ $tdCenterStyle }}; text-align: left; vertical-align: top; padding-top: 5px; padding-left: 5px;">
                        @if(isset($item->foto) && $item->foto)
                            <strong>Barang :</strong><br>
                            <br><br><br><br>
                        @endif
                        
                        @if(isset($item->foto_penerima) && $item->foto_penerima && strtolower($item->status ?? '') === 'selesai')
                            <strong>Penerima :</strong><br>
                            <br><br>
                        @endif
                    </td>
                    <td style="{{ $tdStyle }}">
                        <strong>Titip :</strong><br>
                        {{ \Carbon\Carbon::parse($item->waktu_titip ?? $item->created_at)->format('d/m/Y H:i') }}
                        <br><br>
                        <strong>Terima :</strong><br>
                        {{ $item->waktu_selesai ? \Carbon\Carbon::parse($item->waktu_selesai)->format('d/m/Y H:i') : '-' }}
                    </td>
                    <td style="{{ $tdStyle }}">{{ $item->nama_barang ?? '-' }}</td>
                    <td style="{{ $tdStyle }} font-weight: normal;" colspan="2">
                        <strong>Penitip :</strong><br>
                        {{ $item->nama_penitip ?? '-' }}<br><br>
                        <strong>Penerima :</strong><br>
                        {{ $item->tujuan ?? '-' }}
                    </td>
                    <td style="{{ $tdCenterStyle }}">{{ ucfirst($item->status ?? '-') }}</td>
                    <td style="{{ $tdStyle }}">{{ $item->catatan ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

@if((!isset($data['temu']) || count($data['temu']) == 0) && (!isset($data['titip']) || count($data['titip']) == 0))
        <p>Tidak ada data barang temuan dan titipan</p>
@endif