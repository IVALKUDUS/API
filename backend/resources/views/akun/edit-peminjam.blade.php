<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Akun - Peminjam</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 shadow-sm">
        <div class="container">
            <a class="navbar-brand font-weight-bold" href="{{ route('peminjam.katalog') }}">Panel Peminjam</a>
            <div class="d-flex align-items-center">
                <a href="{{ route('peminjam.katalog') }}" class="btn btn-outline-light btn-sm me-2">Katalog Alat</a>
                <a href="{{ route('peminjam.riwayat') }}" class="btn btn-outline-light btn-sm me-2">Riwayat Pinjam</a>
                <a href="{{ route('peminjam.akun.index') }}" class="btn btn-light btn-sm text-primary fw-bold me-2">Akun Saya</a>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-light btn-sm text-primary font-weight-bold">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container" style="max-width: 760px;">

        <h4 class="mb-3">Edit Akun</h4>

        <!-- FORM PROFIL -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold">Informasi Profil <span class="text-muted small fw-normal">— role dan tanggal bergabung tidak dapat diubah</span></div>
            <div class="card-body">
                <form action="{{ route('peminjam.akun.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Foto Profil</label>
                        <input type="file" name="foto_profile" accept="image/*"
                               class="form-control @error('foto_profile') is-invalid @enderror">
                        <div class="form-text">Format: JPG, JPEG, PNG. Maksimal 2MB. Kosongkan jika tidak ingin mengubah foto.</div>
                        @error('foto_profile') <div class="invalid-feedback">{{ $message }}</div> @enderror
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

                    <div class="text-end">
                        <a href="{{ route('peminjam.akun.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>

                @if($user->foto_profile)
                    <form action="{{ route('peminjam.akun.foto.destroy') }}" method="POST" class="mt-2"
                          onsubmit="return confirm('Hapus foto profil?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-link btn-sm text-danger p-0">Hapus foto profil</button>
                    </form>
                @endif
            </div>
        </div>

        <!-- FORM GANTI PASSWORD -->
        <div id="password" class="card shadow-sm mb-5">
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
                        <input type="password" name="password_confirmation" required autocomplete="new-password"
                               class="form-control">
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-dark">Ganti Password</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
