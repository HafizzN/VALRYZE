@extends('layouts.app')

@section('title', 'Daftar Pengajuan Lembur')
@section('page-title', 'Lembur')
@section('breadcrumb', 'Perizinan › Lembur')

@section('content')

<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h2 style="font-size: 1rem; font-weight: 600;">Daftar Pengajuan Lembur</h2>
        <p style="font-size: 0.78rem; color: #64748b; margin-top: 0.25rem;">Kelola semua pengajuan lembur</p>
    </div>
    <a href="{{ route('overtime.create') }}" class="btn btn-primary">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Ajukan Lembur
    </a>
</div>

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
                    <td style="color: #64748b; font-size: 0.75rem;">{{ $overtimes->firstItem() + $i }}</td>
                    @hasrole(['super_admin', 'hrd', 'manager'])
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <div class="avatar" style="width: 28px; height: 28px; font-size: 0.62rem;">{{ $ot->user?->initials }}</div>
                            <span style="font-size: 0.8rem;">{{ $ot->user?->name }}</span>
                        </div>
                    </td>
                    @endhasrole
                    <td>{{ \Carbon\Carbon::parse($ot->date)->format('d M Y') }}</td>
                    <td style="font-family: monospace; font-size: 0.8rem;">{{ $ot->start_time }}</td>
                    <td style="font-family: monospace; font-size: 0.8rem;">{{ $ot->end_time ?? '-' }}</td>
                    <td>
                        @if($ot->duration_hours)
                            <span style="color: #a78bfa; font-weight: 600;">{{ $ot->duration_hours }}j</span>
                        @else
                            -
                        @endif
                    </td>
                    <td style="max-width: 180px;">
                        <div style="font-size: 0.78rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $ot->reason }}</div>
                    </td>
                    <td>
                        @if($ot->status === 'pending')
                            <span class="badge badge-warning">⏳ Pending</span>
                        @elseif($ot->status === 'approved')
                            <span class="badge badge-success">✓ Disetujui</span>
                        @elseif($ot->status === 'rejected')
                            <span class="badge badge-danger">✗ Ditolak</span>
                        @endif
                    </td>
                    <td>
                        <div style="display: flex; gap: 0.4rem;">
                            <a href="{{ route('overtime.show', $ot) }}" class="btn btn-secondary btn-sm">Detail</a>
                            @hasrole(['super_admin', 'hrd', 'manager'])
                            @if($ot->status === 'pending')
                                @hasrole('manager')
                                <form method="POST" action="{{ route('overtime.approve-manager', $ot) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm" title="Setujui (Manager)">✓</button>
                                </form>
                                @endhasrole
                                @hasrole(['super_admin', 'hrd'])
                                <form method="POST" action="{{ route('overtime.approve-hrd', $ot) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm" title="Setujui (HRD)">✓</button>
                                </form>
                                @endhasrole
                                <form method="POST" action="{{ route('overtime.reject', $ot) }}" style="display:inline;" onsubmit="return confirmReject(this)">
                                    @csrf
                                    <input type="hidden" name="rejection_reason" class="reject-reason-input">
                                    <button type="submit" class="btn btn-danger btn-sm" title="Tolak">✗</button>
                                </form>
                            @endif
                            @endhasrole
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align: center; color: #64748b; padding: 3rem;">Belum ada pengajuan lembur</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(isset($overtimes) && $overtimes->hasPages())
    <div style="margin-top: 1.5rem;">{{ $overtimes->withQueryString()->links() }}</div>
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
