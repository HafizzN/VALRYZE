@extends('layouts.app')

@section('title', 'Dashboard Karyawan')
@section('page-title', 'Dashboard')
@section('breadcrumb', 'Dashboard › Ringkasan Hari Ini')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

    {{-- Today's Attendance Status --}}
    <div class="card" style="grid-column: span 1;">
        <div style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1rem;">Status Hari Ini</div>
        @if($todayAttendance)
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                <div style="width: 48px; height: 48px; background: rgba(16,185,129,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 24px; height: 24px; color: #34d399;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div style="font-weight: 600; font-size: 0.9rem;">Sudah Absen Masuk</div>
                    <div style="font-size: 0.78rem; color: #64748b;">{{ $todayAttendance->check_in_time ? \Carbon\Carbon::parse($todayAttendance->check_in_time)->format('H:i') : '-' }} WIB</div>
                </div>
            </div>
            @if($todayAttendance->status === 'late')
                <span class="badge badge-warning">⏰ Terlambat ({{ $todayAttendance->late_minutes }} menit)</span>
            @else
                <span class="badge badge-success">✓ Tepat Waktu</span>
            @endif

            @if($todayAttendance->early_out_minutes > 0)
                <span class="badge badge-orange" style="margin-top: 0.5rem; display: inline-block;">⏰ Pulang Cepat ({{ $todayAttendance->early_out_minutes }} menit)</span>
            @endif
            @if($todayAttendance->check_out_time)
                <div style="margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid var(--border-color);">
                    <div style="font-size: 0.72rem; color: #64748b;">Absen Pulang</div>
                    <div style="font-weight: 600;">{{ \Carbon\Carbon::parse($todayAttendance->check_out_time)->format('H:i') }} WIB</div>
                </div>
            @else
                <div style="margin-top: 0.75rem;">
                    <a href="{{ route('attendance.check-out') }}" class="btn btn-primary btn-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7"/></svg>
                        Absen Pulang
                    </a>
                </div>
            @endif
        @else
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                <div style="width: 48px; height: 48px; background: rgba(239,68,68,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 24px; height: 24px; color: #f87171;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div style="font-weight: 600; font-size: 0.9rem;">Belum Absen</div>
                    <div style="font-size: 0.78rem; color: #64748b;">Segera lakukan absen masuk</div>
                </div>
            </div>
            <a href="{{ route('attendance.check-in') }}" class="btn btn-primary" style="width: 100%; justify-content: center;">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14"/></svg>
                Absen Masuk Sekarang
            </a>
        @endif
    </div>

    {{-- Monthly Stats --}}
    <div class="card">
        <div style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1rem;">Statistik Bulan Ini</div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div style="background: rgba(16,185,129,0.08); border: 1px solid rgba(16,185,129,0.15); border-radius: 10px; padding: 1rem; text-align: center;">
                <div style="font-size: 1.8rem; font-weight: 700; color: #34d399;">{{ $monthPresent }}</div>
                <div style="font-size: 0.72rem; color: #64748b; margin-top: 0.25rem;">Hadir</div>
            </div>
            <div style="background: rgba(245,158,11,0.08); border: 1px solid rgba(245,158,11,0.15); border-radius: 10px; padding: 1rem; text-align: center;">
                <div style="font-size: 1.8rem; font-weight: 700; color: #fbbf24;">{{ $monthLate }}</div>
                <div style="font-size: 0.72rem; color: #64748b; margin-top: 0.25rem;">Terlambat</div>
            </div>
        </div>
        <div style="margin-top: 1rem; font-size: 0.72rem; color: #64748b; text-align: center;">
            {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}
        </div>
    </div>

    {{-- Leave Balance --}}
    <div class="card">
        <div style="font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1rem;">Sisa Cuti Tahunan</div>
        @php
            $leaveQuota = auth()->user()->annual_leave_quota ?? 12;
            $leaveUsed = auth()->user()->annual_leave_used ?? 0;
            $leaveRemaining = max(0, $leaveQuota - $leaveUsed);
            $pct = $leaveQuota > 0 ? round(($leaveUsed / $leaveQuota) * 100) : 0;
        @endphp
        <div style="text-align: center; margin-bottom: 1rem;">
            <div style="font-size: 2.5rem; font-weight: 700; color: #a78bfa;">{{ $leaveRemaining }}</div>
            <div style="font-size: 0.75rem; color: #64748b;">dari {{ $leaveQuota }} hari</div>
        </div>
        <div style="background: rgba(255,255,255,0.06); border-radius: 99px; height: 6px; overflow: hidden;">
            <div style="width: {{ $pct }}%; height: 100%; background: linear-gradient(90deg, #6366f1, #8b5cf6); border-radius: 99px;"></div>
        </div>
        <div style="display: flex; justify-content: space-between; margin-top: 0.4rem; font-size: 0.68rem; color: #64748b;">
            <span>Terpakai: {{ $leaveUsed }} hari</span>
            <span>{{ $pct }}%</span>
        </div>
        <div style="margin-top: 0.75rem;">
            <a href="{{ route('leave.create') }}" class="btn btn-secondary btn-sm" style="width: 100%; justify-content: center;">Ajukan Cuti</a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    {{-- Recent Attendance --}}
    <div class="card">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
            <h3 style="font-size: 0.9rem; font-weight: 600;">Riwayat Absensi Terakhir</h3>
            <a href="{{ route('attendance.history') }}" style="font-size: 0.75rem; color: #16a34a;">Lihat semua →</a>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Masuk</th>
                        <th>Pulang</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentAttendances ?? [] as $att)
                    <tr>
                        <td style="font-size: 0.78rem;">{{ \Carbon\Carbon::parse($att->date)->format('d M') }}</td>
                        <td style="font-family: monospace; font-size: 0.78rem;">{{ $att->check_in_time ? \Carbon\Carbon::parse($att->check_in_time)->format('H:i') : '-' }}</td>
                        <td style="font-family: monospace; font-size: 0.78rem;">{{ $att->check_out_time ? \Carbon\Carbon::parse($att->check_out_time)->format('H:i') : '-' }}</td>
                        <td>
                            @if($att->status === 'present')
                                <span class="badge badge-success">Hadir</span>
                            @elseif($att->status === 'late')
                                <span class="badge badge-warning">Terlambat ({{ $att->late_minutes }} menit)</span>
                            @elseif($att->status === 'absent')
                                <span class="badge badge-danger">Absen</span>
                            @else
                                <span class="badge badge-gray">{{ $att->status }}</span>
                            @endif

                            @if($att->early_out_minutes > 0)
                                <span class="badge badge-orange" style="margin-top: 0.25rem; display: inline-block;">Pulang Cepat ({{ $att->early_out_minutes }} mnt)</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: #64748b; padding: 2rem;">Belum ada data absensi</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Announcements --}}
    <div class="card">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
            <h3 style="font-size: 0.9rem; font-weight: 600; color: var(--t1);">Pengumuman Terbaru</h3>
            <a href="{{ route('announcements.index') }}" style="font-size: 0.75rem; color: var(--em);">Lihat semua →</a>
        </div>
        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            @forelse($announcements as $ann)
            <a href="{{ route('announcements.show', $ann) }}" style="display: block; padding: 0.75rem; background: var(--bg-base); border: 1px solid var(--border-soft); border-radius: 10px; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='var(--bg-hover)'" onmouseout="this.style.background='var(--bg-base)'">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.25rem;">
                    <span class="badge badge-{{ $ann->category_color ?? 'info' }}" style="font-size: 0.65rem;">{{ $ann->category_label ?? $ann->category }}</span>
                    <span style="font-size: 0.65rem; color: var(--t4);">{{ $ann->created_at->diffForHumans() }}</span>
                </div>
                <div style="font-size: 0.82rem; font-weight: 700; color: var(--t1);">{{ Str::limit($ann->title, 60) }}</div>
                <div style="font-size: 0.72rem; color: var(--t3); margin-top: 0.2rem;">{{ Str::limit(strip_tags($ann->content), 80) }}</div>
            </a>
            @empty
            <div style="text-align: center; color: #64748b; padding: 2rem; font-size: 0.82rem;">Belum ada pengumuman</div>
            @endforelse
        </div>
    </div>
</div>

{{-- Pending Requests --}}
@if(isset($pendingRequests) && $pendingRequests->count() > 0)
<div class="card" style="margin-top: 1.5rem;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
        <h3 style="font-size: 0.9rem; font-weight: 600;">Pengajuan Menunggu Persetujuan</h3>
        <span class="badge badge-warning">{{ $pendingRequests->count() }} Pending</span>
    </div>
    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
        @foreach($pendingRequests as $req)
        <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem; background: rgba(245,158,11,0.05); border: 1px solid rgba(245,158,11,0.15); border-radius: 8px;">
            <div>
                <div style="font-size: 0.82rem; font-weight: 500;">{{ $req->type_label ?? $req->type }}</div>
                <div style="font-size: 0.72rem; color: #64748b;">{{ $req->created_at->format('d M Y') }}</div>
            </div>
            <span class="badge badge-warning">Menunggu</span>
        </div>
        @endforeach
    </div>
</div>
@endif
@endsection
