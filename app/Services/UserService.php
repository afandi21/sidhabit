<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Membuat User baru dan menentukan perannya
     */
    public function createUser(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'program_studi_id' => $data['program_studi_id'] ?? null,
        ]);

        $this->assignUserRole($user, $data['program_studi_id'] ?? null);

        return $user;
    }

    /**
     * Memperbarui data User dan mensinkronisasi perannya
     */
    public function updateUser(User $user, array $data): User
    {
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->program_studi_id = $data['program_studi_id'] ?? null;

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        $this->assignUserRole($user, $data['program_studi_id'] ?? null, true);

        return $user;
    }

    /**
     * Helper internal untuk menentukan role berdasarkan ada tidaknya prodi
     */
    private function assignUserRole(User $user, $prodiId, $isUpdate = false): void
    {
        $role = $prodiId ? 'kaprodi' : 'super_admin';

        if ($isUpdate) {
            $user->syncRoles([$role]);
        } else {
            $user->assignRole($role);
        }
    }
}
