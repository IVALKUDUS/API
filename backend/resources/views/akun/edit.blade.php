@extends('layouts.app')

@section('title', 'Edit Profil')
@section('header-title', 'Edit Profil')

@section('content')

<div class="max-w-2xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200 p-6">

    <form action="{{ route($prefix . '.akun.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Foto Profil</label>
            <div class="flex items-center gap-4 mb-2">
                @if($user->foto_profile)
                    <img src="{{ asset('storage/' . $user->foto_profile) }}" alt="Foto saat ini" class="w-16 h-16 rounded-full object-cover border">
                @else
                    <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center font-semibold text-gray-500 uppercase">
                        {{ mb_substr($user->name, 0, 2) }}
                    </div>
                @endif
                <input type="file" name="foto_profile" accept="image/*"
                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>
            <p class="text-xs text-gray-500 mt-1">Format: JPG, JPEG, PNG. Maksimal 2MB. Kosongkan jika tidak ingin mengubah foto.</p>
            @error('foto_profile') <span class="text-red-500 text-sm block mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Nama</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">No. HP</label>
            <input type="text" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}" placeholder="Masukkan nomor HP"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('no_hp') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Alamat</label>
            <textarea name="alamat" rows="3" placeholder="Masukkan alamat"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('alamat', $user->alamat) }}</textarea>
            @error('alamat') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route($prefix . '.akun.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg font-semibold transition">
                Batal
            </a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold transition">
                Simpan
            </button>
        </div>
    </form>

    @if($user->foto_profile)
        <form action="{{ route($prefix . '.akun.foto.destroy') }}" method="POST" class="mt-4 pt-4 border-t border-gray-100"
              onsubmit="return confirm('Hapus foto profil?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-sm text-red-600 hover:underline">Hapus foto profil</button>
        </form>
    @endif
</div>
@endsection
