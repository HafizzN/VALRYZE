@extends('layouts.app')

@section('title', 'Master Data Shift Kerja')
@section('page-title', 'Shift Kerja')
@section('breadcrumb', 'Master Data / Shift')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Manajemen Shift & Waktu Kerja</h2>
            <p class="text-xs text-slate-500">Kelola jam kerja operasional, batas keterlambatan, dan toleransi check-in karyawan</p>
        </div>
        <div>
            <a href="{{ route('master.shifts.create') }}" class="btn btn-primary">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Shift
            </a>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Nama Shift</th>
                        <th>Jam Masuk</th>
                        <th>Jam Pulang</th>
                        <th>Toleransi Terlambat</th>
                        <th>Toleransi Pulang Cepat</th>
                        <th>Status Overnight</th>
                        <th>Status Aktif</th>
                        <th class="text-right" style="width: 200px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($shifts as $shift)
                        <tr>
                            <td class="font-bold text-slate-800">
                                <div class="flex items-center gap-2">
                                    @if($shift->color)
                                        <span class="w-3 h-3 rounded-full border border-slate-200" style="background-color: {{ $shift->color }}"></span>
                                    @endif
                                    <span>{{ $shift->name }}</span>
                                </div>
                            </td>
                            <td class="font-mono font-semibold text-slate-700">{{ $shift->start_time }}</td>
                            <td class="font-mono font-semibold text-slate-700">{{ $shift->end_time }}</td>
                            <td class="font-mono text-xs text-slate-600">
                                @if($shift->late_tolerance_minutes)
                                    <span class="text-amber-600 font-bold">{{ $shift->late_tolerance_minutes }}</span> Menit
                                @else
                                    <span class="text-slate-500 italic">Tidak ada</span>
                                @endif
                            </td>
                            <td class="font-mono text-xs text-slate-600">
                                @if($shift->early_out_tolerance_minutes)
                                    <span class="text-indigo-600 font-bold">{{ $shift->early_out_tolerance_minutes }}</span> Menit
                                @else
                                    <span class="text-slate-500 italic">Tidak ada</span>
                                @endif
                            </td>
                            <td>
                                @if($shift->is_overnight)
                                    <span class="badge badge-purple">Ya (Luar Hari)</span>
                                @else
                                    <span class="badge badge-gray">Tidak</span>
                                @endif
                            </td>
                            <td>
                                @if($shift->is_active)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-gray">Non-Aktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('master.shifts.edit', $shift->id) }}" class="btn btn-secondary btn-sm text-emerald-700">
                                        Edit
                                    </a>
                                    <form action="{{ route('master.shifts.destroy', $shift->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus shift ini?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm text-xs">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-slate-500">
                                Belum ada data shift terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($shifts) && $shifts instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $shifts->hasPages())
            <div class="mt-4">
                {{ $shifts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
