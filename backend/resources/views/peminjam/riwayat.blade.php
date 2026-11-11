<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pinjam - Peminjam</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- NAVBAR -->
    @include('peminjam.navbar')

    <div class="container">
        <!-- NOTIFIKASI / FLASH MESSAGE -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <h3 class="mb-3 text-dark">Riwayat Peminjaman Saya</h3>

        @php
            $badge = [
                'diajukan'     => 'bg-warning text-dark',
                'dipinjam'     => 'bg-primary',
                'dikembalikan' => 'bg-info text-dark',
                'telat'        => 'bg-danger',
                'selesai'      => 'bg-success',
            ];
        @endphp

        <div class="table-responsive bg-white rounded shadow-sm">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-primary">
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th>Tgl Pinjam</th>
                        <th>Rencana Kembali</th>
                        <th>Alat Dipinjam</th>
                        <th class="text-center">Status</th>
                        <th>Pengembalian</th>
                        <th style="min-width:220px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $p)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ \Carbon\Carbon::parse($p->tgl_pinjam)->format('d M Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($p->tgl_kembali_plan)->format('d M Y') }}</td>
                            <td>
                                <ul class="list-unstyled mb-0">
                                    @foreach($p->detailPinjam as $d)
                                        <li>
                                            {{ $d->alat->nama_alat ?? '-' }}
                                            <span class="badge bg-secondary">x{{ $d->jumlah }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $badge[$p->status] ?? 'bg-secondary' }}">
                                    {{ ucfirst($p->status) }}
                                </span>
                            </td>
                            <td>
                                @if($p->pengembalian)
                                    <div>Tgl: {{ \Carbon\Carbon::parse($p->pengembalian->tgl_kembali)->format('d M Y') }}</div>
                                    <div>Kondisi: {{ $p->pengembalian->kondisi_kembali }}</div>
                                    @if($p->pengembalian->denda > 0)
                                        <div class="text-danger fw-semibold">
                                            Denda: Rp {{ number_format($p->pengembalian->denda, 0, ',', '.') }}
                                        </div>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($p->status === 'dipinjam')
                                    @if($p->tgl_pengajuan_kembali)
                                        <span class="badge bg-info text-dark">Menunggu konfirmasi petugas</span>
                                        <div class="small text-muted mt-1">
                                            Diajukan {{ $p->tgl_pengajuan_kembali->format('d M Y, H:i') }}
                                        </div>
                                        @if($p->catatan_kembali)
                                            <div class="small text-muted">Catatan: {{ $p->catatan_kembali }}</div>
                                        @endif
                                    @else
                                        <form action="{{ route('peminjam.pengembalian.ajukan', $p->id) }}" method="POST"
                                              onsubmit="return confirm('Ajukan pengembalian alat ini?')">
                                            @csrf
                                            <textarea name="catatan_kembali" rows="2" maxlength="255"
                                                class="form-control form-control-sm mb-1"
                                                placeholder="Catatan kondisi alat (opsional)"></textarea>
                                            <button type="submit" class="btn btn-success btn-sm w-100">Ajukan Pengembalian</button>
                                        </form>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                Belum ada riwayat peminjaman.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>