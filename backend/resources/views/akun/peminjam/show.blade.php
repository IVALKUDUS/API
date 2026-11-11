@extends('akun.peminjam.layout')

@section('title', 'Akun Saya')

@section('content')
<div class="card shadow-sm mb-5">
    <div class="card-body">

        <!-- Header: foto + nama + role -->
        <div class="d-flex align-items-center gap-3 pb-3 mb-2 border-bottom">
            @if($user->foto_profile)
                <img src="{{ asset('storage/' . $user->foto_profile) }}" alt="{{ $user->name }}"
                     class="rounded-circle border" style="width:80px;height:80px;object-fit:cover;">
            @else
                <div class="rounded-circle bg-secondary-subtle text-secondary border d-flex align-items-center justify-content-center fw-semibold text-uppercase"
                     style="width:80px;height:80px;">
                    {{ mb_substr($user->name, 0, 2) }}
                </div>
            @endif
            <div>
                <div class="h5 mb-0">{{ $user->name }}</div>
                <div class="text-muted small">{{ $user->email }}</div>
                <span class="badge text-bg-primary text-uppercase mt-1">{{ $user->role }}</span>
            </div>
        </div>

        <!-- Detail informasi (hanya tampilan) -->
        <dl class="row mb-0 small">
            <dt class="col-sm-3 text-muted py-2 border-bottom">Nama</dt>
            <dd class="col-sm-9 fw-semibold py-2 border-bottom mb-0">{{ $user->name }}</dd>

            <dt class="col-sm-3 text-muted py-2 border-bottom">Email</dt>
            <dd class="col-sm-9 fw-semibold py-2 border-bottom mb-0">{{ $user->email }}</dd>

            <dt class="col-sm-3 text-muted py-2 border-bottom">No. HP</dt>
            <dd class="col-sm-9 fw-semibold py-2 border-bottom mb-0">{{ $user->no_hp ?: '-' }}</dd>

            <dt class="col-sm-3 text-muted py-2 border-bottom">Alamat</dt>
            <dd class="col-sm-9 fw-semibold py-2 border-bottom mb-0">{{ $user->alamat ?: '-' }}</dd>

            <dt class="col-sm-3 text-muted py-2">Bergabung</dt>
            <dd class="col-sm-9 fw-semibold py-2 mb-0">{{ $user->created_at?->translatedFormat('d F Y') ?? '-' }}</dd>
        </dl>

        <!-- Tombol aksi -->
        <div class="d-flex justify-content-end gap-2 pt-3 mt-3 border-top">
            <a href="{{ route('peminjam.akun.password.edit') }}" class="btn btn-outline-dark">Ganti Password</a>
            <a href="{{ route('peminjam.akun.edit') }}" class="btn btn-primary">Edit Profil</a>
        </div>
    </div>
</div>
@endsection
