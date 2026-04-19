<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HariLibur extends Model
{
    protected $table = 'hari_libur';
    protected $fillable = ['tanggal', 'keterangan', 'is_recurring'];
    protected $casts = ['tanggal' => 'date', 'is_recurring' => 'boolean'];

    public static function isLibur($tanggal): bool
    {
        return static::where('tanggal', $tanggal)->exists();
    }
}
