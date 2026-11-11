<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            // Diisi saat peminjam mengajukan pengembalian; null = belum mengajukan
            $table->timestamp('tgl_pengajuan_kembali')->nullable()->after('status');
            $table->string('catatan_kembali', 255)->nullable()->after('tgl_pengajuan_kembali');
        });
    }

    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropColumn(['tgl_pengajuan_kembali', 'catatan_kembali']);
        });
    }
};