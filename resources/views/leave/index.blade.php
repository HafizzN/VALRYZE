@extends('layouts.app')

@section('title', 'Daftar Pengajuan Cuti')
@section('page-title', 'Cuti')
@section('breadcrumb', 'Perizinan › Cuti')

@push('styles')
<style>
    .page-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;
    }
    .filter-card {
        background: var(--bg-card);
        border: 1px solid var(--border-soft);
        border-radius: 16px; padding: 1.25rem 1.5rem;
        margin-bottom: 1.25rem;
        box-shadow: var(--shadow-card);
    }
    /* Custom reject modal */
    .reject-modal-overlay {
        position: fixed; inset: 0; z-index: 9999;
        background: rgba(7,24,48,0.85); backdrop-filter: blur(8px);
        display: flex; align-items: center; justify-content: center;
        padding: 1rem;
        opacity: 0; transition: opacity 0.25s ease;
    }
    .reject-modal-overlay.show { opacity: 1; }
    .reject-modal-box {
        background: var(--bg-card);
        border: 1px solid rgba(239,68,68,0.25);
        border-radius: 20px; padding: 1.75rem;
        max-width: 420px; width: 100%;
        box-shadow: 0 24px 64px rgba(0,0,0,0.5), 0 0 0 1px rgba(239,68,68,0.1);
        transform: scale(0.94) translateY(12px);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .reject-modal-overlay.show .reject-modal-box {
        transform: scale(1) translateY(0);
    }
</style>
@endpush

@section('content')

{{-- Custom Reject Modal --}}
<div id="reject-modal" class="reject-modal-overlay" onclick="if(event.target===this) closeRejectModal()">
    <div class="reject-modal-box">
        <div class="flex items-center gap-3 mb-4">
            <div style="width:40px;height:40px;border-radius:12px;background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.25);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg style="width:20px;height:20px;color:#FCA5A5;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <div>
                <h4 style="font-size:0.95rem;font-weight:800;color:var(--t1);">Tolak Pengajuan</h4>
                <p style="font-size:0.72rem;color:var(--t4);margin-top:0.1rem;">Masukkan alasan penolakan</p>
            </div>
        </div>
        <div class="form-group" style="margin-bottom:1rem;">
            <label class="form-label" for="reject-reason-input">Alasan Penolakan <span style="color:var(--danger);">*</span></label>
            <textarea id="reject-reason-input" class="form-control" rows="3"
                      placeholder="Tuliskan alasan penolakan pengajuan ini..."
                      style="resize:none; min-height: 90px;"></textarea>
            <div id="reject-reason-error" class="form-error" style="display:none;">Alasan penolakan minimal 5 karakter.</div>
        </div>
        <div style="display:flex;gap:0.5rem;">
            <button type="button" onclick="closeRejectModal()" class="btn btn-secondary flex-1">Batal</button>
            <button type="button" onclick="submitReject()" class="btn btn-danger flex-1">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Tolak Pengajuan
            </button>
        </div>
    </div>
</div>

{{-- Header --}}
<div class="page-header">
    <div>
        <h2 style="font-size: 1.05rem; font-weight: 700; color: var(--t1);">Daftar Pengajuan Cuti</h2>
        <p style="font-size: 0.78rem; color: var(--t4); margin-top: 0.2rem;">Kelola semua pengajuan cuti karyawan</p>
    </div>
    <a href="{{ route('leave.create') }}" class="btn btn-primary">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Ajukan Cuti
    </a>
</div>

{{-- Flash messages --}}
@if(session('success'))
    <div class="alert alert-success mb-4">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="alert alert-error mb-4">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('error') }}
    </div>
@endif

{{-- Filter --}}
<div class="filter-card">
    <form method="GET" action="{{ route('leave.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                <option value="approved_manager" {{ request('status') === 'approved_manager' ? 'selected' : '' }}>✓ Disetujui Manager</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>✓ Disetujui HRD</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>✗ Ditolak</option>
            </select>
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Jenis Cuti</label>
            <select name="leave_type" class="form-control">
                <option value="">Semua Jenis</option>
                <option value="annual"    {{ request('leave_type') === 'annual'    ? 'selected' : '' }}>Cuti Tahunan</option>
                <option value="sick"      {{ request('leave_type') === 'sick'      ? 'selected' : '' }}>Sakit</option>
                <option value="maternity" {{ request('leave_type') === 'maternity' ? 'selected' : '' }}>Melahirkan</option>
                <option value="paternity" {{ request('leave_type') === 'paternity' ? 'selected' : '' }}>Menemani Melahirkan</option>
                <option value="wedding"   {{ request('leave_type') === 'wedding'   ? 'selected' : '' }}>Pernikahan</option>
                <option value="big_leave" {{ request('leave_type') === 'big_leave' ? 'selected' : '' }}>Cuti Besar</option>
                <option value="other"     {{ request('leave_type') === 'other'     ? 'selected' : '' }}>Lainnya</option>
            </select>
        </div>
        @hasrole(['super_admin', 'hrd', 'manager'])
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Karyawan</label>
            <select name="user_id" class="form-control">
                <option value="">Semua Karyawan</option>
                @foreach($users ?? [] as $user)
                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        @endhasrole
        <div style="display: flex; align-items: flex-end; gap: 0.5rem;">
            <button type="submit" class="btn btn-primary flex-1">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                Filter
            </button>
            <a href="{{ route('leave.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>
</div>

{{-- Table --}}
<div class="card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    @hasrole(['super_admin', 'hrd', 'manager'])<th>Karyawan</th>@endhasrole
                    <th>Jenis Cuti</th>
                    <th>Periode</th>
                    <th>Durasi</th>
                    <th>Status</th>
                    <th>Diajukan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leaves as $i => $leave)
                <tr>
                    <td style="color: var(--t4); font-size: 0.75rem;">{{ $leaves->firstItem() + $i }}</td>
                    @hasrole(['super_admin', 'hrd', 'manager'])
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.6rem;">
                            <div class="avatar" style="width: 30px; height: 30px; font-size: 0.6rem; overflow: hidden; flex-shrink:0;">
                                @if($leave->user?->photo)
                                    <img src="{{ $leave->user->photo_url }}" alt="{{ $leave->user->name }}" style="width:100%;height:100%;object-fit:cover;">
                                @else
                                    {{ $leave->user?->initials }}
                                @endif
                            </div>
                            <div>
                                <div style="font-size: 0.8rem; font-weight: 600; color: var(--t1);">{{ $leave->user?->name }}</div>
                                <div style="font-size: 0.68rem; color: var(--t4);">{{ $leave->user?->division?->name ?? 'Staff' }}</div>
                            </div>
                        </div>
                    </td>
                    @endhasrole
                    <td>
                        @php
                            $typeMap = [
                                'annual'    => ['label' => 'Tahunan',   'badge' => 'badge-info'],
                                'sick'      => ['label' => 'Sakit',     'badge' => 'badge-warning'],
                                'maternity' => ['label' => 'Melahirkan','badge' => 'badge-purple'],
                                'paternity' => ['label' => 'Ayah',      'badge' => 'badge-info'],
                                'wedding'   => ['label' => 'Pernikahan','badge' => 'badge-success'],
                                'big_leave' => ['label' => 'Cuti Besar','badge' => 'badge-orange'],
                                'other'     => ['label' => 'Lainnya',   'badge' => 'badge-gray'],
                            ];
                            $tm = $typeMap[$leave->leave_type] ?? ['label' => $leave->leave_type, 'badge' => 'badge-gray'];
                        @endphp
                        <span class="badge {{ $tm['badge'] }}">{{ $tm['label'] }}</span>
                    </td>
                    <td style="font-size: 0.78rem;">
                        <div style="font-weight: 600;">{{ \Carbon\Carbon::parse($leave->start_date)->format('d M Y') }}</div>
                        <div style="color: var(--t4);">s/d {{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}</div>
                    </td>
                    <td>
                        <span style="font-size:0.78rem;font-weight:700;color:var(--t1);">{{ $leave->total_days }}</span>
                        <span style="font-size:0.7rem;color:var(--t4);"> hari</span>
                    </td>
                    <td>
                        @if($leave->status === 'pending')
                            <span class="badge badge-warning">⏳ Pending</span>
                        @elseif($leave->status === 'approved_manager')
                            <span class="badge badge-info">✓ Manager</span>
                        @elseif($leave->status === 'approved')
                            <span class="badge badge-success">✓ Disetujui</span>
                        @elseif($leave->status === 'rejected')
                            <span class="badge badge-danger">✗ Ditolak</span>
                        @else
                            <span class="badge badge-gray">{{ $leave->status }}</span>
                        @endif
                    </td>
                    <td style="font-size: 0.72rem; color: var(--t4);">{{ $leave->created_at->diffForHumans() }}</td>
                    <td>
                        <div style="display: flex; gap: 0.35rem; flex-wrap: wrap; align-items: center;">
                            <a href="{{ route('leave.show', $leave) }}" class="btn btn-secondary btn-xs">Detail</a>
                            @hasrole(['super_admin', 'hrd', 'manager'])
                            @if($leave->status === 'pending')
                            <form method="POST" action="{{ route('leave.approve', $leave) }}" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-success btn-xs" title="Setujui">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </button>
                            </form>
                            <form id="reject-form-{{ $leave->id }}" method="POST" action="{{ route('leave.reject', $leave) }}" style="display:inline;">
                                @csrf
                                <input type="hidden" name="rejection_reason" class="reject-reason-input" id="reject-reason-{{ $leave->id }}">
                                <button type="button" class="btn btn-danger btn-xs" onclick="openRejectModal({{ $leave->id }})" title="Tolak">
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
                    <td colspan="9" style="text-align: center; color: var(--t4); padding: 3rem;">
                        <div style="font-size: 2rem; margin-bottom: 0.75rem;">📋</div>
                        <div style="font-weight: 600; color: var(--t3);">Belum ada pengajuan cuti</div>
                        <div style="font-size: 0.75rem; margin-top: 0.25rem;">Klik "Ajukan Cuti" untuk membuat pengajuan baru</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(isset($leaves) && $leaves->hasPages())
    <div style="margin-top: 1.5rem; padding: 0 0.25rem;">{{ $leaves->withQueryString()->links() }}</div>
    @endif
</div>

@endsection

@push('scripts')
<script>
let currentRejectLeaveId = null;

function openRejectModal(id) {
    currentRejectLeaveId = id;
    const textarea = document.getElementById('reject-reason-input');
    textarea.value = '';
    document.getElementById('reject-reason-error').style.display = 'none';
    const modal = document.getElementById('reject-modal');
    modal.style.display = 'flex';
    requestAnimationFrame(() => { modal.classList.add('show'); });
    setTimeout(() => textarea.focus(), 200);
}

function closeRejectModal() {
    const modal = document.getElementById('reject-modal');
    modal.classList.remove('show');
    setTimeout(() => { modal.style.display = 'none'; currentRejectLeaveId = null; }, 250);
}

function submitReject() {
    const reason = document.getElementById('reject-reason-input').value.trim();
    const errEl  = document.getElementById('reject-reason-error');
    if (reason.length < 5) {
        errEl.style.display = 'block';
        document.getElementById('reject-reason-input').focus();
        return;
    }
    errEl.style.display = 'none';
    const form = document.getElementById('reject-form-' + currentRejectLeaveId);
    form.querySelector('.reject-reason-input').value = reason;
    closeRejectModal();
    setTimeout(() => form.submit(), 260);
}

// Close modal on Escape key
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeRejectModal(); });
</script>
@endpush
