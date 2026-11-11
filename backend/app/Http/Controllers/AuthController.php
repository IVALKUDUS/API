<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Menampilkan Form Login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Memproses Login User
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Redirect berdasarkan Role
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role === 'petugas') {
                return redirect()->route('petugas.dashboard');
            } elseif ($user->role === 'peminjam') {
                return redirect()->route('peminjam.dashboard');
            }

            Auth::logout();
            return redirect()->route('login')->with('error', 'Role tidak dikenali.');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    /**
     * Menampilkan Form Registrasi (khusus peminjam)
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Memproses Registrasi Peminjam Baru
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'no_hp'    => ['nullable', 'string', 'max:20'],
            'alamat'   => ['nullable', 'string', 'max:500'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'foto_profile' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        // Upload foto profil (opsional), sama seperti saat admin menambah user
        $fotoPath = $request->hasFile('foto_profile')
            ? $request->file('foto_profile')->store('foto_profile', 'public')
            : null;

        // Role SELALU 'peminjam' — tidak boleh diambil dari input user.
        // Password di-hash otomatis oleh cast 'hashed' di model User.
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'no_hp'    => $data['no_hp'] ?? null,
            'alamat'   => $data['alamat'] ?? null,
            'foto_profile' => $fotoPath,
            'password' => $data['password'],
            'role'     => 'peminjam',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('peminjam.dashboard')
            ->with('success', 'Registrasi berhasil. Selamat datang, ' . $user->name . '!');
    }

    /**
     * Proses Logout User
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}