@extends('layouts.app')

@section('title', 'Kelola User - Panel Admin')
@section('header-title', 'Manajemen Data User')

@section('content')

@if(session('success'))
    <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg shadow-sm">
        {{ session('error') }}
    </div>
@endif

<div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">

    <div class="p-5 border-b border-gray-200 bg-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">

        <div class="flex items-center gap-3 w-full md:w-auto">

            <form action="{{ route('admin.user.index') }}" method="GET" class="flex w-full md:w-96">

                <input
                    type="text"
                    name="search"
                    value="{{ $search ?? '' }}"
                    placeholder="Cari nama, email, atau role..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >

                <button
                    type="submit"
                    class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-r-lg font-semibold">
                    Cari
                </button>

            </form>

            @if(request('search'))
                <a
                    href="{{ route('admin.user.index') }}"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg">
                    Reset
                </a>
            @endif

        </div>

        <a
            href="{{ route('admin.user.create') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold transition whitespace-nowrap">
            + Tambah User
        </a>

    </div>

    <div class="overflow-x-auto">

        <table class="w-full text-left border-collapse">

            <thead>

                <tr class="bg-gray-100 text-gray-600 text-xs uppercase tracking-wider">

                    <th class="py-3 px-4 border">No</th>
                    <th class="py-3 px-4 border">Nama</th>
                    <th class="py-3 px-4 border">Email</th>
                    <th class="py-3 px-4 border">Role</th>
                    <th class="py-3 px-4 border">No. HP</th>
                    <th class="py-3 px-4 border text-center">Aksi</th>

                </tr>

            </thead>

            <tbody class="text-gray-700 text-sm">

                @forelse($users as $user)

                    <tr class="border-b hover:bg-gray-50 transition">

                        <td class="py-3 px-4 border">
                            {{ $users->firstItem() + $loop->index }}
                        </td>

                        <td class="py-3 px-4 border font-medium">
                            {{ $user->name }}
                        </td>

                        <td class="py-3 px-4 border">
                            {{ $user->email }}
                        </td>

                        <td class="py-3 px-4 border">

                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                @if($user->role == 'admin')
                                    bg-purple-100 text-purple-800
                                @elseif($user->role == 'petugas')
                                    bg-blue-100 text-blue-800
                                @else
                                    bg-green-100 text-green-800
                                @endif
                            ">
                                {{ ucfirst($user->role) }}
                            </span>

                        </td>

                        <td class="py-3 px-4 border">
                            {{ $user->no_hp ?? '-' }}
                        </td>

                        <td class="py-3 px-4 border">

                            <div class="flex justify-center gap-2">

                                <a
                                    href="{{ route('admin.user.edit', $user->id) }}"
                                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                                    Edit
                                </a>

                                <form
                                    action="{{ route('admin.user.destroy', $user->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus user ini?')">

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                                        Hapus
                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="6" class="py-8 text-center text-gray-500">
                            Belum ada data user.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    <div class="p-4 border-t border-gray-200 bg-gray-50">
        {{ $users->links() }}
    </div>

</div>

@endsection