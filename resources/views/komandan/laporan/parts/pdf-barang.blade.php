{{-- BARANG TEMUAN --}}
@if(isset($barang_temu) && count($barang_temu) > 0)
    <h2 style="margin: 20px 10px; color: #1a1a1a; font-size: 13pt; border-bottom: 2px solid #333; padding-bottom: 5px; padding-left: 10px; padding-right: 10px;">
        BARANG TEMUAN
    </h2>

    <table style="border-collapse: collapse; width: 95%; margin: 10px auto; font-size: 10pt;">
        <thead>
            <tr style="background-color: #cccccc;">
                <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 4%">NO</th>
                <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 12%">FOTO</th>
                <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 9%">WAKTU LAPOR</th>
                <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 9%">WAKTU AMBIL</th>
                <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 10%">NAMA BARANG</th>
                <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 14%">PELAPOR</th>
                <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 14%">LOKASI</th>
                <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 10%">STATUS</th>
                <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 18%">CATATAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach($barang_temu as $index => $item)
                <tr>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $index + 1 }}</td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">
                        @if(isset($item->foto) && $item->foto)
                            <div style="margin-bottom: 3px;">
                                <small style="font-weight: bold;">Barang:</small><br>
                                <img src="{{ public_path('storage/' . $item->foto) }}" style="max-width: 50px; max-height: 50px; object-fit: cover;">
                            </div>
                        @endif
                        @if(isset($item->foto_penerima) && $item->foto_penerima && strtolower($item->status ?? '') === 'selesai')
                            <div style="margin-top: 3px;">
                                <small style="font-weight: bold;">Penerima:</small><br>
                                <img src="{{ public_path('storage/' . $item->foto_penerima) }}" style="max-width: 50px; max-height: 50px; object-fit: cover;">
                            </div>
                        @endif
                        @if((!isset($item->foto) || !$item->foto) && (!isset($item->foto_penerima) || !$item->foto_penerima))
                            -
                        @endif
                    </td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">
                        {{ \Carbon\Carbon::parse($item->waktu_lapor ?? $item->created_at)->format('d/m/Y H:i') }}
                    </td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">
                        {{ $item->waktu_selesai ? \Carbon\Carbon::parse($item->waktu_selesai)->format('d/m/Y H:i') : '-' }}
                    </td>
                    <td style="border: 1px solid #000; padding: 6px;">{{ $item->nama_barang ?? '-' }}</td>
                    <td style="border: 1px solid #000; padding: 6px;">
                        <strong>Pelapor :</strong><br>
                        {{ $item->nama_pelapor ?? '-' }}<br><br>
                        <strong>Pemilik :</strong><br>
                        {{ $item->nama_penerima ?? '-' }}
                    </td>
                    <td style="border: 1px solid #000; padding: 6px;">{{ $item->lokasi_penemuan ?? '-' }}</td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ ucfirst($item->status ?? '-') }}</td>
                    <td style="border: 1px solid #000; padding: 6px;">{{ $item->catatan ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div style="page-break-after: auto;"></div>
@endif

{{-- BARANG TITIPAN --}}
@if(isset($barang_titip) && count($barang_titip) > 0)
    <h2 style="margin: 20px 10px; color: #1a1a1a; font-size: 13pt; border-bottom: 2px solid #333; padding-bottom: 5px; padding-left: 10px; padding-right: 10px;">
        BARANG TITIPAN
    </h2>

    <table style="border-collapse: collapse; width: 95%; margin: 10px auto; font-size: 10pt;">
        <thead>
            <tr style="background-color: #cccccc;">
                <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 4%">NO</th>
                <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 12%">FOTO</th>
                <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 9%">WAKTU TITIP</th>
                <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 9%">WAKTU TERIMA</th>
                <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 10%">NAMA BARANG</th>
                <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 14%">PENITIP</th>
                <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 14%">PENERIMA</th>
                <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 10%">STATUS</th>
                <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 18%">CATATAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach($barang_titip as $index => $item)
                <tr>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $index + 1 }}</td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">
                        @if(isset($item->foto) && $item->foto)
                            <div style="margin-bottom: 3px;">
                                <small style="font-weight: bold;">Barang:</small><br>
                                <img src="{{ public_path('storage/' . $item->foto) }}" style="max-width: 50px; max-height: 50px; object-fit: cover;">
                            </div>
                        @endif
                        @if(isset($item->foto_penerima) && $item->foto_penerima && strtolower($item->status ?? '') === 'selesai')
                            <div style="margin-top: 3px;">
                                <small style="font-weight: bold;">Penerima:</small><br>
                                <img src="{{ public_path('storage/' . $item->foto_penerima) }}" style="max-width: 50px; max-height: 50px; object-fit: cover;">
                            </div>
                        @endif
                        @if((!isset($item->foto) || !$item->foto) && (!isset($item->foto_penerima) || !$item->foto_penerima))
                            -
                        @endif
                    </td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">
                        {{ \Carbon\Carbon::parse($item->waktu_titip ?? $item->created_at)->format('d/m/Y H:i') }}
                    </td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">
                        {{ $item->waktu_selesai ? \Carbon\Carbon::parse($item->waktu_selesai)->format('d/m/Y H:i') : '-' }}
                    </td>
                    <td style="border: 1px solid #000; padding: 6px;">{{ $item->nama_barang ?? '-' }}</td>
                    <td style="border: 1px solid #000; padding: 6px;">{{ $item->nama_penitip ?? '-' }}</td>
                    <td style="border: 1px solid #000; padding: 6px;">{{ $item->tujuan ?? '-' }}</td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ ucfirst($item->status ?? '-') }}</td>
                    <td style="border: 1px solid #000; padding: 6px;">{{ $item->catatan ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

@if((!isset($barang_temu) || count($barang_temu) == 0) && (!isset($barang_titip) || count($barang_titip) == 0))
    <p style="text-align: center; font-style: italic; color: #666; padding: 15px;">
        Tidak ada data barang temuan dan titipan
    </p>
@endif