@extends('layouts.app')

@section('title', 'Daftar Pengajuan Cuti')
@section('page-title', 'Cuti')
@section('breadcrumb', 'Perizinan › Cuti')

@section('content')

<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h2 style="font-size: 1rem; font-weight: 600;">Daftar Pengajuan Cuti</h2>
        <p style="font-size: 0.78rem; color: #64748b; margin-top: 0.25rem;">Kelola semua pengajuan cuti karyawan</p>
    </div>
    <a href="{{ route('leave.create') }}" class="btn btn-primary">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Ajukan Cuti
    </a>
</div>

{{-- Filter --}}
<div class="card mb-6">
    <form method="GET" action="{{ route('leave.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
                <option value="">Semua</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Jenis Cuti</label>
            <select name="leave_type" class="form-control">
                <option value="">Semua Jenis</option>
                <option value="annual" {{ request('leave_type') === 'annual' ? 'selected' : '' }}>Cuti Tahunan</option>
                <option value="sick" {{ request('leave_type') === 'sick' ? 'selected' : '' }}>Sakit</option>
                <option value="maternity" {{ request('leave_type') === 'maternity' ? 'selected' : '' }}>Melahirkan</option>
                <option value="wedding" {{ request('leave_type') === 'wedding' ? 'selected' : '' }}>Pernikahan</option>
                <option value="big_leave" {{ request('leave_type') === 'big_leave' ? 'selected' : '' }}>Cuti Besar</option>
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
            <button type="submit" class="btn btn-primary flex-1">Filter</button>
            <a href="{{ route('leave.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>
</div>

<div class="card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    @hasrole(['super_admin', 'hrd', 'manager'])<th>Karyawan</th>@endhasrole
                    <th>Jenis Cuti</th>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Selesai</th>
                    <th>Durasi</th>
                    <th>Status</th>
                    <th>Diajukan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leaves as $i => $leave)
                <tr>
                    <td style="color: #64748b; font-size: 0.75rem;">{{ $leaves->firstItem() + $i }}</td>
                    @hasrole(['super_admin', 'hrd', 'manager'])
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <div class="avatar" style="width: 28px; height: 28px; font-size: 0.62rem;">{{ $leave->user?->initials }}</div>
                            <span style="font-size: 0.8rem;">{{ $leave->user?->name }}</span>
                        </div>
                    </td>
                    @endhasrole
                    <td>
                        @php
                            $typeColors = ['annual'=>'info','sick'=>'warning','maternity'=>'purple','wedding'=>'success','big_leave'=>'orange'];
                            $typeLabels = ['annual'=>'Tahunan','sick'=>'Sakit','maternity'=>'Melahirkan','wedding'=>'Pernikahan','big_leave'=>'Cuti Besar'];
                        @endphp
                        <span class="badge badge-{{ $typeColors[$leave->leave_type] ?? 'gray' }}">{{ $typeLabels[$leave->leave_type] ?? $leave->leave_type }}</span>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($leave->start_date)->format('d M Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}</td>
                    <td>{{ $leave->duration }} hari</td>
                    <td>
                        @if($leave->status === 'pending')
                            <span class="badge badge-warning">⏳ Pending</span>
                        @elseif($leave->status === 'approved')
                            <span class="badge badge-success">✓ Disetujui</span>
                        @elseif($leave->status === 'rejected')
                            <span class="badge badge-danger">✗ Ditolak</span>
                        @else
                            <span class="badge badge-gray">{{ $leave->status }}</span>
                        @endif
                    </td>
                    <td style="font-size: 0.75rem; color: #64748b;">{{ $leave->created_at->format('d M Y') }}</td>
                    <td>
                        <div style="display: flex; gap: 0.4rem; flex-wrap: wrap;">
                            <a href="{{ route('leave.show', $leave) }}" class="btn btn-secondary btn-sm">Detail</a>
                            @hasrole(['super_admin', 'hrd', 'manager'])
                            @if($leave->status === 'pending')
                            <form method="POST" action="{{ route('leave.approve', $leave) }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">✓</button>
                            </form>
                            <form method="POST" action="{{ route('leave.reject', $leave) }}" style="display: inline;" onsubmit="return confirmReject(this)">
                                @csrf
                                <input type="hidden" name="rejection_reason" class="reject-reason-input">
                                <button type="submit" class="btn btn-danger btn-sm">✗</button>
                            </form>
                            @endif
                            @endhasrole
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align: center; color: #64748b; padding: 3rem;">
                        Belum ada pengajuan cuti
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(isset($leaves) && $leaves->hasPages())
    <div style="margin-top: 1.5rem;">{{ $leaves->withQueryString()->links() }}</div>
    @endif
</div>

@push('scripts')
<script>
function confirmReject(form) {
    const reason = prompt("Masukkan alasan penolakan (minimal 5 karakter):");
    if (reason === null) return false;
    if (reason.trim().length < 5) {
        alert("Alasan penolakan minimal 5 karakter!");
        return false;
    }
    form.querySelector('.reject-reason-input').value = reason;
    return true;
}
</script>
@endpush
@endsection
