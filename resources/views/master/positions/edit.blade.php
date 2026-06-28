@extends('layouts.app')

@section('title', 'Edit Jabatan')
@section('page-title', 'Edit Jabatan')
@section('breadcrumb', 'Master Data › Jabatan › Edit')

@section('content')
<div class="max-w-2xl mx-auto space-y-5 animate-fadeSlideIn">
    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.25rem;">
        <div>
            <h2 style="font-size:1.1rem;font-weight:800;color:var(--t1);letter-spacing:-0.01em;">Edit Jabatan</h2>
            <p style="font-size:0.78rem;color:var(--t4);margin-top:0.25rem;">Perbarui hierarki jabatan, leveling kompetensi, dan penempatan divisi karyawan</p>
        </div>
        <a href="{{ route('master.positions.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
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
        <form method="POST" action="{{ route('master.positions.update', $position->id) }}" style="display:flex;flex-direction:column;gap:1.25rem;">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Nama Jabatan --}}
                <div class="form-group">
                    <label class="form-label" for="name">Nama Jabatan <span style="color:var(--danger);">*</span></label>
                    <input type="text" name="name" id="name" class="form-control"
                        placeholder="Contoh: Senior Web Developer"
                        value="{{ old('name', $position->name) }}" required>
                    @error('name')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                {{-- Kode Jabatan --}}
                <div class="form-group">
                    <label class="form-label" for="code">Kode Jabatan <span style="font-size:0.68rem;font-weight:400;color:var(--t4);">(opsional)</span></label>
                    <input type="text" name="code" id="code" class="form-control"
                        style="font-family:'JetBrains Mono',monospace;letter-spacing:0.06em;"
                        placeholder="SR-DEV"
                        value="{{ old('code', $position->code) }}">
                    @error('code')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Divisi Penempatan --}}
                <div class="form-group">
                    <label class="form-label" for="division_id">Divisi Penempatan <span style="color:var(--danger);">*</span></label>
                    <select name="division_id" id="division_id" class="form-control" required>
                        <option value="">Pilih Divisi</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}" {{ old('division_id', $position->division_id) == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
                        @endforeach
                    </select>
                    @error('division_id')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                {{-- Level Hierarki --}}
                <div class="form-group">
                    <label class="form-label" for="level">Level Hierarki <span style="color:var(--danger);">*</span></label>
                    <select name="level" id="level" class="form-control" required>
                        <option value="">Pilih Level</option>
                        <option value="staff"      {{ old('level', $position->level) == 'staff' ? 'selected' : '' }}>👤 Staff</option>
                        <option value="supervisor" {{ old('level', $position->level) == 'supervisor' ? 'selected' : '' }}>👥 Supervisor</option>
                        <option value="manager"    {{ old('level', $position->level) == 'manager' ? 'selected' : '' }}>🏅 Manager</option>
                        <option value="director"   {{ old('level', $position->level) == 'director' ? 'selected' : '' }}>🎯 Director</option>
                    </select>
                    @error('level')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Status Jabatan --}}
            <div class="form-group">
                <label class="form-label" for="is_active">Status Jabatan</label>
                <select name="is_active" id="is_active" class="form-control">
                    <option value="1" {{ old('is_active', $position->is_active) ? 'selected' : '' }}>✅ Aktif</option>
                    <option value="0" {{ !old('is_active', $position->is_active) ? 'selected' : '' }}>⛔ Non-Aktif</option>
                </select>
                @error('is_active')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Deskripsi Tugas --}}
            <div class="form-group">
                <label class="form-label" for="description">Deskripsi Tugas / Jobdesk</label>
                <textarea name="description" id="description" rows="3" class="form-control"
                    placeholder="Tuliskan deskripsi ringkas tanggung jawab jabatan...">{{ old('description', $position->description) }}</textarea>
                @error('description')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div style="display:flex;align-items:center;justify-content:flex-end;gap:0.75rem;padding-top:1rem;border-top:1px solid var(--border-dim);">
                <a href="{{ route('master.positions.index') }}" class="btn btn-secondary">Batal</a>
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
