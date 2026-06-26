@extends('layouts.app')

@section('title', 'Edit Jabatan')
@section('page-title', 'Edit Jabatan')
@section('breadcrumb', 'Master Data / Jabatan / Edit')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800" style="color: var(--text-main);">Edit Jabatan</h2>
            <p class="text-xs text-slate-500">Perbarui hierarki jabatan, leveling kompetensi, dan penempatan divisi karyawan</p>
        </div>
        <a href="{{ route('master.positions.index') }}" class="btn btn-secondary btn-sm">
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
        <form method="POST" action="{{ route('master.positions.update', $position->id) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Name -->
                <div class="form-group">
                    <label class="form-label" for="name">Nama Jabatan <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="Contoh: Senior Web Developer" value="{{ old('name', $position->name) }}" required>
                    @error('name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Code -->
                <div class="form-group">
                    <label class="form-label" for="code">Kode Jabatan</label>
                    <input type="text" name="code" id="code" class="form-control" placeholder="Contoh: SR-DEV" value="{{ old('code', $position->code) }}">
                    @error('code')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Division -->
                <div class="form-group">
                    <label class="form-label" for="division_id">Divisi Penempatan <span class="text-red-500">*</span></label>
                    <select name="division_id" id="division_id" class="form-control" required>
                        <option value="">Pilih Divisi</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}" {{ old('division_id', $position->division_id) == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
                        @endforeach
                    </select>
                    @error('division_id')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Level -->
                <div class="form-group">
                    <label class="form-label" for="level">Level Hierarki <span class="text-red-500">*</span></label>
                    <select name="level" id="level" class="form-control" required>
                        <option value="">Pilih Level</option>
                        <option value="staff" {{ old('level', $position->level) == 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="supervisor" {{ old('level', $position->level) == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                        <option value="manager" {{ old('level', $position->level) == 'manager' ? 'selected' : '' }}>Manager</option>
                        <option value="director" {{ old('level', $position->level) == 'director' ? 'selected' : '' }}>Director</option>
                    </select>
                    @error('level')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Status Aktif -->
            <div class="form-group">
                <label class="form-label" for="is_active">Status Jabatan</label>
                <select name="is_active" id="is_active" class="form-control">
                    <option value="1" {{ old('is_active', $position->is_active) ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ !old('is_active', $position->is_active) ? 'selected' : '' }}>Non-Aktif</option>
                </select>
                @error('is_active')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <!-- Description -->
            <div class="form-group">
                <label class="form-label" for="description">Deskripsi Tugas / Jobdesk</label>
                <textarea name="description" id="description" rows="3" class="form-control" placeholder="Tuliskan deskripsi ringkas tanggung jawab jabatan...">{{ old('description', $position->description) }}</textarea>
                @error('description')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200" style="border-top-color: var(--border-color);">
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
