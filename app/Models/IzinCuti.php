<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IzinCuti extends Model
{
    protected $table = 'izin_cuti';
    protected $fillable = [
        'dosen_id', 'jenis', 'tanggal_mulai', 'tanggal_selesai',
        'alasan', 'dokumen_pendukung', 'status_approval', 'approved_by',
        'approved_at', 'catatan_approval',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'approved_at' => 'datetime',
    ];

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopePending($query)
    {
        return $query->where('status_approval', 'pending');
    }
}
