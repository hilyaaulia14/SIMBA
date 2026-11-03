<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - SIMBA (Sistem Informasi Balai Pemasyarakatan) Bapas Kelas I Banjarmasin</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --bapas-navy: #0D2A4A;
            --bapas-navy-dark: #07192C;
            --bapas-gold: #D4AF37;
            --bapas-gold-dark: #B8860B;
            --bapas-accent: #0284C7;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #07192C 0%, #0D2A4A 60%, #163B65 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background-image: radial-gradient(rgba(212, 175, 55, 0.08) 1px, transparent 1px);
            background-size: 24px 24px;
            pointer-events: none;
        }

        .login-card {
            background: #FFFFFF;
            border-radius: 18px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.45);
            overflow: hidden;
            max-width: 460px;
            width: 100%;
            z-index: 10;
        }

        .login-header {
            background: linear-gradient(180deg, #F8FAFC 0%, #EDF2F7 100%);
            padding: 2rem 2rem 1.5rem;
            text-align: center;
            border-bottom: 1px solid #E2E8F0;
        }

        .emblem-wrapper {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 68px;
            height: 68px;
            border-radius: 16px;
            background: linear-gradient(135deg, #0D2A4A 0%, #163B65 100%);
            box-shadow: 0 8px 18px rgba(13, 42, 74, 0.35);
            margin-bottom: 1rem;
            border: 2px solid var(--bapas-gold);
            color: #FFFFFF;
        }

        .role-nav .nav-link {
            border: none;
            color: #64748B;
            font-size: 0.86rem;
            font-weight: 600;
            padding: 0.75rem 1rem;
            border-radius: 10px;
            transition: all 0.2s ease;
            text-align: center;
        }

        .role-nav .nav-link:hover {
            color: var(--bapas-navy);
            background-color: #F1F5F9;
        }

        .role-nav .nav-link.active {
            color: #FFFFFF;
            background: linear-gradient(135deg, var(--bapas-navy) 0%, #163B65 100%);
            box-shadow: 0 4px 12px rgba(13, 42, 74, 0.25);
        }

        .form-control {
            border-radius: 10px;
            padding: 0.75rem 1rem;
            border: 1px solid #CBD5E1;
            font-size: 0.92rem;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .form-control:focus {
            border-color: var(--bapas-accent);
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
        }

        .input-group-text {
            border-radius: 10px;
            border: 1px solid #CBD5E1;
            background-color: #F8FAFC;
            color: #64748B;
        }

        .btn-submit {
            background: linear-gradient(135deg, #0D2A4A 0%, #163B65 100%);
            color: #FFFFFF;
            font-weight: 600;
            padding: 0.8rem 1.25rem;
            border-radius: 10px;
            border: none;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(13, 42, 74, 0.25);
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #163B65 0%, #0D2A4A 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(13, 42, 74, 0.35);
            color: #FFFFFF;
        }

        .credential-pill {
            cursor: pointer;
            border: 1px dashed #CBD5E1;
            border-radius: 8px;
            padding: 0.4rem 0.65rem;
            background-color: #F8FAFC;
            font-size: 0.75rem;
            transition: all 0.2s ease;
        }

        .credential-pill:hover {
            background-color: #EFF6FF;
            border-color: #3B82F6;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <!-- Login Header with Institutional Identity -->
        <div class="login-header">
            <div class="emblem-wrapper">
                <i class="bi bi-shield-lock-fill fs-2" style="color: var(--bapas-gold);"></i>
            </div>
            <h4 class="fw-bold text-dark mb-1" style="letter-spacing: -0.3px;">SIMBA</h4>
            <div class="fw-semibold text-primary" style="font-size: 0.85rem; letter-spacing: 0.3px;">
                Sistem Informasi Balai Pemasyarakatan
            </div>
            <div class="text-muted small mt-1">
                Bapas Kelas I Banjarmasin &bull; Poliban
            </div>
        </div>

        <div class="p-4 p-sm-4">
            <!-- Flash Alerts -->
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show small d-flex align-items-center gap-2 mb-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show small d-flex align-items-center gap-2 mb-3" role="alert">
                    <i class="bi bi-check-circle-fill"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show small mb-3" role="alert">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Role Selector Tabs -->
            <label class="form-label small fw-bold text-secondary mb-2">Pilih Peran Pengguna (Role):</label>
            <ul class="nav nav-pills nav-fill role-nav p-1 bg-light rounded-3 mb-4" id="roleTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ ($activeRole ?? 'admin') === 'admin' ? 'active' : '' }}" 
                            id="tab-admin" 
                            type="button" 
                            onclick="selectRole('admin')">
                        <i class="bi bi-folder-symlink me-1"></i> Admin / Umum
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ ($activeRole ?? '') === 'pk' ? 'active' : '' }}" 
                            id="tab-pk" 
                            type="button" 
                            onclick="selectRole('pk')">
                        <i class="bi bi-person-check me-1"></i> Pembimbing (PK)
                    </button>
                </li>
            </ul>

            <!-- Login Form -->
            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <input type="hidden" name="role_target" id="role_target" value="{{ $activeRole ?? 'admin' }}">

                <div class="mb-3">
                    <label for="nip_usn" class="form-label small fw-semibold text-dark">
                        Nomor Induk Pegawai (NIP / Username) <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-vcard"></i></span>
                        <input type="text" 
                               name="nip_usn" 
                               id="nip_usn" 
                               class="form-control @error('nip_usn') is-invalid @enderror" 
                               placeholder="Masukkan NIP Anda" 
                               value="{{ old('nip_usn', ($activeRole ?? 'admin') === 'admin' ? '198501152010121001' : '199003202015031002') }}" 
                               required 
                               autofocus>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label for="password" class="form-label small fw-semibold text-dark mb-0">
                            Kata Sandi <span class="text-danger">*</span>
                        </label>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-key"></i></span>
                        <input type="password" 
                               name="password" 
                               id="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               placeholder="Masukkan kata sandi" 
                               value="{{ ($activeRole ?? 'admin') === 'admin' ? 'admin123' : 'pk123' }}"
                               required>
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="bi bi-eye" id="passwordIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" checked>
                    <label class="form-check-label small text-muted" for="remember">
                        Ingat saya di perangkat ini
                    </label>
                </div>

                <button type="submit" class="btn btn-submit w-100 d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-box-arrow-in-right fs-5"></i>
                    <span>Masuk ke Dashboard</span>
                </button>
            </form>

            <!-- Quick Auto-Fill Demo Credentials -->
            <div class="mt-4 pt-3 border-top">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">
                        Kredensial Pengujian (Demo):
                    </span>
                </div>
                <div class="d-flex flex-column gap-2">
                    <div class="credential-pill d-flex align-items-center justify-content-between" onclick="fillCredentials('admin', '198501152010121001', 'admin123')">
                        <div>
                            <span class="badge bg-primary me-1">Admin</span>
                            <span class="fw-semibold text-dark">Budi Santoso (NIP: 198501152010121001)</span>
                        </div>
                        <i class="bi bi-arrow-right-short text-primary"></i>
                    </div>
                    <div class="credential-pill d-flex align-items-center justify-content-between" onclick="fillCredentials('pk', '199003202015031002', 'pk123')">
                        <div>
                            <span class="badge bg-success me-1">PK</span>
                            <span class="fw-semibold text-dark">Hilyatul Aulia (NIP: 199003202015031002)</span>
                        </div>
                        <i class="bi bi-arrow-right-short text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5.3 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Role tab selection
        function selectRole(role) {
            document.getElementById('role_target').value = role;
            if (role === 'admin') {
                document.getElementById('tab-admin').classList.add('active');
                document.getElementById('tab-pk').classList.remove('active');
                document.getElementById('nip_usn').value = '198501152010121001';
                document.getElementById('password').value = 'admin123';
            } else {
                document.getElementById('tab-pk').classList.add('active');
                document.getElementById('tab-admin').classList.remove('active');
                document.getElementById('nip_usn').value = '199003202015031002';
                document.getElementById('password').value = 'pk123';
            }
        }

        // Quick fill credentials
        function fillCredentials(role, nip, pass) {
            selectRole(role);
            document.getElementById('nip_usn').value = nip;
            document.getElementById('password').value = pass;
        }

        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const passwordIcon = document.getElementById('passwordIcon');

        togglePassword.addEventListener('click', function () {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
            passwordIcon.classList.toggle('bi-eye');
            passwordIcon.classList.toggle('bi-eye-slash');
        });
    </script>
</body>
</html>
