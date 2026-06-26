@extends('layouts.app')

@section('title', 'Ajukan Izin')
@section('page-title', 'Ajukan Izin')
@section('breadcrumb', 'Perizinan › Izin › Baru')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid rgba(255,255,255,0.06);">
            <div style="width: 40px; height: 40px; background: rgba(99,102,241,0.15); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                <svg style="width: 20px; height: 20px; color: #a78bfa;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <h2 style="font-size: 1rem; font-weight: 600;">Formulir Pengajuan Izin</h2>
                <p style="font-size: 0.75rem; color: #64748b;">Isi data izin dengan lengkap dan benar</p>
            </div>
        </div>

        @if($errors->any())
        <div class="alert alert-error" style="margin-bottom: 1.5rem;">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div><div style="font-weight: 600;">Terdapat kesalahan:</div>
                <ul style="margin-top: 0.25rem; padding-left: 1rem;">@foreach($errors->all() as $e)<li style="font-size: 0.78rem;">{{ $e }}</li>@endforeach</ul>
            </div>
        </div>
        @endif

        <form method="POST" action="{{ route('permission.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="form-label">Jenis Izin <span style="color: #f87171;">*</span></label>
                <select name="type" class="form-control" required>
                    <option value="">-- Pilih Jenis Izin --</option>
                    <option value="late_in" {{ old('type') === 'late_in' ? 'selected' : '' }}>Izin Terlambat</option>
                    <option value="early_out" {{ old('type') === 'early_out' ? 'selected' : '' }}>Pulang Lebih Awal</option>
                    <option value="outside" {{ old('type') === 'outside' ? 'selected' : '' }}>Dinas Luar</option>
                    <option value="other" {{ old('type') === 'other' ? 'selected' : '' }}>Lainnya</option>
                </select>
                @error('type')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Tanggal <span style="color: #f87171;">*</span></label>
                <input type="date" name="date" class="form-control" value="{{ old('date', now()->toDateString()) }}" required>
                @error('date')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Jam Mulai</label>
                    <input type="time" name="start_time" class="form-control" value="{{ old('start_time') }}">
                    @error('start_time')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Jam Selesai</label>
                    <input type="time" name="end_time" class="form-control" value="{{ old('end_time') }}">
                    @error('end_time')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Alasan <span style="color: #f87171;">*</span></label>
                <textarea name="reason" class="form-control" rows="4" placeholder="Jelaskan alasan pengajuan izin..." required>{{ old('reason') }}</textarea>
                @error('reason')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Lampiran (Opsional)</label>
                <input type="file" name="attachment" class="form-control" accept=".pdf,.jpg,.jpeg,.png" style="padding: 0.5rem;">
                @error('attachment')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div style="display: flex; gap: 0.75rem; justify-content: flex-end; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.06);">
                <a href="{{ route('permission.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    Kirim Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
