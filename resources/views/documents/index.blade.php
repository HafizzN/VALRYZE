@extends('layouts.app')

@section('title', 'Dokumen Perusahaan')
@section('page-title', 'Dokumen Perusahaan')
@section('breadcrumb', 'Dokumen › Company Documents')

@section('content')
<div class="space-y-5 animate-fadeSlideIn">
    {{-- Header Actions --}}
    <div style="display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:1rem;margin-bottom:0.25rem;">
        <div>
            <h2 style="font-size:1.1rem;font-weight:800;color:var(--t1);letter-spacing:-0.01em;">Repositori Dokumen Perusahaan</h2>
            <p style="font-size:0.78rem;color:var(--t4);margin-top:0.25rem;">Akses, unduh, dan kelola dokumen resmi atau kebijakan internal perusahaan</p>
        </div>
        <div>
            @if(auth()->user()->hasRole(['super_admin', 'hrd']))
                <a href="{{ route('documents.create') }}" class="btn btn-primary">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Unggah Dokumen
                </a>
            @endif
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="card">
        <form method="GET" action="{{ route('documents.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" for="search">Cari Dokumen</label>
                <input type="text" name="search" id="search" class="form-control" placeholder="Cari nama dokumen..." value="{{ request('search') }}">
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" for="category">Kategori Dokumen</label>
                <select name="category" id="category" class="form-control">
                    <option value="">Semua Kategori</option>
                    <option value="sop"        {{ request('category') == 'sop' ? 'selected' : '' }}>📝 SOP (Standard Operating Procedure)</option>
                    <option value="regulation" {{ request('category') == 'regulation' ? 'selected' : '' }}>📕 Peraturan Perusahaan</option>
                    <option value="sk"         {{ request('category') == 'sk' ? 'selected' : '' }}>💼 Surat Keputusan (SK)</option>
                    <option value="contract"   {{ request('category') == 'contract' ? 'selected' : '' }}>📎 Template Kontrak & Legal</option>
                    <option value="other"      {{ request('category') == 'other' ? 'selected' : '' }}>📁 Dokumen Lainnya</option>
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
                    <a href="{{ route('documents.index') }}" class="btn btn-secondary">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Documents Table --}}
    <div class="card">
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Nama Dokumen</th>
                        <th>Kategori</th>
                        <th>Diunggah Oleh</th>
                        <th>Tgl Unggah</th>
                        <th>Ukuran</th>
                        <th>Unduh</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documents as $document)
                        <tr>
                            {{-- Name + icon --}}
                            <td>
                                <div style="display:flex;align-items:center;gap:0.75rem;">
                                    <div style="width:36px;height:36px;border-radius:10px;background:var(--bg-elevated);border:1px solid var(--border-soft);display:flex;align-items:center;justify-content:center;font-size:1.15rem;flex-shrink:0;">
                                        @if(Str::contains($document->mime_type, 'pdf'))
                                            📕
                                        @elseif(Str::contains($document->mime_type, ['word', 'officedocument.wordprocessingml']))
                                            📘
                                        @elseif(Str::contains($document->mime_type, ['sheet', 'excel', 'officedocument.spreadsheetml']))
                                            📗
                                        @else
                                            📁
                                        @endif
                                    </div>
                                    <div>
                                        <div style="font-weight:850;color:var(--t1);font-size:0.82rem;">{{ $document->title }}</div>
                                        <div style="font-size:0.67rem;color:var(--t4);max-width:280px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $document->description ?? 'Tidak ada deskripsi' }}</div>
                                    </div>
                                </div>
                            </td>
                            {{-- Category --}}
                            <td>
                                @switch($document->category)
                                    @case('sop')        <span class="badge badge-purple">SOP</span> @break
                                    @case('regulation') <span class="badge badge-danger">Peraturan</span> @break
                                    @case('sk')         <span class="badge badge-success">SK Direksi</span> @break
                                    @case('contract')   <span class="badge badge-info">Template</span> @break
                                    @default            <span class="badge badge-gray">Lainnya</span>
                                @endswitch
                            </td>
                            {{-- Uploaded By --}}
                            <td>
                                <span style="font-size:0.8rem;font-weight:600;color:var(--t2);">{{ $document->uploadedBy->name ?? 'Admin' }}</span>
                            </td>
                            {{-- Upload date --}}
                            <td style="font-size:0.75rem;color:var(--t4);">{{ $document->created_at->translatedFormat('d M Y') }}</td>
                            {{-- File size --}}
                            <td style="font-family:'JetBrains Mono',monospace;font-size:0.75rem;color:var(--t3);">
                                @if($document->file_size >= 1048576)
                                    {{ number_format($document->file_size / 1048576, 2) }} MB
                                @else
                                    {{ number_format($document->file_size / 1024, 0) }} KB
                                @endif
                            </td>
                            {{-- Download button --}}
                            <td>
                                <a href="{{ route('documents.download', $document->id) }}" class="btn btn-primary btn-xs">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    Unduh ({{ $document->download_count ?? 0 }})
                                </a>
                            </td>
                            {{-- Actions --}}
                            <td>
                                <div style="display:flex;justify-content:flex-end;gap:0.4rem;">
                                    @if(auth()->user()->hasRole(['super_admin', 'hrd']))
                                        <form action="{{ route('documents.destroy', $document->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus dokumen ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm text-xs">Hapus</button>
                                        </form>
                                    @else
                                        <span style="font-size:0.72rem;color:var(--t5);font-style:italic;">No Action</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center;padding:3.5rem;color:var(--t4);">
                                <div style="font-size:2rem;margin-bottom:0.75rem;">📂</div>
                                <div style="font-weight:700;color:var(--t3);">Belum ada dokumen yang diunggah</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($documents->hasPages())
            <div style="margin-top:1.5rem;">
                {{ $documents->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
