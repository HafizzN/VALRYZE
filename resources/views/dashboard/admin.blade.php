@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')
@section('breadcrumb', 'Selamat datang, ' . auth()->user()->name)

@section('content')
@php
    $h = date('H');
    $greeting = $h < 11 ? 'Selamat Pagi' : ($h < 15 ? 'Selamat Siang' : ($h < 18 ? 'Selamat Sore' : 'Selamat Malam'));
    $pendingApprovals = ($pendingLeave ?? 0) + ($pendingPermission ?? 0) + ($pendingOvertime ?? 0);
    $onTimeRate = $presentToday > 0 ? round((($presentToday - $lateToday) / $presentToday) * 100, 1) : 100;
@endphp

<div class="space-y-5">

    {{-- VALRYZE Hero Section --}}
    <div class="hero-section">
        <div class="relative flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <!-- Left — greeting + pills -->
            <div class="flex-1">
                <p class="uppercase mb-1" style="font-family: 'Plus Jakarta Sans',sans-serif; font-size: 10px; font-weight: 700; letter-spacing: 0.13em; color: var(--hero-label);">
                    Hero Dashboard
                </p>
                <h1 class="text-white mb-3" style="font-family: 'Plus Jakarta Sans',sans-serif; font-size: 22px; font-weight: 700;">
                    {{ $greeting }}, {{ explode(' ', auth()->user()->name)[0] }} ✨
                </h1>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="flex items-center gap-1.5 px-2.5 py-1 rounded-full animate-pulse"
                          style="background: rgba(6,182,212,0.2); border: 1px solid rgba(6,182,212,0.32); color: #BAE6FD; font-family: 'Plus Jakarta Sans',sans-serif; font-size: 12px; font-weight: 600;">
                        <span style="width:6px; height:6px; background:#06B6D4; border-radius:50%;"></span>
                        {{ $totalEmployees > 0 ? round(($presentToday/$totalEmployees)*100) : 0 }}% hadir
                    </span>
                    <span style="color: var(--hero-label); font-size: 11px;">·</span>
                    <span style="font-family: 'Plus Jakarta Sans',sans-serif; font-size: 12px; color: var(--hero-sub);">{{ $presentToday }} Present</span>
                    <span style="color: var(--hero-label); font-size: 11px;">·</span>
                    <span class="flex items-center gap-1.5 px-2.5 py-1 rounded-full"
                          style="background: rgba(245,158,11,0.16); border: 1px solid rgba(245,158,11,0.28); color: #FCD34D; font-family: 'Plus Jakarta Sans',sans-serif; font-size: 12px; font-weight: 600;">
                        ⚠ {{ $pendingApprovals }} Pending
                    </span>
                    @if(auth()->user()->shift)
                        <span style="color: var(--hero-label); font-size: 11px;">·</span>
                        <span style="font-family: 'Plus Jakarta Sans',sans-serif; font-size: 12px; color: var(--hero-sub);">
                            Shift {{ \Carbon\Carbon::parse(auth()->user()->shift->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse(auth()->user()->shift->end_time)->format('H:i') }}
                        </span>
                    @endif
                    @if(auth()->user()->division)
                        <span style="color: var(--hero-label); font-size: 11px;">·</span>
                        <span style="font-family: 'Plus Jakarta Sans',sans-serif; font-size: 12px; color: var(--hero-sub);">
                            {{ auth()->user()->division->name }}
                        </span>
                    @endif
                </div>
            </div>

            <!-- Right — Today's Summary card -->
            <div class="shrink-0 rounded-2xl px-5 py-4 flex items-center gap-5"
                 style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08); backdrop-filter: blur(12px); box-shadow: 0 8px 32px rgba(0,0,0,0.37);">
                <!-- Ring chart -->
                <div class="relative flex items-center justify-center" style="width: 76px; height: 76px;">
                    @php
                        $pct = $totalEmployees > 0 ? round(($presentToday/$totalEmployees)*100) : 0;
                        $r = 33;
                        $circ = 2 * pi() * $r;
                        $dash = ($pct / 100) * $circ;
                    @endphp
                    <svg width="76" height="76" viewBox="0 0 76 76" style="transform: rotate(-90deg);" class="overflow-visible">
                        <circle cx="38" cy="38" r="{{ $r }}" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="6" />
                        <circle cx="38" cy="38" r="{{ $r }}" fill="none"
                                stroke="url(#rg)" stroke-width="6"
                                stroke-dasharray="{{ $dash }} {{ $circ }}" stroke-linecap="round" />
                        <defs>
                            <linearGradient id="rg" x1="0%" y1="0%" x2="100%" y2="0%">
                                <stop offset="0%" stop-color="#06B6D4" />
                                <stop offset="100%" stop-color="#38BDF8" />
                            </linearGradient>
                        </defs>
                    </svg>
                    <div class="absolute text-center" style="transform: translate(0, 0);">
                        <div class="text-white" style="font-family: 'Plus Jakarta Sans',sans-serif; font-size: 13px; font-weight: 800; line-height: 1;">{{ $pct }}%</div>
                        <div style="font-family: 'Plus Jakarta Sans',sans-serif; font-size: 8px; color: #E2E8F0; line-height: 1.2;">hadir</div>
                    </div>
                </div>
                <!-- Stats breakdown -->
                <div class="space-y-1">
                    <p class="uppercase" style="font-family: 'Plus Jakarta Sans',sans-serif; font-size: 9px; font-weight: 700; letter-spacing: 0.12em; color: #BAE6FD; margin-bottom: 0.25rem;">
                        Ringkasan Hari Ini
                    </p>
                    <div class="flex items-center gap-3">
                        <div class="w-1.5 h-3 rounded-full bg-[#34D399]"></div>
                        <span style="font-family: 'JetBrains Mono',monospace; font-size: 12px; font-weight: 700; color: #FFFFFF;">{{ $presentToday }}</span>
                        <span style="font-family: 'Plus Jakarta Sans',sans-serif; font-size: 10px; color: #E2E8F0;">Hadir</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-1.5 h-3 rounded-full bg-[#FCD34D]"></div>
                        <span style="font-family: 'JetBrains Mono',monospace; font-size: 12px; font-weight: 700; color: #FFFFFF;">{{ $pendingApprovals }}</span>
                        <span style="font-family: 'Plus Jakarta Sans',sans-serif; font-size: 10px; color: #E2E8F0;">Pending</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-1.5 h-3 rounded-full bg-[#F87171]"></div>
                        <span style="font-family: 'JetBrains Mono',monospace; font-size: 12px; font-weight: 700; color: #FFFFFF;">{{ max(0, $absentToday) }}</span>
                        <span style="font-family: 'Plus Jakarta Sans',sans-serif; font-size: 10px; color: #E2E8F0;">Absen</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- VALRYZE AI Assistant / Insight --}}
    <div class="rounded-2xl px-5 py-4 flex flex-col md:flex-row items-start gap-4"
         style="background: var(--bg-card); border: 1px solid var(--border-soft); box-shadow: var(--shadow-card);">
        <!-- Left: Branding -->
        <div class="flex items-center gap-3 shrink-0">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                 style="background: var(--em-ghost); border: 1px solid var(--em-border); box-shadow: 0 0 15px var(--em-glow);">
                <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
            </div>
            <div>
                <h4 style="font-family: 'Plus Jakarta Sans',sans-serif; font-size: 13px; font-weight: 800; color: var(--t1); display: flex; align-items: center; gap: 0.25rem;">
                    ✨ VALRYZE AI Assistant
                </h4>
                <p style="font-size: 10px; color: var(--t4);">Rekomendasi Tindakan Hari Ini</p>
            </div>
        </div>

        <div style="width: 1px; background: var(--border-soft); align-self: stretch;" class="hidden md:block"></div>

        <!-- Right: Prioritized Insights -->
        <div class="flex-1 space-y-2.5 w-full">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="space-y-2 flex-1">
                    <!-- High Priority -->
                    <div class="flex items-center gap-2.5">
                        <span class="px-2 py-0.5 rounded-md text-[9px] font-extrabold uppercase" style="background: rgba(239,68,68,0.12); color: #FCA5A5; border: 1px solid rgba(239,68,68,0.2);">High</span>
                        <span style="font-size: 11.5px; color: var(--t3);">
                            Ada <strong style="color: var(--t1);">{{ $pendingApprovals }} Permintaan Persetujuan</strong> baru menunggu tindakan Anda (Cuti / Izin / Lembur).
                        </span>
                    </div>

                    <!-- Medium Priority -->
                    <div class="flex items-center gap-2.5">
                        <span class="px-2 py-0.5 rounded-md text-[9px] font-extrabold uppercase" style="background: rgba(245,158,11,0.12); color: #FCD34D; border: 1px solid rgba(245,158,11,0.2);">Medium</span>
                        <span style="font-size: 11.5px; color: var(--t3);">
                            Tingkat kehadiran stabil di angka <strong style="color: var(--em);">{{ $pct }}%</strong> dengan tingkat ketepatan waktu rata-rata <strong style="color: var(--t1);">{{ $onTimeRate }}%</strong>.
                        </span>
                    </div>

                    <!-- Low Priority -->
                    <div class="flex items-center gap-2.5">
                        <span class="px-2 py-0.5 rounded-md text-[9px] font-extrabold uppercase" style="background: rgba(148,163,184,0.12); color: #94A3B8; border: 1px solid rgba(148,163,184,0.2);">Low</span>
                        <span style="font-size: 11.5px; color: var(--t3);">
                            Audit logs mendeteksi <strong style="color: var(--t2);">{{ $failedJobsCount }} error pekerjaan</strong> antrean sistem.
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 4 KPI cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- KPI 1: Total Karyawan -->
        <div class="card p-5 flex flex-col justify-between" style="min-height: 154px;">
            <div class="flex items-start justify-between mb-2">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background: rgba(14,165,233,0.15);">
                    <svg class="w-5 h-5 text-[#0EA5E9]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <span class="flex items-center gap-0.5 px-2 py-0.5 rounded-full"
                      style="background: rgba(34,197,94,0.1); color: #22C55E; font-family: 'Plus Jakarta Sans',sans-serif; font-size: 11px; font-weight: 700;">
                    ↑ 100%
                </span>
            </div>
            <div style="font-family: 'Plus Jakarta Sans',sans-serif; font-size: 26px; font-weight: 700; color: var(--t1);">
                {{ $totalEmployees }}
            </div>
            <div class="my-1.5">
                <svg width="100%" height="24" viewBox="0 0 120 24" class="overflow-visible">
                    <path d="M0,15 C20,15 40,8 60,12 C80,16 100,5 120,2" fill="none" stroke="#0EA5E9" stroke-width="2" stroke-linecap="round" />
                    <path d="M0,15 C20,15 40,8 60,12 C80,16 100,5 120,2 L120,24 L0,24 Z" fill="rgba(14,165,233,0.08)" />
                </svg>
            </div>
            <div style="font-family: 'Plus Jakarta Sans',sans-serif; font-size: 11px; color: var(--t3);">
                Total Karyawan Terdaftar
            </div>
        </div>

        <!-- KPI 2: Hadir Hari Ini -->
        <div class="card p-5 flex flex-col justify-between" style="min-height: 154px;">
            <div class="flex items-start justify-between mb-2">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background: rgba(6,182,212,0.15);">
                    <svg class="w-5 h-5 text-[#06B6D4]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="flex items-center gap-0.5 px-2 py-0.5 rounded-full"
                      style="background: rgba(34,197,94,0.1); color: #22C55E; font-family: 'Plus Jakarta Sans',sans-serif; font-size: 11px; font-weight: 700;">
                    ↑ {{ $pct }}%
                </span>
            </div>
            <div style="font-family: 'Plus Jakarta Sans',sans-serif; font-size: 26px; font-weight: 700; color: var(--t1);">
                {{ $presentToday }}
            </div>
            <div class="my-1.5">
                <svg width="100%" height="24" viewBox="0 0 120 24" class="overflow-visible">
                    <path d="M0,20 C20,18 40,22 60,10 C80,6 100,5 120,3" fill="none" stroke="#06B6D4" stroke-width="2" stroke-linecap="round" />
                    <path d="M0,20 C20,18 40,22 60,10 C80,6 100,5 120,3 L120,24 L0,24 Z" fill="rgba(6,182,212,0.08)" />
                </svg>
            </div>
            <div style="font-family: 'Plus Jakarta Sans',sans-serif; font-size: 11px; color: var(--t3);">
                Hadir Hari Ini
            </div>
        </div>

        <!-- KPI 3: Approval Pending -->
        <div class="card p-5 flex flex-col justify-between" style="min-height: 154px;">
            <div class="flex items-start justify-between mb-2">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background: rgba(245,158,11,0.15);">
                    <svg class="w-5 h-5 text-[#F59E0B]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="flex items-center gap-0.5 px-2 py-0.5 rounded-full"
                      style="background: {{ $pendingApprovals > 5 ? 'rgba(239,68,68,0.1)' : 'rgba(34,197,94,0.1)' }}; color: {{ $pendingApprovals > 5 ? '#EF4444' : '#22C55E' }}; font-family: 'Plus Jakarta Sans',sans-serif; font-size: 11px; font-weight: 700;">
                    {{ $pendingApprovals > 5 ? 'Tinggi' : 'Normal' }}
                </span>
            </div>
            <div style="font-family: 'Plus Jakarta Sans',sans-serif; font-size: 26px; font-weight: 700; color: var(--t1);">
                {{ $pendingApprovals }}
            </div>
            <div class="my-1.5">
                <svg width="100%" height="24" viewBox="0 0 120 24" class="overflow-visible">
                    <path d="M0,5 C20,10 40,8 60,18 C80,22 100,15 120,22" fill="none" stroke="#F59E0B" stroke-width="2" stroke-linecap="round" />
                    <path d="M0,5 C20,10 40,8 60,18 C80,22 100,15 120,22 L120,24 L0,24 Z" fill="rgba(245,158,11,0.08)" />
                </svg>
            </div>
            <div style="font-family: 'Plus Jakarta Sans',sans-serif; font-size: 11px; color: var(--t3);">
                Menunggu Persetujuan
            </div>
        </div>

        <!-- KPI 4: On-time Rate -->
        <div class="card p-5 flex flex-col justify-between" style="min-height: 154px;">
            <div class="flex items-start justify-between mb-2">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background: rgba(14,165,233,0.15);">
                    <svg class="w-5 h-5 text-[#0EA5E9]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="flex items-center gap-0.5 px-2 py-0.5 rounded-full"
                      style="background: rgba(34,197,94,0.1); color: #22C55E; font-family: 'Plus Jakarta Sans',sans-serif; font-size: 11px; font-weight: 700;">
                    ↑ 1.5%
                </span>
            </div>
            <div style="font-family: 'Plus Jakarta Sans',sans-serif; font-size: 26px; font-weight: 700; color: var(--t1);">
                {{ $onTimeRate }}%
            </div>
            <div class="my-1.5">
                <svg width="100%" height="24" viewBox="0 0 120 24" class="overflow-visible">
                    <path d="M0,18 C20,18 40,12 60,10 C80,8 100,5 120,2" fill="none" stroke="#0EA5E9" stroke-width="2" stroke-linecap="round" />
                    <path d="M0,18 C20,18 40,12 60,10 C80,8 100,5 120,2 L120,24 L0,24 Z" fill="rgba(14,165,233,0.08)" />
                </svg>
            </div>
            <div style="font-family: 'Plus Jakarta Sans',sans-serif; font-size: 11px; color: var(--t3);">
                On-time Rate (Tepat Waktu)
            </div>
        </div>
    </div>

    {{-- Main content grid split --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">
        
        <!-- Left column (3/5 width): Chart & Kehadiran Terkini -->
        <div class="lg:col-span-3 space-y-5">
            
            <!-- Bar Chart -->
            <div class="card p-5">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h2 style="font-family: 'Plus Jakarta Sans',sans-serif; font-size: 14px; font-weight: 700; color: var(--t1);">
                            Grafik Kehadiran Mingguan
                        </h2>
                        <p style="font-family: 'Plus Jakarta Sans',sans-serif; font-size: 11px; color: var(--t3); margin-top: 2px;">
                            7 hari terakhir
                        </p>
                    </div>
                    <a href="{{ route('reports.attendance') }}" class="btn btn-secondary btn-sm">Lihat Detail</a>
                </div>
                <div style="position: relative; height: 180px;">
                    <canvas id="attendanceChart" style="width: 100%; height: 100%;"></canvas>
                </div>
            </div>

            <!-- Kehadiran Terkini Table -->
            <div class="card p-5">
                <div class="flex items-center justify-between mb-4 pb-3" style="border-bottom: 1px solid var(--border-soft);">
                    <div>
                        <h2 style="font-family: 'Plus Jakarta Sans',sans-serif; font-size: 14px; font-weight: 700; color: var(--t1);">
                            Kehadiran Terkini
                        </h2>
                        <p style="font-family: 'Plus Jakarta Sans',sans-serif; font-size: 11px; color: var(--t3); margin-top: 2px;">
                            Pembaruan waktu nyata
                        </p>
                    </div>
                    <a href="{{ route('attendance.history') }}" class="btn btn-secondary btn-sm">Semua Absen</a>
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Karyawan</th>
                                <th>Masuk</th>
                                <th>Pulang</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentAttendances as $att)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="avatar" style="width: 32px; height: 32px; font-size: 0.65rem; overflow: hidden;">
                                            @if($att->user?->photo)
                                                <img src="{{ $att->user->photo_url }}" alt="{{ $att->user->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                            @else
                                                {{ $att->user->initials }}
                                            @endif
                                        </div>
                                        <div>
                                            <div style="font-size: 0.8rem; font-weight: 700; color: var(--t1);">{{ $att->user->name }}</div>
                                            <div style="font-size: 0.68rem; color: var(--t3);">{{ $att->user->division->name ?? 'Staff' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $att->check_in_time ?? '-' }}</td>
                                <td>{{ $att->check_out_time ?? 'Belum' }}</td>
                                <td>
                                    @php
                                        $badgeMap = ['present' => 'success', 'late' => 'warning', 'absent' => 'danger', 'permission' => 'info', 'leave' => 'purple', 'sick' => 'orange'];
                                        $badgeClass = $badgeMap[$att->status] ?? 'gray';
                                        $statusLabel = ['present' => 'Hadir', 'late' => 'Terlambat', 'absent' => 'Absen', 'permission' => 'Izin', 'leave' => 'Cuti', 'sick' => 'Sakit'][$att->status] ?? $att->status;
                                    @endphp
                                    <span class="badge badge-{{ $badgeClass }}">{{ $statusLabel }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" style="text-align: center; color: var(--t3); padding: 2rem;">Belum ada absensi hari ini</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right column (2/5 width): AI Insight & Pengumuman -->
        <div class="lg:col-span-2 space-y-5">
            
            <!-- AI Insight Sidebar -->
            <div class="card flex flex-col" style="border: 1px solid var(--border-soft);">
                <!-- Header -->
                <div class="px-5 py-4 rounded-t-2xl"
                     style="background: #071830; border-bottom: 1px solid rgba(6,182,212,0.15);">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl flex items-center justify-center"
                                 style="background: rgba(6,182,212,0.18); border: 1px solid rgba(6,182,212,0.3);">
                                <svg class="w-4 h-4 text-[#06B6D4]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-white" style="font-family: 'Plus Jakarta Sans',sans-serif; font-size: 14px; font-weight: 700;">
                                    AI Insight
                                </h2>
                                <p style="font-family: 'Plus Jakarta Sans',sans-serif; font-size: 11px; color: #7DD3FC; margin-top: 1px;">
                                    Analisis cerdas hari ini
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full"
                             style="background: rgba(6,182,212,0.15); border: 1px solid rgba(6,182,212,0.25);">
                            <div class="w-1.5 h-1.5 rounded-full bg-[#06B6D4] animate-pulse"></div>
                            <span style="font-family: 'Plus Jakarta Sans',sans-serif; font-size: 10px; font-weight: 600; color: #06B6D4;">Live</span>
                        </div>
                    </div>
                </div>

                <!-- Insight Cards Container -->
                @php
                    $insights = [];
                    // Dynamic warning lateness
                    $lateTodayUsers = \App\Models\Attendance::where('date', date('Y-m-d'))->where('status', 'late')->with('user')->limit(1)->get();
                    foreach($lateTodayUsers as $lu) {
                        $insights[] = [
                            'name' => $lu->user->name,
                            'role' => $lu->user->position->name ?? 'Karyawan',
                            'type' => 'warning',
                            'insight' => 'Terlambat masuk jam ' . $lu->check_in_time . '. Pertimbangkan review ketepatan waktu berkala.',
                            'confidence' => 91,
                            'ago' => 'Baru saja'
                        ];
                    }
                    // Dynamic info leave
                    $leaveTodayUsers = \App\Models\LeaveRequest::where('start_date', '<=', date('Y-m-d'))->where('end_date', '>=', date('Y-m-d'))->where('status', 'approved')->with('user')->limit(1)->get();
                    foreach($leaveTodayUsers as $lu) {
                        $insights[] = [
                            'name' => $lu->user->name,
                            'role' => $lu->user->position->name ?? 'Karyawan',
                            'type' => 'info',
                            'insight' => 'Sedang cuti tahunan disetujui. Tugas didelegasikan sementara.',
                            'confidence' => 96,
                            'ago' => '10m lalu'
                        ];
                    }
                    // Success card fallback
                    $insights[] = [
                        'name' => 'Sistem Utama',
                        'role' => 'VALRYZE AI',
                        'type' => 'success',
                        'insight' => 'Kehadiran stabil di angka ' . $pct . '%. Tidak terdeteksi anomali absensi hari ini.',
                        'confidence' => 98,
                        'ago' => 'Baru saja'
                    ];
                @endphp

                <div class="p-3 space-y-2.5 overflow-y-auto">
                    @foreach($insights as $item)
                        @php
                            $bgMap = ['warning' => 'rgba(245,158,11,0.08)', 'info' => 'rgba(6,182,212,0.08)', 'success' => 'rgba(16,185,129,0.08)'];
                            $borderMap = ['warning' => '#F59E0B', 'info' => '#06B6D4', 'success' => '#10B981'];
                            $labelMap = ['warning' => 'Peringatan', 'info' => 'Info', 'success' => 'Bagus'];
                        @endphp
                        <div class="rounded-xl p-3.5" style="background: {{ $bgMap[$item['type']] }}; border-left: 2px solid {{ $borderMap[$item['type']] }};">
                            <div class="flex items-center justify-between mb-1.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-lg bg-slate-800 flex items-center justify-center shrink-0" style="border: 1px solid {{ $borderMap[$item['type']] }}50;">
                                        <span class="text-white" style="font-size: 8px; font-weight: bold;">AI</span>
                                    </div>
                                    <div>
                                        <div style="font-size: 11px; font-weight: 700; color: var(--t1);">{{ $item['name'] }}</div>
                                        <div style="font-size: 9px; color: var(--t3);">{{ $item['role'] }}</div>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded-full" style="font-size: 9px; font-weight: 700; color: {{ $borderMap[$item['type']] }}; background: rgba(255,255,255,0.05);">
                                    {{ $labelMap[$item['type']] }}
                                </span>
                            </div>
                            <p style="font-size: 10.5px; color: var(--t3); line-height: 1.5;">
                                {{ $item['insight'] }}
                            </p>
                            <div class="flex items-center justify-between mt-2 pt-2" style="border-top: 1px solid var(--border-soft); opacity:0.8;">
                                <div class="flex items-center gap-1">
                                    <span style="font-size: 9px; font-weight: 700; color: {{ $borderMap[$item['type']] }};">
                                        ⚡ AI Confidence: {{ $item['confidence'] }}%
                                    </span>
                                </div>
                                <span style="font-size: 9px; color: var(--t4);">
                                    Updated {{ $item['ago'] }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Announcements -->
            <div class="card p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 style="font-size: 0.95rem; font-weight: 700; color: var(--t1);">Pengumuman Kantor</h3>
                    <a href="{{ route('announcements.index') }}" style="font-size: 0.72rem; color: var(--em);">Semua</a>
                </div>
                <div class="space-y-2.5">
                    @forelse($announcements as $ann)
                        <div style="padding: 0.75rem; border-radius: 12px; background: var(--bg-hover); border: 1px solid var(--border-soft); border-left: 3px solid {{ match($ann->category) {'info'=>'#3b82f6','meeting'=>'#8b5cf6','holiday'=>'#10b981','activity'=>'#f59e0b',default=>'#64748b'} }};">
                            @if($ann->is_pinned)
                                <span style="font-size: 0.6rem; color: #fbbf24; font-weight: 700;">📌 PENTING</span>
                            @endif
                            <div style="font-size: 0.8rem; font-weight: 700; color: var(--t1); margin-top: 0.15rem;">{{ $ann->title }}</div>
                            <div style="font-size: 0.68rem; color: var(--t3); margin-top: 0.25rem;">{{ $ann->published_at?->format('d M Y') }}</div>
                        </div>
                    @empty
                        <p style="font-size: 0.8rem; color: var(--t3); text-align: center; padding: 1.5rem 0;">Belum ada pengumuman baru</p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

    {{-- System Status Section / Queue Warning (Moved to Bottom) --}}
    @if($queueStatus === 'offline' || $failedJobsCount > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
        @if($queueStatus === 'offline')
            <div class="alert alert-error">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <div style="font-size: 0.82rem; font-weight: 700;">Queue Worker Offline</div>
                    <div style="font-size: 0.72rem; opacity: 0.85; font-weight: 500;">Pekerja antrean mati. Notifikasi email akan tertunda.</div>
                </div>
            </div>
        @endif
        @if($failedJobsCount > 0)
            <div class="alert alert-warning">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <div style="font-size: 0.82rem; font-weight: 700;">{{ $failedJobsCount }} Pekerjaan Gagal</div>
                    <div style="font-size: 0.72rem; opacity: 0.85; font-weight: 500;">Ada beberapa pekerjaan antrean yang gagal diproses.</div>
                </div>
            </div>
        @endif
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
const weeklyData = @json($chartData);
const isDarkTheme = document.documentElement.classList.contains('dark');
const chartCtx = document.getElementById('attendanceChart').getContext('2d');

// Create gradients for smooth Area Chart fill
const presentGradient = chartCtx.createLinearGradient(0, 0, 0, 180);
presentGradient.addColorStop(0, 'rgba(6, 182, 212, 0.32)');
presentGradient.addColorStop(1, 'rgba(6, 182, 212, 0.0)');

const lateGradient = chartCtx.createLinearGradient(0, 0, 0, 180);
lateGradient.addColorStop(0, 'rgba(245, 158, 11, 0.24)');
lateGradient.addColorStop(1, 'rgba(245, 158, 11, 0.0)');

new Chart(chartCtx, {
    type: 'line',
    data: {
        labels: weeklyData.map(d => {
            const dateObj = new Date(d.date);
            return dateObj.toLocaleDateString('id-ID', { weekday: 'short' });
        }),
        datasets: [
            {
                label: 'Hadir',
                data: weeklyData.map(d => d.present),
                borderColor: '#06B6D4',
                backgroundColor: presentGradient,
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointBackgroundColor: '#06B6D4',
                pointBorderColor: '#071830',
                pointBorderWidth: 2,
                pointHoverRadius: 7,
                pointRadius: 4
            },
            {
                label: 'Terlambat',
                data: weeklyData.map(d => d.late),
                borderColor: '#F59E0B',
                backgroundColor: lateGradient,
                fill: true,
                tension: 0.4,
                borderWidth: 2,
                pointBackgroundColor: '#F59E0B',
                pointBorderColor: '#071830',
                pointBorderWidth: 1.5,
                pointHoverRadius: 6,
                pointRadius: 3
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                labels: {
                    color: isDarkTheme ? '#E2E8F0' : '#334155',
                    font: { size: 10, family: 'Plus Jakarta Sans', weight: '600' }
                }
            },
            tooltip: {
                backgroundColor: '#071830',
                titleColor: '#FFFFFF',
                bodyColor: '#CBD5E1',
                cornerRadius: 8,
                padding: 8
            }
        },
        scales: {
            x: {
                grid: { display: false },
                ticks: { color: isDarkTheme ? '#94A3B8' : '#64748B', font: { size: 10 } }
            },
            y: {
                grid: { color: isDarkTheme ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)' },
                ticks: { color: isDarkTheme ? '#94A3B8' : '#64748B', font: { size: 10 } },
                beginAtZero: true
            }
        }
    }
});
</script>
@endpush
