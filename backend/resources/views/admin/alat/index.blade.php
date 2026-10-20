@extends('layouts.app')

@section('title','Kelola Alat - Panel Admin')
@section('header-title','Manajemen Data Alat')

@section('content')

@if(session('success'))
<div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg shadow-sm text-sm">
    {{ session('success') }}
</div>
@endif

<div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">

    <div class="p-6 border-b border-gray-200 bg-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">

        <div class="flex items-center gap-3 w-full md:w-auto">

            <form action="{{ route('admin.alat.index') }}" method="GET" class="flex w-full md:w-80">

                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari nama alat, kategori..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500">

                <button class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-r-lg">
                    Cari
                </button>

            </form>

            @if(request('search'))
                <a href="{{ route('admin.alat.index') }}"
                   class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-2 rounded-lg">
                    Reset
                </a>
            @endif

        </div>

        <a href="{{ route('admin.alat.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold whitespace-nowrap">
            + Tambah Alat
        </a>

    </div>

    <div class="overflow-x-auto">

        <table class="w-full text-left border-collapse">

            <thead>

            <tr class="bg-gray-100 text-gray-600 text-xs uppercase">

                <th class="py-3 px-4">Gambar</th>
                <th class="py-3 px-4">Nama Alat</th>
                <th class="py-3 px-4">Kategori</th>
                <th class="py-3 px-4">Stok</th>
                <th class="py-3 px-4">Kondisi</th>
                <th class="py-3 px-4 text-center">Aksi</th>

            </tr>

            </thead>

            <tbody class="text-sm">

            @forelse($alats as $alat)

                <tr class="border-b hover:bg-gray-50">

                    <td class="py-3 px-4">
                        @if($alat->gambar)
                            <img src="{{ asset($alat->gambar) }}" class="w-12 h-12 object-cover rounded-lg border">
                        @else
                            <span class="text-gray-400 italic">Tidak ada</span>
                        @endif
                    </td>

                    <td class="py-3 px-4 font-medium">{{ $alat->nama_alat }}</td>

                    <td class="py-3 px-4">{{ $alat->kategori->nama_kategori ?? '-' }}</td>

                    <td class="py-3 px-4">{{ $alat->stok }}</td>

                    <td class="py-3 px-4">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold
                        {{ $alat->status_kondisi=='Baik' ? 'bg-emerald-100 text-emerald-800':'bg-yellow-100 text-yellow-800' }}">
                            {{ $alat->status_kondisi }}
                        </span>
                    </td>

                    <td class="py-3 px-4 text-center">

                        <div class="flex justify-center gap-2">

                            <a href="{{ route('admin.alat.edit',$alat->id) }}"
                               class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1 rounded text-xs">
                                Edit
                            </a>

                            <form action="{{ route('admin.alat.destroy',$alat->id) }}" method="POST"
                                  onsubmit="return confirm('Yakin ingin menghapus alat ini?')">

                                @csrf
                                @method('DELETE')

                                <button class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs">
                                    Hapus
                                </button>

                            </form>

                        </div>

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="6" class="py-8 text-center text-gray-500">
                        Belum ada data alat.
                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>

    <div class="p-4 border-t bg-gray-50">

        {{ $alats->links() }}

    </div>

</div>

@endsection