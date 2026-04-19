<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramStudi extends Model
{
    protected $table = 'program_studi';
    protected $fillable = ['fakultas_id', 'kode_prodi', 'nama_prodi', 'jenjang', 'kaprodi', 'is_active'];

    public function fakultas()
    {
        return $this->belongsTo(Fakultas::class);
    }

    public function dosens()
    {
        return $this->hasMany(Dosen::class);
    }

    public function mataKuliah()
    {
        return $this->hasMany(MataKuliah::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
