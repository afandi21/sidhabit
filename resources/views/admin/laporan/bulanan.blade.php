@extends('layouts.app')

@section('title', 'Laporan Bulanan')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h4 class="fw-bold mb-1 text-primary">Rekapitulasi Bulanan</h4>
                <p class="text-muted mb-0 small">Ringkasan kehadiran dosen per bulan</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.laporan.bulanan.excel', request()->all()) }}" class="btn btn-success btn-sm px-3 rounded-pill">
                    <i class="bi bi-file-earmark-excel me-1"></i> Excel
                </a>
                <a href="{{ route('admin.laporan.bulanan.pdf', request()->all()) }}" class="btn btn-danger btn-sm px-3 rounded-pill">
                    <i class="bi bi-file-earmark-pdf me-1"></i> PDF
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm" style="border-radius: 15px;">
            <div class="card-body p-3">
                <form action="{{ route('admin.laporan.bulanan') }}" method="GET" class="row g-3 align-items-end text-start">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Bulan</label>
                        <select name="bulan" class="form-select form-select-sm" onchange="this.form.submit()">
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->isoFormat('MMMM') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted text-uppercase">Tahun</label>
                        <select name="tahun" class="form-select form-select-sm" onchange="this.form.submit()">
                            @foreach(range(now()->year - 2, now()->year) as $y)
                                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted text-uppercase">Program Studi</label>
                        <select name="program_studi_id" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">Semua Program Studi</option>
                            @foreach($prodis as $p)
                                <option value="{{ $p->id }}" {{ $prodiId == $p->id ? 'selected' : '' }}>{{ $p->nama_prodi }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 text-start">
        <div class="card border-0 shadow-sm" style="border-radius: 20px;">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4 py-3">Nama Dosen</th>
                                <th class="py-3">Hadir</th>
                                <th class="py-3">Izin/Sakit</th>
                                <th class="py-3">Alfa</th>
                                <th class="py-3">Total Menit</th>
                                <th class="py-3 pe-4 text-end">Persentase</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rekap as $dosenId => $r)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold">{{ $r['nama'] }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">{{ $r['hadir'] }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3">{{ $r['izin'] }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">{{ $r['alfa'] }}</span>
                                </td>
                                <td>{{ $r['total_menit'] }} <small class="text-muted">m</small></td>
                                <td class="pe-4 text-end">
                                    @php
                                        $totalTarget = 4; // Placeholder for target meetings per month
                                        $persentase = $totalTarget > 0 ? round(($r['hadir'] / $totalTarget) * 100, 1) : 0;
                                    @endphp
                                    <div class="fw-bold {{ $persentase >= 80 ? 'text-success' : ($persentase >= 50 ? 'text-warning' : 'text-danger') }}">
                                        {{ $persentase }}%
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">Tidak ada data untuk periode ini.</td>
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
