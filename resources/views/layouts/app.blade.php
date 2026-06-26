<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Smart HR Portal - Sistem Manajemen Absensi & SDM Digital">
    <title>@yield('title', 'Dashboard') — {{ config('app.name') }}</title>
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        * { font-family: 'Plus Jakarta Sans', 'Inter', sans-serif; }
        :root {
            --sidebar-width: 260px;
            --primary: #14532D;
            --primary-light: #22C55E;
            --accent: #4ADE80;
            --success: #16A34A;
            --warning: #f59e0b;
            --danger: #dc2626;
            --info: #0d9488;
            --sidebar-bg: #111827;
            --body-bg: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #1f2937;
            --text-muted: #64748b;
            --border-color: #e5e7eb;
            --table-header-bg: #f8fafc;
            --table-hover-bg: #f1f5f9;
            --input-bg: #ffffff;
            --input-border: #d1d5db;
            --topbar-bg: rgba(255, 255, 255, 0.9);
        }

        .dark {
            --body-bg: #0b0f19;
            --card-bg: #111827;
            --text-main: #f3f4f6;
            --text-muted: #9ca3af;
            --border-color: #1f2937;
            --table-header-bg: #1f2937;
            --table-hover-bg: #1f2937;
            --input-bg: #1f2937;
            --input-border: #374151;
            --topbar-bg: rgba(17, 24, 39, 0.9);
        }

        body, .sidebar, .topbar, .card, .stat-card, .btn, .form-control, table, tr, td, th {
            transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
        }

        body { background: var(--body-bg); color: var(--text-main); }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            border-right: 1px solid rgba(255,255,255,0.05);
            position: fixed; left: 0; top: 0; bottom: 0; z-index: 40;
            display: flex; flex-direction: column;
            transition: transform 0.3s ease;
        }
        .sidebar-brand {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            display: flex; align-items: center; gap: 0.75rem;
        }
        .brand-icon {
            width: 42px; height: 42px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; font-weight: 700; color: white;
            box-shadow: 0 4px 15px rgba(34,197,94,0.3);
        }
        .brand-text { font-size: 0.85rem; font-weight: 700; color: #f3f4f6; line-height: 1.2; }
        .brand-sub { font-size: 0.65rem; color: #9ca3af; font-weight: 400; }

        /* Nav items */
        .nav-section-label {
            font-size: 0.6rem; font-weight: 600; letter-spacing: 0.1em;
            color: #4b5563; text-transform: uppercase;
            padding: 1rem 1.25rem 0.4rem;
        }
        .nav-item {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.6rem 1.25rem; margin: 0.1rem 0.5rem;
            border-radius: 8px; cursor: pointer; transition: all 0.2s;
            color: #9ca3af; font-size: 0.83rem; font-weight: 500;
            text-decoration: none;
        }
        .nav-item:hover {
            background: rgba(34,197,94,0.08);
            color: #4ade80;
        }
        .nav-item.active {
            background: linear-gradient(135deg, rgba(20,83,45,0.4), rgba(34,197,94,0.25));
            color: #4ade80;
            border-left: 2px solid var(--primary-light);
        }
        .nav-icon {
            width: 18px; height: 18px; flex-shrink: 0; opacity: 0.9;
        }
        .nav-badge {
            margin-left: auto;
            background: var(--danger);
            color: white; font-size: 0.6rem; font-weight: 600;
            padding: 0.1rem 0.4rem; border-radius: 20px;
            min-width: 18px; text-align: center;
        }
        .nav-submenu { padding-left: 1.5rem; }
        .nav-submenu .nav-item {
            font-size: 0.78rem; padding: 0.5rem 1rem;
        }

        /* Main content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex; flex-direction: column;
        }

        /* Topbar */
        .topbar {
            background: var(--topbar-bg);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-color);
            padding: 0 1.5rem;
            height: 64px;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 30;
        }
        .topbar-title { font-size: 1rem; font-weight: 600; color: var(--text-main); }
        .topbar-breadcrumb { font-size: 0.75rem; color: var(--text-muted); }

        /* Cards */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem;
            transition: transform 0.2s, box-shadow 0.2s;
            color: var(--text-main);
        }
        .card:hover { transform: translateY(-1px); box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05), 0 8px 10px -6px rgba(0,0,0,0.05); }

        /* Stat cards */
        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.25rem;
            position: relative; overflow: hidden;
            color: var(--text-main);
        }
        .stat-card::before {
            content: ''; position: absolute;
            top: -20px; right: -20px;
            width: 80px; height: 80px;
            border-radius: 50%;
            background: var(--primary-light);
            opacity: 0.08;
        }
        .stat-value { font-size: 1.8rem; font-weight: 700; line-height: 1; color: var(--text-main); }
        .stat-label { font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem; }
        .stat-change { font-size: 0.7rem; margin-top: 0.5rem; }

        /* Badges */
        .badge { display: inline-flex; align-items: center; padding: 0.2rem 0.6rem; border-radius: 20px; font-size: 0.7rem; font-weight: 600; }
        .badge-success { background: #dcfce7; color: #15803d; }
        .badge-warning { background: #fef3c7; color: #b45309; }
        .badge-danger  { background: #fee2e2;  color: #b91c1c; }
        .badge-info    { background: #ccfbf1;  color: #0f766e; }
        .badge-purple  { background: #f3e8ff;  color: #6b21a8; }
        .badge-orange  { background: #ffedd5;  color: #c2410c; }
        .badge-gray    { background: #f1f5f9; color: #475569; }

        /* Buttons */
        .btn { display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.82rem; font-weight: 500; cursor: pointer; transition: all 0.2s; border: none; text-decoration: none; }
        .btn-primary { background: linear-gradient(135deg, var(--primary), var(--success)); color: white; box-shadow: 0 4px 12px rgba(22,163,74,0.2); }
        .btn-primary:hover { box-shadow: 0 6px 20px rgba(22,163,74,0.35); transform: translateY(-1px); }
        .btn-success { background: linear-gradient(135deg, #16a34a, #15803d); color: white; box-shadow: 0 4px 12px rgba(22,163,74,0.2); }
        .btn-danger  { background: linear-gradient(135deg, #dc2626, #b91c1c); color: white; }
        .btn-secondary { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
        .btn-secondary:hover { background: #e2e8f0; color: #1e293b; }
        .btn-sm { padding: 0.35rem 0.75rem; font-size: 0.75rem; }

        /* Tables */
        .table-container { overflow-x: auto; border-radius: 12px; border: 1px solid var(--border-color); }
        table { width: 100%; border-collapse: collapse; }
        thead th { background: var(--table-header-bg); color: var(--text-muted); font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; padding: 0.75rem 1rem; text-align: left; border-bottom: 1px solid var(--border-color); }
        tbody tr { border-bottom: 1px solid var(--border-color); transition: background 0.15s; background: var(--card-bg); }
        tbody tr:hover { background: var(--table-hover-bg); }
        tbody td { padding: 0.75rem 1rem; font-size: 0.82rem; color: var(--text-main); }

        /* Forms */
        .form-group { margin-bottom: 1.25rem; }
        .form-label { display: block; font-size: 0.8rem; font-weight: 500; color: var(--text-muted); margin-bottom: 0.4rem; }
        .form-control {
            width: 100%; padding: 0.6rem 0.9rem;
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: 8px; color: var(--text-main);
            font-size: 0.85rem; transition: all 0.2s;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--primary-light);
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(34,197,94,0.15);
        }
        .form-control::placeholder { color: #9ca3af; }
        select.form-control { cursor: pointer; }
        .form-error { font-size: 0.72rem; color: var(--danger); margin-top: 0.25rem; }

        /* Alerts */
        .alert { padding: 0.75rem 1rem; border-radius: 8px; font-size: 0.82rem; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
        .alert-success { background: #dcfce7; border: 1px solid #bbf7d0; color: #15803d; }
        .alert-error   { background: #fee2e2;  border: 1px solid #fca5a5;  color: #b91c1c; }
        .alert-info    { background: #e0f2fe;  border: 1px solid #bae6fd;  color: #0369a1; }
        .alert-warning { background: #fef3c7;  border: 1px solid #fde68a;  color: #b45309; }

        /* Avatar */
        .avatar {
            display: inline-flex; align-items: center; justify-content: center;
            width: 36px; height: 36px; border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white; font-size: 0.75rem; font-weight: 600;
        }

        /* GPS/Camera specific */
        #camera-preview { border-radius: 12px; border: 2px solid rgba(34,197,94,0.3); }
        .gps-status { display: flex; align-items: center; gap: 0.5rem; font-size: 0.8rem; }
        .gps-dot { width: 8px; height: 8px; border-radius: 50%; }
        .gps-dot.active { background: #16a34a; box-shadow: 0 0 8px #16a34a; animation: pulse 1.5s infinite; }
        .gps-dot.error  { background: var(--danger); }
        .gps-dot.loading { background: var(--warning); animation: pulse 1s infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }

        /* Page transitions */
        .page-content { animation: fadeIn 0.3s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }

        /* Mobile */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(34,197,94,0.3); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(34,197,94,0.5); }
    </style>
    @stack('styles')
</head>
<body class="h-full" x-data="{ sidebarOpen: false, activeMenu: '' }">

    <!-- Sidebar -->
    <aside class="sidebar" :class="{ 'open': sidebarOpen }">
        @php
            $company = \App\Models\Company::first();
        @endphp
        <!-- Brand -->
        <div class="sidebar-brand">
            @if($company && $company->logo)
                <img src="{{ Storage::url($company->logo) }}" alt="{{ $company->name }}" style="width: 36px; height: 36px; border-radius: 8px; object-fit: cover;">
            @else
                <div class="brand-icon">HR</div>
            @endif
            <div>
                <div class="brand-text">{{ $company ? $company->name : 'Smart HR Portal' }}</div>
                <div class="brand-sub">Attendance Management</div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto py-2" style="scrollbar-width: thin;">

            {{-- Dashboard --}}
            <div class="nav-section-label">Utama</div>
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>
            <a href="{{ route('calendar.index') }}" class="nav-item {{ request()->routeIs('calendar.*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Kalender Kerja
            </a>

            {{-- Absensi --}}
            <div class="nav-section-label">Absensi</div>
            <a href="{{ route('attendance.check-in') }}" class="nav-item {{ request()->routeIs('attendance.check-in*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                Absen Masuk
            </a>
            <a href="{{ route('attendance.check-out') }}" class="nav-item {{ request()->routeIs('attendance.check-out*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Absen Pulang
            </a>
            <a href="{{ route('attendance.history') }}" class="nav-item {{ request()->routeIs('attendance.history') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Riwayat Absensi
            </a>

            {{-- Perizinan --}}
            <div class="nav-section-label">Perizinan</div>
            <a href="{{ route('permission.index') }}" class="nav-item {{ request()->routeIs('permission.*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Izin
            </a>
            <a href="{{ route('leave.index') }}" class="nav-item {{ request()->routeIs('leave.*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Cuti
            </a>
            <a href="{{ route('overtime.index') }}" class="nav-item {{ request()->routeIs('overtime.*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Lembur
            </a>

            {{-- Surat & Dokumen --}}
            <div class="nav-section-label">Dokumen</div>
            <a href="{{ route('letters.index') }}" class="nav-item {{ request()->routeIs('letters.*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Surat Menyurat
            </a>
            <a href="{{ route('documents.index') }}" class="nav-item {{ request()->routeIs('documents.*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"/></svg>
                Dokumen Perusahaan
            </a>
            <a href="{{ route('announcements.index') }}" class="nav-item {{ request()->routeIs('announcements.*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                Pengumuman
            </a>

            {{-- Admin Only Sections --}}
            @hasrole(['super_admin', 'hrd'])
            <div class="nav-section-label">Manajemen</div>
            <a href="{{ route('employees.index') }}" class="nav-item {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Karyawan
            </a>

            <!-- Master Data with submenu -->
            <div x-data="{ open: {{ request()->routeIs('master.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="nav-item w-full text-left">
                    <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    Master Data
                    <svg class="w-3 h-3 ml-auto transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" x-transition class="nav-submenu">
                    <a href="{{ route('master.divisions.index') }}" class="nav-item {{ request()->routeIs('master.divisions.*') ? 'active' : '' }}">Divisi</a>
                    <a href="{{ route('master.positions.index') }}" class="nav-item {{ request()->routeIs('master.positions.*') ? 'active' : '' }}">Jabatan</a>
                    <a href="{{ route('master.shifts.index') }}" class="nav-item {{ request()->routeIs('master.shifts.*') ? 'active' : '' }}">Shift</a>
                    <a href="{{ route('master.locations.index') }}" class="nav-item {{ request()->routeIs('master.locations.*') ? 'active' : '' }}">Lokasi GPS</a>
                </div>
            </div>

            <!-- Reports -->
            <div x-data="{ open: {{ request()->routeIs('reports.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="nav-item w-full text-left">
                    <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Laporan
                    <svg class="w-3 h-3 ml-auto transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" x-transition class="nav-submenu">
                    <a href="{{ route('reports.attendance') }}" class="nav-item {{ request()->routeIs('reports.attendance') ? 'active' : '' }}">Kehadiran</a>
                    <a href="{{ route('reports.lateness') }}" class="nav-item {{ request()->routeIs('reports.lateness') ? 'active' : '' }}">Keterlambatan</a>
                    <a href="{{ route('reports.leave') }}" class="nav-item {{ request()->routeIs('reports.leave') ? 'active' : '' }}">Cuti</a>
                    <a href="{{ route('reports.gps') }}" class="nav-item {{ request()->routeIs('reports.gps') ? 'active' : '' }}">GPS Map</a>
                </div>
            </div>
            @endhasrole

            @hasrole('super_admin')
            <div class="nav-section-label">Pengaturan</div>
            <a href="{{ route('settings.company') }}" class="nav-item {{ request()->routeIs('settings.company') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Pengaturan Kantor
            </a>
            <a href="{{ route('settings.audit-logs') }}" class="nav-item {{ request()->routeIs('settings.audit-logs') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Jejak Audit (Logs)
            </a>
            @endhasrole

        </nav>

        <!-- User Profile at bottom -->
        <div style="padding: 1rem; border-top: 1px solid rgba(255,255,255,0.06);" x-data="{ open: false }">
            <button @click="open = !open" class="flex items-center gap-2 w-full" style="background: none; border: none; cursor: pointer; padding: 0.5rem; border-radius: 8px; transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.05)'" onmouseout="this.style.background='none'">
                <div class="avatar text-xs" style="overflow: hidden;">
                    @if(auth()->user()->photo)
                        <img src="{{ auth()->user()->photo_url }}" alt="{{ auth()->user()->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        {{ auth()->user()->initials }}
                    @endif
                </div>
                <div class="text-left flex-1 min-w-0">
                    <div style="font-size: 0.78rem; font-weight: 600; color: #e2e8f0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ auth()->user()->name }}</div>
                    <div style="font-size: 0.65rem; color: #64748b;">{{ auth()->user()->role_label }}</div>
                </div>
            </button>
            <div x-show="open" x-transition class="mt-1" style="background: rgba(0,0,0,0.3); border-radius: 8px; overflow: hidden;">
                <a href="{{ route('profile.edit') }}" class="nav-item" style="font-size: 0.78rem; padding: 0.5rem 0.75rem;">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Profil Saya
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="nav-item w-full text-left" style="font-size: 0.78rem; padding: 0.5rem 0.75rem; color: #f87171;">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar -->
        <header class="topbar">
            <div class="flex items-center gap-3">
                <!-- Mobile hamburger -->
                <button @click="sidebarOpen = !sidebarOpen" class="md:hidden p-1.5 rounded-lg" style="background: rgba(0,0,0,0.05);">
                    <svg class="w-5 h-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div>
                    <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
                    <div class="topbar-breadcrumb">@yield('breadcrumb', config('app.name'))</div>
                </div>
            </div>

            <!-- Right side: clock, notifications, user -->
            <div class="flex items-center gap-3">
                <!-- Live clock -->
                <div style="font-size: 0.78rem; color: #64748b; font-variant-numeric: tabular-nums;" id="live-clock"></div>

                <!-- Theme Toggle -->
                <button id="theme-toggle" class="p-2 rounded-lg" style="background: rgba(0,0,0,0.04); border: none; cursor: pointer; display: flex; align-items: center; justify-content: center;" title="Ubah Tema">
                    <!-- Moon icon (shows in light mode) -->
                    <svg id="theme-toggle-dark-icon" class="w-5 h-5 text-slate-500 hidden" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                    <!-- Sun icon (shows in dark mode) -->
                    <svg id="theme-toggle-light-icon" class="w-5 h-5 text-yellow-400 hidden" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.46 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
                </button>

                <!-- Notifications -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="relative p-2 rounded-lg" style="background: rgba(0,0,0,0.04);">
                        <svg class="w-5 h-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        @php $unread = auth()->user()->unreadNotificationsCount(); @endphp
                        @if($unread > 0)
                        <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full text-white text-xs flex items-center justify-content-center" style="font-size: 0.6rem; display: flex; align-items: center; justify-content: center;">{{ $unread }}</span>
                        @endif
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition style="position: absolute; right: 0; top: 120%; width: 320px; background: #ffffff; border: 1px solid var(--border-color); border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); z-index: 100; overflow: hidden;">
                        <div style="padding: 1rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 0.85rem; font-weight: 600; color: var(--text-main);">Notifikasi</span>
                            @if($unread > 0)
                            <button onclick="fetch('/notifications/read-all', {method:'POST', headers:{'X-CSRF-TOKEN': '{{ csrf_token() }}'}})" style="font-size: 0.7rem; color: var(--primary-light); background: none; border: none; cursor: pointer;">Tandai semua dibaca</button>
                            @endif
                        </div>
                        @forelse(auth()->user()->notifications()->latest()->limit(5)->get() as $notif)
                        <div style="padding: 0.75rem 1rem; border-bottom: 1px solid var(--border-color); {{ $notif->read_at ? '' : 'background: rgba(34,197,94,0.05);' }}">
                            <div style="font-size: 0.78rem; font-weight: 500; color: var(--text-main);">{{ $notif->title }}</div>
                            <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 0.2rem;">{{ $notif->message }}</div>
                            <div style="font-size: 0.65rem; color: var(--text-muted); margin-top: 0.3rem;">{{ $notif->created_at->diffForHumans() }}</div>
                        </div>
                        @empty
                        <div style="padding: 2rem; text-align: center; color: var(--text-muted); font-size: 0.8rem;">Tidak ada notifikasi</div>
                        @endforelse
                        <a href="{{ route('notifications.index') }}" style="display: block; padding: 0.75rem; text-align: center; font-size: 0.75rem; color: var(--primary-light); border-top: 1px solid var(--border-color); text-decoration: none;">Lihat semua</a>
                    </div>
                </div>

                <!-- User avatar -->
                <div class="avatar text-xs" style="overflow: hidden;">
                    @if(auth()->user()->photo)
                        <img src="{{ auth()->user()->photo_url }}" alt="{{ auth()->user()->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        {{ auth()->user()->initials }}
                    @endif
                </div>
            </div>
        </header>

        <!-- Page content -->
        <main class="flex-1 p-6 page-content">
            {{-- Flash Messages --}}
            @if(session('success'))
            <div class="alert alert-success">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="alert alert-error">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('error') }}
            </div>
            @endif
            @if(session('info'))
            <div class="alert alert-info">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('info') }}
            </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Overlay for mobile sidebar -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 30;" class="md:hidden"></div>

    <script>
        // Live clock
        function updateClock() {
            const now = new Date();
            document.getElementById('live-clock').textContent = now.toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit', second: '2-digit'}) + ' WIB';
        }
        updateClock();
        setInterval(updateClock, 1000);

        // Theme Toggle
        const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

        if (document.documentElement.classList.contains('dark')) {
            themeToggleLightIcon.classList.remove('hidden');
        } else {
            themeToggleDarkIcon.classList.remove('hidden');
        }

        const themeToggleBtn = document.getElementById('theme-toggle');
        if (themeToggleBtn) {
            themeToggleBtn.addEventListener('click', function() {
                themeToggleDarkIcon.classList.toggle('hidden');
                themeToggleLightIcon.classList.toggle('hidden');

                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                }
            });
        }
    </script>

    @stack('scripts')
</body>
</html>
