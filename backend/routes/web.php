<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\PeminjamController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\API\PeminjamanController; // Disesuaikan dengan PeminjamanController di modul

// Home / Landing Page
Route::get('/', function () {
    return view('welcome');
});

// Route Guest (Belum Login)
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Route Logout (Harus sudah login)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ==========================================
// ROUTE ADMIN
// ==========================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::redirect('/', '/admin/dashboard')->name('index');
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

    // CRUD Kategori
    Route::get('/kategori', [AdminController::class, 'indexKategori'])->name('kategori.index');
    Route::get('/kategori/create', [AdminController::class, 'createKategori'])->name('kategori.create');
    Route::post('/kategori', [AdminController::class, 'storeKategori'])->name('kategori.store');
    Route::get('/kategori/{id}/edit', [AdminController::class, 'editKategori'])->name('kategori.edit');
    Route::put('/kategori/{id}', [AdminController::class, 'updateKategori'])->name('kategori.update');
    Route::delete('/kategori/{id}', [AdminController::class, 'destroyKategori'])->name('kategori.destroy');

    // CRUD Alat
    Route::get('/alat', [AdminController::class, 'indexAlat'])->name('alat.index');
    Route::get('/alat/create', [AdminController::class, 'createAlat'])->name('alat.create');
    Route::post('/alat', [AdminController::class, 'storeAlat'])->name('alat.store');
    Route::get('/alat/{id}/edit', [AdminController::class, 'editAlat'])->name('alat.edit');
    Route::put('/alat/{id}', [AdminController::class, 'updateAlat'])->name('alat.update');
    Route::delete('/alat/{id}', [AdminController::class, 'destroyAlat'])->name('alat.destroy');

    // CRUD User
    Route::get('/users', [AdminController::class, 'indexUser'])->name('user.index');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('user.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('user.store');
    Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('user.edit');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('user.update');
    Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])->name('user.destroy');

    // CRUD Peminjaman
    Route::get('/peminjaman', [AdminController::class, 'indexPeminjaman'])->name('peminjaman.index');
    Route::get('/peminjaman/create', [AdminController::class, 'createPeminjaman'])->name('peminjaman.create');
    Route::post('/peminjaman', [AdminController::class, 'storePeminjaman'])->name('peminjaman.store');
    Route::put('/peminjaman/{id}/status', [AdminController::class, 'updateStatusPeminjaman'])->name('peminjaman.updateStatus');
    Route::delete('/peminjaman/{id}', [AdminController::class, 'destroyPeminjaman'])->name('peminjaman.destroy');
    
    Route::get('/peminjaman-api', [PeminjamanController::class, 'index'])->name('api.peminjaman.index');
    Route::get('/peminjaman-api/{peminjaman}', [PeminjamanController::class, 'show'])->name('api.peminjaman.show');
    Route::post('/peminjaman-api/{peminjaman}/approve', [PeminjamanController::class, 'approve'])->name('api.peminjaman.approve');
    Route::put('/peminjaman-api/{peminjaman}', [PeminjamanController::class, 'update'])->name('api.peminjaman.update');
    Route::delete('/peminjaman-api/{peminjaman}', [PeminjamanController::class, 'destroy'])->name('api.peminjaman.destroy');

    // CRUD Pengembalian
    Route::get('/pengembalian', [PengembalianController::class, 'index']);
    Route::get('/pengembalian/{pengembalian}', [PengembalianController::class, 'show']);
    Route::put('/pengembalian/{pengembalian}', [PengembalianController::class, 'update']);
    Route::delete('/pengembalian/{pengembalian}', [PengembalianController::class, 'destroy']);
    
    // CRUD Log Aktivitas
    Route::get('/log-aktivitas', [LogAktivitasController::class, 'index']);
});

// ==========================================
// ROUTE PETUGAS
// ==========================================
Route::middleware(['auth', 'role:petugas,admin'])->prefix('petugas')->name('petugas.')->group(function () {

    // Peminjaman & Persetujuan
    Route::get('/peminjaman', [PetugasController::class, 'indexPeminjaman'])->name('peminjaman.index');
    Route::post('/peminjaman/{id}/setujui', [PetugasController::class, 'setujuiPeminjaman'])->name('peminjaman.setujui');
    Route::post('/peminjaman/{id}/tolak', [PetugasController::class, 'tolakPeminjaman'])->name('peminjaman.tolak');
    Route::put('/peminjaman/{id}/status', [AdminController::class, 'updateStatusPeminjaman'])->name('peminjaman.updateStatus');

    // Route Approve Peminjaman
    Route::post('/peminjaman-api/{peminjaman}/approve', [PeminjamanController::class, 'approve'])->name('api.peminjaman.approve');

    // Pengembalian
    Route::get('/pengembalian', [PetugasController::class, 'indexPengembalian'])->name('pengembalian.index');
    Route::post('/pengembalian/{id}/proses', [PetugasController::class, 'prosesPengembalian'])->name('pengembalian.proses');
    Route::post('/pengembalian', [PengembalianController::class, 'store']);

    // Laporan
    Route::get('/laporan', function() {
        return 'Halaman Cetak Laporan dalam pengembangan.';
    })->name('laporan.index');

});

// ==========================================
// ROUTE PEMINJAM
// ==========================================
Route::middleware(['auth', 'role:peminjam'])->prefix('peminjam')->name('peminjam.')->group(function () {
    
    Route::get('/katalog', function () {
        return view('peminjam.katalog');
    })->name('katalog');

    // Route Store & Riwayat Peminjaman
    Route::post('/peminjaman-api', [PeminjamanController::class, 'store'])->name('api.peminjaman.store');
    Route::get('/riwayat-pinjam', [PeminjamanController::class, 'riwayat'])->name('api.peminjaman.riwayat');
});