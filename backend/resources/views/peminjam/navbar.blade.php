@php
    $menu = [
        ['label' => 'Dashboard',      'route' => 'peminjam.dashboard',  'match' => 'peminjam.dashboard'],
        ['label' => 'Katalog Alat',   'route' => 'peminjam.katalog',    'match' => 'peminjam.katalog'],
        ['label' => 'Riwayat Pinjam', 'route' => 'peminjam.riwayat',    'match' => 'peminjam.riwayat'],
        ['label' => 'Akun Saya',      'route' => 'peminjam.akun.index', 'match' => 'peminjam.akun.*'],
    ];
@endphp

<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('peminjam.dashboard') }}">Panel Peminjam</a>
        <div class="d-flex align-items-center">
            @foreach ($menu as $item)
                <a href="{{ route($item['route']) }}"
                   class="btn btn-sm me-2 {{ request()->routeIs($item['match']) ? 'btn-light text-primary fw-bold' : 'btn-outline-light' }}">
                    {{ $item['label'] }}
                </a>
            @endforeach
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
            </form>
        </div>
    </div>
</nav>