@extends('layouts.app')

@section('title', 'Dashboard Manager')
@section('page-title', 'Dashboard Manager')
@section('breadcrumb', 'Dashboard › Overview Tim')

@push('styles')
<style>
    .mgr-hero {
        position: relative; overflow: hidden;
        background: linear-gradient(135deg, #071830 0%, #0F2845 55%, #0A2040 100%);
        border: 1px solid rgba(6,182,212,0.18);
        border-radius: 20px; padding: 1.75rem 2rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 8px 32px rgba(0,0,0,0.25);
    }
    .mgr-hero::before {
        content:''; position:absolute; top:-60px; right:-40px;
        width:260px; height:260px; border-radius:50%;
        background: radial-gradient(circle, rgba(6,182,212,0.1) 0%, transparent 70%);
        pointer-events:none;
    }
    .mgr-hero::after {
        content:''; position:absolute; bottom:-50px; left:15%;
        width:200px; height:200px; border-radius:50%;
        background: radial-gradient(circle, rgba(99,102,241,0.07) 0%, transparent 70%);
        pointer-events:none;
    }

    .kpi-card {
        background: var(--bg-card);
        border: 1px solid var(--border-soft);
        border-radius: 16px; padding: 1.25rem 1.5rem;
        box-shadow: var(--shadow-card);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative; overflow: hidden;
    }
    .kpi-card::after {
        content:''; position:absolute; top:0; left:0; right:0; height:2px;
        background: linear-gradient(90deg, var(--kpi-accent, var(--em)), transparent);
        border-radius: 16px 16px 0 0;
    }
    .kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-card), 0 16px 32px rgba(0,0,0,0.1);
        border-color: var(--kpi-accent, var(--em-border));
    }

    .action-tile {
        display: flex; flex-direction: column; align-items: center;
        gap: 0.6rem; padding: 1.25rem 1rem;
        border-radius: 14px; text-decoration: none;
        transition: all 0.2s cubic-bezier(0.16,1,0.3,1);
        position: relative;
    }
    .action-tile:hover {
        transform: translateY(-3px);
    }
    .action-tile-icon {
        width: 44px; height: 44px; border-radius: 13px;
        display: flex; align-items: center; justify-content: center;
    }
    .action-tile-label {
        font-size: 0.78rem; font-weight: 600; text-align: center; line-height: 1.3;
    }
    .action-tile-badge {
        position: absolute; top: 8px; right: 8px;
        background: var(--danger); color: #fff;
        font-size: 0.58rem; font-weight: 800;
        padding: 0.12rem 0.38rem; border-radius: 99px; min-width: 18px; text-align: center;
        box-shadow: 0 0 8px rgba(239,68,68,0.5);
    }
</style>
@endpush

@section('content')
@php
    $h = date('H');
    $greeting = $h < 11 ? 'Selamat Pagi' : ($h < 15 ? 'Selamat Siang' : ($h < 18 ? 'Selamat Sore' : 'Selamat Malam'));
    $user = auth()->user();
    $totalPending = $pendingLeave + $pendingOvertime;
@endphp

{{-- ━━━━━━━━━━━━━━━━━━━━━━ HERO ━━━━━━━━━━━━━━━━━━━━━━ --}}
<div class="mgr-hero">
    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-5">
        <div>
            <p style="font-size:0.62rem;font-weight:700;letter-spacing:0.15em;text-transform:uppercase;color:rgba(6,182,212,0.7);margin-bottom:0.35rem;">
                {{ now()->translatedFormat('l, d F Y') }}
            </p>
            <h1 style="font-size:1.5rem;font-weight:800;color:#F1F5F9;letter-spacing:-0.02em;line-height:1.2;">
                {{ $greeting }}, <span style="color:#38BDF8;">{{ explode(' ', $user->name)[0] }}</span> 👋
            </h1>
            <p style="font-size:0.8rem;color:#64748B;margin-top:0.3rem;">
                Manager · {{ $user->division?->name ?? 'VALRYZE' }}
            </p>
            <div class="flex flex-wrap items-center gap-2 mt-3">
                @if($totalPending > 0)
                <span class="flex items-center gap-1.5 px-2.5 py-1 rounded-full"
                      style="background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.25);color:#FCA5A5;font-size:0.72rem;font-weight:700;">
                    <span style="width:6px;height:6px;border-radius:50%;background:#EF4444;box-shadow:0 0 6px #EF4444;animation:pulse 2s infinite;"></span>
                    {{ $totalPending }} Pengajuan Menunggu
                </span>
                @else
                <span class="flex items-center gap-1.5 px-2.5 py-1 rounded-full"
                      style="background:rgba(16,185,129,0.12);border:1px solid rgba(16,185,129,0.25);color:#34D399;font-size:0.72rem;font-weight:700;">
                    <span style="width:6px;height:6px;border-radius:50%;background:#10B981;box-shadow:0 0 6px #10B981;animation:pulse 2s infinite;"></span>
                    Semua Terproses
                </span>
                @endif
                <span style="font-size:0.72rem;font-weight:600;color:#64748B;padding:0.35rem 0.85rem;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.06);border-radius:99px;">
                    {{ $presentToday }} anggota hadir hari ini
                </span>
            </div>
        </div>
        {{-- Ring indicator total pending --}}
        @if($totalPending > 0)
        <div class="shrink-0 flex items-center gap-3 px-5 py-4 rounded-2xl"
             style="background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.2);">
            <div style="text-align:center;">
                <div style="font-size:2.5rem;font-weight:800;color:#FCA5A5;line-height:1;letter-spacing:-0.04em;">{{ $totalPending }}</div>
                <div style="font-size:0.68rem;color:#94A3B8;margin-top:0.2rem;">Perlu ditindaklanjuti</div>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- ━━━━━━━━━━━━━━━━━━━━━━ KPI CARDS ━━━━━━━━━━━━━━━━━━━━━━ --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="kpi-card" style="--kpi-accent:#10B981;">
        <div style="width:36px;height:36px;border-radius:10px;background:rgba(16,185,129,0.12);display:flex;align-items:center;justify-content:center;margin-bottom:0.75rem;">
            <svg style="width:18px;height:18px;color:#34D399;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <div style="font-size:2rem;font-weight:800;color:#34D399;line-height:1;letter-spacing:-0.04em;">{{ $presentToday }}</div>
        <div style="font-size:0.7rem;color:var(--t4);margin-top:0.25rem;font-weight:600;">Hadir Hari Ini</div>
        <div style="font-size:0.65rem;color:var(--t5);margin-top:0.1rem;">Anggota tim aktif</div>
    </div>

    <div class="kpi-card" style="--kpi-accent:#F59E0B;">
        <div style="width:36px;height:36px;border-radius:10px;background:rgba(245,158,11,0.12);display:flex;align-items:center;justify-content:center;margin-bottom:0.75rem;">
            <svg style="width:18px;height:18px;color:#FCD34D;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        <div style="font-size:2rem;font-weight:800;color:#FCD34D;line-height:1;letter-spacing:-0.04em;">{{ $pendingLeave }}</div>
        <div style="font-size:0.7rem;color:var(--t4);margin-top:0.25rem;font-weight:600;">Cuti Pending</div>
        <div style="font-size:0.65rem;color:var(--t5);margin-top:0.1rem;">Menunggu persetujuan</div>
    </div>

    <div class="kpi-card" style="--kpi-accent:#6366F1;">
        <div style="width:36px;height:36px;border-radius:10px;background:rgba(99,102,241,0.12);display:flex;align-items:center;justify-content:center;margin-bottom:0.75rem;">
            <svg style="width:18px;height:18px;color:#A78BFA;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div style="font-size:2rem;font-weight:800;color:#A78BFA;line-height:1;letter-spacing:-0.04em;">{{ $pendingOvertime }}</div>
        <div style="font-size:0.7rem;color:var(--t4);margin-top:0.25rem;font-weight:600;">Lembur Pending</div>
        <div style="font-size:0.65rem;color:var(--t5);margin-top:0.1rem;">Perlu ditinjau</div>
    </div>

    <div class="kpi-card" style="--kpi-accent:#3B82F6;">
        <div style="width:36px;height:36px;border-radius:10px;background:rgba(59,130,246,0.12);display:flex;align-items:center;justify-content:center;margin-bottom:0.75rem;">
            <svg style="width:18px;height:18px;color:#60A5FA;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
            </svg>
        </div>
        <div style="font-size:2rem;font-weight:800;color:#60A5FA;line-height:1;letter-spacing:-0.04em;">{{ $announcements->count() }}</div>
        <div style="font-size:0.7rem;color:var(--t4);margin-top:0.25rem;font-weight:600;">Pengumuman Aktif</div>
        <div style="font-size:0.65rem;color:var(--t5);margin-top:0.1rem;">Bulan ini</div>
    </div>
</div>

{{-- ━━━━━━━━━━━━━━━━━━━━━━ LOWER ROW ━━━━━━━━━━━━━━━━━━━━━━ --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- Quick Actions --}}
    <div class="card">
        <h3 style="font-size:0.9rem;font-weight:700;color:var(--t1);margin-bottom:1.25rem;">Aksi Cepat</h3>
        <div class="grid grid-cols-2 gap-3">
            {{-- Cuti --}}
            <a href="{{ route('leave.index') }}" class="action-tile"
               style="background:rgba(245,158,11,0.07);border:1px solid rgba(245,158,11,0.18);"
               onmouseover="this.style.background='rgba(245,158,11,0.14)'; this.style.borderColor='rgba(245,158,11,0.35)';"
               onmouseout="this.style.background='rgba(245,158,11,0.07)'; this.style.borderColor='rgba(245,158,11,0.18)';">
                @if($pendingLeave > 0)
                <span class="action-tile-badge">{{ $pendingLeave }}</span>
                @endif
                <div class="action-tile-icon" style="background:rgba(245,158,11,0.15);">
                    <svg style="width:22px;height:22px;color:#FCD34D;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <span class="action-tile-label" style="color:#FCD34D;">Approve Cuti</span>
            </a>

            {{-- Lembur --}}
            <a href="{{ route('overtime.index') }}" class="action-tile"
               style="background:rgba(99,102,241,0.07);border:1px solid rgba(99,102,241,0.18);"
               onmouseover="this.style.background='rgba(99,102,241,0.14)'; this.style.borderColor='rgba(99,102,241,0.35)';"
               onmouseout="this.style.background='rgba(99,102,241,0.07)'; this.style.borderColor='rgba(99,102,241,0.18)';">
                @if($pendingOvertime > 0)
                <span class="action-tile-badge">{{ $pendingOvertime }}</span>
                @endif
                <div class="action-tile-icon" style="background:rgba(99,102,241,0.15);">
                    <svg style="width:22px;height:22px;color:#A78BFA;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="action-tile-label" style="color:#A78BFA;">Approve Lembur</span>
            </a>

            {{-- Laporan --}}
            <a href="{{ route('reports.attendance') }}" class="action-tile"
               style="background:rgba(16,185,129,0.07);border:1px solid rgba(16,185,129,0.18);"
               onmouseover="this.style.background='rgba(16,185,129,0.14)'; this.style.borderColor='rgba(16,185,129,0.35)';"
               onmouseout="this.style.background='rgba(16,185,129,0.07)'; this.style.borderColor='rgba(16,185,129,0.18)';">
                <div class="action-tile-icon" style="background:rgba(16,185,129,0.15);">
                    <svg style="width:22px;height:22px;color:#34D399;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <span class="action-tile-label" style="color:#34D399;">Laporan Absensi</span>
            </a>

            {{-- Pengumuman --}}
            <a href="{{ route('announcements.create') }}" class="action-tile"
               style="background:rgba(59,130,246,0.07);border:1px solid rgba(59,130,246,0.18);"
               onmouseover="this.style.background='rgba(59,130,246,0.14)'; this.style.borderColor='rgba(59,130,246,0.35)';"
               onmouseout="this.style.background='rgba(59,130,246,0.07)'; this.style.borderColor='rgba(59,130,246,0.18)';">
                <div class="action-tile-icon" style="background:rgba(59,130,246,0.15);">
                    <svg style="width:22px;height:22px;color:#60A5FA;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                    </svg>
                </div>
                <span class="action-tile-label" style="color:#60A5FA;">Buat Pengumuman</span>
            </a>
        </div>
    </div>

    {{-- Announcements --}}
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h3 style="font-size:0.9rem;font-weight:700;color:var(--t1);">Pengumuman Aktif</h3>
            <a href="{{ route('announcements.index') }}"
               style="font-size:0.72rem;color:var(--em);font-weight:600;text-decoration:none;display:flex;align-items:center;gap:0.25rem;"
               onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                Lihat Semua
                <svg style="width:12px;height:12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div style="display:flex;flex-direction:column;gap:0.6rem;max-height:280px;overflow-y:auto;">
            @forelse($announcements as $ann)
            <a href="{{ route('announcements.show', $ann) }}"
               style="display:block;padding:0.75rem 0.9rem;background:var(--bg-elevated);border:1px solid var(--border-soft);border-radius:12px;text-decoration:none;transition:all 0.2s ease;"
               onmouseover="this.style.background='var(--bg-hover)';this.style.borderColor='var(--em-border)';this.style.transform='translateX(3px)';"
               onmouseout="this.style.background='var(--bg-elevated)';this.style.borderColor='var(--border-soft)';this.style.transform='translateX(0)';">
                <div class="flex items-center justify-between mb-1">
                    <span class="badge badge-{{ $ann->category_color ?? 'info' }}" style="font-size:0.62rem;">
                        {{ $ann->category_label ?? $ann->category }}
                    </span>
                    <span style="font-size:0.65rem;color:var(--t4);">{{ $ann->created_at->diffForHumans() }}</span>
                </div>
                <div style="font-size:0.82rem;font-weight:700;color:var(--t1);">{{ Str::limit($ann->title, 55) }}</div>
                <div style="font-size:0.7rem;color:var(--t3);margin-top:0.15rem;">{{ Str::limit(strip_tags($ann->content), 70) }}</div>
            </a>
            @empty
            <div style="text-align:center;color:var(--t4);padding:2.5rem;font-size:0.82rem;">
                <div style="font-size:1.5rem;margin-bottom:0.5rem;">📢</div>
                Belum ada pengumuman
            </div>
            @endforelse
        </div>
    </div>
</div>

@endsection
