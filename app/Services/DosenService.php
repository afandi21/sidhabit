<?php

namespace App\Services;

use App\Models\Dosen;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Exception;

class DosenService
{
    /**
     * Membuat akun User dan profil Dosen secara bersamaan.
     */
    public function createDosen(array $data): Dosen
    {
        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $data['nama_lengkap'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);
            $user->assignRole('dosen');

            $dosen = Dosen::create([
                'user_id' => $user->id,
                'nidn' => $data['nidn'] ?? null,
                'nuptk' => $data['nuptk'] ?? null,
                'nama_lengkap' => $data['nama_lengkap'],
                'gelar_depan' => $data['gelar_depan'] ?? null,
                'gelar_belakang' => $data['gelar_belakang'] ?? null,
                'jenis_kelamin' => $data['jenis_kelamin'],
                'tanggal_lahir' => $data['tanggal_lahir'],
                'no_hp' => $data['no_hp'] ?? null,
                'program_studi_id' => $data['program_studi_id'],
                'status_aktif' => 'aktif',
            ]);

            DB::commit();
            return $dosen;
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Memperbarui akun User dan profil Dosen.
     */
    public function updateDosen(Dosen $dosen, array $data): Dosen
    {
        DB::beginTransaction();
        try {
            $dosen->user->update([
                'name' => $data['nama_lengkap'],
                'email' => $data['email'],
            ]);

            if (!empty($data['password'])) {
                $dosen->user->update(['password' => Hash::make($data['password'])]);
            }

            $dosen->update($data);

            DB::commit();
            return $dosen;
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Menghapus profil Dosen dan akun User terkait.
     */
    public function deleteDosen(Dosen $dosen): void
    {
        DB::beginTransaction();
        try {
            $user = $dosen->user;
            $dosen->delete();
            $user->delete();
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
