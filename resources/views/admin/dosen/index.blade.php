@extends('layouts.app')

@section('title', 'Manajemen Dosen')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-4">
        <h4 class="mb-0 fw-bold">Data Dosen</h4>
        <p class="text-muted small mb-0">Total: <strong>{{ $dosens->total() }}</strong> dosen terdaftar</p>
    </div>
    <div class="col-md-8">
        <form action="{{ route('admin.dosen.index') }}" method="GET" class="row g-2 justify-content-md-end">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" id="searchInput" class="form-control border-start-0" placeholder="Cari Nama / NIDN..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="prodi_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Prodi</option>
                    @foreach($prodis as $p)
                        <option value="{{ $p->id }}" {{ request('prodi_id') == $p->id ? 'selected' : '' }}>{{ $p->nama_prodi }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-auto">
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="bi bi-file-earmark-excel"></i> Import
                    </button>
                    <a href="{{ route('admin.dosen.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Tambah
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<x-card>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-muted small text-uppercase">
                <tr>
                    <th class="ps-4 py-3">Nama Dosen</th>
                    <th class="py-3">NIDN / NUPTK</th>
                    <th class="py-3">Program Studi</th>
                    <th class="py-3">Status</th>
                    <th class="py-3 text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dosens as $d)
                <tr>
                    <td class="ps-4">
                        <div class="fw-bold text-dark">{{ $d->nama_lengkap }}</div>
                        <small class="text-muted">{{ $d->user->email }}</small>
                    </td>
                    <td>
                        <div class="small">NIDN: {{ $d->nidn ?? '-' }}</div>
                        <div class="small text-muted">NUPTK: {{ $d->nuptk ?? '-' }}</div>
                    </td>
                    <td>
                        <x-badge type="info" soft>
                            {{ $d->programStudi->nama_prodi ?? 'N/A' }}
                        </x-badge>
                    </td>
                    <td>
                        @if($d->status_aktif == 'aktif')
                            <x-badge type="success" rounded>Aktif</x-badge>
                        @else
                            <x-badge type="secondary" rounded>{{ ucfirst($d->status_aktif) }}</x-badge>
                        @endif
                    </td>
                    <td class="text-end pe-4">
                        <div class="btn-group">
                            <a href="{{ route('admin.dosen.impersonate', $d->id) }}" class="btn btn-sm btn-outline-warning" title="Login Sebagai Dosen ini">
                                <i class="bi bi-person-workspace"></i>
                            </a>
                            <a href="{{ route('admin.dosen.ketersediaan', $d->id) }}" class="btn btn-sm btn-outline-info" title="Atur Ketersediaan/Libur">
                                <i class="bi bi-calendar-check"></i>
                            </a>
                            <a href="{{ route('admin.dosen.edit', $d->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete({{ $d->id }})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                        <form id="delete-form-{{ $d->id }}" action="{{ route('admin.dosen.destroy', $d->id) }}" method="POST" class="d-none">
                            @csrf @method('DELETE')
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        <i class="bi bi-people fs-1 d-block mb-2 opacity-25"></i>
                        Belum ada data dosen.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white py-3 border-top">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                Menampilkan <strong>{{ $dosens->firstItem() ?? 0 }}</strong> sampai <strong>{{ $dosens->lastItem() ?? 0 }}</strong> dari <strong>{{ $dosens->total() }}</strong> data
            </div>
            <div class="pagination-sm">
                {{ $dosens->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</x-card>

{{-- Modal Import --}}
<x-modal id="importModal" title="Import Data Dosen" action="{{ route('admin.dosen.import') }}" hasFile="true" submitText="Upload & Import">
    <div class="text-center mb-4">
        <i class="bi bi-file-earmark-arrow-up text-primary" style="font-size: 3rem;"></i>
        <p class="mt-2 text-muted small">Pilih file Excel (.xlsx) atau CSV yang sesuai dengan template.</p>
    </div>
    
    <div class="mb-3">
        <label class="form-label small fw-bold text-muted">File Excel/CSV</label>
        <input type="file" name="file_excel" class="form-control" accept=".xlsx, .xls, .csv" required>
    </div>

    <div class="bg-light p-3 rounded-3">
        <h6 class="small fw-bold mb-2"><i class="bi bi-info-circle me-1"></i> Instruksi:</h6>
        <ul class="small text-muted mb-0 ps-3">
            <li>Download template melalui link di bawah.</li>
            <li>Isi data sesuai kolom yang tersedia (Wajib isi <strong>Tanggal Lahir</strong>).</li>
            <li>Kolom <strong>prodi</strong> harus sesuai dengan nama prodi di sistem (PBA, MPI, dll).</li>
            <li>Password default adalah <strong>Tanggal Lahir (DDMMYYYY)</strong>. Contoh: 02121995.</li>
        </ul>
    </div>

    <x-slot name="footerLeft">
        <a href="{{ route('admin.dosen.template') }}" class="btn btn-link text-decoration-none small">
            <i class="bi bi-download me-1"></i> Download Template
        </a>
    </x-slot>
</x-modal>

@push('scripts')
<script>
    let searchTimeout = null;

    // Fitur Pencarian Real-Time AJAX (Cari ke Seluruh Database)
    document.getElementById('searchInput').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const searchValue = this.value;
        const prodiValue = document.querySelector('select[name="prodi_id"]').value;

        // Jeda 500ms setelah mengetik agar tidak memberatkan server
        searchTimeout = setTimeout(() => {
            fetchSearch(searchValue, prodiValue);
        }, 500);
    });

    async function fetchSearch(search, prodi) {
        const tableBody = document.querySelector('table tbody');
        const cardFooter = document.querySelector('.card-footer');
        
        // Tampilkan loading sebentar
        tableBody.style.opacity = '0.5';

        try {
            const url = new URL(window.location.href);
            url.searchParams.set('search', search);
            url.searchParams.set('prodi_id', prodi);
            url.searchParams.set('page', 1); // Reset ke halaman 1 saat mencari

            const response = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!response.ok) throw new Error('Network error');

            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            // Update isi tabel dan pagination
            tableBody.innerHTML = doc.querySelector('table tbody').innerHTML;
            cardFooter.innerHTML = doc.querySelector('.card-footer').innerHTML;
            
            // Update URL browser tanpa reload (agar bookmark/refresh tetap di hasil pencarian)
            window.history.pushState({}, '', url);

        } catch (error) {
            console.error('Search error:', error);
        } finally {
            tableBody.style.opacity = '1';
        }
    }

    function confirmDelete(id) {
        if (confirm('Apakah Anda yakin ingin menghapus data dosen ini? Pengguna terkait juga akan dihapus.')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>
@endpush
@endsection
