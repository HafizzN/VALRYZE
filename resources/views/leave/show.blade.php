@extends('layouts.app')

@section('title', 'Detail Cuti')
@section('page-title', 'Detail Cuti')
@section('breadcrumb', 'Perizinan › Cuti › Detail')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="card">
        {{-- Header --}}
        <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid rgba(255,255,255,0.06); flex-wrap: wrap; gap: 1rem;">
            <div>
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <h2 style="font-size: 1.1rem; font-weight: 700;">Detail Pengajuan Cuti</h2>
                    @if($leave->status === 'pending')
                        <span class="badge badge-warning">⏳ Pending</span>
                    @elseif($leave->status === 'approved')
                        <span class="badge badge-success">✓ Disetujui</span>
                    @elseif($leave->status === 'rejected')
                        <span class="badge badge-danger">✗ Ditolak</span>
                    @endif
                </div>
                <p style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">Diajukan {{ $leave->created_at->diffForHumans() }}</p>
            </div>
            <a href="{{ route('leave.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Employee Info --}}
            <div>
                <div style="font-size: 0.7rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; margin-bottom: 0.75rem;">Informasi Karyawan</div>
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem; padding: 0.75rem; background: rgba(255,255,255,0.03); border-radius: 10px;">
                    @if($leave->user->employee?->photo)
                    <img src="{{ Storage::url($leave->user->employee->photo) }}" style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover;">
                    @else
                    <div class="avatar" style="width: 48px; height: 48px; font-size: 0.85rem;">{{ $leave->user->initials }}</div>
                    @endif
                    <div>
                        <div style="font-weight: 600;">{{ $leave->user->name }}</div>
                        <div style="font-size: 0.75rem; color: #64748b;">{{ $leave->user->employee?->position?->name ?? '-' }}</div>
                        <div style="font-size: 0.72rem; color: #64748b;">{{ $leave->user->employee?->division?->name ?? '-' }}</div>
                    </div>
                </div>
            </div>

            {{-- Leave Info --}}
            <div>
                <div style="font-size: 0.7rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; margin-bottom: 0.75rem;">Informasi Cuti</div>
                <div style="display: grid; gap: 0.6rem;">
                    @php
                        $typeLabels = ['annual'=>'Tahunan','sick'=>'Sakit','maternity'=>'Melahirkan','wedding'=>'Pernikahan','big_leave'=>'Cuti Besar'];
                    @endphp
                    <div style="display: flex; justify-content: space-between; font-size: 0.82rem;">
                        <span style="color: #64748b;">Jenis Cuti</span>
                        <strong>{{ $typeLabels[$leave->leave_type] ?? $leave->leave_type }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.82rem;">
                        <span style="color: #64748b;">Tanggal Mulai</span>
                        <strong>{{ \Carbon\Carbon::parse($leave->start_date)->format('d M Y') }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.82rem;">
                        <span style="color: #64748b;">Tanggal Selesai</span>
                        <strong>{{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.82rem;">
                        <span style="color: #64748b;">Durasi</span>
                        <strong>{{ $leave->duration }} hari</strong>
                    </div>
                </div>
            </div>
        </div>

        {{-- Reason --}}
        <div style="margin-top: 1.5rem; padding: 1rem; background: rgba(255,255,255,0.03); border-radius: 10px;">
            <div style="font-size: 0.7rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; margin-bottom: 0.5rem;">Alasan Cuti</div>
            <p style="font-size: 0.85rem; line-height: 1.6; color: #cbd5e1;">{{ $leave->reason }}</p>
        </div>

        {{-- Emergency Contact --}}
        @if($leave->emergency_contact)
        <div style="margin-top: 1rem; padding: 0.75rem; background: rgba(255,255,255,0.03); border-radius: 10px;">
            <div style="font-size: 0.7rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; margin-bottom: 0.25rem;">Kontak Darurat</div>
            <p style="font-size: 0.85rem; color: #cbd5e1;">{{ $leave->emergency_contact }}</p>
        </div>
        @endif

        {{-- Attachment --}}
        @if($leave->attachment)
        <div style="margin-top: 1rem;">
            <div style="font-size: 0.7rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; margin-bottom: 0.5rem;">Lampiran</div>
            <a href="{{ Storage::url($leave->attachment) }}" target="_blank" class="btn btn-secondary btn-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Lihat Lampiran
            </a>
        </div>
        @endif

        {{-- Approval Info --}}
        @if($leave->status !== 'pending' && $leave->approvedBy)
        <div style="margin-top: 1.5rem; padding: 1rem; background: rgba(255,255,255,0.03); border-radius: 10px;">
            <div style="font-size: 0.7rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; margin-bottom: 0.5rem;">
                {{ $leave->status === 'approved' ? 'Disetujui' : 'Ditolak' }} Oleh
            </div>
            <div style="font-size: 0.85rem; font-weight: 500;">{{ $leave->approvedBy->name }}</div>
            <div style="font-size: 0.75rem; color: #64748b;">{{ $leave->approved_at ? \Carbon\Carbon::parse($leave->approved_at)->format('d M Y H:i') : '-' }}</div>
            @if($leave->rejection_reason)
            <div style="margin-top: 0.5rem; font-size: 0.82rem; color: #f87171;">Alasan: {{ $leave->rejection_reason }}</div>
            @endif
        </div>
        @endif

        {{-- Action Buttons --}}
        @hasrole(['super_admin', 'hrd', 'manager'])
        @if($leave->status === 'pending')
        <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.06); display: flex; gap: 0.75rem; justify-content: flex-end;" x-data="{ showReject: false, reason: '' }">
            <form method="POST" action="{{ route('leave.approve', $leave) }}">
                @csrf
                <button type="submit" class="btn btn-success" onclick="return confirm('Setujui pengajuan cuti ini?')">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Setujui
                </button>
            </form>
            <button type="button" @click="showReject = !showReject" class="btn btn-danger">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                Tolak
            </button>
            <div x-show="showReject" x-transition style="position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 50; display: flex; align-items: center; justify-content: center;">
                <div style="background: #1a2235; border-radius: 12px; padding: 1.5rem; width: 400px; max-width: 90vw;">
                    <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 1rem;">Tolak Pengajuan Cuti</h3>
                    <form method="POST" action="{{ route('leave.reject', $leave) }}">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">Alasan Penolakan</label>
                            <textarea name="rejection_reason" class="form-control" rows="3" required placeholder="Jelaskan alasan penolakan..."></textarea>
                        </div>
                        <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                            <button type="button" @click="showReject = false" class="btn btn-secondary">Batal</button>
                            <button type="submit" class="btn btn-danger">Tolak</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
        @endhasrole
    </div>
</div>
@endsection
