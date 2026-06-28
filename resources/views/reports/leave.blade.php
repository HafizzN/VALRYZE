@extends('layouts.app')

@section('title', 'Laporan Cuti Karyawan')
@section('page-title', 'Laporan Cuti')
@section('breadcrumb', 'Laporan › Cuti')

@push('styles')
<style>
    .tab-pill {
        padding: 0.45rem 1.1rem; border-radius: 10px; font-size: 0.78rem;
        font-weight: 700; cursor: pointer; transition: all 0.2s ease;
        border: 1px solid transparent; background: transparent; color: var(--t4);
    }
    .tab-pill:hover  { color: var(--t2); background: var(--bg-elevated); }
    .tab-pill.active { background: var(--em-ghost); border-color: var(--em-border); color: var(--em); }

    .quota-bar-track {
        height: 5px; background: var(--bg-elevated); border-radius: 99px;
        overflow: hidden; margin-top: 0.3rem; width: 80px;
    }
    .quota-bar-fill { height: 100%; border-radius: 99px; transition: width 0.6s ease; }

    .type-icon {
        font-size: 1rem; width: 28px; height: 28px; border-radius: 8px;
        display: inline-flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
</style>
@endpush

@section('content')
@php
    $typeMap = [
        'annual'   => ['label' => 'Tahunan',   'badge' => 'badge-success', 'icon' => '📋'],
        'sick'     => ['label' => 'Sakit',     'badge' => 'badge-purple',  'icon' => '🏥'],
        'maternity'=> ['label' => 'Melahirkan','badge' => 'badge-orange',  'icon' => '👶'],
        'wedding'  => ['label' => 'Pernikahan','badge' => 'badge-info',    'icon' => '💍'],
        'marriage' => ['label' => 'Pernikahan','badge' => 'badge-info',    'icon' => '💍'],
        'big_leave'=> ['label' => 'Cuti Besar','badge' => 'badge-gray',    'icon' => '🌴'],
    ];
    $totalUsed = collect($leaveBalance)->sum('used');
    $totalRemaining = collect($leaveBalance)->sum('remaining');
@endphp

<div x-data="{ activePanel: 'balance' }">

{{-- Header --}}
<div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;">
    <div>
        <h2 style="font-size:1.1rem;font-weight:800;color:var(--t1);letter-spacing:-0.01em;">Laporan & Kuota Cuti Karyawan</h2>
        <p style="font-size:0.78rem;color:var(--t4);margin-top:0.25rem;">
            Pantau sisa kuota cuti tahunan dan riwayat pengajuan yang disetujui
        </p>
    </div>
</div>

{{-- Filter --}}
<div class="card mb-5">
    <form method="GET" action="{{ route('reports.leave') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="form-group" style="margin-bottom:0;">
            <label class="form-label" for="year">Tahun Laporan</label>
            <select name="year" id="year" class="form-control">
                @for($y = now()->year; $y >= now()->year - 4; $y--)
                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <div style="display:flex;align-items:flex-end;gap:0.5rem;">
            <button type="submit" class="btn btn-primary flex-1">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Tampilkan
            </button>
        </div>
        <div></div>
    </form>
</div>

{{-- Tabs --}}
<div style="display:flex;gap:0.4rem;background:var(--bg-card);border:1px solid var(--border-soft);border-radius:14px;padding:0.35rem;width:fit-content;margin-bottom:1.25rem;">
    <button @click="activePanel='balance'" :class="activePanel==='balance' ? 'active' : ''" class="tab-pill">
        📋 Kuota & Sisa Cuti
    </button>
    <button @click="activePanel='history'" :class="activePanel==='history' ? 'active' : ''" class="tab-pill">
        📅 Riwayat Disetujui
    </button>
</div>

{{-- Panel 1: Leave Balance --}}
<div x-show="activePanel === 'balance'" x-transition>
    <div class="card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
            <h3 style="font-size:0.9rem;font-weight:700;color:var(--t1);">Kuota Cuti Tahunan – {{ $year }}</h3>
            <span style="font-size:0.72rem;color:var(--t4);">{{ count($leaveBalance) }} karyawan</span>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Karyawan</th>
                        <th>Divisi</th>
                        <th style="text-align:center;">Kuota</th>
                        <th style="text-align:center;">Terpakai</th>
                        <th style="text-align:center;">Sisa</th>
                        <th style="text-align:center;">Progress</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaveBalance as $item)
                    @php
                        $quota = $item['user']->annual_leave_quota ?? 12;
                        $used  = $item['user']->annual_leave_used ?? 0;
                        $remaining = $item['remaining'];
                        $usedPct = $quota > 0 ? min(100, round(($used / $quota) * 100)) : 0;
                        $barColor = $remaining > 3 ? 'linear-gradient(90deg,#10B981,#34D399)' : ($remaining > 0 ? 'linear-gradient(90deg,#F59E0B,#FCD34D)' : 'linear-gradient(90deg,#EF4444,#FCA5A5)');
                    @endphp
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:0.6rem;">
                                <div class="avatar" style="width:28px;height:28px;font-size:0.58rem;overflow:hidden;flex-shrink:0;">
                                    @if($item['user']->photo)
                                        <img src="{{ $item['user']->photo_url }}" style="width:100%;height:100%;object-fit:cover;">
                                    @else {{ $item['user']->initials }} @endif
                                </div>
                                <div>
                                    <div style="font-size:0.8rem;font-weight:700;color:var(--t1);">{{ $item['user']->name }}</div>
                                    <div style="font-size:0.65rem;font-family:'JetBrains Mono',monospace;color:var(--t4);">{{ $item['user']->nik }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="font-size:0.75rem;color:var(--t3);">{{ $item['user']->division->name ?? '—' }}</td>
                        <td style="text-align:center;font-family:'JetBrains Mono',monospace;font-weight:700;color:var(--t2);">{{ $quota }}h</td>
                        <td style="text-align:center;font-family:'JetBrains Mono',monospace;font-weight:700;color:#FCD34D;">{{ $used }}h</td>
                        <td style="text-align:center;">
                            @if($remaining > 3)
                                <span class="badge badge-success" style="font-family:'JetBrains Mono',monospace;font-weight:800;">{{ $remaining }}h</span>
                            @elseif($remaining > 0)
                                <span class="badge badge-warning" style="font-family:'JetBrains Mono',monospace;font-weight:800;">{{ $remaining }}h</span>
                            @else
                                <span class="badge badge-danger" style="font-family:'JetBrains Mono',monospace;font-weight:800;">Habis</span>
                            @endif
                        </td>
                        <td style="text-align:center;">
                            <div class="quota-bar-track" style="margin:0 auto;">
                                <div class="quota-bar-fill" style="width:{{ $usedPct }}%;background:{{ $barColor }};"></div>
                            </div>
                            <div style="font-size:0.62rem;color:var(--t5);margin-top:0.15rem;">{{ $usedPct }}%</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:3rem;color:var(--t4);">
                            <div style="font-size:2rem;margin-bottom:0.5rem;">📋</div>
                            <div style="font-weight:600;color:var(--t3);">Tidak ada data karyawan</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Panel 2: Leave History --}}
<div x-show="activePanel === 'history'" x-transition style="display:none;">
    <div class="card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
            <h3 style="font-size:0.9rem;font-weight:700;color:var(--t1);">Cuti Disetujui – {{ $year }}</h3>
            <span style="font-size:0.72rem;color:var(--t4);">Hal. {{ $leaves->currentPage() }} / {{ $leaves->lastPage() }}</span>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Karyawan</th>
                        <th>Periode Cuti</th>
                        <th>Jenis</th>
                        <th style="text-align:center;">Durasi</th>
                        <th>Alasan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaves as $leave)
                    @php $t = $typeMap[$leave->leave_type] ?? ['label' => $leave->leave_type, 'badge' => 'badge-gray', 'icon' => '📝']; @endphp
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:0.6rem;">
                                <div class="avatar" style="width:28px;height:28px;font-size:0.58rem;overflow:hidden;flex-shrink:0;">
                                    @if($leave->user->photo)
                                        <img src="{{ $leave->user->photo_url }}" style="width:100%;height:100%;object-fit:cover;">
                                    @else {{ $leave->user->initials }} @endif
                                </div>
                                <div>
                                    <div style="font-size:0.8rem;font-weight:700;color:var(--t1);">{{ $leave->user->name }}</div>
                                    <div style="font-size:0.65rem;color:var(--t4);">{{ $leave->user->division->name ?? '—' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="font-weight:600;font-size:0.8rem;color:var(--t1);">{{ \Carbon\Carbon::parse($leave->start_date)->format('d M Y') }}</div>
                            <div style="font-size:0.68rem;color:var(--t4);">s/d {{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}</div>
                        </td>
                        <td>
                            <span class="badge {{ $t['badge'] }}">{{ $t['icon'] }} {{ $t['label'] }}</span>
                        </td>
                        <td style="text-align:center;">
                            <span style="font-family:'JetBrains Mono',monospace;font-weight:800;color:var(--em);font-size:0.82rem;">
                                {{ $leave->duration ?? \Carbon\Carbon::parse($leave->start_date)->diffInDays(\Carbon\Carbon::parse($leave->end_date)) + 1 }}h
                            </span>
                        </td>
                        <td style="max-width:200px;">
                            <div style="font-size:0.75rem;color:var(--t3);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $leave->reason }}">
                                {{ Str::limit($leave->reason, 50) }}
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center;padding:3rem;color:var(--t4);">
                            <div style="font-size:2rem;margin-bottom:0.5rem;">🌴</div>
                            <div style="font-weight:600;color:var(--t3);">Belum ada riwayat cuti disetujui</div>
                            <div style="font-size:0.75rem;margin-top:0.25rem;">Tahun {{ $year }}</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($leaves->hasPages())
        <div style="margin-top:1.5rem;padding:0 0.25rem;">{{ $leaves->links() }}</div>
        @endif
    </div>
</div>

</div>
@endsection
