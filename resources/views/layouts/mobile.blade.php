<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ config('app.name') }}</title>
    <link rel="apple-touch-icon" href="{{ asset('img/logo-stai.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('img/logo-stai.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #0c6046;
            --secondary-color: #e7d1b1;
            --accent-color: #927a51;
            --bg-light: #f8f5f0;
        }
        body {
            background-color: var(--bg-light);
            padding-bottom: 80px;
            font-family: 'Outfit', -apple-system, sans-serif;
            color: #333;
        }
        .app-header {
            background: var(--primary-color);
            padding: 18px 20px;
            box-shadow: 0 4px 15px rgba(12, 96, 70, 0.15);
            position: sticky;
            top: 0;
            z-index: 1000;
            color: white;
            border-bottom: 3px solid var(--accent-color);
        }
        .app-bottom-nav {
            position: fixed;
            bottom: 15px;
            left: 15px;
            right: 15px;
            background: white;
            display: flex;
            justify-content: space-around;
            padding: 12px 0;
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
            z-index: 1000;
            border-radius: 20px;
            border: 1px solid rgba(0,0,0,0.05);
        }
        .nav-item {
            text-align: center;
            color: #888;
            text-decoration: none;
            font-size: 10px;
            font-weight: 600;
            flex: 1;
            transition: all 0.3s ease;
        }
        .nav-item i {
            font-size: 22px;
            display: block;
            margin-bottom: 4px;
        }
        .nav-item.active {
            color: var(--primary-color);
            transform: translateY(-5px);
        }
        .nav-item.active i {
            color: var(--accent-color);
        }
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        }
        .btn-rounded {
            border-radius: 12px;
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    @stack('styles')
</head>
<body>

    <div class="app-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold tracking-tight"><i class="bi bi-fingerprint me-2 text-warning"></i> SiDhabit</h5>
        <div>
            <span class="badge rounded-pill py-2 px-3 text-dark shadow-sm" style="background-color: var(--secondary-color);">
                <i class="bi bi-person-fill me-1 text-primary"></i> {{ $dosen->nama_lengkap ?? Auth::user()->name }}
            </span>
        </div>
    </div>

    <div class="container mt-3">
        @yield('content')
    </div>

    <div class="app-bottom-nav">
        <a href="{{ url('/dosen/dashboard') }}" class="nav-item {{ Request::is('dosen/dashboard') ? 'active' : '' }}">
            <i class="bi bi-house-door{{ Request::is('dosen/dashboard') ? '-fill' : '' }}"></i> Home
        </a>
        <a href="{{ url('/dosen/presensi') }}" class="nav-item {{ Request::is('dosen/presensi*') ? 'active' : '' }}">
            <i class="bi bi-fingerprint"></i> Scan
        </a>
        <a href="#" class="nav-item">
            <i class="bi bi-clock-history"></i> Riwayat
        </a>
        <a href="#" class="nav-item">
            <i class="bi bi-person"></i> Profil
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @stack('scripts')
</body>
</html>
