@extends('akun.peminjam.layout')

@section('title', 'Edit Profil')

@section('content')
<div class="card shadow-sm mb-5">
    <div class="card-header bg-white fw-semibold">Edit Profil</div>
    <div class="card-body">
        <form action="{{ route('peminjam.akun.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label fw-semibold">Foto Profil</label>
                <div class="d-flex align-items-center gap-3 mb-2">
                    @if($user->foto_profile)
                        <img src="{{ asset('storage/' . $user->foto_profile) }}" alt="Foto saat ini"
                             class="rounded-circle border" style="width:64px;height:64px;object-fit:cover;">
                    @else
                        <div class="rounded-circle bg-secondary-subtle text-secondary border d-flex align-items-center justify-content-center fw-semibold text-uppercase"
                             style="width:64px;height:64px;">{{ mb_substr($user->name, 0, 2) }}</div>
                    @endif
                    <input type="file" name="foto_profile" accept="image/*"
                           class="form-control @error('foto_profile') is-invalid @enderror">
                </div>
                <div class="form-text">Format: JPG, JPEG, PNG. Maksimal 2MB. Kosongkan jika tidak ingin mengubah foto.</div>
                @error('foto_profile') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Nama</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="form-control @error('name') is-invalid @enderror">
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="form-control @error('email') is-invalid @enderror">
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">No. HP</label>
                <input type="text" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}" placeholder="Masukkan nomor HP"
                       class="form-control @error('no_hp') is-invalid @enderror">
                @error('no_hp') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Alamat</label>
                <textarea name="alamat" rows="3" placeholder="Masukkan alamat"
                          class="form-control @error('alamat') is-invalid @enderror">{{ old('alamat', $user->alamat) }}</textarea>
                @error('alamat') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('peminjam.akun.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>

        @if($user->foto_profile)
            <form action="{{ route('peminjam.akun.foto.destroy') }}" method="POST" class="mt-3 pt-3 border-top"
                  onsubmit="return confirm('Hapus foto profil?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-link btn-sm text-danger p-0">Hapus foto profil</button>
            </form>
        @endif
    </div>
</div>
@endsection
