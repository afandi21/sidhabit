@extends('layouts.app')

@section('title', 'Tambah Fakultas')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.fakultas.index') }}" class="text-decoration-none small"><i class="bi bi-arrow-left"></i> Kembali</a>
    <h4 class="mt-2 fw-bold">Tambah Fakultas</h4>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('admin.fakultas.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Kode Fakultas</label>
                        <input type="text" name="kode_fakultas" class="form-control @error('kode_fakultas') is-invalid @enderror" placeholder="Contoh: FTI" required>
                        @error('kode_fakultas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Fakultas</label>
                        <input type="text" name="nama_fakultas" class="form-control @error('nama_fakultas') is-invalid @enderror" placeholder="Contoh: Fakultas Teknologi Informasi" required>
                        @error('nama_fakultas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold">Nama Dekan</label>
                        <input type="text" name="dekan" class="form-control" placeholder="Contoh: Dr. Ahmad, M.Kom">
                    </div>
                    <button type="submit" class="btn btn-primary px-4">Simpan Fakultas</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
