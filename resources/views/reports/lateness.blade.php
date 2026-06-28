@extends('layouts.app')

@section('title', 'Laporan Keterlambatan')
@section('page-title', 'Laporan Keterlambatan')
@section('breadcrumb', 'Laporan › Keterlambatan')

@push('styles')
<style>
    .late-kpi {
        background: var(--bg-card);
        border: 1px solid var(--border-soft);
        border-radius: 16px; padding: 1.2rem 1.4rem;
        box-shadow: var(--shadow-card);
        transition: all 0.2s ease;
        position: relative; overflow: hidden;
    }
    .late-kpi::after {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
        background: linear-gradient(90deg, var(--kpi-c, #F59E0B), transparent);
    }
    .late-kpi:hover { transform: translateY(-2px); }
    .late-kpi-val {
        font-family: 'JetBrains Mono', monospace;
        font-size: 2rem; font-weight: 800; line-height: 1; letter-spacing: -0.04em;
    }
    .late-kpi-label {
        font-size: 0.68rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.07em; color: var(--t4); margin-top: 0.3rem;
    }
    .late-bar-track {
        height: 6px; background: var(--bg-elevated);
        border-radius: 99px; overflow: hidden; margin-top: 0.5rem;
    }
    .late-bar-fill {
        height: 100%; border-radius: 99px;
        background: linear-gradient(90deg, #F59E0B, #FCD34D);
        transition: width 0.8s ease;
    }
    .rank-badge {
        width: 24px; height: 24px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.65rem; font-weight: 800; flex-shrink: 0;
    }
</style>
@endpush

@section('content')
@php
    $totalLateCount   = $lateRecords->total();
    $totalLateMinutes = $lateRecords->sum('late_minutes');
    $maxLateRecord    = $lateRecords->max('late_minutes') ?? 0;
    $avgLate = $totalLateCount > 0 ? round($totalLateMinutes / $totalLateCount) : 0;
@endphp

{{-- Header --}}
<div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;">
    <div>
        <h2 style="font-size:1.1rem;font-weight:800;color:var(--t1);letter-spacing:-0.01em;">Analisis Keterlambatan Karyawan</h2>
        <p style="font-size:0.78rem;color:var(--t4);margin-top:0.25rem;">
            Periode: <strong style="color:var(--t2);">{{ \Carbon\Carbon::parse($month)->translatedFormat('F Y') }}</strong>
        </p>
    </div>
</div>

{{-- Filter --}}
<div class="card mb-5">
    <form method="GET" action="{{ route('reports.lateness') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="form-group" style="margin-bottom:0;">
            <label class="form-label" for="month">Pilih Bulan Laporan</label>
            <input type="month" name="month" id="month" class="form-control" value="{{ $month }}">
        </div>
        <div style="display:flex;align-items:flex-end;gap:0.5rem;">
            <button type="submit" class="btn btn-primary flex-1">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Tampilkan
            </button>
            @if(request()->filled('month'))
            <a href="{{ route('reports.lateness') }}" class="btn btn-secondary">Reset</a>
            @endif
        </div>
        <div></div>
    </form>
</div>

{{-- KPI Cards --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-5">
    <div class="late-kpi" style="--kpi-c:#F59E0B;">
        <div class="late-kpi-val" style="color:#FCD34D;">{{ $totalLateCount }}</div>
        <div class="late-kpi-label">Total Kejadian</div>
    </div>
    <div class="late-kpi" style="--kpi-c:#EF4444;">
        <div class="late-kpi-val" style="color:#FCA5A5;">{{ $totalLateMinutes }}</div>
        <div class="late-kpi-label">Total Menit Terlambat</div>
    </div>
    <div class="late-kpi" style="--kpi-c:#F97316;">
        <div class="late-kpi-val" style="color:#FDBA74;">{{ $maxLateRecord }}</div>
        <div class="late-kpi-label">Terlambat Terlama (menit)</div>
    </div>
    <div class="late-kpi" style="--kpi-c:#7C3AED;">
        <div class="late-kpi-val" style="color:#C4B5FD;">{{ $avgLate }}</div>
        <div class="late-kpi-label">Rata-rata Per Kejadian</div>
    </div>
</div>

{{-- Table --}}
<div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
        <h3 style="font-size:0.9rem;font-weight:700;color:var(--t1);">Daftar Karyawan Terlambat</h3>
        <span style="font-size:0.72rem;color:var(--t4);">Diurutkan: terlambat terlama</span>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width:36px;">#</th>
                    <th>Karyawan</th>
                    <th>Divisi</th>
                    <th>Tanggal</th>
                    <th>Shift</th>
                    <th>Jam Masuk</th>
                    <th>Terlambat</th>
                    <th style="text-align:right;">Toleransi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lateRecords as $i => $rec)
                @php
                    $lateMin = $rec->late_minutes;
                    $severity = $lateMin > 60 ? 'danger' : ($lateMin > 30 ? 'warning' : 'orange');
                    $maxBar = $maxLateRecord > 0 ? min(100, round(($lateMin / $maxLateRecord) * 100)) : 0;
                    $rank = $lateRecords->firstItem() + $i;
                @endphp
                <tr>
                    <td>
                        @if($rank <= 3)
                        <div class="rank-badge" style="background:{{ $rank == 1 ? 'rgba(234,179,8,0.2)' : ($rank == 2 ? 'rgba(148,163,184,0.15)' : 'rgba(180,83,9,0.15)') }};color:{{ $rank == 1 ? '#FCD34D' : ($rank == 2 ? '#CBD5E1' : '#D97706') }};">
                            {{ $rank == 1 ? '🥇' : ($rank == 2 ? '🥈' : '🥉') }}
                        </div>
                        @else
                        <span style="font-size:0.72rem;color:var(--t4);">{{ $rank }}</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:0.6rem;">
                            <div class="avatar" style="width:28px;height:28px;font-size:0.58rem;overflow:hidden;flex-shrink:0;">
                                @if($rec->user->photo)
                                    <img src="{{ $rec->user->photo_url }}" style="width:100%;height:100%;object-fit:cover;">
                                @else {{ $rec->user->initials }} @endif
                            </div>
                            <div>
                                <div style="font-size:0.8rem;font-weight:700;color:var(--t1);">{{ $rec->user->name }}</div>
                                <div style="font-size:0.65rem;font-family:'JetBrains Mono',monospace;color:var(--t4);">{{ $rec->user->nik }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:0.75rem;color:var(--t3);">{{ $rec->user->division->name ?? '—' }}</td>
                    <td>
                        <div style="font-weight:600;font-size:0.8rem;color:var(--t1);">{{ \Carbon\Carbon::parse($rec->date)->format('d M') }}</div>
                        <div style="font-size:0.65rem;color:var(--t4);">{{ \Carbon\Carbon::parse($rec->date)->translatedFormat('l') }}</div>
                    </td>
                    <td style="font-size:0.75rem;color:var(--t3);">{{ $rec->user->shift->name ?? '—' }}</td>
                    <td>
                        <span style="font-family:'JetBrains Mono',monospace;font-weight:800;color:#FCA5A5;font-size:0.82rem;">
                            {{ $rec->check_in_time ? \Carbon\Carbon::parse($rec->check_in_time)->format('H:i') : '—' }}
                        </span>
                    </td>
                    <td>
                        <div style="display:flex;flex-direction:column;gap:0.2rem;">
                            <span class="badge badge-{{ $severity }}" style="font-family:'JetBrains Mono',monospace;font-weight:800;">
                                {{ $lateMin }}m
                            </span>
                            <div class="late-bar-track" style="width:80px;">
                                <div class="late-bar-fill" style="width:{{ $maxBar }}%;background:{{ $lateMin > 60 ? 'linear-gradient(90deg,#EF4444,#FCA5A5)' : 'linear-gradient(90deg,#F59E0B,#FCD34D)' }};"></div>
                            </div>
                        </div>
                    </td>
                    <td style="text-align:right;font-family:'JetBrains Mono',monospace;font-size:0.73rem;color:var(--t4);">
                        {{ $rec->user->shift->late_tolerance_minutes ?? 0 }}m
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:3.5rem;color:var(--t4);">
                        <div style="font-size:2rem;margin-bottom:0.75rem;">🎉</div>
                        <div style="font-weight:700;color:var(--t3);">Tidak ada keterlambatan!</div>
                        <div style="font-size:0.75rem;margin-top:0.25rem;">Semua karyawan hadir tepat waktu pada bulan ini</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($lateRecords->hasPages())
    <div style="margin-top:1.5rem;padding:0 0.25rem;">{{ $lateRecords->links() }}</div>
    @endif
</div>

@endsection
