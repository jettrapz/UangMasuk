<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'nama',
        'tanggal_main',
        'jenis_transfer',
        'tanggal_transfer',
        'nominal',
        'catatan',
        'jam_mulai',
        'jam_selesai',
        'okupansi_jam',
        'gambar_bukti',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_main' => 'date:Y-m-d',
            'tanggal_transfer' => 'date:Y-m-d',
            'nominal' => 'integer',
            'okupansi_jam' => 'decimal:2',
        ];
    }
}
