@extends('layouts.app')

@section('title', 'Detail Izin')
@section('page-title', 'Detail Izin')
@section('breadcrumb', 'Perizinan › Izin › Detail')

@push('styles')
<style>
    .perm-type-map {
        late_in: { icon: '🕐'; label: 'Izin Terlambat'; };
    }
    .perm-block {
        background: var(--bg-elevated); border: 1px solid var(--border-soft);
        border-radius: 12px; padding: 0.9rem 1rem;
    }
    .perm-block-label {
        font-size: 0.6rem; font-weight: 800; letter-spacing: 0.12em;
        text-transform: uppercase; color: var(--t5); margin-bottom: 0.4rem;
    }
    .perm-block-val {
        font-size: 0.88rem; font-weight: 800; color: var(--t1);
    }
</style>
@endpush

@section('content')
@php
    $typeMap = [
        'late_in'   => ['icon' => '🕐', 'label' => 'Izin Terlambat',     'color' => '#FCD34D'],
        'early_out' => ['icon' => '🏃', 'label' => 'Pulang Lebih Awal',  'color' => '#FDBA74'],
        'outside'   => ['icon' => '🗺', 'label' => 'Dinas Luar',         'color' => '#93C5FD'],
        'other'     => ['icon' => '📝', 'label' => 'Lainnya',            'color' => 'var(--t3)'],
    ];
    $t = $typeMap[$permission->type] ?? ['icon' => '📝', 'label' => $permission->type, 'color' => 'var(--t3)'];
@endphp

<div class="max-w-2xl mx-auto">

    {{-- Back + status --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;flex-wrap:wrap;gap:0.75rem;">
        <a href="{{ route('permission.index') }}" style="display:inline-flex;align-items:center;gap:0.4rem;font-size:0.8rem;font-weight:600;color:var(--t3);text-decoration:none;"
           onmouseover="this.style.color='var(--em)'" onmouseout="this.style.color='var(--t3)'">
            <svg style="width:15px;height:15px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Daftar
        </a>
        @if($permission->status === 'pending')     <span class="badge badge-warning">⏳ Menunggu Persetujuan</span>
        @elseif($permission->status === 'approved') <span class="badge badge-success">✓ Disetujui</span>
        @elseif($permission->status === 'rejected') <span class="badge badge-danger">✗ Ditolak</span>
        @endif
    </div>

    <div class="card">
        {{-- Hero header --}}
        <div style="display:flex;align-items:center;gap:1rem;padding-bottom:1.25rem;border-bottom:1px solid var(--border-dim);margin-bottom:1.25rem;">
            <div style="width:52px;height:52px;border-radius:16px;background:rgba(99,102,241,0.12);border:1px solid rgba(99,102,241,0.2);display:flex;align-items:center;justify-content:center;font-size:1.6rem;flex-shrink:0;">
                {{ $t['icon'] }}
            </div>
            <div>
                <h2 style="font-size:1.05rem;font-weight:800;color:var(--t1);">{{ $t['label'] }}</h2>
                <p style="font-size:0.73rem;color:var(--t4);margin-top:0.15rem;">
                    {{ \Carbon\Carbon::parse($permission->date)->translatedFormat('l, d F Y') }}
                    @if($permission->start_time && $permission->end_time)
                    · <span style="font-family:'JetBrains Mono',monospace;color:{{ $t['color'] }};font-weight:700;">{{ $permission->start_time }} – {{ $permission->end_time }}</span>
                    @endif
                </p>
            </div>
        </div>

        {{-- Employee card --}}
        <div style="display:flex;align-items:center;gap:0.85rem;padding:0.9rem 1rem;background:var(--bg-elevated);border:1px solid var(--border-soft);border-radius:12px;margin-bottom:1rem;">
            <div class="avatar" style="width:44px;height:44px;font-size:0.8rem;overflow:hidden;flex-shrink:0;">
                @if($permission->user->photo)
                    <img src="{{ $permission->user->photo_url }}" style="width:100%;height:100%;object-fit:cover;">
                @else {{ $permission->user->initials }} @endif
            </div>
            <div>
                <div style="font-size:0.9rem;font-weight:800;color:var(--t1);">{{ $permission->user->name }}</div>
                <div style="font-size:0.72rem;color:var(--t3);">{{ $permission->user->employee?->division?->name ?? $permission->user->division?->name ?? '—' }}</div>
            </div>
        </div>

        {{-- Detail grid --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;margin-bottom:0.75rem;">
            <div class="perm-block">
                <div class="perm-block-label">🏷 Jenis Izin</div>
                <div class="perm-block-val" style="color:{{ $t['color'] }};">{{ $t['label'] }}</div>
            </div>
            <div class="perm-block">
                <div class="perm-block-label">📅 Tanggal</div>
                <div class="perm-block-val">{{ \Carbon\Carbon::parse($permission->date)->format('d M Y') }}</div>
            </div>
            <div class="perm-block">
                <div class="perm-block-label">🕐 Jam Mulai</div>
                <div class="perm-block-val" style="font-family:'JetBrains Mono',monospace;color:var(--em);">{{ $permission->start_time ?? '—' }}</div>
            </div>
            <div class="perm-block">
                <div class="perm-block-label">🕔 Jam Selesai</div>
                <div class="perm-block-val" style="font-family:'JetBrains Mono',monospace;color:var(--t3);">{{ $permission->end_time ?? '—' }}</div>
            </div>
        </div>

        {{-- Alasan --}}
        <div class="perm-block" style="margin-bottom:0.75rem;">
            <div class="perm-block-label">💬 Alasan</div>
            <p style="font-size:0.84rem;color:var(--t2);line-height:1.7;margin-top:0.25rem;">{{ $permission->reason }}</p>
        </div>

        {{-- Lampiran --}}
        @if($permission->attachment)
        <div class="perm-block" style="margin-bottom:0.75rem;">
            <div class="perm-block-label">📎 Lampiran</div>
            <a href="{{ Storage::url($permission->attachment) }}" target="_blank" class="btn btn-secondary btn-sm" style="margin-top:0.4rem;width:fit-content;">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                Buka Lampiran
            </a>
        </div>
        @endif

        {{-- Approval info --}}
        @if($permission->status !== 'pending' && isset($permission->approvedBy))
        <div class="perm-block" style="border-color:{{ $permission->status === 'approved' ? 'rgba(16,185,129,0.2)' : 'rgba(239,68,68,0.2)' }};margin-bottom:0.75rem;">
            <div class="perm-block-label" style="color:{{ $permission->status === 'approved' ? 'var(--em)' : 'var(--danger)' }};">
                {{ $permission->status === 'approved' ? '✓ Disetujui Oleh' : '✗ Ditolak Oleh' }}
            </div>
            <div style="display:flex;align-items:center;gap:0.6rem;margin-top:0.3rem;">
                <div class="avatar" style="width:30px;height:30px;font-size:0.6rem;overflow:hidden;flex-shrink:0;">
                    @if($permission->approvedBy->photo)
                        <img src="{{ $permission->approvedBy->photo_url }}" style="width:100%;height:100%;object-fit:cover;">
                    @else {{ $permission->approvedBy->initials }} @endif
                </div>
                <div>
                    <div style="font-weight:700;color:var(--t1);font-size:0.83rem;">{{ $permission->approvedBy->name }}</div>
                    <div style="font-size:0.68rem;color:var(--t4);">{{ isset($permission->approved_at) ? \Carbon\Carbon::parse($permission->approved_at)->translatedFormat('d F Y, H:i') : '—' }}</div>
                    @if(!empty($permission->rejection_reason))
                    <div style="margin-top:0.3rem;font-size:0.77rem;color:#FCA5A5;font-style:italic;">"{{ $permission->rejection_reason }}"</div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- Action buttons --}}
        @hasrole(['super_admin','hrd','manager'])
        @if($permission->status === 'pending')
        <div x-data="{ showReject: false }" style="padding-top:1rem;border-top:1px solid var(--border-dim);display:flex;gap:0.5rem;justify-content:flex-end;">
            <form method="POST" action="{{ route('permission.approve', $permission) }}">
                @csrf
                <button type="submit" class="btn btn-success">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Setujui
                </button>
            </form>
            <button type="button" @click="showReject = true" class="btn btn-danger">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                Tolak
            </button>

            {{-- Reject modal --}}
            <div x-show="showReject" x-cloak
                 style="position:fixed;inset:0;background:rgba(7,16,36,0.85);backdrop-filter:blur(8px);z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;"
                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div style="background:var(--bg-card);border:1px solid rgba(239,68,68,0.25);border-radius:20px;padding:1.75rem;max-width:420px;width:100%;box-shadow:var(--shadow-elevated);"
                     @click.away="showReject = false">
                    <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1.25rem;">
                        <div style="width:40px;height:40px;border-radius:12px;background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.25);display:flex;align-items:center;justify-content:center;">
                            <svg style="width:20px;height:20px;color:#FCA5A5;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </div>
                        <div>
                            <h4 style="font-size:0.95rem;font-weight:800;color:var(--t1);">Tolak Pengajuan Izin</h4>
                            <p style="font-size:0.72rem;color:var(--t4);">Masukkan alasan penolakan</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('permission.reject', $permission) }}">
                        @csrf
                        <div class="form-group" style="margin-bottom:1rem;">
                            <label class="form-label">Alasan <span style="color:var(--danger);">*</span></label>
                            <textarea name="rejection_reason" class="form-control" rows="3" required placeholder="Jelaskan alasan penolakan..." style="resize:none;"></textarea>
                        </div>
                        <div style="display:flex;gap:0.5rem;">
                            <button type="button" @click="showReject = false" class="btn btn-secondary flex-1">Batal</button>
                            <button type="submit" class="btn btn-danger flex-1">Tolak Izin</button>
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
