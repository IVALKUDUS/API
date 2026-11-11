@extends('layouts.app')

@section('title', 'Dashboard Petugas - Sistem Peminjaman')
@section('header_title', 'Dashboard Petugas')

@section('content')
    @php
        $badge = [
            'diajukan' => 'bg-yellow-100 text-yellow-800',
            'dipinjam' => 'bg-blue-100 text-blue-800',
        ];
    @endphp

    <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm">
        Selamat datang, <strong class="font-semibold text-emerald-950">{{ auth()->user()->name }}</strong>!
        Anda login sebagai
        <span class="uppercase font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded border border-emerald-300 text-xs tracking-wide">
            {{ auth()->user()->role }}
        </span>.
    </div>

    <!-- Kartu Statistik -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <a href="{{ route('petugas.peminjaman.index') }}" class="bg-white p-5 rounded-lg shadow-sm border border-gray-200 hover:shadow transition">
            <p class="text-xs uppercase tracking-wider text-gray-500">Menunggu Persetujuan</p>
            <p class="mt-2 text-3xl font-bold {{ $stats['menunggu'] > 0 ? 'text-yellow-600' : 'text-gray-900' }}">{{ $stats['menunggu'] }}</p>
            <p class="mt-1 text-xs text-gray-500">Pengajuan peminjaman baru</p>
        </a>
        <a href="{{ route('petugas.pengembalian.index') }}" class="bg-white p-5 rounded-lg shadow-sm border border-gray-200 hover:shadow transition">
            <p class="text-xs uppercase tracking-wider text-gray-500">Sedang Dipinjam</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $stats['dipinjam'] }}</p>
            <p class="mt-1 text-xs text-gray-500">{{ $stats['ajuan_kembali'] }} sudah mengajukan pengembalian</p>
        </a>
        <a href="{{ route('petugas.pengembalian.index') }}" class="bg-white p-5 rounded-lg shadow-sm border border-gray-200 hover:shadow transition">
            <p class="text-xs uppercase tracking-wider text-gray-500">Lewat Jatuh Tempo</p>
            <p class="mt-2 text-3xl font-bold {{ $stats['jatuh_tempo'] > 0 ? 'text-red-600' : 'text-gray-900' }}">{{ $stats['jatuh_tempo'] }}</p>
            <p class="mt-1 text-xs text-gray-500">Belum dikembalikan</p>
        </a>
        <a href="{{ route('petugas.laporan.index') }}" class="bg-white p-5 rounded-lg shadow-sm border border-gray-200 hover:shadow transition">
            <p class="text-xs uppercase tracking-wider text-gray-500">Diproses Hari Ini</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $stats['diproses_hari_ini'] }}</p>
            <p class="mt-1 text-xs text-gray-500">Pengembalian oleh Anda</p>
        </a>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
        <!-- Pengajuan Menunggu -->
        <div class="xl:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-5 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800">Pengajuan Menunggu Persetujuan</h3>
                <a href="{{ route('petugas.peminjaman.index') }}" class="text-sm text-blue-600 hover:underline">Lihat semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100 text-gray-600 text-xs uppercase tracking-wider">
                            <th class="py-3 px-4 border-b">Peminjam</th>
                            <th class="py-3 px-4 border-b">Alat</th>
                            <th class="py-3 px-4 border-b">Rencana Kembali</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-sm divide-y divide-gray-100">
                        @forelse($pengajuanBaru as $p)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="py-3 px-4 font-medium text-gray-900 whitespace-nowrap">{{ $p->user->name ?? '-' }}</td>
                                <td class="py-3 px-4">
                                    @foreach($p->detailPinjam as $d)
                                        <div>{{ $d->alat->nama_alat ?? '-' }} <span class="text-xs text-gray-500">({{ $d->jumlah }})</span></div>
                                    @endforeach
                                </td>
                                <td class="py-3 px-4 text-xs text-gray-500 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($p->tgl_kembali_plan)->format('d M Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-6 text-center text-gray-500">Tidak ada pengajuan yang menunggu.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Stok Menipis -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-5 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800">Stok Menipis</h3>
            </div>
            <ul class="divide-y divide-gray-100">
                @forelse($stokMenipis as $a)
                    <li class="flex justify-between items-center px-5 py-3 text-sm">
                        <span class="text-gray-800">{{ $a->nama_alat }}</span>
                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $a->stok == 0 ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $a->stok == 0 ? 'Habis' : 'Sisa ' . $a->stok }}
                        </span>
                    </li>
                @empty
                    <li class="px-5 py-6 text-center text-sm text-gray-500">Semua stok alat aman.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <!-- Perlu Dikembalikan -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-5 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800">Pengembalian Terdekat</h3>
            <a href="{{ route('petugas.pengembalian.index') }}" class="text-sm text-blue-600 hover:underline">Pemantauan pengembalian</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-xs uppercase tracking-wider">
                        <th class="py-3 px-4 border-b">Peminjam</th>
                        <th class="py-3 px-4 border-b">Alat</th>
                        <th class="py-3 px-4 border-b">Jatuh Tempo</th>
                        <th class="py-3 px-4 border-b">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm divide-y divide-gray-100">
                    @forelse($jatuhTempo as $p)
                        @php
                            $tempo = \Carbon\Carbon::parse($p->tgl_kembali_plan)->startOfDay();
                            $selisih = (int) now()->startOfDay()->diffInDays($tempo, false);
                        @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-3 px-4 font-medium text-gray-900 whitespace-nowrap">{{ $p->user->name ?? '-' }}</td>
                            <td class="py-3 px-4">
                                @foreach($p->detailPinjam as $d)
                                    <div>{{ $d->alat->nama_alat ?? '-' }} <span class="text-xs text-gray-500">({{ $d->jumlah }})</span></div>
                                @endforeach
                            </td>
                            <td class="py-3 px-4 text-xs text-gray-500 whitespace-nowrap">{{ $tempo->format('d M Y') }}</td>
                            <td class="py-3 px-4 space-x-1">
                                @if($selisih < 0)
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">Telat {{ abs($selisih) }} hari</span>
                                @elseif($selisih === 0)
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">Hari ini</span>
                                @else
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">{{ $selisih }} hari lagi</span>
                                @endif
                                @if($p->tgl_pengajuan_kembali)
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Minta dikembalikan</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-6 text-center text-gray-500">Tidak ada alat yang sedang dipinjam.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection