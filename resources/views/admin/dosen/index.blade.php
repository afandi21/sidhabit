@extends('layouts.app')

@section('title', 'Manajemen Dosen')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Data Dosen</h4>
    <div class="btn-group">
        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="bi bi-file-earmark-excel"></i> Import Excel
        </button>
        <a href="{{ route('admin.dosen.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Tambah Dosen
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4 py-3">Nama Dosen</th>
                        <th class="py-3">NIDN / NUPTK</th>
                        <th class="py-3">Program Studi</th>
                        <th class="py-3">Status</th>
                        <th class="py-3 text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dosens as $d)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark">{{ $d->nama_gelar }}</div>
                            <small class="text-muted">{{ $d->user->email }}</small>
                        </td>
                        <td>
                            <div class="small">NIDN: {{ $d->nidn ?? '-' }}</div>
                            <div class="small text-muted">NUPTK: {{ $d->nuptk ?? '-' }}</div>
                        </td>
                        <td>
                            <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-10">
                                {{ $d->programStudi->nama_prodi ?? 'N/A' }}
                            </span>
                        </td>
                        <td>
                            @if($d->status_aktif == 'aktif')
                                <span class="badge bg-success rounded-pill px-3">Aktif</span>
                            @else
                                <span class="badge bg-secondary rounded-pill px-3">{{ ucfirst($d->status_aktif) }}</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group">
                                <a href="{{ route('admin.dosen.impersonate', $d->id) }}" class="btn btn-sm btn-outline-warning" title="Login Sebagai Dosen ini">
                                    <i class="bi bi-person-workspace"></i>
                                </a>
                                <a href="{{ route('admin.dosen.ketersediaan', $d->id) }}" class="btn btn-sm btn-outline-info" title="Atur Ketersediaan/Libur">
                                    <i class="bi bi-calendar-check"></i>
                                </a>
                                <a href="{{ route('admin.dosen.edit', $d->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete({{ $d->id }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            <form id="delete-form-{{ $d->id }}" action="{{ route('admin.dosen.destroy', $d->id) }}" method="POST" class="d-none">
                                @csrf @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-1 d-block mb-2 opacity-25"></i>
                            Belum ada data dosen.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Import --}}
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <form action="{{ route('admin.dosen.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Import Data Dosen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="text-center mb-4">
                        <i class="bi bi-file-earmark-arrow-up text-primary" style="font-size: 3rem;"></i>
                        <p class="mt-2 text-muted small">Pilih file Excel (.xlsx) atau CSV yang sesuai dengan template.</p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">File Excel/CSV</label>
                        <input type="file" name="file_excel" class="form-control" accept=".xlsx, .xls, .csv" required>
                    </div>

                    <div class="bg-light p-3 rounded-3">
                        <h6 class="small fw-bold mb-2"><i class="bi bi-info-circle me-1"></i> Instruksi:</h6>
                        <ul class="small text-muted mb-0 ps-3">
                            <li>Download template melalui link di bawah.</li>
                            <li>Isi data sesuai kolom yang tersedia (Wajib isi <strong>Tanggal Lahir</strong>).</li>
                            <li>Kolom <strong>prodi</strong> harus sesuai dengan nama prodi di sistem (PBA, MPI, dll).</li>
                            <li>Password default adalah <strong>Tanggal Lahir (DDMMYYYY)</strong>. Contoh: 02121995.</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 d-flex justify-content-between">
                    <a href="{{ route('admin.dosen.template') }}" class="btn btn-link text-decoration-none small">
                        <i class="bi bi-download me-1"></i> Download Template
                    </a>
                    <div>
                        <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Upload & Import</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmDelete(id) {
        if (confirm('Apakah Anda yakin ingin menghapus data dosen ini? Pengguna terkait juga akan dihapus.')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>
@endpush
@endsection
