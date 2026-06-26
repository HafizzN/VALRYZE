@extends('layouts.app')

@section('title', 'Edit Divisi — ' . $division->name)
@section('page-title', 'Edit Divisi')
@section('breadcrumb', 'Master Data / Divisi / Edit')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Edit Data Divisi</h2>
            <p class="text-xs text-slate-500">Perbarui rincian informasi atau status aktif divisi kerja</p>
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
        <form method="POST" action="{{ route('master.divisions.update', $division->id) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div class="form-group">
                <label class="form-label" for="name">Nama Divisi <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" class="form-control" placeholder="Nama Divisi" value="{{ old('name', $division->name) }}" required>
                @error('name')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Code -->
                <div class="form-group">
                    <label class="form-label" for="code">Kode Divisi</label>
                    <input type="text" name="code" id="code" class="form-control" placeholder="Kode Divisi" value="{{ old('code', $division->code) }}">
                    @error('code')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Status -->
                <div class="form-group">
                    <label class="form-label" for="is_active">Status Aktif</label>
                    <select name="is_active" id="is_active" class="form-control" required>
                        <option value="1" {{ old('is_active', $division->is_active) == 1 ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('is_active', $division->is_active) == 0 ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                    @error('is_active')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div class="form-group">
                <label class="form-label" for="description">Deskripsi</label>
                <textarea name="description" id="description" rows="4" class="form-control" placeholder="Deskripsi Divisi...">{{ old('description', $division->description) }}</textarea>
                @error('description')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
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
