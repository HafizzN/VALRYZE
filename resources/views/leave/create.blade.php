@extends('layouts.app')

@section('title', 'Ajukan Cuti')
@section('page-title', 'Ajukan Cuti')
@section('breadcrumb', 'Perizinan › Cuti › Baru')

@section('content')
<div class="max-w-2xl mx-auto">

    {{-- Progress Steps --}}
    <div style="display:flex;align-items:center;gap:0;margin-bottom:1.5rem;">
        <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:0.3rem;">
            <div style="width:32px;height:32px;border-radius:50%;background:var(--em);color:#fff;display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:800;box-shadow:0 0 14px var(--em-glow);">1</div>
            <span style="font-size:0.62rem;font-weight:700;color:var(--em);text-align:center;">Jenis &amp; Tanggal</span>
        </div>
        <div style="flex:1;height:2px;background:linear-gradient(90deg,var(--em),var(--border-soft));margin-bottom:1rem;"></div>
        <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:0.3rem;">
            <div style="width:32px;height:32px;border-radius:50%;background:var(--bg-elevated);border:2px solid var(--border-soft);color:var(--t4);display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:800;">2</div>
            <span style="font-size:0.62rem;font-weight:600;color:var(--t4);text-align:center;">Alasan</span>
        </div>
        <div style="flex:1;height:2px;background:var(--border-soft);margin-bottom:1rem;"></div>
        <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:0.3rem;">
            <div style="width:32px;height:32px;border-radius:50%;background:var(--bg-elevated);border:2px solid var(--border-soft);color:var(--t4);display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:800;">3</div>
            <span style="font-size:0.62rem;font-weight:600;color:var(--t4);text-align:center;">Dokumen &amp; Kirim</span>
        </div>
    </div>

    <div class="card">
        <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1.5rem;padding-bottom:1rem;border-bottom:1px solid var(--border-dim);">
            <div style="width:42px;height:42px;background:rgba(167,139,250,0.12);border:1px solid rgba(167,139,250,0.25);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                <svg style="width:21px;height:21px;color:#A78BFA;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <h2 style="font-size:1rem;font-weight:700;color:var(--t1);">Formulir Pengajuan Cuti</h2>
                <p style="font-size:0.74rem;color:var(--t4);margin-top:0.1rem;">Isi semua data dengan lengkap dan benar</p>
            </div>
        </div>

        @if($errors->any())
        <div class="alert alert-error" style="margin-bottom: 1.5rem;">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                <div style="font-weight: 600;">Terdapat kesalahan:</div>
                <ul style="margin-top: 0.25rem; padding-left: 1rem;">
                    @foreach($errors->all() as $error)
                    <li style="font-size: 0.78rem;">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        <form method="POST" action="{{ route('leave.store') }}" enctype="multipart/form-data"
              x-data="leaveForm()" x-init="init()">
            @csrf

            {{-- Leave Type --}}
            <div class="form-group">
                <label class="form-label">Jenis Cuti <span style="color: #f87171;">*</span></label>
                <select name="leave_type" class="form-control" x-model="leaveType" @change="onTypeChange()" required>
                    <option value="">-- Pilih Jenis Cuti --</option>
                    <option value="annual">Cuti Tahunan</option>
                    <option value="sick">Cuti Sakit</option>
                    <option value="maternity">Cuti Melahirkan / Hamil</option>
                    <option value="wedding">Cuti Pernikahan</option>
                    <option value="big_leave">Cuti Besar</option>
                </select>
                @error('leave_type')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Annual leave quota info --}}
            <div x-show="leaveType === 'annual'" x-transition style="margin-bottom: 1.25rem;">
                <div style="background: rgba(99,102,241,0.08); border: 1px solid rgba(99,102,241,0.2); border-radius: 10px; padding: 1rem; display: flex; align-items: center; gap: 0.75rem;">
                    <svg style="width: 20px; height: 20px; color: #a78bfa; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>
                        <div style="font-size: 0.8rem; font-weight: 600; color: #a78bfa;">Sisa Kuota Cuti Tahunan</div>
                        <div style="font-size: 0.78rem; color: #94a3b8; margin-top: 0.2rem;">
                            Sisa: <strong style="color: #e2e8f0;">{{ $leaveBalance ?? 0 }} hari</strong>
                            dari total {{ $totalBalance ?? 12 }} hari
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sick leave note --}}
            <div x-show="leaveType === 'sick'" x-transition style="margin-bottom: 1.25rem;">
                <div class="alert alert-info">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Sertakan surat keterangan sakit dari dokter untuk cuti sakit lebih dari 2 hari.
                </div>
            </div>

            {{-- Maternity note --}}
            <div x-show="leaveType === 'maternity'" x-transition style="margin-bottom: 1.25rem;">
                <div class="alert alert-info">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Cuti melahirkan diberikan maksimal 90 hari. Lampirkan surat keterangan dokter.
                </div>
            </div>

            {{-- Wedding note --}}
            <div x-show="leaveType === 'wedding'" x-transition style="margin-bottom: 1.25rem;">
                <div class="alert alert-info">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Cuti pernikahan diberikan maksimal 3 hari. Lampirkan surat undangan atau akta nikah.
                </div>
            </div>

            {{-- Dates --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Tanggal Mulai <span style="color: #f87171;">*</span></label>
                    <input type="date" name="start_date" class="form-control" x-model="startDate" @change="calcDuration()" value="{{ old('start_date') }}" required min="{{ now()->toDateString() }}">
                    @error('start_date')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Selesai <span style="color: #f87171;">*</span></label>
                    <input type="date" name="end_date" class="form-control" x-model="endDate" @change="calcDuration()" value="{{ old('end_date') }}" required :min="startDate">
                    @error('end_date')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Duration display --}}
            <div x-show="duration > 0" x-transition style="margin-bottom: 1.25rem;">
                <div style="background: rgba(16,185,129,0.08); border: 1px solid rgba(16,185,129,0.2); border-radius: 8px; padding: 0.75rem; font-size: 0.82rem; text-align: center;">
                    Durasi cuti: <strong style="color: #34d399;" x-text="duration + ' hari kerja'"></strong>
                </div>
            </div>

            {{-- Reason --}}
            <div class="form-group">
                <label class="form-label">Alasan Cuti <span style="color: #f87171;">*</span></label>
                <textarea name="reason" class="form-control" rows="3" placeholder="Jelaskan alasan pengajuan cuti..." required>{{ old('reason') }}</textarea>
                @error('reason')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Emergency contact --}}
            <div class="form-group">
                <label class="form-label">Kontak Darurat Selama Cuti</label>
                <input type="text" name="emergency_contact" class="form-control" value="{{ old('emergency_contact') }}" placeholder="No. HP yang bisa dihubungi">
            </div>

            {{-- Attachment --}}
            <div class="form-group">
                <label class="form-label">Lampiran Dokumen</label>
                <div id="drop-zone"
                     style="border:2px dashed var(--border-soft);border-radius:14px;padding:2rem 1.5rem;text-align:center;cursor:pointer;transition:all 0.25s ease;background:var(--bg-elevated);"
                     onmouseover="this.style.borderColor='var(--em)';this.style.background='var(--em-ghost)';this.style.boxShadow='0 0 20px var(--em-glow)';"
                     onmouseout="this.style.borderColor='var(--border-soft)';this.style.background='var(--bg-elevated)';this.style.boxShadow='none';"
                     ondragover="event.preventDefault();this.style.borderColor='var(--em)';this.style.background='var(--em-ghost)';"
                     ondragleave="this.style.borderColor='var(--border-soft)';this.style.background='var(--bg-elevated)';"
                     ondrop="handleDrop(event)">
                    <input type="file" name="attachment" id="attachment" accept=".pdf,.jpg,.jpeg,.png" style="display:none;" @change="previewFile">
                    <label for="attachment" style="cursor:pointer;display:block;">
                        <div x-show="!fileName">
                            <svg style="width:36px;height:36px;color:var(--t5);margin:0 auto 0.6rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <div style="font-size:0.82rem;font-weight:600;color:var(--t3);">Klik untuk upload atau drag &amp; drop</div>
                            <div style="font-size:0.7rem;color:var(--t4);margin-top:0.3rem;">PDF, JPG, PNG · Maksimal 2MB</div>
                        </div>
                        <div x-show="fileName" style="display:flex;flex-direction:column;align-items:center;gap:0.5rem;">
                            <svg style="width:28px;height:28px;color:var(--em);" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div style="font-size:0.82rem;font-weight:700;color:var(--em);" x-text="fileName"></div>
                            <div style="font-size:0.68rem;color:var(--t4);">Klik untuk ganti file</div>
                        </div>
                    </label>
                </div>
                @error('attachment')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div style="display: flex; gap: 0.75rem; justify-content: flex-end; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.06);">
                <a href="{{ route('leave.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary" :disabled="!leaveType">
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
function leaveForm() {
    return {
        leaveType: '{{ old('leave_type', '') }}',
        startDate: '{{ old('start_date', '') }}',
        endDate: '{{ old('end_date', '') }}',
        duration: 0,
        fileName: '',

        init() { this.calcDuration(); },

        onTypeChange() { this.calcDuration(); },

        calcDuration() {
            if (this.startDate && this.endDate) {
                const s = new Date(this.startDate), e = new Date(this.endDate);
                if (e >= s) {
                    let d = 0, cur = new Date(s);
                    while (cur <= e) {
                        const day = cur.getDay();
                        if (day !== 0 && day !== 6) d++;
                        cur.setDate(cur.getDate() + 1);
                    }
                    this.duration = d;
                }
            }
        },

        previewFile(e) {
            const file = e.target.files[0];
            if (file) this.fileName = file.name;
        }
    }
}

function handleDrop(e) {
    e.preventDefault();
    const dz = document.getElementById('drop-zone');
    dz.style.borderColor = 'var(--border-soft)';
    dz.style.background   = 'var(--bg-elevated)';
    const file = e.dataTransfer.files[0];
    if (!file) return;
    const input = document.getElementById('attachment');
    const dt = new DataTransfer();
    dt.items.add(file);
    input.files = dt.files;
    // Update Alpine component
    const comp = Alpine.$data(document.querySelector('[x-data]'));
    if (comp) comp.fileName = file.name;
}
</script>
@endpush
