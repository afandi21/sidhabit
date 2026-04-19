<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IzinCuti;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IzinCutiController extends Controller
{
    public function index()
    {
        $izins = \DB::table('izin_cuti')
            ->join('dosens', 'izin_cuti.dosen_id', '=', 'dosens.id')
            ->select('izin_cuti.*', 'dosens.nama_lengkap', 'dosens.gelar_depan', 'dosens.gelar_belakang')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.izin_cuti.index', compact('izins'));
    }

    public function approve(Request $request, $id)
    {
        \DB::table('izin_cuti')->where('id', $id)->update([
            'status_approval' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'catatan_approval' => $request->catatan
        ]);

        return back()->with('success', 'Pengajuan izin telah disetujui.');
    }

    public function reject(Request $request, $id)
    {
        \DB::table('izin_cuti')->where('id', $id)->update([
            'status_approval' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'catatan_approval' => $request->catatan
        ]);

        return back()->with('success', 'Pengajuan izin telah ditolak.');
    }
}
