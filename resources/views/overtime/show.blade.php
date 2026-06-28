@extends('layouts.app')

@section('title', 'Detail Lembur')
@section('page-title', 'Detail Lembur')
@section('breadcrumb', 'Perizinan › Lembur › Detail')

@push('styles')
<style>
    .ot-info-grid {
        display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;
    }
    .ot-block {
        background: var(--bg-elevated); border: 1px solid var(--border-soft);
        border-radius: 12px; padding: 0.9rem 1rem;
    }
    .ot-block-label {
        font-size: 0.6rem; font-weight: 800; letter-spacing: 0.12em;
        text-transform: uppercase; color: var(--t5); margin-bottom: 0.4rem;
    }
    .ot-block-val {
        font-size: 0.9rem; font-weight: 800; color: var(--t1);
    }
</style>
@endpush

@section('content')
<div class="max-w-2xl mx-auto">

    {{-- Back + status --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;flex-wrap:wrap;gap:0.75rem;">
        <a href="{{ route('overtime.index') }}" style="display:inline-flex;align-items:center;gap:0.4rem;font-size:0.8rem;font-weight:600;color:var(--t3);text-decoration:none;"
           onmouseover="this.style.color='var(--em)'" onmouseout="this.style.color='var(--t3)'">
            <svg style="width:15px;height:15px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Daftar
        </a>
        @if($overtime->status === 'pending')     <span class="badge badge-warning">⏳ Menunggu Persetujuan</span>
        @elseif($overtime->status === 'approved') <span class="badge badge-success">✓ Disetujui</span>
        @elseif($overtime->status === 'rejected') <span class="badge badge-danger">✗ Ditolak</span>
        @endif
    </div>

    <div class="card">
        {{-- Hero header --}}
        <div style="display:flex;align-items:center;gap:1rem;padding-bottom:1.25rem;border-bottom:1px solid var(--border-dim);margin-bottom:1.25rem;">
            <div style="width:52px;height:52px;border-radius:16px;background:rgba(167,139,250,0.12);border:1px solid rgba(167,139,250,0.2);display:flex;align-items:center;justify-content:center;font-size:1.6rem;flex-shrink:0;">
                ⏱
            </div>
            <div>
                <h2 style="font-size:1.05rem;font-weight:800;color:var(--t1);">Detail Pengajuan Lembur</h2>
                <p style="font-size:0.73rem;color:var(--t4);margin-top:0.15rem;">
                    {{ \Carbon\Carbon::parse($overtime->date)->translatedFormat('l, d F Y') }}
                    @if($overtime->duration_hours)
                    · <span style="color:#C4B5FD;font-weight:700;">{{ $overtime->duration_hours }} jam lembur</span>
                    @endif
                </p>
            </div>
        </div>

        {{-- Employee card --}}
        <div style="display:flex;align-items:center;gap:0.85rem;padding:0.9rem 1rem;background:var(--bg-elevated);border:1px solid var(--border-soft);border-radius:12px;margin-bottom:1rem;">
            <div class="avatar" style="width:44px;height:44px;font-size:0.8rem;overflow:hidden;flex-shrink:0;">
                @if($overtime->user->photo)
                    <img src="{{ $overtime->user->photo_url }}" style="width:100%;height:100%;object-fit:cover;">
                @else {{ $overtime->user->initials }} @endif
            </div>
            <div>
                <div style="font-size:0.9rem;font-weight:800;color:var(--t1);">{{ $overtime->user->name }}</div>
                <div style="font-size:0.72rem;color:var(--t3);">
                    {{ $overtime->user->employee?->position?->name ?? $overtime->user->position?->name ?? '—' }}
                    @if($overtime->user->employee?->division?->name ?? $overtime->user->division?->name)
                     · {{ $overtime->user->employee?->division?->name ?? $overtime->user->division?->name }}
                    @endif
                </div>
            </div>
        </div>

        {{-- Time blocks --}}
        <div class="ot-info-grid" style="margin-bottom:0.75rem;">
            <div class="ot-block">
                <div class="ot-block-label">📅 Tanggal</div>
                <div class="ot-block-val">{{ \Carbon\Carbon::parse($overtime->date)->format('d M Y') }}</div>
            </div>
            <div class="ot-block">
                <div class="ot-block-label">⏱ Durasi</div>
                <div class="ot-block-val" style="color:#C4B5FD;">{{ $overtime->duration_hours ?? '—' }} jam</div>
            </div>
            <div class="ot-block">
                <div class="ot-block-label">🕐 Jam Mulai</div>
                <div class="ot-block-val" style="font-family:'JetBrains Mono',monospace;color:var(--em);">{{ $overtime->start_time }}</div>
            </div>
            <div class="ot-block">
                <div class="ot-block-label">🕔 Jam Selesai</div>
                <div class="ot-block-val" style="font-family:'JetBrains Mono',monospace;color:var(--t3);">{{ $overtime->end_time ?? '—' }}</div>
            </div>
        </div>

        {{-- Kompensasi --}}
        <div class="ot-block" style="margin-bottom:0.75rem;">
            <div class="ot-block-label">💰 Kompensasi</div>
            <div style="display:flex;align-items:center;gap:0.5rem;margin-top:0.25rem;">
                @if($overtime->compensation_type === 'money')
                <span class="badge badge-success" style="font-size:0.75rem;font-weight:700;">💵 Uang Lembur</span>
                @else
                <span class="badge badge-purple" style="font-size:0.75rem;font-weight:700;">⏰ Time-off Pengganti</span>
                @endif
            </div>
        </div>

        {{-- Alasan --}}
        <div class="ot-block" style="margin-bottom:0.75rem;">
            <div class="ot-block-label">📝 Tugas / Alasan Lembur</div>
            <p style="font-size:0.84rem;color:var(--t2);line-height:1.7;margin-top:0.25rem;">{{ $overtime->reason }}</p>
        </div>

        {{-- Approval info --}}
        @if($overtime->status !== 'pending' && $overtime->approvedBy)
        <div class="ot-block" style="border-color:{{ $overtime->status === 'approved' ? 'rgba(16,185,129,0.2)' : 'rgba(239,68,68,0.2)' }};margin-bottom:0.75rem;">
            <div class="ot-block-label" style="color:{{ $overtime->status === 'approved' ? 'var(--em)' : 'var(--danger)' }};">
                {{ $overtime->status === 'approved' ? '✓ Disetujui Oleh' : '✗ Ditolak Oleh' }}
            </div>
            <div style="display:flex;align-items:center;gap:0.6rem;margin-top:0.3rem;">
                <div class="avatar" style="width:30px;height:30px;font-size:0.6rem;overflow:hidden;flex-shrink:0;">
                    @if($overtime->approvedBy->photo)
                        <img src="{{ $overtime->approvedBy->photo_url }}" style="width:100%;height:100%;object-fit:cover;">
                    @else {{ $overtime->approvedBy->initials }} @endif
                </div>
                <div>
                    <div style="font-weight:700;color:var(--t1);font-size:0.83rem;">{{ $overtime->approvedBy->name }}</div>
                    <div style="font-size:0.68rem;color:var(--t4);">{{ $overtime->approved_at ? \Carbon\Carbon::parse($overtime->approved_at)->translatedFormat('d F Y, H:i') : '—' }}</div>
                    @if($overtime->rejection_reason)
                    <div style="margin-top:0.3rem;font-size:0.77rem;color:#FCA5A5;font-style:italic;">"{{ $overtime->rejection_reason }}"</div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- Action buttons --}}
        @hasrole(['super_admin', 'hrd', 'manager'])
        @if($overtime->status === 'pending')
        <div style="padding-top:1rem;border-top:1px solid var(--border-dim);display:flex;gap:0.5rem;justify-content:flex-end;flex-wrap:wrap;">
            @hasrole('manager')
            <form method="POST" action="{{ route('overtime.approve-manager', $overtime) }}">
                @csrf
                <button type="submit" class="btn btn-success btn-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Setujui (Manager)
                </button>
            </form>
            @endhasrole
            @hasrole(['super_admin','hrd'])
            <form method="POST" action="{{ route('overtime.approve-hrd', $overtime) }}">
                @csrf
                <button type="submit" class="btn btn-success btn-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Setujui (HRD)
                </button>
            </form>
            @endhasrole
            <form method="POST" action="{{ route('overtime.reject', $overtime) }}">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Tolak
                </button>
            </form>
        </div>
        @endif
        @endhasrole
    </div>
</div>
@endsection
