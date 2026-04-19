@extends('layouts.app')

@section('title', 'Tambah Program Studi')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.prodi.index') }}" class="text-decoration-none small"><i class="bi bi-arrow-left"></i> Kembali</a>
    <h4 class="mt-2 fw-bold">Tambah Program Studi</h4>
</div>

<div class="row">
    <div class="col-md-7">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('admin.prodi.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Fakultas</label>
                        <select name="fakultas_id" class="form-select" required>
                            <option value="">Pilih Fakultas</option>
                            @foreach($fakultas as $f)
                                <option value="{{ $f->id }}" {{ (isset($selectedFakultasId) && $selectedFakultasId == $f->id) ? 'selected' : '' }}>
                                    {{ $f->nama_fakultas }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Kode Prodi</label>
                            <input type="text" name="kode_prodi" class="form-control" placeholder="Contoh: TI" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Nama Program Studi</label>
                            <input type="text" name="nama_prodi" class="form-control" placeholder="Contoh: Teknik Informatika" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold">Jenjang</label>
                        <select name="jenjang" class="form-select" required>
                            <option value="D3">D3</option>
                            <option value="S1" selected>S1</option>
                            <option value="S2">S2</option>
                            <option value="S3">S3</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary px-4">Simpan Prodi</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
