@extends('layouts.app')

@section('title', $announcement->title)
@section('page-title', 'Detail Pengumuman')
@section('breadcrumb', 'Dokumen › Pengumuman › Detail')

@push('styles')
<style>
    .ann-body {
        font-size: 0.92rem;
        line-height: 1.85;
        color: var(--t2);
        white-space: pre-wrap;
    }
    .ann-body p { margin-bottom: 0.75rem; }

    .ann-meta-row {
        display: flex; align-items: center; gap: 0.5rem;
        font-size: 0.73rem; color: var(--t4);
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .ann-card { animation: fadeInUp 0.35s ease both; }
</style>
@endpush

@section('content')
@php
    $catMap = [
        'info'     => ['badge' => 'badge-info',    'icon' => 'ℹ️',  'label' => 'Informasi Umum'],
        'meeting'  => ['badge' => 'badge-purple',  'icon' => '🗓',  'label' => 'Rapat / Koordinasi'],
        'holiday'  => ['badge' => 'badge-danger',  'icon' => '🏖',  'label' => 'Libur Resmi'],
        'activity' => ['badge' => 'badge-success', 'icon' => '🎯',  'label' => 'Kegiatan Perusahaan'],
        'urgent'   => ['badge' => 'badge-orange',  'icon' => '🚨',  'label' => 'Mendesak'],
    ];
    $cat = $catMap[$announcement->category] ?? ['badge' => 'badge-gray', 'icon' => '📌', 'label' => ucfirst($announcement->category)];
@endphp

<div class="max-w-4xl mx-auto">

    {{-- Nav bar --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;flex-wrap:wrap;gap:0.75rem;">
        <a href="{{ route('announcements.index') }}"
           style="display:inline-flex;align-items:center;gap:0.4rem;font-size:0.8rem;font-weight:600;color:var(--t3);text-decoration:none;"
           onmouseover="this.style.color='var(--em)'" onmouseout="this.style.color='var(--t3)'">
            <svg style="width:15px;height:15px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Pengumuman
        </a>
        @if(auth()->user()->hasRole(['super_admin','hrd','manager']))
        <div style="display:flex;gap:0.5rem;">
            <a href="{{ route('announcements.edit', $announcement->id) }}"
               class="btn btn-secondary btn-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit
            </a>
            <form action="{{ route('announcements.destroy', $announcement->id) }}" method="POST" onsubmit="return confirm('Hapus pengumuman ini?')" class="inline">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Hapus
                </button>
            </form>
        </div>
        @endif
    </div>

    {{-- Main card --}}
    <div class="card ann-card" style="position:relative;overflow:hidden;{{ $announcement->is_pinned ? 'border-color:rgba(245,158,11,0.3);background:linear-gradient(145deg,var(--bg-card),rgba(245,158,11,0.03));' : '' }}">

        {{-- Pinned ribbon --}}
        @if($announcement->is_pinned)
        <div style="position:absolute;top:0;right:0;width:80px;height:80px;pointer-events:none;overflow:hidden;">
            <div style="position:absolute;top:14px;right:-22px;background:#D97706;color:#fff;font-size:8px;font-weight:900;text-align:center;letter-spacing:0.08em;padding:3px 0;width:90px;transform:rotate(45deg);">📌 PINNED</div>
        </div>
        @endif

        {{-- Category + badges --}}
        <div style="display:flex;flex-wrap:wrap;align-items:center;gap:0.5rem;margin-bottom:1rem;">
            <span class="badge {{ $cat['badge'] }}" style="font-size:0.75rem;">{{ $cat['icon'] }} {{ $cat['label'] }}</span>
            @if($announcement->is_pinned)
            <span class="badge badge-warning" style="font-size:0.72rem;">⭐ Penting</span>
            @endif
        </div>

        {{-- Title --}}
        <h1 style="font-size:1.5rem;font-weight:900;color:var(--t1);line-height:1.25;letter-spacing:-0.02em;margin-bottom:1.1rem;">
            {{ $announcement->title }}
        </h1>

        {{-- Meta --}}
        <div style="display:flex;flex-wrap:wrap;align-items:center;gap:1rem;padding:0.85rem;background:var(--bg-elevated);border:1px solid var(--border-soft);border-radius:12px;margin-bottom:1.5rem;">
            {{-- Author --}}
            <div class="ann-meta-row">
                <div class="avatar" style="width:24px;height:24px;font-size:0.55rem;overflow:hidden;flex-shrink:0;">
                    @if($announcement->user?->photo)
                        <img src="{{ $announcement->user->photo_url }}" style="width:100%;height:100%;object-fit:cover;">
                    @else {{ $announcement->user?->initials ?? 'A' }} @endif
                </div>
                <div>
                    <span style="font-weight:700;color:var(--t2);">{{ $announcement->user?->name ?? 'Administrator' }}</span>
                    <span style="margin:0 0.25rem;color:var(--t5);">·</span>
                    <span style="color:var(--t4);">{{ ucfirst($announcement->user?->roles?->first()?->name ?? 'Staff') }}</span>
                </div>
            </div>
            {{-- Date --}}
            <div class="ann-meta-row">
                <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>{{ \Carbon\Carbon::parse($announcement->published_at)->translatedFormat('d F Y, H:i') }} WIB</span>
            </div>
            {{-- Expired --}}
            @if($announcement->expired_at)
            <div class="ann-meta-row" style="color:#FCA5A5;">
                <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Aktif s/d {{ \Carbon\Carbon::parse($announcement->expired_at)->translatedFormat('d F Y, H:i') }} WIB</span>
            </div>
            @endif
        </div>

        {{-- Divider --}}
        <div style="height:1px;background:linear-gradient(90deg,var(--em-border),transparent);margin-bottom:1.5rem;border-radius:1px;"></div>

        {{-- Content body --}}
        <div class="ann-body">
            {!! nl2br(e($announcement->content)) !!}
        </div>

        {{-- Attachment --}}
        @if($announcement->attachment)
        <div style="margin-top:2rem;padding-top:1.25rem;border-top:1px solid var(--border-dim);">
            <div style="font-size:0.6rem;font-weight:800;letter-spacing:0.12em;text-transform:uppercase;color:var(--t5);margin-bottom:0.75rem;">📎 Lampiran Pendukung</div>
            <div style="display:flex;align-items:center;justify-content:space-between;padding:0.9rem 1rem;background:var(--bg-elevated);border:1px solid var(--border-soft);border-radius:14px;flex-wrap:wrap;gap:0.75rem;">
                <div style="display:flex;align-items:center;gap:0.85rem;">
                    <div style="width:44px;height:44px;border-radius:12px;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.2);display:flex;align-items:center;justify-content:center;font-size:1.3rem;flex-shrink:0;">
                        📄
                    </div>
                    <div>
                        <div style="font-size:0.82rem;font-weight:700;color:var(--t1);">Berkas Lampiran Pengumuman</div>
                        <div style="font-size:0.68rem;color:var(--t4);margin-top:0.1rem;">Klik tombol untuk membuka atau mengunduh</div>
                    </div>
                </div>
                <a href="{{ Storage::url($announcement->attachment) }}" target="_blank" class="btn btn-primary btn-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Unduh Lampiran
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
