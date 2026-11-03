<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIMBA') - Bapas Kelas I Banjarmasin</title>

    <!-- Google Fonts: Poppins (Mandatory) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --simba-navy: #0D2A4A;
            --simba-navy-dark: #081D33;
            --simba-navy-light: #163E6B;
            --simba-gold: #D4AF37;
            --simba-bg: #F4F7FB;
            --simba-border: #E2E8F0;
            --simba-text: #1E293B;
            --simba-muted: #64748B;
            --topbar-height: 68px;
            --sidebar-width: 240px;
        }

        * {
            font-family: 'Poppins', sans-serif !important;
        }

        body {
            background-color: var(--simba-bg);
            color: var(--simba-text);
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        /* -------------------------------------------------------------
           1. Top Navbar Header (Deep Navy Blue #0D2A4A)
           ------------------------------------------------------------- */
        #topbar-header {
            height: var(--topbar-height);
            background-color: var(--simba-navy);
            color: #FFFFFF;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1050;
            padding: 0 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.12);
        }

        .header-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: #FFFFFF;
        }

        .header-brand .logos-container {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .header-brand img.brand-logo {
            height: 38px;
            width: auto;
            object-fit: contain;
        }

        .header-brand .brand-divider {
            width: 1px;
            height: 32px;
            background-color: rgba(255, 255, 255, 0.35);
            margin: 0 0.25rem;
        }

        .header-brand .brand-titles {
            display: flex;
            flex-direction: column;
            line-height: 1.25;
        }

        .header-brand .brand-title-main {
            font-size: 0.95rem;
            font-weight: 600;
            letter-spacing: 0.3px;
            color: #FFFFFF;
        }

        .header-brand .brand-title-sub {
            font-size: 0.78rem;
            font-weight: 400;
            color: rgba(255, 255, 255, 0.8);
        }

        .header-user-info {
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }

        .header-user-details {
            text-align: right;
            line-height: 1.25;
        }

        .header-user-name {
            font-size: 0.88rem;
            font-weight: 600;
            color: #FFFFFF;
        }

        .header-user-role {
            font-size: 0.72rem;
            color: #94A3B8;
        }

        .header-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            border: 2px solid #FFFFFF;
            object-fit: cover;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        }

        /* -------------------------------------------------------------
           2. Sidebar Navigation (Clean Menu Pills on Soft Background)
           ------------------------------------------------------------- */
        #simba-sidebar {
            width: var(--sidebar-width);
            position: fixed;
            top: var(--topbar-height);
            left: 0;
            bottom: 0;
            background-color: var(--simba-bg);
            border-right: 1px solid var(--simba-border);
            padding: 1.5rem 0.85rem 1rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            z-index: 1040;
            overflow-y: auto;
            transition: all 0.3s ease;
        }

        .sidebar-section-label {
            font-size: 0.72rem;
            font-weight: 600;
            color: #8C9BAE;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 0.75rem;
            padding-left: 0.85rem;
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .sidebar-nav-item {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding: 0.7rem 1.15rem;
            border-radius: 50px;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            color: #334155;
            background-color: #FFFFFF;
            border: 1px solid var(--simba-border);
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            transition: all 0.2s ease;
        }

        .sidebar-nav-item:hover {
            color: var(--simba-navy);
            background-color: #F8FAFC;
            border-color: #CBD5E1;
            transform: translateX(2px);
        }

        .sidebar-nav-item.active {
            background-color: var(--simba-navy);
            color: #FFFFFF;
            border-color: var(--simba-navy);
            box-shadow: 0 4px 12px rgba(13, 42, 74, 0.25);
            font-weight: 600;
        }

        .sidebar-nav-item i {
            font-size: 1.05rem;
        }

        .sidebar-nav-item.active i {
            color: #FFFFFF;
        }

        .sidebar-footer {
            padding-top: 1rem;
            border-top: 1px solid var(--simba-border);
        }

        .sidebar-logout-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.65rem;
            width: 100%;
            padding: 0.65rem 1rem;
            border-radius: 50px;
            border: 1px solid #FECACA;
            background-color: #FEF2F2;
            color: #DC2626;
            font-size: 0.82rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .sidebar-logout-btn:hover {
            background-color: #DC2626;
            color: #FFFFFF;
            border-color: #DC2626;
        }

        /* -------------------------------------------------------------
           3. Content Area & Layout
           ------------------------------------------------------------- */
        #content-wrapper {
            margin-left: var(--sidebar-width);
            margin-top: var(--topbar-height);
            padding: 2rem 2.25rem;
            min-height: calc(100vh - var(--topbar-height));
            transition: margin-left 0.3s ease;
        }

        .page-header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.75rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .page-title-main {
            font-size: 1.45rem;
            font-weight: 700;
            color: var(--simba-navy);
            margin-bottom: 0.15rem;
        }

        .page-subtitle-main {
            font-size: 0.82rem;
            color: var(--simba-muted);
            margin-bottom: 0;
        }

        .btn-simba-primary {
            background-color: var(--simba-navy);
            color: #FFFFFF;
            font-size: 0.82rem;
            font-weight: 600;
            padding: 0.65rem 1.4rem;
            border-radius: 50px;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 4px 10px rgba(13, 42, 74, 0.2);
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-simba-primary:hover {
            background-color: var(--simba-navy-light);
            color: #FFFFFF;
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(13, 42, 74, 0.3);
        }

        /* -------------------------------------------------------------
           4. Metric Cards (White Cards, Big Centered Numbers)
           ------------------------------------------------------------- */
        .metric-card-figma {
            background-color: #FFFFFF;
            border-radius: 14px;
            border: 1px solid #EAEFF5;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.025);
            padding: 1.25rem 1rem;
            text-align: center;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .metric-card-label {
            font-size: 0.82rem;
            font-weight: 500;
            color: var(--simba-muted);
            margin-bottom: 0.35rem;
        }

        .metric-card-number {
            font-size: 2.25rem;
            font-weight: 700;
            color: var(--simba-navy);
            line-height: 1.1;
        }

        /* -------------------------------------------------------------
           5. Table Cards (Solid Deep Navy Thead with White Text)
           ------------------------------------------------------------- */
        .table-card-figma {
            background-color: #FFFFFF;
            border-radius: 14px;
            border: 1px solid #EAEFF5;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .table-filter-bar {
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            border-bottom: 1px solid #F1F5F9;
        }

        .table-simba {
            margin-bottom: 0;
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-simba thead th {
            background-color: var(--simba-navy) !important;
            color: #FFFFFF !important;
            font-size: 0.78rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 0.95rem 1.15rem;
            border: none;
            vertical-align: middle;
        }

        .table-simba tbody td {
            padding: 0.95rem 1.15rem;
            font-size: 0.84rem;
            color: #334155;
            border-bottom: 1px solid #F1F5F9;
            vertical-align: middle;
            background-color: #FFFFFF;
        }

        .table-simba tbody tr:hover td {
            background-color: #F8FAFC;
        }

        /* Action Icon Buttons */
        .btn-action-icon {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            border: none;
            background: transparent;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-action-view {
            color: var(--simba-navy);
        }
        .btn-action-view:hover {
            background-color: #EBF3FF;
            color: #0284C7;
        }

        .btn-action-edit {
            color: #D97706;
        }
        .btn-action-edit:hover {
            background-color: #FEF3C7;
            color: #B45309;
        }

        .btn-action-delete {
            color: #DC2626;
        }
        .btn-action-delete:hover {
            background-color: #FEE2E2;
            color: #B91C1C;
        }

        /* Status Badges */
        .badge-simba {
            padding: 0.35rem 0.8rem;
            border-radius: 50px;
            font-size: 0.74rem;
            font-weight: 600;
            display: inline-block;
        }

        .badge-simba-success {
            background-color: #ECFDF5;
            color: #059669;
            border: 1px solid #A7F3D0;
        }

        .badge-simba-info {
            background-color: #EFF6FF;
            color: #2563EB;
            border: 1px solid #BFDBFE;
        }

        .badge-simba-warning {
            background-color: #FFFBEB;
            color: #D97706;
            border: 1px solid #FDE68A;
        }

        .badge-simba-danger {
            background-color: #FEF2F2;
            color: #DC2626;
            border: 1px solid #FECACA;
        }

        /* -------------------------------------------------------------
           6. Modals (Clean Figma Style)
           ------------------------------------------------------------- */
        .modal-content-figma {
            border-radius: 14px;
            border: none;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }

        .modal-header-figma {
            padding: 1.5rem 1.75rem 1rem;
            background-color: #FFFFFF;
            border-bottom: 1px solid #F1F5F9;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
        }

        .modal-title-figma {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--simba-navy);
            margin-bottom: 0.2rem;
        }

        .modal-subtitle-figma {
            font-size: 0.78rem;
            color: var(--simba-muted);
            margin-bottom: 0;
        }

        .upload-dropzone-figma {
            border: 2px dashed #93C5FD;
            background-color: #F8FAFC;
            border-radius: 12px;
            padding: 1.75rem 1.25rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .upload-dropzone-figma:hover {
            background-color: #EFF6FF;
            border-color: #3B82F6;
        }

        .modal-footer-figma {
            padding: 1.25rem 1.75rem;
            background-color: #FFFFFF;
            border-top: 1px solid #F1F5F9;
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
        }

        /* Modal Delete Warning Badge */
        .delete-icon-wrapper {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background-color: #FEE2E2;
            color: #DC2626;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.85rem;
            margin-bottom: 1rem;
        }

        /* Mobile View */
        @media (max-width: 991.98px) {
            #simba-sidebar {
                transform: translateX(-100%);
            }
            #simba-sidebar.show {
                transform: translateX(0);
            }
            #content-wrapper {
                margin-left: 0;
                padding: 1.25rem;
            }
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- 1. Top Navbar Header -->
    <header id="topbar-header">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm text-white d-lg-none p-0" id="sidebarToggleBtn" aria-label="Toggle Menu">
                <i class="bi bi-list fs-3"></i>
            </button>
            <a href="{{ Auth::user()->isAdmin() ? route('admin.dashboard') : route('pk.dashboard') }}" class="header-brand">
                <div class="logos-container">
                    <img src="{{ asset('assets/images/logo_poliban.svg') }}" alt="Logo Poliban" class="brand-logo">
                    <img src="{{ asset('assets/images/logo_kemenkumham.svg') }}" alt="Logo Kemenkumham" class="brand-logo">
                </div>
                <div class="brand-divider"></div>
                <div class="brand-titles">
                    <span class="brand-title-main">Balai Pemasyarakatan</span>
                    <span class="brand-title-sub">Kelas I Banjarmasin</span>
                </div>
            </a>
        </div>

        <div class="header-user-info">
            <div class="header-user-details d-none d-sm-block">
                <div class="header-user-name">{{ Auth::user()->nama_pegawai }}</div>
                <div class="header-user-role">{{ Auth::user()->role->nama_role ?? (Auth::user()->isAdmin() ? 'Admin / Bagian Umum' : 'Pembimbing Kemasyarakatan') }}</div>
            </div>
            <div class="dropdown">
                <button class="btn p-0 border-0" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ Auth::user()->foto_url }}" alt="Avatar" class="header-avatar">
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 py-2" style="border-radius: 12px; font-size: 0.85rem;">
                    <li>
                        <div class="px-3 py-2 border-bottom">
                            <div class="fw-bold text-dark">{{ Auth::user()->nama_pegawai }}</div>
                            <small class="text-muted">NIP: {{ Auth::user()->nip_usn }}</small>
                        </div>
                    </li>
                    <li>
                        @if(Auth::user()->isAdmin())
                            <a class="dropdown-item py-2" href="{{ route('admin.profile.index') }}">
                                <i class="bi bi-person me-2 text-primary"></i> Profil Saya
                            </a>
                        @else
                            <a class="dropdown-item py-2" href="{{ route('pk.profile.index') }}">
                                <i class="bi bi-person me-2 text-primary"></i> Profil PK
                            </a>
                        @endif
                    </li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item py-2 text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i> Keluar Sistem
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <!-- 2. Sidebar Navigation -->
    <aside id="simba-sidebar">
        <div>
            <div class="sidebar-section-label">Menu Utama</div>
            <nav class="sidebar-nav">
                @if(Auth::user()->isAdmin())
                    <!-- Admin Menu Items -->
                    <a href="{{ route('admin.dashboard') }}" class="sidebar-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid-fill"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('admin.surat-masuk.index') }}" class="sidebar-nav-item {{ request()->routeIs('admin.surat-masuk.*') ? 'active' : '' }}">
                        <i class="bi bi-inbox-fill"></i>
                        <span>Surat Masuk</span>
                    </a>
                    <a href="{{ route('admin.surat-keluar.index') }}" class="sidebar-nav-item {{ request()->routeIs('admin.surat-keluar.*') ? 'active' : '' }}">
                        <i class="bi bi-send-fill"></i>
                        <span>Surat Keluar</span>
                    </a>
                    <a href="{{ route('admin.profile.index') }}" class="sidebar-nav-item {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
                        <i class="bi bi-person-fill"></i>
                        <span>Profil</span>
                    </a>
                @else
                    <!-- PK Menu Items -->
                    <a href="{{ route('pk.dashboard') }}" class="sidebar-nav-item {{ request()->routeIs('pk.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid-fill"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('pk.jadwal.index') }}" class="sidebar-nav-item {{ request()->routeIs('pk.jadwal.*') ? 'active' : '' }}">
                        <i class="bi bi-calendar-event-fill"></i>
                        <span>Jadwal Bimbingan</span>
                    </a>
                    <a href="{{ route('pk.atur-jadwal.index') }}" class="sidebar-nav-item {{ request()->routeIs('pk.atur-jadwal.*') ? 'active' : '' }}">
                        <i class="bi bi-clock-history"></i>
                        <span>Atur Jadwal</span>
                    </a>
                    <a href="{{ route('pk.klien.index') }}" class="sidebar-nav-item {{ request()->routeIs('pk.klien.*') ? 'active' : '' }}">
                        <i class="bi bi-people-fill"></i>
                        <span>Data Klien</span>
                    </a>
                    <a href="{{ route('pk.kehadiran.index') }}" class="sidebar-nav-item {{ request()->routeIs('pk.kehadiran.*') ? 'active' : '' }}">
                        <i class="bi bi-camera-fill"></i>
                        <span>Bukti Kehadiran</span>
                    </a>
                    <a href="{{ route('pk.profile.index') }}" class="sidebar-nav-item {{ request()->routeIs('pk.profile.*') ? 'active' : '' }}">
                        <i class="bi bi-person-fill"></i>
                        <span>Profil</span>
                    </a>
                @endif
            </nav>
        </div>

        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="sidebar-logout-btn">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- 3. Main Content Container -->
    <main id="content-wrapper">
        <!-- Flash Alerts -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-2 mb-4" role="alert" style="border-radius: 12px; font-size: 0.86rem;">
                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-2 mb-4" role="alert" style="border-radius: 12px; font-size: 0.86rem;">
                <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px; font-size: 0.86rem;">
                <div class="d-flex align-items-center gap-2 fw-semibold mb-2">
                    <i class="bi bi-exclamation-octagon-fill text-danger"></i>
                    <span>Mohon periksa kembali formulir berikut:</span>
                </div>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Bootstrap 5.3 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Mobile Sidebar Toggle
        const sidebar = document.getElementById('simba-sidebar');
        const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');

        if (sidebarToggleBtn) {
            sidebarToggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('show');
            });
        }
    </script>
    @yield('scripts')
</body>
</html>
