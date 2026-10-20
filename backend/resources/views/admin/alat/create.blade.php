@extends('layouts.app')

@section('title','Tambah Alat - Panel Admin')
@section('header-title','Tambah Alat Baru')

@section('content')

<div class="max-w-2xl bg-white rounded-lg shadow-sm border border-gray-200 p-6">

<form action="{{ route('admin.alat.store') }}" method="POST" enctype="multipart/form-data">

@csrf

<div class="mb-4">
<label class="block text-gray-700 text-sm font-semibold mb-2">Nama Alat</label>
<input type="text" name="nama_alat" value="{{ old('nama_alat') }}" required
class="w-full px-3 py-2 border border-gray-300 rounded-lg">
@error('nama_alat') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
</div>

<div class="mb-4">
<label class="block text-gray-700 text-sm font-semibold mb-2">Kategori</label>
<select name="kategori_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg" required>
<option value="">-- Pilih Kategori --</option>
@foreach($kategoris as $kategori)
<option value="{{ $kategori->id }}" {{ old('kategori_id')==$kategori->id ? 'selected':'' }}>
{{ $kategori->nama_kategori }}
</option>
@endforeach
</select>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">

<div>
<label class="block text-gray-700 text-sm font-semibold mb-2">Stok</label>
<input type="number" name="stok" value="{{ old('stok',0) }}" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
</div>

<div>
<label class="block text-gray-700 text-sm font-semibold mb-2">Status Kondisi</label>
<input type="text" name="status_kondisi" value="{{ old('status_kondisi') }}" placeholder="Baik / Rusak Ringan"
class="w-full px-3 py-2 border border-gray-300 rounded-lg">
</div>

</div>

<div class="mb-4">
<label class="block text-gray-700 text-sm font-semibold mb-2">Deskripsi (Opsional)</label>
<textarea name="deskripsi" rows="4"
class="w-full px-3 py-2 border border-gray-300 rounded-lg">{{ old('deskripsi') }}</textarea>
</div>

<div class="mb-6">
<label class="block text-gray-700 text-sm font-semibold mb-2">Gambar Alat (Opsional)</label>
<input type="file" name="gambar" accept="image/*"
class="w-full text-sm file:bg-blue-50 file:text-blue-700 file:border-0 file:px-4 file:py-2 file:rounded-lg">
</div>

<div class="flex justify-end gap-2">
<a href="{{ route('admin.alat.index') }}"
class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg">Batal</a>

<button type="submit"
class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
Simpan
</button>
</div>

</form>

</div>

@endsection