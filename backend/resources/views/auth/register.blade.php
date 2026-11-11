<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Sistem Peminjaman Alat</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans antialiased">

<div class="min-h-screen flex flex-col items-center justify-center py-8 px-4">

    <!-- Judul -->
    <div class="max-w-2xl w-full mb-4">
        <h1 class="text-2xl font-bold text-gray-800">Daftar Akun Peminjam</h1>
        <p class="text-sm text-gray-500">Buat akun untuk mengajukan peminjaman alat.</p>
    </div>

    <div class="max-w-2xl w-full bg-white rounded-lg shadow-sm border border-gray-200 p-6">

        <form action="{{ route('register') }}" method="POST" enctype="multipart/form-data">

            @csrf

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2">
                    Foto Profil
                </label>
                <input
                    type="file"
                    name="foto_profile"
                    accept="image/*"
                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                >
                <p class="text-xs text-gray-500 mt-1">Format: JPG, JPEG, PNG. Maksimal 2MB. (Opsional)</p>
                @error('foto_profile')
                    <span class="text-red-500 text-sm block mt-1">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2">
                    Nama
                </label>
                <input
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    placeholder="Masukkan nama lengkap"
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
                    value="{{ old('email') }}"
                    required
                    placeholder="Masukkan email"
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
                    required
                    placeholder="Minimal 8 karakter"
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
                    Konfirmasi Password
                </label>
                <input
                    type="password"
                    name="password_confirmation"
                    required
                    placeholder="Ulangi password"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2">
                    No. HP
                </label>
                <input
                    type="text"
                    name="no_hp"
                    value="{{ old('no_hp') }}"
                    placeholder="Masukkan nomor HP"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                @error('no_hp')
                    <span class="text-red-500 text-sm">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-semibold mb-2">
                    Alamat
                </label>
                <textarea
                    name="alamat"
                    rows="3"
                    placeholder="Masukkan alamat lengkap"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >{{ old('alamat') }}</textarea>
                @error('alamat')
                    <span class="text-red-500 text-sm">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('login') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg font-semibold transition">
                    Batal
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold transition">
                    Daftar
                </button>
            </div>

        </form>
    </div>

    <p class="text-sm text-gray-600 mt-4">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="text-blue-600 font-semibold hover:underline">Masuk</a>
    </p>

</div>

</body>
</html>