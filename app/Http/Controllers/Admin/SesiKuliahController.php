<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SesiKuliah;
use Illuminate\Http\Request;

class SesiKuliahController extends Controller
{
    public function index()
    {
        $sesis = SesiKuliah::all();
        return view('admin.sesikuliah.index', compact('sesis'));
    }

    public function create()
    {
        return view('admin.sesikuliah.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_sesi' => 'required|string|max:50',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
        ]);

        SesiKuliah::create($request->all());
        return redirect()->route('admin.sesikuliah.index')->with('success', 'Sesi kuliah berhasil ditambahkan.');
    }

    public function edit(SesiKuliah $sesikuliah)
    {
        return view('admin.sesikuliah.edit', compact('sesikuliah'));
    }

    public function update(Request $request, SesiKuliah $sesikuliah)
    {
        $request->validate([
            'nama_sesi' => 'required|string|max:50',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
        ]);

        $sesikuliah->update($request->all());
        return redirect()->route('admin.sesikuliah.index')->with('success', 'Sesi kuliah berhasil diperbarui.');
    }

    public function destroy(SesiKuliah $sesikuliah)
    {
        $sesikuliah->delete();
        return redirect()->route('admin.sesikuliah.index')->with('success', 'Sesi kuliah berhasil dihapus.');
    }
}
