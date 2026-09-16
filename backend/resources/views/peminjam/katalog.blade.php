<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Alat - Peminjam</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 shadow-sm">
        <div class="container">
            <a class="navbar-brand font-weight-bold" href="#">Panel Peminjam</a>
            <div class="d-flex align-items-center">
                <a href="{{ route('peminjam.riwayat') }}" class="btn btn-outline-light btn-sm me-2">Riwayat Pinjam</a>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-light btn-sm text-primary font-weight-bold">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- NOTIFIKASI / FLASH MESSAGE -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <h3 class="mb-3 text-dark">Katalog Alat Tersedia</h3>

        <form action="{{ route('peminjam.peminjaman.ajukan') }}" method="POST">
            @csrf
            
            <!-- FORM RENCANA TANGGAL KEMBALI -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label font-weight-semibold">Rencana Tanggal Kembali</label>
                        <input type="date" 
                               name="tgl_kembali_plan" 
                               class="form-control" 
                               min="{{ date('Y-m-d') }}" 
                               required>
                    </div>
                </div>
            </div>

            <!-- TABEL KATALOG ALAT -->
            <div class="table-responsive bg-white rounded shadow-sm">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-primary">
                        <tr>
                            <th width="5%" class="text-center">Pilih</th>
                            <th>Nama Alat</th>
                            <th>Kategori</th>
                            <th class="text-center">Stok Tersedia</th>
                            <th width="15%" class="text-center">Jumlah Pinjam</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($alat as $item)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="alat_id[]" value="{{ $item->id }}" class="form-check-input">
                                </td>
                                <td class="fw-medium">{{ $item->nama_alat }}</td>
                                <td>{{ $item->kategori->nama_kategori ?? $item->kategori ?? '-' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-info text-dark">{{ $item->stok }}</span>
                                </td>
                                <td>
                                    <input type="number" 
                                           name="jumlah[{{ $item->id }}]" 
                                           class="form-control form-control-sm text-center" 
                                           value="1" 
                                           min="1" 
                                           max="{{ $item->stok }}">
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    Tidak ada alat yang tersedia saat ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary px-4 py-2">
                    Ajukan Peminjaman
                </button>
            </div>
        </form>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>