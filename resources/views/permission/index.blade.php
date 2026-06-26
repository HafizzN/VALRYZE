@extends('layouts.app')

@section('title', 'Daftar Pengajuan Izin')
@section('page-title', 'Izin')
@section('breadcrumb', 'Perizinan › Izin')

@section('content')

<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h2 style="font-size: 1rem; font-weight: 600;">Daftar Pengajuan Izin</h2>
        <p style="font-size: 0.78rem; color: #64748b; margin-top: 0.25rem;">Kelola semua pengajuan izin</p>
    </div>
    <a href="{{ route('permission.create') }}" class="btn btn-primary">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Ajukan Izin
    </a>
</div>

<div class="card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    @hasrole(['super_admin', 'hrd', 'manager'])<th>Karyawan</th>@endhasrole
                    <th>Jenis Izin</th>
                    <th>Tanggal</th>
                    <th>Jam Mulai</th>
                    <th>Jam Selesai</th>
                    <th>Alasan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($permissions as $i => $perm)
                <tr>
                    <td style="color: #64748b; font-size: 0.75rem;">{{ $permissions->firstItem() + $i }}</td>
                    @hasrole(['super_admin', 'hrd', 'manager'])
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <div class="avatar" style="width: 28px; height: 28px; font-size: 0.62rem;">{{ $perm->user?->initials }}</div>
                            <span style="font-size: 0.8rem;">{{ $perm->user?->name }}</span>
                        </div>
                    </td>
                    @endhasrole
                    <td>
                        @php
                            $permTypeColors = ['late_in'=>'warning','early_out'=>'orange','outside'=>'info','other'=>'gray'];
                            $permTypeLabels = ['late_in'=>'Izin Terlambat','early_out'=>'Pulang Awal','outside'=>'Dinas Luar','other'=>'Lainnya'];
                        @endphp
                        <span class="badge badge-{{ $permTypeColors[$perm->type] ?? 'gray' }}">{{ $permTypeLabels[$perm->type] ?? $perm->type }}</span>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($perm->date)->format('d M Y') }}</td>
                    <td style="font-family: monospace; font-size: 0.8rem;">{{ $perm->start_time ?? '-' }}</td>
                    <td style="font-family: monospace; font-size: 0.8rem;">{{ $perm->end_time ?? '-' }}</td>
                    <td style="max-width: 200px;">
                        <div style="font-size: 0.78rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $perm->reason }}</div>
                    </td>
                    <td>
                        @if($perm->status === 'pending')
                            <span class="badge badge-warning">⏳ Pending</span>
                        @elseif($perm->status === 'approved')
                            <span class="badge badge-success">✓ Disetujui</span>
                        @elseif($perm->status === 'rejected')
                            <span class="badge badge-danger">✗ Ditolak</span>
                        @endif
                    </td>
                    <td>
                        <div style="display: flex; gap: 0.4rem;">
                            <a href="{{ route('permission.show', $perm) }}" class="btn btn-secondary btn-sm">Detail</a>
                            @hasrole(['super_admin', 'hrd', 'manager'])
                            @if($perm->status === 'pending')
                            <form method="POST" action="{{ route('permission.approve', $perm) }}" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">✓</button>
                            </form>
                            <form method="POST" action="{{ route('permission.reject', $perm) }}" style="display:inline;" onsubmit="return confirmReject(this)">
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
                    <td colspan="9" style="text-align: center; color: #64748b; padding: 3rem;">Belum ada pengajuan izin</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(isset($permissions) && $permissions->hasPages())
    <div style="margin-top: 1.5rem;">{{ $permissions->withQueryString()->links() }}</div>
    @endif
</div>

@push('scripts')
<script>
function confirmReject(form) {
    const reason = prompt("Masukkan alasan penolakan:");
    if (reason === null) return false;
    if (reason.trim() === "") {
        alert("Alasan penolakan tidak boleh kosong!");
        return false;
    }
    form.querySelector('.reject-reason-input').value = reason;
    return true;
}
</script>
@endpush
@endsection
