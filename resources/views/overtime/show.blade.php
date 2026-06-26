@extends('layouts.app')

@section('title', 'Detail Lembur')
@section('page-title', 'Detail Lembur')
@section('breadcrumb', 'Perizinan › Lembur › Detail')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid rgba(255,255,255,0.06); flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <h2 style="font-size: 1rem; font-weight: 600;">Detail Lembur</h2>
                @if($overtime->status === 'pending')
                    <span class="badge badge-warning">⏳ Pending</span>
                @elseif($overtime->status === 'approved')
                    <span class="badge badge-success">✓ Disetujui</span>
                @elseif($overtime->status === 'rejected')
                    <span class="badge badge-danger">✗ Ditolak</span>
                @endif
            </div>
            <a href="{{ route('overtime.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
        </div>

        <div style="display: grid; gap: 0.85rem;">
            <div style="padding: 0.75rem; background: rgba(255,255,255,0.03); border-radius: 8px;">
                <div style="font-size: 0.7rem; color: #64748b; margin-bottom: 0.25rem;">Karyawan</div>
                <div style="font-weight: 600;">{{ $overtime->user->name }}</div>
                <div style="font-size: 0.75rem; color: #64748b;">{{ $overtime->user->employee?->position?->name ?? '' }} — {{ $overtime->user->employee?->division?->name ?? '' }}</div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div style="padding: 0.75rem; background: rgba(255,255,255,0.03); border-radius: 8px;">
                    <div style="font-size: 0.7rem; color: #64748b; margin-bottom: 0.25rem;">Tanggal</div>
                    <div style="font-weight: 600; font-size: 0.85rem;">{{ \Carbon\Carbon::parse($overtime->date)->format('d M Y') }}</div>
                </div>
                <div style="padding: 0.75rem; background: rgba(255,255,255,0.03); border-radius: 8px;">
                    <div style="font-size: 0.7rem; color: #64748b; margin-bottom: 0.25rem;">Durasi</div>
                    <div style="font-weight: 600; font-size: 0.85rem; color: #a78bfa;">{{ $overtime->duration_hours ?? '-' }} jam</div>
                </div>
                <div style="padding: 0.75rem; background: rgba(255,255,255,0.03); border-radius: 8px;">
                    <div style="font-size: 0.7rem; color: #64748b; margin-bottom: 0.25rem;">Jam Mulai</div>
                    <div style="font-weight: 600; font-family: monospace;">{{ $overtime->start_time }}</div>
                </div>
                <div style="padding: 0.75rem; background: rgba(255,255,255,0.03); border-radius: 8px;">
                    <div style="font-size: 0.7rem; color: #64748b; margin-bottom: 0.25rem;">Jam Selesai</div>
                    <div style="font-weight: 600; font-family: monospace;">{{ $overtime->end_time ?? '-' }}</div>
                </div>
            </div>

            <div style="padding: 0.75rem; background: rgba(255,255,255,0.03); border-radius: 8px;">
                <div style="font-size: 0.7rem; color: #64748b; margin-bottom: 0.25rem;">Tugas / Alasan Lembur</div>
                <p style="font-size: 0.85rem; color: #cbd5e1; line-height: 1.6;">{{ $overtime->reason }}</p>
            </div>

            <div style="padding: 0.75rem; background: rgba(255,255,255,0.03); border-radius: 8px;">
                <div style="font-size: 0.7rem; color: #64748b; margin-bottom: 0.25rem;">Kompensasi</div>
                <div style="font-size: 0.85rem;">{{ $overtime->compensation_type === 'money' ? 'Uang Lembur' : 'Time-off' }}</div>
            </div>

            @if($overtime->status !== 'pending' && $overtime->approvedBy)
            <div style="padding: 0.75rem; background: rgba(255,255,255,0.03); border-radius: 8px;">
                <div style="font-size: 0.7rem; color: #64748b; margin-bottom: 0.25rem;">{{ $overtime->status === 'approved' ? 'Disetujui' : 'Ditolak' }} Oleh</div>
                <div style="font-weight: 600; font-size: 0.85rem;">{{ $overtime->approvedBy->name }}</div>
                <div style="font-size: 0.75rem; color: #64748b;">{{ $overtime->approved_at ? \Carbon\Carbon::parse($overtime->approved_at)->format('d M Y H:i') : '-' }}</div>
                @if($overtime->rejection_reason)
                <div style="margin-top: 0.5rem; font-size: 0.82rem; color: #f87171;">Alasan: {{ $overtime->rejection_reason }}</div>
                @endif
            </div>
            @endif
        </div>

        @hasrole(['super_admin', 'hrd', 'manager'])
        @if($overtime->status === 'pending')
        <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.06); display: flex; gap: 0.75rem; justify-content: flex-end;">
            @hasrole('manager')
            <form method="POST" action="{{ route('overtime.approve-manager', $overtime) }}">
                @csrf
                <button type="submit" class="btn btn-success" onclick="return confirm('Setujui pengajuan lembur ini sebagai Manager?')">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Setujui (Manager)
                </button>
            </form>
            @endhasrole

            @hasrole(['super_admin', 'hrd'])
            <form method="POST" action="{{ route('overtime.approve-hrd', $overtime) }}">
                @csrf
                <button type="submit" class="btn btn-success" onclick="return confirm('Setujui pengajuan lembur ini sebagai HRD?')">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Setujui (HRD)
                </button>
            </form>
            @endhasrole

            <form method="POST" action="{{ route('overtime.reject', $overtime) }}">
                @csrf
                <button type="submit" class="btn btn-danger" onclick="return confirm('Tolak pengajuan lembur ini?')">
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
