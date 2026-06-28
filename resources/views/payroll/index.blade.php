@extends('layouts.app')

@section('title', 'Pengaturan Gaji Karyawan')
@section('page-title', 'Payroll & Gaji')
@section('breadcrumb', 'Payroll › Pengaturan Gaji')

@push('styles')
<style>
    .payroll-hero {
        position: relative; overflow: hidden;
        background: linear-gradient(135deg, #071830 0%, #0D2540 50%, #0A1E38 100%);
        border: 1px solid rgba(16,185,129,0.18);
        border-radius: 20px; padding: 1.5rem 2rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 8px 32px rgba(0,0,0,0.25);
    }
    .payroll-hero::before {
        content:''; position:absolute; top:-50px; right:-30px;
        width:200px; height:200px; border-radius:50%;
        background: radial-gradient(circle, rgba(16,185,129,0.1) 0%, transparent 70%);
        pointer-events:none;
    }

    .salary-row { transition: all 0.2s ease; }
    .salary-row:hover { background: var(--bg-hover) !important; }

    .salary-cell-base {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.8rem; font-weight: 700;
        color: var(--t1);
    }
    .salary-cell-deduct {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.8rem; font-weight: 700;
        color: #FCA5A5;
    }
    .salary-cell-net {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.82rem; font-weight: 800;
        color: #34D399;
    }

    .custom-tag {
        display: inline-block; font-size: 0.6rem; font-weight: 800;
        padding: 0.1rem 0.4rem; border-radius: 99px;
        background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.3);
        color: var(--em); letter-spacing: 0.04em;
    }
    .auto-tag {
        display: inline-block; font-size: 0.6rem;
        padding: 0.1rem 0.35rem; border-radius: 99px;
        background: rgba(148,163,184,0.1); border: 1px solid rgba(148,163,184,0.15);
        color: var(--t5); font-style: italic;
    }

    .summary-kpi {
        background: var(--bg-elevated);
        border: 1px solid var(--border-soft);
        border-radius: 12px; padding: 1rem 1.25rem;
        transition: all 0.2s ease;
    }
    .summary-kpi:hover { transform: translateY(-2px); border-color: var(--em-border); }
</style>
@endpush

@section('content')
@php
    // Aggregate totals across current page
    $totalPayroll = 0;
    $totalNetPayroll = 0;
    foreach ($users as $u) {
        $b = $u->basic_salary;
        if (is_null($b)) {
            $pos = strtolower($u->position->name ?? '');
            $b = 6500000;
            if (str_contains($pos, 'director') || str_contains($pos, 'direktur')) $b = 35000000;
            elseif (str_contains($pos, 'manager') || str_contains($pos, 'head')) $b = 22000000;
            elseif (str_contains($pos, 'supervisor') || str_contains($pos, 'lead')) $b = 15000000;
            elseif (str_contains($pos, 'senior')) $b = 11000000;
            elseif (str_contains($pos, 'staff') || str_contains($pos, 'officer')) $b = 8000000;
        }
        $all = $u->allowance ?? (int)($b * 0.15);
        $bpjs = $u->bpjs_deduction ?? (int)($b * 0.03);
        $tax  = $u->tax_deduction  ?? (int)($b * 0.05);
        $totalPayroll += $b;
        $totalNetPayroll += $b + $all - $bpjs - $tax;
    }
@endphp

{{-- Hero --}}
<div class="payroll-hero">
    <div class="relative z-10 flex flex-col md:flex-row md:items-end justify-between gap-5">
        <div>
            <p style="font-size:0.62rem;font-weight:700;letter-spacing:0.15em;text-transform:uppercase;color:rgba(16,185,129,0.7);margin-bottom:0.35rem;">
                💰 Manajemen Kompensasi
            </p>
            <h1 style="font-size:1.35rem;font-weight:800;color:#F1F5F9;letter-spacing:-0.02em;">Pengaturan Gaji Karyawan</h1>
            <p style="font-size:0.78rem;color:#64748B;margin-top:0.25rem;">Kelola gaji pokok, tunjangan, BPJS, dan PPh21 seluruh karyawan</p>
        </div>
        <div class="grid grid-cols-2 gap-3 shrink-0">
            <div class="summary-kpi" style="text-align:right;">
                <div style="font-size:0.62rem;color:var(--t4);font-weight:600;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:0.2rem;">Total Gaji Pokok</div>
                <div style="font-size:1rem;font-weight:800;color:var(--t1);font-family:'JetBrains Mono',monospace;">Rp {{ number_format($totalPayroll, 0, ',', '.') }}</div>
            </div>
            <div class="summary-kpi" style="text-align:right;">
                <div style="font-size:0.62rem;color:var(--t4);font-weight:600;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:0.2rem;">Total Take Home Pay</div>
                <div style="font-size:1rem;font-weight:800;color:#34D399;font-family:'JetBrains Mono',monospace;">Rp {{ number_format($totalNetPayroll, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Flash --}}
@if(session('success'))
<div class="alert alert-success mb-4">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    {{ session('success') }}
</div>
@endif

{{-- Table --}}
<div class="card">
    <div class="flex items-center justify-between mb-4">
        <h3 style="font-size:0.9rem;font-weight:700;color:var(--t1);">Daftar Gaji Karyawan</h3>
        <span style="font-size:0.72rem;color:var(--t4);">{{ $users->count() }} karyawan di halaman ini</span>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Karyawan</th>
                    <th>Jabatan / Divisi</th>
                    <th style="text-align:right;">Gaji Pokok</th>
                    <th style="text-align:right;">Tunjangan</th>
                    <th style="text-align:right;">Potongan</th>
                    <th style="text-align:right;">Take Home Pay</th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $i => $usr)
                @php
                    $basic = $usr->basic_salary;
                    $hasCustom = !is_null($basic);
                    if (!$hasCustom) {
                        $pos = strtolower($usr->position->name ?? '');
                        $basic = 6500000;
                        if (str_contains($pos, 'director') || str_contains($pos, 'direktur')) $basic = 35000000;
                        elseif (str_contains($pos, 'manager') || str_contains($pos, 'head')) $basic = 22000000;
                        elseif (str_contains($pos, 'supervisor') || str_contains($pos, 'lead')) $basic = 15000000;
                        elseif (str_contains($pos, 'senior')) $basic = 11000000;
                        elseif (str_contains($pos, 'staff') || str_contains($pos, 'officer')) $basic = 8000000;
                    }
                    $allowance = $usr->allowance ?? (int)($basic * 0.15);
                    $bpjs      = $usr->bpjs_deduction ?? (int)($basic * 0.03);
                    $tax       = $usr->tax_deduction  ?? (int)($basic * 0.05);
                    $deductions = $bpjs + $tax;
                    $net = $basic + $allowance - $deductions;
                @endphp
                <tr class="salary-row">
                    <td style="color:var(--t4);font-size:0.72rem;">{{ $users->firstItem() + $i }}</td>

                    {{-- Karyawan --}}
                    <td>
                        <div style="display:flex;align-items:center;gap:0.75rem;">
                            <div class="avatar" style="width:36px;height:36px;font-size:0.7rem;overflow:hidden;flex-shrink:0;">
                                @if($usr->photo)
                                    <img src="{{ $usr->photo_url }}" alt="{{ $usr->name }}" style="width:100%;height:100%;object-fit:cover;">
                                @else
                                    {{ $usr->initials }}
                                @endif
                            </div>
                            <div>
                                <div style="font-size:0.83rem;font-weight:700;color:var(--t1);">{{ $usr->name }}</div>
                                <div style="font-size:0.67rem;font-family:'JetBrains Mono',monospace;color:var(--t4);">{{ $usr->nik ?? '—' }}</div>
                            </div>
                        </div>
                    </td>

                    {{-- Jabatan --}}
                    <td>
                        <div style="font-size:0.8rem;font-weight:600;color:var(--t2);">{{ $usr->position->name ?? '—' }}</div>
                        <div style="font-size:0.67rem;color:var(--t4);">{{ $usr->division->name ?? '—' }}</div>
                    </td>

                    {{-- Gaji Pokok --}}
                    <td style="text-align:right;">
                        <div class="salary-cell-base">Rp {{ number_format($basic, 0, ',', '.') }}</div>
                        @if($hasCustom)
                            <span class="custom-tag">KUSTOM</span>
                        @else
                            <span class="auto-tag">otomatis</span>
                        @endif
                    </td>

                    {{-- Tunjangan --}}
                    <td style="text-align:right;">
                        <div style="font-family:'JetBrains Mono',monospace;font-size:0.8rem;font-weight:600;color:#60A5FA;">
                            Rp {{ number_format($allowance, 0, ',', '.') }}
                        </div>
                    </td>

                    {{-- Potongan --}}
                    <td style="text-align:right;">
                        <div class="salary-cell-deduct">Rp {{ number_format($deductions, 0, ',', '.') }}</div>
                        <div style="font-size:0.62rem;color:var(--t5);margin-top:0.1rem;">
                            BPJS {{ number_format($bpjs/1000, 0) }}K · PPh {{ number_format($tax/1000, 0) }}K
                        </div>
                    </td>

                    {{-- Net --}}
                    <td style="text-align:right;">
                        <div class="salary-cell-net">Rp {{ number_format($net, 0, ',', '.') }}</div>
                    </td>

                    {{-- Aksi --}}
                    <td style="text-align:center;">
                        <a href="{{ route('payroll.edit', $usr->id) }}" class="btn btn-secondary btn-sm">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Edit
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:3.5rem;color:var(--t4);">
                        <div style="font-size:2rem;margin-bottom:0.75rem;">💰</div>
                        <div style="font-weight:600;color:var(--t3);">Tidak ada data karyawan</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
    <div style="margin-top:1.5rem;padding:0 0.25rem;">{{ $users->links() }}</div>
    @endif
</div>

@endsection
