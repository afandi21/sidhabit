<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #0c6046;
            --secondary-color: #e7d1b1;
            --accent-color: #927a51;
            --bg-light: #f8f5f0;
        }

        body {
            background: linear-gradient(135deg, var(--primary-color) 0%, #052c20 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Outfit', sans-serif;
            margin: 0;
            overflow: hidden;
        }

        /* Decorative circles */
        .circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(231, 209, 177, 0.05);
            z-index: 0;
        }
        .circle-1 { width: 400px; height: 400px; top: -100px; left: -100px; }
        .circle-2 { width: 300px; height: 300px; bottom: -50px; right: -50px; }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100%;
            height: 100vh;
            background-image: 
                url('{{ asset("img/logo-stai.png") }}'),
                url('{{ asset("img/logo-stai.png") }}');
            background-position: 
                12% center, 
                88% center;
            background-size: 
                55vh auto,
                55vh auto;
            background-repeat: no-repeat;
            z-index: 1;
            pointer-events: none;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 30px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.3);
            overflow: hidden;
            width: 100%;
            max-width: 420px;
            z-index: 10;
            position: relative;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .login-header {
            background: var(--bg-light);
            padding: 40px 30px;
            text-align: center;
            border-bottom: 2px solid var(--secondary-color);
        }

        .logo-wrapper {
            width: 70px;
            height: 70px;
            background: var(--primary-color);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            box-shadow: 0 10px 20px rgba(12, 96, 70, 0.2);
            transform: rotate(-10deg);
        }
        .logo-wrapper i {
            color: var(--secondary-color);
            font-size: 2rem;
            transform: rotate(10deg);
        }

        .login-body {
            padding: 40px 35px;
        }

        .form-control {
            border-radius: 12px;
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
            background: #fdfdfd;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 4px rgba(146, 122, 81, 0.1);
            background: white;
        }

        .btn-login {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 12px;
            font-weight: 700;
            width: 100%;
            transition: all 0.4s;
            letter-spacing: 1px;
            margin-top: 10px;
            box-shadow: 0 10px 20px rgba(12, 96, 70, 0.2);
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(12, 96, 70, 0.3);
            filter: brightness(1.1);
        }

        .alert {
            border-radius: 12px;
            border: none;
        }
    </style>
</head>
<body>

    <div class="watermark"></div>
    <div class="circle circle-1"></div>
    <div class="circle circle-2"></div>

    <div class="login-card fade-in">
        <div class="login-header">
            <div class="logo-wrapper">
                <i class="bi bi-fingerprint"></i>
            </div>
            <h3 class="mb-1 fw-bold text-dark">SiDhabit</h3>
            <p class="text-muted mb-0 small text-uppercase tracking-widest fw-semibold">Sistem Biometrik Presensi Dosen</p>
        </div>
        <div class="login-body">
            @if($errors->any())
                <div class="alert alert-danger shadow-sm py-2 px-3 small mb-4">
                    <ul class="mb-0 list-unstyled">
                        @foreach($errors->all() as $error)
                            <li><i class="bi bi-exclamation-circle me-2"></i>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label small text-uppercase fw-bold text-muted tracking-wide mb-2">Email Address</label>
                    <div class="input-group">
                        <input type="email" name="email" class="form-control" placeholder="akun@university.ac.id" required value="{{ old('email') }}">
                    </div>
                </div>
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label small text-uppercase fw-bold text-muted tracking-wide mb-0">Password</label>
                        <a href="#" class="small text-decoration-none text-muted">Lupa?</a>
                    </div>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn-login">MASUK SEKARANG</button>
                
                <div class="divider my-4 d-flex align-items-center">
                    <hr class="flex-grow-1 opacity-10">
                    <span class="mx-3 small text-muted text-uppercase fw-bold tracking-tighter" style="font-size: 0.7rem;">Atau</span>
                    <hr class="flex-grow-1 opacity-10">
                </div>

                <button type="button" id="btn-biometric-login" class="btn btn-outline-primary w-100 py-3 rounded-pill border-2 fw-bold d-flex align-items-center justify-content-center">
                    <i class="bi bi-fingerprint me-2 fs-5"></i> MASUK DENGAN SIDIK JARI
                </button>
            </form>
            
            <div class="mt-5 text-center">
                <p class="small text-muted mb-0">© {{ date('Y') }} Information Technology Center</p>
            </div>
        </div>
    </div>

    <script src="{{ asset('vendor/webauthn/webauthn.js') }}"></script>
    <script>
        document.getElementById('btn-biometric-login').addEventListener('click', async () => {
            const btn = document.getElementById('btn-biometric-login');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Menunggu Sensor...';

            try {
                // Trigger WebAuthn Login
                const login = await WebAuthn.login();

                if (login.ok) {
                    window.location.href = '/'; // Redirect to home/dashboard
                } else {
                    alert('Gagal autentikasi sidik jari. Gunakan email & password.');
                    resetBtn();
                }
            } catch (error) {
                console.error(error);
                alert('Biometrik tidak tersedia atau dibatalkan.');
                resetBtn();
            }
        });

        function resetBtn() {
            const btn = document.getElementById('btn-biometric-login');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-fingerprint me-2 fs-5"></i> MASUK DENGAN SIDIK JARI';
        }
    </script>

</body>
</html>
