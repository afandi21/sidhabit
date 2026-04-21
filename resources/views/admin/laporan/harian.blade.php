@extends('layouts.app')

@section('title', 'Laporan Harian')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold">Laporan Kehadiran Harian</h4>
    <p class="text-muted small">Monitoring kehadiran dosen secara real-time berdasarkan tanggal.</p>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('admin.laporan.harian') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">Tanggal</label>
                <input type="date" name="tanggal" class="form-control" value="{{ $tanggal }}">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted">Program Studi</label>
                <select name="program_studi_id" class="form-select">
                    <option value="">Semua Program Studi</option>
                    @foreach($prodis as $p)
                        <option value="{{ $p->id }}" {{ $prodiId == $p->id ? 'selected' : '' }}>
                            {{ $p->nama_prodi }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-filter"></i> Filter Laporan
                </button>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-outline-success w-100" onclick="window.print()">
                    <i class="bi bi-printer"></i> Cetak
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4">Dosen</th>
                        <th>Jadwal / MK</th>
                        <th>Jam Masuk</th>
                        <th>Jam Keluar</th>
                        <th>Status</th>
                        <th class="pe-4">Lokasi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($presensis as $p)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold">{{ $p->dosen->nama_lengkap }}</div>
                            <small class="text-muted">{{ $p->dosen->programStudi->nama_prodi ?? '-' }}</small>
                        </td>
                        <td>
                            <div class="small fw-bold">{{ $p->jadwalMengajar->mataKuliah->nama_mk ?? '-' }}</div>
                            <small class="text-muted">Ruang: {{ $p->jadwalMengajar->ruangan->kode_ruangan ?? '-' }}</small>
                        </td>
                        <td>{{ $p->jam_masuk ? substr($p->jam_masuk, 0, 5) : '-' }}</td>
                        <td>{{ $p->jam_keluar ? substr($p->jam_keluar, 0, 5) : '-' }}</td>
                        <td>
                            @php
                                $badgeClass = match($p->status) {
                                    'hadir' => 'bg-success',
                                    'terlambat' => 'bg-warning',
                                    'izin', 'sakit', 'cuti' => 'bg-info',
                                    default => 'bg-danger'
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }} rounded-pill px-3">{{ ucfirst($p->status) }}</span>
                        </td>
                        <td class="pe-4">
                            @if($p->latitude_masuk)
                                <a href="https://www.google.com/maps?q={{ $p->latitude_masuk }},{{ $p->longitude_masuk }}" target="_blank" class="text-decoration-none small">
                                    <i class="bi bi-geo-alt-fill text-danger"></i> Lihat Map
                                </a>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            Tidak ada data presensi untuk tanggal dan filter ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    @media print {
        #sidebar, .navbar, form, .btn-primary, .btn-outline-success {
            display: none !important;
        }
        #content {
            margin-left: 0 !important;
            padding: 0 !important;
        }
        .card {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }
    }
</style>
@endpush
