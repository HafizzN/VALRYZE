@extends('layouts.app')

@section('title', 'Laporan Kehadiran')
@section('page-title', 'Laporan Kehadiran')
@section('breadcrumb', 'Laporan / Kehadiran')

@section('content')
<div class="space-y-6">
    <!-- Header & Export Buttons -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Laporan Kehadiran Karyawan</h2>
            <p class="text-xs text-slate-500">Analisis statistika kehadiran harian dan bulanan karyawan</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <!-- Export Excel -->
            <a href="{{ route('reports.attendance.export', array_merge(request()->query(), ['format' => 'excel'])) }}" class="btn btn-success btn-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Ekspor Excel
            </a>
            <!-- Export PDF -->
            <a href="{{ route('reports.attendance.export', array_merge(request()->query(), ['format' => 'pdf'])) }}" class="btn btn-primary btn-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Unduh PDF
            </a>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card">
        <form method="GET" action="{{ route('reports.attendance') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Month Picker -->
            <div class="form-group mb-0">
                <label class="form-label" for="month">Bulan Laporan</label>
                <input type="month" name="month" id="month" class="form-control" value="{{ $month }}">
            </div>

            <!-- Division Select -->
            <div class="form-group mb-0">
                <label class="form-label" for="division_id">Divisi</label>
                <select name="division_id" id="division_id" class="form-control">
                    <option value="">Semua Divisi</option>
                    @foreach($divisions as $div)
                        <option value="{{ $div->id }}" {{ request('division_id') == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Employee Select -->
            <div class="form-group mb-0">
                <label class="form-label" for="user_id">Karyawan</label>
                <select name="user_id" id="user_id" class="form-control">
                    <option value="">Semua Karyawan</option>
                    @foreach($users as $usr)
                        <option value="{{ $usr->id }}" {{ request('user_id') == $usr->id ? 'selected' : '' }}>{{ $usr->name }} ({{ $usr->nik }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Buttons -->
            <div class="flex items-end gap-2">
                <button type="submit" class="btn btn-primary flex-1">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Tampilkan
                </button>
                @if(request()->anyFilled(['month', 'division_id', 'user_id']))
                    <a href="{{ route('reports.attendance') }}" class="btn btn-secondary">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Summary Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <!-- Present -->
        <div class="stat-card border-emerald-500/10 bg-emerald-950/5">
            <div class="stat-value text-emerald-400 font-mono">{{ $summary['present'] }}</div>
            <div class="stat-label">Hadir (On Time)</div>
        </div>

        <!-- Late -->
        <div class="stat-card border-amber-500/10 bg-amber-50">
            <div class="stat-value text-amber-600 font-mono">{{ $summary['late'] }}</div>
            <div class="stat-label">Terlambat</div>
        </div>

        <!-- Permission -->
        <div class="stat-card border-purple-500/10 bg-purple-950/5">
            <div class="stat-value text-purple-400 font-mono">{{ $summary['permission'] }}</div>
            <div class="stat-label">Izin Sakit / Dinas</div>
        </div>

        <!-- Leave -->
        <div class="stat-card border-orange-500/10 bg-orange-950/5">
            <div class="stat-value text-orange-400 font-mono">{{ $summary['leave'] }}</div>
            <div class="stat-label">Cuti Tahunan</div>
        </div>

        <!-- Absent -->
        <div class="stat-card border-red-500/10 bg-red-950/5 col-span-2 md:col-span-1">
            <div class="stat-value text-red-400 font-mono">{{ $summary['absent'] }}</div>
            <div class="stat-label">Mangkir (Alpa)</div>
        </div>
    </div>

    <!-- Daily Log Table -->
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-slate-700">Rincian Log Kehadiran Harian</h3>
            <span class="text-[10px] text-slate-500">Menampilkan halaman {{ $attendances->currentPage() }} dari {{ $attendances->lastPage() }}</span>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>NIK</th>
                        <th>Karyawan</th>
                        <th>Divisi</th>
                        <th>Jam Masuk</th>
                        <th>Jam Pulang</th>
                        <th>Terlambat</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $attendance)
                        <tr>
                            <!-- Date -->
                            <td class="font-mono text-xs">{{ \Carbon\Carbon::parse($attendance->date)->format('d M Y') }}</td>
                            
                            <!-- NIK -->
                            <td class="font-mono text-xs text-slate-600">{{ $attendance->user?->nik ?? '-' }}</td>
                            
                            <!-- Karyawan -->
                            <td class="font-bold text-slate-800">{{ $attendance->user?->name ?? '-' }}</td>
                            
                            <!-- Divisi -->
                            <td>{{ $attendance->user?->division?->name ?? '-' }}</td>
                            
                            <!-- Check In -->
                            <td class="font-mono font-semibold {{ $attendance->status == 'late' ? 'text-amber-600' : 'text-slate-700' }}">
                                {{ $attendance->check_in_time ? \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i:s') : '--:--:--' }}
                            </td>
                            
                            <!-- Check Out -->
                            <td class="font-mono font-semibold text-slate-700">
                                {{ $attendance->check_out_time ? \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i:s') : '--:--:--' }}
                            </td>
                            
                            <!-- Lateness Minutes -->
                            <td class="font-mono text-xs">
                                @if($attendance->late_minutes > 0)
                                    <span class="text-amber-600 font-semibold">{{ $attendance->late_minutes }} menit</span>
                                @else
                                    <span class="text-slate-500">-</span>
                                @endif
                            </td>
                            
                            <!-- Status Badge -->
                            <td>
                                @switch($attendance->status)
                                    @case('present')
                                        <span class="badge badge-success">Hadir</span>
                                        @break
                                    @case('late')
                                        <span class="badge badge-warning">Terlambat</span>
                                        @break
                                    @case('absent')
                                        <span class="badge badge-danger">Mangkir</span>
                                        @break
                                    @case('leave')
                                        <span class="badge badge-orange">Cuti</span>
                                        @break
                                    @case('permission')
                                        <span class="badge badge-purple">Izin</span>
                                        @break
                                    @default
                                        <span class="badge badge-gray">{{ $attendance->status }}</span>
                                @endswitch

                                @if($attendance->early_out_minutes > 0)
                                    <span class="badge badge-orange" style="margin-top: 0.25rem; display: inline-block;">Pulang Cepat ({{ $attendance->early_out_minutes }} mnt)</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-10 text-slate-500">
                                Tidak ada log kehadiran untuk filter terpilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attendances->hasPages())
            <div class="mt-4">
                {{ $attendances->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
