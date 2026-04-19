<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Dosen extends Model
{
    use LogsActivity;

    protected $fillable = [
        'user_id', 'nidn', 'nuptk', 'nama_lengkap', 'gelar_depan', 'gelar_belakang',
        'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'no_hp', 'alamat',
        'program_studi_id', 'jabatan_fungsional', 'pangkat_golongan', 'status_aktif', 'foto',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }

    public function getNamaGelarAttribute(): string
    {
        $gelarDepan = $this->gelar_depan ? $this->gelar_depan . ' ' : '';
        $gelarBelakang = $this->gelar_belakang ? ', ' . $this->gelar_belakang : '';
        return $gelarDepan . $this->nama_lengkap . $gelarBelakang;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function programStudi(): BelongsTo
    {
        return $this->belongsTo(ProgramStudi::class);
    }

    public function jadwalMengajar(): HasMany
    {
        return $this->hasMany(JadwalMengajar::class);
    }

    public function presensi(): HasMany
    {
        return $this->hasMany(Presensi::class);
    }

    public function izinCuti(): HasMany
    {
        return $this->hasMany(IzinCuti::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('status_aktif', 'aktif');
    }

    public function scopeByProdi($query, $prodiId)
    {
        return $query->where('program_studi_id', $prodiId);
    }
}
