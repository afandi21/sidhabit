<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MataKuliah;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MataKuliahImport;

class MataKuliahController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            Excel::import(new MataKuliahImport, $request->file('file_excel'));
            return back()->with('success', 'Data mata kuliah berhasil diimport.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimport data: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $headers = ['kode_mk', 'nama_mk', 'sks', 'semester', 'jenis', 'kategori', 'prodi'];
        
        $callback = function() use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            fputcsv($file, ['MK001', 'Bahasa Arab I', '2', '1', 'teori', 'mahad', 'PBA']);
            fputcsv($file, ['MK002', 'Pendidikan Pancasila', '2', '1', 'teori', 'dikti', 'PBA']);
            fclose($file);
        };

        return response()->stream($callback, 200, [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=template_import_matakuliah.csv",
        ]);
    }
    public function index(Request $request)
    {
        $activeSemester = \DB::table('semesters')->where('is_active', true)->first();
        $query = MataKuliah::with('programStudi')->byRole();

        // Pencarian Nama/Kode MK
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_mk', 'LIKE', "%{$search}%")
                  ->orWhere('kode_mk', 'LIKE', "%{$search}%");
            });
        }

        // Filter Prodi
        if ($request->filled('prodi_id')) {
            $query->where('program_studi_id', $request->prodi_id);
        }

        if ($activeSemester && !$request->has('semua')) {
            if (str_contains(strtolower($activeSemester->nama_semester), 'ganjil')) {
                $query->whereRaw('semester % 2 != 0');
            } 
            else if (str_contains(strtolower($activeSemester->nama_semester), 'genap')) {
                $query->whereRaw('semester % 2 = 0');
            }
        }

        $matkuls = $query->orderBy('semester')->orderBy('nama_mk')->paginate(25)->withQueryString();
        $prodis = ProgramStudi::orderBy('nama_prodi')->get();

        return view('admin.matakuliah.index', compact('matkuls', 'activeSemester', 'prodis'));
    }

    public function create()
    {
        $prodis = ProgramStudi::with('fakultas')->active()->get();
        return view('admin.matakuliah.create', compact('prodis'));
    }

    public function store(\App\Http\Requests\StoreMataKuliahRequest $request)
    {
        MataKuliah::create($request->validated());
        return redirect()->route('admin.matakuliah.index')->with('success', 'Mata Kuliah berhasil ditambahkan.');
    }

    public function edit(MataKuliah $matakuliah)
    {
        $prodis = ProgramStudi::with('fakultas')->active()->get();
        return view('admin.matakuliah.edit', compact('matakuliah', 'prodis'));
    }

    public function update(\App\Http\Requests\UpdateMataKuliahRequest $request, MataKuliah $matakuliah)
    {
        $matakuliah->update($request->validated());
        return redirect()->route('admin.matakuliah.index')->with('success', 'Mata Kuliah berhasil diperbarui.');
    }

    public function destroy(MataKuliah $matakuliah)
    {
        if ($matakuliah->jadwalMengajar()->exists()) {
            return back()->with('error', 'Gagal menghapus! Mata kuliah ini sudah terdaftar di jadwal.');
        }
        $matakuliah->delete();
        return redirect()->route('admin.matakuliah.index')->with('success', 'Mata Kuliah berhasil dihapus.');
    }
}
