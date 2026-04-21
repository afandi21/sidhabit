<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MataKuliah extends Model
{
    use \App\Traits\FilterByRoleTrait;
    protected $table = 'mata_kuliah';
    protected $fillable = ['program_studi_id', 'kode_mk', 'nama_mk', 'sks', 'semester', 'jenis', 'kategori', 'is_active'];

    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class);
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
