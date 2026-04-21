<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalMengajar extends Model
{
    use \App\Traits\FilterByRoleTrait;
    protected $table = 'jadwal_mengajar';
    protected $fillable = [
        'dosen_id', 'mata_kuliah_id', 'ruangan_id', 'semester_id', 'hari_id',
        'sesi_mulai_id', 'sesi_selesai_id', 'kelas', 'jam_mulai', 'jam_selesai',
        'jumlah_pertemuan', 'is_active',
    ];

    protected $casts = [
        'jam_mulai' => 'string',
        'jam_selesai' => 'string',
    ];

    public function sesiMulai()
    {
        return $this->belongsTo(SesiKuliah::class, 'sesi_mulai_id');
    }

    public function sesiSelesai()
    {
        return $this->belongsTo(SesiKuliah::class, 'sesi_selesai_id');
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class);
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function hari()
    {
        return $this->belongsTo(Hari::class);
    }

    public function presensi()
    {
        return $this->hasMany(Presensi::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeHariIni($query)
    {
        $namaHari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $hariIni = $namaHari[now()->dayOfWeek];
        return $query->whereHas('hari', fn($q) => $q->where('nama_hari', $hariIni));
    }

    public function scopeSemesterAktif($query)
    {
        return $query->whereHas('semester', fn($q) => $q->where('is_active', true));
    }
}
