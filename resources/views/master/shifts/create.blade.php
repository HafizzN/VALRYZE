@extends('layouts.app')

@section('title', 'Tambah Shift Kerja Baru')
@section('page-title', 'Tambah Shift Kerja')
@section('breadcrumb', 'Master Data › Shift › Tambah')

@section('content')
<div class="max-w-2xl mx-auto space-y-5 animate-fadeSlideIn">
    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.25rem;">
        <div>
            <h2 style="font-size:1.1rem;font-weight:800;color:var(--t1);letter-spacing:-0.01em;">Tambah Shift Kerja</h2>
            <p style="font-size:0.78rem;color:var(--t4);margin-top:0.25rem;">Definisikan jam operasional kerja, toleransi keterlambatan, dan warna penanda shift</p>
        </div>
        <a href="{{ route('master.shifts.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
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
        <form method="POST" action="{{ route('master.shifts.store') }}" style="display:flex;flex-direction:column;gap:1.25rem;">
            @csrf

            {{-- Nama & Warna --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="form-group md:col-span-2">
                    <label class="form-label" for="name">Nama Shift <span style="color:var(--danger);">*</span></label>
                    <input type="text" name="name" id="name" class="form-control"
                        placeholder="Contoh: Shift Pagi, Full Time, Shift Malam"
                        value="{{ old('name') }}" required>
                    @error('name')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="color">Warna Penanda</label>
                    <div style="display:flex;gap:0.5rem;align-items:center;">
                        <input type="color" name="color" id="color" class="form-control"
                            style="width:46px;height:40px;padding:3px;cursor:pointer;border-radius:8px;"
                            value="{{ old('color', '#10b981') }}">
                        <span id="colorLabel" style="font-size:0.72rem;color:var(--t4);font-family:'JetBrains Mono',monospace;">#10b981</span>
                    </div>
                    @error('color')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Jam Masuk & Jam Pulang --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label" for="start_time">🕐 Jam Masuk Kerja <span style="color:var(--danger);">*</span></label>
                    <input type="time" name="start_time" id="start_time" class="form-control"
                        style="font-family:'JetBrains Mono',monospace;"
                        value="{{ old('start_time', '08:00') }}" required>
                    @error('start_time')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="end_time">🕔 Jam Pulang Kerja <span style="color:var(--danger);">*</span></label>
                    <input type="time" name="end_time" id="end_time" class="form-control"
                        style="font-family:'JetBrains Mono',monospace;"
                        value="{{ old('end_time', '17:00') }}" required>
                    @error('end_time')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Toleransi & Batas Hari --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="form-group">
                    <label class="form-label" for="late_tolerance_minutes">⏱ Toleransi Terlambat</label>
                    <div style="display:flex;align-items:center;gap:0.4rem;">
                        <input type="number" name="late_tolerance_minutes" id="late_tolerance_minutes" class="form-control"
                            style="font-family:'JetBrains Mono',monospace;"
                            placeholder="10" min="0" max="120"
                            value="{{ old('late_tolerance_minutes', 10) }}">
                        <span style="font-size:0.72rem;color:var(--t4);white-space:nowrap;">menit</span>
                    </div>
                    <p style="font-size:0.67rem;color:var(--t4);margin-top:0.25rem;">Batas keterlambatan masuk</p>
                    @error('late_tolerance_minutes')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="early_out_tolerance_minutes">⏰ Toleransi Pulang Cepat</label>
                    <div style="display:flex;align-items:center;gap:0.4rem;">
                        <input type="number" name="early_out_tolerance_minutes" id="early_out_tolerance_minutes" class="form-control"
                            style="font-family:'JetBrains Mono',monospace;"
                            placeholder="5" min="0" max="120"
                            value="{{ old('early_out_tolerance_minutes', 0) }}">
                        <span style="font-size:0.72rem;color:var(--t4);white-space:nowrap;">menit</span>
                    </div>
                    <p style="font-size:0.67rem;color:var(--t4);margin-top:0.25rem;">Batas pulang sebelum jam pulang</p>
                    @error('early_out_tolerance_minutes')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="is_overnight">🌙 Overnight Shift</label>
                    <select name="is_overnight" id="is_overnight" class="form-control">
                        <option value="0" {{ old('is_overnight') == 0 ? 'selected' : '' }}>Tidak (hari sama)</option>
                        <option value="1" {{ old('is_overnight') == 1 ? 'selected' : '' }}>Ya (melewati tengah malam)</option>
                    </select>
                    <p style="font-size:0.67rem;color:var(--t4);margin-top:0.25rem;">Pilih 'Ya' jika melewati jam 12 malam</p>
                    @error('is_overnight')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div style="display:flex;align-items:center;justify-content:flex-end;gap:0.75rem;padding-top:1rem;border-top:1px solid var(--border-dim);">
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

<script>
    const colorInput = document.getElementById('color');
    const colorLabel = document.getElementById('colorLabel');
    colorInput.addEventListener('input', () => { colorLabel.textContent = colorInput.value; });
</script>
@endsection
