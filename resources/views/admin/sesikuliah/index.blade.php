@extends('layouts.app')

@section('title', 'Manajemen Sesi Kuliah')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Daftar Sesi Kuliah</h4>
    <a href="{{ route('admin.sesikuliah.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg"></i> Tambah Sesi
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4 py-3">Nama Sesi</th>
                        <th class="py-3">Jam Mulai</th>
                        <th class="py-3">Jam Selesai</th>
                        <th class="py-3">Status</th>
                        <th class="py-3 text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sesis as $s)
                    <tr>
                        <td class="ps-4 fw-bold">Sesi {{ $s->nama_sesi }}</td>
                        <td><span class="badge bg-light text-dark">{{ substr($s->jam_mulai, 0, 5) }}</span></td>
                        <td><span class="badge bg-light text-dark">{{ substr($s->jam_selesai, 0, 5) }}</span></td>
                        <td>
                            @if($s->is_active)
                                <span class="badge bg-success rounded-pill">Aktif</span>
                            @else
                                <span class="badge bg-secondary rounded-pill">Non-aktif</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group">
                                <a href="{{ route('admin.sesikuliah.edit', $s->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.sesikuliah.destroy', $s->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus sesi ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-4">Belum ada data sesi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
