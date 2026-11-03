<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Dashboard - SIMBA Bapas Kelas I Banjarmasin</title>

    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        * {
            font-family: 'Poppins', sans-serif !important;
        }

        :root {
            --simba-navy: #0D2A4A;
            --simba-navy-light: #163E6B;
            --simba-bg: #F4F7FB;
            --simba-border: #E2E8F0;
        }

        body {
            margin: 0;
            padding: 0;
            background-color: var(--simba-bg);
            min-height: 100vh;
        }

        .login-split-container {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        /* Left Half Banner */
        .login-left-banner {
            flex: 1;
            background-color: var(--simba-navy);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem;
            position: relative;
        }

        .banner-identity {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            color: #FFFFFF;
        }

        .banner-logos {
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }

        .banner-logo-img {
            height: 52px;
            width: auto;
            object-fit: contain;
        }

        .banner-divider-line {
            width: 2px;
            height: 48px;
            background-color: rgba(255, 255, 255, 0.4);
        }

        .banner-text-title {
            font-size: 1.25rem;
            font-weight: 600;
            line-height: 1.25;
            letter-spacing: 0.3px;
        }

        .banner-text-sub {
            font-size: 0.95rem;
            font-weight: 400;
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.2;
        }

        /* Right Half Form */
        .login-right-form {
            flex: 1;
            background-color: var(--simba-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem;
        }

        .login-form-card {
            background-color: #FFFFFF;
            border-radius: 18px;
            border: 1px solid #EAEFF5;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            padding: 2.5rem 2.25rem;
            max-width: 440px;
            width: 100%;
        }

        .login-title {
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--simba-navy);
            letter-spacing: 0.5px;
            margin-bottom: 0.25rem;
            text-transform: uppercase;
        }

        .login-subtitle {
            font-size: 0.8rem;
            color: #64748B;
            margin-bottom: 1.5rem;
            line-height: 1.45;
        }

        .role-section-label {
            font-size: 0.72rem;
            font-weight: 600;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .role-selector-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .role-card-btn {
            border-radius: 12px;
            padding: 0.9rem 0.75rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            background-color: #FFFFFF;
            border: 1.5px solid var(--simba-border);
            color: #64748B;
            user-select: none;
        }

        .role-card-btn:hover {
            border-color: #CBD5E1;
            background-color: #F8FAFC;
        }

        .role-card-btn.active {
            border-color: var(--simba-navy);
            background-color: #EFF6FF;
            color: var(--simba-navy);
            box-shadow: 0 2px 8px rgba(13, 42, 74, 0.1);
        }

        .role-card-btn i {
            font-size: 1.45rem;
            margin-bottom: 0.35rem;
            display: block;
        }

        .role-card-btn.active i {
            color: var(--simba-navy);
        }

        .role-card-title {
            font-size: 0.82rem;
            font-weight: 600;
            line-height: 1.2;
        }

        .form-label-figma {
            font-size: 0.74rem;
            font-weight: 600;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.35rem;
        }

        .form-control-figma {
            border-radius: 8px;
            border: 1px solid var(--simba-border);
            padding: 0.75rem 1rem;
            font-size: 0.86rem;
            color: #1E293B;
            background-color: #FFFFFF;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .form-control-figma:focus {
            border-color: var(--simba-navy);
            box-shadow: 0 0 0 3px rgba(13, 42, 74, 0.12);
            outline: none;
        }

        .btn-simba-login {
            background-color: var(--simba-navy);
            color: #FFFFFF;
            font-size: 0.88rem;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            border: none;
            width: 100%;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 12px rgba(13, 42, 74, 0.25);
            transition: all 0.2s ease;
            text-transform: uppercase;
        }

        .btn-simba-login:hover {
            background-color: var(--simba-navy-light);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(13, 42, 74, 0.35);
            color: #FFFFFF;
        }

        .login-footer-copy {
            text-align: center;
            font-size: 0.74rem;
            color: #94A3B8;
            margin-top: 1.5rem;
            margin-bottom: 0;
        }

        .demo-pill-btn {
            cursor: pointer;
            border: 1px dashed #CBD5E1;
            border-radius: 6px;
            padding: 0.35rem 0.65rem;
            background-color: #F8FAFC;
            font-size: 0.72rem;
            color: #475569;
            transition: all 0.2s ease;
        }

        .demo-pill-btn:hover {
            background-color: #EFF6FF;
            border-color: #3B82F6;
            color: var(--simba-navy);
        }

        /* Responsive Breakpoint */
        @media (max-width: 991.98px) {
            .login-split-container {
                flex-direction: column;
            }
            .login-left-banner {
                padding: 2.25rem 1.5rem;
                flex: none;
            }
            .banner-logo-img {
                height: 42px;
            }
            .banner-text-title {
                font-size: 1.05rem;
            }
            .banner-text-sub {
                font-size: 0.85rem;
            }
            .login-right-form {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>

    <div class="login-split-container">
        <!-- Left Side: Solid Navy Banner with Logos & Text -->
        <div class="login-left-banner">
            <div class="banner-identity">
                <div class="banner-logos">
                    <img src="{{ asset('assets/images/logo_poliban.svg') }}" alt="Logo Poliban" class="banner-logo-img">
                    <img src="{{ asset('assets/images/logo_kemenkumham.svg') }}" alt="Logo Kemenkumham" class="banner-logo-img">
                </div>
                <div class="banner-divider-line"></div>
                <div>
                    <div class="banner-text-title">Balai Pemasyarakatan</div>
                    <div class="banner-text-sub">Kelas I Banjarmasin</div>
                </div>
            </div>
        </div>

        <!-- Right Side: White Login Form Card -->
        <div class="login-right-form">
            <div class="login-form-card">
                <h4 class="login-title">LOGIN DASHBOARD</h4>
                <p class="login-subtitle">Silakan pilih role dan masukkan akun terdaftar Anda untuk mengakses sistem SIMBA.</p>

                <!-- Flash Alerts -->
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show small d-flex align-items-center gap-2 mb-3 py-2" role="alert">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <div>{{ session('error') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show small d-flex align-items-center gap-2 mb-3 py-2" role="alert">
                        <i class="bi bi-check-circle-fill"></i>
                        <div>{{ session('success') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show small mb-3 py-2" role="alert">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Form -->
                <form action="{{ route('login.post') }}" method="POST">
                    @csrf
                    <input type="hidden" name="role_target" id="role_target" value="{{ $activeRole ?? 'admin' }}">

                    <!-- Role Selector -->
                    <div class="role-section-label">PILIH ROLE LOGIN</div>
                    <div class="role-selector-grid">
                        <div class="role-card-btn {{ ($activeRole ?? 'admin') === 'admin' ? 'active' : '' }}" id="role-btn-admin" onclick="setRole('admin')">
                            <i class="bi bi-shield-lock-fill"></i>
                            <div class="role-card-title">Admin / Umum</div>
                        </div>
                        <div class="role-card-btn {{ ($activeRole ?? '') === 'pk' ? 'active' : '' }}" id="role-btn-pk" onclick="setRole('pk')">
                            <i class="bi bi-person-badge-fill"></i>
                            <div class="role-card-title">Pembimbing (PK)</div>
                        </div>
                    </div>

                    <!-- NIP Input -->
                    <div class="mb-3">
                        <label for="nip_usn" class="form-label-figma">NIP (USN) <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="nip_usn" 
                               id="nip_usn" 
                               class="form-control form-control-figma" 
                               placeholder="Masukkan NIP Anda" 
                               value="{{ old('nip_usn', ($activeRole ?? 'admin') === 'admin' ? '198501152010121001' : '199003202015031002') }}" 
                               required 
                               autofocus>
                    </div>

                    <!-- Password Input -->
                    <div class="mb-4">
                        <label for="password" class="form-label-figma">PASSWORD <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" 
                                   name="password" 
                                   id="password" 
                                   class="form-control form-control-figma" 
                                   placeholder="Masukkan kata sandi" 
                                   value="{{ ($activeRole ?? 'admin') === 'admin' ? 'admin123' : 'pk123' }}"
                                   required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePasswordBtn" style="border-radius: 0 8px 8px 0; border: 1px solid var(--simba-border);">
                                <i class="bi bi-eye" id="passwordEyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" checked>
                        <label class="form-check-label small text-muted" for="remember">
                            Ingat saya di perangkat ini
                        </label>
                    </div>

                    <button type="submit" class="btn btn-simba-login">
                        MASUK
                    </button>
                </form>

                <p class="login-footer-copy">
                    &copy; SIMBA - Bapas Kelas I Banjarmasin
                </p>

                <!-- Demo Pills -->
                <div class="mt-3 pt-3 border-top d-flex gap-2 justify-content-center">
                    <span class="demo-pill-btn" onclick="fillCredential('admin', '198501152010121001', 'admin123')">
                        <i class="bi bi-key-fill text-primary me-1"></i> Demo Admin
                    </span>
                    <span class="demo-pill-btn" onclick="fillCredential('pk', '199003202015031002', 'pk123')">
                        <i class="bi bi-key-fill text-success me-1"></i> Demo PK
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5.3 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function setRole(role) {
            document.getElementById('role_target').value = role;
            if (role === 'admin') {
                document.getElementById('role-btn-admin').classList.add('active');
                document.getElementById('role-btn-pk').classList.remove('active');
                document.getElementById('nip_usn').value = '198501152010121001';
                document.getElementById('password').value = 'admin123';
            } else {
                document.getElementById('role-btn-pk').classList.add('active');
                document.getElementById('role-btn-admin').classList.remove('active');
                document.getElementById('nip_usn').value = '199003202015031002';
                document.getElementById('password').value = 'pk123';
            }
        }

        function fillCredential(role, nip, pass) {
            setRole(role);
            document.getElementById('nip_usn').value = nip;
            document.getElementById('password').value = pass;
        }

        // Toggle password visibility
        const togglePasswordBtn = document.getElementById('togglePasswordBtn');
        const passwordInput = document.getElementById('password');
        const passwordEyeIcon = document.getElementById('passwordEyeIcon');

        togglePasswordBtn.addEventListener('click', function () {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
            passwordEyeIcon.classList.toggle('bi-eye');
            passwordEyeIcon.classList.toggle('bi-eye-slash');
        });
    </script>
</body>
</html>
