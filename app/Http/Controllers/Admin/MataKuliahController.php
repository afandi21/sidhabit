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
    public function index()
    {
        $activeSemester = \DB::table('semesters')->where('is_active', true)->first();
        $query = MataKuliah::with('programStudi');

        if ($activeSemester && !request()->has('semua')) {
            // Jika Semester Ganjil dipilih (misal Ganjil 2025/2026)
            // Maka tampilkan MK semester 1, 3, 5, 7
            if (str_contains(strtolower($activeSemester->nama_semester), 'ganjil')) {
                $query->whereRaw('semester % 2 != 0');
            } 
            // Jika Semester Genap dipilih
            // Maka tampilkan MK semester 2, 4, 6, 8
            else if (str_contains(strtolower($activeSemester->nama_semester), 'genap')) {
                $query->whereRaw('semester % 2 = 0');
            }
        }

        $matkuls = $query->get();
        return view('admin.matakuliah.index', compact('matkuls', 'activeSemester'));
    }

    public function create()
    {
        $prodis = ProgramStudi::with('fakultas')->active()->get();
        return view('admin.matakuliah.create', compact('prodis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_mk' => 'required|unique:mata_kuliah,kode_mk',
            'nama_mk' => 'required|string|max:255',
            'sks' => 'required|integer|min:1|max:8',
            'semester' => 'required|integer|min:1|max:8',
            'program_studi_id' => 'required|exists:program_studi,id',
            'jenis' => 'required|in:teori,praktikum,teori_praktikum',
            'kategori' => 'required|in:dikti,mahad',
        ]);

        MataKuliah::create($request->all());
        return redirect()->route('admin.matakuliah.index')->with('success', 'Mata Kuliah berhasil ditambahkan.');
    }

    public function edit(MataKuliah $matakuliah)
    {
        $prodis = ProgramStudi::with('fakultas')->active()->get();
        return view('admin.matakuliah.edit', compact('matakuliah', 'prodis'));
    }

    public function update(Request $request, MataKuliah $matakuliah)
    {
        $request->validate([
            'kode_mk' => 'required|unique:mata_kuliah,kode_mk,' . $matakuliah->id,
            'nama_mk' => 'required|string|max:255',
            'sks' => 'required|integer',
            'program_studi_id' => 'required|exists:program_studi,id',
            'kategori' => 'required',
        ]);

        $matakuliah->update($request->all());
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
