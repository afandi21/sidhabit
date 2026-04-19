<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ruangan;
use App\Models\LokasiKampus;
use Illuminate\Http\Request;

class RuanganController extends Controller
{
    public function index()
    {
        $ruangans = Ruangan::with('lokasiKampus')->get();
        return view('admin.ruangan.index', compact('ruangans'));
    }

    public function create()
    {
        $lokasis = LokasiKampus::active()->get();
        return view('admin.ruangan.create', compact('lokasis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_ruangan' => 'required|unique:ruangan,kode_ruangan',
            'nama_ruangan' => 'required|string|max:255',
            'lokasi_kampus_id' => 'required|exists:lokasi_kampus,id',
            'kapasitas' => 'nullable|integer',
        ]);

        Ruangan::create($request->all());
        return redirect()->route('admin.ruangan.index')->with('success', 'Ruangan berhasil ditambahkan.');
    }

    public function edit(Ruangan $ruangan)
    {
        $lokasis = LokasiKampus::active()->get();
        return view('admin.ruangan.edit', compact('ruangan', 'lokasis'));
    }

    public function update(Request $request, Ruangan $ruangan)
    {
        $request->validate([
            'kode_ruangan' => 'required|unique:ruangan,kode_ruangan,' . $ruangan->id,
            'nama_ruangan' => 'required|string|max:255',
            'lokasi_kampus_id' => 'required|exists:lokasi_kampus,id',
        ]);

        $ruangan->update($request->all());
        return redirect()->route('admin.ruangan.index')->with('success', 'Ruangan berhasil diperbarui.');
    }

    public function destroy(Ruangan $ruangan)
    {
        if ($ruangan->jadwalMengajars()->exists()) {
            return back()->with('error', 'Gagal menghapus! Ruangan ini sedang digunakan dalam jadwal.');
        }
        $ruangan->delete();
        return redirect()->route('admin.ruangan.index')->with('success', 'Ruangan berhasil dihapus.');
    }
}
