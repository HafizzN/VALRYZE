@extends('layouts.app')

@section('title', 'Dashboard Manager')
@section('page-title', 'Dashboard Manager')
@section('breadcrumb', 'Dashboard › Overview Tim')

@section('content')

{{-- Stat Cards --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="stat-card" style="border-left: 3px solid #10b981;">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div class="stat-label">Hadir Hari Ini</div>
                <div class="stat-value" style="color: #34d399;">{{ $presentToday }}</div>
            </div>
            <div style="width: 48px; height: 48px; background: rgba(16,185,129,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <svg style="width: 24px; height: 24px; color: #34d399;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
        </div>
        <div class="stat-change" style="color: #34d399;">Karyawan aktif bekerja</div>
    </div>

    <div class="stat-card" style="border-left: 3px solid #f59e0b;">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div class="stat-label">Cuti Pending</div>
                <div class="stat-value" style="color: #fbbf24;">{{ $pendingLeave }}</div>
            </div>
            <div style="width: 48px; height: 48px; background: rgba(245,158,11,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <svg style="width: 24px; height: 24px; color: #fbbf24;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
        </div>
        <div class="stat-change" style="color: #fbbf24;">Menunggu persetujuan</div>
    </div>

    <div class="stat-card" style="border-left: 3px solid #6366f1;">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div class="stat-label">Lembur Pending</div>
                <div class="stat-value" style="color: #a78bfa;">{{ $pendingOvertime }}</div>
            </div>
            <div style="width: 48px; height: 48px; background: rgba(99,102,241,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <svg style="width: 24px; height: 24px; color: #a78bfa;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <div class="stat-change" style="color: #a78bfa;">Perlu ditinjau</div>
    </div>

    <div class="stat-card" style="border-left: 3px solid #3b82f6;">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div class="stat-label">Total Pengumuman</div>
                <div class="stat-value" style="color: #60a5fa;">{{ $announcements->count() }}</div>
            </div>
            <div style="width: 48px; height: 48px; background: rgba(59,130,246,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <svg style="width: 24px; height: 24px; color: #60a5fa;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
            </div>
        </div>
        <div class="stat-change" style="color: #60a5fa;">Aktif bulan ini</div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    {{-- Quick Actions --}}
    <div class="card">
        <h3 style="font-size: 0.9rem; font-weight: 600; margin-bottom: 1rem;">Aksi Cepat</h3>
        <div class="grid grid-cols-2 gap-3">
            <a href="{{ route('leave.index') }}" style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem; padding: 1rem; background: rgba(245,158,11,0.08); border: 1px solid rgba(245,158,11,0.2); border-radius: 10px; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='rgba(245,158,11,0.15)'" onmouseout="this.style.background='rgba(245,158,11,0.08)'">
                <svg style="width: 24px; color: #fbbf24;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span style="font-size: 0.78rem; color: #fbbf24; font-weight: 500;">Approve Cuti</span>
                @if($pendingLeave > 0)<span class="badge badge-warning" style="font-size: 0.6rem;">{{ $pendingLeave }}</span>@endif
            </a>
            <a href="{{ route('overtime.index') }}" style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem; padding: 1rem; background: rgba(99,102,241,0.08); border: 1px solid rgba(99,102,241,0.2); border-radius: 10px; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='rgba(99,102,241,0.15)'" onmouseout="this.style.background='rgba(99,102,241,0.08)'">
                <svg style="width: 24px; color: #a78bfa;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span style="font-size: 0.78rem; color: #a78bfa; font-weight: 500;">Approve Lembur</span>
                @if($pendingOvertime > 0)<span class="badge badge-purple" style="font-size: 0.6rem;">{{ $pendingOvertime }}</span>@endif
            </a>
            <a href="{{ route('reports.attendance') }}" style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem; padding: 1rem; background: rgba(16,185,129,0.08); border: 1px solid rgba(16,185,129,0.2); border-radius: 10px; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='rgba(16,185,129,0.15)'" onmouseout="this.style.background='rgba(16,185,129,0.08)'">
                <svg style="width: 24px; color: #34d399;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span style="font-size: 0.78rem; color: #34d399; font-weight: 500;">Laporan</span>
            </a>
            <a href="{{ route('announcements.create') }}" style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem; padding: 1rem; background: rgba(59,130,246,0.08); border: 1px solid rgba(59,130,246,0.2); border-radius: 10px; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='rgba(59,130,246,0.15)'" onmouseout="this.style.background='rgba(59,130,246,0.08)'">
                <svg style="width: 24px; color: #60a5fa;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                <span style="font-size: 0.78rem; color: #60a5fa; font-weight: 500;">Buat Pengumuman</span>
            </a>
        </div>
    </div>

    {{-- Announcements --}}
    <div class="card">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
            <h3 style="font-size: 0.9rem; font-weight: 600; color: var(--t1);">Pengumuman Aktif</h3>
            <a href="{{ route('announcements.index') }}" style="font-size: 0.75rem; color: var(--em);">Lihat semua →</a>
        </div>
        <div style="display: flex; flex-direction: column; gap: 0.6rem; max-height: 280px; overflow-y: auto;">
            @forelse($announcements as $ann)
            <a href="{{ route('announcements.show', $ann) }}" style="display: block; padding: 0.65rem 0.85rem; background: var(--bg-base); border: 1px solid var(--border-soft); border-radius: 8px; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='var(--bg-hover)'" onmouseout="this.style.background='var(--bg-base)'">
                <div style="font-size: 0.8rem; font-weight: 700; color: var(--t1);">{{ Str::limit($ann->title, 55) }}</div>
                <div style="font-size: 0.68rem; color: var(--t3); margin-top: 0.15rem;">{{ $ann->created_at->diffForHumans() }}</div>
            </a>
            @empty
            <div style="text-align: center; color: var(--t3); padding: 2rem; font-size: 0.82rem;">Belum ada pengumuman</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
