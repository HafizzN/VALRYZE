@extends('layouts.app')

@section('title', 'Pengaturan Gaji Karyawan')
@section('page-title', 'Payroll & Gaji')
@section('breadcrumb', 'Payroll / Pengaturan Gaji')

@section('content')
<div class="space-y-6 font-sans">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800 font-bold">Pengaturan Gaji Karyawan</h2>
            <p class="text-xs text-slate-500">Kelola komponen gaji pokok, tunjangan jabatan, serta potongan BPJS & PPh21 untuk seluruh karyawan.</p>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-slate-700">Daftar Gaji Karyawan</h3>
            <span class="text-[10px] text-slate-500">Menampilkan {{ $users->count() }} karyawan di halaman ini</span>
        </div>

        <div class="table-container overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 text-slate-600 text-xs">
                        <th class="py-3 font-semibold">Nama Karyawan</th>
                        <th class="py-3 font-semibold">Jabatan / Divisi</th>
                        <th class="py-3 font-semibold text-right">Gaji Pokok</th>
                        <th class="py-3 font-semibold text-right">Tunjangan</th>
                        <th class="py-3 font-semibold text-right">Potongan (BPJS + Pajak)</th>
                        <th class="py-3 font-semibold text-right">Estimasi Take Home Pay</th>
                        <th class="py-3 font-semibold text-center" style="width: 120px">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-xs text-slate-700">
                    @forelse($users as $usr)
                        @php
                            // Fallback calculations matching HrApiController
                            $basic = $usr->basic_salary;
                            $hasCustom = !is_null($basic);

                            if (!$hasCustom) {
                                // Default basic salary Rp 6.5M or based on position/division name
                                $pos = strtolower($usr->position->name ?? '');
                                $basic = 6500000;
                                if (str_contains($pos, 'director') || str_contains($pos, 'direktur')) {
                                    $basic = 35000000;
                                } elseif (str_contains($pos, 'manager') || str_contains($pos, 'head')) {
                                    $basic = 22000000;
                                } elseif (str_contains($pos, 'supervisor') || str_contains($pos, 'lead')) {
                                    $basic = 15000000;
                                } elseif (str_contains($pos, 'senior')) {
                                    $basic = 11000000;
                                } elseif (str_contains($pos, 'staff') || str_contains($pos, 'officer')) {
                                    $basic = 8000000;
                                }
                            }

                            $allowance = $usr->allowance ?? (int) ($basic * 0.15);
                            $bpjs = $usr->bpjs_deduction ?? (int) ($basic * 0.03);
                            $tax = $usr->tax_deduction ?? (int) ($basic * 0.05);
                            $deductions = $bpjs + $tax;
                            $net = $basic + $allowance - $deductions;
                        @endphp
                        <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                            <!-- Name -->
                            <td class="py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="avatar text-xs w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center font-bold text-slate-700">{{ $usr->initials }}</div>
                                    <div>
                                        <div class="font-bold text-slate-800">{{ $usr->name }}</div>
                                        <div class="text-[10px] text-slate-500 font-mono">{{ $usr->nik ?? 'TIDAK TERTAUT' }}</div>
                                    </div>
                                </div>
                            </td>
                            <!-- Position & Division -->
                            <td class="py-3.5">
                                <div class="font-semibold text-slate-800">{{ $usr->position->name ?? '-' }}</div>
                                <div class="text-[10px] text-slate-500">{{ $usr->division->name ?? '-' }}</div>
                            </td>
                            <!-- Basic Salary -->
                            <td class="py-3.5 text-right font-mono font-semibold">
                                Rp {{ number_format($basic, 0, ',', '.') }}
                                @if(!$hasCustom)
                                    <div class="text-[8px] text-slate-400 italic">Kalkulasi Otomatis</div>
                                @else
                                    <div class="text-[8px] text-emerald-600 font-bold">Kustom</div>
                                @endif
                            </td>
                            <!-- Allowance -->
                            <td class="py-3.5 text-right font-mono text-slate-600">
                                Rp {{ number_format($allowance, 0, ',', '.') }}
                            </td>
                            <!-- Deductions -->
                            <td class="py-3.5 text-right font-mono text-rose-600">
                                Rp {{ number_format($deductions, 0, ',', '.') }}
                                <div class="text-[8px] text-slate-400">BPJS: Rp {{ number_format($bpjs, 0, ',', '.') }} | Pajak: Rp {{ number_format($tax, 0, ',', '.') }}</div>
                            </td>
                            <!-- Net Salary -->
                            <td class="py-3.5 text-right font-mono font-bold text-slate-900">
                                Rp {{ number_format($net, 0, ',', '.') }}
                            </td>
                            <!-- Action -->
                            <td class="py-3.5 text-center">
                                <a href="{{ route('payroll.edit', $usr->id) }}" class="btn btn-secondary btn-sm rounded-xl text-indigo-600 hover:text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border-none transition-colors py-1.5 px-3">
                                    Edit Gaji
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-10 text-slate-400">
                                Tidak ada karyawan yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
