<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Services\PresensiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PresensiController extends Controller
{
    protected $presensiService;

    public function __construct(PresensiService $presensiService)
    {
        $this->presensiService = $presensiService;
    }

    public function index()
    {
        $dosen = Auth::user()->dosen;
        $jadwalHariIni = $this->presensiService->getJadwalHariIni($dosen->id);
        
        return view('dosen.presensi.scan', compact('jadwalHariIni', 'dosen'));
    }

    public function init(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $locationCheck = $this->presensiService->validateLocation($request->lat, $request->lng);

        if (!$locationCheck['valid']) {
            return response()->json([
                'success' => false,
                'message' => $locationCheck['message'],
                'jarak' => $locationCheck['jarak']
            ], 422);
        }

        // Here we would generate WebAuthn challenge
        // For now, return success to proceed to biometric step
        return response()->json([
            'success' => true,
            'challenge' => bin2hex(random_bytes(32)), // Placeholder for real WebAuthn challenge
            'lokasi' => $locationCheck['lokasi']
        ]);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'jadwal_id' => 'required|exists:jadwal_mengajar,id',
            'type' => 'required|in:masuk,keluar',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $user = Auth::user();
        $dosen = $user->dosen;

        // Verifikasi Biometrik menggunakan Laragear WebAuthn
        // Jika request tidak membawa bukti biometrik yang valid, sistem akan menolak
        try {
            if (!$user->hasWebAuthnCredential()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda belum mendaftarkan sidik jari di menu Daftar Perangkat.'
                ], 422);
            }

            // Validasi nyata terhadap data "assertion" dari sidik jari (WebAuthn)
            if (!$request->has('assertion')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data biometrik tidak dikirim. Harap scan jari Anda.'
                ], 422);
            }

            // Melakukan verifikasi kriptografi WebAuthn
            $validated = \Laragear\WebAuthn\Facades\WebAuthn::assert()
                ->fromRequest($request, 'assertion')
                ->verify();

            if (!$validated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sidik jari tidak cocok dengan perangkat ini.'
                ], 422);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sensor biometrik atau sesi telah kadaluarsa. Coba muat ulang halaman.'
            ], 422);
        }

        // Jika lolos verifikasi biometrik, baru jalankan logika absen
        if ($request->type === 'masuk') {
            $result = $this->presensiService->clockIn(
                $dosen,
                $request->jadwal_id,
                $request->lat,
                $request->lng
            );
        } else {
            $result = $this->presensiService->clockOut(
                $dosen,
                $request->jadwal_id,
                $request->lat,
                $request->lng
            );
        }

        return response()->json($result);
    }
}
