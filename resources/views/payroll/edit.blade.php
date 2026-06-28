@extends('layouts.app')

@section('title', 'Ubah Gaji Karyawan')
@section('page-title', 'Ubah Gaji')
@section('breadcrumb', 'Payroll › Pengaturan Gaji › Edit')

@push('styles')
<style>
    .salary-field {
        background: var(--bg-elevated); border: 1px solid var(--border-soft);
        border-radius: 12px; padding: 0.9rem 1rem; margin-bottom: 0.85rem;
        transition: border-color 0.2s;
    }
    .salary-field:focus-within { border-color: var(--em-border); }
    .salary-field label {
        display: block; font-size: 0.62rem; font-weight: 800;
        letter-spacing: 0.1em; text-transform: uppercase;
        color: var(--t5); margin-bottom: 0.5rem;
    }
    .rp-input-wrap { position: relative; }
    .rp-prefix {
        position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%);
        font-size: 0.78rem; font-weight: 800; color: var(--t4);
        pointer-events: none;
    }
    .rp-input {
        width: 100%; padding: 0.55rem 0.75rem 0.55rem 2.1rem;
        background: var(--bg-base); border: 1px solid var(--border-soft);
        border-radius: 10px; color: var(--t1);
        font-family: 'JetBrains Mono', monospace; font-size: 0.88rem; font-weight: 700;
        outline: none; transition: border-color 0.2s;
    }
    .rp-input:focus { border-color: var(--em); box-shadow: 0 0 0 3px var(--em-glow); }
    .rp-input::placeholder { color: var(--t5); font-weight: 400; font-family: inherit; }
    .calc-row {
        display: flex; justify-content: space-between; align-items: center;
        padding: 0.45rem 0; border-bottom: 1px solid var(--border-dim);
        font-size: 0.8rem;
    }
    .calc-row:last-child { border-bottom: none; }
</style>
@endpush

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card">
        {{-- Header --}}
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;padding-bottom:1rem;border-bottom:1px solid var(--border-dim);">
            <div style="display:flex;align-items:center;gap:0.75rem;">
                <div style="width:46px;height:46px;background:rgba(16,185,129,0.1);border:1px solid var(--em-border);border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;flex-shrink:0;">💰</div>
                <div>
                    <h3 style="font-size:1rem;font-weight:800;color:var(--t1);">Atur Komponen Gaji</h3>
                    <p style="font-size:0.72rem;color:var(--t4);margin-top:0.1rem;">Konfigurasi manual gaji pokok & potongan</p>
                </div>
            </div>
            <a href="{{ route('payroll.index') }}" style="display:inline-flex;align-items:center;gap:0.4rem;font-size:0.78rem;font-weight:600;color:var(--t3);text-decoration:none;"
               onmouseover="this.style.color='var(--em)'" onmouseout="this.style.color='var(--t3)'">
                <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Kembali
            </a>
        </div>

        {{-- Employee card --}}
        <div style="display:flex;align-items:center;gap:0.85rem;padding:0.9rem 1rem;background:var(--bg-elevated);border:1px solid var(--border-soft);border-radius:12px;margin-bottom:1.5rem;">
            <div class="avatar" style="width:44px;height:44px;font-size:0.8rem;overflow:hidden;flex-shrink:0;">
                @if($user->photo)
                    <img src="{{ $user->photo_url }}" style="width:100%;height:100%;object-fit:cover;">
                @else {{ $user->initials }} @endif
            </div>
            <div>
                <div style="font-size:0.92rem;font-weight:800;color:var(--t1);">{{ $user->name }}</div>
                <div style="font-size:0.72rem;color:var(--t3);">{{ $user->position->name ?? '—' }} · {{ $user->division->name ?? '—' }}</div>
                <div style="font-size:0.65rem;font-family:'JetBrains Mono',monospace;color:var(--t4);">NIK: {{ $user->nik ?? 'TIDAK TERTAUT' }}</div>
            </div>
        </div>

        {{-- Hint --}}
        <div style="background:rgba(99,102,241,0.07);border:1px solid rgba(99,102,241,0.18);border-radius:12px;padding:0.75rem 1rem;margin-bottom:1.25rem;font-size:0.75rem;color:#A5B4FC;line-height:1.6;">
            💡 <strong>Petunjuk:</strong> Kosongkan kolom di bawah untuk mengaktifkan kalkulasi <em>otomatis default</em> berdasarkan tingkatan jabatan karyawan.
        </div>

        <form action="{{ route('payroll.update', $user->id) }}" method="POST">
            @csrf @method('PUT')

            {{-- Gaji Pokok --}}
            <div class="salary-field">
                <label>💵 Gaji Pokok (Rupiah)</label>
                <div class="rp-input-wrap">
                    <span class="rp-prefix">Rp</span>
                    <input type="number" name="basic_salary" id="basic_salary"
                           value="{{ old('basic_salary', $user->basic_salary ? (int)$user->basic_salary : '') }}"
                           class="rp-input" placeholder="Contoh: 8500000">
                </div>
                @error('basic_salary')<div class="form-error" style="margin-top:0.4rem;">{{ $message }}</div>@enderror
            </div>

            {{-- Tunjangan --}}
            <div class="salary-field">
                <label>🏷 Tunjangan Jabatan / Operasional (Rupiah)</label>
                <div class="rp-input-wrap">
                    <span class="rp-prefix">Rp</span>
                    <input type="number" name="allowance" id="allowance"
                           value="{{ old('allowance', $user->allowance ? (int)$user->allowance : '') }}"
                           class="rp-input" placeholder="Contoh: 1200000">
                </div>
                @error('allowance')<div class="form-error" style="margin-top:0.4rem;">{{ $message }}</div>@enderror
            </div>

            {{-- BPJS + Pajak --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div class="salary-field" style="margin-bottom:0;">
                    <label>🏥 Potongan BPJS (Rupiah)</label>
                    <div class="rp-input-wrap">
                        <span class="rp-prefix">Rp</span>
                        <input type="number" name="bpjs_deduction" id="bpjs_deduction"
                               value="{{ old('bpjs_deduction', $user->bpjs_deduction ? (int)$user->bpjs_deduction : '') }}"
                               class="rp-input" placeholder="Contoh: 150000">
                    </div>
                    @error('bpjs_deduction')<div class="form-error" style="margin-top:0.4rem;">{{ $message }}</div>@enderror
                </div>
                <div class="salary-field" style="margin-bottom:0;">
                    <label>📋 Potongan Pajak PPh21 (Rupiah)</label>
                    <div class="rp-input-wrap">
                        <span class="rp-prefix">Rp</span>
                        <input type="number" name="tax_deduction" id="tax_deduction"
                               value="{{ old('tax_deduction', $user->tax_deduction ? (int)$user->tax_deduction : '') }}"
                               class="rp-input" placeholder="Contoh: 250000">
                    </div>
                    @error('tax_deduction')<div class="form-error" style="margin-top:0.4rem;">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Live calculator --}}
            <div style="margin-top:1.25rem;background:var(--bg-elevated);border:1px solid var(--border-soft);border-radius:14px;padding:1.1rem 1.25rem;">
                <div style="font-size:0.6rem;font-weight:800;letter-spacing:0.12em;text-transform:uppercase;color:var(--em);margin-bottom:0.85rem;">📊 Kalkulator Gaji Real-time</div>
                <div class="calc-row"><span style="color:var(--t4);">Gaji Pokok (+)</span><span style="font-family:'JetBrains Mono',monospace;font-weight:700;color:var(--t2);" id="calc-basic">Rp 0</span></div>
                <div class="calc-row"><span style="color:var(--t4);">Tunjangan (+)</span><span style="font-family:'JetBrains Mono',monospace;font-weight:700;color:var(--t2);" id="calc-allowance">Rp 0</span></div>
                <div class="calc-row"><span style="color:var(--t4);">Total Potongan (−)</span><span style="font-family:'JetBrains Mono',monospace;font-weight:700;color:#FCA5A5;" id="calc-deductions">Rp 0</span></div>
                <div style="margin-top:0.75rem;padding-top:0.75rem;border-top:1px solid var(--border-soft);display:flex;justify-content:space-between;align-items:center;">
                    <span style="font-size:0.85rem;font-weight:700;color:var(--t1);">Estimasi THP Bersih</span>
                    <span style="font-family:'JetBrains Mono',monospace;font-size:1.2rem;font-weight:900;color:var(--em);" id="calc-net">Rp 0</span>
                </div>
            </div>

            {{-- Buttons --}}
            <div style="display:flex;justify-content:flex-end;gap:0.75rem;margin-top:1.25rem;padding-top:1rem;border-top:1px solid var(--border-dim);">
                <a href="{{ route('payroll.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Parameter Gaji
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fmt = n => new Intl.NumberFormat('id-ID', { style:'currency', currency:'IDR', minimumFractionDigits:0 }).format(n);
    const defaultBasic = 6500000;
    function calculate() {
        let basic = parseFloat(document.getElementById('basic_salary').value) || 0;
        const isDefault = basic === 0;
        if (isDefault) basic = defaultBasic;
        const allowance   = parseFloat(document.getElementById('allowance').value)      || (isDefault ? basic * 0.15 : 0);
        const bpjs        = parseFloat(document.getElementById('bpjs_deduction').value) || (isDefault ? basic * 0.03 : 0);
        const tax         = parseFloat(document.getElementById('tax_deduction').value)  || (isDefault ? basic * 0.05 : 0);
        const deductions  = bpjs + tax;
        const net         = basic + allowance - deductions;
        document.getElementById('calc-basic').textContent      = fmt(basic)      + (isDefault ? ' (Default)' : '');
        document.getElementById('calc-allowance').textContent  = fmt(allowance)  + (isDefault ? ' (15%)' : '');
        document.getElementById('calc-deductions').textContent = fmt(deductions) + (isDefault ? ' (8%)' : '');
        document.getElementById('calc-net').textContent        = fmt(net);
    }
    ['basic_salary','allowance','bpjs_deduction','tax_deduction'].forEach(id => {
        document.getElementById(id).addEventListener('input', calculate);
    });
    calculate();
});
</script>
@endsection
