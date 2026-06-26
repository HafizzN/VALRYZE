<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Smart HR Portal - Login">
    <title>Login — Smart HR Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { font-family: 'Plus Jakarta Sans', 'Inter', sans-serif; }
        body { margin: 0; padding: 0; min-height: 100vh; background: #0f1117; display: flex; }

        .auth-left {
            flex: 1;
            background: linear-gradient(135deg, #0d1117 0%, #092415 50%, #0d1117 100%);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            padding: 3rem;
        }

        .auth-left::before {
            content: '';
            position: absolute;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(34,197,94,0.12) 0%, transparent 70%);
            top: -100px; left: -100px;
            border-radius: 50%;
        }
        .auth-left::after {
            content: '';
            position: absolute;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(74,222,128,0.08) 0%, transparent 70%);
            bottom: -100px; right: -50px;
            border-radius: 50%;
        }

        .auth-right {
            width: 440px;
            background: #0d1117;
            border-left: 1px solid rgba(255,255,255,0.06);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem;
        }

        .login-box { width: 100%; }

        .login-logo {
            width: 56px; height: 56px;
            background: linear-gradient(135deg, #14532D, #22C55E);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem; font-weight: 700; color: white;
            box-shadow: 0 8px 30px rgba(34,197,94,0.3);
            margin-bottom: 1.5rem;
        }

        h1.login-title { font-size: 1.4rem; font-weight: 700; color: #e2e8f0; margin: 0 0 0.35rem; }
        .login-sub { font-size: 0.82rem; color: #64748b; margin-bottom: 2rem; }

        .form-group { margin-bottom: 1.25rem; }
        .form-label { display: block; font-size: 0.78rem; font-weight: 500; color: #94a3b8; margin-bottom: 0.4rem; }
        .form-control {
            width: 100%; padding: 0.7rem 0.9rem;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 10px; color: #e2e8f0;
            font-size: 0.88rem; transition: all 0.2s;
            box-sizing: border-box;
        }
        .form-control:focus { 
            outline: none; 
            border-color: #22C55E; 
            box-shadow: 0 0 0 3px rgba(34,197,94,0.15); 
            background: rgba(34,197,94,0.04); 
        }
        .form-control::placeholder { color: #475569; }

        .btn-login {
            width: 100%; padding: 0.8rem;
            background: linear-gradient(135deg, #14532D, #22C55E);
            color: white; border: none; border-radius: 10px;
            font-size: 0.9rem; font-weight: 600; cursor: pointer;
            transition: all 0.2s; margin-top: 0.5rem;
            box-shadow: 0 6px 20px rgba(34,197,94,0.3);
        }
        .btn-login:hover { box-shadow: 0 8px 28px rgba(34,197,94,0.45); transform: translateY(-1px); }

        .checkbox-group { display: flex; align-items: center; gap: 0.5rem; }
        .checkbox-group label { font-size: 0.8rem; color: #94a3b8; cursor: pointer; }
        .checkbox-group input[type="checkbox"] { accent-color: #22C55E; }

        .error-msg { color: #f87171; font-size: 0.75rem; margin-top: 0.3rem; }

        /* Decorative elements for left panel */
        .feature-item {
            display: flex; align-items: center; gap: 1rem;
            padding: 1rem; border-radius: 12px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.06);
            margin-bottom: 0.75rem;
            backdrop-filter: blur(10px);
        }
        .feature-icon {
            width: 44px; height: 44px; flex-shrink: 0;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
        }
        .feature-title { font-size: 0.85rem; font-weight: 600; color: #e2e8f0; }
        .feature-desc { font-size: 0.72rem; color: #64748b; margin-top: 0.15rem; }

        @media (max-width: 768px) {
            body { flex-direction: column; }
            .auth-left { display: none; }
            .auth-right { width: 100%; padding: 2rem 1.5rem; min-height: 100vh; }
        }

        /* Animated background dots */
        .bg-dots {
            position: absolute; inset: 0;
            background-image: radial-gradient(rgba(34,197,94,0.05) 1px, transparent 1px);
            background-size: 30px 30px;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <!-- Left: Branding Panel -->
    <div class="auth-left">
        <div class="bg-dots"></div>
        <div style="position: relative; z-index: 1; max-width: 420px; width: 100%;">
            <div style="margin-bottom: 2rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem;">
                    <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #14532D, #22C55E); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; font-weight: 700; color: white; box-shadow: 0 6px 20px rgba(34,197,94,0.3);">HR</div>
                    <div>
                        <div style="font-size: 1.1rem; font-weight: 700; color: #e2e8f0;">Smart HR Portal</div>
                        <div style="font-size: 0.72rem; color: #22C55E;">Attendance Management System</div>
                    </div>
                </div>
                <h2 style="font-size: 1.6rem; font-weight: 800; color: #e2e8f0; line-height: 1.2; margin: 0 0 0.75rem;">
                    Sistem HR Digital<br>
                    <span style="background: linear-gradient(135deg, #22C55E, #4ADE80); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Modern & Cerdas</span>
                </h2>
                <p style="font-size: 0.85rem; color: #64748b; line-height: 1.6; margin: 0;">
                    Platform terintegrasi untuk manajemen absensi GPS, perizinan, cuti, dan administrasi SDM secara digital.
                </p>
            </div>

            <div class="feature-item">
                <div class="feature-icon" style="background: rgba(34,197,94,0.15);">
                    <svg class="w-5 h-5" style="color: #4ade80;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                </div>
                <div>
                    <div class="feature-title">GPS Geofencing</div>
                    <div class="feature-desc">Absensi hanya dapat dilakukan dalam radius kantor</div>
                </div>
            </div>

            <div class="feature-item">
                <div class="feature-icon" style="background: rgba(16,185,129,0.15);">
                    <svg class="w-5 h-5" style="color: #34d399;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/></svg>
                </div>
                <div>
                    <div class="feature-title">Selfie Verification</div>
                    <div class="feature-desc">Foto selfie dengan watermark lokasi & waktu otomatis</div>
                </div>
            </div>

            <div class="feature-item">
                <div class="feature-icon" style="background: rgba(245,158,11,0.15);">
                    <svg class="w-5 h-5" style="color: #fbbf24;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <div>
                    <div class="feature-title">Laporan Real-time</div>
                    <div class="feature-desc">Ekspor laporan PDF & Excel kapan saja</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right: Login Form -->
    <div class="auth-right">
        <div class="login-box">
            <div class="login-logo">HR</div>
            <h1 class="login-title">Selamat Datang!</h1>
            <p class="login-sub">Masukkan kredensial Anda untuk melanjutkan</p>

            <!-- Session Status -->
            @if (session('status'))
            <div style="background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3); color: #34d399; padding: 0.75rem; border-radius: 8px; font-size: 0.8rem; margin-bottom: 1rem;">
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
                    <a href="{{ route('password.request') }}" style="font-size: 0.78rem; color: #22C55E; text-decoration: none;">Lupa password?</a>
                    @endif
                </div>

                <button type="submit" class="btn-login">Masuk ke Portal</button>

                <!-- Demo accounts hint -->
                <div style="margin-top: 1.5rem; padding: 0.875rem; background: rgba(34,197,94,0.04); border: 1px solid rgba(34,197,94,0.15); border-radius: 10px;">
                    <div style="font-size: 0.72rem; color: #64748b; margin-bottom: 0.5rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Demo Accounts</div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.25rem;">
                        <div style="font-size: 0.72rem; color: #94a3b8;">admin@smarthr.com</div>
                        <div style="font-size: 0.72rem; color: #64748b;">Super Admin</div>
                        <div style="font-size: 0.72rem; color: #94a3b8;">hrd@smarthr.com</div>
                        <div style="font-size: 0.72rem; color: #64748b;">HRD</div>
                        <div style="font-size: 0.72rem; color: #94a3b8;">manager@smarthr.com</div>
                        <div style="font-size: 0.72rem; color: #64748b;">Manager</div>
                        <div style="font-size: 0.72rem; color: #94a3b8;">karyawan@smarthr.com</div>
                        <div style="font-size: 0.72rem; color: #64748b;">Karyawan</div>
                    </div>
                    <div style="font-size: 0.68rem; color: #475569; margin-top: 0.5rem;">Password: <code style="color: #22C55E;">password</code></div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
