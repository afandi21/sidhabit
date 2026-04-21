@extends('layouts.app')

@section('title', 'Manajemen Perangkat')

@section('content')
<div class="row fade-enter">
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm" style="border-radius: 20px; background: linear-gradient(135deg, #0c6046 0%, #1a8a66 100%);">
            <div class="card-body p-4 text-white">
                <h4 class="fw-bold mb-1">Registrasi Biometrik</h4>
                <p class="mb-0 opacity-75">Daftarkan sidik jari smartphone Anda untuk melakukan presensi cerdas.</p>
            </div>
        </div>
    </div>

<style>
    .banking-card {
        border: none;
        border-radius: 24px;
        background: linear-gradient(145deg, #ffffff, #f0f4f8);
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .fingerprint-icon {
        width: 100px;
        height: 100px;
        background: #e9ecef;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        transition: all 0.5s ease;
        font-size: 40px;
        color: #0d6efd;
    }
    .fingerprint-active {
        background: #0d6efd;
        color: #fff;
        box-shadow: 0 0 20px rgba(13, 110, 253, 0.4);
        animation: pulse 1.5s infinite;
    }
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }
    .btn-banking {
        border-radius: 12px;
        padding: 12px 24px;
        font-weight: 600;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
    }
    .status-text {
        font-size: 0.9rem;
        font-weight: 500;
        color: #6c757d;
    }
</style>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="banking-card p-4 text-center">
                <h4 class="fw-bold mb-3">Keamanan Biometrik</h4>
                <p class="text-muted small mb-4">Aktifkan sidik jari untuk login dan presensi yang lebih cepat dan aman.</p>
                
                <div id="icon-container" class="fingerprint-icon">
                    <i class="bi bi-fingerprint"></i>
                </div>

                <div id="status-container" class="mb-4">
                    <h6 id="status-title" class="fw-bold mb-1">Siap Didaftarkan</h6>
                    <p id="status-desc" class="status-text mb-0">Klik tombol di bawah untuk memulai</p>
                </div>

                <button id="register-button" class="btn btn-primary w-100 btn-banking shadow-sm">
                    Mulai Aktivasi
                </button>
                
                <div class="mt-4 p-3 bg-light rounded-3 text-start border-0">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-shield-lock-fill text-success me-2"></i>
                        <span class="small text-muted">Data biometrik Anda aman dan tersimpan secara lokal di perangkat ini.</span>
                    </div>
                </div>
            </div>

            <!-- List Perangkat Terdaftar -->
            <div class="mt-4">
                <h6 class="fw-bold px-2 mb-3">Perangkat Terdaftar</h6>
                @forelse($devices as $device)
                    <div class="card border-0 shadow-sm rounded-4 mb-2">
                        <div class="card-body d-flex align-items-center p-3">
                            <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                                <i class="bi bi-phone text-primary"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0 fw-bold">{{ $device->alias }}</h6>
                                <span class="text-muted smaller">Aktif sejak {{ $device->created_at->format('d M Y') }}</span>
                            </div>
                            <form id="delete-device-form-{{ $device->id }}" action="{{ route('device.destroy', $device->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-link text-danger p-0" onclick="confirmDeleteDevice('{{ $device->id }}')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 bg-light rounded-4 border-dashed">
                        <i class="bi bi-info-circle text-muted mb-2 d-block fs-4"></i>
                        <span class="text-muted small">Belum ada perangkat yang ditautkan.</span>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
</div>
        </div>
    </div>

    <div class="col-md-7 mb-4">
        <div class="card border-0 shadow-sm h-100" style="display: none;">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold"><i class="bi bi-shield-check me-2 text-primary"></i> Perangkat Terdaftar</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light small text-muted">
                            <tr>
                                <th class="ps-4">Nama Perangkat / Alias</th>
                                <th>Terdaftar Pada</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($devices as $device)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-primary">{{ $device->alias ?: 'Smartphone' }}</div>
                                    <small class="text-muted">{{ $device->id }}</small>
                                </td>
                                <td>{{ $device->created_at->isoFormat('D MMMM Y') }}</td>
                                <td class="text-end pe-4">
                                    <form action="{{ route('device.destroy', $device->id) }}" method="POST" onsubmit="return confirm('Hapus perangkat ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm border-0 rounded-circle">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-5 text-muted small">
                                    <i class="bi bi-info-circle me-1"></i> Belum ada perangkat biometrik terdaftar.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmDeleteDevice(id) {
        let pwd = prompt("KEAMANAN SISTEM:\n\nMasukkan password akun Anda untuk mengkonfirmasi penghapusan perangkat biometrik ini:");
        
        if(pwd) {
            let form = document.getElementById('delete-device-form-' + id);
            
            // Tambahkan input hidden untuk mengirim password ke server
            let input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'password';
            input.value = pwd;
            form.appendChild(input);
            
            form.submit();
        }
    }

    // Embedded WebAuthn Helper (Bypass Ngrok Asset Blocking)
    class WebAuthnHelper {
        async register(alias) {
            const optResponseRaw = await fetch('/webauthn/register/options', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'ngrok-skip-browser-warning': 'true' },
                body: JSON.stringify({ alias })
            });

            if (!optResponseRaw.ok) throw new Error("Server error: " + optResponseRaw.status);

            const optResponse = await optResponseRaw.json();
            let options = optResponse.publicKey || optResponse.data || optResponse;
            if (options.publicKey) options = options.publicKey;

            const buffer = (str) => {
                if (typeof str !== 'string' || !str) return str;
                return Uint8Array.from(atob(str.replace(/-/g, "+").replace(/_/g, "/")), c => c.charCodeAt(0));
            };
            
            options.challenge = buffer(options.challenge);
            if (options.user && options.user.id) options.user.id = buffer(options.user.id);
            if (options.excludeCredentials) {
                options.excludeCredentials = options.excludeCredentials.map(c => ({ ...c, id: buffer(c.id) }));
            }

            const credential = await navigator.credentials.create({ publicKey: options });
            
            const regData = {
                id: credential.id,
                rawId: btoa(String.fromCharCode(...new Uint8Array(credential.rawId))),
                type: credential.type,
                response: {
                    attestationObject: btoa(String.fromCharCode(...new Uint8Array(credential.response.attestationObject))),
                    clientDataJSON: btoa(String.fromCharCode(...new Uint8Array(credential.response.clientDataJSON))),
                }
            };

            return await fetch('/webauthn/register', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'ngrok-skip-browser-warning': 'true' },
                body: JSON.stringify({ ...regData, alias })
            });
        }
    }

    const button = document.getElementById('register-button');
    const icon = document.getElementById('icon-container');
    const title = document.getElementById('status-title');
    const desc = document.getElementById('status-desc');

    button.addEventListener('click', async () => {
        if (!window.PublicKeyCredential) {
            alert('Perangkat tidak mendukung Biometrik.');
            return;
        }

        button.disabled = true;
        button.innerText = 'Menghubungkan...';
        icon.classList.add('fingerprint-active');
        title.innerText = 'Memverifikasi Perangkat';
        desc.innerText = 'Silakan tempelkan jari Anda pada sensor';

        try {
            const alias = prompt("Beri nama perangkat ini:", "Smartphone Saya");
            if (!alias) {
                resetUI();
                return;
            }

            const helper = new WebAuthnHelper();
            const res = await helper.register(alias);
            
            if (res.ok) {
                icon.classList.remove('fingerprint-active');
                icon.innerHTML = '<i class="bi bi-check-lg"></i>';
                icon.style.background = '#198754';
                icon.style.color = '#fff';
                title.innerText = 'Aktivasi Berhasil';
                desc.innerText = 'Perangkat Anda telah ditautkan secara aman.';
                setTimeout(() => window.location.reload(), 1500);
            } else {
                throw new Error("Gagal mendaftarkan perangkat");
            }
        } catch (error) {
            resetUI();
            alert(error.message);
        }
    });

    function resetUI() {
        button.disabled = false;
        button.innerText = 'Mulai Aktivasi';
        icon.classList.remove('fingerprint-active');
        title.innerText = 'Siap Didaftarkan';
        desc.innerText = 'Klik tombol di bawah untuk memulai';
    }
</script>
@endpush
