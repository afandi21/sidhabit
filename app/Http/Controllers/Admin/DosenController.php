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
    public function index()
    {
        $dosens = Dosen::with(['programStudi', 'user'])->get();
        return view('admin.dosen.index', compact('dosens'));
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

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'nidn' => 'nullable|unique:dosens,nidn',
            'nuptk' => 'nullable|unique:dosens,nuptk',
            'program_studi_id' => 'required|exists:program_studi,id',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->nama_lengkap,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            $user->assignRole('dosen');

            Dosen::create([
                'user_id' => $user->id,
                'nidn' => $request->nidn,
                'nuptk' => $request->nuptk,
                'nama_lengkap' => $request->nama_lengkap,
                'gelar_depan' => $request->gelar_depan,
                'gelar_belakang' => $request->gelar_belakang,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tanggal_lahir' => $request->tanggal_lahir,
                'no_hp' => $request->no_hp,
                'program_studi_id' => $request->program_studi_id,
                'status_aktif' => 'aktif',
            ]);

            DB::commit();
            return redirect()->route('admin.dosen.index')->with('success', 'Data dosen berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menambahkan data: ' . $e->getMessage());
        }
    }

    public function edit(Dosen $dosen)
    {
        $prodis = ProgramStudi::with('fakultas')->active()->get();
        return view('admin.dosen.edit', compact('dosen', 'prodis'));
    }

    public function update(Request $request, Dosen $dosen)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $dosen->user_id,
            'nidn' => 'nullable|unique:dosens,nidn,' . $dosen->id,
            'nuptk' => 'nullable|unique:dosens,nuptk,' . $dosen->id,
            'program_studi_id' => 'required|exists:program_studi,id',
        ]);

        DB::beginTransaction();
        try {
            $dosen->user->update([
                'name' => $request->nama_lengkap,
                'email' => $request->email,
            ]);

            if ($request->password) {
                $dosen->user->update(['password' => Hash::make($request->password)]);
            }

            $dosen->update($request->all());

            DB::commit();
            return redirect()->route('admin.dosen.index')->with('success', 'Data dosen berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroy(Dosen $dosen)
    {
        DB::beginTransaction();
        try {
            $user = $dosen->user;
            $dosen->delete();
            $user->delete();
            DB::commit();
            return redirect()->route('admin.dosen.index')->with('success', 'Data dosen berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menghapus data.');
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
