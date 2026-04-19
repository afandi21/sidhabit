<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DosenKetersediaan extends Model
{
    protected $table = 'dosen_ketersediaan';
    protected $fillable = ['dosen_id', 'hari_id', 'sesi_id', 'is_bersedia'];

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function hari()
    {
        return $this->belongsTo(Hari::class);
    }

    public function sesi()
    {
        return $this->belongsTo(SesiKuliah::class, 'sesi_id');
    }
}
