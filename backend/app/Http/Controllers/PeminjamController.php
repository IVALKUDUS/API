<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\DetilPinjam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeminjamController extends Controller
{
    // Halaman katalog alat
    public function katalog()
    {
        $alat = Alat::with('kategori')->where('stok', '>', 0)->get();

        return view('peminjam.katalog', compact('alat'));
    }

    // Proses pengajuan peminjaman
    public function ajukan(Request $request)
    {
        $data = $request->validate([
            'tgl_kembali_plan' => 'required|date|after_or_equal:today',
            'alat_id'          => 'required|array|min:1',
            'alat_id.*'        => 'exists:alat,id',
            'jumlah'           => 'required|array',
            'jumlah.*'         => 'nullable|integer|min:1',
        ], [
            'alat_id.required' => 'Pilih minimal satu alat.',
        ]);

        try {
            DB::transaction(function () use ($data) {
                $peminjaman = Peminjaman::create([
                    'user_id'          => auth()->id(),
                    'tgl_pinjam'       => now()->toDateString(),
                    'tgl_kembali_plan' => $data['tgl_kembali_plan'],
                    'status'           => 'diajukan',
                ]);

                foreach (array_unique($data['alat_id']) as $id) {
                    $jumlah = (int) ($data['jumlah'][$id] ?? 1);
                    $alat   = Alat::lockForUpdate()->findOrFail($id);

                    if ($alat->stok < $jumlah) {
                        throw new \Exception("Stok alat '{$alat->nama_alat}' tidak mencukupi (sisa {$alat->stok}).");
                    }

                    DetilPinjam::create([
                        'peminjaman_id' => $peminjaman->id,
                        'alat_id'       => $id,
                        'jumlah'        => $jumlah,
                    ]);
                }
            });
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('peminjam.riwayat')
            ->with('success', 'Peminjaman berhasil diajukan. Menunggu persetujuan petugas.');
    }

    // Halaman riwayat peminjaman milik user yang login
    public function riwayat()
    {
        $riwayat = Peminjaman::with(['detailPinjam.alat', 'pengembalian'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('peminjam.riwayat', compact('riwayat'));
    }

    // Peminjam mengajukan pengembalian alat (menunggu konfirmasi petugas)
    public function ajukanPengembalian(Request $request, $id)
    {
        $request->validate([
            'catatan_kembali' => 'nullable|string|max:255',
        ]);

        // Hanya boleh mengajukan untuk peminjaman milik sendiri
        $peminjaman = Peminjaman::where('user_id', auth()->id())->findOrFail($id);

        if ($peminjaman->status !== 'dipinjam') {
            return back()->with('error', 'Hanya peminjaman berstatus "Dipinjam" yang dapat diajukan pengembaliannya.');
        }

        if ($peminjaman->tgl_pengajuan_kembali) {
            return back()->with('error', 'Pengembalian untuk peminjaman ini sudah diajukan. Menunggu konfirmasi petugas.');
        }

        $peminjaman->update([
            'tgl_pengajuan_kembali' => now(),
            'catatan_kembali'       => $request->input('catatan_kembali'),
        ]);

        return redirect()->route('peminjam.riwayat')
            ->with('success', 'Pengajuan pengembalian terkirim. Silakan serahkan alat ke petugas untuk dikonfirmasi.');
    }
}