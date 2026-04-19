<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\CausesActivity;
use Laragear\WebAuthn\WebAuthnAuthentication;
use Laragear\WebAuthn\Contracts\WebAuthnAuthenticatable;

class User extends Authenticatable implements WebAuthnAuthenticatable
{
    use HasFactory, Notifiable, HasRoles, CausesActivity, WebAuthnAuthentication;

    protected $fillable = [
        'name',
        'email',
        'password',
        'program_studi_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function dosen()
    {
        return $this->hasOne(Dosen::class);
    }
    
    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class);
    }


    public function registeredDevices()
    {
        return $this->hasMany(RegisteredDevice::class);
    }

    public function isDosen(): bool
    {
        return $this->hasRole('dosen');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('wakil_1_akademik') || $this->hasRole('admin_fakultas');
    }

    public function isKaprodi(): bool
    {
        return $this->hasRole('kaprodi');
    }
    
    public function isOperatorProdi(): bool
    {
        return $this->program_studi_id !== null && !$this->isAdmin();
    }
}
