@extends('layouts.app')

@section('title', 'Edit Ruangan')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.ruangan.index') }}" class="text-decoration-none small"><i class="bi bi-arrow-left"></i> Kembali</a>
    <h4 class="mt-2 fw-bold">Edit Ruangan</h4>
</div>

<div class="row">
    <div class="col-md-7">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('admin.ruangan.update', $ruangan->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Lokasi Kampus</label>
                        <select name="lokasi_kampus_id" class="form-select" required>
                            @foreach($lokasis as $l)
                                <option value="{{ $l->id }}" {{ $ruangan->lokasi_kampus_id == $l->id ? 'selected' : '' }}>
                                    {{ $l->nama_lokasi }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Kode Ruangan</label>
                            <input type="text" name="kode_ruangan" class="form-control" value="{{ $ruangan->kode_ruangan }}" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Nama Ruangan</label>
                            <input type="text" name="nama_ruangan" class="form-control" value="{{ $ruangan->nama_ruangan }}" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Gedung</label>
                            <input type="text" name="gedung" class="form-control" value="{{ $ruangan->gedung }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Lantai</label>
                            <input type="number" name="lantai" class="form-control" value="{{ $ruangan->lantai }}">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary px-4">Update Ruangan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
