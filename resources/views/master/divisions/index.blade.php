@extends('layouts.app')

@section('title', 'Master Data Divisi')
@section('page-title', 'Divisi')
@section('breadcrumb', 'Master Data / Divisi')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Manajemen Divisi Kerja</h2>
            <p class="text-xs text-slate-500">Kelola unit kerja, departemen, dan pembagian divisi operasional perusahaan</p>
        </div>
        <div>
            <a href="{{ route('master.divisions.create') }}" class="btn btn-primary">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Divisi
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
                        <th>Kode Divisi</th>
                        <th>Nama Divisi</th>
                        <th>Deskripsi</th>
                        <th>Jumlah Karyawan</th>
                        <th>Status</th>
                        <th class="text-right" style="width: 200px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($divisions as $division)
                        <tr>
                            <td class="font-mono text-xs text-slate-600">#{{ $division->id }}</td>
                            <td class="font-semibold text-emerald-700 font-mono">{{ $division->code ?? '-' }}</td>
                            <td class="font-bold text-slate-800">{{ $division->name }}</td>
                            <td class="text-xs text-slate-600 max-w-xs truncate">{{ $division->description ?? '-' }}</td>
                            <td>
                                <div class="flex items-center gap-1.5">
                                    <span class="badge badge-purple font-mono">{{ $division->users_count ?? 0 }} Karyawan</span>
                                </div>
                            </td>
                            <td>
                                @if($division->is_active ?? true)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-gray">Non-Aktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('master.divisions.edit', $division->id) }}" class="btn btn-secondary btn-sm text-emerald-700">
                                        Edit
                                    </a>
                                    <form action="{{ route('master.divisions.destroy', $division->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus divisi ini?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm text-xs" {{ ($division->users_count ?? 0) > 0 ? 'disabled style=opacity:0.4;cursor:not-allowed title=Tidak-dapat-dihapus-karena-ada-karyawan' : '' }}>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-slate-500">
                                Belum ada data divisi terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($divisions) && $divisions instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $divisions->hasPages())
            <div class="mt-4">
                {{ $divisions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
