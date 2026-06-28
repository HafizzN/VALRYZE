@extends('layouts.app')

@section('title', 'Manajemen Karyawan')
@section('page-title', 'Karyawan')
@section('breadcrumb', 'Manajemen › Karyawan')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div style="display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:1rem;margin-bottom:0.25rem;">
        <div>
            <h2 style="font-size:1.1rem;font-weight:800;color:var(--t1);letter-spacing:-0.01em;">Daftar Karyawan</h2>
            <p style="font-size:0.78rem;color:var(--t4);margin-top:0.25rem;">Kelola profil, divisi, jabatan, shift, dan dokumen resmi karyawan</p>
        </div>
        <a href="{{ route('employees.create') }}" class="btn btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            Tambah Karyawan
        </a>
    </div>

    {{-- Filter --}}
    <div class="card">
        <form method="GET" action="{{ route('employees.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" for="search">Cari Karyawan</label>
                <input type="text" name="search" id="search" class="form-control" placeholder="NIK, nama, atau email..." value="{{ request('search') }}">
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" for="division_id">Divisi</label>
                <select name="division_id" id="division_id" class="form-control">
                    <option value="">Semua Divisi</option>
                    @foreach($divisions as $div)
                    <option value="{{ $div->id }}" {{ request('division_id') == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" for="status">Status</label>
                <select name="status" id="status" class="form-control">
                    <option value="">Semua Status</option>
                    <option value="active"   {{ request('status')=='active'   ? 'selected':'' }}>Aktif</option>
                    <option value="inactive" {{ request('status')=='inactive' ? 'selected':'' }}>Non-Aktif</option>
                    <option value="resign"   {{ request('status')=='resign'   ? 'selected':'' }}>Resign</option>
                </select>
            </div>
            <div style="display:flex;align-items:flex-end;gap:0.5rem;">
                <button type="submit" class="btn btn-primary flex-1">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Filter
                </button>
                @if(request()->anyFilled(['search','division_id','status']))
                <a href="{{ route('employees.index') }}" class="btn btn-secondary">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="card">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Karyawan</th>
                        <th>Divisi · Jabatan</th>
                        <th>Tipe Kontrak</th>
                        <th style="text-align:center;">Status</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $emp)
                    <tr>
                        {{-- Employee name + avatar --}}
                        <td>
                            <div style="display:flex;align-items:center;gap:0.75rem;">
                                <div class="avatar" style="width:36px;height:36px;font-size:0.7rem;overflow:hidden;flex-shrink:0;">
                                    @if($emp->photo)
                                        <img src="{{ Storage::url($emp->photo) }}" style="width:100%;height:100%;object-fit:cover;">
                                    @else {{ $emp->initials }} @endif
                                </div>
                                <div>
                                    <div style="font-size:0.85rem;font-weight:800;color:var(--t1);">{{ $emp->name }}</div>
                                    <div style="font-size:0.65rem;font-family:'JetBrains Mono',monospace;color:var(--em);">{{ $emp->nik }}</div>
                                    <div style="font-size:0.63rem;color:var(--t4);">{{ $emp->email }}</div>
                                </div>
                            </div>
                        </td>
                        {{-- Division + Position --}}
                        <td>
                            <div style="font-size:0.8rem;font-weight:600;color:var(--t2);">{{ $emp->division->name ?? '—' }}</div>
                            <div style="font-size:0.7rem;color:var(--t4);">{{ $emp->position->name ?? '—' }}</div>
                        </td>
                        {{-- Contract type --}}
                        <td>
                            @switch($emp->employment_type)
                                @case('permanent')  <span class="badge badge-success">Tetap</span> @break
                                @case('contract')   <span class="badge badge-orange">Kontrak</span> @break
                                @case('internship') <span class="badge badge-purple">Magang</span> @break
                                @case('freelance')  <span class="badge badge-gray">Freelance</span> @break
                                @default            <span class="badge badge-gray">{{ $emp->employment_type ?? '—' }}</span>
                            @endswitch
                        </td>
                        {{-- Status --}}
                        <td style="text-align:center;">
                            @switch($emp->status)
                                @case('active')   <span class="badge badge-success">Aktif</span> @break
                                @case('inactive') <span class="badge badge-danger">Non-Aktif</span> @break
                                @case('resign')   <span class="badge badge-gray">Resign</span> @break
                                @default          <span class="badge badge-gray">{{ $emp->status }}</span>
                            @endswitch
                        </td>
                        {{-- Actions --}}
                        <td>
                            <div style="display:flex;justify-content:flex-end;gap:0.4rem;flex-wrap:wrap;">
                                <a href="{{ route('employees.show', $emp->id) }}" class="btn btn-secondary btn-sm">Detail</a>
                                <a href="{{ route('employees.edit', $emp->id) }}" class="btn btn-secondary btn-sm" style="color:var(--em);">Edit</a>
                                <form action="{{ route('employees.destroy', $emp->id) }}" method="POST"
                                      onsubmit="return confirm('Nonaktifkan karyawan ini?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center;padding:3.5rem;color:var(--t4);">
                            <div style="font-size:2rem;margin-bottom:0.75rem;">👥</div>
                            <div style="font-weight:700;color:var(--t3);">Tidak ada karyawan ditemukan</div>
                            <div style="font-size:0.75rem;margin-top:0.25rem;">Coba ubah filter pencarian</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($employees->hasPages())
        <div style="margin-top:1.5rem;">{{ $employees->links() }}</div>
        @endif
    </div>
</div>
@endsection
