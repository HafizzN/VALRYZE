@extends('layouts.app')

@section('title', 'Pengumuman Perusahaan')
@section('page-title', 'Pengumuman')
@section('breadcrumb', 'Dokumen › Pengumuman')

@push('styles')
<style>
    .ann-card {
        background: var(--bg-card);
        border: 1px solid var(--border-soft);
        border-radius: 18px;
        overflow: hidden;
        display: flex; flex-direction: column;
        transition: all 0.25s cubic-bezier(0.16,1,0.3,1);
        box-shadow: var(--shadow-card);
    }
    .ann-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-elevated);
        border-color: var(--em-border);
    }
    .ann-card-body { padding: 1.25rem 1.25rem 1rem; flex: 1; }
    .ann-card-footer {
        padding: 0.75rem 1.25rem;
        border-top: 1px solid var(--border-dim);
        display: flex; align-items: center; justify-content: space-between;
    }

    .pinned-card {
        position: relative; overflow: hidden;
        background: linear-gradient(135deg, var(--bg-card) 0%, rgba(245,158,11,0.04) 100%);
        border: 1px solid rgba(245,158,11,0.25);
        border-radius: 18px; padding: 1.5rem;
        display: flex; flex-direction: column;
        transition: all 0.25s cubic-bezier(0.16,1,0.3,1);
        box-shadow: var(--shadow-card);
    }
    .pinned-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
        background: linear-gradient(90deg, #F59E0B, #FCD34D, transparent);
    }
    .pinned-card:hover {
        transform: translateY(-3px);
        border-color: rgba(245,158,11,0.45);
        box-shadow: 0 16px 40px rgba(245,158,11,0.1), var(--shadow-card);
    }

    .pinned-ribbon {
        position: absolute; top: 12px; right: -18px;
        background: #F59E0B; color: #1a1a1a;
        font-size: 0.55rem; font-weight: 800;
        letter-spacing: 0.08em; text-transform: uppercase;
        padding: 0.2rem 0; width: 72px; text-align: center;
        transform: rotate(45deg);
    }

    .section-divider {
        font-size: 0.62rem; font-weight: 700; letter-spacing: 0.12em;
        text-transform: uppercase; color: var(--t5);
        display: flex; align-items: center; gap: 0.75rem;
        margin: 0 0 1rem;
    }
    .section-divider::after {
        content: ''; flex: 1; height: 1px;
        background: var(--border-dim);
    }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .ann-card, .pinned-card { animation: fadeUp 0.35s ease forwards; }
</style>
@endpush

@section('content')
@php
    $pinned  = $announcements->where('is_pinned', true);
    $regular = $announcements->where('is_pinned', false);

    $catMap = [
        'info'     => ['label' => 'Info',      'badge' => 'badge-info'],
        'meeting'  => ['label' => 'Rapat',     'badge' => 'badge-purple'],
        'holiday'  => ['label' => 'Libur',     'badge' => 'badge-danger'],
        'activity' => ['label' => 'Kegiatan',  'badge' => 'badge-success'],
        'urgent'   => ['label' => 'Mendesak',  'badge' => 'badge-orange'],
        'other'    => ['label' => 'Lainnya',   'badge' => 'badge-gray'],
    ];
@endphp

{{-- Header --}}
<div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;">
    <div>
        <h2 style="font-size:1.1rem;font-weight:800;color:var(--t1);letter-spacing:-0.01em;">Pengumuman & Informasi Terbaru</h2>
        <p style="font-size:0.78rem;color:var(--t4);margin-top:0.25rem;">Tetap terhubung dengan informasi resmi dari manajemen perusahaan</p>
    </div>
    @if(auth()->user()->hasRole(['super_admin', 'hrd', 'manager']))
    <a href="{{ route('announcements.create') }}" class="btn btn-primary">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Buat Pengumuman
    </a>
    @endif
</div>

{{-- ━━━━━━━━━━━━━━━━━━━━━━ PINNED ━━━━━━━━━━━━━━━━━━━━━━ --}}
@if($pinned->count() > 0)
<div class="section-divider" style="margin-bottom:1rem;">
    <svg style="width:13px;height:13px;color:#F59E0B;" fill="currentColor" viewBox="0 0 20 20"><path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"/></svg>
    Informasi Penting · Disematkan
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
    @foreach($pinned as $idx => $ann)
    @php $cat = $catMap[$ann->category] ?? ['label' => $ann->category, 'badge' => 'badge-gray']; @endphp
    <div class="pinned-card" style="animation-delay:{{ $idx * 0.07 }}s;">
        <div class="pinned-ribbon">PINNED</div>

        <div style="display:flex;align-items:center;gap:0.6rem;margin-bottom:0.75rem;">
            <span class="badge {{ $cat['badge'] }}">{{ $cat['label'] }}</span>
            <span style="font-size:0.68rem;color:var(--t4);">
                {{ \Carbon\Carbon::parse($ann->published_at)->translatedFormat('d F Y') }}
            </span>
        </div>

        <h4 style="font-size:1rem;font-weight:800;color:var(--t1);line-height:1.35;margin-bottom:0.6rem;">
            <a href="{{ route('announcements.show', $ann->id) }}"
               style="text-decoration:none;color:inherit;transition:color 0.2s;"
               onmouseover="this.style.color='var(--em)'" onmouseout="this.style.color='inherit'">
                {{ $ann->title }}
            </a>
        </h4>

        <p style="font-size:0.8rem;color:var(--t3);line-height:1.65;flex:1;margin-bottom:1rem;">
            {{ Str::limit(strip_tags($ann->content), 150) }}
        </p>

        <div style="display:flex;align-items:center;justify-content:space-between;padding-top:0.75rem;border-top:1px solid rgba(245,158,11,0.15);">
            <div style="display:flex;align-items:center;gap:0.6rem;">
                <div class="avatar" style="width:22px;height:22px;font-size:0.55rem;overflow:hidden;flex-shrink:0;">
                    @if($ann->user?->photo)
                        <img src="{{ $ann->user->photo_url }}" alt="{{ $ann->user->name }}" style="width:100%;height:100%;object-fit:cover;">
                    @else
                        {{ $ann->user?->initials ?? 'A' }}
                    @endif
                </div>
                <span style="font-size:0.7rem;color:var(--t4);">{{ $ann->user?->name ?? 'Admin' }}</span>
            </div>
            <a href="{{ route('announcements.show', $ann->id) }}"
               style="font-size:0.75rem;font-weight:700;color:#F59E0B;text-decoration:none;display:flex;align-items:center;gap:0.3rem;"
               onmouseover="this.style.opacity='0.75'" onmouseout="this.style.opacity='1'">
                Selengkapnya
                <svg style="width:13px;height:13px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- ━━━━━━━━━━━━━━━━━━━━━━ REGULAR ━━━━━━━━━━━━━━━━━━━━━━ --}}
<div class="section-divider">
    <svg style="width:13px;height:13px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
    Semua Pengumuman
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-5">
    @forelse($regular as $idx => $ann)
    @php $cat = $catMap[$ann->category] ?? ['label' => $ann->category, 'badge' => 'badge-gray']; @endphp
    <div class="ann-card" style="animation-delay:{{ ($pinned->count() + $idx) * 0.06 }}s;">
        <div class="ann-card-body">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.6rem;">
                <span class="badge {{ $cat['badge'] }}">{{ $cat['label'] }}</span>
                <span style="font-size:0.65rem;color:var(--t4);">{{ \Carbon\Carbon::parse($ann->published_at ?? $ann->created_at)->diffForHumans() }}</span>
            </div>
            <h4 style="font-size:0.9rem;font-weight:800;color:var(--t1);line-height:1.35;margin-bottom:0.5rem;">
                <a href="{{ route('announcements.show', $ann->id) }}"
                   style="text-decoration:none;color:inherit;"
                   onmouseover="this.style.color='var(--em)'" onmouseout="this.style.color='inherit'">
                    {{ Str::limit($ann->title, 60) }}
                </a>
            </h4>
            <p style="font-size:0.76rem;color:var(--t3);line-height:1.6;">
                {{ Str::limit(strip_tags($ann->content), 110) }}
            </p>
        </div>
        <div class="ann-card-footer">
            <div style="display:flex;align-items:center;gap:0.5rem;">
                <div class="avatar" style="width:20px;height:20px;font-size:0.52rem;overflow:hidden;flex-shrink:0;">
                    @if($ann->user?->photo)
                        <img src="{{ $ann->user->photo_url }}" alt="{{ $ann->user->name }}" style="width:100%;height:100%;object-fit:cover;">
                    @else
                        {{ $ann->user?->initials ?? 'A' }}
                    @endif
                </div>
                <span style="font-size:0.67rem;color:var(--t4);">{{ Str::limit($ann->user?->name ?? 'Admin', 18) }}</span>
            </div>
            <a href="{{ route('announcements.show', $ann->id) }}"
               style="font-size:0.72rem;font-weight:700;color:var(--em);text-decoration:none;display:flex;align-items:center;gap:0.25rem;"
               onmouseover="this.style.opacity='0.75'" onmouseout="this.style.opacity='1'">
                Baca
                <svg style="width:12px;height:12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>
    @empty
    @if($pinned->count() == 0)
    <div class="col-span-3 card" style="text-align:center;padding:4rem;color:var(--t4);">
        <div style="font-size:2.5rem;margin-bottom:1rem;">📢</div>
        <div style="font-weight:700;color:var(--t3);">Tidak ada pengumuman aktif</div>
        <div style="font-size:0.75rem;margin-top:0.35rem;">Pengumuman dari manajemen akan muncul di sini</div>
    </div>
    @endif
    @endforelse
</div>

@if($announcements->hasPages())
<div style="margin-top:2rem;">{{ $announcements->links() }}</div>
@endif

@endsection
