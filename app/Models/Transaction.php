<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Transaction extends Model
{
    use SoftDeletes;

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
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_main' => 'date:Y-m-d',
            'tanggal_transfer' => 'date:Y-m-d',
            'nominal' => 'integer',
            'okupansi_jam' => 'decimal:2',
            'deleted_at' => 'datetime',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeForUser(Builder $query, $user): Builder
    {
        if ($user->role === 'superadmin') {
            return $query;
        }
        return $query->where('created_by', $user->id);
    }

    public function scopeForRole(Builder $query, $user): Builder
    {
        if ($user->role === 'superadmin') {
            return $query;
        }
        return $query->where('created_by', $user->id);
    }
}
