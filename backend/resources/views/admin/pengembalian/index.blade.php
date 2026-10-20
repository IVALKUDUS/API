@extends('layouts.app')

@section('title', 'Kelola Pengembalian - Panel Admin')
@section('header_title', 'Manajemen Transaksi Pengembalian')

@section('content')

@if(session('success'))
    <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm text-sm">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg shadow-sm text-sm">
        {{ session('error') }}
    </div>
@endif

<div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">

    <div class="p-5 border-b border-gray-200 bg-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">
        <h3 class="text-lg font-bold text-gray-800">
            Daftar Riwayat Pengembalian
        </h3>

        <div class="flex items-center gap-3 w-full md:w-auto">
            <!-- Form Search -->
            <form action="{{ route('admin.pengembalian.index') }}" method="GET" class="flex w-full md:w-80">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari peminjam / petugas..."
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500">

                <button
                    type="submit"
                    class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 text-sm font-semibold rounded-r-lg transition">
                    Cari
                </button>
            </form>

            @if(request('search'))
                <a href="{{ route('admin.pengembalian.index') }}"
                   class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-2 text-sm rounded-lg flex items-center transition">
                    Reset
                </a>
            @endif
        </div>

        <!-- Tombol Tambah -->
        <a href="{{ route('admin.pengembalian.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition whitespace-nowrap">
            + Proses Pengembalian
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                    <th class="py-3 px-4 border-b">Peminjam</th>
                    <th class="py-3 px-4 border-b">Alat & Jumlah</th>
                    <th class="py-3 px-4 border-b">Tgl Kembali</th>
                    <th class="py-3 px-4 border-b">Kondisi Alat</th>
                    <th class="py-3 px-4 border-b">Denda</th>
                    <th class="py-3 px-4 border-b">Petugas</th>
                    <th class="py-3 px-4 border-b">Aksi</th>
                </tr>
            </thead>

            <tbody class="text-gray-700 text-sm">
                @forelse($pengembalians as $pengembalian)
                    <tr class="hover:bg-gray-50 transition align-top">

                        <!-- Peminjam -->
                        <td class="py-3 px-4 border-b font-medium text-gray-900">
                            <div>{{ $pengembalian->peminjaman->user->name ?? '-' }}</div>
                            <div class="text-xs text-gray-500 font-normal">{{ $pengembalian->peminjaman->user->no_hp ?? '' }}</div>
                        </td>

                        <!-- Daftar Alat yang Dipinjam -->
                        <td class="py-3 px-4 border-b">
                            @php
                                $details = $pengembalian->peminjaman->detailPinjam ?? $pengembalian->peminjaman->detail_pinjam ?? [];
                            @endphp
                            <ul class="space-y-1">
                                @forelse($details as $detail)
                                    <li class="text-xs">
                                        <span class="font-semibold text-gray-800">• {{ $detail->alat->nama_alat ?? 'Alat dihapus' }}</span>
                                        <span class="bg-gray-200 text-gray-700 px-1.5 py-0.5 rounded font-mono text-[10px] ml-1">{{ $detail->jumlah }} unit</span>
                                    </li>
                                @empty
                                    <li class="text-xs text-gray-400 italic">Tidak ada rincian alat</li>
                                @endforelse
                            </ul>
                        </td>

                        <!-- Tanggal Kembali Realisasi -->
                        <td class="py-3 px-4 border-b text-xs text-gray-600 whitespace-nowrap">
                            <div>{{ \Carbon\Carbon::parse($pengembalian->tgl_kembali)->format('d M Y') }}</div>
                            <div class="text-[10px] text-gray-400">Target: {{ \Carbon\Carbon::parse($pengembalian->peminjaman->tgl_kembali_plan)->format('d M Y') }}</div>
                        </td>

                        <!-- Kondisi Alat -->
                        <td class="py-3 px-4 border-b max-w-xs text-xs">
                            {{ $pengembalian->kondisi_kembali }}
                        </td>

                        <!-- Denda -->
                        <td class="py-3 px-4 border-b whitespace-nowrap">
                            @if($pengembalian->denda > 0)
                                <span class="text-red-600 font-semibold text-xs bg-red-50 px-2 py-1 rounded border border-red-200">
                                    Rp {{ number_format($pengembalian->denda, 0, ',', '.') }}
                                </span>
                            @else
                                <span class="text-emerald-600 font-semibold text-xs bg-emerald-50 px-2 py-1 rounded border border-emerald-200">
                                    Rp 0
                                </span>
                            @endif
                        </td>

                        <!-- Petugas Penerima -->
                        <td class="py-3 px-4 border-b text-xs text-gray-600 whitespace-nowrap">
                            {{ $pengembalian->petugas->name ?? '-' }}
                        </td>

                        <!-- Aksi Hapus / Rollback -->
                        <td class="py-3 px-4 border-b whitespace-nowrap">
                            <form action="{{ route('admin.pengembalian.destroy', $pengembalian->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Membatalkan pengembalian akan menarik stok kembali dari gudang. Yakin ingin menghapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs font-semibold transition">
                                    Hapus
                                </button>
                            </form>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-6 text-center text-gray-500">
                            Belum ada riwayat transaksi pengembalian.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if(method_exists($pengembalians, 'links'))
        <div class="p-4 border-t border-gray-200 bg-gray-50">
            {{ $pengembalians->links() }}
        </div>
    @endif

</div>

@endsection