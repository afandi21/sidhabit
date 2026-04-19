@extends('layouts.app')

@section('title', 'Dashboard Dosen')

@section('content')
<div class="row fade-enter">
    <div class="col-12 mb-4">
        <div class="card bg-primary text-white border-0 shadow-sm overflow-hidden" style="border-radius: 24px;">
            <div class="card-body p-4 p-md-5 position-relative">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="fw-bold mb-1">Selamat Datang, {{ $dosen->nama_gelar }}!</h2>
                        <p class="opacity-75 mb-4">Anda mengajar di Semester {{ $semesterAktif->nama_semester ?? '-' }}</p>
                        <div class="d-flex gap-3">
                            <a href="{{ route('dosen.presensi') }}" class="btn btn-warning px-4 py-2 fw-bold shadow">
                                <i class="bi bi-fingerprint me-2"></i> Presensi Sekarang
                            </a>
                        </div>
                    </div>
                    <div class="col-md-4 d-none d-md-block text-end">
                        <i class="bi bi-calendar-check-fill opacity-25" style="font-size: 8rem; position: absolute; right: 20px; top: 0;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="col-md-4 mb-4">
        <div class="card border-0 h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="p-3 bg-light rounded-circle me-3">
                        <i class="bi bi-journals text-primary fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Mata Kuliah</div>
                        <div class="fs-4 fw-bold text-primary">{{ $jadwals->pluck('mata_kuliah_id')->unique()->count() }} MK</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card border-0 h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="p-3 bg-light rounded-circle me-3">
                        <i class="bi bi-clock-history text-success fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Kehadiran</div>
                        <div class="fs-4 fw-bold text-success">{{ $totalHadir }} Pertemuan</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card border-0 h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="p-3 bg-light rounded-circle me-3">
                        <i class="bi bi-calendar-event text-warning fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Jadwal Hari Ini</div>
                        <div class="fs-4 fw-bold text-warning">{{ $jadwalHariIni->count() }} Sesi</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Jadwal Mengajar (Seluruh Prodi) -->
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-table me-2"></i> Jadwal Mengajar Seluruh Prodi</h5>
                <span class="badge bg-light text-muted">{{ $jadwals->count() }} Sesi Terdaftar</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light text-uppercase small fw-bold">
                            <tr>
                                <th class="ps-4">Hari</th>
                                <th>Jam / Sesi</th>
                                <th>Mata Kuliah</th>
                                <th>Prodi</th>
                                <th>Kelas / Ruang</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jadwals as $jadwal)
                            <tr>
                                <td class="ps-4">
                                    <span class="badge bg-primary px-3 py-2">{{ $jadwal->hari->nama_hari }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}</div>
                                    <small class="text-muted">Sesi {{ $jadwal->sesiMulai->nama_sesi ?? $loop->iteration }}</small>
                                </td>
                                <td>
                                    <div class="fw-bold text-primary">{{ $jadwal->mataKuliah->nama_mk }}</div>
                                    <small class="text-muted">{{ $jadwal->mataKuliah->kode_mk }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary opacity-75">{{ $jadwal->mataKuliah->programStudi->nama_prodi }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $jadwal->kelas }}</div>
                                    <div class="small text-muted"><i class="bi bi-door-open me-1"></i> {{ $jadwal->ruangan->nama_ruangan }}</div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-5 text-center text-muted">
                                    <i class="bi bi-calendar-x fs-1 opacity-25 d-block mb-3"></i>
                                    Belum ada jadwal mengajar yang terdaftar untuk semester ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
