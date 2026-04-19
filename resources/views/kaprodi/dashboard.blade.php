@extends('layouts.app')

@section('title', 'Dashboard Kaprodi')

@section('content')
<div class="row fade-enter">
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, var(--primary-color) 0%, #0a4d38 100%); border-radius: 20px;">
            <div class="card-body p-4 text-white">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="mb-1 fw-bold">Monitoring Program Studi</h4>
                        <p class="mb-0 opacity-75">{{ $prodi->nama_prodi }} - Kendali Mutu Perkuliahan</p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <span class="badge bg-dark bg-opacity-25 py-2 px-3 rounded-pill text-uppercase tracking-wider small text-white border border-white border-opacity-25">
                            {{ now()->isoFormat('dddd, D MMMM Y') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="col-md-3 mb-4">
        <div class="card border-0 shadow-sm h-100 card-hover" style="border-radius: 15px;">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2 me-3">
                        <i class="bi bi-people fs-4"></i>
                    </div>
                    <h6 class="mb-0 text-muted">Total Dosen</h6>
                </div>
                <h2 class="fw-bold mb-0">{{ $stats['total_dosen'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card border-0 shadow-sm h-100 card-hover" style="border-radius: 15px;">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-success bg-opacity-10 text-success rounded-3 p-2 me-3">
                        <i class="bi bi-journal-check fs-4"></i>
                    </div>
                    <h6 class="mb-0 text-muted">Mata Kuliah</h6>
                </div>
                <h2 class="fw-bold mb-0">{{ $stats['total_mk'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card border-0 shadow-sm h-100 card-hover" style="border-radius: 15px;">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-2 me-3">
                        <i class="bi bi-calendar-event fs-4"></i>
                    </div>
                    <h6 class="mb-0 text-muted">Total Jadwal</h6>
                </div>
                <h2 class="fw-bold mb-0">{{ $stats['total_jadwal'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card border-0 shadow-sm h-100 card-hover" style="border-radius: 15px;">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-info bg-opacity-10 text-info rounded-3 p-2 me-3">
                        <i class="bi bi-fingerprint fs-4"></i>
                    </div>
                    <h6 class="mb-0 text-muted">Hadir Hari Ini</h6>
                </div>
                <h2 class="fw-bold mb-0 text-primary">{{ $stats['hadir_hari_ini'] }}</h2>
            </div>
        </div>
    </div>

    <!-- Keaktifan Dosen & Jadwal Berjalan -->
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm" style="border-radius: 15px;">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold"><i class="bi bi-graph-up-arrow me-2 text-primary"></i> Dosen Paling Aktif (Hadir)</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <tbody>
                            @foreach($dosenKeaktifan as $index => $d)
                            <tr>
                                <td class="ps-4" style="width: 50px;">
                                    <span class="badge bg-light text-dark rounded-circle">{{ $index + 1 }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $d->nama_gelar }}</div>
                                    <small class="text-muted">{{ $d->nidn }}</small>
                                </td>
                                <td class="text-end pe-4">
                                    <span class="badge bg-success bg-opacity-10 text-success">{{ $d->presensi_count }} Hadir</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm" style="border-radius: 15px;">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i> Jadwal Mengajar Hari Ini</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 280px;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light small text-muted">
                            <tr>
                                <th class="ps-4">Jam</th>
                                <th>Dosen</th>
                                <th class="pe-4 text-end">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jadwalBerjalan as $j)
                            <tr>
                                <td class="ps-4">
                                    <small class="fw-bold">{{ substr($j->jam_mulai, 0, 5) }}</small>
                                </td>
                                <td>
                                    <div class="small fw-bold">{{ $j->dosen->nama_lengkap }}</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">{{ $j->mataKuliah->nama_mk }}</div>
                                </td>
                                <td class="pe-4 text-end">
                                    @php
                                        $sudahAbsen = \App\Models\Presensi::where('jadwal_mengajar_id', $j->id)->where('tanggal', today())->exists();
                                    @endphp
                                    @if($sudahAbsen)
                                        <span class="badge bg-success rounded-pill" style="font-size: 0.6rem;">HADIR</span>
                                    @else
                                        <span class="badge bg-light text-muted rounded-pill" style="font-size: 0.6rem;">BELUM</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted small">Tidak ada jadwal aktif saat ini.</td>
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
