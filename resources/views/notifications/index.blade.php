@extends('layouts.app')

@section('title', 'Pusat Notifikasi')
@section('page-title', 'Notifikasi')
@section('breadcrumb', 'Notifikasi › Pusat Notifikasi')

@push('styles')
<style>
    .notif-item {
        display: flex; align-items: flex-start; gap: 1rem;
        padding: 1rem 1.25rem;
        border-radius: 14px;
        transition: all 0.2s ease;
        position: relative;
        border: 1px solid transparent;
        cursor: default;
    }
    .notif-item.unread {
        background: linear-gradient(135deg, var(--bg-elevated) 0%, rgba(6,182,212,0.03) 100%);
        border-color: rgba(6,182,212,0.12);
        border-left: 3px solid var(--em);
    }
    .notif-item.read {
        background: var(--bg-card);
        border-color: var(--border-dim);
        opacity: 0.72;
    }
    .notif-item.unread:hover {
        background: var(--bg-hover);
        border-color: var(--em-border);
        opacity: 1;
    }
    .notif-item.read:hover {
        background: var(--bg-elevated);
        opacity: 0.9;
    }

    .notif-icon {
        width: 40px; height: 40px; border-radius: 12px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem;
    }

    .notif-dot {
        width: 7px; height: 7px; border-radius: 50%;
        background: var(--em);
        box-shadow: 0 0 6px var(--em);
        flex-shrink: 0; margin-top: 0.5rem;
    }

    .mark-read-btn {
        font-size: 0.68rem; font-weight: 700;
        color: var(--em); border: 1px solid var(--em-border);
        background: var(--em-ghost);
        padding: 0.25rem 0.65rem; border-radius: 99px;
        cursor: pointer; flex-shrink: 0; align-self: center;
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    .mark-read-btn:hover {
        background: var(--em); color: #fff;
        box-shadow: 0 0 12px var(--em-glow);
    }

    .notif-separator {
        font-size: 0.62rem; font-weight: 700; letter-spacing: 0.12em;
        text-transform: uppercase; color: var(--t5);
        padding: 0.5rem 0 0.35rem;
        border-bottom: 1px solid var(--border-dim);
        margin-bottom: 0.5rem;
    }

    @keyframes notifSlideIn {
        from { opacity: 0; transform: translateX(-8px); }
        to   { opacity: 1; transform: translateX(0); }
    }
    .notif-item { animation: notifSlideIn 0.25s ease forwards; }
</style>
@endpush

@section('content')
@php
    $unread = $notifications->where('read_at', null)->count();
@endphp

<div class="max-w-3xl mx-auto">

    {{-- ━━━━━━━━━━━━━━━━━━━━━━ HEADER ━━━━━━━━━━━━━━━━━━━━━━ --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;">
        <div>
            <h2 style="font-size:1.1rem;font-weight:800;color:var(--t1);letter-spacing:-0.01em;">
                Pusat Notifikasi
                @if($unread > 0)
                <span style="display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;border-radius:99px;background:var(--danger);color:#fff;font-size:0.6rem;font-weight:800;margin-left:0.4rem;box-shadow:0 0 8px rgba(239,68,68,0.5);animation:pulse 2s infinite;">{{ $unread }}</span>
                @endif
            </h2>
            <p style="font-size:0.78rem;color:var(--t4);margin-top:0.25rem;">
                @if($unread > 0)
                    <span style="color:var(--em);font-weight:600;">{{ $unread }} belum dibaca</span> dari {{ $notifications->total() }} notifikasi
                @else
                    Semua notifikasi sudah dibaca · {{ $notifications->total() }} total
                @endif
            </p>
        </div>
        @if($unread > 0)
        <button onclick="markAllAsRead()" class="btn btn-ghost btn-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Tandai Semua Dibaca
        </button>
        @endif
    </div>

    {{-- ━━━━━━━━━━━━━━━━━━━━━━ NOTIFICATIONS ━━━━━━━━━━━━━━━━━━━━━━ --}}
    <div class="card" style="padding:1.25rem;">
        @forelse($notifications as $idx => $notif)
            @php
                $isUnread = !$notif->read_at;

                // Determine icon & color by keywords in title/type
                $title_lc = strtolower($notif->title . ' ' . ($notif->type ?? ''));
                if (str_contains($title_lc, 'setuju') || str_contains($title_lc, 'approve') || str_contains($title_lc, 'sukses')) {
                    $icon = '✅'; $iconBg = 'rgba(16,185,129,0.12)';
                } elseif (str_contains($title_lc, 'tolak') || str_contains($title_lc, 'reject') || str_contains($title_lc, 'gagal')) {
                    $icon = '❌'; $iconBg = 'rgba(239,68,68,0.12)';
                } elseif (str_contains($title_lc, 'ulang tahun') || str_contains($title_lc, 'birthday') || str_contains($title_lc, 'cake')) {
                    $icon = '🎂'; $iconBg = 'rgba(236,72,153,0.12)';
                } elseif (str_contains($title_lc, 'lembur') || str_contains($title_lc, 'overtime')) {
                    $icon = '⏱️'; $iconBg = 'rgba(167,139,250,0.12)';
                } elseif (str_contains($title_lc, 'cuti') || str_contains($title_lc, 'leave')) {
                    $icon = '📋'; $iconBg = 'rgba(6,182,212,0.12)';
                } elseif (str_contains($title_lc, 'izin') || str_contains($title_lc, 'permission')) {
                    $icon = '📝'; $iconBg = 'rgba(245,158,11,0.12)';
                } elseif (str_contains($title_lc, 'pengumuman') || str_contains($title_lc, 'announcement')) {
                    $icon = '📢'; $iconBg = 'rgba(59,130,246,0.12)';
                } elseif (str_contains($title_lc, 'payroll') || str_contains($title_lc, 'gaji')) {
                    $icon = '💰'; $iconBg = 'rgba(16,185,129,0.12)';
                } else {
                    $icon = '🔔'; $iconBg = 'rgba(148,163,184,0.1)';
                }

                // Date separator
                $currentDate = \Carbon\Carbon::parse($notif->created_at)->format('Y-m-d');
                $prevDate = $idx > 0 ? \Carbon\Carbon::parse($notifications[$idx - 1]->created_at)->format('Y-m-d') : null;
                $showSeparator = $currentDate !== $prevDate;
                $separatorLabel = \Carbon\Carbon::parse($notif->created_at)->isToday() ? 'Hari Ini'
                    : (\Carbon\Carbon::parse($notif->created_at)->isYesterday() ? 'Kemarin'
                    : \Carbon\Carbon::parse($notif->created_at)->translatedFormat('d F Y'));
            @endphp

            @if($showSeparator)
            <div class="notif-separator" style="margin-top: {{ $idx > 0 ? '1rem' : '0' }};">
                {{ $separatorLabel }}
            </div>
            @endif

            <div class="notif-item {{ $isUnread ? 'unread' : 'read' }}" style="animation-delay: {{ $idx * 0.04 }}s;">
                {{-- Unread dot --}}
                @if($isUnread)
                <div class="notif-dot"></div>
                @else
                <div style="width:7px;flex-shrink:0;"></div>
                @endif

                {{-- Icon --}}
                <div class="notif-icon" style="background: {{ $iconBg }};">
                    {{ $icon }}
                </div>

                {{-- Content --}}
                <div style="flex:1;min-width:0;">
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:0.5rem;flex-wrap:wrap;margin-bottom:0.2rem;">
                        <h4 style="font-size:0.85rem;font-weight:{{ $isUnread ? '800' : '600' }};color:{{ $isUnread ? 'var(--t1)' : 'var(--t3)' }};line-height:1.3;">
                            {{ $notif->title }}
                        </h4>
                        <span style="font-size:0.65rem;color:var(--t5);font-weight:500;white-space:nowrap;flex-shrink:0;">
                            {{ $notif->created_at->format('H:i') }}
                        </span>
                    </div>
                    <p style="font-size:0.78rem;color:{{ $isUnread ? 'var(--t2)' : 'var(--t4)' }};line-height:1.55;margin:0;">
                        {{ $notif->message }}
                    </p>
                    @if($notif->url)
                    <a href="{{ $notif->url }}" onclick="@if($isUnread)markAsRead({{ $notif->id }})@endif"
                       style="display:inline-flex;align-items:center;gap:0.3rem;margin-top:0.5rem;font-size:0.72rem;font-weight:700;color:var(--em);text-decoration:none;"
                       onmouseover="this.style.opacity='0.75'" onmouseout="this.style.opacity='1'">
                        Lihat Detail
                        <svg style="width:11px;height:11px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    @endif
                </div>

                {{-- Mark read button --}}
                @if($isUnread)
                <button class="mark-read-btn" onclick="markAsRead({{ $notif->id }})">Dibaca</button>
                @endif
            </div>
        @empty
            <div style="text-align:center;padding:4rem 2rem;color:var(--t4);">
                <div style="font-size:3rem;margin-bottom:1rem;opacity:0.5;">🔔</div>
                <div style="font-size:0.9rem;font-weight:700;color:var(--t3);">Tidak ada notifikasi</div>
                <div style="font-size:0.75rem;margin-top:0.35rem;">Notifikasi pengajuan, persetujuan, dan info sistem akan muncul di sini</div>
            </div>
        @endforelse

        @if($notifications->hasPages())
        <div style="margin-top:1.5rem;padding-top:1rem;border-top:1px solid var(--border-dim);">
            {{ $notifications->links() }}
        </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
function markAsRead(id) {
    fetch(`/notifications/${id}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => { if (data.success) location.reload(); })
    .catch(err => console.error(err));
}

function markAllAsRead() {
    const btn = event.currentTarget;
    btn.disabled = true;
    btn.style.opacity = '0.6';
    fetch('/notifications/read-all', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => { if (data.success) location.reload(); })
    .catch(err => { console.error(err); btn.disabled = false; btn.style.opacity = '1'; });
}
</script>
@endpush
