@extends('layouts.app')

@section('title', 'Riwayat Absensi')
@section('page-title', 'Riwayat Absensi')
@section('breadcrumb', 'Absensi › Riwayat')

@section('content')

{{-- Filter Form --}}
<div class="card mb-6">
    <form method="GET" action="{{ route('attendance.history') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Bulan</label>
            <input type="month" name="month" value="{{ request('month', now()->format('Y-m')) }}" class="form-control">
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
                <option value="">Semua Status</option>
                <option value="present" {{ request('status') === 'present' ? 'selected' : '' }}>Hadir</option>
                <option value="late" {{ request('status') === 'late' ? 'selected' : '' }}>Terlambat</option>
                <option value="absent" {{ request('status') === 'absent' ? 'selected' : '' }}>Tidak Hadir</option>
                <option value="leave" {{ request('status') === 'leave' ? 'selected' : '' }}>Cuti</option>
                <option value="permission" {{ request('status') === 'permission' ? 'selected' : '' }}>Izin</option>
            </select>
        </div>
        @hasrole(['super_admin', 'hrd', 'manager'])
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Karyawan</label>
            <select name="user_id" class="form-control">
                <option value="">Semua Karyawan</option>
                @foreach($users as $user)
                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        @endhasrole
        <div style="display: flex; align-items: flex-end; gap: 0.5rem;">
            <button type="submit" class="btn btn-primary flex-1">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                Filter
            </button>
            <a href="{{ route('attendance.history') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>
</div>

{{-- Export Buttons --}}
<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.75rem;">
    <div style="font-size: 0.82rem; color: #64748b;">
        Menampilkan {{ $attendances->count() }} dari {{ $attendances->total() }} data
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="{{ route('attendance.history', array_merge(request()->all(), ['export' => 'excel'])) }}" class="btn btn-secondary btn-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Export Excel
        </a>
        <a href="{{ route('attendance.history', array_merge(request()->all(), ['export' => 'pdf'])) }}" class="btn btn-secondary btn-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            Export PDF
        </a>
    </div>
</div>

{{-- Table --}}
<div class="card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tanggal</th>
                    @hasrole(['super_admin', 'hrd', 'manager'])<th>Nama Karyawan</th>@endhasrole
                    <th>Jam Masuk</th>
                    <th>Jam Pulang</th>
                    <th>Jarak (m)</th>
                    <th>Durasi</th>
                    <th>Status</th>
                    <th>Foto</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $i => $att)
                <tr>
                    <td style="color: #64748b; font-size: 0.75rem;">{{ $attendances->firstItem() + $i }}</td>
                    <td>
                        <div style="font-weight: 500;">{{ \Carbon\Carbon::parse($att->date)->format('d M Y') }}</div>
                        <div style="font-size: 0.7rem; color: #64748b;">{{ \Carbon\Carbon::parse($att->date)->translatedFormat('l') }}</div>
                    </td>
                    @hasrole(['super_admin', 'hrd', 'manager'])
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <div class="avatar" style="width: 30px; height: 30px; font-size: 0.65rem; overflow: hidden;">
                                @if($att->user?->photo)
                                    <img src="{{ $att->user->photo_url }}" alt="{{ $att->user->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    {{ $att->user?->initials }}
                                @endif
                            </div>
                            <div>
                                <div style="font-size: 0.8rem; font-weight: 500;">{{ $att->user?->name }}</div>
                                <div style="font-size: 0.68rem; color: #64748b;">{{ $att->user?->employee?->division?->name ?? '-' }}</div>
                            </div>
                        </div>
                    </td>
                    @endhasrole
                    <td style="font-family: monospace;">{{ $att->check_in_time ? \Carbon\Carbon::parse($att->check_in_time)->format('H:i') : '-' }}</td>
                    <td style="font-family: monospace;">{{ $att->check_out_time ? \Carbon\Carbon::parse($att->check_out_time)->format('H:i') : '-' }}</td>
                    <td>{{ $att->check_in_distance ? number_format($att->check_in_distance, 0) : '-' }}</td>
                    <td>{{ $att->work_duration ?? '-' }}</td>
                    <td>
                        @if($att->status === 'present')
                            <span class="badge badge-success">Hadir</span>
                        @elseif($att->status === 'late')
                            <span class="badge badge-warning">Terlambat ({{ $att->late_minutes }} menit)</span>
                        @elseif($att->status === 'absent')
                            <span class="badge badge-danger">Tidak Hadir</span>
                        @elseif($att->status === 'leave')
                            <span class="badge badge-info">Cuti</span>
                        @elseif($att->status === 'permission')
                            <span class="badge badge-purple">Izin</span>
                        @else
                            <span class="badge badge-gray">{{ $att->status }}</span>
                        @endif

                        @if($att->early_out_minutes > 0)
                            <span class="badge badge-orange" style="margin-top: 0.25rem; display: inline-block;">Pulang Cepat ({{ $att->early_out_minutes }} mnt)</span>
                        @endif
                    </td>
                    <td>
                        @if($att->check_in_photo)
                        <a href="{{ Storage::url($att->check_in_photo) }}" target="_blank" style="color: #6366f1; font-size: 0.75rem;">Lihat</a>
                        @else
                        <span style="color: #475569; font-size: 0.75rem;">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align: center; color: #64748b; padding: 3rem;">
                        <svg style="width: 40px; height: 40px; margin: 0 auto 1rem; color: #374151;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <div>Tidak ada data absensi</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($attendances->hasPages())
    <div style="margin-top: 1.5rem;">
        {{ $attendances->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
