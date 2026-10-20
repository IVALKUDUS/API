@extends('layouts.app')

@section('title', 'Cetak Laporan - Panel Petugas')
@section('header_title', 'Laporan Peminjaman Alat')

@section('content')

{{-- Notifikasi --}}
@if(session('success'))
    <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm text-sm print:hidden">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg shadow-sm text-sm print:hidden">
        {{ session('error') }}
    </div>
@endif

{{-- Card Filter & Tombol Aksi (Sembunyi saat dicetak) --}}
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 mb-6 print:hidden">
    <form action="{{ route('petugas.laporan.index') }}" method="GET" class="flex flex-col md:flex-row items-end gap-4">
        
        <!-- Filter Tanggal Mulai -->
        <div class="w-full md:w-1/4">
            <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Tanggal Mulai</label>
            <input type="date" 
                   name="tgl_mulai" 
                   value="{{ request('tgl_mulai') }}" 
                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <!-- Filter Tanggal Selesai -->
        <div class="w-full md:w-1/4">
            <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Tanggal Selesai</label>
            <input type="date" 
                   name="tgl_selesai" 
                   value="{{ request('tgl_selesai') }}" 
                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <!-- Filter Status -->
        <div class="w-full md:w-1/4">
            <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Status</label>
            <select name="status" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Semua Status --</option>
                <option value="diajukan" {{ request('status') == 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                <option value="telat" {{ request('status') == 'telat' ? 'selected' : '' }}>Telat</option>
            </select>
        </div>

        <!-- Tombol Filter & Cetak -->
        <div class="flex items-center gap-2 w-full md:w-auto">
            <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 text-sm font-semibold rounded-lg transition">
                Filter
            </button>

            @if(request()->hasAny(['tgl_mulai', 'tgl_selesai', 'status']))
                <a href="{{ route('petugas.laporan.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-2 text-sm rounded-lg transition">
                    Reset
                </a>
            @endif

            <button type="button" onclick="window.print()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 text-sm font-semibold rounded-lg transition flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Cetak Laporan
            </button>
        </div>

    </form>
</div>

{{-- Kop Laporan (Hanya muncul saat dicetak) --}}
<div class="hidden print:block mb-6 text-center border-b pb-4 border-gray-800">
    <h2 class="text-2xl font-bold uppercase tracking-wide">Laporan Peminjaman Alat</h2>
    <p class="text-sm text-gray-600">Sistem Informasi Peminjaman Alat Inventaris</p>
    @if(request('tgl_mulai') || request('tgl_selesai'))
        <p class="text-xs text-gray-500 mt-1">
            Periode: {{ request('tgl_mulai') ?? 'Awal' }} s/d {{ request('tgl_selesai') ?? 'Hari ini' }}
        </p>
    @endif
</div>

{{-- Tabel Data --}}
<div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200 print:border-none print:shadow-none">

    <div class="p-5 border-b border-gray-200 bg-gray-50 flex justify-between items-center print:hidden">
        <h3 class="text-lg font-bold text-gray-800">
            Rekap Data Peminjaman
        </h3>
        <span class="text-xs font-semibold text-gray-500">
            Total: {{ $peminjamans->count() }} Data
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse border-gray-300 print:text-xs">
            <thead>
                <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider print:bg-gray-200 print:text-black">
                    <th class="py-3 px-4 border-b border-gray-200 print:border-gray-400 w-12 text-center">No</th>
                    <th class="py-3 px-4 border-b border-gray-200 print:border-gray-400">Peminjam</th>
                    <th class="py-3 px-4 border-b border-gray-200 print:border-gray-400">Alat yang Dipinjam</th>
                    <th class="py-3 px-4 border-b border-gray-200 print:border-gray-400">Tgl Pinjam / Kembali</th>
                    <th class="py-3 px-4 border-b border-gray-200 print:border-gray-400">Status</th>
                </tr>
            </thead>

            <tbody class="text-gray-700 text-sm">
                @forelse($peminjamans as $index => $peminjaman)
                    <tr class="hover:bg-gray-50 transition align-top print:border-b print:border-gray-300">
                        
                        <td class="py-3 px-4 border-b border-gray-200 print:border-gray-300 text-center">
                            {{ $loop->iteration }}
                        </td>

                        <!-- Peminjam -->
                        <td class="py-3 px-4 border-b border-gray-200 print:border-gray-300 font-medium text-gray-900">
                            {{ $peminjaman->user->name ?? $peminjaman->user->nama ?? $peminjaman->nama_peminjam ?? 'N/A' }}
                        </td>

                        <!-- Alat -->
                        <td class="py-3 px-4 border-b border-gray-200 print:border-gray-300">
                            <ul class="list-disc list-inside space-y-1">
                                @php
                                    $details = $peminjaman->detailPinjam ?? $peminjaman->detailPinjams;
                                @endphp
                                @if($details)
                                    @foreach($details as $detail)
                                        <li>
                                            <span class="font-semibold">
                                                {{ $detail->alat->nama_alat ?? $detail->nama_alat ?? 'Alat Tidak Ditemukan' }}
                                            </span>
                                            <span class="text-xs bg-gray-200 px-1.5 py-0.5 rounded print:border print:border-gray-400">
                                                ({{ $detail->jumlah }} pcs)
                                            </span>
                                        </li>
                                    @endforeach
                                @else
                                    <li class="text-xs text-gray-400">Tidak ada detail</li>
                                @endif
                            </ul>
                        </td>

                        <!-- Tanggal -->
                        <td class="py-3 px-4 border-b border-gray-200 print:border-gray-300 text-xs">
                            <span class="block">
                                Pinjam: {{ \Carbon\Carbon::parse($peminjaman->tgl_pinjam)->format('d/m/Y') }}
                            </span>
                            <span class="block font-semibold mt-0.5">
                                Plan: {{ \Carbon\Carbon::parse($peminjaman->tgl_kembali_plan)->format('d/m/Y') }}
                            </span>
                        </td>

                        <!-- Status -->
                        <td class="py-3 px-4 border-b border-gray-200 print:border-gray-300">
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full print:border print:border-gray-400
                                @if($peminjaman->status == 'diajukan') bg-yellow-100 text-yellow-800
                                @elseif($peminjaman->status == 'dipinjam') bg-blue-100 text-blue-800
                                @elseif($peminjaman->status == 'selesai') bg-emerald-100 text-emerald-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($peminjaman->status) }}
                            </span>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-6 text-center text-gray-500">
                            Tidak ada data peminjaman yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination (Sembunyi saat cetak) -->
    @if(method_exists($peminjamans, 'links'))
        <div class="p-4 border-t border-gray-200 bg-gray-50 print:hidden">
            {{ $peminjamans->links() }}
        </div>
    @endif

</div>

<div class="hidden print:flex justify-between items-end mt-12 px-8 text-xs">
    <div>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}</p>
    </div>
    <div class="text-center w-48">
        <p>Petugas Laboratorium,</p>
        <div class="h-16"></div>
        <p class="font-bold underline">{{ auth()->user()->name ?? 'Petugas' }}</p>
    </div>
</div>

<style>
    @media print {
        body {
            background-color: white !important;
        }
        aside, header, footer {
            display: none !important;
        }
        main {
            padding: 0 !important;
        }
        .shadow-sm, .rounded-lg {
            box-shadow: none !important;
            border-radius: 0 !important;
        }
    }
</style>

@endsection