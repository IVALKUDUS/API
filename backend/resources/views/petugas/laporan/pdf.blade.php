<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Peminjaman Alat</title>
    <style>
        @page { margin: 28px 32px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111; }
        .kop { text-align: center; border-bottom: 2px solid #111; padding-bottom: 8px; margin-bottom: 12px; }
        .kop h1 { font-size: 16px; margin: 0; text-transform: uppercase; letter-spacing: 1px; }
        .kop p { margin: 2px 0 0; color: #555; font-size: 10px; }
        .info { margin-bottom: 10px; }
        .info table { border: none; width: auto; }
        .info td { border: none; padding: 1px 8px 1px 0; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data th { background: #e5e7eb; text-transform: uppercase; font-size: 9px; text-align: left; }
        table.data th, table.data td { border: 1px solid #9ca3af; padding: 5px 6px; vertical-align: top; }
        table.data tr { page-break-inside: avoid; }
        .center { text-align: center; }
        .badge { display: inline-block; padding: 1px 6px; border: 1px solid #6b7280; border-radius: 8px; font-size: 9px; }
        .muted { color: #6b7280; }
        .ringkasan { margin-top: 12px; }
        .ringkasan td { border: none; padding: 1px 12px 1px 0; }
        .ttd { margin-top: 28px; width: 100%; }
        .ttd td { border: none; vertical-align: bottom; }
    </style>
</head>
<body>

    <div class="kop">
        <h1>Laporan Peminjaman Alat</h1>
        <p>Sistem Informasi Peminjaman Alat Inventaris</p>
    </div>

    <div class="info">
        <table>
            <tr>
                <td>Periode</td>
                <td>:
                    @if($tglMulai || $tglSelesai)
                        {{ $tglMulai ? \Carbon\Carbon::parse($tglMulai)->format('d/m/Y') : 'Awal' }}
                        s/d
                        {{ $tglSelesai ? \Carbon\Carbon::parse($tglSelesai)->format('d/m/Y') : 'Hari ini' }}
                    @else
                        Semua periode
                    @endif
                </td>
            </tr>
            <tr>
                <td>Status</td>
                <td>: {{ $status ? ucfirst($status) : 'Semua status' }}</td>
            </tr>
            <tr>
                <td>Total Data</td>
                <td>: {{ $peminjamans->count() }}</td>
            </tr>
        </table>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th class="center" style="width:6%">No</th>
                <th style="width:22%">Peminjam</th>
                <th style="width:34%">Alat yang Dipinjam</th>
                <th style="width:22%">Tgl Pinjam / Kembali</th>
                <th style="width:16%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($peminjamans as $p)
                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    <td>{{ $p->user->name ?? 'N/A' }}</td>
                    <td>
                        @foreach($p->detailPinjams as $d)
                            <div>- {{ $d->alat->nama_alat ?? 'Alat tidak ditemukan' }} ({{ $d->jumlah }} pcs)</div>
                        @endforeach
                    </td>
                    <td>
                        Pinjam: {{ \Carbon\Carbon::parse($p->tgl_pinjam)->format('d/m/Y') }}<br>
                        <strong>Plan: {{ \Carbon\Carbon::parse($p->tgl_kembali_plan)->format('d/m/Y') }}</strong>
                    </td>
                    <td><span class="badge">{{ ucfirst($p->status) }}</span></td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="center muted" style="padding:14px;">
                        Tidak ada data peminjaman yang ditemukan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($peminjamans->count())
        <table class="ringkasan">
            <tr>
                <td><strong>Ringkasan:</strong></td>
                @foreach($ringkasan as $st => $jumlah)
                    <td>{{ ucfirst($st) }}: {{ $jumlah }}</td>
                @endforeach
            </tr>
        </table>
    @endif

    <table class="ttd">
        <tr>
            <td class="muted">Dicetak pada: {{ now()->format('d/m/Y H:i') }}</td>
            <td style="width:190px; text-align:center;">
                Petugas,
                <div style="height:55px;"></div>
                <strong><u>{{ auth()->user()->name ?? 'Petugas' }}</u></strong>
            </td>
        </tr>
    </table>

</body>
</html>