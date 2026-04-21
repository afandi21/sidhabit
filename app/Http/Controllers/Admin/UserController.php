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

    protected $userService;

    public function __construct(\App\Services\UserService $userService)
    {
        $this->userService = $userService;
    }

    public function store(\App\Http\Requests\StoreUserRequest $request)
    {
        $this->userService->createUser($request->validated());
        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $prodis = ProgramStudi::all();
        return view('admin.users.edit', compact('user', 'prodis'));
    }

    public function update(\App\Http\Requests\UpdateUserRequest $request, User $user)
    {
        $this->userService->updateUser($user, $request->validated());
        return redirect()->route('admin.users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
