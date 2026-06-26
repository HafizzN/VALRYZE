@extends('layouts.app')

@section('title', 'Pusat Notifikasi')
@section('page-title', 'Notifikasi')
@section('breadcrumb', 'Notifikasi / Pusat Notifikasi')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Kotak Masuk Notifikasi</h2>
            <p class="text-xs text-slate-500">Pantau semua aktivitas pengajuan, persetujuan, dan informasi resmi terbaru Anda</p>
        </div>
        <div>
            @php $unread = $notifications->where('read_at', null)->count(); @endphp
            @if($unread > 0)
                <button onclick="markAllAsRead()" class="btn btn-primary btn-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Tandai Semua Dibaca
                </button>
            @endif
        </div>
    </div>

    <!-- Notifications List Card -->
    <div class="card space-y-4">
        <div class="flex items-center justify-between border-b border-slate-200 pb-2">
            <h3 class="text-sm font-bold text-slate-700">Pemberitahuan Anda</h3>
            <span class="text-[10px] text-emerald-700 font-semibold">{{ $unread }} Belum Dibaca</span>
        </div>

        <div class="divide-y divide-slate-800/80">
            @forelse($notifications as $notif)
                <div class="py-4 flex gap-4 items-start relative transition hover:bg-slate-50/10 rounded px-2 {{ $notif->read_at ? 'opacity-70' : 'bg-indigo-950/5 border-l-2 border-indigo-500 pl-3' }}">
                    <!-- Status Icon -->
                    <div class="p-2 rounded-lg text-xs flex-shrink-0 {{ $notif->read_at ? 'bg-slate-50 text-slate-500' : 'bg-indigo-500/10 text-emerald-700' }}">
                        @if(Str::contains(strtolower($notif->title), ['setuju', 'approve', 'sukses']))
                            <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @elseif(Str::contains(strtolower($notif->title), ['tolak', 'reject', 'gagal']))
                            <svg class="w-5 h-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @else
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        @endif
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0 space-y-1">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-bold {{ $notif->read_at ? 'text-slate-600' : 'text-slate-800' }}">
                                {{ $notif->title }}
                            </h4>
                            <span class="text-[10px] text-slate-500 font-mono">{{ $notif->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-xs text-slate-600 leading-relaxed">{{ $notif->message }}</p>
                    </div>

                    <!-- Action Mark Read -->
                    @if(!$notif->read_at)
                        <button onclick="markAsRead({{ $notif->id }})" class="text-[10px] text-emerald-700 hover:text-emerald-600 font-semibold flex-shrink-0 self-center border border-emerald-200 bg-indigo-500/5 px-2.5 py-1 rounded transition">
                            Tandai Dibaca
                        </button>
                    @endif
                </div>
            @empty
                <div class="text-center py-16 text-slate-500 space-y-2">
                    <svg class="w-12 h-12 mx-auto text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <p class="text-xs">Kotak masuk notifikasi Anda kosong.</p>
                </div>
            @endforelse
        </div>

        @if($notifications->hasPages())
            <div class="mt-6 border-t border-slate-200/80 pt-4">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>

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
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(err => console.error(err));
    }

    function markAllAsRead() {
        fetch('/notifications/read-all', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(err => console.error(err));
    }
</script>
@endsection
