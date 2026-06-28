@extends('layouts.app')

@section('title', 'Master Data Jabatan')
@section('page-title', 'Jabatan')
@section('breadcrumb', 'Master Data › Jabatan')

@section('content')
<div class="space-y-5 animate-fadeSlideIn">
    {{-- Header --}}
    <div style="display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:1rem;margin-bottom:0.25rem;">
        <div>
            <h2 style="font-size:1.1rem;font-weight:800;color:var(--t1);letter-spacing:-0.01em;">Manajemen Jabatan & Leveling</h2>
            <p style="font-size:0.78rem;color:var(--t4);margin-top:0.25rem;">Kelola hierarki jabatan, level kompetensi, dan penempatan divisi karyawan</p>
        </div>
        <a href="{{ route('master.positions.create') }}" class="btn btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Jabatan
        </a>
    </div>

    {{-- Table Card --}}
    <div class="card">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 70px">ID</th>
                        <th>Nama Jabatan</th>
                        <th>Divisi</th>
                        <th>Level Hierarki</th>
                        <th>Kode Jabatan</th>
                        <th style="text-align:center;">Status</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($positions as $position)
                        <tr>
                            <td style="font-family:'JetBrains Mono',monospace;font-size:0.75rem;color:var(--t4);">#{{ $position->id }}</td>
                            <td>
                                <div style="font-weight:800;color:var(--t1);font-size:0.82rem;">{{ $position->name }}</div>
                            </td>
                            <td>
                                <span style="font-weight:700;color:var(--t2);font-size:0.8rem;">
                                    {{ $position->division->name ?? 'Semua Divisi' }}
                                </span>
                            </td>
                            <td>
                                @switch($position->level)
                                    @case('director')   <span class="badge badge-danger">Director</span> @break
                                    @case('manager')    <span class="badge badge-orange">Manager</span> @break
                                    @case('supervisor') <span class="badge badge-purple">Supervisor</span> @break
                                    @case('staff')      <span class="badge badge-info">Staff</span> @break
                                    @default            <span class="badge badge-gray">{{ ucfirst($position->level) }}</span>
                                @endswitch
                            </td>
                            <td>
                                <span style="font-family:'JetBrains Mono',monospace;font-size:0.78rem;font-weight:700;color:var(--em);">
                                    {{ $position->code ?? '—' }}
                                </span>
                            </td>
                            <td style="text-align:center;">
                                @if($position->is_active ?? true)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-gray">Non-Aktif</span>
                                @endif
                            </td>
                            <td>
                                <div style="display:flex;justify-content:flex-end;gap:0.4rem;">
                                    <a href="{{ route('master.positions.edit', $position->id) }}" class="btn btn-secondary btn-sm" style="color:var(--em);">
                                        Edit
                                    </a>
                                    <form action="{{ route('master.positions.destroy', $position->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jabatan ini?')" class="inline">
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
                            <td colspan="7" style="text-align:center;padding:3.5rem;color:var(--t4);">
                                <div style="font-size:2rem;margin-bottom:0.75rem;">👔</div>
                                <div style="font-weight:700;color:var(--t3);">Belum ada data jabatan terdaftar</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($positions) && $positions instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $positions->hasPages())
            <div style="margin-top:1.5rem;">
                {{ $positions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
