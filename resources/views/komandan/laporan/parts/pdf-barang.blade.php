{{-- BARANG TEMUAN --}}
@if(isset($barang_temu) && count($barang_temu) > 0)
    <h2 style="margin-top: 20px; color: #1a1a1a; font-size: 13pt; border-bottom: 2px solid #333; padding-bottom: 5px;">
        BARANG TEMUAN
    </h2>

    <table style="border-collapse: collapse; width: 100%; margin: 10px 0; font-size: 10pt;">
        <thead>
            <tr style="background-color: #cccccc;">
                <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 4%">NO</th>
                <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 14%">WAKTU LAPOR</th>
                <th style="border: 1px solid #000; padding: 6px; text-align: left; width: 18%">NAMA BARANG</th>
                <th style="border: 1px solid #000; padding: 6px; text-align: left; width: 18%">PELAPOR</th>
                <th style="border: 1px solid #000; padding: 6px; text-align: left; width: 18%">LOKASI</th>
                <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 10%">STATUS</th>
                <th style="border: 1px solid #000; padding: 6px; text-align: left; width: 18%">CATATAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach($barang_temu as $index => $item)
                <tr>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $index + 1 }}</td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">
                        {{ \Carbon\Carbon::parse($item->waktu_lapor ?? $item->created_at)->format('d/m/Y H:i') }}
                    </td>
                    <td style="border: 1px solid #000; padding: 6px;">{{ $item->nama_barang ?? '-' }}</td>
                    <td style="border: 1px solid #000; padding: 6px;">{{ $item->nama_pelapor ?? '-' }}</td>
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
    <h2 style="margin-top: 20px; color: #1a1a1a; font-size: 13pt; border-bottom: 2px solid #333; padding-bottom: 5px;">
        BARANG TITIPAN
    </h2>

    <table style="border-collapse: collapse; width: 100%; margin: 10px 0; font-size: 10pt;">
        <thead>
            <tr style="background-color: #cccccc;">
                <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 4%">NO</th>
                <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 14%">WAKTU TITIP</th>
                <th style="border: 1px solid #000; padding: 6px; text-align: left; width: 18%">NAMA BARANG</th>
                <th style="border: 1px solid #000; padding: 6px; text-align: left; width: 18%">PENITIP</th>
                <th style="border: 1px solid #000; padding: 6px; text-align: left; width: 18%">TUJUAN</th>
                <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 10%">STATUS</th>
                <th style="border: 1px solid #000; padding: 6px; text-align: left; width: 18%">CATATAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach($barang_titip as $index => $item)
                <tr>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $index + 1 }}</td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: center;">
                        {{ \Carbon\Carbon::parse($item->waktu_titip ?? $item->created_at)->format('d/m/Y H:i') }}
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