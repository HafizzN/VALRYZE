@extends('layouts.app')

@section('title', 'Daftar Pengajuan Lembur')
@section('page-title', 'Lembur')
@section('breadcrumb', 'Perizinan › Lembur')

@section('content')

{{-- Shared Reject Modal (reusable) --}}
<div id="reject-modal" onclick="if(event.target===this)closeRejectModal()"
     style="position:fixed;inset:0;z-index:9999;background:rgba(7,24,48,0.85);backdrop-filter:blur(8px);display:none;align-items:center;justify-content:center;padding:1rem;opacity:0;transition:opacity 0.25s ease;">
    <div style="background:var(--bg-card);border:1px solid rgba(239,68,68,0.25);border-radius:20px;padding:1.75rem;max-width:420px;width:100%;box-shadow:0 24px 64px rgba(0,0,0,0.5);transform:scale(0.94) translateY(12px);transition:all 0.25s cubic-bezier(0.16,1,0.3,1);" id="reject-modal-box">
        <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1.25rem;">
            <div style="width:40px;height:40px;border-radius:12px;background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.25);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg style="width:20px;height:20px;color:#FCA5A5;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <div>
                <h4 style="font-size:0.95rem;font-weight:800;color:var(--t1);">Tolak Pengajuan Lembur</h4>
                <p style="font-size:0.72rem;color:var(--t4);margin-top:0.1rem;">Masukkan alasan penolakan</p>
            </div>
        </div>
        <div class="form-group" style="margin-bottom:1rem;">
            <label class="form-label">Alasan <span style="color:var(--danger);">*</span></label>
            <textarea id="reject-reason-input" class="form-control" rows="3" placeholder="Tuliskan alasan penolakan..." style="resize:none;"></textarea>
            <div id="reject-reason-error" class="form-error" style="display:none;">Alasan minimal 5 karakter.</div>
        </div>
        <div style="display:flex;gap:0.5rem;">
            <button type="button" onclick="closeRejectModal()" class="btn btn-secondary flex-1">Batal</button>
            <button type="button" onclick="submitReject()" class="btn btn-danger flex-1">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                Tolak
            </button>
        </div>
    </div>
</div>

{{-- Header --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;">
    <div>
        <h2 style="font-size:1.05rem;font-weight:700;color:var(--t1);">Daftar Pengajuan Lembur</h2>
        <p style="font-size:0.78rem;color:var(--t4);margin-top:0.2rem;">Kelola semua pengajuan lembur karyawan</p>
    </div>
    <a href="{{ route('overtime.create') }}" class="btn btn-primary">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Ajukan Lembur
    </a>
</div>

@if(session('success'))
<div class="alert alert-success mb-4">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    {{ session('success') }}
</div>
@endif

<div class="card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    @hasrole(['super_admin', 'hrd', 'manager'])<th>Karyawan</th>@endhasrole
                    <th>Tanggal</th>
                    <th>Jam Mulai</th>
                    <th>Jam Selesai</th>
                    <th>Durasi</th>
                    <th>Alasan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($overtimes as $i => $ot)
                <tr>
                    <td style="color:var(--t4);font-size:0.72rem;">{{ $overtimes->firstItem() + $i }}</td>

                    @hasrole(['super_admin', 'hrd', 'manager'])
                    <td>
                        <div style="display:flex;align-items:center;gap:0.6rem;">
                            <div class="avatar" style="width:30px;height:30px;font-size:0.6rem;overflow:hidden;flex-shrink:0;">
                                @if($ot->user?->photo)
                                    <img src="{{ $ot->user->photo_url }}" alt="{{ $ot->user->name }}" style="width:100%;height:100%;object-fit:cover;">
                                @else
                                    {{ $ot->user?->initials }}
                                @endif
                            </div>
                            <div>
                                <div style="font-size:0.8rem;font-weight:600;color:var(--t1);">{{ $ot->user?->name }}</div>
                                <div style="font-size:0.67rem;color:var(--t4);">{{ $ot->user?->division?->name ?? 'Staff' }}</div>
                            </div>
                        </div>
                    </td>
                    @endhasrole

                    <td>
                        <div style="font-weight:600;font-size:0.82rem;color:var(--t1);">{{ \Carbon\Carbon::parse($ot->date)->format('d M Y') }}</div>
                        <div style="font-size:0.68rem;color:var(--t4);">{{ \Carbon\Carbon::parse($ot->date)->translatedFormat('l') }}</div>
                    </td>
                    <td style="font-family:'JetBrains Mono',monospace;font-size:0.82rem;font-weight:700;color:var(--em);">{{ $ot->start_time ?? '—' }}</td>
                    <td style="font-family:'JetBrains Mono',monospace;font-size:0.82rem;font-weight:700;color:var(--t3);">{{ $ot->end_time ?? '—' }}</td>
                    <td>
                        @if($ot->duration_hours)
                        <span style="display:inline-flex;align-items:center;gap:0.3rem;padding:0.18rem 0.55rem;background:rgba(167,139,250,0.1);border:1px solid rgba(167,139,250,0.2);border-radius:99px;font-size:0.72rem;font-weight:700;color:#A78BFA;">
                            <svg style="width:11px;height:11px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $ot->duration_hours }}j
                        </span>
                        @else
                        <span style="color:var(--t5);">—</span>
                        @endif
                    </td>
                    <td style="max-width:200px;">
                        <div style="font-size:0.78rem;color:var(--t2);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $ot->reason }}">
                            {{ $ot->reason }}
                        </div>
                    </td>
                    <td>
                        @if($ot->status === 'pending')
                            <span class="badge badge-warning">⏳ Pending</span>
                        @elseif($ot->status === 'approved_manager')
                            <span class="badge badge-info">✓ Manager</span>
                        @elseif($ot->status === 'approved')
                            <span class="badge badge-success">✓ Disetujui</span>
                        @elseif($ot->status === 'rejected')
                            <span class="badge badge-danger">✗ Ditolak</span>
                        @else
                            <span class="badge badge-gray">{{ $ot->status }}</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:0.35rem;align-items:center;flex-wrap:wrap;">
                            <a href="{{ route('overtime.show', $ot) }}" class="btn btn-secondary btn-xs">Detail</a>
                            @hasrole(['super_admin', 'hrd', 'manager'])
                            @if($ot->status === 'pending')
                                @hasrole('manager')
                                <form method="POST" action="{{ route('overtime.approve-manager', $ot) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-xs" title="Setujui (Manager)">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                </form>
                                @endhasrole
                                @hasrole(['super_admin', 'hrd'])
                                <form method="POST" action="{{ route('overtime.approve-hrd', $ot) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-xs" title="Setujui (HRD)">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                </form>
                                @endhasrole
                                <form id="reject-form-{{ $ot->id }}" method="POST" action="{{ route('overtime.reject', $ot) }}" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="rejection_reason" class="reject-reason-hidden">
                                    <button type="button" class="btn btn-danger btn-xs" onclick="openRejectModal({{ $ot->id }})" title="Tolak">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </form>
                            @endif
                            @endhasrole
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align:center;padding:3.5rem;color:var(--t4);">
                        <div style="font-size:2rem;margin-bottom:0.75rem;">⏰</div>
                        <div style="font-weight:600;color:var(--t3);">Belum ada pengajuan lembur</div>
                        <div style="font-size:0.75rem;margin-top:0.25rem;">Klik "Ajukan Lembur" untuk membuat pengajuan baru</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(isset($overtimes) && $overtimes->hasPages())
    <div style="margin-top:1.5rem;padding:0 0.25rem;">{{ $overtimes->withQueryString()->links() }}</div>
    @endif
</div>

@endsection

@push('scripts')
<script>
let _rejectId = null;
function openRejectModal(id) {
    _rejectId = id;
    document.getElementById('reject-reason-input').value = '';
    document.getElementById('reject-reason-error').style.display = 'none';
    const m = document.getElementById('reject-modal');
    m.style.display = 'flex';
    requestAnimationFrame(() => {
        m.style.opacity = '1';
        document.getElementById('reject-modal-box').style.transform = 'scale(1) translateY(0)';
    });
    setTimeout(() => document.getElementById('reject-reason-input').focus(), 200);
}
function closeRejectModal() {
    const m = document.getElementById('reject-modal');
    m.style.opacity = '0';
    document.getElementById('reject-modal-box').style.transform = 'scale(0.94) translateY(12px)';
    setTimeout(() => { m.style.display = 'none'; _rejectId = null; }, 250);
}
function submitReject() {
    const reason = document.getElementById('reject-reason-input').value.trim();
    if (reason.length < 5) {
        document.getElementById('reject-reason-error').style.display = 'block';
        return;
    }
    const form = document.getElementById('reject-form-' + _rejectId);
    form.querySelector('.reject-reason-hidden').value = reason;
    closeRejectModal();
    setTimeout(() => form.submit(), 260);
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeRejectModal(); });
</script>
@endpush
