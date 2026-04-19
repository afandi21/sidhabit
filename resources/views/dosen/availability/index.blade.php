@extends('layouts.app')

@section('title', 'Ketersediaan Mengajar')

@section('content')
<div class="row fade-enter">
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm" style="border-radius: 15px; background: linear-gradient(135deg, #434343 0%, #000000 100%);">
            <div class="card-body p-4 text-white">
                <h4 class="fw-bold mb-1"><i class="bi bi-calendar-x me-2"></i> Jadwal Berhalangan</h4>
                <p class="mb-0 opacity-75">Ceklis sesi di mana Anda <strong>TIDAK BISA</strong> mengajar. Sistem tidak akan menempatkan jadwal pada sesi tersebut.</p>
            </div>
        </div>
    </div>

    <div class="col-12">
        <form action="{{ route('dosen.availability.store') }}" method="POST">
            @csrf
            <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 20px;">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0 text-center">
                            <thead class="bg-light">
                                <tr>
                                    <th class="py-3 px-4 text-start" style="min-width: 150px;">Sesi / Hari</th>
                                    @foreach($haris as $h)
                                    <th class="py-3">{{ $h->nama_hari }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sesis as $s)
                                <tr>
                                    <td class="text-start ps-4">
                                        <div class="fw-bold">{{ $s->nama_sesi }}</div>
                                        <small class="text-muted">{{ substr($s->jam_mulai, 0, 5) }} - {{ substr($s->jam_selesai, 0, 5) }}</small>
                                    </td>
                                    @foreach($haris as $h)
                                    @php
                                        // Jika ada datanya, berarti IS_BERSEDIA = false (Sesuai logika store kita)
                                        $isNotAvailable = isset($ketersediaan[$h->id][$s->id]);
                                    @endphp
                                    <td class="p-0">
                                        <label class="d-block py-4 cursor-pointer w-100 h-100" style="background: {{ $isNotAvailable ? 'rgba(220, 53, 69, 0.05)' : 'transparent' }}">
                                            <input type="checkbox" 
                                                   name="availability[{{ $h->id }}][{{ $s->id }}]" 
                                                   value="0" 
                                                   class="form-check-input"
                                                   {{ $isNotAvailable ? 'checked' : '' }}>
                                            <div class="small mt-1 {{ $isNotAvailable ? 'text-danger fw-bold' : 'text-muted opacity-50' }}">
                                                {{ $isNotAvailable ? 'Berhalangan' : 'Bisa' }}
                                            </div>
                                        </label>
                                    </td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white py-3 text-end px-4">
                    <button type="submit" class="btn btn-dark rounded-pill px-5 shadow-sm">
                        <i class="bi bi-cloud-check me-2"></i> Simpan Ketersediaan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    .cursor-pointer { cursor: pointer; }
    .form-check-input:checked + .small { color: #dc3545 !important; font-weight: bold; }
</style>
@endsection
