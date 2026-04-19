@extends('layouts.app')

@section('title', 'Pusat Pengaturan')

@section('content')
<div class="row fade-enter">
    <div class="col-12 mb-4">
        <h4 class="fw-bold"><i class="bi bi-gear-wide-connected text-primary me-2"></i> Pusat Pengaturan & Pemeliharaan</h4>
        <p class="text-muted small">Kelola parameter inti sistem dan pantau kesehatan server SiDhabit.</p>
    </div>

    {{-- System Health --}}
    <div class="col-md-4 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold"><i class="bi bi-cpu me-2 text-info"></i> Kesehatan Sistem</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted small">Laravel Version</span>
                    <span class="badge bg-light text-dark fw-bold">{{ $health['laravel_version'] }}</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted small">PHP Version</span>
                    <span class="badge bg-light text-dark fw-bold">{{ $health['php_version'] }}</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted small">Disk Usage</span>
                    <span class="small fw-bold">{{ $health['disk_free'] }} / {{ $health['disk_total'] }}</span>
                </div>
                <div class="progress mb-4" style="height: 8px;">
                    <div class="progress-bar bg-success" style="width: 75%"></div>
                </div>
                
                <form action="{{ route('admin.pengaturan.backup') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary w-100 btn-sm">
                        <i class="bi bi-hdd-network me-1"></i> Jalankan Pembersihan Cache
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Maintenance Mode --}}
    <div class="col-md-4 mb-4">
        <div class="card border-0 shadow-sm h-100 {{ $health['is_maintenance'] ? 'bg-warning bg-opacity-10 border border-warning' : '' }}">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold"><i class="bi bi-tools me-2 text-danger"></i> Mode Pemeliharaan</h6>
            </div>
            <div class="card-body text-center">
                <div class="mb-4">
                    <i class="bi bi-shield-lock text-{{ $health['is_maintenance'] ? 'danger' : 'success' }}" style="font-size: 3rem;"></i>
                </div>
                <p class="small text-muted mb-4">
                    Saat aktif, hanya admin dengan kunci rahasia yang bisa mengakses website.
                </p>
                <form action="{{ route('admin.pengaturan.maintenance') }}" method="POST" onsubmit="return confirm('Anda yakin ingin mengubah status pemeliharaan?')">
                    @csrf
                    <button type="submit" class="btn btn-{{ $health['is_maintenance'] ? 'success' : 'danger' }} w-100">
                        <i class="bi bi-power me-1"></i> {{ $health['is_maintenance'] ? 'Aktifkan Sistem (Go Online)' : 'Matikan Sistem (Maintenance)' }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Backup Database --}}
    <div class="col-md-4 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold"><i class="bi bi-database-down me-2 text-primary"></i> Backup & Restore</h6>
            </div>
            <div class="card-body text-center">
                <div class="mb-4">
                    <i class="bi bi-cloud-arrow-down text-primary" style="font-size: 3rem;"></i>
                </div>
                <p class="small text-muted mb-4">Cadangkan seluruh basis data presensi untuk menghindari kehilangan data.</p>
                <button class="btn btn-primary w-100 mb-2" onclick="alert('Fitur Backup Database sedang diproses oleh sistem...')">
                    <i class="bi bi-download me-1"></i> Backup Database (.sql)
                </button>
                <button class="btn btn-light w-100 btn-sm" disabled>
                    <i class="bi bi-upload me-1"></i> Restore Data
                </button>
            </div>
        </div>
    </div>

    {{-- Academic Period Settings --}}
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 15px;">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-calendar-check me-2 text-warning"></i> Periode Akademik Aktif</h6>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-3 me-2" data-bs-toggle="modal" data-bs-target="#addSemesterModal">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Periode
                    </button>
                    @if($activeSemester)
                    <span class="badge bg-success rounded-pill px-3">
                        Aktif: {{ $activeSemester->nama_semester }}
                    </span>
                    @endif
                </div>
            </div>
            <div class="card-body p-4 bg-light bg-opacity-50">
                <form action="{{ route('admin.pengaturan.semester') }}" method="POST" class="row g-3 align-items-end">
                    @csrf
                    <div class="col-md-8">
                        <label class="form-label small fw-bold text-muted">Pilih Semester & Tahun Akademik</label>
                        <select name="semester_id" class="form-select border-0 shadow-sm py-2">
                            @foreach($semesters as $s)
                                <option value="{{ $s->id }}" {{ $s->is_active ? 'selected' : '' }}>
                                    {{ $s->nama_semester }} ({{ $s->kode_semester }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-warning w-100 py-2 fw-bold">
                            <i class="bi bi-check-circle me-1"></i> Terapkan Secara Global
                        </button>
                    </div>
                    <div class="col-12">
                        <p class="mb-0 small text-muted">
                            <i class="bi bi-info-circle me-1"></i> 
                            <strong>Catatan:</strong> Saat diterapkan, modul Mata Kuliah, Jadwal, dan Laporan akan otomatis terfilter sesuai pilihan ini.
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Location Settings --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold"><i class="bi bi-geo-alt me-2 text-success"></i> Pengaturan Titik Lokasi Kampus</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.pengaturan.location') }}" method="POST">
                    @csrf
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Latitude</label>
                            <input type="text" name="latitude" class="form-control" value="{{ $lokasi->latitude ?? '' }}" placeholder="Contoh: 3.531231">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Longitude</label>
                            <input type="text" name="longitude" class="form-control" value="{{ $lokasi->longitude ?? '' }}" placeholder="Contoh: 98.760735">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Radius Kehadiran (Meter)</label>
                            <div class="input-group">
                                <input type="number" name="radius_meter" class="form-control" value="{{ $lokasi->radius_meter ?? 50 }}">
                                <span class="input-group-text">Meter</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="alert alert-info border-0 mb-4 small">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                Gunakan <strong>Google Maps</strong> untuk mendapatkan koordinat presisi titik tengah kampus. Radius standar adalah 50-100 meter.
                            </div>
                            <button type="submit" class="btn btn-success px-4">
                                <i class="bi bi-check-circle me-2"></i> Simpan Pengaturan Lokasi
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah Semester --}}
<div class="modal fade" id="addSemesterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <form action="{{ route('admin.pengaturan.semester.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Tambah Periode Akademik</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Kode Semester</label>
                        <input type="text" name="kode_semester" class="form-control" placeholder="Contoh: 20261" required>
                        <small class="text-muted">Kode unik (Tahun+1 untuk Ganjil, Tahun+2 untuk Genap).</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Nama Semester</label>
                        <input type="text" name="nama_semester" class="form-control" placeholder="Contoh: Ganjil 2026/2027" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold text-white">Simpan Periode</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
