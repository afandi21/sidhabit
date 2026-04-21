<?php

namespace App\Services;

use App\Models\Presensi;
use App\Models\Dosen;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class LaporanService
{
    /**
     * Memfilter query presensi berdasarkan role User (Wakil 1 vs Kaprodi)
     */
    public function filterByRoleAndProdi(Builder $query, $prodiId = null): Builder
    {
        $user = auth()->user();

        if (!$user->hasRole('wakil_1_akademik')) {
            // Kaprodi / Operator hanya melihat prodinya sendiri
            $lockedProdiId = $user->program_studi_id;
            return $query->whereHas('dosen', function($q) use ($lockedProdiId) {
                $q->where('program_studi_id', $lockedProdiId);
            });
        } elseif ($prodiId) {
            // Wakil 1 memfilter prodi spesifik
            return $query->whereHas('dosen', function($q) use ($prodiId) {
                $q->where('program_studi_id', $prodiId);
            });
        }

        return $query;
    }

    /**
     * Memfilter query Dosen berdasarkan role User (digunakan untuk Export)
     */
    public function filterDosenByRoleAndProdi(Builder $query, $prodiId = null): Builder
    {
        $user = auth()->user();

        if (!$user->hasRole('wakil_1_akademik')) {
            return $query->where('program_studi_id', $user->program_studi_id);
        } elseif ($prodiId) {
            return $query->where('program_studi_id', $prodiId);
        }

        return $query;
    }

    /**
     * Mengkalkulasi rekap bulanan untuk tiap dosen
     */
    public function calculateRekapBulanan(Collection $presensis): Collection
    {
        return $presensis->groupBy('dosen_id')->map(function ($items) {
            return [
                'nama' => $items->first()->dosen->nama_lengkap,
                'hadir' => $items->whereIn('status', ['hadir', 'terlambat'])->count(),
                'izin' => $items->whereIn('status', ['izin', 'sakit', 'cuti'])->count(),
                'alfa' => $items->where('status', 'alfa')->count(),
                'total_menit' => $items->sum('durasi_menit'),
            ];
        });
    }
}
