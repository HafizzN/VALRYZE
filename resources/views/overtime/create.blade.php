@extends('layouts.app')

@section('title', 'Ajukan Lembur')
@section('page-title', 'Ajukan Lembur')
@section('breadcrumb', 'Perizinan › Lembur › Baru')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card">
        {{-- Header --}}
        <div style="display:flex;align-items:center;gap:0.85rem;margin-bottom:1.5rem;padding-bottom:1rem;border-bottom:1px solid var(--border-dim);">
            <div style="width:46px;height:46px;background:rgba(167,139,250,0.12);border:1px solid rgba(167,139,250,0.25);border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;flex-shrink:0;">
                ⏱
            </div>
            <div>
                <h2 style="font-size:1rem;font-weight:800;color:var(--t1);">Formulir Pengajuan Lembur</h2>
                <p style="font-size:0.73rem;color:var(--t4);margin-top:0.1rem;">Isi data lembur dengan lengkap dan benar</p>
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

        <form method="POST" action="{{ route('overtime.store') }}" x-data="overtimeForm()">
            @csrf

            {{-- Date --}}
            <div class="form-group">
                <label class="form-label">Tanggal Lembur <span style="color:var(--danger);">*</span></label>
                <input type="date" name="date" class="form-control" value="{{ old('date', now()->toDateString()) }}" required>
                @error('date')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Time grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Jam Mulai <span style="color:var(--danger);">*</span></label>
                    <input type="time" name="start_time" class="form-control" x-model="startTime" @change="calcDuration()" value="{{ old('start_time') }}" required>
                    @error('start_time')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Jam Selesai (Perkiraan) <span style="color:var(--danger);">*</span></label>
                    <input type="time" name="end_time" class="form-control" x-model="endTime" @change="calcDuration()" value="{{ old('end_time') }}" required>
                    @error('end_time')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Duration pill --}}
            <div x-show="duration > 0" x-transition style="margin-bottom:1.25rem;">
                <div style="background:rgba(167,139,250,0.08);border:1px solid rgba(167,139,250,0.22);border-radius:10px;padding:0.75rem 1rem;font-size:0.82rem;display:flex;align-items:center;justify-content:center;gap:0.5rem;">
                    <span style="font-size:1rem;">⏱</span>
                    Estimasi durasi lembur:
                    <strong style="color:#C4B5FD;font-family:'JetBrains Mono',monospace;" x-text="duration + ' jam'"></strong>
                </div>
            </div>

            {{-- Reason --}}
            <div class="form-group">
                <label class="form-label">Tugas / Pekerjaan yang Dilakukan <span style="color:var(--danger);">*</span></label>
                <textarea name="reason" class="form-control" rows="4" placeholder="Jelaskan pekerjaan yang akan dilakukan saat lembur..." required>{{ old('reason') }}</textarea>
                @error('reason')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Compensation type --}}
            <div class="form-group">
                <label class="form-label">Kompensasi yang Diinginkan</label>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;margin-top:0.35rem;">
                    <label style="display:flex;align-items:center;gap:0.75rem;padding:0.85rem 1rem;background:var(--bg-elevated);border:1px solid var(--border-soft);border-radius:12px;cursor:pointer;transition:all 0.2s;"
                           onmouseover="this.style.borderColor='var(--em-border)'" onmouseout="this.style.borderColor='var(--border-soft)'">
                        <input type="radio" name="compensation_type" value="money" {{ old('compensation_type','money') === 'money' ? 'checked' : '' }} style="accent-color:var(--em);">
                        <div>
                            <div style="font-size:0.82rem;font-weight:700;color:var(--t1);">💵 Uang Lembur</div>
                            <div style="font-size:0.68rem;color:var(--t4);">Dibayarkan lewat payroll</div>
                        </div>
                    </label>
                    <label style="display:flex;align-items:center;gap:0.75rem;padding:0.85rem 1rem;background:var(--bg-elevated);border:1px solid var(--border-soft);border-radius:12px;cursor:pointer;transition:all 0.2s;"
                           onmouseover="this.style.borderColor='var(--em-border)'" onmouseout="this.style.borderColor='var(--border-soft)'">
                        <input type="radio" name="compensation_type" value="time_off" {{ old('compensation_type') === 'time_off' ? 'checked' : '' }} style="accent-color:var(--em);">
                        <div>
                            <div style="font-size:0.82rem;font-weight:700;color:var(--t1);">⏰ Time-off</div>
                            <div style="font-size:0.68rem;color:var(--t4);">Waktu istirahat pengganti</div>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Buttons --}}
            <div style="display:flex;gap:0.75rem;justify-content:flex-end;padding-top:1rem;border-top:1px solid var(--border-dim);">
                <a href="{{ route('overtime.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    Kirim Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function overtimeForm() {
    return {
        startTime: '{{ old('start_time', '') }}',
        endTime:   '{{ old('end_time', '') }}',
        duration: 0,
        calcDuration() {
            if (this.startTime && this.endTime) {
                const [sh, sm] = this.startTime.split(':').map(Number);
                const [eh, em] = this.endTime.split(':').map(Number);
                let diff = (eh * 60 + em) - (sh * 60 + sm);
                if (diff < 0) diff += 24 * 60;
                this.duration = (diff / 60).toFixed(1);
            }
        }
    }
}
</script>
@endpush
