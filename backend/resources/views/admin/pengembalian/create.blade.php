@extends('layouts.app')

@section('title', 'Proses Pengembalian - Panel Admin')
@section('header_title', 'Form Pengembalian Alat')

@section('content')

<div class="max-w-2xl bg-white rounded-lg shadow-sm border border-gray-200 p-6">

    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-3 rounded-lg text-sm">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('admin.pengembalian.store') }}" method="POST">
        @csrf

        <!-- Pilih Transaksi Peminjaman -->
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">
                Pilih Peminjaman Aktif
            </label>

            <select
                name="peminjaman_id"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Pilih Transaksi Peminjaman --</option>
                @foreach($peminjamanAktif as $pinjam)
                    <option value="{{ $pinjam->id }}" {{ old('peminjaman_id') == $pinjam->id ? 'selected' : '' }}>
                        #{{ $pinjam->id }} - {{ $pinjam->user->name ?? 'User N/A' }} (Batas Kembali: {{ \Carbon\Carbon::parse($pinjam->tgl_kembali_plan)->format('d/m/Y') }})
                    </option>
                @endforeach
            </select>
            <p class="text-xs text-gray-500 mt-1">Hanya menampilkan peminjaman yang berstatus 'dipinjam'.</p>

            @error('peminjaman_id')
                <span class="text-red-500 text-xs block mt-1">{{ $message }}</span>
            @enderror
        </div>

        <!-- Tanggal Kembali Realisasi -->
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">
                Tanggal Pengembalian (Hari Ini)
            </label>

            <input
                type="date"
                name="tgl_kembali"
                value="{{ old('tgl_kembali', date('Y-m-d')) }}"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">

            @error('tgl_kembali')
                <span class="text-red-500 text-xs block mt-1">{{ $message }}</span>
            @enderror
        </div>

        <!-- Kondisi Alat Saat Kembali -->
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">
                Kondisi Alat Kembali
            </label>

            <textarea
                name="kondisi_kembali"
                rows="3"
                placeholder="Contoh: Lengkap dan Berfungsi Baik / Casing Sedikit Tergores..."
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('kondisi_kembali') }}</textarea>

            @error('kondisi_kembali')
                <span class="text-red-500 text-xs block mt-1">{{ $message }}</span>
            @enderror
        </div>

        <!-- Nominal Denda -->
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-semibold mb-2">
                Nominal Denda (Rp)
            </label>

            <input
                type="number"
                name="denda"
                value="{{ old('denda', 0) }}"
                min="0"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <p class="text-xs text-gray-500 mt-1">Masukkan angka 0 jika tidak ada denda keterlambatan/kerusakan.</p>

            @error('denda')
                <span class="text-red-500 text-xs block mt-1">{{ $message }}</span>
            @enderror
        </div>

        <!-- Action Button -->
        <div class="flex justify-end space-x-2">
            <a
                href="{{ route('admin.pengembalian.index') }}"
                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg text-sm font-semibold transition">
                Batal
            </a>

            <button
                type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                Simpan & Update Stok
            </button>
        </div>
    </form>
</div>

@endsection