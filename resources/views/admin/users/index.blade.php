@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Manajemen Pengguna (User)</h4>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus"></i> Tambah Pengguna
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
@endif

<div class="card">
    <div class="card-body p-0 table-responsive border-0">
        <table class="table text-nowrap align-middle">
            <thead>
                <tr>
                    <th class="ps-4">No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Peran / Akses Data</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td class="ps-4">{{ $loop->iteration }}</td>
                    <td class="fw-bold text-primary">{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if($user->program_studi_id)
                            <span class="badge bg-secondary"><i class="bi bi-shield-lock me-1"></i> Operator: {{ $user->programStudi->nama_prodi }}</span>
                        @else
                            <span class="badge bg-success"><i class="bi bi-globe me-1"></i> Super Admin / Global</span>
                        @endif
                    </td>
                    <td class="text-end pe-4">
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary shadow-sm"><i class="bi bi-pencil"></i></a>
                        @if($user->id !== auth()->id())
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus pengguna ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger shadow-sm"><i class="bi bi-trash"></i></button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
