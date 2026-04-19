@extends('layouts.app')

@section('title', 'Tambah Ruangan')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.ruangan.index') }}" class="text-decoration-none small"><i class="bi bi-arrow-left"></i> Kembali</a>
    <h4 class="mt-2 fw-bold">Tambah Ruangan</h4>
</div>

<div class="row">
    <div class="col-md-7">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('admin.ruangan.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Lokasi Kampus</label>
                        <select name="lokasi_kampus_id" class="form-select" required>
                            <option value="">Pilih Kampus</option>
                            @foreach($lokasis as $l)
                                <option value="{{ $l->id }}">{{ $l->nama_lokasi }}</label>
                            @endforeach
                        </select>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Kode Ruangan</label>
                            <input type="text" name="kode_ruangan" class="form-control" placeholder="Contoh: R101" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Nama Ruangan</label>
                            <input type="text" name="nama_ruangan" class="form-control" placeholder="Contoh: Ruang Teori 1" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Gedung</label>
                            <input type="text" name="gedung" class="form-control" placeholder="Gedung A">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Lantai</label>
                            <input type="number" name="lantai" class="form-control" placeholder="1">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary px-4">Simpan Ruangan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
