@extends('layouts.app')

@section('title', 'Dashboard Karyawan')
@section('page-title', 'Dashboard')
@section('breadcrumb', 'Dashboard › Ringkasan Hari Ini')

@push('styles')
<style>
    /* Karyawan Dashboard Styles */
    .emp-hero {
        position: relative; overflow: hidden;
        background: linear-gradient(135deg, #071830 0%, #0D2A4A 50%, #0A2240 100%);
        border: 1px solid rgba(6,182,212,0.18);
        border-radius: 20px; padding: 1.75rem 2rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 8px 32px rgba(0,0,0,0.25);
    }
    .emp-hero::before {
        content: ''; position: absolute;
        top: -60px; right: -40px; width: 240px; height: 240px; border-radius: 50%;
        background: radial-gradient(circle, rgba(6,182,212,0.12) 0%, transparent 70%);
        pointer-events: none;
    }
    .emp-hero::after {
        content: ''; position: absolute;
        bottom: -40px; left: 20%; width: 180px; height: 180px; border-radius: 50%;
        background: radial-gradient(circle, rgba(6,182,212,0.06) 0%, transparent 70%);
        pointer-events: none;
    }

    .emp-stat-tile {
        background: var(--bg-card);
        border: 1px solid var(--border-soft);
        border-radius: 16px; padding: 1.25rem 1.5rem;
        box-shadow: var(--shadow-card);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative; overflow: hidden;
    }
    .emp-stat-tile::after {
        content: ''; position: absolute;
        top: 0; left: 0; right: 0; height: 2px;
        background: linear-gradient(90deg, var(--tile-accent, var(--em)), transparent);
        border-radius: 16px 16px 0 0;
    }
    .emp-stat-tile:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-card), 0 12px 24px rgba(0,0,0,0.12);
        border-color: var(--tile-accent, var(--em-border));
    }

    .attendance-action-card {
        background: var(--bg-card);
        border: 1px solid var(--border-soft);
        border-radius: 20px; padding: 1.5rem;
        box-shadow: var(--shadow-card);
        transition: all 0.25s ease;
    }
    .attendance-action-card.checked-in {
        border-color: rgba(16,185,129,0.25);
        background: linear-gradient(135deg, var(--bg-card) 80%, rgba(16,185,129,0.04) 100%);
    }
    .attendance-action-card.not-checked-in {
        border-color: rgba(239,68,68,0.2);
        background: linear-gradient(135deg, var(--bg-card) 80%, rgba(239,68,68,0.03) 100%);
    }

    .progress-track {
        background: var(--border-soft);
        border-radius: 99px; height: 8px; overflow: hidden;
    }
    .progress-fill {
        height: 100%;
        border-radius: 99px;
        transition: width 1s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .ann-item {
        padding: 0.85rem 1rem;
        background: var(--bg-elevated);
        border: 1px solid var(--border-soft);
        border-radius: 12px;
        text-decoration: none;
        display: block;
        transition: all 0.2s ease;
    }
    .ann-item:hover {
        background: var(--bg-hover);
        border-color: var(--em-border);
        transform: translateX(3px);
    }

    .time-pill {
        display: flex; align-items: center; gap: 0.4rem;
        padding: 0.35rem 0.85rem;
        background: var(--bg-elevated);
        border: 1px solid var(--border-soft);
        border-radius: 99px;
        font-size: 0.78rem; font-weight: 700;
        color: var(--t2);
    }

    @keyframes countUp {
        from { opacity: 0; transform: translateY(8px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .stat-value-anim {
        animation: countUp 0.5s ease forwards;
    }
</style>
@endpush

@section('content')
@php
    $h = date('H');
    $greeting = $h < 11 ? 'Selamat Pagi' : ($h < 15 ? 'Selamat Siang' : ($h < 18 ? 'Selamat Sore' : 'Selamat Malam'));
    $user = auth()->user();
    $leaveQuota = $user->annual_leave_quota ?? 12;
    $leaveUsed = $user->annual_leave_used ?? 0;
    $leaveRemaining = max(0, $leaveQuota - $leaveUsed);
    $leavePct = $leaveQuota > 0 ? round(($leaveUsed / $leaveQuota) * 100) : 0;
    $onTimePct = ($monthPresent > 0) ? max(0, round((($monthPresent - $monthLate) / $monthPresent) * 100)) : 100;
@endphp

{{-- ━━━━━━━━━━━━━━━━━━━━━━ HERO SECTION ━━━━━━━━━━━━━━━━━━━━━━ --}}
<div class="emp-hero">
    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        {{-- Left: Greeting --}}
        <div>
            <p style="font-size: 0.62rem; font-weight: 700; letter-spacing: 0.15em; text-transform: uppercase; color: rgba(6,182,212,0.7); margin-bottom: 0.35rem;">
                {{ now()->translatedFormat('l, d F Y') }}
            </p>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: #F1F5F9; letter-spacing: -0.02em; line-height: 1.2;">
                {{ $greeting }}, <span style="color: #38BDF8;">{{ explode(' ', $user->name)[0] }}</span> 👋
            </h1>
            <p style="font-size: 0.8rem; color: #64748B; margin-top: 0.35rem;">
                {{ $user->position?->name ?? 'Karyawan' }} · {{ $user->division?->name ?? 'VALRYZE' }}
            </p>

            {{-- Status Chips --}}
            <div class="flex flex-wrap items-center gap-2 mt-3">
                @if($todayAttendance)
                    <span class="flex items-center gap-1.5 px-2.5 py-1 rounded-full"
                          style="background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.3); color: #34D399; font-size: 0.72rem; font-weight: 700;">
                        <span style="width: 6px; height: 6px; border-radius: 50%; background: #10B981; box-shadow: 0 0 6px #10B981; animation: pulse 2s infinite;"></span>
                        Hadir · {{ $todayAttendance->check_in_time ? \Carbon\Carbon::parse($todayAttendance->check_in_time)->format('H:i') : '-' }} WIB
                    </span>
                    @if($todayAttendance->status === 'late')
                        <span class="badge badge-warning">⏰ Terlambat {{ $todayAttendance->late_minutes }} mnt</span>
                    @endif
                    @if($todayAttendance->check_out_time)
                        <span class="badge badge-success">✓ Sudah Pulang</span>
                    @endif
                @else
                    <span class="flex items-center gap-1.5 px-2.5 py-1 rounded-full"
                          style="background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.25); color: #FCA5A5; font-size: 0.72rem; font-weight: 700;">
                        <span style="width: 6px; height: 6px; border-radius: 50%; background: #EF4444;"></span>
                        Belum Absen
                    </span>
                @endif
                @if($user->shift)
                    <span class="time-pill">
                        <svg style="width:12px;height:12px;color:#94A3B8;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Shift {{ \Carbon\Carbon::parse($user->shift->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($user->shift->end_time)->format('H:i') }}
                    </span>
                @endif
            </div>
        </div>

        {{-- Right: Quick Action --}}
        <div class="shrink-0">
            @if(!$todayAttendance)
                <a href="{{ route('attendance.check-in') }}"
                   class="btn btn-primary"
                   style="padding: 0.75rem 1.75rem; font-size: 0.88rem; border-radius: 14px; box-shadow: 0 8px 24px rgba(6,182,212,0.3);">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14"/>
                    </svg>
                    Absen Masuk Sekarang
                </a>
            @elseif(!$todayAttendance->check_out_time)
                <a href="{{ route('attendance.check-out') }}"
                   class="btn"
                   style="padding: 0.75rem 1.75rem; font-size: 0.88rem; border-radius: 14px; background: linear-gradient(135deg,#F59E0B,#D97706); color:#fff; box-shadow: 0 8px 24px rgba(245,158,11,0.25);">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7"/>
                    </svg>
                    Absen Pulang
                </a>
            @else
                <div class="flex items-center gap-2 px-4 py-3 rounded-2xl"
                     style="background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.2);">
                    <svg class="w-5 h-5" style="color:#34D399;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span style="font-size: 0.85rem; font-weight: 700; color: #34D399;">Hari ini selesai ✓</span>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- ━━━━━━━━━━━━━━━━━━━━━━ STATS TILES ━━━━━━━━━━━━━━━━━━━━━━ --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">

    {{-- Tile 1: Hadir Bulan Ini --}}
    <div class="emp-stat-tile" style="--tile-accent: #10B981;">
        <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(16,185,129,0.12); display:flex; align-items:center; justify-content:center; margin-bottom: 0.75rem;">
            <svg style="width:18px;height:18px;color:#34D399;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="stat-value-anim" style="font-size: 2rem; font-weight: 800; color: #34D399; line-height: 1; letter-spacing: -0.04em;">{{ $monthPresent }}</div>
        <div style="font-size: 0.7rem; color: var(--t4); margin-top: 0.25rem; font-weight: 600;">Hari Hadir</div>
        <div style="font-size: 0.65rem; color: var(--t5); margin-top: 0.1rem;">{{ now()->translatedFormat('F Y') }}</div>
    </div>

    {{-- Tile 2: Terlambat Bulan Ini --}}
    <div class="emp-stat-tile" style="--tile-accent: #F59E0B;">
        <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(245,158,11,0.12); display:flex; align-items:center; justify-content:center; margin-bottom: 0.75rem;">
            <svg style="width:18px;height:18px;color:#FCD34D;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="stat-value-anim" style="font-size: 2rem; font-weight: 800; color: #FCD34D; line-height: 1; letter-spacing: -0.04em;">{{ $monthLate }}</div>
        <div style="font-size: 0.7rem; color: var(--t4); margin-top: 0.25rem; font-weight: 600;">Hari Terlambat</div>
        <div style="font-size: 0.65rem; color: var(--t5); margin-top: 0.1rem;">{{ now()->translatedFormat('F Y') }}</div>
    </div>

    {{-- Tile 3: Tepat Waktu % --}}
    <div class="emp-stat-tile" style="--tile-accent: #06B6D4;">
        <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(6,182,212,0.12); display:flex; align-items:center; justify-content:center; margin-bottom: 0.75rem;">
            <svg style="width:18px;height:18px;color:#38BDF8;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="stat-value-anim" style="font-size: 2rem; font-weight: 800; color: #38BDF8; line-height: 1; letter-spacing: -0.04em;">{{ $onTimePct }}%</div>
        <div style="font-size: 0.7rem; color: var(--t4); margin-top: 0.25rem; font-weight: 600;">Tepat Waktu</div>
        <div style="font-size: 0.65rem; color: var(--t5); margin-top: 0.1rem;">Bulan ini</div>
    </div>

    {{-- Tile 4: Sisa Cuti --}}
    <div class="emp-stat-tile" style="--tile-accent: #A78BFA;">
        <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(167,139,250,0.12); display:flex; align-items:center; justify-content:center; margin-bottom: 0.75rem;">
            <svg style="width:18px;height:18px;color:#A78BFA;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        <div class="stat-value-anim" style="font-size: 2rem; font-weight: 800; color: #A78BFA; line-height: 1; letter-spacing: -0.04em;">{{ $leaveRemaining }}</div>
        <div style="font-size: 0.7rem; color: var(--t4); margin-top: 0.25rem; font-weight: 600;">Sisa Cuti</div>
        <div style="font-size: 0.65rem; color: var(--t5); margin-top: 0.1rem;">dari {{ $leaveQuota }} hari jatah</div>
    </div>
</div>

{{-- ━━━━━━━━━━━━━━━━━━━━━━ LEAVE PROGRESS BAR ━━━━━━━━━━━━━━━━━━━━━━ --}}
<div class="card mb-6">
    <div class="flex items-center justify-between mb-3">
        <div>
            <h3 style="font-size: 0.85rem; font-weight: 700; color: var(--t1);">Progres Kuota Cuti Tahunan</h3>
            <p style="font-size: 0.7rem; color: var(--t4); margin-top: 0.1rem;">{{ $leaveUsed }} hari terpakai dari {{ $leaveQuota }} hari</p>
        </div>
        <a href="{{ route('leave.create') }}" class="btn btn-ghost btn-sm">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Ajukan Cuti
        </a>
    </div>
    <div class="progress-track">
        <div class="progress-fill" style="width: {{ $leavePct }}%; background: {{ $leavePct > 80 ? 'linear-gradient(90deg,#EF4444,#DC2626)' : 'linear-gradient(90deg,#6366F1,#A78BFA)' }};"></div>
    </div>
    <div class="flex justify-between mt-2" style="font-size: 0.68rem; color: var(--t4);">
        <span>{{ $leavePct }}% terpakai</span>
        <span>{{ $leaveRemaining }} hari tersisa</span>
    </div>
</div>

{{-- ━━━━━━━━━━━━━━━━━━━━━━ LOWER ROW ━━━━━━━━━━━━━━━━━━━━━━ --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- Recent Attendance --}}
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h3 style="font-size: 0.9rem; font-weight: 700; color: var(--t1);">Riwayat Absensi Terakhir</h3>
            <a href="{{ route('attendance.history') }}"
               style="font-size: 0.72rem; color: var(--em); font-weight: 600; text-decoration: none; display:flex; align-items:center; gap:0.25rem;"
               onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                Lihat Semua
                <svg style="width:12px;height:12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Masuk</th>
                        <th>Pulang</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentAttendances ?? [] as $att)
                    <tr>
                        <td style="font-size: 0.78rem; font-weight: 600;">{{ \Carbon\Carbon::parse($att->date)->translatedFormat('d M') }}</td>
                        <td style="font-family: 'JetBrains Mono', monospace; font-size: 0.78rem; color: var(--em);">
                            {{ $att->check_in_time ? \Carbon\Carbon::parse($att->check_in_time)->format('H:i') : '–' }}
                        </td>
                        <td style="font-family: 'JetBrains Mono', monospace; font-size: 0.78rem; color: var(--t3);">
                            {{ $att->check_out_time ? \Carbon\Carbon::parse($att->check_out_time)->format('H:i') : '–' }}
                        </td>
                        <td>
                            @if($att->status === 'present')
                                <span class="badge badge-success">Hadir</span>
                            @elseif($att->status === 'late')
                                <span class="badge badge-warning">Terlambat</span>
                            @elseif($att->status === 'absent')
                                <span class="badge badge-danger">Absen</span>
                            @else
                                <span class="badge badge-gray">{{ $att->status }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: var(--t4); padding: 2.5rem; font-size: 0.82rem;">
                            <div style="margin-bottom: 0.5rem; font-size: 1.5rem;">📋</div>
                            Belum ada data absensi
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Announcements --}}
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h3 style="font-size: 0.9rem; font-weight: 700; color: var(--t1);">Pengumuman Terbaru</h3>
            <a href="{{ route('announcements.index') }}"
               style="font-size: 0.72rem; color: var(--em); font-weight: 600; text-decoration: none; display:flex; align-items:center; gap:0.25rem;"
               onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                Lihat Semua
                <svg style="width:12px;height:12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div style="display: flex; flex-direction: column; gap: 0.6rem;">
            @forelse($announcements as $ann)
            <a href="{{ route('announcements.show', $ann) }}" class="ann-item">
                <div class="flex items-center justify-between mb-1">
                    <span class="badge badge-{{ $ann->category_color ?? 'info' }}" style="font-size: 0.62rem;">
                        {{ $ann->category_label ?? $ann->category }}
                    </span>
                    <span style="font-size: 0.65rem; color: var(--t4);">{{ $ann->created_at->diffForHumans() }}</span>
                </div>
                <div style="font-size: 0.82rem; font-weight: 700; color: var(--t1);">{{ Str::limit($ann->title, 55) }}</div>
                <div style="font-size: 0.72rem; color: var(--t3); margin-top: 0.2rem;">{{ Str::limit(strip_tags($ann->content), 75) }}</div>
            </a>
            @empty
            <div style="text-align:center; color: var(--t4); padding: 2.5rem; font-size: 0.82rem;">
                <div style="margin-bottom: 0.5rem; font-size: 1.5rem;">📢</div>
                Belum ada pengumuman
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- ━━━━━━━━━━━━━━━━━━━━━━ PENDING REQUESTS ━━━━━━━━━━━━━━━━━━━━━━ --}}
@if(isset($pendingRequests) && $pendingRequests->count() > 0)
<div class="card mt-6" style="border-color: rgba(245,158,11,0.2); background: linear-gradient(135deg, var(--bg-card) 90%, rgba(245,158,11,0.02) 100%);">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <div style="width:8px;height:8px;border-radius:50%;background:#F59E0B;box-shadow:0 0 8px #F59E0B;animation:pulse 2s infinite;"></div>
            <h3 style="font-size: 0.9rem; font-weight: 700; color: var(--t1);">Pengajuan Menunggu Persetujuan</h3>
        </div>
        <span class="badge badge-warning">{{ $pendingRequests->count() }} Pending</span>
    </div>
    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
        @foreach($pendingRequests as $req)
        <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.85rem 1rem; background: rgba(245,158,11,0.05); border: 1px solid rgba(245,158,11,0.15); border-radius: 10px; border-left: 3px solid #F59E0B;">
            <div>
                <div style="font-size: 0.82rem; font-weight: 600; color: var(--t1);">{{ $req->type_label ?? $req->type }}</div>
                <div style="font-size: 0.7rem; color: var(--t4); margin-top: 0.15rem;">Diajukan {{ $req->created_at->diffForHumans() }}</div>
            </div>
            <span class="badge badge-warning">⏳ Menunggu</span>
        </div>
        @endforeach
    </div>
</div>
@endif

@endsection
