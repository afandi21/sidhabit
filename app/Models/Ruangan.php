<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    protected $table = 'ruangan';
    protected $fillable = ['kode_ruangan', 'nama_ruangan', 'gedung', 'lantai', 'kapasitas', 'lokasi_kampus_id', 'is_active'];

    public function lokasiKampus()
    {
        return $this->belongsTo(LokasiKampus::class);
    }

    public function jadwalMengajar()
    {
        return $this->hasMany(JadwalMengajar::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
