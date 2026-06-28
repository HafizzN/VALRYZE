@extends('layouts.app')

@section('title', 'Master Data Shift Kerja')
@section('page-title', 'Shift Kerja')
@section('breadcrumb', 'Master Data › Shift')

@section('content')
<div class="space-y-5 animate-fadeSlideIn">
    {{-- Header --}}
    <div style="display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:1rem;margin-bottom:0.25rem;">
        <div>
            <h2 style="font-size:1.1rem;font-weight:800;color:var(--t1);letter-spacing:-0.01em;">Manajemen Shift & Waktu Kerja</h2>
            <p style="font-size:0.78rem;color:var(--t4);margin-top:0.25rem;">Kelola jam kerja operasional, batas keterlambatan, dan toleransi check-in karyawan</p>
        </div>
        <a href="{{ route('master.shifts.create') }}" class="btn btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Shift
        </a>
    </div>

    {{-- Table Card --}}
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
                        <th style="text-align:center;">Status Overnight</th>
                        <th style="text-align:center;">Status Aktif</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($shifts as $shift)
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:0.5rem;">
                                    @if($shift->color)
                                        <span style="width:10px;height:10px;border-radius:50%;border:1px solid var(--border-soft);background-color:{{ $shift->color }};display:inline-block;"></span>
                                    @endif
                                    <span style="font-weight:800;color:var(--t1);font-size:0.82rem;">{{ $shift->name }}</span>
                                </div>
                            </td>
                            <td>
                                <span style="font-family:'JetBrains Mono',monospace;font-size:0.8rem;font-weight:700;color:var(--t2);">{{ $shift->start_time }}</span>
                            </td>
                            <td>
                                <span style="font-family:'JetBrains Mono',monospace;font-size:0.8rem;font-weight:700;color:var(--t2);">{{ $shift->end_time }}</span>
                            </td>
                            <td>
                                @if($shift->late_tolerance_minutes)
                                    <span style="font-family:'JetBrains Mono',monospace;font-size:0.78rem;font-weight:800;color:#FCD34D;">{{ $shift->late_tolerance_minutes }}</span> <span style="font-size:0.7rem;color:var(--t4);">Menit</span>
                                @else
                                    <span style="font-size:0.72rem;color:var(--t5);font-style:italic;">Tidak ada</span>
                                @endif
                            </td>
                            <td>
                                @if($shift->early_out_tolerance_minutes)
                                    <span style="font-family:'JetBrains Mono',monospace;font-size:0.78rem;font-weight:800;color:#C4B5FD;">{{ $shift->early_out_tolerance_minutes }}</span> <span style="font-size:0.7rem;color:var(--t4);">Menit</span>
                                @else
                                    <span style="font-size:0.72rem;color:var(--t5);font-style:italic;">Tidak ada</span>
                                @endif
                            </td>
                            <td style="text-align:center;">
                                @if($shift->is_overnight)
                                    <span class="badge badge-purple">Ya</span>
                                @else
                                    <span class="badge badge-gray" style="opacity:0.65;">Tidak</span>
                                @endif
                            </td>
                            <td style="text-align:center;">
                                @if($shift->is_active)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-gray">Non-Aktif</span>
                                @endif
                            </td>
                            <td>
                                <div style="display:flex;justify-content:flex-end;gap:0.4rem;">
                                    <a href="{{ route('master.shifts.edit', $shift->id) }}" class="btn btn-secondary btn-sm" style="color:var(--em);">
                                        Edit
                                    </a>
                                    <form action="{{ route('master.shifts.destroy', $shift->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus shift ini?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align:center;padding:3.5rem;color:var(--t4);">
                                <div style="font-size:2rem;margin-bottom:0.75rem;">⏱</div>
                                <div style="font-weight:700;color:var(--t3);">Belum ada data shift terdaftar</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($shifts) && $shifts instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $shifts->hasPages())
            <div style="margin-top:1.5rem;">
                {{ $shifts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
