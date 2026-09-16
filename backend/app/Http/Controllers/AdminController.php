<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Kategori;
use App\Models\User;
use App\Models\LogAktivitas;
use App\Models\Peminjaman;
use App\Models\DetilPinjam;
use App\Models\Pengembalian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;
use Exception;

class AdminController extends Controller
{
    // =========================================================
    // HELPER METHODS
    // =========================================================

    /**
     * Helper to log system activity cleanly.
     */
    private function logActivity(string $activity): void
    {
        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => $activity,
        ]);
    }

    // =========================================================
    // DASHBOARD
    // =========================================================

    public function index(): View
    {
        $logs = LogAktivitas::with('user')->latest()->take(10)->get();
        return view('admin.dashboard', compact('logs'));
    }

    // =========================================================
    // CRUD ALAT
    // =========================================================

    public function indexAlat(Request $request): View
    {
        $search = $request->input('search');

        $alats = Alat::with('kategori')
            ->when($search, function ($query, $search) {
                $query->where('nama_alat', 'like', "%{$search}%")
                    ->orWhere('status_kondisi', 'like', "%{$search}%")
                    ->orWhereHas('kategori', fn($q) => $q->where('nama_kategori', 'like', "%{$search}%"));
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.alat.index', compact('alats', 'search'));
    }

    public function createAlat(): View
    {
        $kategoris = Kategori::all();
        return view('admin.alat.create', compact('kategoris'));
    }

    public function storeAlat(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama_alat' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori,id',
            'stok' => 'required|integer|min:0',
            'status_kondisi' => 'required|string|max:100',
            'deskripsi' => 'nullable|string|max:1000',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('alat', 'public');
            $data['gambar'] = 'storage/' . $path;
        }

        Alat::create($data);
        $this->logActivity('Menambahkan alat baru: ' . $request->nama_alat);

        return redirect()->route('admin.alat.index')->with('success', 'Data alat berhasil ditambahkan.');
    }

    public function editAlat(int $id): View
    {
        $alat = Alat::findOrFail($id);
        $kategoris = Kategori::all();
        return view('admin.alat.edit', compact('alat', 'kategoris'));
    }

    public function updateAlat(Request $request, int $id): RedirectResponse
    {
        $alat = Alat::findOrFail($id);

        $data = $request->validate([
            'nama_alat' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori,id',
            'stok' => 'required|integer|min:0',
            'status_kondisi' => 'required|string|max:100',
            'deskripsi' => 'nullable|string|max:1000',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('gambar')) {
            if ($alat->gambar) {
                Storage::disk('public')->delete(str_replace('storage/', '', $alat->gambar));
            }
            $path = $request->file('gambar')->store('alat', 'public');
            $data['gambar'] = 'storage/' . $path;
        }

        $alat->update($data);
        $this->logActivity('Memperbarui data alat: ' . $alat->nama_alat);

        return redirect()->route('admin.alat.index')->with('success', 'Data alat berhasil diperbarui.');
    }

    public function destroyAlat(int $id): RedirectResponse
    {
        $alat = Alat::findOrFail($id);

        if ($alat->gambar) {
            Storage::disk('public')->delete(str_replace('storage/', '', $alat->gambar));
        }

        $namaAlat = $alat->nama_alat;
        $alat->delete();

        $this->logActivity('Menghapus alat: ' . $namaAlat);

        return redirect()->route('admin.alat.index')->with('success', 'Data alat berhasil dihapus.');
    }

    // =========================================================
    // CRUD USER
    // =========================================================

    public function indexUser(Request $request): View
    {
        $search = $request->input('search');

        $users = User::when($search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('role', 'like', "%{$search}%");
        })
        ->latest()
        ->paginate(10)
        ->withQueryString();

        return view('admin.user.index', compact('users', 'search'));
    }

    public function createUser(): View
    {
        return view('admin.user.create');
    }

    public function storeUser(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,petugas,peminjam',
            'no_hp' => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'no_hp' => $request->no_hp,
        ]);

        $this->logActivity("Menambahkan pengguna baru: {$user->name} ({$user->role})");

        return redirect()->route('admin.user.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function editUser(int $id): View
    {
        $user = User::findOrFail($id);
        return view('admin.user.edit', compact('user'));
    }

    public function updateUser(Request $request, int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'role' => 'required|in:admin,petugas,peminjam',
            'no_hp' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8',
        ]);

        $data = $request->only(['name', 'email', 'role', 'no_hp']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        $this->logActivity('Memperbarui data pengguna: ' . $user->name);

        return redirect()->route('admin.user.index')->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroyUser(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        $namaUser = $user->name;
        $user->delete();

        $this->logActivity('Menghapus pengguna: ' . $namaUser);

        return redirect()->route('admin.user.index')->with('success', 'User berhasil dihapus.');
    }

    // =========================================================
    // CRUD KATEGORI
    // =========================================================

    public function indexKategori(Request $request): View
    {
        $search = $request->input('search');

        $kategoris = Kategori::when($search, fn($q) => $q->where('nama_kategori', 'like', "%{$search}%"))
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('admin.kategori.index', compact('kategoris', 'search'));
    }

    public function createKategori(): View
    {
        return view('admin.kategori.create');
    }

    public function storeKategori(Request $request): RedirectResponse
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori,nama_kategori',
        ]);

        Kategori::create(['nama_kategori' => $request->nama_kategori]);
        $this->logActivity('Menambahkan kategori baru: ' . $request->nama_kategori);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function editKategori(int $id): View
    {
        $kategori = Kategori::findOrFail($id);
        return view('admin.kategori.edit', compact('kategori'));
    }

    public function updateKategori(Request $request, int $id): RedirectResponse
    {
        $kategori = Kategori::findOrFail($id);

        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori,nama_kategori,' . $id,
        ]);

        $kategori->update(['nama_kategori' => $request->nama_kategori]);
        $this->logActivity('Memperbarui kategori: ' . $request->nama_kategori);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroyKategori(int $id): RedirectResponse
    {
        $kategori = Kategori::findOrFail($id);

        if ($kategori->alats()->exists()) {
            return redirect()->route('admin.kategori.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh data alat.');
        }

        $namaKategori = $kategori->nama_kategori;
        $kategori->delete();

        $this->logActivity('Menghapus kategori: ' . $namaKategori);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }

    // =========================================================
    // CRUD PEMINJAMAN
    // =========================================================

    public function indexPeminjaman(Request $request): View
    {
        $search = $request->input('search');

        $peminjamans = Peminjaman::with(['user', 'detailPinjams.alat'])
            ->when($search, function ($query, $search) {
                $query->where('status', 'like', "%{$search}%")
                    ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"));
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.peminjaman.index', compact('peminjamans', 'search'));
    }

    public function createPeminjaman(): View
    {
        $users = User::where('role', 'peminjam')->get();
        $alats = Alat::where('stok', '>', 0)->get();

        return view('admin.peminjaman.create', compact('users', 'alats'));
    }

    public function storePeminjaman(Request $request): RedirectResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tgl_pinjam' => 'required|date',
            'tgl_kembali_plan' => 'required|date|after_or_equal:tgl_pinjam',
            'alat_id' => 'required|array',
            'alat_id.*' => 'exists:alat,id',
            'jumlah' => 'required|array',
            'jumlah.*' => 'integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $peminjaman = Peminjaman::create([
                'user_id' => $request->user_id,
                'tgl_pinjam' => $request->tgl_pinjam,
                'tgl_kembali_plan' => $request->tgl_kembali_plan,
                'status' => 'diajukan',
            ]);

            foreach ($request->alat_id as $index => $alatId) {
                $jumlahPinjam = $request->jumlah[$index];
                $alat = Alat::findOrFail($alatId);

                if ($alat->stok < $jumlahPinjam) {
                    throw new \Exception("Stok alat {$alat->nama_alat} tidak mencukupi.");
                }

                DetilPinjam::create([
                    'peminjaman_id' => $peminjaman->id,
                    'alat_id' => $alatId,
                    'jumlah' => $jumlahPinjam,
                ]);
            }

            DB::commit();
            $this->logActivity('Mengajukan peminjaman baru ID: #' . $peminjaman->id);

            return redirect()->route('admin.peminjaman.index')->with('success', 'Data peminjaman berhasil diajukan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function updateStatusPeminjaman(Request $request, int $id): RedirectResponse
    {
        $peminjaman = Peminjaman::with('detailPinjams.alat')->findOrFail($id);

        $request->validate([
            'status' => 'required|string',
        ]);

        // Standardize status strings to lowercase
        $statusLama = strtolower($peminjaman->status);
        $statusBaru = strtolower($request->status);

        DB::beginTransaction();

        try {
            // Deduct stock when moving to 'dipinjam'
            if ($statusLama !== 'dipinjam' && $statusBaru === 'dipinjam') {
                foreach ($peminjaman->detailPinjams as $detail) {
                    if ($detail->alat->stok < $detail->jumlah) {
                        throw new \Exception("Stok alat {$detail->alat->nama_alat} tidak mencukupi.");
                    }
                    $detail->alat->decrement('stok', $detail->jumlah);
                }
            }
            // Restore stock when completing or returning items
            elseif (in_array($statusLama, ['dipinjam', 'telat']) && in_array($statusBaru, ['selesai', 'dikembalikan'])) {
                foreach ($peminjaman->detailPinjams as $detail) {
                    $detail->alat->increment('stok', $detail->jumlah);
                }
            }

            $peminjaman->status = $statusBaru;
            $peminjaman->save();

            DB::commit();
            $this->logActivity("Memperbarui status peminjaman #{$peminjaman->id} menjadi: {$statusBaru}");

            return redirect()->route('admin.peminjaman.index')->with('success', 'Status peminjaman berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroyPeminjaman(int $id): RedirectResponse
    {
        $peminjaman = Peminjaman::with('detailPinjams.alat')->findOrFail($id);

        if (strtolower($peminjaman->status) === 'dipinjam') {
            foreach ($peminjaman->detailPinjams as $detail) {
                $detail->alat->increment('stok', $detail->jumlah);
            }
        }

        $peminjaman->delete();
        $this->logActivity('Menghapus peminjaman ID: #' . $id);

        return redirect()->route('admin.peminjaman.index')->with('success', 'Data peminjaman berhasil dihapus.');
    }

    // =========================================================
    // CRUD PENGEMBALIAN
    // =========================================================

    public function indexPengembalian(Request $request): View
    {
        $search = $request->input('search');

        $pengembalians = Pengembalian::with(['peminjaman.user', 'peminjaman.detailPinjams.alat', 'petugas'])
            ->when($search, function ($query, $search) {
                $query->whereHas('peminjaman.user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.pengembalian.index', compact('pengembalians', 'search'));
    }

    public function createPengembalian(): View
    {
        $peminjamanAktif = Peminjaman::with('user')
            ->where('status', 'dipinjam')
            ->latest()
            ->get();

        return view('admin.pengembalian.create', compact('peminjamanAktif'));
    }

    public function storePengembalian(Request $request): RedirectResponse
    {
        $request->validate([
            'peminjaman_id' => 'required|exists:peminjaman,id',
            'tgl_kembali' => 'required|date',
            'kondisi_kembali' => 'required|string',
            'denda' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $peminjaman = Peminjaman::with('detailPinjams.alat')
                    ->lockForUpdate()->findOrFail($request->peminjaman_id);

                if (strtolower($peminjaman->status) !== 'dipinjam') {
                    throw new Exception("Peminjaman ini berstatus '{$peminjaman->status}', bukan 'dipinjam'.");
                }

                $tglKembaliPlan = Carbon::parse($peminjaman->tgl_kembali_plan)->startOfDay();
                $hariIni = Carbon::parse($request->tgl_kembali)->startOfDay();
                $statusBaru = $hariIni->greaterThan($tglKembaliPlan) ? 'telat' : 'dikembalikan';

                Pengembalian::create([
                    'peminjaman_id' => $peminjaman->id,
                    'tgl_kembali' => $request->tgl_kembali,
                    'kondisi_kembali' => $request->kondisi_kembali,
                    'denda' => $request->denda ?? 0,
                    'petugas_id' => auth()->id(),
                ]);

                $peminjaman->update(['status' => $statusBaru]);

                foreach ($peminjaman->detailPinjams as $detail) {
                    Alat::where('id', $detail->alat_id)->increment('stok', $detail->jumlah);
                }
            });

            $this->logActivity('Memproses pengembalian peminjaman ID: #' . $request->peminjaman_id);

            return redirect()->route('admin.pengembalian.index')->with('success', 'Pengembalian berhasil diproses!');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroyPengembalian(int $id): RedirectResponse
    {
        try {
            DB::transaction(function () use ($id) {
                $pengembalian = Pengembalian::findOrFail($id);
                $peminjaman = Peminjaman::with('detailPinjams.alat')->findOrFail($pengembalian->peminjaman_id);

                foreach ($peminjaman->detailPinjams as $detail) {
                    Alat::where('id', $detail->alat_id)->decrement('stok', $detail->jumlah);
                }

                $peminjaman->update(['status' => 'dipinjam']);
                $pengembalian->delete();
            });

            $this->logActivity('Menghapus data pengembalian ID: #' . $id);

            return redirect()->route('admin.pengembalian.index')->with('success', 'Data pengembalian berhasil dihapus.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}