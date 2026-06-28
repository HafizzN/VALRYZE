@extends('layouts.app')

@section('title', 'Tambah Divisi Baru')
@section('page-title', 'Tambah Divisi')
@section('breadcrumb', 'Master Data › Divisi › Tambah')

@section('content')
<div class="max-w-xl mx-auto space-y-5 animate-fadeSlideIn">
    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.25rem;">
        <div>
            <h2 style="font-size:1.1rem;font-weight:800;color:var(--t1);letter-spacing:-0.01em;">Tambah Divisi Baru</h2>
            <p style="font-size:0.78rem;color:var(--t4);margin-top:0.25rem;">Definisikan unit atau departemen kerja baru dalam struktur organisasi</p>
        </div>
        <a href="{{ route('master.divisions.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
    </div>

    @if($errors->any())
        <div class="alert alert-error">
            <div class="flex-1">
                <p style="font-weight:700;font-size:0.8rem;">Terjadi kesalahan input data:</p>
                <ul style="padding-left:1rem;margin-top:0.25rem;">
                    @foreach($errors->all() as $error)
                        <li style="font-size:0.75rem;">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="card">
        <form method="POST" action="{{ route('master.divisions.store') }}" style="display:flex;flex-direction:column;gap:1.25rem;">
            @csrf

            {{-- Nama Divisi --}}
            <div class="form-group">
                <label class="form-label" for="name">Nama Divisi <span style="color:var(--danger);">*</span></label>
                <input type="text" name="name" id="name" class="form-control"
                    placeholder="Contoh: Human Resource Development, Information Technology"
                    value="{{ old('name') }}" required>
                @error('name')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Kode Divisi --}}
            <div class="form-group">
                <label class="form-label" for="code">Kode Divisi <span style="font-size:0.68rem;font-weight:400;color:var(--t4);">(Maks. 10 karakter)</span></label>
                <input type="text" name="code" id="code" class="form-control"
                    style="font-family:'JetBrains Mono',monospace;letter-spacing:0.06em;"
                    placeholder="Contoh: HRD, IT, FIN, MKT"
                    value="{{ old('code') }}" maxlength="10">
                <p style="font-size:0.68rem;color:var(--t4);margin-top:0.3rem;">Kode identifikasi unik divisi (opsional)</p>
                @error('code')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Deskripsi --}}
            <div class="form-group">
                <label class="form-label" for="description">Deskripsi</label>
                <textarea name="description" id="description" rows="4" class="form-control"
                    placeholder="Tuliskan deskripsi singkat mengenai tanggung jawab atau cakupan kerja divisi ini...">{{ old('description') }}</textarea>
                @error('description')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div style="display:flex;align-items:center;justify-content:flex-end;gap:0.75rem;padding-top:1rem;border-top:1px solid var(--border-dim);">
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
