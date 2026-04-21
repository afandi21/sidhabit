<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fakultas;
use Illuminate\Http\Request;

class FakultasController extends Controller
{
    public function index()
    {
        $fakultas = Fakultas::with('programStudi')->withCount('programStudi')->get();
        return view('admin.fakultas.index', compact('fakultas'));
    }

    public function create()
    {
        return view('admin.fakultas.create');
    }

    public function store(\App\Http\Requests\StoreFakultasRequest $request)
    {
        Fakultas::create($request->validated());
        return redirect()->route('admin.fakultas.index')->with('success', 'Fakultas berhasil ditambahkan.');
    }

    public function edit(Fakultas $fakultas)
    {
        return view('admin.fakultas.edit', compact('fakultas'));
    }

    public function update(\App\Http\Requests\UpdateFakultasRequest $request, Fakultas $fakultas)
    {
        $fakultas->update($request->validated());
        return redirect()->route('admin.fakultas.index')->with('success', 'Fakultas berhasil diperbarui.');
    }

    public function destroy(Fakultas $fakultas)
    {
        if ($fakultas->programStudi()->exists()) {
            return back()->with('error', 'Gagal menghapus! Fakultas ini masih memiliki Program Studi.');
        }
        $fakultas->delete();
        return redirect()->route('admin.fakultas.index')->with('success', 'Fakultas berhasil dihapus.');
    }
}
