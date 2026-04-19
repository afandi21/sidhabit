<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Hari;
use App\Models\SesiKuliah;
use App\Models\DosenKetersediaan;
use Illuminate\Http\Request;

class DosenAvailabilityController extends Controller
{
    public function show(Dosen $dosen)
    {
        $haris = Hari::orderBy('urutan')->get();
        $sesis = SesiKuliah::active()->get();
        
        $ketersediaan = DosenKetersediaan::where('dosen_id', $dosen->id)
            ->get()
            ->groupBy(['hari_id', 'sesi_id']);

        return view('admin.dosen.availability', compact('dosen', 'haris', 'sesis', 'ketersediaan'));
    }

    public function update(Request $request, Dosen $dosen)
    {
        $data = $request->input('availability', []);

        DosenKetersediaan::where('dosen_id', $dosen->id)->delete();

        foreach ($data as $hariId => $sesis) {
            foreach ($sesis as $sesiId => $value) {
                DosenKetersediaan::create([
                    'dosen_id' => $dosen->id,
                    'hari_id' => $hariId,
                    'sesi_id' => $sesiId,
                    'is_bersedia' => false
                ]);
            }
        }

        return redirect()->route('admin.dosen.index')->with('success', 'Ketersediaan dosen ' . $dosen->nama_lengkap . ' berhasil diperbarui.');
    }
}
