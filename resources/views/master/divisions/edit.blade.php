@extends('layouts.app')

@section('title', 'Edit Divisi — ' . $division->name)
@section('page-title', 'Edit Divisi')
@section('breadcrumb', 'Master Data › Divisi › Edit')

@section('content')
<div class="max-w-xl mx-auto space-y-5 animate-fadeSlideIn">
    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.25rem;">
        <div>
            <h2 style="font-size:1.1rem;font-weight:800;color:var(--t1);letter-spacing:-0.01em;">Edit Data Divisi</h2>
            <p style="font-size:0.78rem;color:var(--t4);margin-top:0.25rem;">Perbarui rincian informasi atau status aktif divisi kerja</p>
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
        <form method="POST" action="{{ route('master.divisions.update', $division->id) }}" style="display:flex;flex-direction:column;gap:1.25rem;">
            @csrf
            @method('PUT')

            {{-- Nama Divisi --}}
            <div class="form-group">
                <label class="form-label" for="name">Nama Divisi <span style="color:var(--danger);">*</span></label>
                <input type="text" name="name" id="name" class="form-control"
                    placeholder="Nama Divisi"
                    value="{{ old('name', $division->name) }}" required>
                @error('name')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Kode Divisi --}}
                <div class="form-group">
                    <label class="form-label" for="code">Kode Divisi</label>
                    <input type="text" name="code" id="code" class="form-control"
                        style="font-family:'JetBrains Mono',monospace;letter-spacing:0.06em;"
                        placeholder="HRD, IT, FIN..."
                        value="{{ old('code', $division->code) }}" maxlength="10">
                    @error('code')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                {{-- Status Aktif --}}
                <div class="form-group">
                    <label class="form-label" for="is_active">Status Aktif</label>
                    <select name="is_active" id="is_active" class="form-control" required>
                        <option value="1" {{ old('is_active', $division->is_active) == 1 ? 'selected' : '' }}>✅ Aktif</option>
                        <option value="0" {{ old('is_active', $division->is_active) == 0 ? 'selected' : '' }}>⛔ Non-Aktif</option>
                    </select>
                    @error('is_active')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Deskripsi --}}
            <div class="form-group">
                <label class="form-label" for="description">Deskripsi</label>
                <textarea name="description" id="description" rows="4" class="form-control"
                    placeholder="Deskripsi Divisi...">{{ old('description', $division->description) }}</textarea>
                @error('description')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div style="display:flex;align-items:center;justify-content:flex-end;gap:0.75rem;padding-top:1rem;border-top:1px solid var(--border-dim);">
                <a href="{{ route('master.divisions.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
