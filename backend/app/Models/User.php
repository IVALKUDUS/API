<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'no_hp',
        'alamat',
        'foto_profile',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed', // Laravel otomatis meng-hash teks apapun yang masuk ke properti password!
        ];
    }

    public function peminjaman(): HasMany
    {
        return $this->hasMany(Peminjaman::class);
    }

    public function logAktivitas(): HasMany
    {
        return $this->hasMany(LogAktivitas::class);
    }

    /**
     * Foto profil dipotong persegi + dibulatkan, dikembalikan sebagai data-URI PNG.
     * Dipakai di PDF (dompdf tidak bisa memuat URL storage & tidak memotong
     * gambar dengan border-radius). Mengembalikan null jika tidak ada foto /
     * GD tidak tersedia, sehingga tampilan jatuh ke inisial.
     */
    public function fotoDataUri(int $size = 80): ?string
    {
        static $cache = [];

        if (! $this->foto_profile || ! function_exists('imagecreatefromstring')) {
            return null;
        }

        $key = $this->foto_profile . '|' . $size;
        if (array_key_exists($key, $cache)) {
            return $cache[$key];
        }

        try {
            $disk = Storage::disk('public');
            if (! $disk->exists($this->foto_profile)) {
                return $cache[$key] = null;
            }

            $src = @imagecreatefromstring($disk->get($this->foto_profile));
            if (! $src) {
                return $cache[$key] = null;
            }

            $w = imagesx($src);
            $h = imagesy($src);
            $side = min($w, $h);

            $dst = imagecreatetruecolor($size, $size);
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            $transparent = imagecolorallocatealpha($dst, 255, 255, 255, 127);
            imagefill($dst, 0, 0, $transparent);

            imagecopyresampled(
                $dst, $src, 0, 0,
                intdiv($w - $side, 2), intdiv($h - $side, 2),
                $size, $size, $side, $side
            );

            $r = $size / 2;
            for ($x = 0; $x < $size; $x++) {
                for ($y = 0; $y < $size; $y++) {
                    if (($x + 0.5 - $r) ** 2 + ($y + 0.5 - $r) ** 2 > $r * $r) {
                        imagesetpixel($dst, $x, $y, $transparent);
                    }
                }
            }

            ob_start();
            imagepng($dst);
            $png = ob_get_clean();

            return $cache[$key] = 'data:image/png;base64,' . base64_encode($png);
        } catch (\Throwable $e) {
            return $cache[$key] = null;
        }
    }

    public function scopeTersedia($query)
    {
        return $query->where('stok', '>', 0)->where('status_kondisi',
'Baik');
    }
}