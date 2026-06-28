@extends('layouts.app')

@section('title', 'Manajemen Akses User')
@section('page-title', 'Manajemen User')
@section('breadcrumb', 'Pengaturan › Manajemen User')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div style="display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:1rem;margin-bottom:0.25rem;">
        <div>
            <h2 style="font-size:1.1rem;font-weight:800;color:var(--t1);letter-spacing:-0.01em;">Hak Akses & Pengguna Sistem</h2>
            <p style="font-size:0.78rem;color:var(--t4);margin-top:0.25rem;">Kelola kredensial, sinkronisasi hak akses, dan tingkat kewenangan (Roles)</p>
        </div>
        <a href="{{ route('settings.users.create') }}" class="btn btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            Tambah Pengguna
        </a>
    </div>

    {{-- Table --}}
    <div class="card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
            <h3 style="font-size:0.9rem;font-weight:700;color:var(--t1);">Daftar Akun Pengguna</h3>
            <span style="font-size:0.72rem;color:var(--t4);">Hal. {{ $users->currentPage() }} / {{ $users->lastPage() }}</span>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Nama Pengguna</th>
                        <th>Email</th>
                        <th>NIK Asosiasi</th>
                        <th>Hak Akses</th>
                        <th>Dibuat</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $usr)
                    <tr>
                        {{-- Name + avatar --}}
                        <td>
                            <div style="display:flex;align-items:center;gap:0.65rem;">
                                <div class="avatar" style="width:32px;height:32px;font-size:0.65rem;overflow:hidden;flex-shrink:0;">
                                    @if($usr->photo)
                                        <img src="{{ $usr->photo_url }}" style="width:100%;height:100%;object-fit:cover;">
                                    @else {{ $usr->initials }} @endif
                                </div>
                                <div>
                                    <div style="font-size:0.82rem;font-weight:700;color:var(--t1);">{{ $usr->name }}</div>
                                    @if($usr->id === auth()->id())
                                    <div style="font-size:0.6rem;color:var(--em);font-weight:700;">← Akun Anda</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        {{-- Email --}}
                        <td style="font-size:0.78rem;color:var(--t3);">{{ $usr->email }}</td>
                        {{-- NIK --}}
                        <td>
                            <span style="font-family:'JetBrains Mono',monospace;font-size:0.75rem;font-weight:700;color:{{ $usr->nik ? 'var(--em)' : 'var(--t5)' }};">
                                {{ $usr->nik ?? 'TIDAK TERTAUT' }}
                            </span>
                        </td>
                        {{-- Roles --}}
                        <td>
                            @forelse($usr->roles as $role)
                                @switch($role->name)
                                    @case('super_admin') <span class="badge badge-danger">SUPER ADMIN</span> @break
                                    @case('hrd')         <span class="badge badge-success">HRD STAFF</span> @break
                                    @case('manager')     <span class="badge badge-purple">MANAGER</span> @break
                                    @case('employee')    <span class="badge badge-info">KARYAWAN</span> @break
                                    @default             <span class="badge badge-gray">{{ strtoupper($role->name) }}</span>
                                @endswitch
                            @empty
                                <span class="badge badge-gray" style="font-style:italic;opacity:0.7;">TIDAK ADA ROLE</span>
                            @endforelse
                        </td>
                        {{-- Date --}}
                        <td style="font-size:0.75rem;color:var(--t4);">{{ $usr->created_at->format('d M Y') }}</td>
                        {{-- Actions --}}
                        <td>
                            <div style="display:flex;justify-content:flex-end;gap:0.4rem;">
                                <a href="{{ route('settings.users.edit', $usr->id) }}" class="btn btn-secondary btn-sm" style="color:var(--em);">Edit</a>
                                @if($usr->id !== auth()->id())
                                <form action="{{ route('settings.users.destroy', $usr->id) }}" method="POST"
                                      onsubmit="return confirm('Hapus akun pengguna ini?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:3.5rem;color:var(--t4);">
                            <div style="font-size:2rem;margin-bottom:0.75rem;">🔐</div>
                            <div style="font-weight:700;color:var(--t3);">Tidak ada akun pengguna terdaftar</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div style="margin-top:1.5rem;">{{ $users->links() }}</div>
        @endif
    </div>
</div>
@endsection
