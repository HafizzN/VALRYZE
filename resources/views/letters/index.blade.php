@extends('layouts.app')

@section('title', 'Surat Menyurat')
@section('page-title', 'Surat Menyurat')
@section('breadcrumb', 'Dokumen › Surat Menyurat')

@section('content')
<div class="space-y-5 animate-fadeSlideIn">
    {{-- Header Actions --}}
    <div style="display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:1rem;margin-bottom:0.25rem;">
        <div>
            <h2 style="font-size:1.1rem;font-weight:800;color:var(--t1);letter-spacing:-0.01em;">Daftar Pengajuan Surat</h2>
            <p style="font-size:0.78rem;color:var(--t4);margin-top:0.25rem;">Kelola dan lihat status pengajuan surat resmi perusahaan</p>
        </div>
        <a href="{{ route('letters.create') }}" class="btn btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Buat Surat
        </a>
    </div>

    {{-- Filter Card --}}
    <div class="card">
        <form method="GET" action="{{ route('letters.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" for="search">Cari Surat</label>
                <input type="text" name="search" id="search" class="form-control" placeholder="Cari Nomer Surat atau Judul..." value="{{ request('search') }}">
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" for="category">Kategori Surat</label>
                <select name="category" id="category" class="form-control">
                    <option value="">Semua Kategori</option>
                    <option value="permission"       {{ request('category') == 'permission' ? 'selected' : '' }}>📝 Izin Kehadiran</option>
                    <option value="leave"            {{ request('category') == 'leave' ? 'selected' : '' }}>🏖 Cuti</option>
                    <option value="assignment"       {{ request('category') == 'assignment' ? 'selected' : '' }}>💼 Surat Tugas (SK)</option>
                    <option value="field_duty"       {{ request('category') == 'field_duty' ? 'selected' : '' }}>🗺 Dinas Luar</option>
                    <option value="work_certificate" {{ request('category') == 'work_certificate' ? 'selected' : '' }}>📄 Keterangan Kerja</option>
                    <option value="other"            {{ request('category') == 'other' ? 'selected' : '' }}>📎 Lainnya</option>
                </select>
            </div>
            <div style="display:flex;align-items:flex-end;gap:0.5rem;">
                <button type="submit" class="btn btn-primary flex-1">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Filter
                </button>
                @if(request()->anyFilled(['search', 'category']))
                    <a href="{{ route('letters.index') }}" class="btn btn-secondary">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Letters Table --}}
    <div class="card">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px">No</th>
                        <th>Nomer Surat</th>
                        <th>Judul / Subjek</th>
                        <th>Kategori</th>
                        <th>Diajukan Oleh</th>
                        <th>Tgl Dibuat</th>
                        <th style="text-align:center;">Status</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($letters as $letter)
                        <tr>
                            <td style="color:var(--t4);font-family:'JetBrains Mono',monospace;font-size:0.75rem;">
                                {{ ($letters->currentPage() - 1) * $letters->perPage() + $loop->iteration }}
                            </td>
                            <td>
                                <span style="font-family:'JetBrains Mono',monospace;font-size:0.78rem;font-weight:700;color:var(--em);">
                                    {{ $letter->letter_number }}
                                </span>
                            </td>
                            <td>
                                <div style="font-weight:700;color:var(--t1);font-size:0.82rem;">{{ $letter->subject }}</div>
                            </td>
                            <td>
                                @switch($letter->letter_type)
                                    @case('permission')       <span class="badge badge-purple">Izin Kehadiran</span> @break
                                    @case('leave')            <span class="badge badge-orange">Cuti</span> @break
                                    @case('assignment')       <span class="badge badge-success">Surat Tugas</span> @break
                                    @case('field_duty')       <span class="badge badge-info">Dinas Luar</span> @break
                                    @case('work_certificate') <span class="badge badge-success">Keterangan Kerja</span> @break
                                    @default                  <span class="badge badge-gray">Lainnya</span>
                                @endswitch
                            </td>
                            <td>
                                <div style="display:flex;align-items:center;gap:0.5rem;">
                                    <div class="avatar" style="width:24px;height:24px;font-size:0.55rem;overflow:hidden;flex-shrink:0;">
                                        @if($letter->user?->photo)
                                            <img src="{{ $letter->user->photo_url }}" style="width:100%;height:100%;object-fit:cover;">
                                        @else
                                            {{ $letter->user->initials ?? 'K' }}
                                        @endif
                                    </div>
                                    <span style="font-size:0.8rem;color:var(--t2);font-weight:600;">{{ $letter->user->name ?? 'System' }}</span>
                                </div>
                            </td>
                            <td style="font-size:0.75rem;color:var(--t4);">{{ $letter->created_at->translatedFormat('d M Y') }}</td>
                            <td style="text-align:center;">
                                @switch($letter->status)
                                    @case('approved') <span class="badge badge-success">Disetujui</span> @break
                                    @case('rejected') <span class="badge badge-danger">Ditolak</span> @break
                                    @default          <span class="badge badge-warning">Menunggu</span>
                                @endswitch
                            </td>
                            <td>
                                <div style="display:flex;justify-content:flex-end;gap:0.4rem;">
                                    <a href="{{ route('letters.show', $letter->id) }}" class="btn btn-secondary btn-sm">Detail</a>
                                    <a href="{{ route('letters.download', $letter->id) }}" class="btn btn-primary btn-sm" style="padding:0.35rem 0.65rem;">
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
                            <td colspan="8" style="text-align:center;padding:3.5rem;color:var(--t4);">
                                <div style="font-size:2rem;margin-bottom:0.75rem;">📩</div>
                                <div style="font-weight:700;color:var(--t3);">Tidak ada data surat ditemukan</div>
                                <div style="font-size:0.75rem;margin-top:0.25rem;">Coba ubah filter pencarian</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($letters->hasPages())
            <div style="margin-top:1.5rem;">
                {{ $letters->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
