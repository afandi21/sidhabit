@extends('layouts.app')

@section('title', 'Edit Sesi Kuliah')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.sesikuliah.index') }}" class="text-decoration-none small"><i class="bi bi-arrow-left"></i> Kembali</a>
    <h4 class="mt-2 fw-bold">Edit Sesi Kuliah</h4>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('admin.sesikuliah.update', $sesikuliah->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama/Nomor Sesi</label>
                        <input type="text" name="nama_sesi" class="form-control" value="{{ $sesikuliah->nama_sesi }}" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Jam Mulai</label>
                            <input type="time" name="jam_mulai" class="form-control" value="{{ substr($sesikuliah->jam_mulai, 0, 5) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Jam Selesai</label>
                            <input type="time" name="jam_selesai" class="form-control" value="{{ substr($sesikuliah->jam_selesai, 0, 5) }}" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold">Status</label>
                        <select name="is_active" class="form-select">
                            <option value="1" {{ $sesikuliah->is_active ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ !$sesikuliah->is_active ? 'selected' : '' }}>Non-aktif</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary px-4">Update Sesi</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
