@extends('layouts.app')

@section('title', 'Kelola Pengembalian Alat')
@section('header_title', 'Manajemen Transaksi Pengembalian')

@section('content')

{{-- Flash Message Success --}}
@if(session('success'))
    <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm text-sm">
        {{ session('success') }}
    </div>
@endif

{{-- Flash Message Error --}}
@if(session('error'))
    <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg shadow-sm text-sm">
        {{ session('error') }}
    </div>
@endif

{{-- Card Container --}}
<div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">

    {{-- Card Header & Form Pencarian --}}
    <div class="p-5 border-b border-gray-200 bg-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">
        <h3 class="text-lg font-bold text-gray-800">
            Daftar Barang Sedang Dipinjam
        </h3>

        <div class="flex items-center gap-3 w-full md:w-auto">
            <!-- Form Search -->
            <form action="{{ route('petugas.pengembalian.index') }}" method="GET" class="flex w-full md:w-80">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nama peminjam..."
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">

                <button
                    type="submit"
                    class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 text-sm font-semibold rounded-r-lg transition">
                    Cari
                </button>
            </form>

            @if(request('search'))
                <a href="{{ route('petugas.pengembalian.index') }}"
                   class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-2 text-sm rounded-lg flex items-center transition">
                    Reset
                </a>
            @endif
        </div>
    </div>

    {{-- Tabel Peminjaman Aktif / Pengembalian --}}
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                    <th class="py-3 px-4 border-b">Peminjam</th>
                    <th class="py-3 px-4 border-b">Alat & Jumlah</th>
                    <th class="py-3 px-4 border-b">Tgl Pinjam / Target</th>
                    <th class="py-3 px-4 border-b">Status</th>
                </tr>
            </thead>

            <tbody class="text-gray-700 text-sm">
                @forelse($pengembalian as $item)
                    <tr class="hover:bg-gray-50 transition align-top">

                        <!-- Peminjam & ID Pinjam -->
                        <td class="py-3 px-4 border-b font-medium text-gray-900">
                            <div>{{ $item->user->name ?? 'User Dihapus' }}</div>
                            <div class="text-xs text-gray-400">ID Pinjam: #{{ $item->id }}</div>
                            <div class="text-xs text-gray-500 font-normal">{{ $item->user->no_hp ?? '-' }}</div>
                        </td>

                        <!-- Daftar Alat yang Dipinjam -->
                        <td class="py-3 px-4 border-b">
                            <ul class="space-y-1">
                                @forelse($item->detailPinjams as $detail)
                                    <li class="text-xs">
                                        <span class="font-semibold text-gray-800">• {{ $detail->alat->nama_alat ?? 'Alat dihapus' }}</span>
                                        <span class="bg-gray-200 text-gray-700 px-1.5 py-0.5 rounded font-mono text-[10px] ml-1">{{ $detail->jumlah }} unit</span>
                                    </li>
                                @empty
                                    <li class="text-xs text-gray-400 italic">Tidak ada rincian alat</li>
                                @endforelse
                            </ul>
                        </td>

                        <!-- Tanggal Pinjam & Tanggal Target Kembali -->
                        <td class="py-3 px-4 border-b text-xs text-gray-600 whitespace-nowrap">
                            <div>Pinjam: {{ $item->tgl_pinjam ? \Carbon\Carbon::parse($item->tgl_pinjam)->format('d M Y') : '-' }}</div>
                            <div class="text-[10px] text-gray-400">
                                Target: {{ $item->tgl_kembali_plan ? \Carbon\Carbon::parse($item->tgl_kembali_plan)->format('d M Y') : ($item->tgl_kembali ? \Carbon\Carbon::parse($item->tgl_kembali)->format('d M Y') : '-') }}
                            </div>
                        </td>

                        <!-- Status Peminjaman -->
                        <td class="py-3 px-4 border-b whitespace-nowrap">
                            <span class="bg-amber-100 text-amber-800 font-semibold text-xs px-2.5 py-1 rounded-full border border-amber-200">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-6 text-center text-gray-500">
                            Belum ada transaksi barang yang sedang dipinjam.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if(is_object($pengembalian) && method_exists($pengembalian, 'links'))
        <div class="p-4 border-t border-gray-200 bg-gray-50">
            {{ $pengembalian->links() }}
        </div>
    @endif

</div>

@endsection