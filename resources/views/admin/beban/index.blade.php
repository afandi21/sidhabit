@extends('layouts.app')

@section('title', 'Beban Mengajar (Plotting)')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Plotting Beban Mengajar</h4>
    <div>
        <a href="{{ route('admin.beban.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Tambah Plotting
        </a>
    </div>
</div>

<div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-4">
    <i class="bi bi-info-circle-fill me-2 fs-4"></i>
    <div>
        Data di bawah ini merupakan daftar "Tugas Mengajar" dosen pada <strong>Semester Aktif</strong>. Data ini akan menjadi basis ("bahan baku") saat Sistem men-generate jadwal secara otomatis.
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4 py-3">Dosen Pengampu</th>
                        <th class="py-3">Mata Kuliah & Kelas</th>
                        <th class="py-3">Progress SKS</th>
                        <th class="py-3">Status Penjadwalan</th>
                        <th class="py-3 text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bebans as $b)
                    <tr>
                        <td class="ps-4 fw-bold text-primary">{{ $b->dosen->nama_gelar }}</td>
                        <td>
                            <div class="fw-bold">
                                <span class="badge bg-secondary me-1">{{ $b->mataKuliah->programStudi->kode_prodi ?? '-' }}</span> 
                                {{ $b->mataKuliah->nama_mk }}
                            </div>
                            <small class="text-muted">Kelas: {{ $b->kelas }} | SKS: {{ $b->total_sks }}</small>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold">{{ $b->sks_terjadwal }}</span>
                                <span class="text-muted">/ {{ $b->total_sks }} SKS</span>
                            </div>
                            <div class="progress mt-1" style="height: 6px;">
                                @php
                                    $percent = ($b->sks_terjadwal / $b->total_sks) * 100;
                                    $color = $percent == 100 ? 'bg-success' : ($percent > 0 ? 'bg-warning' : 'bg-secondary');
                                @endphp
                                <div class="progress-bar {{ $color }}" style="width: {{ $percent }}%"></div>
                            </div>
                        </td>
                        <td>
                            @if($b->is_selesai)
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1"><i class="bi bi-check-circle"></i> Selesai Terjadwal</span>
                            @elseif($b->sks_terjadwal > 0)
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1"><i class="bi bi-clock-history"></i> Sebagian Terjadwal</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2 py-1"><i class="bi bi-hourglass"></i> Belum Terjadwal</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <form action="{{ route('admin.beban.destroy', $b->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus plotting ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Hapus Plotting" {{ $b->sks_terjadwal > 0 ? 'disabled' : '' }}>
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-5 text-muted">Belum ada plotting beban mengajar untuk semester ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
