<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * Panel akun yang dipakai bersama oleh admin, petugas, dan peminjam.
 * Setiap user hanya bisa mengubah akunnya sendiri (auth()->user()),
 * dan role TIDAK bisa diubah dari sini.
 */
class AkunController extends Controller
{
    /** Halaman utama: hanya MENAMPILKAN informasi akun. */
    public function show(Request $request): View
    {
        return $this->tampil($request, 'show');
    }

    /** Halaman formulir edit profil (dibuka dari tombol "Edit Profil"). */
    public function edit(Request $request): View
    {
        return $this->tampil($request, 'edit');
    }

    /** Halaman formulir ganti password (dibuka dari tombol "Ganti Password"). */
    public function editPassword(Request $request): View
    {
        return $this->tampil($request, 'password');
    }

    private function tampil(Request $request, string $halaman): View
    {
        $user = $request->user();
        $prefix = $this->prefix($request);

        // Peminjam memakai tampilan Bootstrap (sama seperti katalog & riwayat),
        // admin & petugas memakai layout Tailwind yang punya sidebar.
        $folder = $user->role === 'peminjam' ? 'akun.peminjam' : 'akun';

        return view("$folder.$halaman", compact('user', 'prefix'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'no_hp'        => ['nullable', 'string', 'max:20'],
            'alamat'       => ['nullable', 'string', 'max:500'],
            'foto_profile' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        if ($request->hasFile('foto_profile')) {
            $this->hapusFile($user->foto_profile);
            $data['foto_profile'] = $request->file('foto_profile')->store('foto_profile', 'public');
        } else {
            unset($data['foto_profile']);
        }

        $user->update($data);
        $this->log('Memperbarui profil akun sendiri');

        return redirect()->route($this->prefix($request) . '.akun.index')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'password_lama' => ['required', 'current_password'],
            'password'      => ['required', 'confirmed', Password::min(8)],
        ], [
            'password_lama.current_password' => 'Password lama tidak sesuai.',
        ]);

        // Cast 'hashed' pada model User otomatis meng-hash password.
        $request->user()->update(['password' => $request->password]);
        $this->log('Mengganti password akun sendiri');

        return redirect()->route($this->prefix($request) . '.akun.index')
            ->with('success', 'Password berhasil diganti.');
    }

    public function hapusFoto(Request $request): RedirectResponse
    {
        $user = $request->user();

        $this->hapusFile($user->foto_profile);
        $user->update(['foto_profile' => null]);
        $this->log('Menghapus foto profil akun sendiri');

        return redirect()->route($this->prefix($request) . '.akun.edit')
            ->with('success', 'Foto profil dihapus.');
    }

    /** admin | petugas | peminjam — diambil dari nama route yang sedang dipakai. */
    private function prefix(Request $request): string
    {
        return explode('.', $request->route()->getName())[0];
    }

    private function hapusFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    private function log(string $aktivitas): void
    {
        LogAktivitas::create([
            'user_id'   => auth()->id(),
            'aktivitas' => $aktivitas,
        ]);
    }
}
