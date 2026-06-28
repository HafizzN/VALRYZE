@extends('layouts.app')

@section('title', 'Unggah Dokumen Baru')
@section('page-title', 'Unggah Dokumen')
@section('breadcrumb', 'Dokumen › Company Documents › Unggah')

@section('content')
<div class="max-w-2xl mx-auto space-y-5 animate-fadeSlideIn">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.25rem;">
        <div>
            <h2 style="font-size:1.1rem;font-weight:800;color:var(--t1);letter-spacing:-0.01em;">Unggah Dokumen Baru</h2>
            <p style="font-size:0.78rem;color:var(--t4);margin-top:0.25rem;">Unggah berkas resmi atau kebijakan untuk repositori perusahaan</p>
        </div>
        <a href="{{ route('documents.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
    </div>

    @if($errors->any())
        <div class="alert alert-error" style="margin-bottom:1.25rem;">
            <div class="flex-1">
                <p style="font-weight:700;">Terjadi kesalahan pada input data:</p>
                <ul style="margin-top:0.25rem;padding-left:1rem;">
                    @foreach($errors->all() as $error)
                        <li style="font-size:0.78rem;">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="card">
        <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data" style="display:flex;flex-direction:column;gap:1.25rem;">
            @csrf

            <!-- Title -->
            <div class="form-group">
                <label class="form-label" for="title">Nama / Judul Dokumen <span style="color:var(--danger);">*</span></label>
                <input type="text" name="title" id="title" class="form-control" placeholder="Contoh: SOP Pengajuan Lembur v2.1 / Kebijakan Cuti Melahirkan" value="{{ old('title') }}" required>
                @error('title')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Category -->
                <div class="form-group">
                    <label class="form-label" for="category">Kategori Dokumen <span style="color:var(--danger);">*</span></label>
                    <select name="category" id="category" class="form-control" required>
                        <option value="" disabled selected>Pilih Kategori</option>
                        <option value="sop"        {{ old('category') == 'sop' ? 'selected' : '' }}>📝 SOP (Standard Operating Procedure)</option>
                        <option value="regulation" {{ old('category') == 'regulation' ? 'selected' : '' }}>📕 Peraturan Perusahaan (Policy)</option>
                        <option value="sk"         {{ old('category') == 'sk' ? 'selected' : '' }}>💼 Surat Keputusan (SK / Memo)</option>
                        <option value="contract"   {{ old('category') == 'contract' ? 'selected' : '' }}>📎 Template Kontrak & Legal (Template)</option>
                        <option value="other"      {{ old('category') == 'other' ? 'selected' : '' }}>📁 Dokumen Lainnya</option>
                    </select>
                    @error('category')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <!-- Accessibility -->
                <div class="form-group">
                    <label class="form-label">Aksesibilitas</label>
                    <label style="display:flex;align-items:center;gap:0.75rem;padding:0.75rem 1rem;background:var(--bg-elevated);border:1px solid var(--border-soft);border-radius:12px;cursor:pointer;margin-top:0.15rem;transition:all 0.2s;"
                           onmouseover="this.style.borderColor='var(--em-border)'" onmouseout="this.style.borderColor='var(--border-soft)'">
                        <input type="checkbox" name="is_public" id="is_public" value="1" style="accent-color:var(--em);width:1rem;height:1rem;cursor:pointer;" {{ old('is_public', '1') == '1' ? 'checked' : '' }}>
                        <div>
                            <div style="font-size:0.8rem;font-weight:700;color:var(--t1);">🌐 Akses Publik</div>
                            <div style="font-size:0.65rem;color:var(--t4);margin-top:0.1rem;">Dapat diunduh oleh semua karyawan</div>
                        </div>
                    </label>
                    @error('is_public')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <!-- Description -->
            <div class="form-group">
                <label class="form-label" for="description">Deskripsi Singkat</label>
                <textarea name="description" id="description" rows="4" class="form-control" placeholder="Tuliskan deskripsi singkat mengenai kegunaan atau isi dari dokumen ini...">{{ old('description') }}</textarea>
                @error('description')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <!-- File Input -->
            <div class="form-group">
                <label class="form-label">Berkas Dokumen <span style="color:var(--danger);">*</span></label>
                <div style="border:2px dashed var(--border-soft);border-radius:14px;padding:1.75rem;text-align:center;background:var(--bg-elevated);transition:all 0.25s ease;cursor:pointer;"
                     onmouseover="this.style.borderColor='var(--em)';this.style.background='var(--em-ghost)';"
                     onmouseout="this.style.borderColor='var(--border-soft)';this.style.background='var(--bg-elevated)';">
                    <label for="file" style="cursor:pointer;display:block;">
                        <svg style="width:30px;height:30px;color:var(--t5);margin:0 auto 0.5rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        <div style="font-size:0.8rem;font-weight:600;color:var(--t3);">Klik untuk memilih berkas dokumen</div>
                        <div style="font-size:0.68rem;color:var(--t4);margin-top:0.25rem;">PDF, DOC, DOCX, XLSX, XLS · Maks 20 MB</div>
                    </label>
                    <input type="file" id="file" name="file" accept=".pdf,.doc,.docx,.xlsx,.xls" style="display:none;" required>
                </div>
                @error('file')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div style="display:flex;align-items:center;justify-content:flex-end;gap:0.75rem;padding-top:1rem;border-top:1px solid var(--border-dim);">
                <a href="{{ route('documents.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Mulai Unggah
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
