<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\User;
use App\Models\Peminjaman;
use App\Models\DetailPinjam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeminjamanAdminController extends Controller
{
    // Halaman Form Tambah Peminjaman (Admin)
    public function create()
    {
        // Ambil data user (bisa difilter berdasarkan role jika ada)
        $users = User::all(); 
        
        // Ambil data alat yang stoknya tersedia
        $alats = Alat::where('stok', '>', 0)->get();

        return view('admin.peminjaman.create', compact('users', 'alats'));
    }

    // Process Simpan Peminjaman oleh Admin
    public function store(Request $request)
    {
        $request->validate([
            'user_id'          => 'required|exists:users,id',
            'tgl_pinjam'       => 'required|date',
            'tgl_kembali_plan' => 'required|date|after_or_equal:tgl_pinjam',
            'alat_id'          => 'required|array',
            'alat_id.*'        => 'required|exists:alats,id',
            'jumlah'           => 'required|array',
            'jumlah.*'         => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // 1. Buat Data Header Peminjaman
            $peminjaman = Peminjaman::create([
                'user_id'          => $request->user_id,
                'tgl_pinjam'       => $request->tgl_pinjam,
                'tgl_kembali_plan' => $request->tgl_kembali_plan,
                'status'           => 'dipinjam', // Langsung dipinjam/disetujui karena diinput Admin
            ]);

            // 2. Simpan Detail Alat & Potong Stok Alat
            foreach ($request->alat_id as $index => $alatId) {
                $jumlahPinjam = $request->jumlah[$index];
                $alat = Alat::findOrFail($alatId);

                // Validasi Cek Stok Alat
                if ($alat->stok < $jumlahPinjam) {
                    throw new \Exception("Stok alat '{$alat->nama_alat}' tidak mencukupi (Tersedia: {$alat->stok}).");
                }

                // Simpan ke Detail
                DetailPinjam::create([
                    'peminjaman_id' => $peminjaman->id,
                    'alat_id'       => $alatId,
                    'jumlah'        => $jumlahPinjam,
                ]);

                // Kurangi stok alat
                $alat->decrement('stok', $jumlahPinjam);
            }

            DB::commit();
            return redirect()->route('admin.peminjaman.index')->with('success', 'Transaksi peminjaman berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan transaksi: ' . $e->getMessage());
        }
    }
}