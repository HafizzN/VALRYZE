@extends('layouts.app')

@section('title', 'Laporan Kehadiran')
@section('page-title', 'Laporan Kehadiran')
@section('breadcrumb', 'Laporan › Kehadiran')

@push('styles')
<style>
    .report-kpi {
        background: var(--bg-card);
        border: 1px solid var(--border-soft);
        border-radius: 16px; padding: 1.1rem 1.25rem;
        box-shadow: var(--shadow-card);
        transition: all 0.2s ease;
        position: relative; overflow: hidden;
    }
    .report-kpi::after {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
        background: linear-gradient(90deg, var(--kpi-c, var(--em)), transparent);
        border-radius: 16px 16px 0 0;
    }
    .report-kpi:hover { transform: translateY(-2px); border-color: var(--kpi-c, var(--em-border)); }
    .report-kpi-val {
        font-family: 'JetBrains Mono', monospace;
        font-size: 2rem; font-weight: 800;
        line-height: 1; letter-spacing: -0.04em;
    }
    .report-kpi-label {
        font-size: 0.68rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.07em; color: var(--t4); margin-top: 0.3rem;
    }

    .export-btn-xl {
        display: flex; align-items: center; gap: 0.6rem;
        padding: 0.65rem 1.25rem; border-radius: 12px;
        font-size: 0.82rem; font-weight: 700; text-decoration: none;
        border: 1px solid; transition: all 0.2s ease;
    }
    .export-btn-xl:hover { transform: translateY(-2px); }

    .report-table td { font-size: 0.8rem; }
</style>
@endpush

@section('content')

{{-- ━━━━━━━━━━━━━━━━━━━━━━ HEADER ━━━━━━━━━━━━━━━━━━━━━━ --}}
<div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;">
    <div>
        <h2 style="font-size:1.1rem;font-weight:800;color:var(--t1);letter-spacing:-0.01em;">Laporan Kehadiran Karyawan</h2>
        <p style="font-size:0.78rem;color:var(--t4);margin-top:0.25rem;">
            Periode:
            <strong style="color:var(--t2);">{{ \Carbon\Carbon::parse($month)->translatedFormat('F Y') }}</strong>
            @if(request('division_id'))· <span style="color:var(--em);">{{ $divisions->find(request('division_id'))?->name }}</span>@endif
        </p>
    </div>
    <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
        <a href="{{ route('reports.attendance.export', array_merge(request()->query(), ['format' => 'excel'])) }}"
           class="export-btn-xl"
           style="background:rgba(16,185,129,0.08);border-color:rgba(16,185,129,0.25);color:#34D399;">
            <svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Excel
        </a>
        <a href="{{ route('reports.attendance.export', array_merge(request()->query(), ['format' => 'pdf'])) }}"
           class="export-btn-xl"
           style="background:rgba(239,68,68,0.08);border-color:rgba(239,68,68,0.25);color:#FCA5A5;">
            <svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            PDF
        </a>
    </div>
</div>

{{-- ━━━━━━━━━━━━━━━━━━━━━━ FILTER ━━━━━━━━━━━━━━━━━━━━━━ --}}
<div class="card mb-5">
    <form method="GET" action="{{ route('reports.attendance') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="form-group" style="margin-bottom:0;">
            <label class="form-label" for="month">Bulan Laporan</label>
            <input type="month" name="month" id="month" class="form-control" value="{{ $month }}">
        </div>
        <div class="form-group" style="margin-bottom:0;">
            <label class="form-label" for="division_id">Divisi</label>
            <select name="division_id" id="division_id" class="form-control">
                <option value="">Semua Divisi</option>
                @foreach($divisions as $div)
                <option value="{{ $div->id }}" {{ request('division_id') == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group" style="margin-bottom:0;">
            <label class="form-label" for="user_id">Karyawan</label>
            <select name="user_id" id="user_id" class="form-control">
                <option value="">Semua Karyawan</option>
                @foreach($users as $usr)
                <option value="{{ $usr->id }}" {{ request('user_id') == $usr->id ? 'selected' : '' }}>{{ $usr->name }}</option>
                @endforeach
            </select>
        </div>
        <div style="display:flex;align-items:flex-end;gap:0.5rem;">
            <button type="submit" class="btn btn-primary flex-1">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Tampilkan
            </button>
            @if(request()->anyFilled(['month','division_id','user_id']))
            <a href="{{ route('reports.attendance') }}" class="btn btn-secondary">Reset</a>
            @endif
        </div>
    </form>
</div>

{{-- ━━━━━━━━━━━━━━━━━━━━━━ KPI CARDS ━━━━━━━━━━━━━━━━━━━━━━ --}}
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-5">
    <div class="report-kpi" style="--kpi-c:#10B981;">
        <div class="report-kpi-val" style="color:#34D399;">{{ $summary['present'] }}</div>
        <div class="report-kpi-label">Hadir Tepat Waktu</div>
        @if(isset($summary['present_pct']))
        <div style="font-size:0.65rem;color:var(--t5);margin-top:0.2rem;">{{ $summary['present_pct'] }}% dari total</div>
        @endif
    </div>
    <div class="report-kpi" style="--kpi-c:#F59E0B;">
        <div class="report-kpi-val" style="color:#FCD34D;">{{ $summary['late'] }}</div>
        <div class="report-kpi-label">Terlambat</div>
    </div>
    <div class="report-kpi" style="--kpi-c:#7C3AED;">
        <div class="report-kpi-val" style="color:#C4B5FD;">{{ $summary['permission'] }}</div>
        <div class="report-kpi-label">Izin / Sakit</div>
    </div>
    <div class="report-kpi" style="--kpi-c:#6366F1;">
        <div class="report-kpi-val" style="color:#A5B4FC;">{{ $summary['leave'] }}</div>
        <div class="report-kpi-label">Cuti Tahunan</div>
    </div>
    <div class="report-kpi" style="--kpi-c:#EF4444;grid-column:span 2 / span 2;" class="md:col-span-1">
        <div class="report-kpi-val" style="color:#FCA5A5;">{{ $summary['absent'] }}</div>
        <div class="report-kpi-label">Mangkir (Alpa)</div>
    </div>
</div>

{{-- ━━━━━━━━━━━━━━━━━━━━━━ TABLE ━━━━━━━━━━━━━━━━━━━━━━ --}}
<div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
        <h3 style="font-size:0.9rem;font-weight:700;color:var(--t1);">Rincian Log Kehadiran Harian</h3>
        <span style="font-size:0.72rem;color:var(--t4);">
            Hal. {{ $attendances->currentPage() }} / {{ $attendances->lastPage() }}
            &nbsp;·&nbsp; {{ $attendances->total() }} total
        </span>
    </div>

    <div class="table-container">
        <table class="report-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>NIK</th>
                    <th>Karyawan</th>
                    <th>Divisi</th>
                    <th>Masuk</th>
                    <th>Pulang</th>
                    <th>Terlambat</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $att)
                <tr>
                    <td>
                        <div style="font-weight:600;color:var(--t1);">{{ \Carbon\Carbon::parse($att->date)->format('d M') }}</div>
                        <div style="font-size:0.67rem;color:var(--t4);">{{ \Carbon\Carbon::parse($att->date)->translatedFormat('l') }}</div>
                    </td>
                    <td style="font-family:'JetBrains Mono',monospace;font-size:0.72rem;color:var(--t4);">{{ $att->user?->nik ?? '—' }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:0.5rem;">
                            <div class="avatar" style="width:26px;height:26px;font-size:0.55rem;overflow:hidden;flex-shrink:0;">
                                @if($att->user?->photo)
                                    <img src="{{ $att->user->photo_url }}" style="width:100%;height:100%;object-fit:cover;">
                                @else
                                    {{ $att->user?->initials }}
                                @endif
                            </div>
                            <span style="font-weight:600;color:var(--t1);font-size:0.8rem;">{{ $att->user?->name ?? '—' }}</span>
                        </div>
                    </td>
                    <td style="color:var(--t3);font-size:0.75rem;">{{ $att->user?->division?->name ?? '—' }}</td>
                    <td>
                        @if($att->check_in_time)
                        <span style="font-family:'JetBrains Mono',monospace;font-weight:700;color:{{ $att->status === 'late' ? '#FCD34D' : 'var(--em)' }};">
                            {{ \Carbon\Carbon::parse($att->check_in_time)->format('H:i') }}
                        </span>
                        @else
                        <span style="color:var(--t5);">—</span>
                        @endif
                    </td>
                    <td>
                        @if($att->check_out_time)
                        <span style="font-family:'JetBrains Mono',monospace;font-weight:700;color:var(--t3);">
                            {{ \Carbon\Carbon::parse($att->check_out_time)->format('H:i') }}
                        </span>
                        @else
                        <span style="color:var(--t5);">—</span>
                        @endif
                    </td>
                    <td>
                        @if($att->late_minutes > 0)
                        <span style="display:inline-flex;align-items:center;gap:0.25rem;font-size:0.73rem;font-weight:700;color:#FCD34D;">
                            ⏰ {{ $att->late_minutes }}m
                        </span>
                        @else
                        <span style="color:var(--t5);">—</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;flex-direction:column;gap:0.25rem;">
                            @switch($att->status)
                                @case('present')  <span class="badge badge-success">✓ Hadir</span>    @break
                                @case('late')     <span class="badge badge-warning">⏰ Terlambat</span> @break
                                @case('absent')   <span class="badge badge-danger">✗ Mangkir</span>   @break
                                @case('leave')    <span class="badge badge-orange">📋 Cuti</span>     @break
                                @case('permission')<span class="badge badge-purple">📝 Izin</span>    @break
                                @default          <span class="badge badge-gray">{{ $att->status }}</span>
                            @endswitch
                            @if($att->early_out_minutes > 0)
                            <span class="badge badge-orange" style="font-size:0.6rem;">Pulang Cepat</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:3.5rem;color:var(--t4);">
                        <div style="font-size:2rem;margin-bottom:0.75rem;">📋</div>
                        <div style="font-weight:600;color:var(--t3);">Tidak ada log kehadiran</div>
                        <div style="font-size:0.75rem;margin-top:0.25rem;">Coba ubah filter bulan atau divisi</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($attendances->hasPages())
    <div style="margin-top:1.5rem;padding:0 0.25rem;">{{ $attendances->links() }}</div>
    @endif
</div>

@endsection
