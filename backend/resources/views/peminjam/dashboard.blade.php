<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Peminjam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    @include('peminjam.navbar')

    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @php
            $badge = [
                'diajukan'     => 'bg-warning text-dark',
                'dipinjam'     => 'bg-primary',
                'dikembalikan' => 'bg-info text-dark',
                'telat'        => 'bg-danger',
                'selesai'      => 'bg-success',
            ];
        @endphp

        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h3 class="mb-0 text-dark">Halo, {{ auth()->user()->name }}!</h3>
                <small class="text-muted">Ringkasan peminjaman alat Anda.</small>
            </div>
            <a href="{{ route('peminjam.katalog') }}" class="btn btn-primary">+ Ajukan Peminjaman</a>
        </div>

        <!-- Statistik -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <a href="{{ route('peminjam.riwayat') }}" class="text-decoration-none">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <div class="text-muted small text-uppercase">Menunggu Persetujuan</div>
                            <div class="fs-2 fw-bold text-warning">{{ $stats['diajukan'] }}</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-lg-3">
                <a href="{{ route('peminjam.riwayat') }}" class="text-decoration-none">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <div class="text-muted small text-uppercase">Sedang Dipinjam</div>
                            <div class="fs-2 fw-bold text-primary">{{ $stats['dipinjam'] }}</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-lg-3">
                <a href="{{ route('peminjam.riwayat') }}" class="text-decoration-none">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <div class="text-muted small text-uppercase">Lewat Jatuh Tempo</div>
                            <div class="fs-2 fw-bold {{ $stats['jatuh_tempo'] > 0 ? 'text-danger' : 'text-dark' }}">{{ $stats['jatuh_tempo'] }}</div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-lg-3">
                <a href="{{ route('peminjam.riwayat') }}" class="text-decoration-none">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <div class="text-muted small text-uppercase">Total Peminjaman</div>
                            <div class="fs-2 fw-bold text-dark">{{ $stats['total'] }}</div>
                            @if($stats['total_denda'] > 0)
                                <div class="small text-danger">Denda: Rp {{ number_format($stats['total_denda'], 0, ',', '.') }}</div>
                            @endif
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Sedang dipinjam -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong>Alat yang Sedang Dipinjam</strong>
                <a href="{{ route('peminjam.riwayat') }}" class="small">Ke riwayat</a>
            </div>
            <ul class="list-group list-group-flush">
                @forelse($sedangDipinjam as $p)
                    @php
                        $tempo = \Carbon\Carbon::parse($p->tgl_kembali_plan)->startOfDay();
                        $selisih = (int) now()->startOfDay()->diffInDays($tempo, false);
                    @endphp
                    <li class="list-group-item d-flex justify-content-between align-items-start flex-wrap gap-2">
                        <div>
                            @foreach($p->detailPinjam as $d)
                                <div>{{ $d->alat->nama_alat ?? '-' }} <span class="text-muted small">({{ $d->jumlah }})</span></div>
                            @endforeach
                            <small class="text-muted">Kembali paling lambat {{ $tempo->format('d M Y') }}</small>
                        </div>
                        <div class="text-end">
                            @if($selisih < 0)
                                <span class="badge bg-danger">Telat {{ abs($selisih) }} hari</span>
                            @elseif($selisih === 0)
                                <span class="badge bg-warning text-dark">Kembali hari ini</span>
                            @else
                                <span class="badge bg-primary">{{ $selisih }} hari lagi</span>
                            @endif
                            @if($p->tgl_pengajuan_kembali)
                                <div class="small text-success mt-1">Pengembalian diajukan</div>
                            @endif
                        </div>
                    </li>
                @empty
                    <li class="list-group-item text-center text-muted py-4">Tidak ada alat yang sedang Anda pinjam.</li>
                @endforelse
            </ul>
        </div>

        <!-- Aktivitas terbaru -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong>Aktivitas Terbaru</strong>
                <a href="{{ route('peminjam.riwayat') }}" class="small">Lihat semua</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal Pinjam</th>
                            <th>Alat</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($terbaru as $p)
                            <tr>
                                <td class="text-nowrap">{{ \Carbon\Carbon::parse($p->tgl_pinjam)->format('d M Y') }}</td>
                                <td>
                                    @foreach($p->detailPinjam as $d)
                                        <div>{{ $d->alat->nama_alat ?? '-' }} <span class="text-muted small">({{ $d->jumlah }})</span></div>
                                    @endforeach
                                </td>
                                <td><span class="badge {{ $badge[$p->status] ?? 'bg-secondary' }}">{{ ucfirst($p->status) }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-4">Belum ada peminjaman. Mulai dari katalog alat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>