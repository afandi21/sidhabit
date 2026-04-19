@extends('layouts.app')

@section('title', 'Data Ruangan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Daftar Ruangan</h4>
    <a href="{{ route('admin.ruangan.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg"></i> Tambah Ruangan
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4 py-3">Kode</th>
                        <th class="py-3">Nama Ruangan</th>
                        <th class="py-3">Gedung / Lantai</th>
                        <th class="py-3">Lokasi Kampus</th>
                        <th class="py-3 text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ruangans as $r)
                    <tr>
                        <td class="ps-4 fw-bold text-danger">{{ $r->kode_ruangan }}</td>
                        <td>{{ $r->nama_ruangan }}</td>
                        <td><small>{{ $r->gedung ?? '-' }} / Lantai {{ $r->lantai ?? '-' }}</small></td>
                        <td><span class="badge bg-light text-dark">{{ $r->lokasiKampus->nama_lokasi ?? '-' }}</span></td>
                        <td class="text-end pe-4">
                            <div class="btn-group">
                                <a href="{{ route('admin.ruangan.edit', $r->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.ruangan.destroy', $r->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus ruangan ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-4">Belum ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
