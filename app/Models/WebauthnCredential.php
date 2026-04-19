<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebauthnCredential extends Model
{
    protected $fillable = [
        'user_id', 'credential_id', 'public_key', 'attestation_type',
        'transports', 'sign_count', 'user_handle', 'aaguid', 'device_name',
        'is_active', 'last_used_at',
    ];

    protected $casts = [
        'transports' => 'array',
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
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
