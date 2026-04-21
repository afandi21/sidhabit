<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\User;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DosenImport;
use Illuminate\Support\Facades\Storage;

class DosenController extends Controller
{
    public function index(Request $request)
    {
        $query = Dosen::with(['programStudi', 'user'])->byRole();

        // Filter berdasarkan Pencarian Nama/NIDN
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'LIKE', "%{$search}%")
                  ->orWhere('nidn', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($qu) use ($search) {
                      $qu->where('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Filter berdasarkan Program Studi
        if ($request->filled('prodi_id')) {
            $query->where('program_studi_id', $request->prodi_id);
        }

        $dosens = $query->latest()->paginate(25)->withQueryString();
        $prodis = ProgramStudi::orderBy('nama_prodi')->get();

        return view('admin.dosen.index', compact('dosens', 'prodis'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            Excel::import(new DosenImport, $request->file('file_excel'));
            return back()->with('success', 'Data dosen berhasil diimport secara massal.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimport data: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $headers = [
            'nidn',
            'nama_lengkap',
            'nama_lengkap_beserta_gelar',
            'email',
            'prodi',
            'jenis_kelamin',
            'tanggal_lahir',
            'no_hp'
        ];

        $callback = function() use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            // Contoh baris
            fputcsv($file, ['12345678', 'Ahmad Dani', 'Dr. Ahmad Dani, M.Ag', 'ahmad@example.com', 'PBA', 'L', '1995-12-02', '08123456789']);
            fclose($file);
        };

        return response()->stream($callback, 200, [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=template_import_dosen.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ]);
    }

    public function create()
    {
        $prodis = ProgramStudi::with('fakultas')->active()->get();
        return view('admin.dosen.create', compact('prodis'));
    }

    protected $dosenService;

    public function __construct(\App\Services\DosenService $dosenService)
    {
        $this->dosenService = $dosenService;
    }

    public function store(\App\Http\Requests\StoreDosenRequest $request)
    {
        try {
            $this->dosenService->createDosen($request->validated());
            return redirect()->route('admin.dosen.index')->with('success', 'Data dosen berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan data: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Dosen $dosen)
    {
        $prodis = ProgramStudi::with('fakultas')->active()->get();
        return view('admin.dosen.edit', compact('dosen', 'prodis'));
    }

    public function update(\App\Http\Requests\UpdateDosenRequest $request, Dosen $dosen)
    {
        try {
            $this->dosenService->updateDosen($dosen, $request->validated());
            return redirect()->route('admin.dosen.index')->with('success', 'Data dosen berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Dosen $dosen)
    {
        try {
            $this->dosenService->deleteDosen($dosen);
            return redirect()->route('admin.dosen.index')->with('success', 'Data dosen berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function loginAsDosen(Dosen $dosen)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        // Simpan ID admin di session agar bisa balik (Opsional di masa depan)
        session(['impersonate_admin' => auth()->id()]);
        
        \Illuminate\Support\Facades\Auth::login($dosen->user);

        return redirect()->route('dosen.dashboard')->with('success', 'Anda sekarang masuk sebagai ' . $dosen->nama_lengkap);
    }
}
