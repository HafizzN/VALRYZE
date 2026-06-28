@extends('layouts.app')

@section('title', 'Ajukan Izin')
@section('page-title', 'Ajukan Izin')
@section('breadcrumb', 'Perizinan › Izin › Baru')

@section('content')
@php
    $typeMap = [
        'late_in'   => ['icon' => '🕐', 'label' => 'Izin Terlambat',    'desc' => 'Hadir lebih lambat dari jam kerja'],
        'early_out' => ['icon' => '🏃', 'label' => 'Pulang Lebih Awal', 'desc' => 'Meninggalkan kantor sebelum selesai'],
        'outside'   => ['icon' => '🗺', 'label' => 'Dinas Luar',        'desc' => 'Tugas di luar kantor / perjalanan dinas'],
        'other'     => ['icon' => '📝', 'label' => 'Lainnya',           'desc' => 'Alasan izin lainnya'],
    ];
@endphp

<div class="max-w-2xl mx-auto">
    <div class="card">
        {{-- Header --}}
        <div style="display:flex;align-items:center;gap:0.85rem;margin-bottom:1.5rem;padding-bottom:1rem;border-bottom:1px solid var(--border-dim);">
            <div style="width:46px;height:46px;background:rgba(99,102,241,0.12);border:1px solid rgba(99,102,241,0.22);border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;flex-shrink:0;">
                📝
            </div>
            <div>
                <h2 style="font-size:1rem;font-weight:800;color:var(--t1);">Formulir Pengajuan Izin</h2>
                <p style="font-size:0.73rem;color:var(--t4);margin-top:0.1rem;">Isi data izin dengan lengkap dan benar</p>
            </div>
        </div>

        {{-- Error alert --}}
        @if($errors->any())
        <div class="alert alert-error" style="margin-bottom:1.5rem;">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                <div style="font-weight:700;">Terdapat kesalahan:</div>
                <ul style="margin-top:0.25rem;padding-left:1rem;">
                    @foreach($errors->all() as $e)<li style="font-size:0.78rem;">{{ $e }}</li>@endforeach
                </ul>
            </div>
        </div>
        @endif

        <form method="POST" action="{{ route('permission.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- Jenis Izin as card tiles --}}
            <div class="form-group">
                <label class="form-label">Jenis Izin <span style="color:var(--danger);">*</span></label>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.65rem;margin-top:0.4rem;">
                    @foreach($typeMap as $val => $info)
                    <label style="display:flex;align-items:center;gap:0.75rem;padding:0.85rem 1rem;background:var(--bg-elevated);border:1px solid var(--border-soft);border-radius:12px;cursor:pointer;transition:all 0.2s;"
                           onmouseover="this.style.borderColor='rgba(99,102,241,0.4)';this.style.background='rgba(99,102,241,0.06)';"
                           onmouseout="this.style.borderColor='var(--border-soft)';this.style.background='var(--bg-elevated)';">
                        <input type="radio" name="type" value="{{ $val }}" {{ old('type') === $val ? 'checked' : '' }} required style="accent-color:#A78BFA;">
                        <div>
                            <div style="font-size:0.82rem;font-weight:700;color:var(--t1);">{{ $info['icon'] }} {{ $info['label'] }}</div>
                            <div style="font-size:0.65rem;color:var(--t4);margin-top:0.1rem;">{{ $info['desc'] }}</div>
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('type')<div class="form-error" style="margin-top:0.5rem;">{{ $message }}</div>@enderror
            </div>

            {{-- Date --}}
            <div class="form-group">
                <label class="form-label">Tanggal <span style="color:var(--danger);">*</span></label>
                <input type="date" name="date" class="form-control" value="{{ old('date', now()->toDateString()) }}" required>
                @error('date')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Time grid --}}
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

            {{-- Reason --}}
            <div class="form-group">
                <label class="form-label">Alasan <span style="color:var(--danger);">*</span></label>
                <textarea name="reason" class="form-control" rows="4" placeholder="Jelaskan alasan pengajuan izin..." required>{{ old('reason') }}</textarea>
                @error('reason')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Attachment --}}
            <div class="form-group">
                <label class="form-label">Lampiran (Opsional)</label>
                <div style="border:2px dashed var(--border-soft);border-radius:14px;padding:1.5rem;text-align:center;cursor:pointer;background:var(--bg-elevated);transition:all 0.25s ease;"
                     onmouseover="this.style.borderColor='var(--em)';this.style.background='var(--em-ghost)';"
                     onmouseout="this.style.borderColor='var(--border-soft)';this.style.background='var(--bg-elevated)';">
                    <label for="attachment_file" style="cursor:pointer;display:block;">
                        <svg style="width:28px;height:28px;color:var(--t5);margin:0 auto 0.5rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                        <div style="font-size:0.8rem;font-weight:600;color:var(--t3);">Klik untuk lampirkan file</div>
                        <div style="font-size:0.68rem;color:var(--t4);margin-top:0.25rem;">PDF, JPG, PNG · Maks 2 MB</div>
                    </label>
                    <input type="file" id="attachment_file" name="attachment" accept=".pdf,.jpg,.jpeg,.png" style="display:none;">
                </div>
                @error('attachment')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Buttons --}}
            <div style="display:flex;gap:0.75rem;justify-content:flex-end;padding-top:1rem;border-top:1px solid var(--border-dim);">
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
