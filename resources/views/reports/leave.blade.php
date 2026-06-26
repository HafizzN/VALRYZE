@extends('layouts.app')

@section('title', 'Laporan Cuti Karyawan')
@section('page-title', 'Laporan Cuti')
@section('breadcrumb', 'Laporan / Cuti')

@section('content')
<div class="space-y-6" x-data="{ activePanel: 'balance' }">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Laporan & Kuota Cuti Karyawan</h2>
            <p class="text-xs text-slate-500">Pantau sisa kuota cuti tahunan karyawan serta histori pengajuan cuti yang disetujui</p>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card col-span-3">
        <form method="GET" action="{{ route('reports.leave') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="form-group mb-0">
                <label class="form-label" for="year">Tahun Laporan</label>
                <select name="year" id="year" class="form-control">
                    @for($y = now()->year; $y >= now()->year - 4; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="btn btn-primary flex-1">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Tampilkan Data
                </button>
            </div>
            <div></div> <!-- Spacer -->
        </form>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex border-b border-slate-200 gap-1 bg-slate-50/50 p-1.5 rounded-lg max-w-md">
        <button @click="activePanel = 'balance'" :class="activePanel === 'balance' ? 'bg-emerald-700 text-white' : 'text-slate-600 hover:text-slate-800'" class="flex-1 py-2 text-center text-xs font-semibold rounded-md transition">
            Kuota & Sisa Cuti
        </button>
        <button @click="activePanel = 'history'" :class="activePanel === 'history' ? 'bg-emerald-700 text-white' : 'text-slate-600 hover:text-slate-800'" class="flex-1 py-2 text-center text-xs font-semibold rounded-md transition">
            Riwayat Penggunaan Cuti
        </button>
    </div>

    <!-- Panel 1: Leave Balance Summary -->
    <div x-show="activePanel === 'balance'" class="card" x-transition>
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-slate-700">Kuota Cuti Tahunan Aktif</h3>
            <span class="text-[10px] text-slate-500">Menampilkan seluruh karyawan aktif</span>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>NIK</th>
                        <th>Karyawan</th>
                        <th>Divisi</th>
                        <th>Kuota Tahunan</th>
                        <th>Telah Digunakan</th>
                        <th>Sisa Saldo Cuti</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaveBalance as $item)
                        <tr>
                            <td class="font-mono text-xs text-slate-600">{{ $item['user']->nik }}</td>
                            <td class="font-bold text-slate-800">{{ $item['user']->name }}</td>
                            <td>{{ $item['user']->division->name ?? '-' }}</td>
                            <td class="font-mono text-xs text-slate-700 font-semibold">{{ $item['user']->annual_leave_quota }} hari</td>
                            <td class="font-mono text-xs text-amber-500 font-semibold">{{ $item['user']->annual_leave_used }} hari</td>
                            <td>
                                @if($item['remaining'] > 3)
                                    <span class="badge badge-success font-mono font-semibold">{{ $item['remaining'] }} hari</span>
                                @elseif($item['remaining'] > 0)
                                    <span class="badge badge-warning font-mono font-semibold">{{ $item['remaining'] }} hari</span>
                                @else
                                    <span class="badge badge-danger font-mono font-semibold">{{ $item['remaining'] }} hari</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-slate-500">
                                Tidak ada data karyawan ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Panel 2: Leave History Logs -->
    <div x-show="activePanel === 'history'" class="card" x-transition style="display: none;">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-slate-700">Daftar Cuti yang Disetujui (Tahun {{ $year }})</h3>
            <span class="text-[10px] text-slate-500">Menampilkan halaman {{ $leaves->currentPage() }} dari {{ $leaves->lastPage() }}</span>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal Cuti</th>
                        <th>NIK</th>
                        <th>Karyawan</th>
                        <th>Divisi</th>
                        <th>Durasi</th>
                        <th>Tipe Cuti</th>
                        <th>Alasan / Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaves as $leave)
                        <tr>
                            <!-- Leave Date Range -->
                            <td class="font-mono text-xs">
                                {{ \Carbon\Carbon::parse($leave->start_date)->format('d M Y') }} s/d 
                                {{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}
                            </td>
                            <!-- NIK -->
                            <td class="font-mono text-xs text-slate-600">{{ $leave->user->nik }}</td>
                            <!-- Name -->
                            <td class="font-bold text-slate-800">{{ $leave->user->name }}</td>
                            <!-- Division -->
                            <td>{{ $leave->user->division->name ?? '-' }}</td>
                            <!-- Duration -->
                            <td class="font-mono text-xs font-semibold text-emerald-700">
                                {{ $leave->duration ?? \Carbon\Carbon::parse($leave->start_date)->diffInDays(\Carbon\Carbon::parse($leave->end_date)) + 1 }} hari
                            </td>
                            <!-- Type -->
                            <td>
                                @switch($leave->leave_type)
                                    @case('annual')
                                        <span class="badge badge-success">Cuti Tahunan</span>
                                        @break
                                    @case('sick')
                                        <span class="badge badge-purple">Sakit</span>
                                        @break
                                    @case('marriage')
                                        <span class="badge badge-info">Pernikahan</span>
                                        @break
                                    @case('maternity')
                                        <span class="badge badge-orange">Melahirkan</span>
                                        @break
                                    @default
                                        <span class="badge badge-gray">{{ ucfirst($leave->leave_type) }}</span>
                                @endswitch
                            </td>
                            <!-- Reason -->
                            <td class="text-xs text-slate-600 max-w-xs truncate" title="{{ $leave->reason }}">
                                {{ $leave->reason }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-10 text-slate-500">
                                Belum ada riwayat cuti disetujui pada tahun {{ $year }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($leaves->hasPages())
            <div class="mt-4">
                {{ $leaves->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
