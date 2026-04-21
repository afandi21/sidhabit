<!DOCTYPE html>
<html>
<head>
    <title>Jadwal Mengajar - {{ $dosen->nama_lengkap }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; padding: 0; }
        .info { margin-bottom: 15px; }
        .info table { width: 100%; border: none; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .footer { margin-top: 30px; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h2>KARTU JADWAL MENGAJAR DOSEN</h2>
        <h3>{{ $semesterAktif->nama_semester ?? 'Semester Aktif' }}</h3>
    </div>

    <div class="info">
        <table>
            <tr>
                <td style="border:none; width: 100px;">Nama Dosen</td>
                <td style="border:none;">: <strong>{{ $dosen->gelar_depan }} {{ $dosen->nama_lengkap }}{{ $dosen->gelar_belakang ? ', ' . $dosen->gelar_belakang : '' }}</strong></td>
            </tr>
            <tr>
                <td style="border:none;">NIDN</td>
                <td style="border:none;">: {{ $dosen->nidn ?? '-' }}</td>
            </tr>
            <tr>
                <td style="border:none;">Prodi Home</td>
                <td style="border:none;">: {{ $dosen->programStudi->nama_prodi ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th>Hari</th>
                <th>Waktu</th>
                <th>Mata Kuliah</th>
                <th>SKS</th>
                <th>Kelas</th>
                <th>Ruangan</th>
                <th>Prodi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($jadwals as $j)
            <tr>
                <td>{{ $j->hari->nama_hari }}</td>
                <td>{{ substr($j->jam_mulai, 0, 5) }} - {{ substr($j->jam_selesai, 0, 5) }}</td>
                <td>{{ $j->mataKuliah->nama_mk }}</td>
                <td>{{ $j->mataKuliah->sks }}</td>
                <td>{{ $j->kelas }}</td>
                <td>{{ $j->ruangan->nama_ruangan }}</td>
                <td>{{ $j->mataKuliah->programStudi->kode_prodi }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ date('d/m/Y H:i') }}
    </div>
</body>
</html>
