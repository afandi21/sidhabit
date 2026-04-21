<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BebanMengajar extends Model
{
    use \App\Traits\FilterByRoleTrait;
    protected $table = 'beban_mengajar';
    protected $fillable = ['semester_id', 'dosen_id', 'mata_kuliah_id', 'kelas', 'total_sks', 'sks_terjadwal'];

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class);
    }

    public function getIsSelesaiAttribute()
    {
        return $this->sks_terjadwal >= $this->total_sks;
    }
}
