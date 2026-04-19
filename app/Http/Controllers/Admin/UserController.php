<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('programStudi')->get();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $prodis = ProgramStudi::all();
        return view('admin.users.create', compact('prodis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'program_studi_id' => ['nullable', 'exists:program_studi,id'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'program_studi_id' => $request->program_studi_id,
        ]);

        if ($request->program_studi_id) {
            $user->assignRole('kaprodi');
        } else {
            $user->assignRole('super_admin');
        }

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $prodis = ProgramStudi::all();
        return view('admin.users.edit', compact('user', 'prodis'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'program_studi_id' => ['nullable', 'exists:program_studi,id'],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->program_studi_id = $request->program_studi_id;

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', Rules\Password::defaults()],
            ]);
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Sync Roles
        if ($user->program_studi_id) {
            $user->syncRoles(['kaprodi']);
        } else {
            $user->syncRoles(['super_admin']);
        }

        return redirect()->route('admin.users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
