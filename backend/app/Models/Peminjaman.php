<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Peminjaman extends Model
{
    protected $table = 'peminjaman';

    protected $fillable = [
        'user_id', 'tgl_pinjam', 'tgl_kembali_plan', 'status'
    ];

    protected function casts(): array
    {
        return [
            'tgl_pinjam' => 'date:Y-m-d',
            'tgl_kembali_plan' => 'date:Y-m-d',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // FIX: Buat relasi utama tanpa huruf 's' agar cocok dengan Controller & Resource
    public function detailPinjam(): HasMany
    {
        return $this->hasMany(DetilPinjam::class, 'peminjaman_id', 'id');
    }

    // Tambahkan alias ini agar Blade View yang panggil detailPinjams() tetap jalan tanpa error
    public function detailPinjams(): HasMany
    {
        return $this->detailPinjam();
    }

    public function pengembalian(): HasOne
    {
        return $this->hasOne(Pengembalian::class);
    }
}