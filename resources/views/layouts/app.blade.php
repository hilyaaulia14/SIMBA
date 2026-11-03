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
            --simba-navy: #102A45;
            --simba-navy-hover: #193D64;
            --simba-bg: #EEF2F6;
            --simba-border: #E2E8F0;
            --simba-text: #102A45;
            --simba-muted: #64748B;
            --topbar-height: 72px;
            --sidebar-width: 270px;
        }

        * {
            font-family: 'Poppins', sans-serif !important;
            box-sizing: border-box;
        }

        body {
            background-color: var(--simba-bg);
            color: var(--simba-text);
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        /* -------------------------------------------------------------
           1. Top Navbar Header (#102A45 with Rounded Bottom Corners)
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
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom-left-radius: 24px;
            border-bottom-right-radius: 24px;
            box-shadow: 0 4px 20px rgba(16, 42, 69, 0.15);
        }

        .header-brand {
            display: flex;
            align-items: center;
            gap: 1rem;
            text-decoration: none;
            color: #FFFFFF;
        }

        .header-brand .logos-container img {
            height: 42px;
            width: auto;
            object-fit: contain;
        }

        .header-brand .brand-divider {
            width: 1.5px;
            height: 36px;
            background-color: rgba(255, 255, 255, 0.4);
            margin: 0 0.15rem;
        }

        .header-brand .brand-titles {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }

        .header-brand .brand-title-main {
            font-size: 1rem;
            font-weight: 500;
            color: #FFFFFF;
            letter-spacing: 0.2px;
        }

        .header-brand .brand-title-sub {
            font-size: 0.95rem;
            font-weight: 500;
            color: #FFFFFF;
            letter-spacing: 0.2px;
        }

        .header-user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .header-user-details {
            text-align: right;
            line-height: 1.25;
        }

        .header-user-name {
            font-size: 0.88rem;
            font-weight: 500;
            color: #FFFFFF;
        }

        .header-user-role {
            font-size: 0.78rem;
            color: #94A3B8;
        }

        .header-avatar-circle {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background-color: #FFFFFF;
            color: var(--simba-navy);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            text-decoration: none;
            cursor: pointer;
        }

        /* -------------------------------------------------------------
           2. Sidebar Navigation (Pill capsule buttons on #EEF2F6)
           ------------------------------------------------------------- */
        #simba-sidebar {
            width: var(--sidebar-width);
            position: fixed;
            top: var(--topbar-height);
            left: 0;
            bottom: 0;
            background-color: var(--simba-bg);
            padding: 2rem 1.25rem 1.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            z-index: 1040;
            overflow-y: auto;
            transition: all 0.3s ease;
        }

        .sidebar-section-label {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--simba-navy);
            margin-bottom: 1.15rem;
            padding-left: 0.5rem;
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .sidebar-nav-item {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding: 0.75rem 1.25rem;
            border-radius: 50px;
            text-decoration: none;
            font-size: 0.88rem;
            font-weight: 500;
            color: var(--simba-navy);
            background-color: #FFFFFF;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
            border: 1px solid transparent;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .sidebar-nav-item:hover {
            color: var(--simba-navy);
            background-color: #F8FAFC;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }

        .sidebar-nav-item.active {
            background-color: var(--simba-navy);
            color: #FFFFFF !important;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(16, 42, 69, 0.25);
        }

        .sidebar-nav-item i {
            font-size: 1.15rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar-nav-item.active i {
            color: #FFFFFF;
        }

        .sidebar-footer {
            padding-top: 1rem;
        }

        .sidebar-logout-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            width: 100%;
            padding: 0.75rem 1.25rem;
            border-radius: 50px;
            border: none;
            background-color: #FFFFFF;
            color: var(--simba-navy);
            font-size: 0.88rem;
            font-weight: 500;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .sidebar-logout-btn:hover {
            background-color: #FEF2F2;
            color: #DC2626;
        }

        /* -------------------------------------------------------------
           3. Main Content Container
           ------------------------------------------------------------- */
        #content-wrapper {
            margin-left: var(--sidebar-width);
            margin-top: var(--topbar-height);
            padding: 2.25rem 2.5rem;
            min-height: calc(100vh - var(--topbar-height));
            transition: margin-left 0.3s ease;
        }

        .page-header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .page-title-main {
            font-size: 1.85rem;
            font-weight: 700;
            color: var(--simba-navy);
            margin-bottom: 0.2rem;
        }

        .page-subtitle-main {
            font-size: 0.92rem;
            color: #334155;
            margin-bottom: 0;
        }

        .header-date-badge {
            background-color: #FFFFFF;
            border-radius: 50px;
            padding: 0.55rem 1.25rem;
            border: 1px solid var(--simba-border);
            font-size: 0.86rem;
            font-weight: 500;
            color: var(--simba-navy);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        }

        .btn-simba-primary {
            background-color: var(--simba-navy);
            color: #FFFFFF;
            font-size: 0.88rem;
            font-weight: 500;
            padding: 0.65rem 1.4rem;
            border-radius: 50px;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 2px 6px rgba(16, 42, 69, 0.2);
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-simba-primary:hover {
            background-color: var(--simba-navy-hover);
            color: #FFFFFF;
        }

        /* -------------------------------------------------------------
           4. Metric Cards (Figma 100% Match)
           ------------------------------------------------------------- */
        .metric-card-figma {
            background-color: #FFFFFF;
            border-radius: 16px;
            border: none;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
            padding: 2.25rem 1.5rem;
            text-align: center;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .metric-card-label {
            font-size: 1rem;
            font-weight: 600;
            color: var(--simba-navy);
            margin-bottom: 0.75rem;
        }

        .metric-card-number {
            font-size: 3.25rem;
            font-weight: 700;
            color: var(--simba-navy);
            line-height: 1;
        }

        /* -------------------------------------------------------------
           5. Table Card & Filter Bar (Figma Exact Style)
           ------------------------------------------------------------- */
        .table-card-figma {
            background-color: #FFFFFF;
            border-radius: 16px;
            border: none;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .table-card-header {
            padding: 1.5rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .table-card-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--simba-navy);
            margin-bottom: 0.15rem;
        }

        .table-card-subtitle {
            font-size: 0.82rem;
            color: #64748B;
            margin-bottom: 0;
        }

        .table-filter-bar {
            padding: 1.25rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .filter-pill-input {
            border-radius: 50px !important;
            background-color: #FFFFFF !important;
            border: 1px solid var(--simba-border) !important;
            padding: 0.55rem 1.25rem !important;
            font-size: 0.85rem !important;
            color: #334155 !important;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }

        .filter-pill-input:focus {
            border-color: var(--simba-navy) !important;
            box-shadow: 0 0 0 2px rgba(16, 42, 69, 0.1) !important;
        }

        .table-simba {
            margin-bottom: 0;
            width: 100%;
            border-collapse: collapse;
        }

        .table-simba thead th {
            background-color: var(--simba-navy) !important;
            color: #FFFFFF !important;
            font-size: 0.82rem;
            font-weight: 600;
            padding: 1.15rem 1.5rem;
            border: none;
            vertical-align: middle;
        }

        .table-simba tbody td {
            padding: 1.15rem 1.5rem;
            font-size: 0.88rem;
            color: #1E293B;
            border-top: 1px solid #F1F5F9;
            border-bottom: 1px solid #F1F5F9;
            vertical-align: middle;
            background-color: #FFFFFF;
        }

        .table-simba tbody tr:hover td {
            background-color: #F8FAFC;
        }

        /* Action Icon Buttons */
        .btn-action-square {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            border: 1px solid #E2E8F0;
            background: #FFFFFF;
            font-size: 0.95rem;
            color: var(--simba-navy);
            transition: all 0.15s ease;
            text-decoration: none;
        }

        .btn-action-square:hover {
            background-color: #F8FAFC;
            border-color: #CBD5E1;
            color: var(--simba-navy);
        }

        .btn-action-square.btn-delete {
            color: #EF4444;
        }
        .btn-action-square.btn-delete:hover {
            background-color: #FEE2E2;
            border-color: #FCA5A5;
            color: #DC2626;
        }

        /* Status Pills (Exact Figma) */
        .status-pill-hadir {
            background-color: #DCFCE7;
            color: #16A34A;
            padding: 0.35rem 0.85rem;
            border-radius: 50px;
            font-size: 0.78rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }

        .status-pill-terjadwal {
            background-color: #DBEAFE;
            color: #2563EB;
            padding: 0.35rem 0.85rem;
            border-radius: 50px;
            font-size: 0.78rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }

        .status-pill-tidak-hadir {
            background-color: #FEE2E2;
            color: #DC2626;
            padding: 0.35rem 0.85rem;
            border-radius: 50px;
            font-size: 0.78rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }

        .status-pill-dijadwalkan-ulang {
            background-color: #FFEDD5;
            color: #EA580C;
            padding: 0.35rem 0.85rem;
            border-radius: 50px;
            font-size: 0.78rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }

        /* -------------------------------------------------------------
           6. Modals (Clean Figma Style)
           ------------------------------------------------------------- */
        .modal-content-figma {
            border-radius: 16px;
            border: none;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            padding: 0.5rem;
        }

        .modal-header-figma {
            padding: 1.5rem 2rem 1rem;
            background-color: #FFFFFF;
            border-bottom: 1px solid #F1F5F9;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
        }

        .modal-title-figma {
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--simba-navy);
            margin-bottom: 0.2rem;
        }

        .modal-subtitle-figma {
            font-size: 0.84rem;
            color: #64748B;
            margin-bottom: 0;
        }

        .form-label-figma {
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--simba-navy);
            margin-bottom: 0.4rem;
        }

        .form-control-figma {
            border-radius: 8px !important;
            border: 1px solid #CBD5E1 !important;
            padding: 0.65rem 1rem !important;
            font-size: 0.88rem !important;
            color: #1E293B !important;
        }

        .form-control-figma:focus {
            border-color: var(--simba-navy) !important;
            box-shadow: 0 0 0 2px rgba(16, 42, 69, 0.1) !important;
        }

        .upload-dropzone-figma {
            border: 1.5px solid #CBD5E1;
            background-color: #FFFFFF;
            border-radius: 8px;
            padding: 2.25rem 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .upload-dropzone-figma:hover {
            border-color: var(--simba-navy);
            background-color: #F8FAFC;
        }

        .modal-footer-figma {
            padding: 1.25rem 2rem;
            background-color: #FFFFFF;
            border-top: 1px solid #F1F5F9;
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
        }

        .btn-figma-cancel {
            background-color: #FFFFFF;
            border: 1px solid #CBD5E1;
            color: var(--simba-navy);
            border-radius: 8px;
            padding: 0.6rem 1.85rem;
            font-size: 0.88rem;
            font-weight: 500;
        }

        .btn-figma-save {
            background-color: var(--simba-navy);
            border: none;
            color: #FFFFFF;
            border-radius: 8px;
            padding: 0.6rem 1.85rem;
            font-size: 0.88rem;
            font-weight: 500;
        }
        .btn-figma-save:hover {
            background-color: var(--simba-navy-hover);
            color: #FFFFFF;
        }

        /* Delete Confirmation Dialog */
        .delete-dialog-badge {
            width: 76px;
            height: 76px;
            border-radius: 50%;
            background-color: #FEE2E2;
            color: #EF4444;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2.25rem;
            margin-bottom: 1.25rem;
        }

        /* -------------------------------------------------------------
           7. Pagination Styling (Bootstrap 5 Clean Pill/Button Match)
           ------------------------------------------------------------- */
        .pagination {
            margin-bottom: 0;
            gap: 4px;
            display: inline-flex;
            align-items: center;
        }
        .page-item .page-link {
            color: var(--simba-navy);
            border-radius: 8px !important;
            border: 1px solid #CBD5E1;
            font-size: 0.82rem;
            font-weight: 500;
            padding: 0.38rem 0.75rem;
            background-color: #FFFFFF;
            transition: all 0.2s ease;
        }
        .page-item .page-link:hover {
            background-color: #F1F5F9;
            color: var(--simba-navy);
            border-color: #94A3B8;
        }
        .page-item.active .page-link {
            background-color: var(--simba-navy) !important;
            border-color: var(--simba-navy) !important;
            color: #FFFFFF !important;
            box-shadow: 0 2px 6px rgba(16, 42, 69, 0.25);
        }
        .page-item.disabled .page-link {
            color: #94A3B8 !important;
            background-color: #F8FAFC !important;
            border-color: #E2E8F0 !important;
            cursor: not-allowed;
        }
        /* Strict constraint on any SVGs inside pagination to prevent giant icons */
        nav svg,
        .pagination svg {
            max-width: 14px !important;
            max-height: 14px !important;
            width: 14px !important;
            height: 14px !important;
            vertical-align: middle;
            display: inline-block;
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
                    <img src="{{ asset('assets/images/figma_login_logos.png') }}" alt="Logo Resmi" class="brand-logo">
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
                <div class="header-user-name">
                    {{ Auth::user()->isAdmin() ? 'Admin Bapas' : 'Pembimbing Kemasyarakatan' }}
                </div>
                <div class="header-user-role">
                    {{ Auth::user()->email ?? (Auth::user()->isAdmin() ? 'Bapasbjm@gmail.com' : 'pembimbingkemasyarakatan@gmail.com') }}
                </div>
            </div>
            <div class="dropdown">
                <button class="btn p-0 border-0" data-bs-toggle="dropdown" aria-expanded="false">
                    @if(Auth::user()->foto && file_exists(public_path('storage/' . Auth::user()->foto)))
                        <img src="{{ asset('storage/' . Auth::user()->foto) }}" alt="Avatar" class="header-avatar-circle" style="object-fit: cover;">
                    @else
                        <div class="header-avatar-circle">
                            {{ Auth::user()->isAdmin() ? 'AB' : 'PK' }}
                        </div>
                    @endif
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
                                <i class="bi bi-person me-2 text-primary"></i> Profil Saya
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
                        <i class="bi bi-envelope-fill"></i>
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
                        <i class="bi bi-calendar-event"></i>
                        <span>Jadwal Bimbingan</span>
                    </a>
                    <a href="{{ route('pk.kehadiran.index') }}" class="sidebar-nav-item {{ request()->routeIs('pk.kehadiran.*') ? 'active' : '' }}">
                        <i class="bi bi-person-check"></i>
                        <span>Bukti Kehadiran</span>
                    </a>
                    <a href="{{ route('pk.atur-jadwal.index') }}" class="sidebar-nav-item {{ request()->routeIs('pk.atur-jadwal.*') ? 'active' : '' }}">
                        <i class="bi bi-clock"></i>
                        <span>Buat Jadwal</span>
                    </a>
                    <a href="{{ route('pk.klien.index') }}" class="sidebar-nav-item {{ request()->routeIs('pk.klien.*') ? 'active' : '' }}">
                        <i class="bi bi-person-vcard"></i>
                        <span>Data Klien</span>
                    </a>
                    <a href="{{ route('pk.profile.index') }}" class="sidebar-nav-item {{ request()->routeIs('pk.profile.*') ? 'active' : '' }}">
                        <i class="bi bi-person"></i>
                        <span>Profil</span>
                    </a>
                @endif
            </nav>
        </div>

        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="sidebar-logout-btn">
                    <i class="bi bi-box-arrow-left"></i>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- 3. Main Content Container -->
    <main id="content-wrapper">
        <!-- Flash Alerts -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-2 mb-4" role="alert" style="border-radius: 12px; font-size: 0.86rem; background-color: #DCFCE7; color: #16A34A;">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-2 mb-4" role="alert" style="border-radius: 12px; font-size: 0.86rem; background-color: #FEE2E2; color: #DC2626;">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <div>{{ session('error') }}</div>
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
