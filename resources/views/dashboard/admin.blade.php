@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')
@section('breadcrumb', 'Selamat datang, ' . auth()->user()->name)

@section('content')
<div class="space-y-6">

    {{-- System Status Section --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @if($queueStatus === 'offline')
            <div style="background: linear-gradient(135deg, rgba(239,68,68,0.1), rgba(220,38,38,0.1)); border: 1px solid rgba(239,68,68,0.25); border-radius: 12px; padding: 1rem 1.25rem; display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 36px; height: 36px; background: rgba(239,68,68,0.2); border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg class="w-5 h-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size: 0.85rem; font-weight: 600; color: #f87171;">Layanan Antrean (Queue Worker) Mati</div>
                    <div style="font-size: 0.75rem; color: #94a3b8;">Pekerja antrean tidak merespons. Pengiriman email notifikasi akan tertunda. Hubungi developer atau jalankan perintah `php artisan queue:work` di server.</div>
                </div>
            </div>
        @else
            <div style="background: linear-gradient(135deg, rgba(16,185,129,0.05), rgba(5,150,105,0.05)); border: 1px solid rgba(16,185,129,0.15); border-radius: 12px; padding: 1rem 1.25rem; display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 36px; height: 36px; background: rgba(16,185,129,0.15); border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg class="w-5 h-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size: 0.85rem; font-weight: 600; color: #34d399;">Layanan Antrean Aktif</div>
                    <div style="font-size: 0.75rem; color: #94a3b8;">Pekerja antrean berjalan normal dan siap memproses tugas latar belakang.</div>
                </div>
            </div>
        @endif

        @if($failedJobsCount > 0)
            <div style="background: linear-gradient(135deg, rgba(245,158,11,0.1), rgba(217,119,6,0.1)); border: 1px solid rgba(245,158,11,0.25); border-radius: 12px; padding: 1rem 1.25rem; display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 36px; height: 36px; background: rgba(245,158,11,0.2); border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg class="w-5 h-5 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size: 0.85rem; font-weight: 600; color: #fbbf24;">Terdeteksi {{ $failedJobsCount }} Pekerjaan Gagal</div>
                    <div style="font-size: 0.75rem; color: #94a3b8;">Ada beberapa pekerjaan antrean yang gagal diproses. Hubungi tim IT untuk memeriksa daftar kegagalan pekerjaan.</div>
                </div>
            </div>
        @else
            <div style="background: linear-gradient(135deg, rgba(16,185,129,0.05), rgba(5,150,105,0.05)); border: 1px solid rgba(16,185,129,0.15); border-radius: 12px; padding: 1rem 1.25rem; display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 36px; height: 36px; background: rgba(16,185,129,0.15); border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg class="w-5 h-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size: 0.85rem; font-weight: 600; color: #34d399;">Pekerjaan Gagal Nihil</div>
                    <div style="font-size: 0.75rem; color: #94a3b8;">Seluruh pekerjaan antrean berhasil diproses tanpa hambatan.</div>
                </div>
            </div>
        @endif
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">

        <div class="stat-card" style="--accent: #6366f1;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <div class="stat-label">Total Karyawan</div>
                    <div class="stat-value" style="color: #a5b4fc;">{{ $totalEmployees }}</div>
                </div>
                <div style="width: 40px; height: 40px; background: rgba(99,102,241,0.15); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                    <svg class="w-5 h-5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
            </div>
            <div class="stat-change" style="color: #64748b;">Aktif</div>
        </div>

        <div class="stat-card">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <div class="stat-label">Hadir Hari Ini</div>
                    <div class="stat-value" style="color: #34d399;">{{ $presentToday }}</div>
                </div>
                <div style="width: 40px; height: 40px; background: rgba(16,185,129,0.15); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                    <svg class="w-5 h-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="stat-change badge badge-success">Hadir</div>
        </div>

        <div class="stat-card">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <div class="stat-label">Terlambat</div>
                    <div class="stat-value" style="color: #fbbf24;">{{ $lateToday }}</div>
                </div>
                <div style="width: 40px; height: 40px; background: rgba(245,158,11,0.15); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                    <svg class="w-5 h-5 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="stat-change badge badge-warning">Terlambat</div>
        </div>

        <div class="stat-card">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <div class="stat-label">Izin</div>
                    <div class="stat-value" style="color: #60a5fa;">{{ $onPermissionToday }}</div>
                </div>
                <div style="width: 40px; height: 40px; background: rgba(59,130,246,0.15); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                    <svg class="w-5 h-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
            </div>
            <div class="stat-change badge badge-info">Izin</div>
        </div>

        <div class="stat-card">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <div class="stat-label">Cuti</div>
                    <div class="stat-value" style="color: #a78bfa;">{{ $onLeaveToday }}</div>
                </div>
                <div style="width: 40px; height: 40px; background: rgba(139,92,246,0.15); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                    <svg class="w-5 h-5 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <div class="stat-change badge badge-purple">Cuti</div>
        </div>

        <div class="stat-card">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <div class="stat-label">Tidak Hadir</div>
                    <div class="stat-value" style="color: #f87171;">{{ max(0, $absentToday) }}</div>
                </div>
                <div style="width: 40px; height: 40px; background: rgba(239,68,68,0.15); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                    <svg class="w-5 h-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
            </div>
            <div class="stat-change badge badge-danger">Absen</div>
        </div>
    </div>

    {{-- Pending approvals --}}
    @if($pendingLeave > 0 || $pendingPermission > 0 || $pendingOvertime > 0)
    <div style="background: linear-gradient(135deg, rgba(245,158,11,0.1), rgba(249,115,22,0.1)); border: 1px solid rgba(245,158,11,0.25); border-radius: 12px; padding: 1rem 1.25rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: gap: 1rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <div style="width: 36px; height: 36px; background: rgba(245,158,11,0.2); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                <svg class="w-5 h-5 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div style="font-size: 0.85rem; font-weight: 600; color: #fbbf24;">Menunggu Persetujuan</div>
                <div style="font-size: 0.75rem; color: #64748b;">
                    @if($pendingLeave) {{ $pendingLeave }} cuti @endif
                    @if($pendingPermission) &bull; {{ $pendingPermission }} izin @endif
                    @if($pendingOvertime) &bull; {{ $pendingOvertime }} lembur @endif
                </div>
            </div>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            @if($pendingLeave) <a href="{{ route('leave.index') }}" class="btn btn-secondary btn-sm">Cuti</a> @endif
            @if($pendingPermission) <a href="{{ route('permission.index') }}" class="btn btn-secondary btn-sm">Izin</a> @endif
            @if($pendingOvertime) <a href="{{ route('overtime.index') }}" class="btn btn-secondary btn-sm">Lembur</a> @endif
        </div>
    </div>
    @endif

    {{-- Chart + Announcements --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Attendance Chart --}}
        <div class="card lg:col-span-2">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                <div>
                    <h3 style="font-size: 0.95rem; font-weight: 600; color: #e2e8f0;">Grafik Kehadiran</h3>
                    <p style="font-size: 0.72rem; color: #64748b; margin-top: 0.2rem;">7 hari terakhir</p>
                </div>
                <a href="{{ route('reports.attendance') }}" class="btn btn-secondary btn-sm">Lihat Laporan</a>
            </div>
            <canvas id="attendanceChart" height="80"></canvas>
        </div>

        {{-- Announcements --}}
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 0.9rem; font-weight: 600;">Pengumuman</h3>
                <a href="{{ route('announcements.index') }}" style="font-size: 0.72rem; color: #6366f1;">Semua</a>
            </div>
            @forelse($announcements as $ann)
            <div style="padding: 0.75rem; border-radius: 8px; background: rgba(255,255,255,0.03); margin-bottom: 0.5rem; border-left: 3px solid {{ match($ann->category) {'info'=>'#3b82f6','meeting'=>'#8b5cf6','holiday'=>'#10b981','activity'=>'#f59e0b',default=>'#64748b'} }};">
                @if($ann->is_pinned)
                <span style="font-size: 0.6rem; color: #fbbf24; font-weight: 600;">📌 PENTING</span>
                @endif
                <div style="font-size: 0.8rem; font-weight: 500; color: #e2e8f0; margin-top: 0.2rem;">{{ $ann->title }}</div>
                <div style="font-size: 0.7rem; color: #64748b; margin-top: 0.25rem;">{{ $ann->published_at?->format('d M Y') }}</div>
            </div>
            @empty
            <p style="font-size: 0.8rem; color: #64748b; text-align: center; padding: 1rem;">Belum ada pengumuman</p>
            @endforelse
        </div>
    </div>

    {{-- Recent Attendance Table --}}
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <h3 style="font-size: 0.95rem; font-weight: 600;">Absensi Hari Ini</h3>
            <a href="{{ route('attendance.history') }}" class="btn btn-secondary btn-sm">Lihat Semua</a>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Karyawan</th>
                        <th>Jam Masuk</th>
                        <th>Jam Pulang</th>
                        <th>Jarak</th>
                        <th>Status</th>
                        <th>Keterlambatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentAttendances as $att)
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div class="avatar" style="width: 32px; height: 32px; font-size: 0.65rem; overflow: hidden;">
                                    @if($att->user?->photo)
                                        <img src="{{ $att->user->photo_url }}" alt="{{ $att->user->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                    @else
                                        {{ $att->user->initials }}
                                    @endif
                                </div>
                                <div>
                                    <div style="font-size: 0.82rem; font-weight: 500; color: #e2e8f0;">{{ $att->user->name }}</div>
                                    <div style="font-size: 0.7rem; color: #64748b;">{{ $att->user->division->name ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $att->check_in_time ?? '-' }}</td>
                        <td>
                            @if($att->check_out_time)
                                {{ $att->check_out_time }}
                            @else
                                <span style="color: #475569;">Belum</span>
                            @endif
                        </td>
                        <td>{{ $att->check_in_distance ? $att->check_in_distance . 'm' : '-' }}</td>
                        <td>
                            @php
                                $badgeMap = ['present' => 'success', 'late' => 'warning', 'absent' => 'danger', 'permission' => 'info', 'leave' => 'purple', 'sick' => 'orange'];
                                $badgeClass = $badgeMap[$att->status] ?? 'gray';
                                $statusLabel = ['present' => 'Hadir', 'late' => 'Terlambat', 'absent' => 'Absen', 'permission' => 'Izin', 'leave' => 'Cuti', 'sick' => 'Sakit'][$att->status] ?? $att->status;
                            @endphp
                            <span class="badge badge-{{ $badgeClass }}">{{ $statusLabel }}</span>
                        </td>
                        <td>{{ $att->late_minutes > 0 ? $att->late_minutes . ' menit' : '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align: center; color: #64748b; padding: 2rem;">Belum ada absensi hari ini</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const chartData = @json($chartData);
const ctx = document.getElementById('attendanceChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: chartData.map(d => d.date),
        datasets: [
            {
                label: 'Hadir',
                data: chartData.map(d => d.present),
                backgroundColor: 'rgba(99,102,241,0.7)',
                borderRadius: 6,
            },
            {
                label: 'Terlambat',
                data: chartData.map(d => d.late),
                backgroundColor: 'rgba(245,158,11,0.7)',
                borderRadius: 6,
            },
            {
                label: 'Tidak Hadir',
                data: chartData.map(d => d.absent),
                backgroundColor: 'rgba(239,68,68,0.5)',
                borderRadius: 6,
            },
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { labels: { color: '#94a3b8', font: { size: 11 } } },
            tooltip: { backgroundColor: '#1a2235', titleColor: '#e2e8f0', bodyColor: '#94a3b8' }
        },
        scales: {
            x: { grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { color: '#64748b', font: { size: 11 } } },
            y: { grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { color: '#64748b', font: { size: 11 } }, beginAtZero: true }
        }
    }
});
</script>
@endpush
