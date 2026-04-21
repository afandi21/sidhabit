@extends('layouts.app')

@section('title', 'Edit Jadwal Kuliah')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.jadwal.index') }}" class="text-decoration-none small"><i class="bi bi-arrow-left"></i> Kembali</a>
    <h4 class="mt-2 fw-bold">Edit Jadwal Kuliah</h4>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('admin.jadwal.update', $jadwal->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-12">
                            <label class="form-label small fw-bold">Dosen Pengampu</label>
                            <select name="dosen_id" class="form-select @error('dosen_id') is-invalid @enderror" required>
                                @foreach($dosens as $d)
                                    <option value="{{ $d->id }}" {{ $jadwal->dosen_id == $d->id ? 'selected' : '' }}>
                                        {{ $d->nama_lengkap }} ([{{ $d->programStudi->kode_prodi ?? '-' }}])
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Mata Kuliah</label>
                            <select name="mata_kuliah_id" class="form-select" required>
                                @foreach($matkuls as $m)
                                    <option value="{{ $m->id }}" {{ $jadwal->mata_kuliah_id == $m->id ? 'selected' : '' }}>
                                        [{{ $m->kode_mk }}] {{ $m->nama_mk }} ({{ $m->sks }} SKS)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Kelas</label>
                            <input type="text" name="kelas" class="form-control" value="{{ $jadwal->kelas }}" placeholder="Ex: A, B, atau TI-A">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Ruangan</label>
                            <select name="ruangan_id" class="form-select" required>
                                @foreach($ruangans as $r)
                                    <option value="{{ $r->id }}" {{ $jadwal->ruangan_id == $r->id ? 'selected' : '' }}>
                                        {{ $r->nama_ruangan }} ({{ $r->gedung }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Hari</label>
                            <select name="hari_id" class="form-select" required>
                                @foreach($haris as $h)
                                    <option value="{{ $h->id }}" {{ $jadwal->hari_id == $h->id ? 'selected' : '' }}>{{ $h->nama_hari }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Sesi Mulai</label>
                            <select name="sesi_mulai_id" class="form-select" required>
                                @foreach($sesis as $s)
                                    <option value="{{ $s->id }}" {{ $jadwal->sesi_mulai_id == $s->id ? 'selected' : '' }}>{{ $s->label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Sesi Selesai</label>
                            <select name="sesi_selesai_id" class="form-select" required>
                                @foreach($sesis as $s)
                                    <option value="{{ $s->id }}" {{ $jadwal->sesi_selesai_id == $s->id ? 'selected' : '' }}>{{ $s->label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">Semester</label>
                        <select name="semester_id" class="form-select" required>
                            @foreach($semesters as $s)
                                <option value="{{ $s->id }}" {{ $jadwal->semester_id == $s->id ? 'selected' : '' }}>
                                    {{ $s->nama_semester }} ({{ $s->kode_semester }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <hr>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-primary px-4">Update Jadwal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
