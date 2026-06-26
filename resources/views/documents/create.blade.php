@extends('layouts.app')

@section('title', 'Unggah Dokumen Baru')
@section('page-title', 'Unggah Dokumen')
@section('breadcrumb', 'Dokumen / Company Documents / Unggah')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Unggah Dokumen Baru</h2>
            <p class="text-xs text-slate-500">Unggah berkas resmi atau kebijakan untuk repositori perusahaan</p>
        </div>
        <a href="{{ route('documents.index') }}" class="btn btn-secondary btn-sm">
            Kembali
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-error">
            <div class="flex-1">
                <p class="font-semibold text-sm">Terjadi kesalahan pada input data:</p>
                <ul class="list-disc list-inside text-xs mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="card">
        <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <!-- Title -->
            <div class="form-group">
                <label class="form-label" for="title">Nama / Judul Dokumen <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" class="form-control" placeholder="Contoh: SOP Pengajuan Lembur v2.1 / Kebijakan Cuti Melahirkan" value="{{ old('title') }}" required>
                @error('title')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Category / Type -->
                <div class="form-group">
                    <label class="form-label" for="category">Kategori Dokumen <span class="text-red-500">*</span></label>
                    <select name="category" id="category" class="form-control" required>
                        <option value="" disabled selected>Pilih Kategori</option>
                        <option value="sop" {{ old('category') == 'sop' ? 'selected' : '' }}>SOP (Standard Operating Procedure)</option>
                        <option value="regulation" {{ old('category') == 'regulation' ? 'selected' : '' }}>Peraturan Perusahaan (Policy)</option>
                        <option value="sk" {{ old('category') == 'sk' ? 'selected' : '' }}>Surat Keputusan (SK / Memo)</option>
                        <option value="contract" {{ old('category') == 'contract' ? 'selected' : '' }}>Template Kontrak & Legal (Template)</option>
                        <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>Dokumen Lainnya</option>
                    </select>
                    @error('category')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Public Access -->
                <div class="form-group">
                    <label class="form-label" for="is_public">Aksesibilitas</label>
                    <div class="flex items-center mt-2">
                        <input type="checkbox" name="is_public" id="is_public" value="1" class="rounded border-slate-200 bg-slate-100 text-emerald-700 focus:ring-indigo-500 h-4 w-4" {{ old('is_public', '1') == '1' ? 'checked' : '' }}>
                        <span class="ml-2 text-xs text-slate-600">Publik (Dapat diunduh oleh semua karyawan)</span>
                    </div>
                    @error('is_public')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div class="form-group">
                <label class="form-label" for="description">Deskripsi Singkat</label>
                <textarea name="description" id="description" rows="4" class="form-control" placeholder="Tuliskan deskripsi singkat mengenai kegunaan atau isi dari dokumen ini...">{{ old('description') }}</textarea>
                @error('description')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <!-- File Input -->
            <div class="form-group">
                <label class="form-label" for="file">Berkas Dokumen <span class="text-red-500">*</span></label>
                <input type="file" name="file" id="file" class="form-control" accept=".pdf,.doc,.docx,.xlsx,.xls" required>
                <p class="text-[10px] text-slate-500 mt-1">Format yang diizinkan: PDF, DOC, DOCX, XLSX, XLS (Maksimal 20MB)</p>
                @error('file')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('documents.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Mulai Unggah
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
