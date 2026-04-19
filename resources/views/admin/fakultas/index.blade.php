@extends('layouts.app')

@section('title', 'Data Fakultas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Daftar Fakultas</h4>
    <a href="{{ route('admin.fakultas.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg"></i> Tambah Fakultas
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4 py-3">Kode</th>
                        <th class="py-3">Nama Fakultas</th>
                        <th class="py-3">Dekan</th>
                        <th class="py-3">Jumlah Prodi</th>
                        <th class="py-3 text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fakultas as $f)
                    <tr class="bg-light bg-opacity-50">
                        <td class="ps-4 fw-bold text-primary">{{ $f->kode_fakultas }}</td>
                        <td class="fw-bold">{{ $f->nama_fakultas }}</td>
                        <td>{{ $f->dekan ?? '-' }}</td>
                        <td><span class="badge bg-primary">{{ $f->program_studi_count }} Prodi</span></td>
                        <td class="text-end pe-4">
                            <div class="btn-group">
                                <a href="{{ route('admin.prodi.create', ['fakultas_id' => $f->id]) }}" class="btn btn-sm btn-outline-success" title="Tambah Prodi">
                                    <i class="bi bi-plus-circle"></i> Prodi
                                </a>
                                <a href="{{ route('admin.fakultas.edit', $f->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.fakultas.destroy', $f->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus fakultas ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @foreach($f->programStudi as $p)
                    <tr>
                        <td class="ps-5 text-muted small">—</td>
                        <td class="ps-3"><i class="bi bi-arrow-return-right text-muted me-2"></i> {{ $p->nama_prodi }}</td>
                        <td class="small text-muted">{{ $p->jenjang }}</td>
                        <td></td>
                        <td class="text-end pe-4">
                            <a href="{{ route('admin.prodi.edit', $p->id) }}" class="text-decoration-none text-primary small me-2" title="Edit Prodi">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <form action="{{ route('admin.prodi.destroy', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus Program Studi ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-link text-danger p-0 text-decoration-none small" title="Hapus Prodi">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                    @empty
                    <tr><td colspan="5" class="text-center py-4">Belum ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
