@extends('layouts.app')

@section('title', 'Tambah Dosen')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.dosen.index') }}" class="text-decoration-none small">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Dosen
    </a>
    <h4 class="mt-2 fw-bold">Tambah Dosen Baru</h4>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('admin.dosen.store') }}" method="POST">
                    @csrf
                    
                    <h6 class="fw-bold mb-3 text-primary">Informasi Akun</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Email Login</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="dosen@email.com" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Password Dasar</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="min. 8 karakter" required>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3 text-primary">Biodata Dosen</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Gelar Depan</label>
                            <input type="text" name="gelar_depan" class="form-control" value="{{ old('gelar_depan') }}" placeholder="Dr.">
                        </div>
                        <div class="col-md-7">
                            <label class="form-label small fw-bold">Nama Lengkap (Tanpa Gelar)</label>
                            <input type="text" name="nama_lengkap" class="form-control @error('nama_lengkap') is-invalid @enderror" value="{{ old('nama_lengkap') }}" placeholder="Contoh: Ahmad Fauzi" required>
                            @error('nama_lengkap') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Gelar Belakang</label>
                            <input type="text" name="gelar_belakang" class="form-control" value="{{ old('gelar_belakang') }}" placeholder="M.Kom">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">NIDN</label>
                            <input type="text" name="nidn" class="form-control @error('nidn') is-invalid @enderror" value="{{ old('nidn') }}">
                            @error('nidn') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">NUPTK</label>
                            <input type="text" name="nuptk" class="form-control @error('nuptk') is-invalid @enderror" value="{{ old('nuptk') }}">
                            @error('nuptk') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select" required>
                                <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control @error('tanggal_lahir') is-invalid @enderror" value="{{ old('tanggal_lahir') }}" required>
                            @error('tanggal_lahir') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Program Studi</label>
                            <select name="program_studi_id" class="form-select" required>
                                <option value="">Pilih Program Studi</option>
                                @foreach($prodis as $p)
                                    <option value="{{ $p->id }}" {{ old('program_studi_id') == $p->id ? 'selected' : '' }}>
                                        [{{ $p->fakultas->kode_fakultas }} - {{ $p->kode_prodi }}] {{ $p->nama_prodi }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">No. HP (WhatsApp)</label>
                        <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp') }}" placeholder="08123456789">
                    </div>

                    <hr>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="reset" class="btn btn-light px-4">Reset</button>
                        <button type="submit" class="btn btn-primary px-4">Simpan Data Dosen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card border-0 bg-light">
            <div class="card-body">
                <h6 class="fw-bold"><i class="bi bi-info-circle me-1"></i> Catatan Pembuatan Akun</h6>
                <p class="small text-muted mb-0">
                    Sistem akan otomatis membuat akun user untuk login dosen dengan email dan password yang Anda tentukan di atas.
                </p>
                <hr>
                <ul class="small text-muted ps-3">
                    <li>Gunakan email resmi institusi jika ada.</li>
                    <li>Password minimal 8 karakter.</li>
                    <li>Dosen dapat mendaftarkan fingerprint smartphone-nya sendiri setelah login pertama kali.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
