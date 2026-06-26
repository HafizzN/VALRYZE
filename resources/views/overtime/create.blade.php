@extends('layouts.app')

@section('title', 'Ajukan Lembur')
@section('page-title', 'Ajukan Lembur')
@section('breadcrumb', 'Perizinan › Lembur › Baru')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid rgba(255,255,255,0.06);">
            <div style="width: 40px; height: 40px; background: rgba(139,92,246,0.15); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                <svg style="width: 20px; height: 20px; color: #a78bfa;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <h2 style="font-size: 1rem; font-weight: 600;">Formulir Pengajuan Lembur</h2>
                <p style="font-size: 0.75rem; color: #64748b;">Isi data lembur dengan lengkap</p>
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

        <form method="POST" action="{{ route('overtime.store') }}" x-data="overtimeForm()">
            @csrf

            <div class="form-group">
                <label class="form-label">Tanggal Lembur <span style="color: #f87171;">*</span></label>
                <input type="date" name="date" class="form-control" value="{{ old('date', now()->toDateString()) }}" required>
                @error('date')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Jam Mulai <span style="color: #f87171;">*</span></label>
                    <input type="time" name="start_time" class="form-control" x-model="startTime" @change="calcDuration()" value="{{ old('start_time') }}" required>
                    @error('start_time')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Jam Selesai (Perkiraan) <span style="color: #f87171;">*</span></label>
                    <input type="time" name="end_time" class="form-control" x-model="endTime" @change="calcDuration()" value="{{ old('end_time') }}" required>
                    @error('end_time')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div x-show="duration > 0" x-transition style="margin-bottom: 1.25rem;">
                <div style="background: rgba(139,92,246,0.08); border: 1px solid rgba(139,92,246,0.2); border-radius: 8px; padding: 0.75rem; font-size: 0.82rem; text-align: center;">
                    Estimasi durasi lembur: <strong style="color: #a78bfa;" x-text="duration + ' jam'"></strong>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Tugas / Pekerjaan yang Dilakukan <span style="color: #f87171;">*</span></label>
                <textarea name="reason" class="form-control" rows="4" placeholder="Jelaskan pekerjaan yang akan dilakukan saat lembur..." required>{{ old('reason') }}</textarea>
                @error('reason')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Kompensasi Yang Diinginkan</label>
                <select name="compensation_type" class="form-control">
                    <option value="money" {{ old('compensation_type') === 'money' ? 'selected' : '' }}>Uang Lembur</option>
                    <option value="time_off" {{ old('compensation_type') === 'time_off' ? 'selected' : '' }}>Waktu Istirahat (Time-off)</option>
                </select>
            </div>

            <div style="display: flex; gap: 0.75rem; justify-content: flex-end; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.06);">
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
        endTime: '{{ old('end_time', '') }}',
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
