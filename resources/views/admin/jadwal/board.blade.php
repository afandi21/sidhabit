@extends('layouts.app')

@section('title', 'Interactive Jadwal Board')

@push('styles')
<style>
    .draggable-item {
        cursor: grab;
        transition: transform 0.2s;
    }
    .draggable-item:active {
        cursor: grabbing;
        transform: scale(0.95);
    }
    .dropzone {
        min-height: 80px;
        border: 2px dashed rgba(12, 96, 70, 0.2);
        transition: all 0.2s;
        padding: 4px;
        border-radius: 6px;
    }
    .dropzone.drag-over {
        border-color: var(--primary-color);
        background-color: rgba(12, 96, 70, 0.05);
    }
    .grid-container {
        max-width: 100%;
        overflow-x: auto;
        border-radius: 12px;
        box-shadow: inset 0 0 10px rgba(0,0,0,0.02);
        background: #fff;
    }
    .custom-board-table {
        table-layout: fixed;
        width: max-content;
    }
    th.sticky-left, td.sticky-left {
        position: sticky;
        left: 0;
        background-color: #fdfaf5; 
        z-index: 2;
        border-right: 2px solid #e7d1b1;
        width: 120px;
        min-width: 120px;
        white-space: nowrap;
    }
    th.sticky-top {
        position: sticky;
        top: 0;
        background-color: #fcf8ef;
        z-index: 1;
        width: 280px;
        min-width: 280px;
    }
    th.sticky-cross {
        z-index: 4;
        background-color: #e7d1b1 !important; 
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Interactive Scheduling Board</h4>
    <a href="{{ route('admin.jadwal.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-x-lg"></i> Tutup Board
    </a>
</div>

<div class="row h-100">
    <!-- Sidebar: Draggable Beban Mengajar -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm sticky-top" style="top: 20px; max-height: 85vh; overflow-y: auto;">
            <div class="card-header bg-white border-bottom fw-bold py-3">
                <i class="bi bi-inbox me-2"></i> Bank Beban (Tarik Saya!)
            </div>
            <div class="card-body p-2 bg-light">
                @forelse($bebans as $beban)
                    <div class="card border border-primary border-opacity-25 mb-2 draggable-item shadow-sm" 
                         draggable="true" 
                         data-id="{{ $beban->id }}"
                         id="beban_{{ $beban->id }}">
                        <div class="card-body p-2">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <span class="badge bg-secondary">{{ $beban->mataKuliah->programStudi->kode_prodi ?? '-' }}</span>
                                <span class="badge bg-primary rounded-pill sks-count"><span class="terjadwal">{{ $beban->sks_terjadwal }}</span>/{{ $beban->total_sks }}</span>
                            </div>
                            <div class="fw-bold mb-1 lh-sm">{{ $beban->mataKuliah->nama_mk }}</div>
                            <div class="small text-muted mb-1"><i class="bi bi-person"></i> {{ $beban->dosen->nama_gelar }}</div>
                            <div class="small fw-semibold text-success"><i class="bi bi-door-open"></i> Kelas: {{ $beban->kelas }}</div>
                        </div>
                    </div>
                @empty
                    <div class="text-center p-4 text-muted small">
                        Semua beban sudah terjadwal atau belum ada plotting.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="col-md-9">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-0 grid-container">
                <table class="table table-bordered mb-0 text-center align-middle custom-board-table">
                    <thead class="bg-light small text-uppercase">
                        <tr>
                            <th class="sticky-left sticky-top sticky-cross align-middle py-3" style="width: 150px;">Hari & Jam</th>
                            @foreach($ruangans as $ruangan)
                                <th class="sticky-top py-3">{{ $ruangan->nama_ruangan }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="small">
                        @foreach($haris as $hari)
                            @php 
                                $max_sesi = ($hari->nama_hari == 'Kamis') ? 4 : $sesis->count();
                            @endphp
                            
                            <tr class="table-active border-dark border-bottom border-2">
                                <td colspan="{{ $ruangans->count() + 1 }}" class="fw-bold text-start ps-3 py-2 sticky-left">
                                    <i class="bi bi-calendar-day me-2"></i> HARI {{ strtoupper($hari->nama_hari) }}
                                </td>
                            </tr>
                            
                            @foreach($sesis->take($max_sesi) as $sesi)
                                <tr>
                                    <td class="sticky-left text-start ps-3 fw-semibold bg-white text-muted">
                                        Sesi {{ $loop->iteration }} <br>
                                        <small class="fw-normal">{{ substr($sesi->jam_mulai, 0, 5) }} - {{ substr($sesi->jam_selesai, 0, 5) }}</small>
                                    </td>
                                    
                                    @foreach($ruangans as $ruangan)
                                        @php
                                            $key = $ruangan->id . '_' . $hari->id . '_' . $sesi->id;
                                            $isi = $jadwals[$key] ?? null;
                                        @endphp
                                        
                                        <td class="p-1 align-top" style="width:{{ 100 / $ruangans->count() }}%">
                                            <div class="dropzone h-100" 
                                                 data-hari="{{ $hari->id }}" 
                                                 data-sesi="{{ $sesi->id }}" 
                                                 data-ruangan="{{ $ruangan->id }}">
                                                
                                                @if($isi && $isi !== 'BLOCKED_BY_ID')
                                                    @if(is_object($isi))
                                                        <div class="badge bg-secondary bg-opacity-75 text-wrap text-start p-2 w-100 mb-1 shadow-sm">
                                                            <div class="fw-bold fs-7">{{ $isi->mataKuliah->nama_mk }}</div>
                                                            <small>{{ $isi->dosen->nama_gelar }} ({{ $isi->kelas }})</small>
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Loading -->
<div class="modal" id="loadingModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content bg-transparent border-0">
      <div class="modal-body text-center">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const draggables = document.querySelectorAll('.draggable-item');
    const dropzones = document.querySelectorAll('.dropzone');
    const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
    
    let draggedId = null;

    draggables.forEach(draggable => {
        draggable.addEventListener('dragstart', (e) => {
            draggedId = draggable.dataset.id;
            draggable.style.opacity = '0.5';
            e.dataTransfer.effectAllowed = 'copy';
        });

        draggable.addEventListener('dragend', () => {
            draggable.style.opacity = '1';
        });
    });

    dropzones.forEach(zone => {
        zone.addEventListener('dragover', e => {
            e.preventDefault();
            zone.classList.add('drag-over');
        });

        zone.addEventListener('dragleave', () => {
            zone.classList.remove('drag-over');
        });

        zone.addEventListener('drop', e => {
            e.preventDefault();
            zone.classList.remove('drag-over');
            
            if(!draggedId) return;

            const hariId = zone.dataset.hari;
            const sesiId = zone.dataset.sesi;
            const ruanganId = zone.dataset.ruangan;

            if(zone.innerHTML.trim() !== '') {
                if(!confirm('Ada jadwal di blok ini. Tetap lemparkan? (Mungkin overlap waktu sebagian)')) return;
            }

            processDrop(draggedId, hariId, sesiId, ruanganId, zone);
        });
    });

    function processDrop(bebanId, hariId, sesiId, ruanganId, targetZone) {
        loadingModal.show();
        
        fetch("{{ route('admin.jadwal.board.drop') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                beban_id: bebanId,
                hari_id: hariId,
                sesi_id: sesiId,
                ruangan_id: ruanganId
            })
        })
        .then(response => response.json())
        .then(data => {
            loadingModal.hide();
            if (data.success) {
                // Tambahkan elemen visual ke cell
                targetZone.innerHTML += data.html;
                
                // Update sisa SKS di sidebar
                const card = document.getElementById('beban_' + bebanId);
                const terjadwalSpan = card.querySelector('.terjadwal');
                terjadwalSpan.innerText = data.sks_terjadwal;
                
                if(data.sks_terjadwal >= data.total_sks) {
                    card.style.display = 'none'; // Sembunyikan jika sudah full
                }
                
                toast('Berhasil!', data.message, 'success');
            } else {
                toast('Gagal', data.message, 'danger');
            }
        })
        .catch(err => {
            loadingModal.hide();
            console.error(err);
            toast('Network Error', 'Gagal menghubungi server.', 'danger');
        });
    }

    function toast(title, message, bg) {
        const toastContainer = document.createElement('div');
        toastContainer.className = 'position-fixed bottom-0 end-0 p-3';
        toastContainer.style.zIndex = '1080';
        toastContainer.innerHTML = `
            <div class="toast align-items-center text-white bg-${bg} border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
              <div class="d-flex">
                <div class="toast-body fw-bold">
                  ${title} - <span class="fw-normal">${message}</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
              </div>
            </div>`;
        document.body.appendChild(toastContainer);
        setTimeout(() => toastContainer.remove(), 4000);
    }
});
</script>
@endpush
