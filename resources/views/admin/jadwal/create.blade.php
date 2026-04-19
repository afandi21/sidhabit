@extends('layouts.app')

@section('title', 'Tambah Jadwal')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.jadwal.index') }}" class="text-decoration-none small">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Jadwal
    </a>
    <h4 class="mt-2 fw-bold">Tambah Jadwal Mengajar</h4>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('admin.jadwal.store') }}" method="POST">
                    @csrf
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-12">
                            <label class="form-label small fw-bold">Dosen Pengampu</label>
                            <select name="dosen_id" class="form-select @error('dosen_id') is-invalid @enderror" required>
                                <option value="">Pilih Dosen</option>
                                @foreach($dosens as $d)
                                    <option value="{{ $d->id }}" {{ old('dosen_id') == $d->id ? 'selected' : '' }}>
                                        {{ $d->nama_gelar }}
                                    </option>
                                @endforeach
                            </select>
                            @error('dosen_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-9">
                            <label class="form-label small fw-bold">Mata Kuliah</label>
                            <select name="mata_kuliah_id" class="form-select @error('mata_kuliah_id') is-invalid @enderror" required>
                                <option value="">Pilih Mata Kuliah</option>
                                @foreach($matkuls as $m)
                                    <option value="{{ $m->id }}" {{ old('mata_kuliah_id') == $m->id ? 'selected' : '' }}>
                                        [{{ $m->kode_mk }}] {{ $m->nama_mk }} ({{ $m->sks }} SKS)
                                    </option>
                                @endforeach
                            </select>
                            @error('mata_kuliah_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Kelas</label>
                            <input type="text" name="kelas" class="form-control" value="{{ old('kelas') }}" placeholder="Ex: Regular A">
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Semester</label>
                            <select name="semester_id" class="form-select" required>
                                @foreach($semesters as $s)
                                    <option value="{{ $s->id }}" {{ old('semester_id') == $s->id ? 'selected' : '' }}>
                                        {{ $s->nama_semester }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Ruangan</label>
                            <select name="ruangan_id" class="form-select" required>
                                <option value="">Pilih Ruangan</option>
                                @foreach($ruangans as $r)
                                    <option value="{{ $r->id }}" {{ old('ruangan_id') == $r->id ? 'selected' : '' }}>
                                        {{ $r->kode_ruangan }} - {{ $r->nama_ruangan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3 text-primary border-bottom pb-2">Pengaturan Waktu</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Hari</label>
                            <select name="hari_id" class="form-select" required>
                                @foreach($haris as $h)
                                    <option value="{{ $h->id }}" {{ old('hari_id') == $h->id ? 'selected' : '' }}>
                                        {{ $h->nama_hari }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Sesi Mulai</label>
                            <select name="sesi_mulai_id" class="form-select" required>
                                <option value="">Pilih Sesi Mulai</option>
                                @foreach($sesis as $s)
                                    <option value="{{ $s->id }}">{{ $s->label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Sesi Selesai</label>
                            <select name="sesi_selesai_id" class="form-select" required>
                                <option value="">Pilih Sesi Selesai</option>
                                @foreach($sesis as $s)
                                    <option value="{{ $s->id }}">{{ $s->label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <hr>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="reset" class="btn btn-light px-4">Reset</button>
                        <button type="submit" class="btn btn-primary px-4">Simpan Jadwal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card border-0 bg-light mb-3">
            <div class="card-body">
                <h6 class="fw-bold"><i class="bi bi-shield-check me-1"></i> Validasi Sistem</h6>
                <p class="small text-muted">
                    Sistem akan secara otomatis mengecek apakah dosen memiliki jadwal lain yang bentrok pada jam dan hari yang sama sebelum menyimpan.
                </p>
            </div>
        </div>
        <div class="card border-0 bg-primary bg-opacity-10">
            <div class="card-body text-primary">
                <h6 class="fw-bold fw-600 mb-1">Informasi Semester</h6>
                <p class="small mb-0">Hanya menampilkan Semester yang berstatus <strong>Aktif</strong>.</p>
            </div>
        </div>
    </div>
</div>
@endsection
