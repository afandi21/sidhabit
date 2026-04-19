<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fakultas extends Model
{
    protected $table = 'fakultas';
    protected $fillable = ['kode_fakultas', 'nama_fakultas', 'dekan', 'is_active'];

    public function programStudi()
    {
        return $this->hasMany(ProgramStudi::class);
    }

    public function dosens()
    {
        return $this->hasManyThrough(Dosen::class, ProgramStudi::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
