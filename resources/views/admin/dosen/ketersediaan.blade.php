@extends('layouts.app')

@section('title', 'Ketersediaan Mengajar')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.dosen.index') }}" class="text-decoration-none small"><i class="bi bi-arrow-left"></i> Kembali</a>
    <h4 class="mt-2 fw-bold">Atur Ketersediaan Dosen</h4>
    <p class="text-muted">Dosen: <strong>{{ $dosen->nama_gelar }}</strong></p>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="alert alert-info border-0 py-2 small">
                    <i class="bi bi-info-circle me-1"></i> Centang hari di mana dosen tersebut <strong>bersedia</strong> untuk mengajar.
                </div>
                
                <form action="{{ route('admin.dosen.ketersediaan.update', $dosen->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Hari Operasional Kampus</label>
                        <div class="list-group">
                            @foreach($haris as $hari)
                                @php
                                    // Default bersedia (true) jika belum di-setting sama sekali
                                    $isBersedia = isset($ketersediaan[$hari->id]) ? $ketersediaan[$hari->id] : true;
                                @endphp
                                <label class="list-group-item d-flex gap-3 bg-light bg-opacity-50 border-0 mb-2 rounded">
                                    <input class="form-check-input flex-shrink-0 fs-5" type="checkbox" name="hari_id[]" value="{{ $hari->id }}" {{ $isBersedia ? 'checked' : '' }}>
                                    <span class="pt-1 form-checked-content">
                                        <strong>Hari {{ $hari->nama_hari }}</strong>
                                        <small class="d-block text-muted">
                                            {{ $isBersedia ? 'Bersedia menerima jadwal kelas' : 'TIDAK bersedia (Libur/Izin)' }}
                                        </small>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary px-4 w-100">Simpan Pengaturan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
