@if(isset($data['temu']) && count($data['temu']) > 0)
    <h3>BARANG TEMUAN</h3>
    <table border="1" style="border-collapse: collapse; width: 100%; border: 1px solid #000000;">
        <thead>
            <tr>
                <th style="{{ $thStyle }}">NO</th>
                <th style="{{ $thStyle }}">FOTO</th>
                <th style="{{ $thStyle }}">WAKTU LAPOR</th>
                <th style="{{ $thStyle }}">NAMA BARANG</th>
                <th style="{{ $thStyle }}">PELAPOR / PEMILIK</th>
                <th style="{{ $thStyle }}">LOKASI</th>
                <th style="{{ $thStyle }}">STATUS</th>
                <th style="{{ $thStyle }}">CATATAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['temu'] as $index => $item)
                @php
                    $hasFoto1 = isset($item->foto) && $item->foto;
                    $hasFoto2 = isset($item->foto_penerima) && $item->foto_penerima && strtolower($item->status ?? '') === 'selesai';
                    $doubleRow = $hasFoto1 && $hasFoto2;
                    $rowSpanAttr = $doubleRow ? 'rowspan="2"' : '';
                @endphp
                <tr>
                    <td style="{{ $tdCenterStyle }}">{{ $index + 1 }}</td>
                    <td style="{{ $tdCenterStyle }}; text-align: center; vertical-align: top;">
                        @if(isset($item->foto) && $item->foto)
                            <strong>Barang :</strong><br><br><br><br><br>
                        @endif

                        @if(isset($item->foto) && $item->foto && isset($item->foto_penerima) && $item->foto_penerima && strtolower($item->status ?? '') === 'selesai')
                            <br>
                        @endif

                        @if(isset($item->foto_penerima) && $item->foto_penerima && strtolower($item->status ?? '') === 'selesai')
                            <strong>Penerima :</strong><br><br><br><br>
                        @endif
                    </td>

                    <td style="{{ $tdCenterStyle }}" {!! $rowSpanAttr !!}>{{ \Carbon\Carbon::parse($item->waktu_lapor ?? $item->created_at)->format('d/m/Y H:i') }}</td>
                    <td style="{{ $tdStyle }}" {!! $rowSpanAttr !!}>{{ $item->nama_barang ?? '-' }}</td>
                    <td style="{{ $tdStyle }} font-weight: normal;" {!! $rowSpanAttr !!}>
                        <strong>Pelapor :</strong><br>
                        {{ $item->nama_pelapor ?? '-' }}<br><br>
                        <strong>Pemilik :</strong><br>
                        {{ $item->nama_penerima ?? '-' }}
                    </td>
                    <td style="{{ $tdStyle }}" {!! $rowSpanAttr !!}>{{ $item->lokasi_penemuan ?? '-' }}</td>
                    <td style="{{ $tdCenterStyle }}" {!! $rowSpanAttr !!}>{{ ucfirst($item->status ?? '-') }}</td>
                    <td style="{{ $tdStyle }}" {!! $rowSpanAttr !!}>{{ $item->catatan ?? '-' }}</td>
                </tr>

                {{-- Row 2 (Only if double photo) --}}
                @if($doubleRow)
                <tr>
                    <td style="{{ $tdCenterStyle }}; text-align: center; vertical-align: middle;">
                        <strong>Penerima :</strong><br>
                        <img src="{{ public_path('storage/' . $item->foto_penerima) }}" height="50" width="auto" style="display: block; margin: auto; margin-top: 5px;">
                    </td>
                </tr>
                @endif
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
                <th style="{{ $thStyle }}">WAKTU TITIP</th>
                <th style="{{ $thStyle }}">NAMA BARANG</th>
                <th style="{{ $thStyle }}" colspan="2">PENITIP / PENERIMA</th>
                <th style="{{ $thStyle }}">STATUS</th>
                <th style="{{ $thStyle }}">CATATAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['titip'] as $index => $item)
                @php
                    $hasFoto1 = isset($item->foto) && $item->foto;
                    $hasFoto2 = isset($item->foto_penerima) && $item->foto_penerima && strtolower($item->status ?? '') === 'selesai';
                    $doubleRow = $hasFoto1 && $hasFoto2;
                    $rowSpanAttr = $doubleRow ? 'rowspan="2"' : '';
                @endphp
                <tr>
                    <td style="{{ $tdCenterStyle }}">{{ $index + 1 }}</td>
                    <td style="{{ $tdCenterStyle }}; text-align: center; vertical-align: top;">
                        @if(isset($item->foto) && $item->foto)
                            <strong>Barang :</strong><br><br><br><br><br>
                        @endif
                        
                        @if(isset($item->foto) && $item->foto && isset($item->foto_penerima) && $item->foto_penerima && strtolower($item->status ?? '') === 'selesai')
                            <br>
                        @endif

                        @if(isset($item->foto_penerima) && $item->foto_penerima && strtolower($item->status ?? '') === 'selesai')
                            <strong>Penerima :</strong><br><br><br><br>
                        @endif
                    </td>

                    <td style="{{ $tdCenterStyle }}" {!! $rowSpanAttr !!}>{{ \Carbon\Carbon::parse($item->waktu_titip ?? $item->created_at)->format('d/m/Y H:i') }}</td>
                    <td style="{{ $tdStyle }}" {!! $rowSpanAttr !!}>{{ $item->nama_barang ?? '-' }}</td>
                    <td style="{{ $tdStyle }} font-weight: normal;" colspan="2" {!! $rowSpanAttr !!}>
                        <strong>Penitip :</strong><br>
                        {{ $item->nama_penitip ?? '-' }}<br><br>
                        <strong>Penerima :</strong><br>
                        {{ $item->tujuan ?? '-' }}
                    </td>
                    <td style="{{ $tdCenterStyle }}" {!! $rowSpanAttr !!}>{{ ucfirst($item->status ?? '-') }}</td>
                    <td style="{{ $tdStyle }}" {!! $rowSpanAttr !!}>{{ $item->catatan ?? '-' }}</td>
                </tr>

                {{-- Row 2 (Only if double photo) --}}
                @if($doubleRow)
                <tr>
                    <td style="{{ $tdCenterStyle }}; text-align: center; vertical-align: middle;">
                        <strong>Penerima :</strong><br>
                        <img src="{{ public_path('storage/' . $item->foto_penerima) }}" height="50" width="auto" style="display: block; margin: auto; margin-top: 5px;">
                    </td>
                </tr>
                @endif
            @endforeach
        </tbody>
    </table>
@endif

@if((!isset($data['temu']) || count($data['temu']) == 0) && (!isset($data['titip']) || count($data['titip']) == 0))
        <p>Tidak ada data barang temuan dan titipan</p>
@endif
