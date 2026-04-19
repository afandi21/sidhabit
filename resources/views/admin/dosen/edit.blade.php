@extends('layouts.app')

@section('title', 'Edit Dosen')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.dosen.index') }}" class="text-decoration-none small">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Dosen
    </a>
    <h4 class="mt-2 fw-bold">Edit Data Dosen</h4>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('admin.dosen.update', $dosen->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <h6 class="fw-bold mb-3 text-primary">Informasi Akun</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Email Login</label>
                            <input type="email" name="email" class="form-control" value="{{ $dosen->user->email }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Password Baru (Kosongkan jika tidak ganti)</label>
                            <input type="password" name="password" class="form-control" placeholder="min. 8 karakter">
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3 text-primary">Biodata Dosen</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Gelar Depan</label>
                            <input type="text" name="gelar_depan" class="form-control" value="{{ $dosen->gelar_depan }}">
                        </div>
                        <div class="col-md-7">
                            <label class="form-label small fw-bold">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control" value="{{ $dosen->nama_lengkap }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Gelar Belakang</label>
                            <input type="text" name="gelar_belakang" class="form-control" value="{{ $dosen->gelar_belakang }}">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">NIDN</label>
                            <input type="text" name="nidn" class="form-control" value="{{ $dosen->nidn }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">NUPTK</label>
                            <input type="text" name="nuptk" class="form-control" value="{{ $dosen->nuptk }}">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select" required>
                                <option value="L" {{ $dosen->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ $dosen->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control" value="{{ $dosen->tanggal_lahir ? $dosen->tanggal_lahir->format('Y-m-d') : '' }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Program Studi</label>
                            <select name="program_studi_id" class="form-select" required>
                                @foreach($prodis as $p)
                                    <option value="{{ $p->id }}" {{ $dosen->program_studi_id == $p->id ? 'selected' : '' }}>
                                        [{{ $p->fakultas->kode_fakultas }} - {{ $p->kode_prodi }}] {{ $p->nama_prodi }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">No. HP</label>
                            <input type="text" name="no_hp" class="form-control" value="{{ $dosen->no_hp }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Status Aktif</label>
                            <select name="status_aktif" class="form-select">
                                <option value="aktif" {{ $dosen->status_aktif == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ $dosen->status_aktif == 'nonaktif' ? 'selected' : '' }}>Non-Aktif</option>
                                <option value="cuti" {{ $dosen->status_aktif == 'cuti' ? 'selected' : '' }}>Cuti</option>
                                <option value="tugas_belajar" {{ $dosen->status_aktif == 'tugas_belajar' ? 'selected' : '' }}>Tugas Belajar</option>
                            </select>
                        </div>
                    </div>

                    <hr>
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.dosen.index') }}" class="btn btn-light px-4">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">Update Data Dosen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
