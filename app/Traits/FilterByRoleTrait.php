<?php

namespace App\Traits;

trait FilterByRoleTrait
{
    /**
     * Scope untuk memfilter data berdasarkan role user (terutama Operator Prodi).
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $column Nama kolom prodi_id (default: program_studi_id)
     * @param string|null $relation Jika filter harus lewat relasi (misal: 'mataKuliah')
     */
    public function scopeByRole($query, $column = 'program_studi_id', $relation = null)
    {
        $user = auth()->user();
        
        if ($user && $user->isOperatorProdi()) {
            if ($relation) {
                return $query->whereHas($relation, function ($q) use ($column, $user) {
                    $q->where($column, $user->program_studi_id);
                });
            }
            return $query->where($column, $user->program_studi_id);
        }

        return $query;
    }
}
