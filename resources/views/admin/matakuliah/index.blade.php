@extends('layouts.app')

@section('title', 'Data Mata Kuliah')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-4">
        <h4 class="mb-0 fw-bold">Daftar Mata Kuliah</h4>
        <p class="text-muted small mb-0">Total: <strong>{{ $matkuls->total() }}</strong> mata kuliah terdaftar</p>
    </div>
    <div class="col-md-8">
        <form action="{{ route('admin.matakuliah.index') }}" method="GET" class="row g-2 justify-content-md-end">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" id="searchInput" class="form-control border-start-0" placeholder="Cari Nama / Kode MK..." value="{{ request('search') }}">
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
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importMKModal">
                        <i class="bi bi-file-earmark-excel"></i> Import
                    </button>
                    <a href="{{ route('admin.matakuliah.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Tambah
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

@if($activeSemester)
    @php
        $isGanjil = str_contains(strtolower($activeSemester->nama_semester), 'ganjil');
        $isGenap = str_contains(strtolower($activeSemester->nama_semester), 'genap');
        $jenisSemester = $isGanjil ? 'Ganjil' : ($isGenap ? 'Genap' : '');
    @endphp
    <div class="alert alert-info border-0 shadow-sm mb-4 d-flex align-items-center justify-content-between">
        <div class="small">
            <i class="bi bi-info-circle-fill me-2"></i>
            Hanya menampilkan MK semester <strong>{{ $jenisSemester }}</strong> (Periode <strong>{{ $activeSemester->nama_semester }}</strong>).
        </div>
        <div>
            @if(!request()->has('semua'))
                <a href="{{ route('admin.matakuliah.index', ['semua' => 1]) }}" class="btn btn-sm btn-light border text-nowrap">Lihat Semua</a>
            @else
                <a href="{{ route('admin.matakuliah.index') }}" class="btn btn-sm btn-light border text-nowrap">Lihat {{ $jenisSemester }}</a>
            @endif
        </div>
    </div>
@endif

<x-card>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-muted small text-uppercase">
                <tr>
                    <th class="ps-4 py-3">Kode MK</th>
                    <th class="py-3">Nama Mata Kuliah</th>
                    <th class="py-3">Prodi</th>
                    <th class="py-3 text-center">SMT</th>
                    <th class="py-3">SKS</th>
                    <th class="py-3 text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($matkuls as $m)
                <tr>
                    <td class="ps-4 fw-bold">{{ $m->kode_mk }}</td>
                    <td>
                        <div class="fw-bold text-dark">{{ $m->nama_mk }}</div>
                        <div class="d-flex gap-1 mt-1">
                            <x-badge type="light" class="border small" style="font-size: 0.65rem;">{{ str_replace('_', ' & ', $m->jenis) }}</x-badge>
                            <x-badge type="{{ $m->kategori == 'mahad' ? 'warning' : 'info' }}" class="small text-uppercase" style="font-size: 0.65rem;">{{ $m->kategori }}</x-badge>
                        </div>
                    </td>
                    <td>{{ $m->programStudi->nama_prodi ?? 'N/A' }}</td>
                    <td class="text-center">
                        <x-badge type="{{ $m->semester % 2 == 0 ? 'info' : 'primary' }}" soft>
                            {{ $m->semester }}
                        </x-badge>
                    </td>
                    <td><x-badge type="secondary">{{ $m->sks }} SKS</x-badge></td>
                    <td class="text-end pe-4">
                        <div class="btn-group">
                            <a href="{{ route('admin.matakuliah.edit', $m->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.matakuliah.destroy', $m->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus mata kuliah ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-4">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white py-3 border-top">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                Menampilkan <strong>{{ $matkuls->firstItem() ?? 0 }}</strong> sampai <strong>{{ $matkuls->lastItem() ?? 0 }}</strong> dari <strong>{{ $matkuls->total() }}</strong> data
            </div>
            <div class="pagination-sm">
                {{ $matkuls->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</x-card>

{{-- Modal Import Mata Kuliah --}}
<x-modal id="importMKModal" title="Import Mata Kuliah" action="{{ route('admin.matakuliah.import') }}" hasFile="true" submitText="Import Data">
    <div class="mb-3">
        <label class="form-label small fw-bold text-muted">File Excel/CSV</label>
        <input type="file" name="file_excel" class="form-control" accept=".xlsx, .xls, .csv" required>
    </div>

    <div class="bg-light p-3 rounded-3">
        <h6 class="small fw-bold mb-2"><i class="bi bi-info-circle me-1"></i> Aturan Kolom:</h6>
        <ul class="small text-muted mb-0 ps-3">
            <li><strong>jenis</strong>: teori / praktikum</li>
            <li><strong>kategori</strong>: dikti / mahad</li>
            <li><strong>prodi</strong>: Sesuai nama Prodi (PBA, MPI, dll)</li>
        </ul>
    </div>

    <x-slot name="footerLeft">
        <a href="{{ route('admin.matakuliah.template') }}" class="btn btn-link text-decoration-none small">
            <i class="bi bi-download me-1"></i> Download Template
        </a>
    </x-slot>
</x-modal>

@push('scripts')
<script>
    let searchTimeout = null;

    document.getElementById('searchInput').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const searchValue = this.value;
        const prodiValue = document.querySelector('select[name="prodi_id"]').value;

        searchTimeout = setTimeout(() => {
            fetchSearch(searchValue, prodiValue);
        }, 500);
    });

    async function fetchSearch(search, prodi) {
        const tableBody = document.querySelector('table tbody');
        const cardFooter = document.querySelector('.card-footer');
        tableBody.style.opacity = '0.5';

        try {
            const url = new URL(window.location.href);
            url.searchParams.set('search', search);
            url.searchParams.set('prodi_id', prodi);
            url.searchParams.set('page', 1);

            const response = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            tableBody.innerHTML = doc.querySelector('table tbody').innerHTML;
            cardFooter.innerHTML = doc.querySelector('.card-footer').innerHTML;
            window.history.pushState({}, '', url);
        } catch (error) {
            console.error('Search error:', error);
        } finally {
            tableBody.style.opacity = '1';
        }
    }
</script>
@endpush
@endsection
