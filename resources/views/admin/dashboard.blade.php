@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row g-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card h-100 p-3">
            <div class="d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary me-3">
                    <i class="bi bi-person-check fs-3"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold">{{ $stats['hadir'] }}</h5>
                    <small class="text-muted">Dosen Hadir</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card h-100 p-3">
            <div class="d-flex align-items-center">
                <div class="bg-warning bg-opacity-10 p-3 rounded-circle text-warning me-3">
                    <i class="bi bi-clock-history fs-3"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold">{{ $stats['terlambat'] }}</h5>
                    <small class="text-muted">Terlambat</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card h-100 p-3">
            <div class="d-flex align-items-center">
                <div class="bg-info bg-opacity-10 p-3 rounded-circle text-info me-3">
                    <i class="bi bi-envelope-paper fs-3"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold">{{ $stats['izin'] }}</h5>
                    <small class="text-muted">Izin/Sakit</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card h-100 p-3">
            <div class="d-flex align-items-center">
                <div class="bg-danger bg-opacity-10 p-3 rounded-circle text-danger me-3">
                    <i class="bi bi-person-x fs-3"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold">{{ $stats['alfa'] }}</h5>
                    <small class="text-muted">Tanpa Keterangan</small>
                </div>
            </div>
        </div>
    </div>
    <!-- Infografis Chart -->
    <div class="col-12 mt-4">
        <div class="card border-0 shadow-sm" style="border-radius: 15px;">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold"><i class="bi bi-bar-chart-line me-2 text-primary"></i> Tren Kehadiran (7 Hari Terakhir)</h6>
            </div>
            <div class="card-body">
                <canvas id="presenceChart" height="80"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-8 mt-4">
        <div class="card">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold">Presensi Terkini</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3 border-0">Dosen</th>
                            <th class="border-0">Waktu</th>
                            <th class="border-0">Status</th>
                            <th class="border-0">Ruangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($presensiTerbaru as $p)
                        <tr>
                            <td class="ps-3">
                                <div>{{ $p->dosen->nama_gelar }}</div>
                                <small class="text-muted">{{ $p->jadwalMengajar->mataKuliah->nama_mk }}</small>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($p->jam_masuk)->format('H:i') }}</td>
                            <td>
                                <span class="badge {{ $p->status == 'hadir' ? 'bg-success' : 'bg-warning' }} rounded-pill">
                                    {{ ucfirst($p->status) }}
                                </span>
                            </td>
                            <td>{{ $p->jadwalMengajar->ruangan->kode_ruangan ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">Belum ada data presensi hari ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold text-danger">Belum Hadir</h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($dosenBelumHadir as $d)
                    <li class="list-group-item d-flex align-items-center py-3">
                        <div class="bg-light rounded p-2 me-3">
                            <i class="bi bi-person text-secondary"></i>
                        </div>
                        <div>
                            <div class="small fw-bold">{{ $d->nama_gelar }}</div>
                            <small class="text-muted">{{ $d->nip ?? 'NIP -' }}</small>
                        </div>
                    </li>
                    @empty
                    <li class="list-group-item text-center py-4 text-muted small">Semua dosen sudah presensi.</li>
                    @endforelse
                </ul>
            </div>
            <div class="card-footer bg-white border-0 text-center py-3">
                <a href="#" class="small text-decoration-none">Lihat Semua</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('presenceChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartData['labels']) !!},
            datasets: [{
                label: 'Jumlah Kehadiran',
                data: {!! json_encode($chartData['data']) !!},
                backgroundColor: 'rgba(12, 96, 70, 0.7)',
                borderColor: '#0c6046',
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
</script>
@endpush
