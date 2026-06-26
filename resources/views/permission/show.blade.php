@extends('layouts.app')

@section('title', 'Detail Izin')
@section('page-title', 'Detail Izin')
@section('breadcrumb', 'Perizinan › Izin › Detail')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid rgba(255,255,255,0.06); flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <h2 style="font-size: 1rem; font-weight: 600;">Detail Pengajuan Izin</h2>
                @if($permission->status === 'pending')
                    <span class="badge badge-warning">⏳ Pending</span>
                @elseif($permission->status === 'approved')
                    <span class="badge badge-success">✓ Disetujui</span>
                @elseif($permission->status === 'rejected')
                    <span class="badge badge-danger">✗ Ditolak</span>
                @endif
            </div>
            <a href="{{ route('permission.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
        </div>

        <div style="display: grid; gap: 0.85rem;">
            @php
                $permTypeLabels = ['late_in'=>'Izin Terlambat','early_out'=>'Pulang Lebih Awal','outside'=>'Dinas Luar','other'=>'Lainnya'];
            @endphp
            <div style="padding: 0.75rem; background: rgba(255,255,255,0.03); border-radius: 8px;">
                <div style="font-size: 0.7rem; color: #64748b; margin-bottom: 0.25rem;">Karyawan</div>
                <div style="font-weight: 600;">{{ $permission->user->name }}</div>
                <div style="font-size: 0.75rem; color: #64748b;">{{ $permission->user->employee?->division?->name ?? '' }}</div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div style="padding: 0.75rem; background: rgba(255,255,255,0.03); border-radius: 8px;">
                    <div style="font-size: 0.7rem; color: #64748b; margin-bottom: 0.25rem;">Jenis Izin</div>
                    <div style="font-weight: 600; font-size: 0.85rem;">{{ $permTypeLabels[$permission->type] ?? $permission->type }}</div>
                </div>
                <div style="padding: 0.75rem; background: rgba(255,255,255,0.03); border-radius: 8px;">
                    <div style="font-size: 0.7rem; color: #64748b; margin-bottom: 0.25rem;">Tanggal</div>
                    <div style="font-weight: 600; font-size: 0.85rem;">{{ \Carbon\Carbon::parse($permission->date)->format('d M Y') }}</div>
                </div>
                <div style="padding: 0.75rem; background: rgba(255,255,255,0.03); border-radius: 8px;">
                    <div style="font-size: 0.7rem; color: #64748b; margin-bottom: 0.25rem;">Jam Mulai</div>
                    <div style="font-weight: 600; font-size: 0.85rem; font-family: monospace;">{{ $permission->start_time ?? '-' }}</div>
                </div>
                <div style="padding: 0.75rem; background: rgba(255,255,255,0.03); border-radius: 8px;">
                    <div style="font-size: 0.7rem; color: #64748b; margin-bottom: 0.25rem;">Jam Selesai</div>
                    <div style="font-weight: 600; font-size: 0.85rem; font-family: monospace;">{{ $permission->end_time ?? '-' }}</div>
                </div>
            </div>
            <div style="padding: 0.75rem; background: rgba(255,255,255,0.03); border-radius: 8px;">
                <div style="font-size: 0.7rem; color: #64748b; margin-bottom: 0.25rem;">Alasan</div>
                <p style="font-size: 0.85rem; color: #cbd5e1; line-height: 1.6;">{{ $permission->reason }}</p>
            </div>
            @if($permission->attachment)
            <div>
                <a href="{{ Storage::url($permission->attachment) }}" target="_blank" class="btn btn-secondary btn-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                    Lihat Lampiran
                </a>
            </div>
            @endif
        </div>

        @hasrole(['super_admin', 'hrd', 'manager'])
        @if($permission->status === 'pending')
        <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.06); display: flex; gap: 0.75rem; justify-content: flex-end;">
            <form method="POST" action="{{ route('permission.approve', $permission) }}">
                @csrf
                <button type="submit" class="btn btn-success" onclick="return confirm('Setujui pengajuan izin ini?')">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Setujui
                </button>
            </form>
            <form method="POST" action="{{ route('permission.reject', $permission) }}">
                @csrf
                <button type="submit" class="btn btn-danger" onclick="return confirm('Tolak pengajuan izin ini?')">
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
