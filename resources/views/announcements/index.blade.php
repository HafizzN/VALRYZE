@extends('layouts.app')

@section('title', 'Pengumuman Perusahaan')
@section('page-title', 'Pengumuman')
@section('breadcrumb', 'Dokumen / Pengumuman')

@section('content')
<div class="space-y-6">
    <!-- Action Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Pengumuman & Informasi Terbaru</h2>
            <p class="text-xs text-slate-500">Tetap terhubung dengan informasi dan kegiatan resmi dari manajemen perusahaan</p>
        </div>
        <div>
            @if(auth()->user()->hasRole(['super_admin', 'hrd', 'manager']))
                <a href="{{ route('announcements.create') }}" class="btn btn-primary">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Buat Pengumuman
                </a>
            @endif
        </div>
    </div>

    <!-- Pinned Announcements Section (if any are pinned) -->
    @php $pinned = $announcements->where('is_pinned', true); @endphp
    @if($pinned->count() > 0)
        <div class="space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-500 flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"/></svg>
                Informasi Penting / Pinned
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($pinned as $announcement)
                    <div class="card border-amber-200 bg-amber-50 relative overflow-hidden flex flex-col justify-between min-h-[180px]">
                        <!-- Gold Ribbon -->
                        <div class="absolute top-0 right-0 w-16 h-16 pointer-events-none">
                            <div class="absolute top-2 right-[-20px] bg-amber-500 text-slate-900 text-[8px] font-bold text-center uppercase py-0.5 w-20 rotate-45">PINNED</div>
                        </div>

                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                @switch($announcement->category)
                                    @case('info')
                                        <span class="badge badge-info">Info</span>
                                        @break
                                    @case('meeting')
                                        <span class="badge badge-purple">Rapat</span>
                                        @break
                                    @case('holiday')
                                        <span class="badge badge-danger">Libur</span>
                                        @break
                                    @case('activity')
                                        <span class="badge badge-success">Kegiatan</span>
                                        @break
                                    @default
                                        <span class="badge badge-gray">Lainnya</span>
                                @endswitch
                                <span class="text-[10px] text-slate-500">{{ \Carbon\Carbon::parse($announcement->published_at)->format('d M Y') }}</span>
                            </div>

                            <h4 class="text-base font-bold text-slate-800 line-clamp-1 mb-2">
                                <a href="{{ route('announcements.show', $announcement->id) }}" class="hover:text-amber-600 transition">
                                    {{ $announcement->title }}
                                </a>
                            </h4>
                            <p class="text-xs text-slate-600 line-clamp-3 leading-relaxed mb-4">
                                {{ Str::limit(strip_tags($announcement->content), 140) }}
                            </p>
                        </div>

                        <div class="flex items-center justify-between border-t border-slate-200/60 pt-3 mt-auto">
                            <div class="flex items-center gap-2">
                                <div class="avatar text-[9px] w-5 h-5">{{ $announcement->user->initials ?? 'A' }}</div>
                                <span class="text-[10px] text-slate-500">{{ $announcement->user->name ?? 'Admin' }}</span>
                            </div>
                            <a href="{{ route('announcements.show', $announcement->id) }}" class="text-xs font-semibold text-amber-500 hover:text-amber-600 flex items-center gap-1">
                                Selengkapnya
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Regular Announcements Grid -->
    <div class="space-y-3 pt-4">
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Semua Pengumuman</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @forelse($announcements->where('is_pinned', false) as $announcement)
                <div class="card flex flex-col justify-between min-h-[200px]">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            @switch($announcement->category)
                                @case('info')
                                    <span class="badge badge-info">Info</span>
                                    @break
                                @case('meeting')
                                    <span class="badge badge-purple">Rapat</span>
                                    @break
                                @case('holiday')
                                    <span class="badge badge-danger">Libur</span>
                                    @break
                                @case('activity')
                                    <span class="badge badge-success">Kegiatan</span>
                                    @break
                                @default
                                    <span class="badge badge-gray">Lainnya</span>
                            @endswitch
                            <span class="text-[10px] text-slate-500">{{ \Carbon\Carbon::parse($announcement->published_at)->format('d M Y') }}</span>
                        </div>

                        <h4 class="text-sm font-bold text-slate-800 line-clamp-2 mb-2">
                            <a href="{{ route('announcements.show', $announcement->id) }}" class="hover:text-emerald-700 transition">
                                {{ $announcement->title }}
                            </a>
                        </h4>
                        <p class="text-xs text-slate-600 line-clamp-3 leading-relaxed mb-4">
                            {{ Str::limit(strip_tags($announcement->content), 120) }}
                        </p>
                    </div>

                    <div class="flex items-center justify-between border-t border-slate-200/60 pt-3 mt-auto">
                        <div class="flex items-center gap-2">
                            <div class="avatar text-[9px] w-5 h-5">{{ $announcement->user->initials ?? 'A' }}</div>
                            <span class="text-[10px] text-slate-500">{{ $announcement->user->name ?? 'Admin' }}</span>
                        </div>
                        <a href="{{ route('announcements.show', $announcement->id) }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-600 flex items-center gap-1">
                            Baca
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
            @empty
                @if($pinned->count() == 0)
                    <div class="col-span-1 md:col-span-3 card text-center py-12 text-slate-500">
                        <svg class="w-12 h-12 mx-auto text-slate-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        Tidak ada pengumuman yang aktif saat ini.
                    </div>
                @endif
            @endforelse
        </div>

        @if($announcements->hasPages())
            <div class="mt-6">
                {{ $announcements->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
