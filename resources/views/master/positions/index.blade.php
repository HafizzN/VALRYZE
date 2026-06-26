@extends('layouts.app')

@section('title', 'Master Data Jabatan')
@section('page-title', 'Jabatan')
@section('breadcrumb', 'Master Data / Jabatan')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Manajemen Jabatan & Leveling</h2>
            <p class="text-xs text-slate-500">Kelola hierarki jabatan, level kompetensi, dan penempatan divisi karyawan</p>
        </div>
        <div>
            <a href="{{ route('master.positions.create') }}" class="btn btn-primary">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Jabatan
            </a>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 80px">ID</th>
                        <th>Nama Jabatan</th>
                        <th>Divisi</th>
                        <th>Level Hierarki</th>
                        <th>Kode Jabatan</th>
                        <th>Status</th>
                        <th class="text-right" style="width: 200px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($positions as $position)
                        <tr>
                            <td class="font-mono text-xs text-slate-600">#{{ $position->id }}</td>
                            <td class="font-bold text-slate-800">{{ $position->name }}</td>
                            <td>
                                <span class="text-slate-700 font-medium">
                                    {{ $position->division->name ?? 'Semua Divisi' }}
                                </span>
                            </td>
                            <td>
                                @switch($position->level)
                                    @case('director')
                                        <span class="badge badge-danger">Director</span>
                                        @break
                                    @case('manager')
                                        <span class="badge badge-orange">Manager</span>
                                        @break
                                    @case('supervisor')
                                        <span class="badge badge-purple">Supervisor</span>
                                        @break
                                    @case('staff')
                                        <span class="badge badge-info">Staff</span>
                                        @break
                                    @default
                                        <span class="badge badge-gray">{{ ucfirst($position->level) }}</span>
                                @endswitch
                            </td>
                            <td class="font-mono text-xs text-emerald-700 font-semibold">{{ $position->code ?? '-' }}</td>
                            <td>
                                @if($position->is_active ?? true)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-gray">Non-Aktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('master.positions.edit', $position->id) }}" class="btn btn-secondary btn-sm text-emerald-700">
                                        Edit
                                    </a>
                                    <form action="{{ route('master.positions.destroy', $position->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jabatan ini?')" class="inline">
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
                                Belum ada data jabatan terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($positions) && $positions instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $positions->hasPages())
            <div class="mt-4">
                {{ $positions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
