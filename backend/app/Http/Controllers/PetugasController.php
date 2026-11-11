<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Alat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon; // Tambahkan ini di bagian atas agar tidak error saat memanggil Carbon

class PetugasController extends Controller
{
    //Dashboard petugas
    public function dashboard()
    {
        $hariIni = now()->toDateString();

        $stats = [
            'menunggu'          => Peminjaman::where('status', 'diajukan')->count(),
            'dipinjam'          => Peminjaman::where('status', 'dipinjam')->count(),
            'ajuan_kembali'     => Peminjaman::where('status', 'dipinjam')->whereNotNull('tgl_pengajuan_kembali')->count(),
            'jatuh_tempo'       => Peminjaman::where('status', 'dipinjam')->whereDate('tgl_kembali_plan', '<', $hariIni)->count(),
            'diproses_hari_ini' => Pengembalian::where('petugas_id', auth()->id())->whereDate('tgl_kembali', $hariIni)->count(),
        ];

        $pengajuanBaru = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->where('status', 'diajukan')->latest()->take(5)->get();

        $jatuhTempo = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->where('status', 'dipinjam')->orderBy('tgl_kembali_plan')->take(5)->get();

        $stokMenipis = Alat::where('stok', '<=', 3)->orderBy('stok')->take(5)->get();

        return view('petugas.dashboard', compact('stats', 'pengajuanBaru', 'jatuhTempo', 'stokMenipis'));
    }
    
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
            ->orderByRaw('tgl_pengajuan_kembali IS NULL') // yang sudah diajukan peminjam tampil paling atas
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


    // Query laporan (dipakai bersama oleh tampilan web & PDF)
    private function queryLaporan(Request $request)
    {
        $tglMulai   = $request->input('tgl_mulai');
        $tglSelesai = $request->input('tgl_selesai');
        $status     = $request->input('status');

        return Peminjaman::with(['user', 'detailPinjams.alat'])
            ->when($tglMulai, fn($q, $v) => $q->whereDate('tgl_pinjam', '>=', $v))
            ->when($tglSelesai, fn($q, $v) => $q->whereDate('tgl_pinjam', '<=', $v))
            ->when($status, fn($q, $v) => $q->where('status', $v))
            ->latest()
            ->get();
    }

    // Menampilkan Halaman Laporan Peminjaman
    public function indexlaporan(Request $request)
    {
        $peminjamans = $this->queryLaporan($request);

        return view('petugas.laporan.index', compact('peminjamans'));
    }

    // Cetak Laporan Peminjaman dalam bentuk PDF
    public function cetakLaporanPdf(Request $request)
    {
        $peminjamans = $this->queryLaporan($request);

        $tglMulai   = $request->input('tgl_mulai');
        $tglSelesai = $request->input('tgl_selesai');
        $status     = $request->input('status');
        $ringkasan  = $peminjamans->groupBy('status')->map->count();

        $pdf = Pdf::loadView('petugas.laporan.pdf', compact(
            'peminjamans', 'tglMulai', 'tglSelesai', 'status', 'ringkasan'
        ))->setPaper('a4', 'portrait');

        return $pdf->stream('laporan-peminjaman-' . now()->format('Ymd-His') . '.pdf');
    }
}