<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Progres - SIP-RPS</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 5px 0; }
        .meta { margin-bottom: 20px; }
        @media print {
            @page { size: landscape; } /* Cetak Landscape agar tabel muat */
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h1>DINAS PENDIDIKAN DAN KEBUDAYAAN</h1>
        <h1>PROVINSI KALIMANTAN SELATAN</h1>
        <p>Laporan Progres Rehabilitasi Sekolah</p>
    </div>

    <div class="meta">
        <strong>Dicetak Tanggal:</strong> {{ date('d F Y') }} <br>
        <strong>Filter Proyek:</strong> {{ request('proyek_id') ? 'Terfilter' : 'Semua Proyek' }} <br>
        <strong>Periode:</strong> 
        {{ request('start_date') ? date('d/m/Y', strtotime(request('start_date'))) : 'Awal' }} 
        s/d 
        {{ request('end_date') ? date('d/m/Y', strtotime(request('end_date'))) : 'Sekarang' }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 100px;">Tanggal</th>
                <th>Nama Proyek</th>
                <th>Sekolah</th>
                <th>Pelapor</th>
                <th style="width: 50px;">Fisik</th>
                <th style="width: 50px;">Keu</th>
                <th>Keterangan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($laporans as $laporan)
            <tr>
                <td>{{ date('d/m/Y', strtotime($laporan->tanggal_laporan)) }}</td>
                <td>{{ $laporan->proyek->nama_proyek }}</td>
                <td>{{ $laporan->proyek->sekolah->nama_sekolah }}</td>
                <td>{{ $laporan->pelapor->nama }}</td>
                <td style="text-align: center;">{{ $laporan->progres_fisik }}%</td>
                <td style="text-align: center;">{{ $laporan->progres_keuangan }}%</td>
                <td>{{ $laporan->keterangan }}</td>
                <td>{{ $laporan->status_validasi }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 50px; text-align: right; margin-right: 50px;">
        <p>Banjarmasin, {{ date('d F Y') }}</p>
        <br><br><br>
        <p><strong>( Kepala Dinas / Admin )</strong></p>
    </div>

</body>
</html>