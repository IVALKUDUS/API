<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('header_title', View::getSection('title'))</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-sans antialiased">

<div class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside class="w-64 bg-gray-900 text-white hidden md:flex flex-col shrink-0">

        <div class="p-4 text-xl font-bold border-b border-gray-800">
            Peminjaman Alat
        </div>

        <nav class="flex-1 p-4 space-y-2">

            <!-- MENU KHUSUS ADMIN -->
            @if(auth()->user()->role === 'admin')

                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}"
                    class="block px-4 py-2 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800 text-white font-medium shadow' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} transition">
                    Dashboard
                </a>

                <!-- Kelola User -->
                <a href="{{ route('admin.user.index') }}"
                    class="block px-4 py-2 rounded-lg {{ request()->routeIs('admin.user.*') ? 'bg-gray-800 text-white font-medium shadow' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} transition">
                    Kelola User
                </a>

                <!-- Kelola Kategori -->
                <a href="{{ route('admin.kategori.index') }}"
                    class="block px-4 py-2 rounded-lg {{ request()->routeIs('admin.kategori.*') ? 'bg-gray-800 text-white font-medium shadow' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} transition">
                    Kelola Kategori
                </a>

                <!-- Kelola Alat -->
                <a href="{{ route('admin.alat.index') }}"
                    class="block px-4 py-2 rounded-lg {{ request()->routeIs('admin.alat.*') ? 'bg-gray-800 text-white font-medium shadow' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} transition">
                    Kelola Alat
                </a>

                <!-- Kelola Peminjaman -->
                <a href="{{ route('admin.peminjaman.index') }}"
                    class="block px-4 py-2 rounded-lg {{ request()->routeIs('admin.peminjaman.*') ? 'bg-gray-800 text-white font-medium shadow' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} transition">
                    Kelola Peminjaman
                </a>

                <!-- Kelola Pengembalian -->
                <a href="{{ route('admin.pengembalian.index') }}"
                    class="block px-4 py-2 rounded-lg text-gray-400 hover:bg-gray-800 hover:text-white transition">
                    Kelola Pengembalian
                </a>

            @endif


            <!-- MENU KHUSUS PETUGAS -->
            @if(auth()->user()->role === 'petugas')

                <!-- Persetujuan Peminjaman -->
                <a href="{{ route('petugas.peminjaman.index') }}"
                    class="block px-4 py-2 rounded-lg {{ request()->routeIs('petugas.peminjaman.*') ? 'bg-gray-800 text-white font-medium shadow' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} transition">
                    Persetujuan Peminjaman
                </a>

                <!-- Pemantauan Pengembalian -->
                <a href="{{ route('petugas.pengembalian.index') }}"
                    class="block px-4 py-2 rounded-lg {{ request()->routeIs('petugas.pengembalian.*') ? 'bg-gray-800 text-white font-medium shadow' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} transition">
                    Pemantauan Pengembalian
                </a>

                <!-- Cetak Laporan -->
                <a href="{{ route('petugas.laporan.index') }}"
                    class="block px-4 py-2 rounded-lg {{ request()->routeIs('petugas.laporan.*') ? 'bg-gray-800 text-white font-medium shadow' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} transition">
                    Cetak Laporan
                </a>

            @endif

        </nav>

        <div class="p-4 border-t border-gray-800 text-sm">
            Login sebagai
            <span class="font-semibold">
                {{ auth()->user()->name ?? 'User' }}
            </span>
        </div>

    </aside>

    <!-- Content -->
    <div class="flex-1 flex flex-col overflow-y-auto">

        <header class="bg-white shadow-sm px-6 h-16 flex items-center justify-between">

            <h1 class="text-lg font-semibold text-gray-800">
                @yield('header-title', View::getSection('title'))
            </h1>

            <form action="{{ route('logout') }}" method="POST">
                @csrf

                <button
                    type="submit"
                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-semibold">
                    Logout
                </button>

            </form>

        </header>

        <main class="flex-1 p-6">

            @yield('content')

        </main>

    </div>

</div>

</body>
</html>