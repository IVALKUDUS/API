@extends('layouts.app')

@section('title', 'Ganti Password')
@section('header-title', 'Ganti Password')

@section('content')

<div class="max-w-2xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200 p-6">

    <form action="{{ route($prefix . '.akun.password') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Password Lama</label>
            <input type="password" name="password_lama" required autocomplete="current-password"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('password_lama') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Password Baru</label>
            <input type="password" name="password" required minlength="8" autocomplete="new-password"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <p class="text-xs text-gray-500 mt-1">Minimal 8 karakter.</p>
            @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Konfirmasi Password Baru</label>
            <input type="password" name="password_confirmation" required autocomplete="new-password"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route($prefix . '.akun.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg font-semibold transition">
                Batal
            </a>
            <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-lg font-semibold transition">
                Ganti Password
            </button>
        </div>
    </form>
</div>
@endsection
