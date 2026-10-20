<?php

namespace App\Http\Requests\Peminjaman;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePeminjamanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // FIX: Menggunakan 'after_or_equal:today' yang aman dengan format tanggal Y-m-d
            'tgl_kembali_plan' => ['required', 'date_format:Y-m-d', 'after_or_equal:' . date('Y-m-d')],
            'items'            => ['required', 'array', 'min:1'],
            'items.*.alat_id'  => ['required', 'integer', Rule::exists('alat', 'id')],
            'items.*.jumlah'   => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'tgl_kembali_plan.required'       => 'Tanggal rencana pengembalian wajib diisi.',
            'tgl_kembali_plan.date_format'    => 'Format tanggal harus YYYY-MM-DD (Contoh: 2026-09-20).',
            'tgl_kembali_plan.after_or_equal' => 'Tanggal rencana kembali tidak boleh sebelum hari ini.',
            'items.required'                  => 'Anda harus memilih minimal satu alat untuk dipinjam.',
            'items.array'                     => 'Format data item harus berupa array.',
            'items.min'                       => 'Anda harus memilih minimal satu alat untuk dipinjam.',
            'items.*.alat_id.required'        => 'Pilihan alat wajib diisi.',
            'items.*.alat_id.exists'          => 'Alat yang dipilih tidak ditemukan di database.',
            'items.*.jumlah.required'         => 'Jumlah alat wajib diisi.',
            'items.*.jumlah.min'              => 'Jumlah alat minimal 1.',
        ];
    }

    public function attributes(): array
    {
        return [
            'items.*.alat_id' => 'Alat',
            'items.*.jumlah'  => 'Jumlah barang',
        ];
    }
}