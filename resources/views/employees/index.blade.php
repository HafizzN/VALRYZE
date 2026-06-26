@extends('layouts.app')

@section('title', 'Manajemen Karyawan')
@section('page-title', 'Karyawan')
@section('breadcrumb', 'Manajemen / Karyawan')

@section('content')
<div class="space-y-6">
    <!-- Action Header & Button -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Daftar Karyawan</h2>
            <p class="text-xs text-slate-500">Kelola informasi profil, divisi, jabatan, shift, dan dokumen resmi karyawan</p>
        </div>
        <div>
            <a href="{{ route('employees.create') }}" class="btn btn-primary">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                Tambah Karyawan
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card">
        <form method="GET" action="{{ route('employees.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div class="form-group mb-0">
                <label class="form-label" for="search">Cari Karyawan</label>
                <input type="text" name="search" id="search" class="form-control" placeholder="Cari NIK, nama, atau email..." value="{{ request('search') }}">
            </div>
            
            <!-- Division -->
            <div class="form-group mb-0">
                <label class="form-label" for="division_id">Divisi</label>
                <select name="division_id" id="division_id" class="form-control">
                    <option value="">Semua Divisi</option>
                    @foreach($divisions as $div)
                        <option value="{{ $div->id }}" {{ request('division_id') == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status -->
            <div class="form-group mb-0">
                <label class="form-label" for="status">Status</label>
                <select name="status" id="status" class="form-control">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
                    <option value="resign" {{ request('status') == 'resign' ? 'selected' : '' }}>Resign</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex items-end gap-2">
                <button type="submit" class="btn btn-primary flex-1">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Filter
                </button>
                @if(request()->anyFilled(['search', 'division_id', 'status']))
                    <a href="{{ route('employees.index') }}" class="btn btn-secondary">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Employees Table Card -->
    <div class="card">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>NIK</th>
                        <th>Karyawan</th>
                        <th>Divisi</th>
                        <th>Jabatan</th>
                        <th>Tipe Kontrak</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $employee)
                        <tr>
                            <!-- NIK -->
                            <td class="font-mono text-xs font-semibold text-emerald-700">
                                {{ $employee->nik }}
                            </td>
                            <!-- Karyawan Photo & Name -->
                            <td>
                                <div class="flex items-center gap-3">
                                    @if($employee->photo)
                                        <img src="{{ Storage::url($employee->photo) }}" alt="{{ $employee->name }}" class="w-9 h-9 rounded-full object-cover border border-slate-200">
                                    @else
                                        <div class="avatar text-xs w-9 h-9">{{ $employee->initials }}</div>
                                    @endif
                                    <div>
                                        <div class="font-bold text-slate-800">{{ $employee->name }}</div>
                                        <div class="text-[10px] text-slate-500">{{ $employee->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <!-- Division -->
                            <td>
                                {{ $employee->division->name ?? '-' }}
                            </td>
                            <!-- Position -->
                            <td>
                                {{ $employee->position->name ?? '-' }}
                            </td>
                            <!-- Contract Type -->
                            <td>
                                @switch($employee->employment_type)
                                    @case('permanent')
                                        <span class="badge badge-success">Karyawan Tetap</span>
                                        @break
                                    @case('contract')
                                        <span class="badge badge-orange">Kontrak</span>
                                        @break
                                    @case('internship')
                                        <span class="badge badge-purple">Magang</span>
                                        @break
                                    @case('freelance')
                                        <span class="badge badge-gray">Freelance</span>
                                        @break
                                    @default
                                        <span class="badge badge-gray">{{ $employee->employment_type ?? '-' }}</span>
                                @endswitch
                            </td>
                            <!-- Status -->
                            <td>
                                @switch($employee->status)
                                    @case('active')
                                        <span class="badge badge-success">Aktif</span>
                                        @break
                                    @case('inactive')
                                        <span class="badge badge-danger">Non-Aktif</span>
                                        @break
                                    @case('resign')
                                        <span class="badge badge-gray">Resign</span>
                                        @break
                                    @default
                                        <span class="badge badge-gray">{{ $employee->status }}</span>
                                @endswitch
                            </td>
                            <!-- Actions -->
                            <td>
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('employees.show', $employee->id) }}" class="btn btn-secondary btn-sm">
                                        Detail
                                    </a>
                                    <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-secondary btn-sm text-emerald-700">
                                        Edit
                                    </a>
                                    <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan karyawan ini?')" class="inline">
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
                            <td colspan="7" class="text-center py-10 text-slate-500">
                                Tidak ada data karyawan yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($employees->hasPages())
            <div class="mt-4">
                {{ $employees->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
