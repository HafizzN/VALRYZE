<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Smart HR Portal - Sistem Manajemen Absensi & SDM Digital">
    <title>@yield('title', 'Dashboard') — {{ config('app.name') }}</title>
    <script>
        // Default: light mode. Only apply dark if explicitly set.
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
            if (!localStorage.getItem('theme')) localStorage.setItem('theme', 'light');
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
        * { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* VALRYZE TOKENS — Figma Design System Theme */
        :root {
            /* Light Theme (Content Area) */
            --bg-base:     #EFF6FF;
            --bg-sidebar:  #071830;
            --bg-topbar:   #071830; /* Navbar always dark deep navy like in Figma theme */
            --bg-card:     #FFFFFF;
            --bg-elevated: #F8FAFC;
            --bg-hover:    #EFF6FF;

            /* Brand cyan green khas VALRYZE */
            --em:          #06B6D4;
            --em-light:    #38BDF8;
            --em-dark:     #0284C7;
            --em-ghost:    rgba(6,182,212,0.1);
            --em-border:   rgba(6,182,212,0.25);
            --em-glow:     rgba(6,182,212,0.15);

            /* Text hierarchy */
            --t1: #0F172A;
            --t2: #334155;
            --t3: #64748B;
            --t4: #94A3B8;
            --t5: #CBD5E1;

            /* Semantic */
            --success: #10B981;
            --warning: #F59E0B;
            --danger:  #EF4444;
            --info:    #0EA5E9;

            /* Borders */
            --border-dim:  rgba(255,255,255,0.08);
            --border-soft: #DBEAFE;
            --border-em:   rgba(6,182,212,0.25);

            /* Typography scale */
            --text-hero: 2.25rem;
            --text-xl:   1.75rem;
            --text-lg:   1.375rem;
            --text-md:   1.1rem;
            --text-base: 0.875rem;
            --text-sm:   0.8rem;
            --text-xs:   0.72rem;
            --text-2xs:  0.65rem;

            /* Shadows */
            --shadow-card:     0 4px 20px rgba(15,23,42,0.05);
            --shadow-glow:     0 8px 32px rgba(6,182,212,0.1);
            --shadow-elevated: 0 16px 48px rgba(0,0,0,0.5);

            /* Backward compat */
            --body-bg:         var(--bg-base);
            --card-bg:         var(--bg-card);
            --text-main:       var(--t1);
            --text-muted:      var(--t3);
            --border-color:    var(--border-soft);
            --primary:         #071830;
            --primary-light:   var(--em);
            --sidebar-bg:      var(--bg-sidebar);
            --topbar-bg:       var(--bg-topbar);
            --table-header-bg: var(--bg-elevated);
            --table-hover-bg:  var(--bg-hover);
            --input-bg:        var(--bg-elevated);
            --input-border:    var(--border-soft);
            --hero-label:      #93C5FD;
            --hero-sub:        #E2E8F0;
        }

        .dark {
            /* Dark Theme (Content Area) */
            --bg-base:     #071524;
            --bg-sidebar:  #030C16;
            --bg-topbar:   #030C16;
            --bg-card:     #0D1F38;
            --bg-elevated: #112543;
            --bg-hover:    #162E52;

            /* Brand cyan green khas VALRYZE */
            --em:          #06B6D4;
            --em-light:    #38BDF8;
            --em-dark:     #0EA5E9;
            --em-ghost:    rgba(6,182,212,0.14);
            --em-border:   rgba(6,182,212,0.25);
            --em-glow:     rgba(6,182,212,0.2);

            /* Text hierarchy */
            --t1: #E2E8F0;
            --t2: #CBD5E1;
            --t3: #94A3B8;
            --t4: #64748B;
            --t5: #334155;

            /* Semantic */
            --success: #22C55E;
            --warning: #F59E0B;
            --danger:  #EF4444;
            --info:    #06B6D4;

            /* Borders */
            --border-dim:  rgba(6,182,212,0.08);
            --border-soft: rgba(6,182,212,0.14);
            --border-em:   rgba(6,182,212,0.25);

            --shadow-card:     0 4px 24px rgba(0,0,0,0.3);
            --shadow-glow:     0 8px 32px rgba(6,182,212,0.15);
            --shadow-elevated: 0 16px 48px rgba(0,0,0,0.6);
            --hero-label:      #93C5FD;
            --hero-sub:        #E2E8F0;
        }

        html, body { height: 100%; }
        body { background: var(--bg-base); color: var(--t1); }
        body, .topbar, .card, .stat-card, .btn, .form-control, table, tr, td, th {
            transition: background-color 0.25s ease, border-color 0.25s ease, color 0.2s ease;
        }

        /* MAIN + TOPBAR */
        .main-content { min-height: 100vh; display: flex; flex-direction: column; }
        .topbar {
            background: var(--bg-topbar);
            border-bottom: 1px solid var(--border-dim);
            padding: 0 1.5rem; height: 56px;
            display: flex; align-items: center; justify-content: space-between;
            position: fixed; top: 0; left: 0; right: 0; z-index: 50;
            backdrop-filter: blur(16px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }

        /* App Layout Wrapper */
        .app-layout {
            display: flex;
            min-height: calc(100vh - 56px);
            margin-top: 56px;
            width: 100%;
        }

        /* Sidebar styles */
        .sidebar {
            width: 192px;
            background: #071830;
            border-right: 1px solid var(--border-soft);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            flex-shrink: 0;
            z-index: 40;
            height: calc(100vh - 56px);
            position: sticky;
            top: 56px;
            overflow-y: auto;
            transition: all 0.3s ease;
        }
        .sidebar-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.55rem 0.75rem;
            border-radius: 10px;
            text-decoration: none;
            color: var(--t3);
            font-size: 0.8rem;
            font-weight: 500;
            transition: all 0.2s;
            cursor: pointer;
            border: none;
            background: transparent;
            margin-bottom: 0.15rem;
        }
        .sidebar-item:hover {
            color: var(--t1);
            background: var(--bg-hover);
        }
        .sidebar-item.active {
            color: #06B6D4;
            background: rgba(6, 182, 212, 0.13);
            font-weight: 700;
            border-left: 2px solid #06B6D4;
            border-radius: 0 10px 10px 0;
        }
        .sidebar-subitem {
            padding: 0.4rem 0.75rem;
            border-radius: 8px;
            font-size: 0.75rem;
            color: var(--t3);
            text-decoration: none;
            transition: all 0.15s;
            display: flex;
            align-items: center;
        }
        .sidebar-subitem:hover {
            color: var(--em-light);
            background: var(--bg-hover);
        }
        .sidebar-subitem.active {
            color: var(--em);
            background: var(--em-ghost);
            font-weight: 600;
        }

        /* Top Navbar links and dropdowns (Backward compatibility) */
        .topnav-dropdown {
            position: absolute; top: 120%; left: 0; min-width: 200px;
            background: var(--bg-elevated); border: 1px solid var(--border-soft);
            border-radius: 12px; box-shadow: var(--shadow-elevated);
            padding: 0.5rem; display: flex; flex-direction: column; gap: 0.15rem;
            z-index: 100;
        }
        .topnav-dropdown-item {
            padding: 0.45rem 0.85rem; border-radius: 8px; font-size: 0.78rem;
            color: var(--t3); text-decoration: none; transition: all 0.15s;
            display: flex; align-items: center; gap: 0.5rem;
        }
        .topnav-dropdown-item:hover { color: var(--em-light); background: var(--bg-hover); }
        .topnav-dropdown-item.active { color: var(--em); background: var(--em-ghost); font-weight: 600; }

        /* HERO */
        .hero-section {
            position: relative; overflow: hidden;
            background: linear-gradient(135deg, #0F2745 0%, #143A63 55%, #195A88 100%);
            border: 1px solid var(--border-soft);
            border-radius: 20px; padding: 2rem 2.25rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow-card);
        }
        .hero-section::before {
            content: ''; position: absolute;
            top:-80px; right:-80px; width: 320px; height: 320px; border-radius: 50%;
            background: radial-gradient(circle, rgba(16,185,129,0.14) 0%, transparent 70%);
        }
        .hero-section::after {
            content: ''; position: absolute;
            bottom:-50px; left: 25%; width: 240px; height: 240px; border-radius: 50%;
            background: radial-gradient(circle, rgba(16,185,129,0.06) 0%, transparent 70%);
        }
        .hero-line { width: 36px; height: 3px; background: linear-gradient(90deg, var(--em), transparent); border-radius: 2px; margin-bottom: 0.7rem; }
        .hero-greeting { font-size: 1.8rem; font-weight: 800; color: var(--t1); letter-spacing: -0.03em; line-height: 1.15; position: relative; z-index:1; }
        .hero-greeting span { color: var(--em); }
        .hero-sub { font-size: 0.8rem; color: var(--t4); margin-top: 0.4rem; position: relative; z-index:1; }
        .hero-kpi { display: flex; gap: 2.5rem; margin-top: 1.5rem; position: relative; z-index: 1; flex-wrap: wrap; }
        .hero-kpi-val   { font-size: 1.9rem; font-weight: 800; color: var(--t1); letter-spacing: -0.04em; line-height: 1; }
        .hero-kpi-val.em { color: var(--em); }
        .hero-kpi-label { font-size: 0.7rem; color: var(--t4); margin-top: 0.2rem; font-weight: 500; }
        .hero-bar       { height: 3px; background: var(--border-soft); border-radius: 4px; overflow: hidden; width: 70px; margin-top: 0.4rem; }
        .hero-bar-fill  { height: 100%; background: linear-gradient(90deg, var(--em), var(--em-light)); border-radius: 4px; }
        .hero-status    { position: absolute; right: 2rem; top: 50%; transform: translateY(-50%); display: flex; align-items: center; gap: 0.5rem; z-index: 1; }
        .hero-status-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--em); box-shadow: 0 0 12px var(--em); animation: pulse 2s infinite; }
        .hero-status-text { font-size: 0.75rem; color: var(--t3); }
 
        /* CARDS */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-soft);
            border-radius: 18px; padding: 1.5rem;
            color: var(--t1);
            box-shadow: var(--shadow-card);
            position: relative; overflow: hidden;
            transition: transform 0.22s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.22s ease, border-color 0.22s ease, background-color 0.22s ease;
        }
        .card::before {
            content: ''; position: absolute; top:0; left:0; right:0; height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.07), transparent);
        }
        .card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-card), 0 12px 28px rgba(0,0,0,0.45), var(--shadow-glow);
            border-color: var(--em-border);
        }
 
        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border-soft);
            border-radius: 18px; padding: 1.3rem 1.2rem 1.1rem;
            position: relative; overflow: hidden; color: var(--t1);
            box-shadow: var(--shadow-card);
            transition: transform 0.22s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.22s ease, border-color 0.22s ease, background-color 0.22s ease;
        }
        .stat-card::before {
            content: ''; position: absolute; top:0; left:0; right:0; height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.06), transparent);
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-card), 0 12px 28px rgba(0,0,0,0.45), var(--shadow-glow);
            border-color: var(--em-border);
        }
        .stat-icon {
            width: 50px; height: 50px; border-radius: 15px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 1rem; position: relative; overflow: hidden;
        }
        .stat-icon::before {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.12) 0%, transparent 60%);
        }
        .stat-icon svg { width: 24px; height: 24px; position: relative; z-index: 1; }
        .stat-value { font-size: 2rem; font-weight: 800; line-height: 1; letter-spacing: -0.04em; color: var(--t1); }
        .stat-label { font-size: var(--text-xs); color: var(--t4); margin-top: 0.3rem; font-weight: 500; }
        .stat-change { font-size: var(--text-xs); margin-top: 0.65rem; }

        /* BADGES */
        .badge { display: inline-flex; align-items: center; gap: 0.22rem; padding: 0.22rem 0.6rem; border-radius: 20px; font-size: 0.68rem; font-weight: 700; letter-spacing: 0.01em; }
        .badge-success { background: rgba(16,185,129,0.12);  color: #34D399; border: 1px solid rgba(16,185,129,0.2); }
        .badge-warning { background: rgba(245,158,11,0.12);  color: #FCD34D; border: 1px solid rgba(245,158,11,0.2); }
        .badge-danger  { background: rgba(239,68,68,0.12);   color: #FCA5A5; border: 1px solid rgba(239,68,68,0.2); }
        .badge-info    { background: rgba(124,58,237,0.12);  color: #C4B5FD; border: 1px solid rgba(124,58,237,0.2); }
        .badge-purple  { background: rgba(124,58,237,0.12);  color: #C4B5FD; border: 1px solid rgba(124,58,237,0.2); }
        .badge-orange  { background: rgba(249,115,22,0.12);  color: #FDBA74; border: 1px solid rgba(249,115,22,0.2); }
        .badge-gray    { background: rgba(148,163,184,0.09); color: #94A3B8; border: 1px solid rgba(148,163,184,0.15); }

        /* BUTTONS */
        .btn {
            display: inline-flex; align-items: center; gap: 0.4rem;
            padding: 0.5rem 1.1rem; border-radius: 10px;
            font-size: 0.8rem; font-weight: 700; cursor: pointer;
            transition: transform 0.13s ease, box-shadow 0.2s ease, background 0.18s ease;
            border: none; text-decoration: none; white-space: nowrap;
        }
        .btn:active { transform: scale(0.97) !important; }
        .btn-primary { background: linear-gradient(135deg, var(--em), var(--em-dark)); color: #fff; box-shadow: 0 4px 18px rgba(16,185,129,0.3); }
        .btn-primary:hover { box-shadow: 0 6px 28px rgba(16,185,129,0.45); transform: translateY(-1px); }
        .btn-secondary { background: var(--bg-elevated); color: var(--t2); border: 1px solid var(--border-soft); }
        .btn-secondary:hover { background: var(--bg-hover); color: var(--t1); border-color: var(--em-border); transform: translateY(-1px); }
        .btn-ghost { background: transparent; color: var(--em); border: 1px solid var(--em-border); }
        .btn-ghost:hover { background: var(--em-ghost); box-shadow: 0 0 20px var(--em-glow); transform: translateY(-1px); }
        .btn-success { background: linear-gradient(135deg, var(--em), #059669); color: #fff; box-shadow: 0 4px 18px rgba(16,185,129,0.3); }
        .btn-success:hover { box-shadow: 0 6px 28px rgba(16,185,129,0.45); transform: translateY(-1px); }
        .btn-danger { background: rgba(239,68,68,0.15); color: #FCA5A5; border: 1px solid rgba(239,68,68,0.25); }
        .btn-danger:hover { background: rgba(239,68,68,0.25); color: #FEE2E2; transform: translateY(-1px); }
        .btn-sm { padding: 0.3rem 0.75rem; font-size: 0.72rem; border-radius: 8px; }
        .btn-xs { padding: 0.2rem 0.55rem; font-size: 0.65rem; border-radius: 6px; }

        /* TABLES */
        .table-container { overflow-x: auto; border-radius: 16px; border: 1px solid var(--border-soft); background: var(--bg-card); box-shadow: var(--shadow-card); }
        table { width: 100%; border-collapse: collapse; }
        thead th { background: var(--bg-elevated); color: var(--t4); font-size: 0.67rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; padding: 0.85rem 1rem; text-align: left; border-bottom: 1px solid var(--em-border); }
        tbody tr { border-bottom: 1px solid var(--border-dim); transition: background 0.15s ease; background: var(--bg-card); }
        tbody tr:hover { background: var(--bg-hover); }
        tbody td { padding: 0.8rem 1rem; font-size: 0.82rem; color: var(--t2); }
        .avatar { display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 50%; background: var(--em-ghost); border: 1.5px solid var(--em-border); color: var(--em); font-size: 0.72rem; font-weight: 800; transition: border-color 0.2s, box-shadow 0.2s; flex-shrink: 0; }
        .avatar:hover { border-color: var(--em); box-shadow: 0 0 14px var(--em-glow); }

        /* FORMS */
        .form-group  { margin-bottom: 1.25rem; }
        .form-label  { display: block; font-size: 0.74rem; font-weight: 700; color: var(--t3); margin-bottom: 0.4rem; letter-spacing: 0.02em; }
        .form-control { width: 100%; padding: 0.65rem 0.9rem; background: var(--bg-elevated); border: 1.5px solid var(--border-soft); border-radius: 10px; color: var(--t1); font-size: 0.85rem; font-family: inherit; transition: border-color 0.2s, box-shadow 0.2s, background 0.2s; }
        .form-control:focus { outline: none; border-color: var(--em); background: var(--bg-hover); box-shadow: 0 0 0 3px var(--em-ghost); }
        .form-control::placeholder { color: var(--t5); }
        select.form-control { cursor: pointer; }
        .form-error { font-size: 0.7rem; color: #FCA5A5; margin-top: 0.3rem; }

        /* ALERTS */
        .alert { 
            padding: 0.85rem 1.15rem; 
            border-radius: 14px; 
            font-size: 0.82rem; 
            margin-bottom: 1.25rem; 
            display: flex; 
            align-items: center; 
            gap: 0.75rem; 
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border: 1.5px solid transparent;
        }
        .alert-success { 
            background: #E6FDF4;  
            border-color: #A7F3D0; 
            color: #047857; 
        }
        .dark .alert-success {
            background: rgba(16,185,129,0.09);  
            border-color: rgba(16,185,129,0.3); 
            color: #34D399; 
        }
        .alert-error { 
            background: #FFF4F4;   
            border-color: #FCA5A5;  
            color: #DC2626; 
        }
        .dark .alert-error {
            background: rgba(239,68,68,0.09);   
            border-color: rgba(239,68,68,0.3);  
            color: #FCA5A5; 
        }
        .alert-warning { 
            background: #FFFBEB;  
            border-color: #FCD34D; 
            color: #D97706; 
        }
        .dark .alert-warning {
            background: rgba(245,158,11,0.09);  
            border-color: rgba(245,158,11,0.3); 
            color: #FCD34D; 
        }
        .alert-info { 
            background: #F5F3FF;  
            border-color: #C4B5FD; 
            color: #6D28D9; 
        }
        .dark .alert-info {
            background: rgba(124,58,237,0.09);  
            border-color: rgba(124,58,237,0.3); 
            color: #C4B5FD; 
        }

        /* GPS */
        #camera-preview { border-radius: 16px; border: 2px solid var(--em-border); }
        .gps-status { display: flex; align-items: center; gap: 0.5rem; font-size: 0.8rem; }
        .gps-dot { width: 8px; height: 8px; border-radius: 50%; }
        .gps-dot.active  { background: var(--em); box-shadow: 0 0 10px var(--em); animation: pulse 1.5s infinite; }
        .gps-dot.error   { background: var(--danger); }
        .gps-dot.loading { background: var(--warning); animation: pulse 1s infinite; }

        @keyframes pulse        { 0%,100%{opacity:1} 50%{opacity:0.35} }
        @keyframes fadeSlideIn  { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
        .page-content { animation: fadeSlideIn 0.3s ease; }

        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(6,182,212,0.2); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(6,182,212,0.4); }

        /* Mobile adjustments */
        @media (max-width: 1024px) {
            .sidebar { display: none; }
            .app-layout { margin-top: 56px; min-height: calc(100vh - 56px); }
        }
    </style>
    @stack('styles')
</head>
<body class="h-full" x-data="{ mobileMenuOpen: false }">

    <!-- Topbar (Full Width) -->
    <header class="topbar">
        <!-- Left: Logo -->
        <div class="flex items-center gap-6">
            <div class="flex items-center gap-2">
                <div class="brand-icon" style="width: 28px; height: 28px; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg,#06B6D4,#0284C7); flex-shrink: 0; border: none; box-shadow: 0 0 10px rgba(6,182,212,0.3);">
                    <svg style="width: 13px; height: 13px; color: #FFFFFF; fill: #FFFFFF;" viewBox="0 0 24 24" fill="currentColor"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <span style="font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 700; font-size: 13px; color: #FFFFFF; letter-spacing: 0.15em;">
                    VAL<span style="color: #06B6D4;">RYZE</span>
                </span>
            </div>
        </div>

        <!-- Middle: Search Bar (Max 448px) -->
        <div style="flex: 1; max-width: 448px; position: relative;" class="hidden md:block" x-data="globalSearchApp()">
            <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2" style="color: var(--t4);" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input
                type="text"
                x-model="query"
                @input.debounce.300ms="performSearch"
                @keydown.escape="clearSearch"
                @blur="clearSearch"
                placeholder="Cari karyawan, divisi, cuti, pengumuman..."
                style="width: 100%; padding: 0.45rem 1rem 0.45rem 2.25rem; background: rgba(255,255,255,0.06); border: 1px solid rgba(6,182,212,0.15); border-radius: 10px; font-size: 0.8rem; color: #FFFFFF; outline: none; transition: border-color 0.2s;"
                onfocus="this.style.borderColor='#06B6D4'"
                onblur="this.style.borderColor='rgba(6,182,212,0.15)'"
            >

            <!-- Search Dropdown Results -->
            <div x-show="results.length > 0" 
                 style="position: absolute; left: 0; right: 0; top: 115%; background: #071830; border: 1px solid rgba(6,182,212,0.25); border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.6); z-index: 999; max-height: 320px; overflow-y: auto; padding: 0.4rem;"
                 x-transition>
                <template x-for="item in results" :key="item.url + item.title">
                    <a :href="item.url" 
                       style="display: flex; align-items: center; justify-content: space-between; padding: 0.6rem 0.8rem; border-radius: 8px; text-decoration: none; transition: all 0.15s; margin-bottom: 2px;"
                       class="hover:bg-slate-800/80 group">
                        <div style="min-width: 0; flex: 1; padding-right: 0.5rem;">
                            <div style="font-size: 0.8rem; font-weight: 700; color: #FFFFFF;" x-text="item.title"></div>
                            <div style="font-size: 0.68rem; color: #94A3B8; margin-top: 1px;" x-text="item.sub"></div>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[9px] font-extrabold uppercase shrink-0" 
                              style="background: rgba(6,182,212,0.12); color: #38BDF8;"
                              x-text="item.type"></span>
                    </a>
                </template>
            </div>
            
            <!-- Loading Indicator -->
            <div x-show="loading" 
                 style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%);"
                 class="flex items-center">
                 <svg class="animate-spin h-4 w-4 text-cyan-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                     <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                     <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                 </svg>
            </div>
        </div>

        <!-- Right: export, clock, theme, notif, user avatar dropdown -->
        <div class="flex items-center gap-3">
            <!-- Mobile Menu Button -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 rounded-lg" style="background: rgba(255,255,255,0.05);">
                <svg class="w-5 h-5 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>

            <!-- Ekspor (outline) -->
            <button
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl transition-all duration-200"
                style="border: 1px solid rgba(6,182,212,0.45); color: #06B6D4; background: transparent; cursor: pointer;"
                onmouseenter="this.style.background='linear-gradient(to right,#0284C7,#06B6D4)'; this.style.color='#fff'; this.style.border='1px solid transparent';"
                onmouseleave="this.style.background='transparent'; this.style.color='#06B6D4'; this.style.border='1px solid rgba(6,182,212,0.45)';"
                onclick="window.location.href='{{ route('reports.attendance.export') }}'"
            >
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span style="font-family: 'Plus Jakarta Sans',sans-serif; font-size: 12px; font-weight: 600;">Ekspor</span>
            </button>

            <!-- Live clock -->
            <div class="hidden sm:block" style="font-size: 0.75rem; color: #94A3B8; font-variant-numeric: tabular-nums; padding: 0.35rem 0.8rem; background: rgba(0,0,0,0.2); border: 1px solid rgba(6,182,212,0.15); border-radius: 8px;" id="live-clock"></div>

            <!-- Theme Toggle -->
            <button id="theme-toggle" class="p-2 rounded-lg" style="background: rgba(0,0,0,0.2); border: 1px solid rgba(6,182,212,0.15); cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background 0.2s;" title="Ubah Tema"
                onmouseover="this.style.background='var(--bg-hover)'" onmouseout="this.style.background='rgba(0,0,0,0.2)'">
                <svg id="theme-toggle-dark-icon" class="w-4 h-4 hidden" style="color:#94A3B8;" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                <svg id="theme-toggle-light-icon" class="w-4 h-4 hidden" style="color:#F59E0B;" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.46 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
            </button>

            <!-- Notifications -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="relative p-2 rounded-lg" style="background: rgba(0,0,0,0.2); border: 1px solid rgba(6,182,212,0.15); cursor: pointer; display:flex; align-items:center; transition: background 0.2s;"
                    onmouseover="this.style.background='var(--bg-hover)'" onmouseout="this.style.background='rgba(0,0,0,0.2)'">
                    <svg class="w-4 h-4" style="color:#94A3B8;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    @php $unread = auth()->user()->unreadNotificationsCount(); @endphp
                    @if($unread > 0)
                    <span style="position:absolute; top:-2px; right:-2px; width:16px; height:16px; background:#EF4444; border-radius:50%; color:white; font-size:0.55rem; display:flex; align-items:center; justify-content:center; font-weight:700;">{{ $unread }}</span>
                    @endif
                </button>
                <div x-show="open" @click.away="open = false" x-transition style="position: absolute; right: 0; top: 120%; width: 320px; background: var(--bg-elevated); border: 1px solid rgba(6,182,212,0.15); border-radius: 14px; box-shadow: 0 12px 40px rgba(0,0,0,0.4); z-index: 100; overflow: hidden;">
                    <div style="padding: 1rem; border-bottom: 1px solid rgba(6,182,212,0.15); display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 0.85rem; font-weight: 700; color: var(--t1);">Notifikasi</span>
                        @if($unread > 0)
                        <button onclick="fetch('/notifications/read-all', {method:'POST', headers:{'X-CSRF-TOKEN': '{{ csrf_token() }}'}}).then(() => window.location.reload())" style="font-size: 0.7rem; color: #06B6D4; background: none; border: none; cursor: pointer; font-weight: 600;">Tandai dibaca</button>
                        @endif
                    </div>
                    @forelse(auth()->user()->notifications()->latest()->limit(5)->get() as $notif)
                    <div onclick="fetch('/notifications/{{ $notif->id }}/read', {method:'POST', headers:{'X-CSRF-TOKEN': '{{ csrf_token() }}'}}).then(() => window.location.reload())"
                         style="padding: 0.75rem 1rem; border-bottom: 1px solid rgba(6,182,212,0.15); {{ $notif->read_at ? '' : 'background: rgba(6,182,212,0.04);' }} transition: background 0.15s; cursor: pointer;" 
                         onmouseover="this.style.background='var(--bg-hover)'" 
                         onmouseout="this.style.background='{{ $notif->read_at ? '' : 'rgba(6,182,212,0.04)' }}'">
                        <div style="font-size: 0.78rem; font-weight: 600; color: var(--t1); display: flex; align-items: center; gap: 0.4rem;">
                            @if(!$notif->read_at)
                                <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 shrink-0"></span>
                            @endif
                            <span>{{ $notif->title }}</span>
                        </div>
                        <div style="font-size: 0.72rem; color: var(--t3); margin-top: 0.2rem;">{{ $notif->message }}</div>
                        <div style="font-size: 0.65rem; color: var(--t4); margin-top: 0.3rem;">{{ $notif->created_at->diffForHumans() }}</div>
                    </div>
                    @empty
                    <div style="padding: 2rem; text-align: center; color: var(--t3); font-size: 0.8rem;">Tidak ada notifikasi</div>
                    @endforelse
                    <a href="{{ route('notifications.index') }}" style="display: block; padding: 0.75rem; text-align: center; font-size: 0.75rem; color: #06B6D4; border-top: 1px solid rgba(6,182,212,0.15); text-decoration: none; font-weight: 600;">Lihat semua</a>
                </div>
            </div>

            <!-- User profile dropdown -->
            <div x-data="{ open: false }" class="relative" @click.away="open = false">
                <button @click="open = !open" class="avatar" style="overflow: hidden; font-size: 0.7rem; cursor: pointer;">
                    @if(auth()->user()->photo)
                        <img src="{{ auth()->user()->photo_url }}" alt="{{ auth()->user()->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        {{ auth()->user()->initials }}
                    @endif
                </button>
                <div x-show="open" x-transition class="topnav-dropdown" style="right: 0; left: auto; width: 220px; top: 120%;">
                    <div style="padding: 0.5rem 0.85rem; border-bottom: 1px solid var(--border-soft); margin-bottom: 0.25rem;">
                        <div style="font-size: 0.78rem; font-weight: 700; color: var(--t1); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ auth()->user()->name }}</div>
                        <div style="font-size: 0.65rem; color: var(--t4);">{{ auth()->user()->role_label }}</div>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="topnav-dropdown-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Profil Saya
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="topnav-dropdown-item w-full text-left" style="color: #f87171; border: none; background: transparent; cursor: pointer;">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- App layout wrapper (Sidebar + Content) -->
    <div class="app-layout">
        <!-- Sidebar (Left, 192px) -->
        <aside class="sidebar hidden lg:flex">
            <div style="padding: 1rem 0.75rem; width: 100%;">
                <span style="font-family: 'Plus Jakarta Sans',sans-serif; font-size: 9px; font-weight: 700; letter-spacing: 0.12em; color: var(--t4); display: block; margin-bottom: 0.75rem; padding-left: 0.5rem;">
                    MENU UTAMA
                </span>

                <nav style="display: flex; flex-direction: column; gap: 0.25rem;">
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}" class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                            <span>Dashboard</span>
                        </div>
                        @if(request()->routeIs('dashboard'))
                            <span style="font-size: 0.7rem;">&gt;</span>
                        @endif
                    </a>

                    <!-- Kalender -->
                    <a href="{{ route('calendar.index') }}" class="sidebar-item {{ request()->routeIs('calendar.*') ? 'active' : '' }}">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span>Kalender</span>
                        </div>
                        @if(request()->routeIs('calendar.index'))
                            <span style="font-size: 0.7rem;">&gt;</span>
                        @endif
                    </a>

                    <!-- Attendance Collapsible -->
                    <div x-data="{ open: {{ request()->routeIs('attendance.*') ? 'true' : 'false' }} }" style="display: flex; flex-direction: column;">
                        <button @click="open = !open" class="sidebar-item w-full" :class="open || {{ request()->routeIs('attendance.*') ? 'true' : 'false' }} ? 'active' : ''">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span>Attendance</span>
                            </div>
                            <svg class="w-3 h-3 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition style="padding-left: 1.5rem; display: flex; flex-direction: column; gap: 0.15rem; margin-top: 0.15rem; margin-bottom: 0.25rem;">
                            <a href="{{ route('attendance.check-in') }}" class="sidebar-subitem {{ request()->routeIs('attendance.check-in') ? 'active' : '' }}">Absen Masuk</a>
                            <a href="{{ route('attendance.check-out') }}" class="sidebar-subitem {{ request()->routeIs('attendance.check-out') ? 'active' : '' }}">Absen Pulang</a>
                            <a href="{{ route('attendance.history') }}" class="sidebar-subitem {{ request()->routeIs('attendance.history') ? 'active' : '' }}">Riwayat</a>
                        </div>
                    </div>

                    <!-- Leave Collapsible -->
                    <div x-data="{ open: {{ request()->routeIs('leave.*') || request()->routeIs('permission.*') || request()->routeIs('overtime.*') ? 'true' : 'false' }} }" style="display: flex; flex-direction: column;">
                        <button @click="open = !open" class="sidebar-item w-full" :class="open || {{ request()->routeIs('leave.*') || request()->routeIs('permission.*') || request()->routeIs('overtime.*') ? 'true' : 'false' }} ? 'active' : ''">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>Leave & Permits</span>
                            </div>
                            <svg class="w-3 h-3 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition style="padding-left: 1.5rem; display: flex; flex-direction: column; gap: 0.15rem; margin-top: 0.15rem; margin-bottom: 0.25rem;">
                            <a href="{{ route('leave.index') }}" class="sidebar-subitem {{ request()->routeIs('leave.index') ? 'active' : '' }}">Cuti</a>
                            <a href="{{ route('permission.index') }}" class="sidebar-subitem {{ request()->routeIs('permission.index') ? 'active' : '' }}">Izin</a>
                            <a href="{{ route('overtime.index') }}" class="sidebar-subitem {{ request()->routeIs('overtime.index') ? 'active' : '' }}">Lembur</a>
                        </div>
                    </div>

                    <!-- Documents Collapsible -->
                    <div x-data="{ open: {{ request()->routeIs('letters.*') || request()->routeIs('documents.*') || request()->routeIs('announcements.*') ? 'true' : 'false' }} }" style="display: flex; flex-direction: column;">
                        <button @click="open = !open" class="sidebar-item w-full" :class="open || {{ request()->routeIs('letters.*') || request()->routeIs('documents.*') || request()->routeIs('announcements.*') ? 'true' : 'false' }} ? 'active' : ''">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>Documents</span>
                            </div>
                            <svg class="w-3 h-3 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition style="padding-left: 1.5rem; display: flex; flex-direction: column; gap: 0.15rem; margin-top: 0.15rem; margin-bottom: 0.25rem;">
                            <a href="{{ route('letters.index') }}" class="sidebar-subitem {{ request()->routeIs('letters.index') ? 'active' : '' }}">Surat Menyurat</a>
                            <a href="{{ route('documents.index') }}" class="sidebar-subitem {{ request()->routeIs('documents.index') ? 'active' : '' }}">File Bersama</a>
                            <a href="{{ route('announcements.index') }}" class="sidebar-subitem {{ request()->routeIs('announcements.index') ? 'active' : '' }}">Pengumuman</a>
                        </div>
                    </div>

                    <!-- Master Data Collapsible (Admin/HRD only) -->
                    @hasrole(['super_admin', 'hrd'])
                    <div x-data="{ open: {{ request()->routeIs('master.*') ? 'true' : 'false' }} }" style="display: flex; flex-direction: column;">
                        <button @click="open = !open" class="sidebar-item w-full" :class="open || {{ request()->routeIs('master.*') ? 'true' : 'false' }} ? 'active' : ''">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>
                                <span>Master Data</span>
                            </div>
                            <svg class="w-3 h-3 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition style="padding-left: 1.5rem; display: flex; flex-direction: column; gap: 0.15rem; margin-top: 0.15rem; margin-bottom: 0.25rem;">
                            <a href="{{ route('master.divisions.index') }}" class="sidebar-subitem {{ request()->routeIs('master.divisions.*') ? 'active' : '' }}">Divisi</a>
                            <a href="{{ route('master.positions.index') }}" class="sidebar-subitem {{ request()->routeIs('master.positions.*') ? 'active' : '' }}">Jabatan</a>
                            <a href="{{ route('master.shifts.index') }}" class="sidebar-subitem {{ request()->routeIs('master.shifts.*') ? 'active' : '' }}">Shift Kerja</a>
                            <a href="{{ route('master.locations.index') }}" class="sidebar-subitem {{ request()->routeIs('master.locations.*') ? 'active' : '' }}">Lokasi GPS</a>
                        </div>
                    </div>
                    @endhasrole

                    <!-- Reports Collapsible (Admin/Manager only) -->
                    @hasrole(['super_admin', 'hrd', 'manager'])
                    <div x-data="{ open: {{ request()->routeIs('reports.*') || request()->routeIs('employees.*') ? 'true' : 'false' }} }" style="display: flex; flex-direction: column;">
                        <button @click="open = !open" class="sidebar-item w-full" :class="open || {{ request()->routeIs('reports.*') || request()->routeIs('employees.*') ? 'true' : 'false' }} ? 'active' : ''">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                <span>Reports</span>
                            </div>
                            <svg class="w-3 h-3 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition style="padding-left: 1.5rem; display: flex; flex-direction: column; gap: 0.15rem; margin-top: 0.15rem; margin-bottom: 0.25rem;">
                            @hasrole(['super_admin', 'hrd'])
                            <a href="{{ route('employees.index') }}" class="sidebar-subitem {{ request()->routeIs('employees.*') ? 'active' : '' }}">Data Karyawan</a>
                            @endhasrole
                            <a href="{{ route('reports.attendance') }}" class="sidebar-subitem {{ request()->routeIs('reports.attendance') ? 'active' : '' }}">Laporan Absen</a>
                            <a href="{{ route('reports.lateness') }}" class="sidebar-subitem {{ request()->routeIs('reports.lateness') ? 'active' : '' }}">Lateness</a>
                            <a href="{{ route('reports.leave') }}" class="sidebar-subitem {{ request()->routeIs('reports.leave') ? 'active' : '' }}">Laporan Cuti</a>
                            <a href="{{ route('reports.gps') }}" class="sidebar-subitem {{ request()->routeIs('reports.gps') ? 'active' : '' }}">Peta Lokasi GPS</a>
                        </div>
                    </div>
                    @endhasrole

                    <!-- Settings Collapsible (Admin only) -->
                    @hasrole('super_admin')
                    <div x-data="{ open: {{ request()->routeIs('settings.*') ? 'true' : 'false' }} }" style="display: flex; flex-direction: column;">
                        <button @click="open = !open" class="sidebar-item w-full" :class="open || {{ request()->routeIs('settings.*') ? 'true' : 'false' }} ? 'active' : ''">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>Settings</span>
                            </div>
                            <svg class="w-3 h-3 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition style="padding-left: 1.5rem; display: flex; flex-direction: column; gap: 0.15rem; margin-top: 0.15rem; margin-bottom: 0.25rem;">
                            <a href="{{ route('settings.company') }}" class="sidebar-subitem {{ request()->routeIs('settings.company') ? 'active' : '' }}">Profil Kantor</a>
                            <a href="{{ route('settings.users.index') }}" class="sidebar-subitem {{ request()->routeIs('settings.users.*') ? 'active' : '' }}">User Manajemen</a>
                            <a href="{{ route('settings.audit-logs') }}" class="sidebar-subitem {{ request()->routeIs('settings.audit-logs') ? 'active' : '' }}">Audit Logs</a>
                        </div>
                    </div>
                    @endhasrole
                </nav>
            </div>

            <!-- Sidebar footer section -->
            <div style="padding: 1rem 0.75rem; border-top: 1px solid var(--border-soft); margin-top: auto; width: 100%;">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
                    <div style="width: 8px; height: 8px; border-radius: 50%; background: #22C55E; box-shadow: 0 0 10px #22C55E;" class="animate-pulse"></div>
                    <span style="font-size: 0.75rem; color: #22C55E; font-weight: 700;">System Healthy</span>
                </div>
                <div style="font-size: 0.65rem; color: var(--t4);">Last sync 2 min ago</div>
                <div style="font-size: 0.65rem; color: var(--t5); margin-top: 0.15rem; font-family: monospace;">VALRYZE v1.0.0</div>
            </div>
        </aside>

        <!-- Main Content area -->
        <div style="flex: 1; display: flex; flex-direction: column; overflow-y: auto;" class="main-content">
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

            <!-- Status Bar (Bottom, Full Width inside content area) -->
            <footer class="status-bar" style="height: 28px; background: #071830; border-top: 1px solid var(--border-soft); display: flex; align-items: center; justify-content: space-between; padding: 0 1rem; font-size: 0.7rem; color: var(--t4); flex-shrink: 0; z-index: 100;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #10B981; box-shadow: 0 0 8px #10B981;"></span>
                    <span>Sistem berjalan normal</span>
                </div>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    @php
                        $onTimeRateStatus = 100;
                        try {
                            $presentStatusCount = \App\Models\Attendance::where('date', date('Y-m-d'))->where('status', 'present')->count();
                            $lateStatusCount = \App\Models\Attendance::where('date', date('Y-m-d'))->where('status', 'late')->count();
                            $totalStatusCount = $presentStatusCount + $lateStatusCount;
                            $onTimeRateStatus = $totalStatusCount > 0 ? round(($presentStatusCount / $totalStatusCount) * 100, 1) : 100;
                        } catch(\Exception $e) {}
                    @endphp
                    <span>{{ $onTimeRateStatus }}% On-time</span>
                    <span>·</span>
                    <span id="status-bar-clock">00:00</span>
                    <span>·</span>
                    <span>VALRYZE v1.0.0</span>
                </div>
            </footer>
        </div>
    </div>

    <!-- Mobile Navigation Drawer (Dropdown mobile alternative) -->
    <div x-show="mobileMenuOpen" x-transition class="lg:hidden" style="position: fixed; top: 56px; left: 0; right: 0; background: var(--bg-topbar); border-bottom: 1px solid var(--border-soft); padding: 1rem; display: flex; flex-direction: column; gap: 0.5rem; z-index: 45; max-height: calc(100vh - 56px); overflow-y: auto;">
        <a href="{{ route('dashboard') }}" class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
        <a href="{{ route('calendar.index') }}" class="sidebar-item {{ request()->routeIs('calendar.*') ? 'active' : '' }}">Kalender</a>
        <div style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--t4); padding-left: 0.75rem; font-weight: 700; margin-top: 0.25rem;">Absensi</div>
        <a href="{{ route('attendance.check-in') }}" class="sidebar-subitem {{ request()->routeIs('attendance.check-in') ? 'active' : '' }}">Absen Masuk</a>
        <a href="{{ route('attendance.check-out') }}" class="sidebar-subitem {{ request()->routeIs('attendance.check-out') ? 'active' : '' }}">Absen Pulang</a>
        <a href="{{ route('attendance.history') }}" class="sidebar-subitem {{ request()->routeIs('attendance.history') ? 'active' : '' }}">Riwayat Absen</a>

        <div style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--t4); padding-left: 0.75rem; font-weight: 700; margin-top: 0.25rem;">Perizinan</div>
        <a href="{{ route('permission.index') }}" class="sidebar-subitem {{ request()->routeIs('permission.index') ? 'active' : '' }}">Izin</a>
        <a href="{{ route('leave.index') }}" class="sidebar-subitem {{ request()->routeIs('leave.index') ? 'active' : '' }}">Cuti</a>
        <a href="{{ route('overtime.index') }}" class="sidebar-subitem {{ request()->routeIs('overtime.index') ? 'active' : '' }}">Lembur</a>

        <div style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--t4); padding-left: 0.75rem; font-weight: 700; margin-top: 0.25rem;">Dokumen</div>
        <a href="{{ route('letters.index') }}" class="sidebar-subitem {{ request()->routeIs('letters.index') ? 'active' : '' }}">Surat Menyurat</a>
        <a href="{{ route('documents.index') }}" class="sidebar-subitem {{ request()->routeIs('documents.index') ? 'active' : '' }}">File Bersama</a>
        <a href="{{ route('announcements.index') }}" class="sidebar-subitem {{ request()->routeIs('announcements.index') ? 'active' : '' }}">Pengumuman</a>

        @hasrole(['super_admin', 'hrd'])
        <div style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--t4); padding-left: 0.75rem; font-weight: 700; margin-top: 0.25rem;">Manajemen & Laporan</div>
        <a href="{{ route('employees.index') }}" class="sidebar-subitem {{ request()->routeIs('employees.index') ? 'active' : '' }}">Data Karyawan</a>
        <a href="{{ route('reports.attendance') }}" class="sidebar-subitem {{ request()->routeIs('reports.attendance') ? 'active' : '' }}">Laporan Absen</a>
        <a href="{{ route('reports.lateness') }}" class="sidebar-subitem {{ request()->routeIs('reports.lateness') ? 'active' : '' }}">Lateness</a>
        <a href="{{ route('reports.leave') }}" class="sidebar-subitem {{ request()->routeIs('reports.leave') ? 'active' : '' }}">Laporan Cuti</a>
        <a href="{{ route('reports.gps') }}" class="sidebar-subitem {{ request()->routeIs('reports.gps') ? 'active' : '' }}">Peta Lokasi GPS</a>
        @endhasrole

        @hasrole(['super_admin', 'hrd'])
        <div style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--t4); padding-left: 0.75rem; font-weight: 700; margin-top: 0.25rem;">Master Data</div>
        <a href="{{ route('master.divisions.index') }}" class="sidebar-subitem {{ request()->routeIs('master.divisions.*') ? 'active' : '' }}">Divisi</a>
        <a href="{{ route('master.positions.index') }}" class="sidebar-subitem {{ request()->routeIs('master.positions.*') ? 'active' : '' }}">Jabatan</a>
        <a href="{{ route('master.shifts.index') }}" class="sidebar-subitem {{ request()->routeIs('master.shifts.*') ? 'active' : '' }}">Shift Kerja</a>
        <a href="{{ route('master.locations.index') }}" class="sidebar-subitem {{ request()->routeIs('master.locations.*') ? 'active' : '' }}">Lokasi GPS</a>
        @endhasrole

        @hasrole('super_admin')
        <div style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--t4); padding-left: 0.75rem; font-weight: 700; margin-top: 0.25rem;">Settings</div>
        <a href="{{ route('settings.company') }}" class="sidebar-subitem {{ request()->routeIs('settings.company') ? 'active' : '' }}">Profil Kantor</a>
        <a href="{{ route('settings.users.index') }}" class="sidebar-subitem {{ request()->routeIs('settings.users.*') ? 'active' : '' }}">User Manajemen</a>
        <a href="{{ route('settings.audit-logs') }}" class="sidebar-subitem {{ request()->routeIs('settings.audit-logs') ? 'active' : '' }}">Audit Logs</a>
        @endhasrole
    </div>

    <script>
        // Live clocks
        function updateClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit', second: '2-digit'}) + ' WIB';
            const statusTimeStr = now.toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'});
            
            const liveClock = document.getElementById('live-clock');
            if (liveClock) liveClock.textContent = timeStr;
            
            const statusClock = document.getElementById('status-bar-clock');
            if (statusClock) statusClock.textContent = statusTimeStr;
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

        function globalSearchApp() {
            return {
                query: '',
                results: [],
                loading: false,
                performSearch() {
                    if (this.query.trim().length < 2) {
                        this.results = [];
                        return;
                    }
                    this.loading = true;
                    fetch(`/global-search?q=${encodeURIComponent(this.query)}`)
                        .then(res => res.json())
                        .then(data => {
                            this.results = data;
                            this.loading = false;
                        })
                        .catch(err => {
                            console.error(err);
                            this.loading = false;
                        });
                },
                clearSearch() {
                    // Slight delay to allow click on links before closing
                    setTimeout(() => {
                        this.query = '';
                        this.results = [];
                    }, 200);
                }
            };
        }
    </script>

    @if(auth()->check() && auth()->user()->birth_date && auth()->user()->birth_date->format('m-d') === now()->format('m-d'))
        <!-- Birthday Celebration overlay + script -->
        <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
        <div id="birthday-celebration-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm transition-opacity duration-300">
            <div class="card max-w-sm w-full p-6 text-center shadow-2xl scale-95 transform transition-transform duration-300 relative overflow-hidden" style="background: linear-gradient(135deg, #1e1b4b 0%, #0f172a 100%); border: 2px solid #ec4899;">
                <div class="relative z-10 space-y-4">
                    <div class="w-16 h-16 bg-pink-500/10 border-2 border-pink-500/30 rounded-full flex items-center justify-center mx-auto shadow-lg shadow-pink-500/20">
                        <span class="text-3xl animate-bounce">🎂</span>
                    </div>
                    
                    <h2 class="text-xl font-black text-transparent bg-clip-text bg-gradient-to-r from-pink-400 to-indigo-400">Selamat Ulang Tahun! 🎉</h2>
                    <p class="text-slate-200 text-sm font-semibold">{{ auth()->user()->name }}</p>
                    <p class="text-slate-400 text-xs leading-relaxed">
                        Manajemen & segenap rekan kerja di PT. Smart Teknologi Indonesia mengucapkan Selamat Hari Ulang Tahun! Semoga panjang umur, sehat selalu, dan dilancarkan segala urusannya. 🌟
                    </p>
                    
                    <div class="pt-2">
                        <button onclick="closeBirthdayCelebration()" class="btn btn-primary w-full justify-center" style="background: linear-gradient(135deg, #ec4899, #be185d); box-shadow: 0 4px 14px rgba(236,72,153,0.4);">
                            Terima Kasih! ❤️
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            window.addEventListener('DOMContentLoaded', (event) => {
                const end = Date.now() + (3 * 1000); // 3 seconds
                const colors = ['#ec4899', '#3b82f6', '#10b981', '#f59e0b'];

                (function frame() {
                    confetti({
                        particleCount: 2,
                        angle: 60,
                        spread: 55,
                        origin: { x: 0 },
                        colors: colors
                    });
                    confetti({
                        particleCount: 2,
                        angle: 120,
                        spread: 55,
                        origin: { x: 1 },
                        colors: colors
                    });

                    if (Date.now() < end) {
                        requestAnimationFrame(frame);
                    }
                }());

                confetti({
                    particleCount: 80,
                    spread: 70,
                    origin: { y: 0.6 },
                    colors: colors
                });
            });

            function closeBirthdayCelebration() {
                const modal = document.getElementById('birthday-celebration-modal');
                if (modal) {
                    modal.style.opacity = '0';
                    setTimeout(() => modal.remove(), 300);
                }
            }
        </script>
    @endif

    @stack('scripts')
</body>
</html>
