<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Presensi extends Model
{
    use LogsActivity;

    protected $table = 'presensi';
    protected $fillable = [
        'dosen_id', 'jadwal_mengajar_id', 'tanggal', 'pertemuan_ke',
        'jam_masuk', 'jam_keluar', 'durasi_menit', 'status', 'metode_presensi',
        'latitude_masuk', 'longitude_masuk', 'latitude_keluar', 'longitude_keluar',
        'device_fingerprint', 'keterangan', 'ip_address', 'user_agent',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function jadwalMengajar()
    {
        return $this->belongsTo(JadwalMengajar::class);
    }

    public function scopeHariIni($query)
    {
        return $query->where('tanggal', today());
    }

    public function scopeByBulan($query, $bulan, $tahun)
    {
        return $query->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function hitungDurasi(): ?int
    {
        if ($this->jam_masuk && $this->jam_keluar) {
            $masuk = \Carbon\Carbon::parse($this->jam_masuk);
            $keluar = \Carbon\Carbon::parse($this->jam_keluar);
            return $keluar->diffInMinutes($masuk);
        }
        return null;
    }
}
