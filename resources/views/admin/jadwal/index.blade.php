@extends('layouts.app')

@section('title', 'Manajemen Jadwal')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Jadwal Mengajar Dosen</h4>
    <div>
        <a href="{{ route('admin.jadwal.board') }}" class="btn btn-dark me-2">
            <i class="bi bi-grid-3x3-gap"></i> Interactive Board
        </a>
        <form action="{{ route('admin.jadwal.generate') }}" method="POST" class="d-inline" onsubmit="return confirm('Sistem akan men-generate jadwal secara otomatis untuk beban tersisa. Lanjutkan?')">
            @csrf
            <button type="submit" class="btn btn-warning me-2 fw-bold text-dark">
                <i class="bi bi-magic"></i> Auto Generate
            </button>
        </form>
        <a href="{{ route('admin.jadwal.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Tambah Manual
        </a>
    </div>
</div>

<div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-4">
    <i class="bi bi-info-circle-fill me-2 fs-4"></i>
    <div>
        Menampilkan jadwal untuk <strong>Semester Aktif</strong>. Pastikan data dosen dan mata kuliah sudah terisi.
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4 py-3">Hari & Waktu</th>
                        <th class="py-3">Mata Kuliah</th>
                        <th class="py-3">Dosen Pengampu</th>
                        <th class="py-3">Ruangan / Kelas</th>
                        <th class="py-3 text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                        $currentDay = '';
                    @endphp
                    @forelse($jadwals as $j)
                        @if($currentDay != $j->hari->nama_hari)
                            <tr class="table-light">
                                <td colspan="5" class="ps-4 fw-bold py-2 text-primary small text-uppercase">
                                    <i class="bi bi-calendar3 me-1"></i> {{ $j->hari->nama_hari }}
                                </td>
                            </tr>
                            @php $currentDay = $j->hari->nama_hari; @endphp
                        @endif
                        <tr>
                            <td class="ps-4">
                                <span class="badge bg-white text-dark border fw-normal py-2 px-3">
                                    <i class="bi bi-clock me-1 text-primary"></i> 
                                    {{ substr($j->jam_mulai, 0, 5) }} - {{ substr($j->jam_selesai, 0, 5) }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold">
                                    <span class="badge bg-secondary me-1">{{ $j->mataKuliah->programStudi->kode_prodi ?? '-' }}</span> 
                                    {{ $j->mataKuliah->nama_mk }}
                                </div>
                                <small class="text-muted">{{ $j->mataKuliah->kode_mk }} ({{ $j->mataKuliah->sks }} SKS)</small>
                            </td>
                            <td>{{ $j->dosen->nama_gelar }}</td>
                            <td>
                                <span class="badge bg-light text-dark fw-bold border">{{ $j->ruangan->kode_ruangan ?? '-' }}</span>
                                <span class="ms-2 small text-muted">Kelas: {{ $j->kelas ?? '-' }}</span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <a href="{{ route('admin.jadwal.edit', $j->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete({{ $j->id }})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                <form id="delete-form-{{ $j->id }}" action="{{ route('admin.jadwal.destroy', $j->id) }}" method="POST" class="d-none">
                                    @csrf @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-calendar-x fs-1 d-block mb-2 opacity-25"></i>
                                Belum ada jadwal yang terdaftar untuk semester ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmDelete(id) {
        if (confirm('Apakah Anda yakin ingin menghapus jadwal ini?')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>
@endpush
@endsection
