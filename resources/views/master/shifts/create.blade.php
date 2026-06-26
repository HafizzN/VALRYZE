@extends('layouts.app')

@section('title', 'Tambah Shift Kerja Baru')
@section('page-title', 'Tambah Shift Kerja')
@section('breadcrumb', 'Master Data / Shift / Tambah')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800" style="color: var(--text-main);">Tambah Shift Kerja</h2>
            <p class="text-xs text-slate-500">Definisikan jam operasional kerja, toleransi keterlambatan, dan warna penanda shift</p>
        </div>
        <a href="{{ route('master.shifts.index') }}" class="btn btn-secondary btn-sm">
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
        <form method="POST" action="{{ route('master.shifts.store') }}" class="space-y-4">
            @csrf

            <!-- Name & Color -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="form-group md:col-span-2">
                    <label class="form-label" for="name">Nama Shift <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="Contoh: Shift Pagi, Full Time, Shift Malam" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="color">Warna Penanda</label>
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <input type="color" name="color" id="color" class="form-control" style="width: 50px; height: 38px; padding: 2px; cursor: pointer;" value="{{ old('color', '#10b981') }}">
                        <span style="font-size: 0.75rem; color: var(--text-muted);">Pilih warna label</span>
                    </div>
                    @error('color')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Start Time & End Time -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label" for="start_time">Jam Masuk Kerja <span class="text-red-500">*</span></label>
                    <input type="time" name="start_time" id="start_time" class="form-control" value="{{ old('start_time', '08:00') }}" required>
                    @error('start_time')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="end_time">Jam Pulang Kerja <span class="text-red-500">*</span></label>
                    <input type="time" name="end_time" id="end_time" class="form-control" value="{{ old('end_time', '17:00') }}" required>
                    @error('end_time')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Toleransi & Batas Hari -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="form-group">
                    <label class="form-label" for="late_tolerance_minutes">Toleransi Keterlambatan (Menit)</label>
                    <input type="number" name="late_tolerance_minutes" id="late_tolerance_minutes" class="form-control" placeholder="Contoh: 10" min="0" max="120" value="{{ old('late_tolerance_minutes', 10) }}">
                    <p class="text-[9px] text-slate-500 mt-1">Batas keterlambatan masuk</p>
                    @error('late_tolerance_minutes')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="early_out_tolerance_minutes">Toleransi Pulang Cepat (Menit)</label>
                    <input type="number" name="early_out_tolerance_minutes" id="early_out_tolerance_minutes" class="form-control" placeholder="Contoh: 5" min="0" max="120" value="{{ old('early_out_tolerance_minutes', 0) }}">
                    <p class="text-[9px] text-slate-500 mt-1">Batas pulang cepat sebelum jam pulang</p>
                    @error('early_out_tolerance_minutes')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="is_overnight">Batas Hari (Overnight Shift)</label>
                    <select name="is_overnight" id="is_overnight" class="form-control">
                        <option value="0" {{ old('is_overnight') == 0 ? 'selected' : '' }}>Tidak (Selesai hari sama)</option>
                        <option value="1" {{ old('is_overnight') == 1 ? 'selected' : '' }}>Ya (Shift Malam - esok hari)</option>
                    </select>
                    <p class="text-[9px] text-slate-500 mt-1">Pilih 'Ya' jika melewati jam 12 malam</p>
                    @error('is_overnight')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200" style="border-top-color: var(--border-color);">
                <a href="{{ route('master.shifts.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Simpan Shift
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
