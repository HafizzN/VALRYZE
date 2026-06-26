@extends('layouts.app')

@section('title', 'Surat Menyurat')
@section('page-title', 'Surat Menyurat')
@section('breadcrumb', 'Dokumen / Surat Menyurat')

@section('content')
<div class="space-y-6">
    <!-- Action Header & Filters -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Daftar Pengajuan Surat</h2>
            <p class="text-xs text-slate-500">Kelola dan lihat status pengajuan surat resmi perusahaan</p>
        </div>
        <div>
            <a href="{{ route('letters.create') }}" class="btn btn-primary">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Buat Surat
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card">
        <form method="GET" action="{{ route('letters.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="form-group mb-0">
                <label class="form-label" for="search">Cari Surat</label>
                <input type="text" name="search" id="search" class="form-control" placeholder="Cari Nomer Surat atau Judul..." value="{{ request('search') }}">
            </div>
            <div class="form-group mb-0">
                <label class="form-label" for="category">Kategori Surat</label>
                <select name="category" id="category" class="form-control">
                    <option value="">Semua Kategori</option>
                    <option value="permission" {{ request('category') == 'permission' ? 'selected' : '' }}>Izin Kehadiran</option>
                    <option value="leave" {{ request('category') == 'leave' ? 'selected' : '' }}>Cuti</option>
                    <option value="assignment" {{ request('category') == 'assignment' ? 'selected' : '' }}>Surat Tugas (SK)</option>
                    <option value="field_duty" {{ request('category') == 'field_duty' ? 'selected' : '' }}>Dinas Luar</option>
                    <option value="work_certificate" {{ request('category') == 'work_certificate' ? 'selected' : '' }}>Keterangan Kerja</option>
                    <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="btn btn-primary flex-1">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Filter
                </button>
                @if(request()->anyFilled(['search', 'category']))
                    <a href="{{ route('letters.index') }}" class="btn btn-secondary">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Letters Table -->
    <div class="card">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 60px">No</th>
                        <th>Nomer Surat</th>
                        <th>Judul</th>
                        <th>Kategori</th>
                        <th>Diajukan Oleh</th>
                        <th>Tgl Dibuat</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($letters as $letter)
                        <tr>
                            <td>{{ ($letters->currentPage() - 1) * $letters->perPage() + $loop->iteration }}</td>
                            <td class="font-semibold text-emerald-700">{{ $letter->letter_number }}</td>
                            <td>{{ $letter->subject }}</td>
                            <td>
                                @switch($letter->letter_type)
                                    @case('permission')
                                        <span class="badge badge-purple">Izin Kehadiran</span>
                                        @break
                                    @case('leave')
                                        <span class="badge badge-orange">Cuti</span>
                                        @break
                                    @case('assignment')
                                        <span class="badge badge-success">Surat Tugas (SK)</span>
                                        @break
                                    @case('field_duty')
                                        <span class="badge badge-info">Dinas Luar</span>
                                        @break
                                    @case('work_certificate')
                                        <span class="badge badge-success">Keterangan Kerja</span>
                                        @break
                                    @default
                                        <span class="badge badge-gray">Lainnya</span>
                                @endswitch
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="avatar text-[10px] w-6 h-6">{{ $letter->user->initials ?? 'K' }}</div>
                                    <span>{{ $letter->user->name ?? 'System' }}</span>
                                </div>
                            </td>
                            <td>{{ $letter->created_at->format('d M Y') }}</td>
                            <td>
                                @switch($letter->status)
                                    @case('approved')
                                        <span class="badge badge-success">Disetujui</span>
                                        @break
                                    @case('rejected')
                                        <span class="badge badge-danger">Ditolak</span>
                                        @break
                                    @default
                                        <span class="badge badge-warning">Menunggu</span>
                                @endswitch
                            </td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('letters.show', $letter->id) }}" class="btn btn-secondary btn-sm">
                                        Detail
                                    </a>
                                    <a href="{{ route('letters.download', $letter->id) }}" class="btn btn-primary btn-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                        PDF
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-slate-500">
                                Tidak ada data surat yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($letters->hasPages())
            <div class="mt-4">
                {{ $letters->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
