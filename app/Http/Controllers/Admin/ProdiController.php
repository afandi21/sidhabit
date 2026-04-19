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

    public function store(Request $request)
    {
        $request->validate([
            'kode_prodi' => 'required|unique:program_studi,kode_prodi',
            'nama_prodi' => 'required|string|max:255',
            'fakultas_id' => 'required|exists:fakultas,id',
            'jenjang' => 'required|in:D3,S1,S2,S3',
        ]);

        ProgramStudi::create($request->all());
        return redirect()->route('admin.fakultas.index')->with('success', 'Program Studi berhasil ditambahkan.');
    }

    public function edit(ProgramStudi $prodi)
    {
        $fakultas = Fakultas::all();
        return view('admin.prodi.edit', compact('prodi', 'fakultas'));
    }

    public function update(Request $request, ProgramStudi $prodi)
    {
        $request->validate([
            'kode_prodi' => 'required|unique:program_studi,kode_prodi,' . $prodi->id,
            'nama_prodi' => 'required|string|max:255',
            'fakultas_id' => 'required|exists:fakultas,id',
        ]);

        $prodi->update($request->all());
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
