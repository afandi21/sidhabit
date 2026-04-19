@extends('layouts.app')

@section('title', 'Manajemen Izin & Cuti')

@section('content')
<div class="row fade-enter">
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm" style="border-radius: 20px; background: linear-gradient(135deg, #0c6046 0%, #1a8a66 100%);">
            <div class="card-body p-4 text-white">
                <h4 class="fw-bold mb-1">Daftar Pengajuan Izin & Cuti</h4>
                <p class="mb-0 opacity-75">Kelola dan verifikasi permohonan ketidakhadiran dosen di sini.</p>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light small text-muted text-uppercase">
                            <tr>
                                <th class="ps-4">Dosen</th>
                                <th>Jenis</th>
                                <th>Tanggal</th>
                                <th>Alasan</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($izins as $i)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">{{ $i->nama_lengkap }}</div>
                                    <small class="text-muted">Diajukan: {{ \Carbon\Carbon::parse($i->created_at)->isoFormat('D MMM Y') }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-10 px-3">
                                        {{ ucfirst($i->jenis) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="small fw-bold">{{ \Carbon\Carbon::parse($i->tanggal_mulai)->isoFormat('D MMM') }} - {{ \Carbon\Carbon::parse($i->tanggal_selesai)->isoFormat('D MMM Y') }}</div>
                                </td>
                                <td>
                                    <small class="text-muted d-block" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $i->alasan }}">
                                        {{ $i->alasan }}
                                    </small>
                                </td>
                                <td>
                                    @php
                                        $badgeClass = match($i->status_approval) {
                                            'approved' => 'bg-success',
                                            'rejected' => 'bg-danger',
                                            default => 'bg-warning'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }} rounded-pill px-3">{{ ucfirst($i->status_approval) }}</span>
                                </td>
                                <td class="text-end pe-4">
                                    @if($i->status_approval == 'pending')
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-success rounded-start-pill px-3" onclick="handleAction({{ $i->id }}, 'approve')">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger rounded-end-pill px-3" onclick="handleAction({{ $i->id }}, 'reject')">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>
                                    @else
                                    <span class="small text-muted italic">Sudah diproses</span>
                                    @endif
                                    
                                    @if($i->dokumen_pendukung)
                                    <a href="{{ asset('storage/'.$i->dokumen_pendukung) }}" target="_blank" class="btn btn-sm btn-outline-primary ms-2 rounded-circle" title="Lihat Dokumen">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </a>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted small">
                                    <i class="bi bi-envelope-open d-block fs-2 opacity-25 mb-2"></i>
                                    Tidak ada pengajuan izin saat ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Konfirmasi --}}
<div class="modal fade" id="approvalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <form id="approvalForm" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="modalTitle">Konfirmasi Izin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Catatan (Opsional)</label>
                        <textarea name="catatan" class="form-control" rows="3" placeholder="Berikan alasan atau pesan tambahan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btnSubmit" class="btn btn-primary rounded-pill px-4">Kirim Keputusan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const modal = new bootstrap.Modal(document.getElementById('approvalModal'));
    const form = document.getElementById('approvalForm');
    const title = document.getElementById('modalTitle');
    const btnSubmit = document.getElementById('btnSubmit');

    function handleAction(id, type) {
        if(type === 'approve') {
            title.innerText = 'Setujui Pengajuan Izin';
            btnSubmit.className = 'btn btn-success rounded-pill px-4';
            form.action = `/admin/izin-cuti/${id}/approve`;
        } else {
            title.innerText = 'Tolak Pengajuan Izin';
            btnSubmit.className = 'btn btn-danger rounded-pill px-4';
            form.action = `/admin/izin-cuti/${id}/reject`;
        }
        modal.show();
    }
</script>
@endpush
