@extends('layouts.app')

@section('title', 'Tambah Beban Mengajar')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.beban.index') }}" class="text-decoration-none small"><i class="bi bi-arrow-left"></i> Kembali ke Daftar Plotting</a>
    <h4 class="mt-2 fw-bold">Tambah Plotting Baru</h4>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('admin.beban.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Semester</label>
                        <select name="semester_id" class="form-select border-info" required>
                            @foreach($semesters as $s)
                                <option value="{{ $s->id }}">{{ $s->nama_semester }} ({{ $s->kode_semester }})</option>
                            @endforeach
                        </select>
                        <div class="form-text text-muted small">Plotting ini berlaku untuk semester yang dipilih.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Dosen Pengampu</label>
                        <select name="dosen_id" class="form-select" required>
                            <option value="">-- Pilih Dosen --</option>
                            @foreach($dosens as $d)
                                <option value="{{ $d->id }}">{{ $d->nama_gelar }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Mata Kuliah</label>
                            <select name="mata_kuliah_id" class="form-select" required>
                                <option value="">-- Pilih Mata Kuliah --</option>
                                @foreach($matkuls as $m)
                                    <option value="{{ $m->id }}">[{{ $m->kode_mk }}] {{ $m->nama_mk }} ({{ $m->sks }} SKS)</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Kelas</label>
                            <input type="text" name="kelas" class="form-control" placeholder="Contoh: TI-A" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary px-4 w-100">Simpan Plotting</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 bg-light">
            <div class="card-body p-4">
                <h6 class="fw-bold"><i class="bi bi-info-circle me-2"></i>Informasi Sistem SKS</h6>
                <p class="text-muted small">
                    Dengan mendaftarkan Mata Kuliah di sini, Anda memberikan tugas kepada sistem. Jika Anda mendaftarkan MK dengan 3 SKS, mesin akan mencarikan <strong>3 Sesi</strong> waktu luang untuk Dosen yang dipilih secara otomatis saat Anda menekan tombol "Generate Jadwal".
                </p>
                <hr>
                <ul class="text-muted small ps-3">
                    <li>Sistem tidak akan mencarikan jadwal pada hari di mana dosen ditandai "TIDAK Bersedia" di menu Ketersediaan Dosen.</li>
                    <li>Sistem dapat memecah SKS di hari yang berbeda sesuai aturan batas maksimal sesi harian (4 sesi di hari Kamis, 6-8 sesi di hari lain).</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
