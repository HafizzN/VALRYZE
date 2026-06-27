@extends('layouts.app')

@section('title', $announcement->title)
@section('page-title', 'Detail Pengumuman')
@section('breadcrumb', 'Dokumen / Pengumuman / Detail')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Action Header & Controls -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <a href="{{ route('announcements.index') }}" class="btn btn-secondary btn-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Pengumuman
        </a>

        @if(auth()->user()->hasRole(['super_admin', 'hrd', 'manager']))
            <div class="flex gap-2">
                <a href="{{ route('announcements.edit', $announcement->id) }}" class="btn btn-secondary btn-sm text-emerald-700">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>
                <form action="{{ route('announcements.destroy', $announcement->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengumuman ini?')" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Hapus
                    </button>
                </form>
            </div>
        @endif
    </div>

    <!-- Announcement Detail Card -->
    <div class="card relative overflow-hidden">
        <!-- Pinned Ribbon/Badge -->
        @if($announcement->is_pinned)
            <div class="absolute top-0 right-0 w-20 h-20 pointer-events-none">
                <div class="absolute top-3 right-[-24px] bg-amber-500 text-slate-900 text-[9px] font-bold text-center uppercase py-0.5 w-24 rotate-45">PINNED</div>
            </div>
        @endif

        <div class="space-y-6">
            <!-- Meta Header -->
            <div class="border-b border-slate-200/80 pb-4 space-y-3">
                <div class="flex flex-wrap items-center gap-2">
                    @switch($announcement->category)
                        @case('info')
                            <span class="badge badge-info">Informasi Umum</span>
                            @break
                        @case('meeting')
                            <span class="badge badge-purple">Rapat / Koordinasi</span>
                            @break
                        @case('holiday')
                            <span class="badge badge-danger">Libur Resmi</span>
                            @break
                        @case('activity')
                            <span class="badge badge-success">Kegiatan Perusahaan</span>
                            @break
                        @default
                            <span class="badge badge-gray">Lainnya</span>
                    @endswitch

                    @if($announcement->is_pinned)
                        <span class="badge badge-warning flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/></svg>
                            Penting
                        </span>
                    @endif
                </div>

                <h1 class="text-xl md:text-2xl font-bold text-slate-800 leading-tight">
                    {{ $announcement->title }}
                </h1>

                <!-- Publisher and Date Details -->
                <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500 pt-1">
                    <div class="flex items-center gap-2">
                        <div class="avatar text-[10px] w-6 h-6" style="overflow: hidden;">
                            @if($announcement->user?->photo)
                                <img src="{{ $announcement->user->photo_url }}" alt="{{ $announcement->user->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                {{ $announcement->user?->initials ?? 'A' }}
                            @endif
                        </div>
                        <div>
                            <span class="font-medium text-slate-700">{{ $announcement->user?->name ?? 'Administrator' }}</span>
                            <span class="mx-1.5">•</span>
                            <span class="text-slate-500">{{ $announcement->user?->roles?->first()?->name ?? 'Staff' }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>Diterbitkan: {{ \Carbon\Carbon::parse($announcement->published_at)->isoFormat('D MMMM Y - HH:mm') }} WIB</span>
                    </div>
                    @if($announcement->expired_at)
                        <div class="flex items-center gap-1.5 text-red-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Aktif s/d: {{ \Carbon\Carbon::parse($announcement->expired_at)->isoFormat('D MMMM Y - HH:mm') }} WIB</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Content Body -->
            <div class="prose  max-w-none text-slate-700 leading-relaxed text-sm md:text-base whitespace-pre-wrap font-sans">
                {!! nl2br(e($announcement->content)) !!}
            </div>

            <!-- Attachment Section (if any file is uploaded) -->
            @if($announcement->attachment)
                <div class="mt-8 border-t border-slate-200/80 pt-6 space-y-3">
                    <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wider">Lampiran Pendukung</h3>
                    
                    <div class="flex items-center justify-between p-4 bg-slate-100/30 rounded-lg border border-slate-200">
                        <div class="flex items-center gap-3">
                            <div class="p-2.5 rounded bg-slate-50 border border-slate-200 text-red-400">
                                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="text-xs font-semibold text-slate-800">Berkas Lampiran Pengumuman</div>
                                <div class="text-[10px] text-slate-500">Unduh berkas PDF atau Gambar pendukung untuk detail selengkapnya</div>
                            </div>
                        </div>
                        <a href="{{ Storage::url($announcement->attachment) }}" target="_blank" class="btn btn-primary btn-sm">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Unduh Lampiran
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
