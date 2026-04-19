<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegisteredDevice extends Model
{
    protected $fillable = [
        'user_id', 'device_fingerprint', 'device_name', 'device_model',
        'browser_info', 'os_info', 'is_active', 'is_primary', 'registered_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_primary' => 'boolean',
        'registered_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
