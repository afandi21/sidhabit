@extends('layouts.app')

@section('title', 'Data Program Studi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Daftar Program Studi</h4>
    <a href="{{ route('admin.prodi.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg"></i> Tambah Prodi
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4 py-3">Kode</th>
                        <th class="py-3">Nama Prodi</th>
                        <th class="py-3">Fakultas</th>
                        <th class="py-3">Jenjang</th>
                        <th class="py-3 text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prodis as $p)
                    <tr>
                        <td class="ps-4 fw-bold text-success">{{ $p->kode_prodi }}</td>
                        <td>{{ $p->nama_prodi }}</td>
                        <td><small class="text-muted">{{ $p->fakultas->nama_fakultas }}</small></td>
                        <td><span class="badge bg-info text-white">{{ $p->jenjang }}</span></td>
                        <td class="text-end pe-4">
                            <div class="btn-group">
                                <a href="{{ route('admin.prodi.edit', $p->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.prodi.destroy', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus prodi ini?')">
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
