@extends('layouts.app')

@section('title', 'Akun Saya')
@section('header-title', 'Akun Saya')

@section('content')

<div class="max-w-2xl mx-auto space-y-6">

    @if(session('success'))
        <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">

        <!-- Header: foto + nama + role -->
        <div class="flex items-center gap-5 pb-6 border-b border-gray-100">
            <x-user-avatar :user="$user" size="w-20 h-20">
                <div class="text-sm text-gray-500 font-normal">{{ $user->email }}</div>
                <span class="inline-block mt-1 px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold uppercase">
                    {{ $user->role }}
                </span>
            </x-user-avatar>
        </div>

        <!-- Detail informasi (hanya tampilan) -->
        <dl class="divide-y divide-gray-100 text-sm">
            <div class="py-3 grid grid-cols-3 gap-4">
                <dt class="text-gray-500">Nama</dt>
                <dd class="col-span-2 text-gray-900 font-medium">{{ $user->name }}</dd>
            </div>
            <div class="py-3 grid grid-cols-3 gap-4">
                <dt class="text-gray-500">Email</dt>
                <dd class="col-span-2 text-gray-900 font-medium">{{ $user->email }}</dd>
            </div>
            <div class="py-3 grid grid-cols-3 gap-4">
                <dt class="text-gray-500">No. HP</dt>
                <dd class="col-span-2 text-gray-900 font-medium">{{ $user->no_hp ?: '-' }}</dd>
            </div>
            <div class="py-3 grid grid-cols-3 gap-4">
                <dt class="text-gray-500">Alamat</dt>
                <dd class="col-span-2 text-gray-900 font-medium">{{ $user->alamat ?: '-' }}</dd>
            </div>
            <div class="py-3 grid grid-cols-3 gap-4">
                <dt class="text-gray-500">Role</dt>
                <dd class="col-span-2 text-gray-900 font-medium capitalize">{{ $user->role }}</dd>
            </div>
            <div class="py-3 grid grid-cols-3 gap-4">
                <dt class="text-gray-500">Bergabung</dt>
                <dd class="col-span-2 text-gray-900 font-medium">{{ $user->created_at?->translatedFormat('d F Y') ?? '-' }}</dd>
            </div>
        </dl>

        <!-- Tombol aksi -->
        <div class="flex justify-end gap-2 pt-6 border-t border-gray-100">
            <a href="{{ route($prefix . '.akun.password.edit') }}"
               class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg font-semibold transition">
                Ganti Password
            </a>
            <a href="{{ route($prefix . '.akun.edit') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold transition">
                Edit Profil
            </a>
        </div>
    </div>

</div>
@endsection
