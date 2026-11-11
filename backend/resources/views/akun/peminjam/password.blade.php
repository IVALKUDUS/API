@extends('akun.peminjam.layout')

@section('title', 'Ganti Password')

@section('content')
<div class="card shadow-sm mb-5">
    <div class="card-header bg-white fw-semibold">Ganti Password</div>
    <div class="card-body">
        <form action="{{ route('peminjam.akun.password') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label fw-semibold">Password Lama</label>
                <input type="password" name="password_lama" required autocomplete="current-password"
                       class="form-control @error('password_lama') is-invalid @enderror">
                @error('password_lama') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Password Baru</label>
                <input type="password" name="password" required minlength="8" autocomplete="new-password"
                       class="form-control @error('password') is-invalid @enderror">
                <div class="form-text">Minimal 8 karakter.</div>
                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" required autocomplete="new-password" class="form-control">
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('peminjam.akun.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-dark">Ganti Password</button>
            </div>
        </form>
    </div>
</div>
@endsection
