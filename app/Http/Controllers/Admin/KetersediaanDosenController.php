<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Hari;
use App\Models\DosenKetersediaan;
use Illuminate\Http\Request;

class KetersediaanDosenController extends Controller
{
    public function edit(Dosen $dosen)
    {
        $haris = Hari::orderBy('urutan')->get();
        // Load ketersediaan yang sudah ada
        $ketersediaan = DosenKetersediaan::where('dosen_id', $dosen->id)
                            ->pluck('is_bersedia', 'hari_id')
                            ->toArray();

        return view('admin.dosen.ketersediaan', compact('dosen', 'haris', 'ketersediaan'));
    }

    public function update(Request $request, Dosen $dosen)
    {
        $hari_ids = $request->input('hari_id', []); // Array ID hari yang dicentang (bersedia)
        $haris = Hari::all();

        foreach ($haris as $hari) {
            $is_bersedia = in_array($hari->id, $hari_ids);
            
            DosenKetersediaan::updateOrCreate(
                ['dosen_id' => $dosen->id, 'hari_id' => $hari->id],
                ['is_bersedia' => $is_bersedia]
            );
        }

        return redirect()->route('admin.dosen.index')->with('success', 'Ketersediaan dosen berhasil diperbarui.');
    }
}
