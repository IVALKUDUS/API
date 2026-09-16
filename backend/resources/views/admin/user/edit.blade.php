@extends('layouts.app')

@section('title', 'Edit User - Panel Admin')
@section('header-title', 'Edit Data User')

@section('content')

<div class="max-w-2xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200 p-6">

    <form action="{{ route('admin.user.update', $user->id) }}" method="POST">

        @csrf
        @method('PUT')

        <div class="mb-4">

            <label class="block text-gray-700 text-sm font-semibold mb-2">
                Nama
            </label>

            <input
                type="text"
                name="name"
                value="{{ old('name', $user->name) }}"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            >

            @error('name')
                <span class="text-red-500 text-sm">
                    {{ $message }}
                </span>
            @enderror

        </div>

        <div class="mb-4">

            <label class="block text-gray-700 text-sm font-semibold mb-2">
                Email
            </label>

            <input
                type="email"
                name="email"
                value="{{ old('email', $user->email) }}"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            >

            @error('email')
                <span class="text-red-500 text-sm">
                    {{ $message }}
                </span>
            @enderror

        </div>

        <div class="mb-4">

            <label class="block text-gray-700 text-sm font-semibold mb-2">
                Password
            </label>

            <input
                type="password"
                name="password"
                placeholder="Kosongkan jika tidak ingin mengubah password"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            >

            @error('password')
                <span class="text-red-500 text-sm">
                    {{ $message }}
                </span>
            @enderror

        </div>

        <div class="mb-4">

            <label class="block text-gray-700 text-sm font-semibold mb-2">
                Role
            </label>

            <select
                name="role"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">

                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>
                    Admin
                </option>

                <option value="petugas" {{ old('role', $user->role) == 'petugas' ? 'selected' : '' }}>
                    Petugas
                </option>

                <option value="peminjam" {{ old('role', $user->role) == 'peminjam' ? 'selected' : '' }}>
                    Peminjam
                </option>

            </select>

            @error('role')
                <span class="text-red-500 text-sm">
                    {{ $message }}
                </span>
            @enderror

        </div>

        <div class="mb-6">

            <label class="block text-gray-700 text-sm font-semibold mb-2">
                No. HP
            </label>

            <input
                type="text"
                name="no_hp"
                value="{{ old('no_hp', $user->no_hp) }}"
                placeholder="Masukkan nomor HP"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            >

            @error('no_hp')
                <span class="text-red-500 text-sm">
                    {{ $message }}
                </span>
            @enderror

        </div>

        <div class="flex justify-end gap-2">

            <a
                href="{{ route('admin.user.index') }}"
                class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg font-semibold transition">
                Batal
            </a>

            <button
                type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold transition">
                Perbarui
            </button>

        </div>

    </form>

</div>

@endsection