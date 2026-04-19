<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Hari;
use App\Models\SesiKuliah;
use App\Models\DosenKetersediaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AvailabilityController extends Controller
{
    public function index()
    {
        $dosen = Auth::user()->dosen;
        $haris = Hari::orderBy('urutan')->get();
        $sesis = SesiKuliah::active()->get();
        
        // Ambil data ketersediaan yang sudah ada
        $ketersediaan = DosenKetersediaan::where('dosen_id', $dosen->id)
            ->get()
            ->groupBy(['hari_id', 'sesi_id']);

        return view('dosen.availability.index', compact('haris', 'sesis', 'ketersediaan'));
    }

    public function store(Request $request)
    {
        $dosen = Auth::user()->dosen;
        $data = $request->input('availability', []);

        // Hapus ketersediaan lama untuk dosen ini
        DosenKetersediaan::where('dosen_id', $dosen->id)->delete();

        // Simpan yang baru (yang diceklis saja = Tidak Bersedia)
        // Note: Sesuai permintaan user "menceklis sendiri hari yang mereka tidak bisa"
        foreach ($data as $hariId => $sesis) {
            foreach ($sesis as $sesiId => $value) {
                DosenKetersediaan::create([
                    'dosen_id' => $dosen->id,
                    'hari_id' => $hariId,
                    'sesi_id' => $sesiId,
                    'is_bersedia' => false // Jika ada di input, berarti TIDAK BERSEDIA
                ]);
            }
        }

        return back()->with('success', 'Ketersediaan mengajar Anda telah diperbarui.');
    }
}
