@extends('layouts.app')

@section('title', 'Edit Pengguna')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Edit Pengguna</h4>
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="card">
    <div class="card-body p-4">
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label class="form-label fw-bold">Nama Lengkap</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                @error('name')<small class="text-danger">{{ $message }}</small>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                @error('email')<small class="text-danger">{{ $message }}</small>@enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Password Baru (Opsional)</label>
                    <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah password">
                    @error('password')<small class="text-danger">{{ $message }}</small>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password baru">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Hak Akses Data (Atribusi Program Studi)</label>
                <select name="program_studi_id" class="form-select border-primary shadow-sm" {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                    <option value="">-- JADIKAN SUPER ADMIN (GLOBAL) --</option>
                    @foreach($prodis as $prodi)
                        <option value="{{ $prodi->id }}" {{ old('program_studi_id', $user->program_studi_id) == $prodi->id ? 'selected' : '' }}>
                            Operator Prodi: {{ $prodi->nama_prodi }} ({{ $prodi->kode_prodi }})
                        </option>
                    @endforeach
                </select>
                @if($user->id === auth()->id())
                    <small class="text-warning">Anda tidak dapat mengubah hak akses untuk akun Anda sendiri.</small>
                    <input type="hidden" name="program_studi_id" value="{{ $user->program_studi_id }}">
                @endif
                @error('program_studi_id')<small class="text-danger">{{ $message }}</small>@enderror
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm"><i class="bi bi-save me-2"></i> Perbarui Pengguna</button>
            </div>
        </form>
    </div>
</div>
@endsection
