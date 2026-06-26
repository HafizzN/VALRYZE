@extends('layouts.app')

@section('title', 'Manajemen Akses User')
@section('page-title', 'Manajemen User')
@section('breadcrumb', 'Pengaturan / Manajemen User')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Hak Akses & Pengguna Sistem</h2>
            <p class="text-xs text-slate-500">Kelola kredensial akun pengguna portal, sinkronisasi hak akses, dan tingkat kewenangan (Roles & Permissions)</p>
        </div>
        <div>
            <a href="{{ route('settings.users.create') }}" class="btn btn-primary">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                Tambah Pengguna
            </a>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-slate-700">Daftar Akun Pengguna</h3>
            <span class="text-[10px] text-slate-500">Menampilkan halaman {{ $users->currentPage() }} dari {{ $users->lastPage() }}</span>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Nama Pengguna</th>
                        <th>Alamat Email</th>
                        <th>NIK Asosiasi</th>
                        <th>Tingkat Hak Akses (Role)</th>
                        <th>Dibuat Pada</th>
                        <th class="text-right" style="width: 200px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $usr)
                        <tr>
                            <!-- Name & Avatar -->
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="avatar text-xs w-8 h-8">{{ $usr->initials }}</div>
                                    <div class="font-bold text-slate-800">{{ $usr->name }}</div>
                                </div>
                            </td>
                            <!-- Email -->
                            <td>{{ $usr->email }}</td>
                            <!-- NIK -->
                            <td class="font-mono text-xs text-emerald-700 font-semibold">{{ $usr->nik ?? 'TIDAK TERTAUT' }}</td>
                            <!-- Roles Badges -->
                            <td>
                                @forelse($usr->roles as $role)
                                    @switch($role->name)
                                        @case('super_admin')
                                            <span class="badge badge-danger">SUPER ADMIN</span>
                                            @break
                                        @case('hrd')
                                            <span class="badge badge-success">HRD STAFF</span>
                                            @break
                                        @case('manager')
                                            <span class="badge badge-purple">MANAGER</span>
                                            @break
                                        @case('employee')
                                            <span class="badge badge-info">KARYAWAN</span>
                                            @break
                                        @default
                                            <span class="badge badge-gray">{{ strtoupper($role->name) }}</span>
                                    @endswitch
                                @empty
                                    <span class="badge badge-gray italic text-[10px]">TIDAK ADA ROLE</span>
                                @endforelse
                            </td>
                            <!-- Created Date -->
                            <td>{{ $usr->created_at->format('d M Y') }}</td>
                            <!-- Actions -->
                            <td>
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('settings.users.edit', $usr->id) }}" class="btn btn-secondary btn-sm text-emerald-700">
                                        Edit
                                    </a>
                                    
                                    @if($usr->id !== auth()->id())
                                        <form action="{{ route('settings.users.destroy', $usr->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun user ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm text-xs">
                                                Hapus
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-[10px] text-slate-600 italic">Akun Anda</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-slate-500">
                                Tidak ada akun pengguna yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
