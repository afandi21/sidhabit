@extends('layouts.app')

@section('title', 'Riwayat Presensi')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <div class="d-flex align-items-center justify-content-between">
            <h4 class="fw-bold mb-0 text-primary">Riwayat Kehadiran</h4>
            <div class="d-flex gap-2">
                <form action="{{ route('dosen.riwayat') }}" method="GET" class="d-flex gap-2">
                    <select name="month" class="form-select form-select-sm" onchange="this.form.submit()">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->isoFormat('MMMM') }}
                            </option>
                        @endforeach
                    </select>
                    <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                        @foreach(range(now()->year - 2, now()->year) as $y)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius: 20px;">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4 py-3">Tanggal & Waktu</th>
                                <th class="py-3">Mata Kuliah & Kelas</th>
                                <th class="py-3">Ruangan</th>
                                <th class="py-3">Status</th>
                                <th class="py-3 pe-4 text-end">Durasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($riwayats as $r)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($r->tanggal)->isoFormat('dddd, D MMM Y') }}</div>
                                    <small class="text-muted">
                                        {{ substr($r->jam_masuk, 0, 5) }} 
                                        {!! $r->jam_keluar ? ' &mdash; ' . substr($r->jam_keluar, 0, 5) : '<span class="badge bg-warning text-dark ms-1" style="font-size: 0.6rem;">Sesi Berjalan</span>' !!}
                                    </small>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $r->jadwalMengajar->mataKuliah->nama_mk }}</div>
                                    <div class="small text-muted">{{ $r->jadwalMengajar->kelas }} (Pertemuan #{{ $r->pertemuan_ke }})</div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-primary border border-primary border-opacity-10 py-2 px-3">
                                        <i class="bi bi-door-open me-1"></i> {{ $r->jadwalMengajar->ruangan->kode_ruangan }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $statusClass = match($r->status) {
                                            'hadir' => 'bg-success',
                                            'terlambat' => 'bg-warning text-dark',
                                            'izin', 'sakit' => 'bg-info',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $statusClass }} rounded-pill px-3">
                                        {{ ucfirst($r->status) }}
                                    </span>
                                </td>
                                <td class="pe-4 text-end">
                                    @if($r->durasi_menit)
                                        <span class="fw-bold">{{ $r->durasi_menit }}</span> <small class="text-muted">menit</small>
                                    @else
                                        <span class="text-muted">&mdash;</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="opacity-25 mb-3">
                                        <i class="bi bi-journal-x" style="font-size: 4rem;"></i>
                                    </div>
                                    <h6 class="text-muted">Tidak ada riwayat presensi di bulan ini.</h6>
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
