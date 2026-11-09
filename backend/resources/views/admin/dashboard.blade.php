@extends('layouts.app')

@section('title', 'Dashboard Admin - Sistem Peminjaman')
@section('header_title', 'Dashboard Admin')

@section('content')
    @php
        $badge = [
            'diajukan'     => 'bg-yellow-100 text-yellow-800',
            'dipinjam'     => 'bg-blue-100 text-blue-800',
            'dikembalikan' => 'bg-gray-200 text-gray-700',
            'telat'        => 'bg-red-100 text-red-700',
            'selesai'      => 'bg-green-100 text-green-700',
        ];
    @endphp

    <!-- Alert Selamat Datang -->
    <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm">
        Selamat datang, <strong class="font-semibold text-emerald-950">{{ auth()->user()->name }}</strong>! Anda login sebagai hak akses
        <span class="uppercase font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded border border-emerald-300 text-xs tracking-wide">
            {{ auth()->user()->role }}
        </span>.
    </div>

    <!-- Kartu Statistik -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <a href="{{ route('admin.user.index') }}" class="bg-white p-5 rounded-lg shadow-sm border border-gray-200 hover:shadow transition">
            <p class="text-xs uppercase tracking-wider text-gray-500">Total User</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $stats['total_user'] }}</p>
        </a>
        <a href="{{ route('admin.alat.index') }}" class="bg-white p-5 rounded-lg shadow-sm border border-gray-200 hover:shadow transition">
            <p class="text-xs uppercase tracking-wider text-gray-500">Total Alat</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $stats['total_alat'] }}</p>
        </a>
        <a href="{{ route('admin.peminjaman.index') }}" class="bg-white p-5 rounded-lg shadow-sm border border-gray-200 hover:shadow transition">
            <p class="text-xs uppercase tracking-wider text-gray-500">Total Peminjaman</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $stats['total_peminjaman'] }}</p>
        </a>
        <a href="{{ route('admin.pengembalian.index') }}" class="bg-white p-5 rounded-lg shadow-sm border border-gray-200 hover:shadow transition">
            <p class="text-xs uppercase tracking-wider text-gray-500">Total Pengembalian</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $stats['total_pengembalian'] }}</p>
            <p class="mt-1 text-xs text-gray-500">Denda terkumpul: Rp {{ number_format($stats['total_denda'], 0, ',', '.') }}</p>
        </a>
    </div>

    <!-- Ringkasan Status Peminjaman -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Status Peminjaman</h3>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
            @foreach(['diajukan' => 'Diajukan', 'dipinjam' => 'Dipinjam', 'dikembalikan' => 'Dikembalikan', 'telat' => 'Telat', 'selesai' => 'Selesai'] as $key => $label)
                <div class="rounded-lg p-3 text-center {{ $badge[$key] }}">
                    <p class="text-2xl font-bold">{{ $statusCounts[$key] ?? 0 }}</p>
                    <p class="text-xs uppercase tracking-wide">{{ $label }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Peminjaman Terbaru -->
        <div class="xl:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-5 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800">Peminjaman Terbaru</h3>
                <a href="{{ route('admin.peminjaman.index') }}" class="text-sm text-blue-600 hover:underline">Lihat semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100 text-gray-600 text-xs uppercase tracking-wider">
                            <th class="py-3 px-4 border-b">Peminjam</th>
                            <th class="py-3 px-4 border-b">Alat</th>
                            <th class="py-3 px-4 border-b">Rencana Kembali</th>
                            <th class="py-3 px-4 border-b">Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-sm divide-y divide-gray-100">
                        @forelse($peminjamanTerbaru as $p)
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
                                <td class="py-3 px-4">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $badge[$p->status] ?? 'bg-gray-100 text-gray-700' }}">
                                        {{ ucfirst($p->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-6 text-center text-gray-500">Belum ada peminjaman.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Stok Menipis -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-5 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800">Stok Menipis</h3>
                <a href="{{ route('admin.alat.index') }}" class="text-sm text-blue-600 hover:underline">Kelola alat</a>
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
@endsection