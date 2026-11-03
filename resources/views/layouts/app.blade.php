<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIMBA') - Bapas Kelas I Banjarmasin</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --bapas-navy: #0D2A4A;
            --bapas-navy-light: #163B65;
            --bapas-navy-dark: #07192C;
            --bapas-gold: #D4AF37;
            --bapas-gold-dark: #B8860B;
            --bapas-accent: #0284C7;
            --sidebar-width: 260px;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #F8FAFC;
            color: #1E293B;
            min-height: 100vh;
        }

        /* Sidebar Styling */
        #sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: linear-gradient(180deg, var(--bapas-navy) 0%, var(--bapas-navy-dark) 100%);
            color: #E2E8F0;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1040;
            transition: all 0.3s ease;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.05);
        }

        #sidebar .brand-header {
            padding: 1.25rem 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            background-color: rgba(0, 0, 0, 0.15);
        }

        #sidebar .nav-link {
            color: #CBD5E1;
            font-size: 0.9rem;
            font-weight: 500;
            padding: 0.75rem 1.25rem;
            border-radius: 8px;
            margin: 0.2rem 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.2s ease;
        }

        #sidebar .nav-link:hover {
            color: #FFFFFF;
            background-color: rgba(255, 255, 255, 0.08);
            transform: translateX(3px);
        }

        #sidebar .nav-link.active {
            color: #FFFFFF;
            background: linear-gradient(90deg, #0284C7 0%, #0369A1 100%);
            box-shadow: 0 4px 10px rgba(2, 132, 199, 0.35);
            font-weight: 600;
        }

        #sidebar .nav-section-title {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.075em;
            color: #94A3B8;
            padding: 1rem 1.5rem 0.35rem;
            font-weight: 700;
        }

        /* Main Content Wrapper */
        #main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.3s ease;
        }

        /* Topbar Header */
        #topbar {
            background-color: #FFFFFF;
            border-bottom: 1px solid #E2E8F0;
            padding: 0.85rem 1.75rem;
            position: sticky;
            top: 0;
            z-index: 1020;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }

        .avatar-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--bapas-gold);
        }

        /* Cards & Components */
        .card {
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stat-card {
            border-radius: 14px;
            border: none;
            overflow: hidden;
            position: relative;
        }

        .stat-card .stat-icon-wrapper {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .table > :not(caption) > * > * {
            padding: 0.85rem 1rem;
            vertical-align: middle;
        }

        .table thead th {
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 700;
            color: #64748B;
            background-color: #F8FAFC;
            border-bottom: 1px solid #E2E8F0;
        }

        .badge-status {
            padding: 0.4rem 0.75rem;
            font-weight: 600;
            font-size: 0.75rem;
            border-radius: 20px;
        }

        /* Modals */
        .modal-content {
            border-radius: 14px;
            border: none;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .modal-header {
            background-color: #F8FAFC;
            border-bottom: 1px solid #E2E8F0;
            border-top-left-radius: 14px;
            border-top-right-radius: 14px;
        }

        /* Mobile Responsive */
        @media (max-width: 991.98px) {
            #sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }
            #sidebar.show {
                margin-left: 0;
            }
            #main-wrapper {
                margin-left: 0;
            }
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Sidebar Navigation -->
    <aside id="sidebar">
        <div class="brand-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <div style="width: 38px; height: 38px; border-radius: 8px; background: linear-gradient(135deg, #D4AF37 0%, #B8860B 100%); display: flex; align-items: center; justify-content: center; color: #0D2A4A; font-weight: 800; font-size: 1.15rem; box-shadow: 0 2px 6px rgba(212,175,55,0.4);">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div>
                    <h6 class="mb-0 text-white fw-bold" style="letter-spacing: 0.5px;">SIMBA</h6>
                    <small class="text-white-50" style="font-size: 0.72rem;">Bapas Kelas I Banjarmasin</small>
                </div>
            </div>
            <button class="btn btn-sm text-white d-lg-none" id="sidebarCloseBtn">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="py-3">
            @if(Auth::user()->isAdmin())
                <!-- Menu Admin / Bagian Umum -->
                <div class="nav-section-title">Panel Bagian Umum</div>
                <nav class="nav flex-column">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-grid-1x2-fill"></i>
                        <span>Dashboard</span>
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.surat-masuk.*') ? 'active' : '' }}" href="{{ route('admin.surat-masuk.index') }}">
                        <i class="bi bi-inbox-fill"></i>
                        <span>Surat Masuk</span>
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.surat-keluar.*') ? 'active' : '' }}" href="{{ route('admin.surat-keluar.index') }}">
                        <i class="bi bi-send-fill"></i>
                        <span>Surat Keluar</span>
                    </a>
                </nav>

                <div class="nav-section-title">Akun & Pengaturan</div>
                <nav class="nav flex-column">
                    <a class="nav-link {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}" href="{{ route('admin.profile.index') }}">
                        <i class="bi bi-person-badge-fill"></i>
                        <span>Profil Saya</span>
                    </a>
                </nav>
            @else
                <!-- Menu Pembimbing Kemasyarakatan (PK) -->
                <div class="nav-section-title">Panel Pembimbing (PK)</div>
                <nav class="nav flex-column">
                    <a class="nav-link {{ request()->routeIs('pk.dashboard') ? 'active' : '' }}" href="{{ route('pk.dashboard') }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard PK</span>
                    </a>
                    <a class="nav-link {{ request()->routeIs('pk.klien.*') ? 'active' : '' }}" href="{{ route('pk.klien.index') }}">
                        <i class="bi bi-people-fill"></i>
                        <span>Data Klien Bapas</span>
                    </a>
                    <a class="nav-link {{ request()->routeIs('pk.atur-jadwal.*') ? 'active' : '' }}" href="{{ route('pk.atur-jadwal.index') }}">
                        <i class="bi bi-clock-history"></i>
                        <span>Atur Jadwal Bimbingan</span>
                    </a>
                    <a class="nav-link {{ request()->routeIs('pk.jadwal.*') ? 'active' : '' }}" href="{{ route('pk.jadwal.index') }}">
                        <i class="bi bi-calendar-check-fill"></i>
                        <span>Jadwal Bimbingan</span>
                    </a>
                    <a class="nav-link {{ request()->routeIs('pk.kehadiran.*') ? 'active' : '' }}" href="{{ route('pk.kehadiran.index') }}">
                        <i class="bi bi-camera-fill"></i>
                        <span>Bukti Kehadiran Klien</span>
                    </a>
                </nav>

                <div class="nav-section-title">Akun & Pengaturan</div>
                <nav class="nav flex-column">
                    <a class="nav-link {{ request()->routeIs('pk.profile.*') ? 'active' : '' }}" href="{{ route('pk.profile.index') }}">
                        <i class="bi bi-person-circle"></i>
                        <span>Profil PK</span>
                    </a>
                </nav>
            @endif
        </div>

        <div class="position-absolute bottom-0 w-100 p-3" style="border-top: 1px solid rgba(255,255,255,0.08); background-color: rgba(0,0,0,0.2);">
            <div class="d-flex align-items-center gap-2">
                <img src="{{ Auth::user()->foto_url }}" alt="Avatar" class="avatar-circle" style="width: 36px; height: 36px;">
                <div class="text-truncate" style="max-width: 140px;">
                    <div class="fw-semibold text-white small text-truncate">{{ Auth::user()->nama_pegawai }}</div>
                    <div class="text-white-50" style="font-size: 0.7rem;">NIP: {{ Auth::user()->nip_usn }}</div>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Wrapper -->
    <div id="main-wrapper">
        <!-- Topbar -->
        <header id="topbar" class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-outline-secondary btn-sm d-lg-none" id="sidebarToggleBtn">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div>
                    <h5 class="mb-0 fw-bold text-dark">@yield('page_title', 'Sistem Informasi SIMBA')</h5>
                    <small class="text-muted d-none d-sm-inline">
                        <i class="bi bi-calendar3 me-1"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                    </small>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <div class="text-end d-none d-md-block">
                    <div class="fw-bold text-dark small">{{ Auth::user()->nama_pegawai }}</div>
                    <span class="badge {{ Auth::user()->isAdmin() ? 'bg-primary' : 'bg-success' }} text-white" style="font-size: 0.7rem;">
                        {{ Auth::user()->role->nama_role ?? (Auth::user()->isAdmin() ? 'Admin / Bagian Umum' : 'Pembimbing Kemasyarakatan') }}
                    </span>
                </div>

                <div class="dropdown">
                    <button class="btn p-0 border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="{{ Auth::user()->foto_url }}" alt="Avatar" class="avatar-circle">
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 py-2" style="border-radius: 10px;">
                        <li>
                            <div class="px-3 py-2 border-bottom">
                                <p class="mb-0 fw-semibold text-dark">{{ Auth::user()->nama_pegawai }}</p>
                                <small class="text-muted">{{ Auth::user()->jabatan }}</small>
                            </div>
                        </li>
                        <li>
                            @if(Auth::user()->isAdmin())
                                <a class="dropdown-item py-2" href="{{ route('admin.profile.index') }}">
                                    <i class="bi bi-person me-2 text-primary"></i> Profil Saya
                                </a>
                            @else
                                <a class="dropdown-item py-2" href="{{ route('pk.profile.index') }}">
                                    <i class="bi bi-person me-2 text-success"></i> Profil PK
                                </a>
                            @endif
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger py-2">
                                    <i class="bi bi-box-arrow-right me-2"></i> Keluar Sistem
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Flash Messages & Alerts -->
        <main class="flex-grow-1 p-3 p-lg-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm border-0 mb-4" role="alert">
                    <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm border-0 mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                    <div class="d-flex align-items-center gap-2 fw-semibold mb-2">
                        <i class="bi bi-exclamation-octagon-fill text-danger fs-5"></i>
                        <span>Mohon periksa kembali formulir berikut:</span>
                    </div>
                    <ul class="mb-0 ps-3 small">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Main Content Area -->
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-top py-3 px-4 text-center text-muted small mt-auto">
            <div>
                &copy; {{ date('Y') }} <strong>SIMBA (Sistem Informasi Balai Pemasyarakatan)</strong> &bull; Bapas Kelas I Banjarmasin.
            </div>
            <div style="font-size: 0.75rem;" class="text-secondary mt-1">
                Kementerian Hukum dan Hak Asasi Manusia Republik Indonesia &bull; Kerja Sama Magang D3 TI Politeknik Negeri Banjarmasin
            </div>
        </footer>
    </div>

    <!-- Bootstrap 5.3 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Sidebar toggle for mobile
        const sidebar = document.getElementById('sidebar');
        const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
        const sidebarCloseBtn = document.getElementById('sidebarCloseBtn');

        if (sidebarToggleBtn) {
            sidebarToggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('show');
            });
        }
        if (sidebarCloseBtn) {
            sidebarCloseBtn.addEventListener('click', () => {
                sidebar.classList.remove('show');
            });
        }
    </script>
    @yield('scripts')
</body>
</html>
