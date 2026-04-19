@extends('layouts.app')

@section('title', 'Tambah Sesi Kuliah')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.sesikuliah.index') }}" class="text-decoration-none small"><i class="bi bi-arrow-left"></i> Kembali</a>
    <h4 class="mt-2 fw-bold">Tambah Sesi Kuliah</h4>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('admin.sesikuliah.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama/Nomor Sesi</label>
                        <input type="text" name="nama_sesi" class="form-control" placeholder="Contoh: 1" required>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Jam Mulai</label>
                            <input type="time" name="jam_mulai" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Jam Selesai</label>
                            <input type="time" name="jam_selesai" class="form-control" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary px-4">Simpan Sesi</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
