<!DOCTYPE html>
<html>
<head>
    <title>Rekap Presensi Dosen</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 5px 0; color: #0c6046; }
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background-color: #f2f2f2; font-weight: bold; }
        .footer { margin-top: 30px; text-align: right; }
        .meta { margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>REKAPITULASI KEHADIRAN DOSEN</h2>
        <h3>{{ strtoupper($prodi) }}</h3>
        <p>Periode: {{ $bulan }} {{ $tahun }}</p>
    </div>

    <div class="meta">
        Dicetak pada: {{ now()->isoFormat('D MMMM Y H:i') }}
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Dosen</th>
                <th>NIDN</th>
                <th>Total Hadir</th>
                <th>Terlambat</th>
                <th>Izin/Sakit</th>
                <th>% Kehadiran</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dosens as $index => $dosen)
                @php
                    $hadir = \App\Models\Presensi::where('dosen_id', $dosen->id)->where('status', 'hadir')->whereMonth('tanggal', \Carbon\Carbon::parse($bulan)->month)->whereYear('tanggal', $tahun)->count();
                    $terlambat = \App\Models\Presensi::where('dosen_id', $dosen->id)->where('status', 'terlambat')->whereMonth('tanggal', \Carbon\Carbon::parse($bulan)->month)->whereYear('tanggal', $tahun)->count();
                    $izin = \App\Models\Presensi::where('dosen_id', $dosen->id)->whereIn('status', ['izin', 'sakit'])->whereMonth('tanggal', \Carbon\Carbon::parse($bulan)->month)->whereYear('tanggal', $tahun)->count();
                    $totalTarget = 4; // Placeholder
                    $persentase = $totalTarget > 0 ? round((($hadir + $terlambat) / $totalTarget) * 100, 1) : 0;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $dosen->nama_gelar }}</td>
                    <td>{{ $dosen->nidn }}</td>
                    <td>{{ $hadir }}</td>
                    <td>{{ $terlambat }}</td>
                    <td>{{ $izin }}</td>
                    <td>{{ $persentase }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Mengetahui,</p>
        <br><br><br>
        <p><b>__________________________</b></p>
        <p>Bagian Akademik</p>
    </div>
</body>
</html>
