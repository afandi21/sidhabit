@extends('layouts.app')

@section('title', 'Data Ruangan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Daftar Ruangan</h4>
    <div class="btn-group">
        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importRuanganModal">
            <i class="bi bi-file-earmark-excel"></i> Import Excel
        </button>
        <a href="{{ route('admin.ruangan.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Tambah Ruangan
        </a>
    </div>
</div>

{{-- Modal Import Ruangan --}}
<x-modal id="importRuanganModal" title="Import Ruangan" action="{{ route('admin.ruangan.import') }}" hasFile="true" submitText="Import Data">
    <div class="mb-3">
        <label class="form-label small fw-bold text-muted">File Excel/CSV</label>
        <input type="file" name="file_excel" class="form-control" accept=".xlsx, .xls, .csv" required>
    </div>

    <div class="bg-light p-3 rounded-3">
        <h6 class="small fw-bold mb-2"><i class="bi bi-info-circle me-1"></i> Aturan Kolom:</h6>
        <ul class="small text-muted mb-0 ps-3">
            <li><strong>kode_ruangan</strong>: Unik (misal: R301)</li>
            <li><strong>nama_ruangan</strong>: Nama jelas ruangan</li>
            <li><strong>gedung</strong>: Nama gedung (opsional)</li>
            <li><strong>lantai</strong>: Angka (opsional)</li>
        </ul>
    </div>

    <x-slot name="footerLeft">
        <a href="{{ route('admin.ruangan.template') }}" class="btn btn-link text-decoration-none small">
            <i class="bi bi-download me-1"></i> Download Template
        </a>
    </x-slot>
</x-modal>

<x-card>
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
                    <td><x-badge type="light">{{ $r->lokasiKampus->nama_lokasi ?? '-' }}</x-badge></td>
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
</x-card>
@endsection
