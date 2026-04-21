<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProgramStudi;
use App\Models\Fakultas;
use Illuminate\Http\Request;

class ProdiController extends Controller
{
    public function index()
    {
        $prodis = ProgramStudi::with('fakultas')->get();
        return view('admin.prodi.index', compact('prodis'));
    }

    public function create(Request $request)
    {
        $selectedFakultasId = $request->get('fakultas_id');
        $fakultas = Fakultas::all();
        return view('admin.prodi.create', compact('fakultas', 'selectedFakultasId'));
    }

    public function store(\App\Http\Requests\StoreProdiRequest $request)
    {
        ProgramStudi::create($request->validated());
        return redirect()->route('admin.fakultas.index')->with('success', 'Program Studi berhasil ditambahkan.');
    }

    public function edit(ProgramStudi $prodi)
    {
        $fakultas = Fakultas::all();
        return view('admin.prodi.edit', compact('prodi', 'fakultas'));
    }

    public function update(\App\Http\Requests\UpdateProdiRequest $request, ProgramStudi $prodi)
    {
        $prodi->update($request->validated());
        return redirect()->route('admin.fakultas.index')->with('success', 'Program Studi berhasil diperbarui.');
    }

    public function destroy(ProgramStudi $prodi)
    {
        if ($prodi->dosens()->exists()) {
            return back()->with('error', 'Gagal menghapus! Program studi ini masih memiliki data dosen.');
        }
        $prodi->delete();
        return redirect()->route('admin.fakultas.index')->with('success', 'Program Studi berhasil dihapus.');
    }
}
