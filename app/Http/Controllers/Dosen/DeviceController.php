<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeviceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // Ambil kredensial webauthn yang terdaftar untuk user ini
        $devices = $user->webAuthnCredentials()->get();
        
        return view('dosen.device.index', compact('devices'));
    }

    public function destroy(\App\Http\Requests\DestroyDeviceRequest $request, $id)
    {
        $user = Auth::user();
        $device = $user->webAuthnCredentials()->findOrFail($id);
        $device->delete();

        return back()->with('success', 'Perangkat/Sidik jari berhasil dihapus.');
    }
}
