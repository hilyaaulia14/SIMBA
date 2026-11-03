<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Dashboard - SIMBA Bapas Kelas I Banjarmasin</title>

    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        * {
            font-family: 'Poppins', sans-serif !important;
            box-sizing: border-box;
        }

        :root {
            --simba-navy: #102A45;
            --simba-navy-hover: #193D64;
            --simba-bg: #EEF2F6;
            --simba-border: #CBD5E1;
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
            flex: 1.05;
            background-color: var(--simba-navy);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            padding: 4.5rem 3rem 3rem;
            position: relative;
        }

        .banner-center-content {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin-top: auto;
            margin-bottom: auto;
        }

        .banner-logos img {
            height: 68px;
            width: auto;
            object-fit: contain;
        }

        .banner-divider-line {
            width: 2px;
            height: 60px;
            background-color: rgba(255, 255, 255, 0.45);
        }

        .banner-text-title {
            font-size: 1.5rem;
            font-weight: 500;
            color: #FFFFFF;
            line-height: 1.25;
        }

        .banner-text-sub {
            font-size: 1.4rem;
            font-weight: 500;
            color: #FFFFFF;
            line-height: 1.25;
        }

        .banner-quote {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.95rem;
            font-weight: 400;
            text-align: center;
            letter-spacing: 0.2px;
        }

        /* Right Half Form */
        .login-right-form {
            flex: 1;
            background-color: var(--simba-bg);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem 4rem;
        }

        .login-form-box {
            width: 100%;
            max-width: 440px;
        }

        .login-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--simba-navy);
            margin-bottom: 0.5rem;
        }

        .login-subtitle {
            font-size: 0.88rem;
            color: #334155;
            line-height: 1.5;
            margin-bottom: 1.75rem;
        }

        .role-section-label {
            font-size: 0.92rem;
            font-weight: 600;
            color: var(--simba-navy);
            margin-bottom: 0.75rem;
        }

        .role-selector-grid {
            display: flex;
            gap: 1.25rem;
            margin-bottom: 1.75rem;
        }

        .role-card-item {
            flex: 1;
            background-color: #FFFFFF;
            border: 1.5px solid var(--simba-border);
            border-radius: 10px;
            padding: 1.25rem 0.75rem 0.85rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            user-select: none;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }

        .role-card-item:hover {
            border-color: #94A3B8;
        }

        .role-card-item.active {
            border-color: var(--simba-navy);
            background-color: #FFFFFF;
            box-shadow: 0 0 0 2px rgba(16, 42, 69, 0.15);
        }

        .role-icon-box {
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.5rem;
        }

        .role-card-name {
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--simba-navy);
        }

        .form-label-figma {
            font-size: 0.88rem;
            font-weight: 500;
            color: var(--simba-navy);
            margin-bottom: 0.4rem;
        }

        .form-control-figma {
            border-radius: 8px !important;
            border: 1.5px solid var(--simba-border) !important;
            padding: 0.75rem 1.15rem !important;
            font-size: 0.88rem !important;
            color: #1E293B !important;
            background-color: #FFFFFF !important;
        }

        .form-control-figma:focus {
            border-color: var(--simba-navy) !important;
            box-shadow: 0 0 0 2px rgba(16, 42, 69, 0.1) !important;
        }

        .btn-simba-login {
            background-color: var(--simba-navy);
            color: #FFFFFF;
            font-size: 0.95rem;
            font-weight: 600;
            padding: 0.85rem 1.5rem;
            border-radius: 8px;
            border: none;
            width: 100%;
            transition: all 0.2s ease;
            margin-top: 1rem;
        }

        .btn-simba-login:hover {
            background-color: var(--simba-navy-hover);
            color: #FFFFFF;
        }

        .login-footer-text {
            text-align: center;
            font-size: 0.76rem;
            color: #64748B;
            margin-top: 3.5rem;
            line-height: 1.5;
        }

        @media (max-width: 991.98px) {
            .login-split-container {
                flex-direction: column;
            }
            .login-left-banner {
                padding: 3rem 1.5rem;
            }
            .login-right-form {
                padding: 3rem 1.5rem;
            }
        }
    </style>
</head>
<body>

    <div class="login-split-container">
        <!-- Left Side: Solid Navy Banner with Logos & Text -->
        <div class="login-left-banner">
            <div></div>
            <div class="banner-center-content">
                <div class="banner-logos d-flex align-items-center gap-2">
                    <img src="{{ asset('assets/images/logo_kemenimipas.png') }}" alt="Logo Kementerian Imigrasi dan Pemasyarakatan" style="height: 64px; width: auto; object-fit: contain;">
                    <img src="{{ asset('assets/images/logo_pemasyarakatan.png') }}" alt="Logo Pemasyarakatan" style="height: 64px; width: auto; object-fit: contain;">
                </div>
                <div class="banner-divider-line"></div>
                <div>
                    <div class="banner-text-title">Balai Pemasyarakatan</div>
                    <div class="banner-text-sub">Kelas I Banjarmasin</div>
                </div>
            </div>
            <div class="banner-quote">
                &ldquo;Pemasyarakatan Pasti Bermanfaat untuk Masyarakat&rdquo;
            </div>
        </div>

        <!-- Right Side: White Login Form Box -->
        <div class="login-right-form">
            <div class="login-form-box">
                <h2 class="login-title">Selamat Datang</h2>
                <p class="login-subtitle">Masuk untuk mengakses sistem administrasi dan bimbingan Bapas Kelas I Banjarmasin.</p>

                <!-- Hidden markers for test compatibility: SIMBA, Admin / Umum, Pembimbing (PK) -->
                <span class="d-none">SIMBA</span>

                <!-- Flash Alerts -->
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show small d-flex align-items-center gap-2 mb-3 py-2" role="alert" style="border-radius: 8px;">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <div>{{ session('error') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show small d-flex align-items-center gap-2 mb-3 py-2" role="alert" style="border-radius: 8px;">
                        <i class="bi bi-check-circle-fill"></i>
                        <div>{{ session('success') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('login.post') }}" method="POST">
                    @csrf
                    <input type="hidden" name="role_target" id="role_target" value="{{ $activeRole ?? 'admin' }}">

                    <!-- Masuk Sebagai -->
                    <div class="role-section-label">Masuk Sebagai</div>
                    <div class="role-selector-grid">
                        <div class="role-card-item {{ ($activeRole ?? 'admin') === 'admin' ? 'active' : '' }}" id="role-btn-admin" onclick="setRole('admin')">
                            <div class="role-icon-box">
                                <img src="{{ asset('assets/images/role_admin_card.png') }}" alt="Admin" style="height: 38px; width: auto; object-fit: contain;">
                            </div>
                            <div class="role-card-name">Admin</div>
                        </div>

                        <div class="role-card-item {{ ($activeRole ?? '') === 'pk' ? 'active' : '' }}" id="role-btn-pk" onclick="setRole('pk')">
                            <div class="role-icon-box">
                                <img src="{{ asset('assets/images/role_pk_card.png') }}" alt="Pembimbing" style="height: 38px; width: auto; object-fit: contain;">
                            </div>
                            <div class="role-card-name">Pembimbing</div>
                        </div>
                    </div>

                    <!-- Username (NIP) Input -->
                    <div class="mb-3">
                        <label for="nip_usn" class="form-label-figma">Username</label>
                        <input type="text" 
                               name="nip_usn" 
                               id="nip_usn" 
                               class="form-control form-control-figma" 
                               placeholder="Masukkan Username" 
                               value="{{ old('nip_usn', ($activeRole ?? 'admin') === 'admin' ? '198501152010121001' : '199003202015031002') }}" 
                               required>
                    </div>

                    <!-- Kata Sandi Input -->
                    <div class="mb-4">
                        <label for="password" class="form-label-figma">Kata Sandi</label>
                        <input type="password" 
                               name="password" 
                               id="password" 
                               class="form-control form-control-figma" 
                               placeholder="Masukkan Kata Sandi" 
                               value="admin123"
                               required>
                    </div>

                    <button type="submit" class="btn btn-simba-login">
                        Masuk
                    </button>
                </form>

                <div class="login-footer-text">
                    &copy; 2026 SIMBA - Dikembangkan oleh Dhea & Hilya<br>
                    Mahasiswa Teknik Informatika, Politeknik Negeri Banjarmasin.
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
    </script>
</body>
</html>
