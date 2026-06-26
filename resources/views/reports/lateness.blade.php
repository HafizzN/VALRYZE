@extends('layouts.app')

@section('title', 'Laporan Keterlambatan')
@section('page-title', 'Laporan Keterlambatan')
@section('breadcrumb', 'Laporan / Keterlambatan')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Analisis Keterlambatan Karyawan</h2>
            <p class="text-xs text-slate-500">Menganalisis statistik keterlambatan kehadiran bulanan untuk perbaikan disiplin</p>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card">
        <form method="GET" action="{{ route('reports.lateness') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="form-group mb-0">
                <label class="form-label" for="month">Pilih Bulan Laporan</label>
                <input type="month" name="month" id="month" class="form-control" value="{{ $month }}">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="btn btn-primary flex-1">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Tampilkan Data
                </button>
                @if(request()->filled('month'))
                    <a href="{{ route('reports.lateness') }}" class="btn btn-secondary">Reset</a>
                @endif
            </div>
            <div></div> <!-- Spacer -->
        </form>
    </div>

    <!-- Summary Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @php
            $totalLateCount = $lateRecords->total();
            $totalLateMinutes = $lateRecords->sum('late_minutes');
            $maxLateRecord = $lateRecords->max('late_minutes');
        @endphp
        <div class="stat-card border-amber-500/10 bg-amber-50">
            <div class="stat-value text-amber-600 font-mono">{{ $totalLateCount }}</div>
            <div class="stat-label">Total Frekuensi Keterlambatan</div>
        </div>
        
        <div class="stat-card border-orange-500/10 bg-orange-950/5">
            <div class="stat-value text-orange-400 font-mono">{{ $totalLateMinutes }} Min</div>
            <div class="stat-label">Total Akumulasi Waktu Terlambat</div>
        </div>

        <div class="stat-card border-red-500/10 bg-red-950/5">
            <div class="stat-value text-red-400 font-mono">{{ $maxLateRecord ?? 0 }} Min</div>
            <div class="stat-label">Keterlambatan Terlama (Maksimal)</div>
        </div>
    </div>

    <!-- Lateness Records Table -->
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-slate-700">Daftar Karyawan Terlambat</h3>
            <span class="text-[10px] text-slate-500">Urutan keterlambatan terlama teratas</span>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>NIK</th>
                        <th>Karyawan</th>
                        <th>Divisi</th>
                        <th>Shift Kerja</th>
                        <th>Jam Masuk</th>
                        <th>Waktu Terlambat</th>
                        <th class="text-right">Toleransi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lateRecords as $record)
                        <tr>
                            <!-- Date -->
                            <td class="font-mono text-xs">{{ \Carbon\Carbon::parse($record->date)->format('d M Y') }}</td>
                            
                            <!-- NIK -->
                            <td class="font-mono text-xs text-slate-600">{{ $record->user->nik }}</td>
                            
                            <!-- Karyawan -->
                            <td class="font-bold text-slate-800">{{ $record->user->name }}</td>
                            
                            <!-- Divisi -->
                            <td>{{ $record->user->division->name ?? '-' }}</td>
                            
                            <!-- Shift -->
                            <td>{{ $record->user->shift->name ?? '-' }}</td>
                            
                            <!-- Jam Masuk -->
                            <td class="font-mono font-semibold text-red-400">{{ $record->check_in_time ? \Carbon\Carbon::parse($record->check_in_time)->format('H:i:s') : '--:--:--' }}</td>
                            
                            <!-- Waktu Terlambat -->
                            <td>
                                <span class="badge badge-danger font-mono font-semibold">
                                    {{ $record->late_minutes }} menit
                                </span>
                            </td>

                            <!-- Shift Tolerance -->
                            <td class="text-right font-mono text-xs text-slate-500">
                                {{ $record->user->shift->late_tolerance_minutes ?? 0 }} menit
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-10 text-slate-500">
                                Tidak ada data keterlambatan yang tercatat pada bulan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($lateRecords->hasPages())
            <div class="mt-4">
                {{ $lateRecords->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
