@extends('layouts.app')

@section('title', 'Dokumen Perusahaan')
@section('page-title', 'Dokumen Perusahaan')
@section('breadcrumb', 'Dokumen / Company Documents')

@section('content')
<div class="space-y-6">
    <!-- Action Header & Filters -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Repositori Dokumen Perusahaan</h2>
            <p class="text-xs text-slate-500">Akses, unduh, dan kelola dokumen resmi atau kebijakan internal perusahaan</p>
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

    <!-- Filter Card -->
    <div class="card">
        <form method="GET" action="{{ route('documents.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="form-group mb-0">
                <label class="form-label" for="search">Cari Dokumen</label>
                <input type="text" name="search" id="search" class="form-control" placeholder="Cari nama dokumen..." value="{{ request('search') }}">
            </div>
            <div class="form-group mb-0">
                <label class="form-label" for="category">Kategori Dokumen</label>
                <select name="category" id="category" class="form-control">
                    <option value="">Semua Kategori</option>
                    <option value="sop" {{ request('category') == 'sop' ? 'selected' : '' }}>SOP (Standard Operating Procedure)</option>
                    <option value="regulation" {{ request('category') == 'regulation' ? 'selected' : '' }}>Peraturan Perusahaan</option>
                    <option value="sk" {{ request('category') == 'sk' ? 'selected' : '' }}>Surat Keputusan (SK)</option>
                    <option value="contract" {{ request('category') == 'contract' ? 'selected' : '' }}>Template Kontrak & Legal</option>
                    <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>Dokumen Lainnya</option>
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
                    <a href="{{ route('documents.index') }}" class="btn btn-secondary">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Documents Table -->
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
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documents as $document)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="p-2 rounded bg-slate-100 border border-slate-200 text-slate-700">
                                        @if(Str::contains($document->mime_type, 'pdf'))
                                            <svg class="w-6 h-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        @elseif(Str::contains($document->mime_type, ['word', 'officedocument.wordprocessingml']))
                                            <svg class="w-6 h-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        @elseif(Str::contains($document->mime_type, ['sheet', 'excel', 'officedocument.spreadsheetml']))
                                            <svg class="w-6 h-6 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @else
                                            <svg class="w-6 h-6 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h1.5"/></svg>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-semibold text-slate-800">{{ $document->title }}</div>
                                        <div class="text-[10px] text-slate-500 max-w-sm truncate">{{ $document->description ?? 'Tidak ada deskripsi' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @switch($document->category)
                                    @case('sop')
                                        <span class="badge badge-purple">SOP</span>
                                        @break
                                    @case('regulation')
                                        <span class="badge badge-danger">Peraturan</span>
                                        @break
                                    @case('sk')
                                        <span class="badge badge-success">SK Direksi</span>
                                        @break
                                    @case('contract')
                                        <span class="badge badge-info">Template</span>
                                        @break
                                    @default
                                        <span class="badge badge-gray">Lainnya</span>
                                @endswitch
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs">{{ $document->uploadedBy->name ?? 'Admin' }}</span>
                                </div>
                            </td>
                            <td>{{ $document->created_at->format('d M Y') }}</td>
                            <td class="font-mono text-xs text-slate-600">
                                @if($document->file_size >= 1048576)
                                    {{ number_format($document->file_size / 1048576, 2) }} MB
                                @else
                                    {{ number_format($document->file_size / 1024, 0) }} KB
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('documents.download', $document->id) }}" class="btn btn-primary btn-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    Unduh ({{ $document->download_count ?? 0 }})
                                </a>
                            </td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    @if(auth()->user()->hasRole(['super_admin', 'hrd']))
                                        <form action="{{ route('documents.destroy', $document->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus dokumen ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm text-xs">
                                                Hapus
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-slate-600 italic">No Action</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-slate-500">
                                Belum ada dokumen yang diunggah.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($documents->hasPages())
            <div class="mt-4">
                {{ $documents->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
