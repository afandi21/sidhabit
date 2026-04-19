<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SesiKuliah extends Model
{
    protected $table = 'sesi_kuliah';
    protected $fillable = ['nama_sesi', 'jam_mulai', 'jam_selesai', 'is_active'];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    public function getLabelAttribute()
    {
        return $this->nama_sesi . " (" . substr($this->jam_mulai, 0, 5) . " - " . substr($this->jam_selesai, 0, 5) . ")";
    }
}
