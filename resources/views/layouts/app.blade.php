<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0c6046">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/logo-stai.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('img/logo-stai.png') }}">

    <title>SiDhabit - @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Sidebar Collapsed by Default */
        :root {
            --primary-color: #0c6046; /* Hijau Tua */
            --secondary-color: #094a36; /* Hijau Tua (Darker) */
            --accent-color: #927a51; /* Coklat Emas/Ochre */
            --dark-bg: #0c6046;
            --sidebar-width-expanded: 260px;
            --sidebar-width-collapsed: 80px;
            --body-bg: #f9f6f0; /* Krem sangat muda untuk keterbacaan (turunan #e7d1b1) */
            --card-bg: #ffffff;
            --text-primary: #2d3748;
            --text-muted: #718096;
            --warm-bg: #e7d1b1; /* Krem/Tan */
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--body-bg);
            color: var(--text-primary);
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        #sidebar {
            width: var(--sidebar-width-collapsed);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: linear-gradient(180deg, var(--dark-bg) 0%, #063124 100%);
            color: white;
            transition: width 0.4s cubic-bezier(0.25, 0.8, 0.25, 1), box-shadow 0.4s;
            z-index: 1000;
            box-shadow: 4px 0 24px rgba(12, 96, 70, 0.15);
            overflow-x: hidden;
            overflow-y: hidden;
            white-space: nowrap;
        }

        #sidebar:hover {
            width: var(--sidebar-width-expanded);
            box-shadow: 10px 0 40px rgba(12, 96, 70, 0.2);
            overflow-y: auto;
        }

        #sidebar::-webkit-scrollbar { width: 6px; }
        #sidebar::-webkit-scrollbar-thumb { background: rgba(231, 209, 177, 0.2); border-radius: 10px; }

        /* Fading text properties */
        .sidebar-text {
            opacity: 0;
            transition: opacity 0.3s ease;
            visibility: hidden;
            display: inline-block;
            vertical-align: middle;
        }

        #sidebar:hover .sidebar-text {
            opacity: 1;
            visibility: visible;
        }

        .section-title {
            opacity: 0;
            transition: opacity 0.3s;
            height: 0;
            margin: 0 !important;
            padding: 0 !important;
            overflow: hidden;
            color: var(--warm-bg) !important;
        }

        #sidebar:hover .section-title {
            opacity: 1;
            height: auto;
            margin: 1rem 0 0.5rem 0 !important;
            padding: 0 1rem !important;
        }

        /* Nav links */
        .nav-link {
            color: #d1e3dd;
            border-radius: 12px;
            margin: 8px 12px;
            padding: 12px;
            transition: all 0.3s ease;
            font-weight: 500;
            display: flex;
            align-items: center;
        }

        #sidebar:hover .nav-link {
            padding: 12px 18px;
            margin: 4px 16px;
        }

        .nav-link:hover, .nav-link.active {
            color: #fff;
            background: rgba(231, 209, 177, 0.1);
            transform: translateX(4px);
        }

        .nav-link.active {
            background: linear-gradient(90deg, rgba(146, 122, 81, 0.2) 0%, rgba(255,255,255,0.05) 100%);
            border-left: 3px solid var(--accent-color);
        }

        .nav-link i {
            font-size: 1.4rem;
            min-width: 30px;
            text-align: center;
            margin-right: 0;
            transition: all 0.3s ease;
            opacity: 0.8;
        }

        #sidebar:hover .nav-link i {
            font-size: 1.2rem;
            margin-right: 14px;
        }

        .nav-link:hover i, .nav-link.active i {
            transform: scale(1.15) rotate(5deg);
            color: var(--warm-bg);
            opacity: 1;
        }

        #content {
            margin-left: var(--sidebar-width-collapsed);
            padding: 10px 30px 40px;
            min-height: 100vh;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        #sidebar:hover + #content {
            margin-left: var(--sidebar-width-expanded);
        }

        /* Responsive Breakpoints */
        @media (max-width: 991.98px) {
            #sidebar {
                left: calc(-1 * var(--sidebar-width-expanded));
                width: var(--sidebar-width-expanded);
                transition: left 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            }
            #sidebar.active {
                left: 0;
                z-index: 1060;
            }
            #content {
                margin-left: 0;
                padding: 10px 15px 40px;
            }
            .sidebar-text {
                opacity: 1;
                visibility: visible;
            }
            .section-title {
                opacity: 1;
                height: auto;
                margin: 1rem 0 0.5rem 0 !important;
                padding: 0 1rem !important;
            }
            #sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                background: rgba(0,0,0,0.5);
                backdrop-filter: blur(2px);
                z-index: 1055;
                display: none;
                animation: fadeIn 0.3s ease;
            }
            #sidebar-overlay.active {
                display: block;
            }
        }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        /* Cards Soft & Elevated */
        .card {
            border-radius: 20px;
            border: none;
            box-shadow: 0 4px 20px rgba(12, 96, 70, 0.04) !important;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: var(--card-bg);
            margin-bottom: 24px;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(12, 96, 70, 0.08) !important;
        }

        /* Modern Table */
        .table { --bs-table-bg: transparent; margin-bottom: 0; }
        .table thead th {
            font-weight: 600;
            color: var(--primary-color);
            letter-spacing: 0.5px;
            border-bottom: 2px solid var(--warm-bg);
            padding: 18px 16px;
            background: rgba(231, 209, 177, 0.15);
        }
        .table tbody td {
            padding: 16px;
            vertical-align: middle;
            color: var(--text-primary);
            border-bottom: 1px solid rgba(231, 209, 177, 0.3);
            transition: background-color 0.2s;
        }
        .table tbody tr:hover td { background-color: rgba(231, 209, 177, 0.08); }

        /* Buttons Premium Look */
        .btn { border-radius: 10px; font-weight: 500; padding: 8px 16px; transition: all 0.3s ease; }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            box-shadow: 0 4px 15px rgba(12, 96, 70, 0.3);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(12, 96, 70, 0.45);
            color: white;
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #e7d1b1, #d4b88f);
            color: #0c6046;
            border: none;
            box-shadow: 0 4px 15px rgba(231, 209, 177, 0.4);
            font-weight: 600;
        }
        .btn-warning:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 8px 25px rgba(231, 209, 177, 0.6); 
            color: #0c6046;
        }

        .text-primary { color: var(--primary-color) !important; }
        .bg-primary { background-color: var(--primary-color) !important; }
        .badge.bg-primary { background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)) !important; }
        .badge.bg-secondary { background-color: var(--accent-color) !important; color: white !important; }

        /* Page Loading Transition */
        .fade-enter { animation: smoothLoad 0.6s cubic-bezier(0.16, 1, 0.3, 1); }
        @keyframes smoothLoad {
            from { opacity: 0; transform: translateY(15px) scale(0.99); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* Top Navbar minimal */
        .navbar {
            backdrop-filter: blur(10px);
            background: rgba(255,255,255,0.8) !important;
            border-radius: 20px;
            padding: 10px 20px;
            box-shadow: 0 4px 20px rgba(12, 96, 70, 0.03) !important;
            margin-top: 15px;
            border-bottom: none !important;
        }

    </style>
    @stack('styles')
</head>
<body class="fade-enter">

    <div id="sidebar-overlay"></div>
    <div id="sidebar">
        <div class="px-2 py-4">
            <h4 class="mb-4 text-center d-flex align-items-center justify-content-center">
                <i class="bi bi-fingerprint text-warning" style="font-size: 1.8rem; margin-right: 8px;"></i>
                <span class="sidebar-text fw-bold tracking-wide">SiDhabit</span>
            </h4>
            <div class="nav flex-column">
                @if(auth()->user()->isDosen())
                <a href="{{ route('dosen.dashboard') }}" class="nav-link {{ Request::is('dosen/dashboard') ? 'active' : '' }}" title="Dashboard Dosen">
                    <i class="bi bi-speedometer2"></i> <span class="sidebar-text">Dashboard Dosen</span>
                </a>
                <a href="{{ route('dosen.presensi') }}" class="nav-link {{ Request::is('dosen/presensi*') ? 'active' : '' }}" title="Fingerprint Scan">
                    <i class="bi bi-fingerprint"></i> <span class="sidebar-text">Fingerprint Scan</span>
                </a>
                <a href="{{ route('dosen.riwayat') }}" class="nav-link {{ Request::is('dosen/riwayat*') ? 'active' : '' }}" title="Riwayat Presensi">
                    <i class="bi bi-clock-history"></i> <span class="sidebar-text">Riwayat Presensi</span>
                </a>
                <a href="{{ route('device.index') }}" class="nav-link {{ Request::is('dosen/device*') ? 'active' : '' }}" title="Daftar Perangkat">
                    <i class="bi bi-phone-vibrate"></i> <span class="sidebar-text">Daftar Perangkat</span>
                </a>
                <a href="{{ route('dosen.availability') }}" class="nav-link {{ Request::is('dosen/availability*') ? 'active' : '' }}" title="Ketersediaan Mengajar">
                    <i class="bi bi-calendar-x"></i> <span class="sidebar-text">Ketersediaan</span>
                </a>
                @endif

                @if(auth()->user()->isKaprodi())
                <a href="{{ route('kaprodi.dashboard') }}" class="nav-link {{ Request::is('kaprodi/dashboard') ? 'active' : '' }}" title="Dashboard Kaprodi">
                    <i class="bi bi-bar-chart-steps"></i> <span class="sidebar-text">Dashboard Kaprodi</span>
                </a>
                @endif

                @if(!auth()->user()->isDosen() && auth()->user()->isAdmin())
                <a href="{{ url('/admin/dashboard') }}" class="nav-link {{ Request::is('admin/dashboard') ? 'active' : '' }}" title="Dashboard">
                    <i class="bi bi-grid-1x2"></i> <span class="sidebar-text">Dashboard Wakil 1</span>
                </a>
                @endif
                
                @if(!auth()->user()->isDosen())
                <div class="section-title small text-uppercase text-muted fw-bold">Master Data</div>
                <a href="{{ route('admin.dosen.index') }}" class="nav-link {{ Request::is('admin/dosen*') ? 'active' : '' }}" title="Data Dosen">
                    <i class="bi bi-people"></i> <span class="sidebar-text">Data Dosen</span>
                </a>
                <a href="{{ route('admin.fakultas.index') }}" class="nav-link {{ Request::is('admin/fakultas*') || Request::is('admin/prodi*') ? 'active' : '' }}" title="Fakultas & Prodi">
                    <i class="bi bi-buildings"></i> <span class="sidebar-text">Fakultas & Prodi</span>
                </a>
                <a href="{{ route('admin.matakuliah.index') }}" class="nav-link {{ Request::is('admin/matakuliah*') ? 'active' : '' }}" title="Mata Kuliah">
                    <i class="bi bi-journals"></i> <span class="sidebar-text">Mata Kuliah</span>
                </a>
                <a href="{{ route('admin.ruangan.index') }}" class="nav-link {{ Request::is('admin/ruangan*') ? 'active' : '' }}" title="Ruangan">
                    <i class="bi bi-door-closed"></i> <span class="sidebar-text">Ruangan</span>
                </a>
                <a href="{{ route('admin.sesikuliah.index') }}" class="nav-link {{ Request::is('admin/sesikuliah*') ? 'active' : '' }}" title="Sesi Kuliah">
                    <i class="bi bi-clock"></i> <span class="sidebar-text">Sesi Kuliah</span>
                </a>
                @endif
                
                @if(!auth()->user()->isDosen())
                <div class="section-title small text-uppercase text-muted fw-bold">Akademik</div>
                <a href="{{ route('admin.beban.index') }}" class="nav-link {{ Request::is('admin/beban*') ? 'active' : '' }}" title="Beban Mengajar">
                    <i class="bi bi-briefcase"></i> <span class="sidebar-text">Beban Mengajar</span>
                </a>
                <a href="{{ route('admin.jadwal.index') }}" class="nav-link {{ Request::is('admin/jadwal*') ? 'active' : '' }}" title="Jadwal Mengajar">
                    <i class="bi bi-calendar4-week"></i> <span class="sidebar-text">Jadwal Mengajar</span>
                </a>
                <a href="{{ route('admin.laporan.harian') }}" class="nav-link {{ Request::is('admin/laporan/harian*') ? 'active' : '' }}" title="Laporan Harian">
                    <i class="bi bi-clipboard2-check"></i> <span class="sidebar-text">Laporan Harian</span>
                </a>
                <a href="{{ route('admin.laporan.bulanan') }}" class="nav-link {{ Request::is('admin/laporan/bulanan*') ? 'active' : '' }}" title="Rekap Bulanan">
                    <i class="bi bi-file-earmark-bar-graph"></i> <span class="sidebar-text">Rekap Bulanan</span>
                </a>
                <a href="{{ route('admin.izin.index') }}" class="nav-link {{ Request::is('admin/izin-cuti*') ? 'active' : '' }}" title="Izin & Cuti">
                    <i class="bi bi-envelope-paper"></i> <span class="sidebar-text">Izin & Cuti</span>
                </a>
                @endif
                
                @if(!auth()->user()->isDosen())
                <div class="section-title small text-uppercase text-muted fw-bold">Sistem</div>
                @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.users.index') }}" class="nav-link {{ Request::is('admin/users*') ? 'active' : '' }}" title="Manajemen Pengguna">
                    <i class="bi bi-person-gear"></i> <span class="sidebar-text">Manajemen Pengguna</span>
                </a>
                @endif
                @if(auth()->user()->hasRole('wakil_1_akademik'))
                <a href="{{ route('admin.pengaturan.index') }}" class="nav-link {{ Request::is('admin/pengaturan*') ? 'active' : '' }}" title="Pengaturan">
                    <i class="bi bi-gear"></i> <span class="sidebar-text">Pengaturan</span>
                </a>
                @endif
                @endif
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start" title="Keluar">
                        <i class="bi bi-box-arrow-left text-danger"></i> <span class="sidebar-text">Keluar</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div id="content">
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom mb-4">
            <div class="container-fluid">
                <button type="button" id="sidebarCollapse" class="btn btn-light d-md-none">
                    <i class="bi bi-list"></i>
                </button>
                <div class="ms-auto d-flex align-items-center">
                    <span class="me-3 d-none d-sm-inline">{{ Auth::user()->name }}</span>
                    <i class="bi bi-person-circle fs-4"></i>
                </div>
            </div>
        </nav>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const btnCollapse = document.getElementById('sidebarCollapse');

        btnCollapse?.addEventListener('click', function() {
            sidebar.classList.add('active');
            overlay.classList.add('active');
        });

        overlay?.addEventListener('click', function() {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });

        // Auto close sidebar on link click (mobile)
        document.querySelectorAll('#sidebar .nav-link').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 992) {
                    sidebar.classList.remove('active');
                    overlay.classList.remove('active');
                }
            });
        });
    </script>
    @stack('scripts')
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => console.log('Service Worker registered'))
                    .catch(err => console.log('Service Worker not registered', err));
            });
        }
    </script>
</body>
</html>
