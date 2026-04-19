@extends('layouts.app')

@section('title', 'Tambah Mata Kuliah')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.matakuliah.index') }}" class="text-decoration-none small"><i class="bi bi-arrow-left"></i> Kembali</a>
    <h4 class="mt-2 fw-bold">Tambah Mata Kuliah</h4>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('admin.matakuliah.store') }}" method="POST">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Program Studi</label>
                            <select name="program_studi_id" class="form-select" required>
                                <option value="">Pilih Prodi</option>
                                @foreach($prodis as $p)
                                    <option value="{{ $p->id }}">{{ $p->fakultas->kode_fakultas }} - {{ $p->kode_prodi }} | {{ $p->nama_prodi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Kode MK</label>
                            <input type="text" name="kode_mk" class="form-control" placeholder="Ex: MK001" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Mata Kuliah</label>
                        <input type="text" name="nama_mk" class="form-control" placeholder="Contoh: Pemrograman Mobile" required>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">SKS</label>
                            <input type="number" name="sks" class="form-control" min="1" max="8" value="2" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Semester</label>
                            <input type="number" name="semester" class="form-control" min="1" max="8" value="1" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Jenis</label>
                            <select name="jenis" class="form-select" required>
                                <option value="teori">Teori</option>
                                <option value="praktikum">Praktikum</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Kategori</label>
                            <select name="kategori" class="form-select" required>
                                <option value="dikti">DIKTI</option>
                                <option value="mahad">MA'HAD</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary px-4">Simpan Mata Kuliah</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
