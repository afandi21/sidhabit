<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ruangan;
use App\Models\LokasiKampus;
use Illuminate\Http\Request;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\RuanganImport;

class RuanganController extends Controller
{
    public function index()
    {
        $ruangans = Ruangan::with('lokasiKampus')->orderBy('nama_ruangan')->get();
        return view('admin.ruangan.index', compact('ruangans'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            Excel::import(new RuanganImport, $request->file('file_excel'));
            return back()->with('success', 'Data ruangan berhasil diimport.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimport data: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $headers = ['kode_ruangan', 'nama_ruangan', 'gedung', 'lantai', 'kapasitas'];
        
        $callback = function() use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            fputcsv($file, ['R301', 'Ruang Teori 301', 'Gedung A', '3', '40']);
            fputcsv($file, ['L102', 'Lab Komputer 102', 'Gedung B', '1', '25']);
            fclose($file);
        };

        return response()->stream($callback, 200, [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=template_import_ruangan.csv",
        ]);
    }

    public function create()
    {
        $lokasis = LokasiKampus::active()->get();
        return view('admin.ruangan.create', compact('lokasis'));
    }

    public function store(\App\Http\Requests\StoreRuanganRequest $request)
    {
        Ruangan::create($request->validated());
        return redirect()->route('admin.ruangan.index')->with('success', 'Ruangan berhasil ditambahkan.');
    }

    public function edit(Ruangan $ruangan)
    {
        $lokasis = LokasiKampus::active()->get();
        return view('admin.ruangan.edit', compact('ruangan', 'lokasis'));
    }

    public function update(\App\Http\Requests\UpdateRuanganRequest $request, Ruangan $ruangan)
    {
        $ruangan->update($request->validated());
        return redirect()->route('admin.ruangan.index')->with('success', 'Ruangan berhasil diperbarui.');
    }

    public function destroy(Ruangan $ruangan)
    {
        if ($ruangan->jadwalMengajar()->exists()) {
            return back()->with('error', 'Gagal menghapus! Ruangan ini sedang digunakan dalam jadwal.');
        }
        $ruangan->delete();
        return redirect()->route('admin.ruangan.index')->with('success', 'Ruangan berhasil dihapus.');
    }
}
