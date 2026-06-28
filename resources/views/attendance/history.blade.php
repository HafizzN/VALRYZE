@extends('layouts.app')

@section('title', 'Riwayat Absensi')
@section('page-title', 'Riwayat Absensi')
@section('breadcrumb', 'Absensi › Riwayat')

@push('styles')
<style>
    .summary-strip {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
        gap: 0.75rem; margin-bottom: 1.25rem;
    }
    .summary-chip {
        display: flex; flex-direction: column; align-items: center;
        padding: 0.85rem 0.75rem;
        background: var(--bg-card);
        border: 1px solid var(--border-soft);
        border-radius: 14px;
        box-shadow: var(--shadow-card);
        transition: all 0.2s ease;
    }
    .summary-chip:hover { transform: translateY(-2px); }
    .summary-chip-val {
        font-size: 1.5rem; font-weight: 800;
        line-height: 1; letter-spacing: -0.04em;
    }
    .summary-chip-label {
        font-size: 0.65rem; font-weight: 600;
        color: var(--t4); margin-top: 0.2rem;
        text-transform: uppercase; letter-spacing: 0.06em;
    }

    /* Photo thumb in table */
    .photo-thumb {
        width: 32px; height: 32px; border-radius: 8px;
        object-fit: cover; cursor: zoom-in;
        border: 1.5px solid var(--border-soft);
        transition: all 0.2s ease;
    }
    .photo-thumb:hover {
        transform: scale(1.15);
        border-color: var(--em);
        box-shadow: 0 0 12px var(--em-glow);
    }

    /* Lightbox overlay */
    .photo-lightbox {
        position: fixed; inset: 0; z-index: 9999;
        background: rgba(7,24,48,0.9); backdrop-filter: blur(10px);
        display: flex; align-items: center; justify-content: center;
        opacity: 0; transition: opacity 0.25s ease;
        cursor: zoom-out;
    }
    .photo-lightbox.show { opacity: 1; }
    .photo-lightbox img {
        max-width: 90vw; max-height: 85vh;
        border-radius: 16px;
        border: 1px solid rgba(6,182,212,0.25);
        box-shadow: 0 24px 64px rgba(0,0,0,0.6);
        transform: scale(0.9);
        transition: transform 0.25s cubic-bezier(0.16,1,0.3,1);
    }
    .photo-lightbox.show img { transform: scale(1); }

    .work-duration-pill {
        display: inline-flex; align-items: center; gap: 0.3rem;
        padding: 0.18rem 0.55rem;
        background: var(--bg-elevated);
        border: 1px solid var(--border-soft);
        border-radius: 99px;
        font-size: 0.7rem; font-weight: 600; color: var(--t3);
    }
</style>
@endpush

@section('content')

{{-- Lightbox --}}
<div id="photo-lightbox" class="photo-lightbox" onclick="closeLightbox()">
    <img id="lightbox-img" src="" alt="Foto Absensi">
</div>

{{-- ━━━━━━━━━━━━━━━━━━━━━━ FILTER ━━━━━━━━━━━━━━━━━━━━━━ --}}
<div class="card mb-5">
    <form method="GET" action="{{ route('attendance.history') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="form-group" style="margin-bottom:0;">
            <label class="form-label">Bulan</label>
            <input type="month" name="month" value="{{ request('month', now()->format('Y-m')) }}" class="form-control">
        </div>
        <div class="form-group" style="margin-bottom:0;">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
                <option value="">Semua Status</option>
                <option value="present"    {{ request('status') === 'present'    ? 'selected' : '' }}>✓ Hadir</option>
                <option value="late"       {{ request('status') === 'late'       ? 'selected' : '' }}>⏰ Terlambat</option>
                <option value="absent"     {{ request('status') === 'absent'     ? 'selected' : '' }}>✗ Tidak Hadir</option>
                <option value="leave"      {{ request('status') === 'leave'      ? 'selected' : '' }}>📋 Cuti</option>
                <option value="permission" {{ request('status') === 'permission' ? 'selected' : '' }}>📝 Izin</option>
            </select>
        </div>
        @hasrole(['super_admin', 'hrd', 'manager'])
        <div class="form-group" style="margin-bottom:0;">
            <label class="form-label">Karyawan</label>
            <select name="user_id" class="form-control">
                <option value="">Semua Karyawan</option>
                @foreach($users as $user)
                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        @endhasrole
        <div style="display:flex;align-items:flex-end;gap:0.5rem;">
            <button type="submit" class="btn btn-primary flex-1">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Filter
            </button>
            <a href="{{ route('attendance.history') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>
</div>

{{-- ━━━━━━━━━━━━━━━━━━━━━━ SUMMARY STRIP ━━━━━━━━━━━━━━━━━━━━━━ --}}
@php
    $totalPresent = $attendances->getCollection()->whereIn('status',['present','late'])->count();
    $totalLate    = $attendances->getCollection()->where('status','late')->count();
    $totalAbsent  = $attendances->getCollection()->where('status','absent')->count();
    $totalLeave   = $attendances->getCollection()->whereIn('status',['leave','permission'])->count();
@endphp
<div class="summary-strip">
    <div class="summary-chip">
        <span class="summary-chip-val" style="color:#34D399;">{{ $totalPresent }}</span>
        <span class="summary-chip-label">Hadir</span>
    </div>
    <div class="summary-chip">
        <span class="summary-chip-val" style="color:#FCD34D;">{{ $totalLate }}</span>
        <span class="summary-chip-label">Terlambat</span>
    </div>
    <div class="summary-chip">
        <span class="summary-chip-val" style="color:#FCA5A5;">{{ $totalAbsent }}</span>
        <span class="summary-chip-label">Absen</span>
    </div>
    <div class="summary-chip">
        <span class="summary-chip-val" style="color:#C4B5FD;">{{ $totalLeave }}</span>
        <span class="summary-chip-label">Cuti/Izin</span>
    </div>
    <div class="summary-chip">
        <span class="summary-chip-val" style="color:var(--t2);">{{ $attendances->total() }}</span>
        <span class="summary-chip-label">Total Data</span>
    </div>
</div>

{{-- ━━━━━━━━━━━━━━━━━━━━━━ TOOLBAR ━━━━━━━━━━━━━━━━━━━━━━ --}}
<div class="flex items-center justify-between mb-4 flex-wrap gap-3">
    <div style="font-size:0.8rem;color:var(--t4);">
        Menampilkan <strong style="color:var(--t2);">{{ $attendances->firstItem() ?? 0 }}–{{ $attendances->lastItem() ?? 0 }}</strong>
        dari <strong style="color:var(--t2);">{{ $attendances->total() }}</strong> data
    </div>
    <div class="flex gap-2">
        <a href="{{ route('attendance.history', array_merge(request()->all(), ['export' => 'excel'])) }}"
           class="btn btn-secondary btn-sm">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Excel
        </a>
        <a href="{{ route('attendance.history', array_merge(request()->all(), ['export' => 'pdf'])) }}"
           class="btn btn-secondary btn-sm">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            PDF
        </a>
    </div>
</div>

{{-- ━━━━━━━━━━━━━━━━━━━━━━ TABLE ━━━━━━━━━━━━━━━━━━━━━━ --}}
<div class="card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tanggal</th>
                    @hasrole(['super_admin', 'hrd', 'manager'])<th>Karyawan</th>@endhasrole
                    <th>Jam Masuk</th>
                    <th>Jam Pulang</th>
                    <th>Durasi Kerja</th>
                    <th>Jarak (m)</th>
                    <th>Status</th>
                    <th>Foto</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $i => $att)
                <tr>
                    <td style="color:var(--t4);font-size:0.72rem;">{{ $attendances->firstItem() + $i }}</td>
                    <td>
                        <div style="font-weight:600;font-size:0.82rem;color:var(--t1);">
                            {{ \Carbon\Carbon::parse($att->date)->format('d M Y') }}
                        </div>
                        <div style="font-size:0.68rem;color:var(--t4);">
                            {{ \Carbon\Carbon::parse($att->date)->translatedFormat('l') }}
                        </div>
                    </td>
                    @hasrole(['super_admin', 'hrd', 'manager'])
                    <td>
                        <div style="display:flex;align-items:center;gap:0.6rem;">
                            <div class="avatar" style="width:30px;height:30px;font-size:0.6rem;overflow:hidden;flex-shrink:0;">
                                @if($att->user?->photo)
                                    <img src="{{ $att->user->photo_url }}" alt="{{ $att->user->name }}" style="width:100%;height:100%;object-fit:cover;">
                                @else
                                    {{ $att->user?->initials }}
                                @endif
                            </div>
                            <div>
                                <div style="font-size:0.8rem;font-weight:600;color:var(--t1);">{{ $att->user?->name }}</div>
                                <div style="font-size:0.67rem;color:var(--t4);">{{ $att->user?->division?->name ?? '-' }}</div>
                            </div>
                        </div>
                    </td>
                    @endhasrole
                    <td>
                        @if($att->check_in_time)
                        <span style="font-family:'JetBrains Mono',monospace;font-size:0.82rem;font-weight:700;color:var(--em);">
                            {{ \Carbon\Carbon::parse($att->check_in_time)->format('H:i') }}
                        </span>
                        @else
                        <span style="color:var(--t5);">—</span>
                        @endif
                    </td>
                    <td>
                        @if($att->check_out_time)
                        <span style="font-family:'JetBrains Mono',monospace;font-size:0.82rem;font-weight:700;color:var(--t3);">
                            {{ \Carbon\Carbon::parse($att->check_out_time)->format('H:i') }}
                        </span>
                        @else
                        <span style="color:var(--t5);">—</span>
                        @endif
                    </td>
                    <td>
                        @if($att->work_duration)
                        <span class="work-duration-pill">
                            <svg style="width:11px;height:11px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $att->work_duration }}
                        </span>
                        @else
                        <span style="color:var(--t5);font-size:0.78rem;">—</span>
                        @endif
                    </td>
                    <td>
                        @if($att->check_in_distance !== null)
                        <span style="font-size:0.78rem;font-weight:600;color:{{ $att->check_in_distance < 100 ? 'var(--success)' : ($att->check_in_distance < 500 ? 'var(--warning)' : 'var(--danger)') }};">
                            {{ number_format($att->check_in_distance, 0) }}
                        </span>
                        @else
                        <span style="color:var(--t5);font-size:0.78rem;">—</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;flex-direction:column;gap:0.3rem;">
                            @if($att->status === 'present')
                                <span class="badge badge-success">✓ Hadir</span>
                            @elseif($att->status === 'late')
                                <span class="badge badge-warning">⏰ Terlambat</span>
                                @if($att->late_minutes > 0)
                                <span style="font-size:0.65rem;color:var(--t4);">{{ $att->late_minutes }} menit</span>
                                @endif
                            @elseif($att->status === 'absent')
                                <span class="badge badge-danger">✗ Absen</span>
                            @elseif($att->status === 'leave')
                                <span class="badge badge-info">📋 Cuti</span>
                            @elseif($att->status === 'permission')
                                <span class="badge badge-purple">📝 Izin</span>
                            @else
                                <span class="badge badge-gray">{{ $att->status }}</span>
                            @endif
                            @if($att->early_out_minutes > 0)
                            <span class="badge badge-orange" style="font-size:0.62rem;">Pulang Cepat</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        @if($att->check_in_photo)
                        <img src="{{ Storage::url($att->check_in_photo) }}"
                             alt="Foto"
                             class="photo-thumb"
                             onclick="openLightbox('{{ Storage::url($att->check_in_photo) }}')"
                             loading="lazy">
                        @else
                        <span style="color:var(--t5);font-size:0.75rem;">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align:center;padding:3.5rem;color:var(--t4);">
                        <div style="font-size:2rem;margin-bottom:0.75rem;">📋</div>
                        <div style="font-weight:600;color:var(--t3);">Tidak ada data absensi</div>
                        <div style="font-size:0.75rem;margin-top:0.25rem;">Coba ubah filter bulan atau status</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($attendances->hasPages())
    <div style="margin-top:1.5rem;padding:0 0.25rem;">
        {{ $attendances->withQueryString()->links() }}
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
function openLightbox(url) {
    const lb  = document.getElementById('photo-lightbox');
    const img = document.getElementById('lightbox-img');
    img.src = url;
    lb.style.display = 'flex';
    requestAnimationFrame(() => lb.classList.add('show'));
}
function closeLightbox() {
    const lb = document.getElementById('photo-lightbox');
    lb.classList.remove('show');
    setTimeout(() => { lb.style.display = 'none'; }, 250);
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLightbox(); });
</script>
@endpush
