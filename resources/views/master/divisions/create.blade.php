@extends('layouts.app')

@section('title', 'Tambah Divisi Baru')
@section('page-title', 'Tambah Divisi')
@section('breadcrumb', 'Master Data / Divisi / Tambah')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Tambah Divisi Baru</h2>
            <p class="text-xs text-slate-500">Definisikan unit atau departemen kerja baru dalam struktur organisasi</p>
        </div>
        <a href="{{ route('master.divisions.index') }}" class="btn btn-secondary btn-sm">
            Kembali
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-error">
            <div class="flex-1">
                <p class="font-semibold text-sm">Terjadi kesalahan input data:</p>
                <ul class="list-disc list-inside text-xs mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="card">
        <form method="POST" action="{{ route('master.divisions.store') }}" class="space-y-4">
            @csrf

            <!-- Name -->
            <div class="form-group">
                <label class="form-label" for="name">Nama Divisi <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" class="form-control" placeholder="Contoh: Human Resource Development, Information Technology" value="{{ old('name') }}" required>
                @error('name')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <!-- Code -->
            <div class="form-group">
                <label class="form-label" for="code">Kode Divisi (Maksimal 10 Karakter)</label>
                <input type="text" name="code" id="code" class="form-control" placeholder="Contoh: HRD, IT, FIN, MKT" value="{{ old('code') }}">
                <p class="text-[10px] text-slate-500 mt-1">Kode identifikasi unik divisi (Opsional)</p>
                @error('code')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <!-- Description -->
            <div class="form-group">
                <label class="form-label" for="description">Deskripsi</label>
                <textarea name="description" id="description" rows="4" class="form-control" placeholder="Tuliskan deskripsi singkat mengenai tanggung jawab atau cakupan kerja divisi ini...">{{ old('description') }}</textarea>
                @error('description')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('master.divisions.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Simpan Divisi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
