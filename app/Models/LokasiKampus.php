<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LokasiKampus extends Model
{
    protected $table = 'lokasi_kampus';
    protected $fillable = ['nama_lokasi', 'alamat', 'latitude', 'longitude', 'radius_meter', 'is_active'];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function ruangan()
    {
        return $this->hasMany(Ruangan::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if given coordinates are within the radius of this location
     */
    public function isWithinRadius(float $lat, float $lng): bool
    {
        $distance = $this->calculateDistance($lat, $lng);
        return $distance <= $this->radius_meter;
    }

    /**
     * Calculate distance in meters using Haversine formula
     */
    public function calculateDistance(float $lat, float $lng): float
    {
        $earthRadius = 6371000; // meters
        $latFrom = deg2rad($this->latitude);
        $latTo = deg2rad($lat);
        $latDelta = deg2rad($lat - $this->latitude);
        $lngDelta = deg2rad($lng - $this->longitude);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($latFrom) * cos($latTo) *
             sin($lngDelta / 2) * sin($lngDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
