<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Alat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // Tambahkan ini di bagian atas agar tidak error saat memanggil Carbon

class PetugasController extends Controller
{
    // Menampilkan daftar pengajuan peminjaman dari siswa/peminjam
    public function indexPeminjaman(Request $request)
    {
        $search = $request->input('search');

        $peminjamans = Peminjaman::with(['user', 'detailPinjams.alat'])
            ->where('status', 'diajukan')
            ->when($search, function ($query, $search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();

        return view('petugas.peminjaman.index', compact('peminjamans', 'search'));
    }

    // Menampilkan daftar peminjaman yang sedang dipinjam (siap dikembalikan)
    public function indexPengembalian(Request $request)
    {
        $search = $request->input('search');

        $pengembalian = Peminjaman::with(['user', 'detailPinjams.alat'])
            ->where('status', 'dipinjam')
            ->when($search, function ($query, $search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();

        return view('petugas.pengembalian.index', compact('pengembalian', 'search'));
    }

    // Menyetujui Peminjaman (Mengubah status & mengurangi stok alat)
    public function setujuiPeminjaman($id)
    {
        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::with('detailPinjams')->findOrFail($id);
            $peminjaman->update(['status' => 'dipinjam']);

            // Kurangi stok alat secara otomatis
            foreach ($peminjaman->detailPinjams as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);
                $alat->stok -= $detail->jumlah;
                $alat->save();
            }

            DB::commit();
            return redirect()->back()->with('success', 'Peminjaman disetujui dan stok alat dikurangi.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Menolak Peminjaman (Menghapus pengajuan agar siswa bisa mengajukan ulang)
    public function tolakPeminjaman($id)
    {
        try {
            $peminjaman = Peminjaman::findOrFail($id);

            // Pastikan statusnya memang masih diajukan
            if ($peminjaman->status == 'diajukan') {
                $peminjaman->delete();
                return redirect()->back()->with('success', 'Pengajuan peminjaman berhasil ditolak.');
            }

            return redirect()->back()->with('error', 'Status peminjaman sudah berubah.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

// Memproses Pengembalian Alat (Mengembalikan stok alat & ubah status ke 'dikembalikan')
    public function prosesPengembalian($id)
    {
        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::with('detailPinjams')->findOrFail($id);

            if ($peminjaman->status !== 'dipinjam') {
                return redirect()->back()->with('error', 'Status peminjaman tidak valid atau sudah dikembalikan.');
            }

            // 1. Update status peminjaman menjadi 'dikembalikan'
            $peminjaman->update([
                'status' => 'dikembalikan',
                'tgl_kembali' => now() 
            ]);

            // 2. Kembalikan stok alat secara otomatis ke database
            foreach ($peminjaman->detailPinjams as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);
                $alat->stok += $detail->jumlah;
                $alat->save();
            }

            // 3. Catat riwayat ke tabel pengembalian menggunakan Eloquent Model (Pengembalian dengan P besar)
            Pengembalian::create([
                'peminjaman_id'   => $peminjaman->id,
                'tgl_kembali'     => now(),
                'kondisi_kembali' => 'Baik', 
                'denda'           => 0,
                'petugas_id'      => auth()->id()
            ]);

                DB::commit();
        
                // Disamakan dengan nama route index petugas Anda agar tidak mendatangkan error route not found
                return redirect()->route('petugas.pengembalian.index')->with('success', 'Pengembalian berhasil diproses dan stok alat telah bertambah.');
        
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    // Menampilkan Cetak Laporan Peminjaman
    public function indexlaporan(Request $request)
    {
        $tglMulai   = $request->input('tgl_mulai');
        $tglSelesai = $request->input('tgl_selesai');
        $status     = $request->input('status');

        $peminjamans = Peminjaman::with(['user', 'detailPinjams.alat'])
            ->when($tglMulai, function ($query, $tglMulai) {
                return $query->whereDate('tgl_pinjam', '>=', $tglMulai);
            })
            ->when($tglSelesai, function ($query, $tglSelesai) {
                return $query->whereDate('tgl_pinjam', '<=', $tglSelesai);
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->get();

        return view('petugas.laporan.index', compact('peminjamans'));
    }
}
