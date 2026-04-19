@extends('layouts.app')

@section('title', 'Data Mata Kuliah')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Daftar Mata Kuliah</h4>
    <div class="btn-group">
        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importMKModal">
            <i class="bi bi-file-earmark-excel"></i> Import Excel
        </button>
        <a href="{{ route('admin.matakuliah.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Tambah MK
        </a>
    </div>
</div>

@if($activeSemester)
    @php
        $isGanjil = str_contains(strtolower($activeSemester->nama_semester), 'ganjil');
        $isGenap = str_contains(strtolower($activeSemester->nama_semester), 'genap');
        $jenisSemester = $isGanjil ? 'Ganjil' : ($isGenap ? 'Genap' : '');
    @endphp
    <div class="alert alert-info border-0 shadow-sm mb-4 d-flex align-items-center justify-content-between">
        <div>
            <i class="bi bi-info-circle-fill me-2"></i>
            Data yang tampil saat ini hanya <strong>Mata Kuliah Semester {{ $jenisSemester }}</strong> menyesuaikan dengan Periode Akademik Aktif (<strong>{{ $activeSemester->nama_semester }}</strong>). Jika Anda menambahkan MK baru dan tidak muncul di sini, kemungkinan MK tersebut berada di semester yang berbeda.
        </div>
        @if(!request()->has('semua'))
            <a href="{{ route('admin.matakuliah.index', ['semua' => 1]) }}" class="btn btn-sm btn-light border ms-3 text-nowrap">Tampilkan Semua</a>
        @else
            <a href="{{ route('admin.matakuliah.index') }}" class="btn btn-sm btn-light border ms-3 text-nowrap">Tampilkan {{ $jenisSemester }} Saja</a>
        @endif
    </div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4 py-3">Kode MK</th>
                        <th class="py-3">Nama Mata Kuliah</th>
                        <th class="py-3">Prodi</th>
                        <th class="py-3 text-center">SMT</th>
                        <th class="py-3">SKS</th>
                        <th class="py-3 text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($matkuls as $m)
                    <tr>
                        <td class="ps-4 fw-bold">{{ $m->kode_mk }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $m->nama_mk }}</div>
                            <div class="d-flex gap-1 mt-1">
                                <span class="badge bg-light text-muted border small px-2" style="font-size: 0.65rem;">{{ str_replace('_', ' & ', $m->jenis) }}</span>
                                <span class="badge {{ $m->kategori == 'mahad' ? 'bg-warning text-dark' : 'bg-info text-white' }} small px-2 text-uppercase" style="font-size: 0.65rem;">{{ $m->kategori }}</span>
                            </div>
                        </td>
                        <td>{{ $m->programStudi->nama_prodi }}</td>
                        <td class="text-center">
                            <span class="badge {{ $m->semester % 2 == 0 ? 'bg-info' : 'bg-primary' }} bg-opacity-10 text-{{ $m->semester % 2 == 0 ? 'info' : 'primary' }}">
                                {{ $m->semester }}
                            </span>
                        </td>
                        <td><span class="badge bg-secondary">{{ $m->sks }} SKS</span></td>
                        <td class="text-end pe-4">
                            <div class="btn-group">
                                <a href="{{ route('admin.matakuliah.edit', $m->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.matakuliah.destroy', $m->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus mata kuliah ini?')">
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

{{-- Modal Import Mata Kuliah --}}
<div class="modal fade" id="importMKModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <form action="{{ route('admin.matakuliah.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Import Mata Kuliah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">File Excel/CSV</label>
                        <input type="file" name="file_excel" class="form-control" accept=".xlsx, .xls, .csv" required>
                    </div>

                    <div class="bg-light p-3 rounded-3">
                        <h6 class="small fw-bold mb-2"><i class="bi bi-info-circle me-1"></i> Aturan Kolom:</h6>
                        <ul class="small text-muted mb-0 ps-3">
                            <li><strong>jenis</strong>: teori / praktikum</li>
                            <li><strong>kategori</strong>: dikti / mahad</li>
                            <li><strong>prodi</strong>: Sesuai nama Prodi (PBA, MPI, dll)</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 d-flex justify-content-between">
                    <a href="{{ route('admin.matakuliah.template') }}" class="btn btn-link text-decoration-none small">
                        <i class="bi bi-download me-1"></i> Download Template
                    </a>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Import Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
