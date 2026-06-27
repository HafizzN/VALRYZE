<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="VALRYZE Smart HR Portal - Login">
    <title>Login — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; box-sizing: border-box; margin: 0; padding: 0; }
        body { min-height: 100vh; background: #0D1B2E; display: flex; }

        /* ── Left: Branding ─── */
        .auth-left {
            flex: 1;
            background: linear-gradient(160deg, #0D1B2E 0%, #18263D 50%, #1E3250 100%);
            position: relative;
            display: flex; align-items: center; justify-content: center;
            overflow: hidden; padding: 3rem;
        }
        .auth-left::before {
            content: '';
            position: absolute;
            width: 480px; height: 480px;
            background: radial-gradient(circle, rgba(16,185,129,0.1) 0%, transparent 70%);
            top: -80px; left: -80px; border-radius: 50%;
        }
        .auth-left::after {
            content: '';
            position: absolute;
            width: 360px; height: 360px;
            background: radial-gradient(circle, rgba(16,185,129,0.06) 0%, transparent 70%);
            bottom: -80px; right: -40px; border-radius: 50%;
        }
        .bg-grid {
            position: absolute; inset: 0;
            background-image: radial-gradient(rgba(16,185,129,0.04) 1px, transparent 1px);
            background-size: 32px 32px;
        }

        /* ── Right: Form ─── */
        .auth-right {
            width: 440px;
            background: #0D1B2E;
            border-left: 1px solid rgba(16,185,129,0.08);
            display: flex; align-items: center; justify-content: center;
            padding: 2.5rem;
        }
        .login-box { width: 100%; }

        .login-logo {
            width: 52px; height: 52px;
            background: rgba(16,185,129,0.12);
            border: 1px solid rgba(16,185,129,0.2);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.9rem; font-weight: 700; color: #10B981;
            margin-bottom: 1.5rem;
            transition: background 0.2s, border-color 0.2s;
        }
        .login-logo:hover { background: rgba(16,185,129,0.18); border-color: rgba(16,185,129,0.35); }

        h1.login-title { font-size: 1.4rem; font-weight: 700; color: #F1F5F9; margin: 0 0 0.35rem; letter-spacing: -0.02em; }
        .login-sub { font-size: 0.82rem; color: #64748B; margin-bottom: 2rem; }

        .form-group { margin-bottom: 1.25rem; }
        .form-label { display: block; font-size: 0.78rem; font-weight: 600; color: #94A3B8; margin-bottom: 0.4rem; }
        .form-control {
            width: 100%; padding: 0.7rem 0.9rem;
            background: rgba(255,255,255,0.04);
            border: 1.5px solid rgba(255,255,255,0.08);
            border-radius: 10px; color: #E2E8F0;
            font-size: 0.88rem; font-family: inherit;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }
        .form-control:focus {
            outline: none;
            border-color: #10B981;
            box-shadow: 0 0 0 3px rgba(16,185,129,0.12);
            background: rgba(16,185,129,0.04);
        }
        .form-control::placeholder { color: #334155; }

        .btn-login {
            width: 100%; padding: 0.8rem;
            background: #10B981;
            color: white; border: none; border-radius: 10px;
            font-size: 0.9rem; font-weight: 700; cursor: pointer; font-family: inherit;
            transition: background 0.2s, box-shadow 0.2s, transform 0.15s;
            margin-top: 0.5rem;
            box-shadow: 0 4px 16px rgba(16,185,129,0.25);
        }
        .btn-login:hover { background: #059669; box-shadow: 0 6px 24px rgba(16,185,129,0.4); transform: translateY(-1px); }
        .btn-login:active { transform: scale(0.98); }

        .checkbox-group { display: flex; align-items: center; gap: 0.5rem; }
        .checkbox-group label { font-size: 0.8rem; color: #94A3B8; cursor: pointer; }
        .checkbox-group input[type="checkbox"] { accent-color: #10B981; }
        .forgot-link { font-size: 0.78rem; color: #10B981; text-decoration: none; transition: color 0.2s; }
        .forgot-link:hover { color: #34D399; }
        .error-msg { color: #F87171; font-size: 0.75rem; margin-top: 0.3rem; }

        .feature-item {
            display: flex; align-items: center; gap: 1rem;
            padding: 1rem; border-radius: 14px;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.05);
            margin-bottom: 0.75rem;
            backdrop-filter: blur(8px);
            transition: background 0.2s, border-color 0.2s, transform 0.2s;
        }
        .feature-item:hover { background: rgba(16,185,129,0.06); border-color: rgba(16,185,129,0.12); transform: translateX(4px); }
        .feature-icon { width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; display: flex; align-items: center; justify-content: center; }
        .feature-title { font-size: 0.85rem; font-weight: 600; color: #E2E8F0; }
        .feature-desc { font-size: 0.72rem; color: #64748B; margin-top: 0.15rem; }

        .demo-box { margin-top: 1.5rem; padding: 0.875rem; background: rgba(16,185,129,0.04); border: 1px solid rgba(16,185,129,0.1); border-radius: 12px; }
        .demo-label { font-size: 0.68rem; color: #475569; margin-bottom: 0.5rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; }
        .demo-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.25rem; }
        .demo-email { font-size: 0.72rem; color: #94A3B8; }
        .demo-role  { font-size: 0.72rem; color: #64748B; }
        .demo-pass  { font-size: 0.68rem; color: #475569; margin-top: 0.5rem; }
        .demo-pass code { color: #10B981; font-family: monospace; }

        @media (max-width: 768px) {
            body { flex-direction: column; }
            .auth-left { display: none; }
            .auth-right { width: 100%; padding: 2rem 1.5rem; min-height: 100vh; }
        }
    </style>
</head>
<body>
    <!-- Left: Branding Panel -->
    <div class="auth-left">
        <div class="bg-grid"></div>
        <div style="position: relative; z-index: 1; max-width: 420px; width: 100%;">
            <div style="margin-bottom: 2rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.75rem;">
                    <div style="width: 46px; height: 46px; background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; font-weight: 700; color: #10B981;">VAL</div>
                    <div>
                        <div style="font-size: 1.1rem; font-weight: 700; color: #F1F5F9; letter-spacing: -0.01em;">VAL<span style="color: #10B981; font-weight: 300;">RYZE</span></div>
                        <div style="font-size: 0.7rem; color: #64748B;">Smart HR Portal</div>
                    </div>
                </div>
                <h2 style="font-size: 1.75rem; font-weight: 800; color: #F1F5F9; line-height: 1.2; margin: 0 0 0.75rem; letter-spacing: -0.03em;">
                    Sistem HR Digital<br>
                    <span style="color: #10B981; font-weight: 300;">Modern &amp; Cerdas</span>
                </h2>
                <p style="font-size: 0.85rem; color: #64748B; line-height: 1.7; margin: 0;">
                    Platform terintegrasi untuk manajemen absensi GPS, perizinan, cuti, dan administrasi SDM secara digital.
                </p>
            </div>

            <div class="feature-item">
                <div class="feature-icon" style="background: rgba(16,185,129,0.12);">
                    <svg style="width:20px;height:20px;color:#10B981;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                </div>
                <div><div class="feature-title">GPS Geofencing</div><div class="feature-desc">Absensi hanya dapat dilakukan dalam radius kantor</div></div>
            </div>
            <div class="feature-item">
                <div class="feature-icon" style="background: rgba(124,58,237,0.12);">
                    <svg style="width:20px;height:20px;color:#7C3AED;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/></svg>
                </div>
                <div><div class="feature-title">Selfie Verification</div><div class="feature-desc">Foto selfie dengan watermark lokasi &amp; waktu otomatis</div></div>
            </div>
            <div class="feature-item">
                <div class="feature-icon" style="background: rgba(245,158,11,0.12);">
                    <svg style="width:20px;height:20px;color:#F59E0B;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <div><div class="feature-title">Laporan Real-time</div><div class="feature-desc">Ekspor laporan PDF &amp; Excel kapan saja</div></div>
            </div>
        </div>
    </div>

    <!-- Right: Login Form -->
    <div class="auth-right">
        <div class="login-box">
            <div class="login-logo">VAL</div>
            <h1 class="login-title">Selamat Datang!</h1>
            <p class="login-sub">Masukkan kredensial Anda untuk melanjutkan</p>

            @if (session('status'))
            <div style="background: rgba(16,185,129,0.08); border: 1px solid rgba(16,185,129,0.2); color: #34D399; padding: 0.75rem; border-radius: 10px; font-size: 0.8rem; margin-bottom: 1rem;">
                {{ session('status') }}
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                        class="form-control" placeholder="nama@perusahaan.com" required autofocus>
                    @error('email')
                    <p class="error-msg">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input id="password" type="password" name="password"
                        class="form-control" placeholder="••••••••" required>
                    @error('password')
                    <p class="error-msg">{{ $message }}</p>
                    @enderror
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <div class="checkbox-group">
                        <input type="checkbox" id="remember_me" name="remember">
                        <label for="remember_me">Ingat saya</label>
                    </div>
                    @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot-link">Lupa password?</a>
                    @endif
                </div>

                <button type="submit" class="btn-login">Masuk ke Portal</button>

                <div class="demo-box">
                    <div class="demo-label">Demo Accounts</div>
                    <div class="demo-grid">
                        <div class="demo-email">admin@smarthr.com</div><div class="demo-role">Super Admin</div>
                        <div class="demo-email">hrd@smarthr.com</div><div class="demo-role">HRD</div>
                        <div class="demo-email">manager@smarthr.com</div><div class="demo-role">Manager</div>
                        <div class="demo-email">karyawan@smarthr.com</div><div class="demo-role">Karyawan</div>
                    </div>
                    <div class="demo-pass">Password: <code>password</code></div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
