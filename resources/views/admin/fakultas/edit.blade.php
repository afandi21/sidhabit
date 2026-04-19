@extends('layouts.app')

@section('title', 'Edit Fakultas')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.fakultas.index') }}" class="text-decoration-none small"><i class="bi bi-arrow-left"></i> Kembali</a>
    <h4 class="mt-2 fw-bold">Edit Fakultas</h4>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('admin.fakultas.update', $fakultas->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Kode Fakultas</label>
                        <input type="text" name="kode_fakultas" class="form-control" value="{{ $fakultas->kode_fakultas }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Fakultas</label>
                        <input type="text" name="nama_fakultas" class="form-control" value="{{ $fakultas->nama_fakultas }}" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold">Nama Dekan</label>
                        <input type="text" name="dekan" class="form-control" value="{{ $fakultas->dekan }}">
                    </div>
                    <button type="submit" class="btn btn-primary px-4">Update Fakultas</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
