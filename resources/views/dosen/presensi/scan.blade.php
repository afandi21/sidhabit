@extends('layouts.app')

@section('title', 'Scan Presensi')

@section('content')
<div class="row fade-enter">
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm" style="border-radius: 20px; background: linear-gradient(135deg, #927a51 0%, #b59a6d 100%);">
            <div class="card-body p-4 text-white">
                <h4 class="fw-bold mb-1">Presensi Cerdas</h4>
                <p class="mb-0 opacity-75">Gunakan sidik jari & GPS untuk melakukan absensi hari ini.</p>
            </div>
        </div>
    </div>

    <div class="col-md-5 mb-4">
        <div class="card border-0 shadow-sm" style="border-radius: 15px;">
            <div class="card-body p-4 text-center">
                <div id="location-card" class="mb-4">
                    <div class="status-icon mb-3">
                        <div class="bg-light text-muted rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="bi bi-geo-alt" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                    <h6 class="fw-bold mb-1">Status Lokasi</h6>
                    <div id="location-status" class="badge bg-light text-muted rounded-pill px-3 py-2">
                        <span class="spinner-border spinner-border-sm me-2"></span> Mencari GPS...
                    </div>
                    <p id="distance-info" class="text-muted small mt-2 mb-0"></p>
                </div>

                <hr class="opacity-10 my-4">

                <div id="biometric-card">
                    <div class="status-icon mb-3">
                        <div id="fp-bg" class="bg-light text-muted rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="bi bi-fingerprint" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                    <h6 class="fw-bold mb-1">Verifikasi Biometrik</h6>
                    <div id="biometric-status" class="small text-muted mb-4">
                        Menunggu verifikasi lokasi...
                    </div>
                    
                    <button type="button" id="btn-scan" class="btn btn-secondary w-100 py-3 rounded-pill shadow-sm" disabled>
                        <i class="bi bi-shield-lock me-2"></i> Konfirmasi Sidik Jari
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-7 mb-4">
        <div class="card border-0 shadow-sm" style="border-radius: 15px;">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">Jadwal Mengajar Hari Ini</h6>
                <span class="badge bg-primary rounded-pill">{{ date('d M Y') }}</span>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($jadwalHariIni as $jadwal)
                        @php
                            $presensi = \App\Models\Presensi::where('dosen_id', $dosen->id)
                                ->where('jadwal_mengajar_id', $jadwal->id)
                                ->where('tanggal', today())
                                ->first();
                        @endphp
                        <div class="list-group-item p-4 border-0 border-bottom">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h6 class="fw-bold text-primary mb-1">{{ $jadwal->mataKuliah->nama_mk }}</h6>
                                    <div class="small text-muted">
                                        <i class="bi bi-door-open me-1"></i> Ruang {{ $jadwal->ruangan->nama_ruangan }} | 
                                        <i class="bi bi-clock me-1"></i> {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}
                                    </div>
                                </div>
                                @if($presensi && $presensi->jam_keluar)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Selesai</span>
                                @elseif($presensi)
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">Mengajar</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-muted border border-secondary-subtle rounded-pill">Belum Absen</span>
                                @endif
                            </div>

                            <div class="row g-2">
                                <div class="col-6">
                                    <button onclick="handleAbsen({{ $jadwal->id }}, 'masuk')" 
                                            class="btn btn-{{ ($presensi) ? 'light' : 'success' }} w-100 py-2 rounded-3 shadow-sm" 
                                            {{ ($presensi) ? 'disabled' : '' }}>
                                        <i class="bi bi-box-arrow-in-right me-1"></i> Absen Masuk
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button onclick="handleAbsen({{ $jadwal->id }}, 'keluar')" 
                                            class="btn btn-{{ (!$presensi || $presensi->jam_keluar) ? 'light' : 'danger' }} w-100 py-2 rounded-3 shadow-sm" 
                                            {{ (!$presensi || $presensi->jam_keluar) ? 'disabled' : '' }}>
                                        <i class="bi bi-box-arrow-right me-1"></i> Absen Pulang
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-5 text-center text-muted">
                            <i class="bi bi-calendar-x mb-2 d-block" style="font-size: 3rem;"></i>
                            <p class="mb-0">Tidak ada jadwal mengajar untuk Anda hari ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Loading/Success --}}
<div class="modal fade" id="resultModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0" style="border-radius: 20px;">
            <div class="modal-body text-center p-4" id="modal-content">
                <div class="spinner-border text-primary mb-3" role="status"></div>
                <h6 class="fw-bold">Memproses Presensi...</h6>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Embedded WebAuthn Helper (Bypass Ngrok Asset Blocking)
    class WebAuthnHelper {
        async login() {
            const optResponseRaw = await fetch('/webauthn/login/options', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json', 
                    'Accept': 'application/json', 
                    'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                    'ngrok-skip-browser-warning': 'true' 
                }
            });

            if (!optResponseRaw.ok) {
                const text = await optResponseRaw.text();
                console.error("Server Error Response (Login):", text);
                throw new Error("Server error: " + optResponseRaw.status);
            }

            const optResponse = await optResponseRaw.json();
            console.log("Server JSON Response (Login):", optResponse);

            let options = optResponse.publicKey || optResponse.data || optResponse;
            if (options.publicKey) options = options.publicKey;

            const buffer = (str) => {
                if (typeof str !== 'string' || !str) return str;
                try {
                    return Uint8Array.from(atob(str.replace(/-/g, "+").replace(/_/g, "/")), c => c.charCodeAt(0));
                } catch (e) {
                    console.error("Gagal decode:", str);
                    return str;
                }
            };
            
            if (!options.challenge) throw new Error("Challenge tidak ditemukan.");

            options.challenge = buffer(options.challenge);
            if (options.allowCredentials) {
                options.allowCredentials = options.allowCredentials.map(c => ({ ...c, id: buffer(c.id) }));
            }

            console.log("Processed Options (Login):", options);

            const assertion = await navigator.credentials.get({ publicKey: options });
            
            return {
                id: assertion.id,
                rawId: btoa(String.fromCharCode(...new Uint8Array(assertion.rawId))),
                type: assertion.type,
                response: {
                    authenticatorData: btoa(String.fromCharCode(...new Uint8Array(assertion.response.authenticatorData))),
                    clientDataJSON: btoa(String.fromCharCode(...new Uint8Array(assertion.response.clientDataJSON))),
                    signature: btoa(String.fromCharCode(...new Uint8Array(assertion.response.signature))),
                    userHandle: assertion.response.userHandle ? btoa(String.fromCharCode(...new Uint8Array(assertion.response.userHandle))) : null,
                }
            };
        }
    }

    let userLocation = { lat: null, lng: null };
    // ... (sisa variabel tetap sama)
    let locationValid = false;
    let currentJadwalId = null;
    let currentType = null;
    const modal = new bootstrap.Modal(document.getElementById('resultModal'));

    // ... (fungsi initLocation, checkRadius, dll tetap sama)

    // 1. Get Location Automatically
    function initLocation() {
        if (!navigator.geolocation) {
            updateLocationStatus('danger', 'GPS Tidak Didukung');
            return;
        }

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                userLocation.lat = pos.coords.latitude;
                userLocation.lng = pos.coords.longitude;
                checkRadius();
            },
            (err) => {
                updateLocationStatus('danger', 'Gagal Akses GPS');
            },
            { enableHighAccuracy: true }
        );
    }

    async function checkRadius() {
        try {
            const response = await fetch('/dosen/presensi/init', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'ngrok-skip-browser-warning': 'true'
                },
                body: JSON.stringify(userLocation)
            });
            
            const data = await response.json();
            
            if (data.success) {
                locationValid = true;
                updateLocationStatus('success', 'Lokasi Siap: ' + data.lokasi);
                document.getElementById('distance-info').innerText = 'Jarak: ' + data.jarak + 'm dari Kampus';
                enableBiometric();
            } else {
                locationValid = false;
                updateLocationStatus('danger', 'Diluar Radius');
                document.getElementById('distance-info').innerText = 'Jarak Anda: ' + data.jarak + 'm (Maks 50m)';
            }
        } catch (e) {
            updateLocationStatus('danger', 'Error Koneksi');
        }
    }

    function updateLocationStatus(type, text) {
        const el = document.getElementById('location-status');
        el.className = `badge bg-${type}-subtle text-${type} rounded-pill px-3 py-2`;
        el.innerHTML = `<i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i> ${text}`;
    }

    function enableBiometric() {
        const btn = document.getElementById('btn-scan');
        const bg = document.getElementById('fp-bg');
        const status = document.getElementById('biometric-status');
        
        btn.disabled = false;
        btn.classList.replace('btn-secondary', 'btn-primary');
        bg.classList.replace('bg-light', 'bg-primary-subtle');
        bg.classList.replace('text-muted', 'text-primary');
        status.innerText = 'Siap melakukan scan sidik jari.';
    }

    function handleAbsen(jadwalId, type) {
        if (!locationValid) {
            alert('Anda harus berada di radius 50m kampus untuk melakukan presensi.');
            return;
        }
        currentJadwalId = jadwalId;
        currentType = type;
        
        // Trigger Biometric Scan
        document.getElementById('btn-scan').click();
    }

    document.getElementById('btn-scan').addEventListener('click', async () => {
        if (!currentJadwalId) {
            alert('Pilih jadwal terlebih dahulu di tabel kanan.');
            return;
        }

        // 1. Cek Dukungan Biometrik
        if (!window.PublicKeyCredential) {
            alert('Perangkat Anda tidak mendukung fitur keamanan biometrik.');
            return;
        }

        try {
            const helper = new WebAuthnHelper();
            const authData = await helper.login();
            
            if (authData) {
                submitPresensi(authData);
            }
        } catch (error) {
            console.error(error);
            alert('Gagal Verifikasi: ' + (error.name === 'NotAllowedError' ? 'Sidik jari tidak cocok.' : 'Sensor bermasalah.'));
        }
    });

    async function submitPresensi(authData) {
        modal.show();
        try {
            const response = await fetch('/dosen/presensi/verify', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'ngrok-skip-browser-warning': 'true'
                },
                body: JSON.stringify({
                    jadwal_id: currentJadwalId,
                    type: currentType,
                    lat: userLocation.lat,
                    lng: userLocation.lng,
                    assertion: authData // Package will handle validation
                })
            });

            const data = await response.json();
            
            const modalContent = document.getElementById('modal-content');
            if (data.success) {
                modalContent.innerHTML = `
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                    <h5 class="fw-bold mt-3">Berhasil!</h5>
                    <p class="small text-muted mb-0">${data.message}</p>
                `;
                setTimeout(() => window.location.reload(), 2000);
            } else {
                modalContent.innerHTML = `
                    <i class="bi bi-x-circle-fill text-danger" style="font-size: 3rem;"></i>
                    <h5 class="fw-bold mt-3">Gagal</h5>
                    <p class="small text-muted mb-0">${data.message}</p>
                    <button class="btn btn-sm btn-outline-secondary mt-3" data-bs-dismiss="modal">Tutup</button>
                `;
            }
        } catch (e) {
            modal.hide();
            alert('Terjadi kesalahan koneksi saat mengirim data.');
        }
    }

    // Start GPS on load
    window.onload = initLocation;
</script>

<style>
    #btn-scan {
        transition: all 0.3s ease;
    }
    #btn-scan:not(:disabled):hover {
        transform: scale(1.02);
    }
    .status-icon i {
        transition: all 0.5s ease;
    }
</style>
@endpush
