@extends('layouts.app')

@section('title', 'Detail Cuti')
@section('page-title', 'Detail Cuti')
@section('breadcrumb', 'Perizinan › Cuti › Detail')

@push('styles')
<style>
    .detail-section {
        background: var(--bg-elevated);
        border: 1px solid var(--border-soft);
        border-radius: 14px; padding: 1.1rem 1.25rem;
        margin-bottom: 0.85rem;
    }
    .detail-section-label {
        font-size: 0.6rem; font-weight: 800; letter-spacing: 0.13em;
        text-transform: uppercase; color: var(--t5);
        margin-bottom: 0.7rem; display: flex; align-items: center; gap: 0.4rem;
    }
    .detail-row {
        display: flex; justify-content: space-between; align-items: center;
        padding: 0.45rem 0;
        border-bottom: 1px solid var(--border-dim);
        font-size: 0.82rem;
    }
    .detail-row:last-child { border-bottom: none; }
    .detail-row-label { color: var(--t4); font-weight: 500; }
    .detail-row-val { color: var(--t1); font-weight: 700; text-align: right; }

    .timeline-dot {
        width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0;
        margin-top: 3px;
    }
</style>
@endpush

@section('content')
@php
    $typeLabels = [
        'annual'   => ['label' => 'Cuti Tahunan',   'icon' => '📋', 'color' => 'var(--em)'],
        'sick'     => ['label' => 'Cuti Sakit',      'icon' => '🏥', 'color' => '#FCD34D'],
        'maternity'=> ['label' => 'Cuti Melahirkan', 'icon' => '👶', 'color' => '#F9A8D4'],
        'wedding'  => ['label' => 'Cuti Pernikahan', 'icon' => '💍', 'color' => '#FCA5A5'],
        'big_leave'=> ['label' => 'Cuti Besar',      'icon' => '🌴', 'color' => '#86EFAC'],
    ];
    $typeInfo = $typeLabels[$leave->leave_type] ?? ['label' => $leave->leave_type, 'icon' => '📝', 'color' => 'var(--t3)'];
@endphp

<div class="max-w-3xl mx-auto">

    {{-- Back + Status --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;flex-wrap:wrap;gap:0.75rem;">
        <a href="{{ route('leave.index') }}" style="display:inline-flex;align-items:center;gap:0.4rem;font-size:0.8rem;font-weight:600;color:var(--t3);text-decoration:none;"
           onmouseover="this.style.color='var(--em)'" onmouseout="this.style.color='var(--t3)'">
            <svg style="width:15px;height:15px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Daftar
        </a>
        <div style="display:flex;align-items:center;gap:0.5rem;">
            @if($leave->status === 'pending')     <span class="badge badge-warning">⏳ Menunggu Persetujuan</span>
            @elseif($leave->status === 'approved') <span class="badge badge-success">✓ Disetujui</span>
            @elseif($leave->status === 'rejected') <span class="badge badge-danger">✗ Ditolak</span>
            @endif
        </div>
    </div>

    <div class="card">
        {{-- Hero header --}}
        <div style="display:flex;align-items:center;gap:1rem;padding-bottom:1.25rem;border-bottom:1px solid var(--border-dim);margin-bottom:1.25rem;">
            <div style="width:52px;height:52px;border-radius:16px;background:rgba(99,102,241,0.12);border:1px solid rgba(99,102,241,0.2);display:flex;align-items:center;justify-content:center;font-size:1.6rem;flex-shrink:0;">
                {{ $typeInfo['icon'] }}
            </div>
            <div>
                <h2 style="font-size:1.05rem;font-weight:800;color:var(--t1);">{{ $typeInfo['label'] }}</h2>
                <p style="font-size:0.73rem;color:var(--t4);margin-top:0.15rem;">
                    Diajukan {{ $leave->created_at->diffForHumans() }} ·
                    <span style="color:{{ $typeInfo['color'] }};font-weight:600;">{{ $leave->duration }} hari kerja</span>
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Employee info --}}
            <div>
                <div class="detail-section-label">
                    <svg style="width:11px;height:11px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Karyawan
                </div>
                <div style="display:flex;align-items:center;gap:0.85rem;padding:0.9rem;background:var(--bg-elevated);border:1px solid var(--border-soft);border-radius:12px;">
                    <div class="avatar" style="width:44px;height:44px;font-size:0.8rem;overflow:hidden;flex-shrink:0;">
                        @if($leave->user->photo)
                            <img src="{{ $leave->user->photo_url }}" style="width:100%;height:100%;object-fit:cover;">
                        @else
                            {{ $leave->user->initials }}
                        @endif
                    </div>
                    <div>
                        <div style="font-size:0.88rem;font-weight:800;color:var(--t1);">{{ $leave->user->name }}</div>
                        <div style="font-size:0.72rem;color:var(--t3);margin-top:0.1rem;">{{ $leave->user->position?->name ?? '—' }}</div>
                        <div style="font-size:0.68rem;color:var(--t4);">{{ $leave->user->division?->name ?? '—' }}</div>
                    </div>
                </div>
            </div>

            {{-- Leave info --}}
            <div>
                <div class="detail-section-label">
                    <svg style="width:11px;height:11px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Informasi Cuti
                </div>
                <div class="detail-section" style="margin-bottom:0;">
                    <div class="detail-row">
                        <span class="detail-row-label">Jenis</span>
                        <span class="detail-row-val" style="color:{{ $typeInfo['color'] }};">{{ $typeInfo['label'] }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-row-label">Tanggal Mulai</span>
                        <span class="detail-row-val">{{ \Carbon\Carbon::parse($leave->start_date)->translatedFormat('d F Y') }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-row-label">Tanggal Selesai</span>
                        <span class="detail-row-val">{{ \Carbon\Carbon::parse($leave->end_date)->translatedFormat('d F Y') }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-row-label">Durasi</span>
                        <span class="detail-row-val" style="color:var(--em);">{{ $leave->duration }} hari kerja</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Reason --}}
        <div class="detail-section" style="margin-top:1rem;">
            <div class="detail-section-label">💬 Alasan Cuti</div>
            <p style="font-size:0.84rem;line-height:1.7;color:var(--t2);">{{ $leave->reason }}</p>
        </div>

        {{-- Emergency contact --}}
        @if($leave->emergency_contact)
        <div class="detail-section">
            <div class="detail-section-label">📞 Kontak Darurat</div>
            <span style="font-size:0.84rem;font-weight:700;color:var(--t1);">{{ $leave->emergency_contact }}</span>
        </div>
        @endif

        {{-- Attachment --}}
        @if($leave->attachment)
        <div class="detail-section">
            <div class="detail-section-label">📎 Lampiran Dokumen</div>
            <a href="{{ Storage::url($leave->attachment) }}" target="_blank" class="btn btn-secondary btn-sm" style="width:fit-content;">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Buka Lampiran
            </a>
        </div>
        @endif

        {{-- Approval info --}}
        @if($leave->status !== 'pending' && $leave->approvedBy)
        <div class="detail-section" style="border-color:{{ $leave->status === 'approved' ? 'rgba(16,185,129,0.2)' : 'rgba(239,68,68,0.2)' }};">
            <div class="detail-section-label" style="color:{{ $leave->status === 'approved' ? 'var(--em)' : 'var(--danger)' }};">
                {{ $leave->status === 'approved' ? '✓ Disetujui Oleh' : '✗ Ditolak Oleh' }}
            </div>
            <div style="display:flex;align-items:center;gap:0.75rem;">
                <div class="avatar" style="width:32px;height:32px;font-size:0.62rem;overflow:hidden;flex-shrink:0;">
                    @if($leave->approvedBy->photo)
                        <img src="{{ $leave->approvedBy->photo_url }}" style="width:100%;height:100%;object-fit:cover;">
                    @else {{ $leave->approvedBy->initials }} @endif
                </div>
                <div>
                    <div style="font-weight:700;color:var(--t1);font-size:0.85rem;">{{ $leave->approvedBy->name }}</div>
                    <div style="font-size:0.7rem;color:var(--t4);">{{ $leave->approved_at ? \Carbon\Carbon::parse($leave->approved_at)->translatedFormat('d F Y, H:i') : '—' }}</div>
                    @if($leave->rejection_reason)
                    <div style="margin-top:0.35rem;font-size:0.78rem;color:#FCA5A5;font-style:italic;">"{{ $leave->rejection_reason }}"</div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- Action buttons --}}
        @hasrole(['super_admin', 'hrd', 'manager'])
        @if($leave->status === 'pending')
        <div style="margin-top:1.25rem;padding-top:1rem;border-top:1px solid var(--border-dim);display:flex;gap:0.75rem;justify-content:flex-end;"
             x-data="{ showReject: false }">
            <form method="POST" action="{{ route('leave.approve', $leave) }}">
                @csrf
                <button type="submit" class="btn btn-success">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Setujui Cuti
                </button>
            </form>
            <button type="button" @click="showReject = true" class="btn btn-danger">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                Tolak
            </button>

            {{-- Reject modal --}}
            <div x-show="showReject" x-cloak
                 style="position:fixed;inset:0;background:rgba(7,16,36,0.85);backdrop-filter:blur(8px);z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;"
                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100">
                <div style="background:var(--bg-card);border:1px solid rgba(239,68,68,0.25);border-radius:20px;padding:1.75rem;max-width:420px;width:100%;box-shadow:var(--shadow-elevated);"
                     @click.away="showReject = false">
                    <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1.25rem;">
                        <div style="width:40px;height:40px;border-radius:12px;background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.25);display:flex;align-items:center;justify-content:center;">
                            <svg style="width:20px;height:20px;color:#FCA5A5;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </div>
                        <div>
                            <h4 style="font-size:0.95rem;font-weight:800;color:var(--t1);">Tolak Pengajuan Cuti</h4>
                            <p style="font-size:0.72rem;color:var(--t4);">Masukkan alasan penolakan</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('leave.reject', $leave) }}">
                        @csrf
                        <div class="form-group" style="margin-bottom:1rem;">
                            <label class="form-label">Alasan <span style="color:var(--danger);">*</span></label>
                            <textarea name="rejection_reason" class="form-control" rows="3" required placeholder="Jelaskan alasan penolakan..." style="resize:none;"></textarea>
                        </div>
                        <div style="display:flex;gap:0.5rem;">
                            <button type="button" @click="showReject = false" class="btn btn-secondary flex-1">Batal</button>
                            <button type="submit" class="btn btn-danger flex-1">Tolak Cuti</button>
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
